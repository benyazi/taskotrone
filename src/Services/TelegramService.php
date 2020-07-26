<?php
namespace App\Services;

use App\Commands\NewTaskCommand;
use App\Commands\StartCommand;
use App\Commands\TaskDeleteCommand;
use App\Commands\TaskEffortCommand;
use App\Commands\TaskEndCommand;
use App\Commands\TaskListCommand;
use App\Entity\ChatUser;
use Telegram\Bot\Api;

class TelegramService
{
    private $userService;
    private $taskService;

    public function __construct(UserService $userService, TaskService $taskService)
    {
        $this->userService = $userService;
        $this->taskService = $taskService;
    }

    public function getApiWithCommands()
    {
        $telegram = new Api($_ENV['TELEGRAM_BOT_KEY']);
        $telegram->addCommand(StartCommand::class);
        $telegram->addCommand(new NewTaskCommand($this->userService, $this->taskService));
        $telegram->addCommand(new TaskListCommand($this->userService, $this->taskService));
        $telegram->addCommand(new TaskDeleteCommand($this->userService, $this->taskService));
        $telegram->addCommand(new TaskEffortCommand($this->userService, $this->taskService));
        $telegram->addCommand(new TaskEndCommand($this->userService, $this->taskService));
        return $telegram;
    }

    public function sendTaskNotify()
    {
        $telegram = new Api($_ENV['TELEGRAM_BOT_KEY']);
        /** @var ChatUser $user */
        foreach ($this->userService->getUserWithNotify() as $user)
        {
            $msg = 'Your today tasks:'.PHP_EOL.PHP_EOL;
            $tasks = $this->taskService->getUserTasks($user);
            if(empty($tasks)) {
                continue;
            }
            foreach ($tasks as $task) {
                $msg .= $this->taskService->printTaskInfo($task) . PHP_EOL . PHP_EOL;
            }
            $result = $telegram->sendMessage([
                'chat_id' => $user->getTelegramId(),
                'text' => $msg,
                'parse_mode' => 'HTML'
            ]);
        }
    }
}