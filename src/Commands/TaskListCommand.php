<?php

namespace App\Commands;

use App\Entity\Task;
use App\Services\TaskService;
use App\Services\UserService;
use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Message;

class TaskListCommand extends Command
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
    protected $name = "task_list";

    /**
     * @var string Command Description
     */
    protected $description = "List of current tasks";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $msg = $this->getUpdate()->getMessage();
        $arguments = trim(str_replace('/'.$this->name, '', $msg->text));
        $args = explode(' ', $arguments);
        $dt = Carbon::now();
        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $userData = $msg->get('from');
        $user = $this->userService->getUserOrCreate($userData->get('id'), [
            'name' => $userData->get('first_name') . ' ' . $userData->get('last_name'),
            'nickname' => $userData->get('username')
        ]);
        $type = isset($args[0])?(string)$args[0]:'active';
        $tasks = $this->taskService->getUserTasks($user, $type);
        if(empty($tasks)) {
            $this->replyWithMessage([
                'text' => "List of task is empty.",
                'parse_mode' => 'HTML'
            ]);
            return;
        }
        $response = 'List of your tasks:'.PHP_EOL.PHP_EOL;
        /** @var Task $task */
        foreach ($tasks as $task) {
            $response .= $this->taskService->printTaskInfo($task);
            $response .= PHP_EOL;
            $response .= PHP_EOL;
        }
        // Reply with the commands list
        $this->replyWithMessage([
            'text' => $response,
            'parse_mode' => 'HTML'
        ]);
    }
}