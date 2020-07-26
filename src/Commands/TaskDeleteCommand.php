<?php

namespace App\Commands;

use App\Entity\Task;
use App\Services\TaskService;
use App\Services\UserService;
use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Message;

class TaskDeleteCommand extends Command
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
    protected $name = "task_delete";

    /**
     * @var string Command Description
     */
    protected $description = "Delete task with ID";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $msg = $this->getUpdate()->getMessage();
        $arguments = trim(str_replace('/'.$this->name, '', $msg->text));
        $args = explode(' ', $arguments);
        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $userData = $msg->get('from');
        $user = $this->userService->getUserOrCreate($userData->get('id'), [
            'name' => $userData->get('first_name') . ' ' . $userData->get('last_name'),
            'nickname' => $userData->get('username')
        ]);
        /** @var Task $task */
        $task = $this->taskService->getUserTask($user, (int)$args[0]);
        if($task === null) {
            $this->replyWithMessage([
                'text' => "You dont have task with ID " . (int)$args[0],
                'parse_mode' => 'HTML'
            ]);
            return;
        }
        $this->taskService->deleteTask($task);
        // Reply with the commands list
        $this->replyWithMessage([
            'text' => 'Task '. (int)$args[0] . ' deleted.',
            'parse_mode' => 'HTML'
        ]);
    }
}