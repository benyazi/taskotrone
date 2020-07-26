<?php

namespace App\Commands;

use App\Entity\Task;
use App\Entity\TaskEffort;
use App\Services\TaskService;
use App\Services\UserService;
use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Message;

class TaskEffortCommand extends Command
{
    /** @var UserService */
    private $userService;

    /** @var TaskService */
    private $taskService;

    public function __construct(UserService $userService, TaskService $taskService)
    {
        $this->userService = $userService;
        $this->taskService = $taskService;
    }
    /**
     * @var string Command Name
     */
    protected $name = "task_effort";

    /**
     * @var string Command Description
     */
    protected $description = "Effort time to task, 'task_effort 1 1h30m'";

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
        /** @var Task $task */
        $task = $this->taskService->getUserTask($user, (int)$args[0]);
        if($task === null) {
            $this->replyWithMessage([
                'text' => "You dont have task with ID " . (int)$args[0],
                'parse_mode' => 'HTML'
            ]);
            return;
        }
        $effortString = (string) $args[1];
        /** @var TaskEffort $taskEffort */
        $taskEffort = $this->taskService->effortForTask($task, $effortString);
        // Reply with the commands list
        $this->replyWithMessage([
            'text' => 'You efforted ' . $taskEffort->getEffortHour() .' hours and '. $taskEffort->getEffortMinutes() . ' minutes.',
            'parse_mode' => 'HTML'
        ]);
    }
}