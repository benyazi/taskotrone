<?php

namespace App\Commands;

use App\Entity\Task;
use App\Services\TaskService;
use App\Services\UserService;
use Carbon\Carbon;
use Symfony\Component\DependencyInjection\Container;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Message;

class NewTaskCommand extends Command
{
    /** @var UserService */
    private $userService;

    /** @var TaskService */
    private $taskService;

    public function __construct($userService, $taskService)
    {
        $this->userService = $userService;
        $this->taskService = $taskService;
    }

    /**
     * @var string Command Name
     */
    protected $name = "task_new";

    /**
     * @var string Command Description
     */
    protected $description = "New Task Command to create new task";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $msg = $this->getUpdate()->getMessage();
        $arguments = trim(str_replace('/'.$this->name, '', $msg->text));
        $args = explode(' ', $arguments);
        if(count($args) < 2) {
            $this->replyWithMessage([
                'text' => "Set date and text of task. Example:".PHP_EOL."<pre>/task_new 2020-07-04 Text of task</pre>",
                'parse_mode' => 'HTML'
            ]);
            return;
        }
        $dateStr = $args[0];
        $timeStr = $args[1];
        if(preg_match("/^(?:2[0-4]|[01][1-9]|10):([0-5][0-9])$/", $timeStr)) {
            $text = trim(str_replace($args[0] . ' ' . $args[1],'', $arguments));
            $dt = Carbon::createFromFormat('Y-m-d H:i', $dateStr . ' ' . $timeStr);
        } else {
            $text = trim(str_replace($args[0],'', $arguments));
            $dt = Carbon::createFromFormat('Y-m-d', $dateStr);
        }

        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
//        $this->replyWithMessage(['text' => 'Hello! Welcome to our bot, Here are our available commands:']);

        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $userData = $msg->get('from');
        $user = $this->userService->getUserOrCreate($userData->get('id'), [
            'name' => $userData->get('first_name') . ' ' . $userData->get('last_name'),
            'nickname' => $userData->get('username')
        ]);
        $task = $this->taskService->createTask($user, $text, $dt);
        $response = 'Created new task N_'.$task->getId().'. With due date as ' . $task->getDateDue()->format('d.m.Y H:i') .' and text "' . $task->getName(). '"';
        // Reply with the commands list
        $this->replyWithMessage([
            'text' => $response,
            'parse_mode' => 'HTML'
        ]);
    }
}