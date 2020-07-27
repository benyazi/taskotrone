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

class EffortListCommand extends Command
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
    protected $name = "effort_list";

    /**
     * @var string Command Description
     */
    protected $description = "Report of efforts";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $msg = $this->getUpdate()->getMessage();
        try {
            $arguments = trim(str_replace('/' . $this->name, '', $msg->text));
            $args = explode(' ', $arguments);
            // This will update the chat status to typing...
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $userData = $msg->get('from');
            $user = $this->userService->getUserOrCreate($userData->get('id'), [
                'name' => $userData->get('first_name') . ' ' . $userData->get('last_name'),
                'nickname' => $userData->get('username')
            ]);
            $from = isset($args[0]) ?
                (Carbon::createFromFormat('Y-m-d', $args[0])->setTime(0, 0, 0)) :
                (Carbon::now()->setTime(0, 0, 0));
            $to = isset($args[1]) ?
                (Carbon::createFromFormat('Y-m-d', $args[1])->setTime(23, 59, 59)) :
                null;
            $tasks = $this->taskService->getTasksWithEfforts($user, $from, $to);
            $response = 'List of tasks with effort:' . PHP_EOL . PHP_EOL;
            /** @var Task $task */
            foreach ($tasks as $task) {
                $response .= $this->taskService->printTaskInfo($task, $from, $to) . PHP_EOL . PHP_EOL;
            }
            // Reply with the commands list
            $this->replyWithMessage([
                'text' => $response,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            // Reply with the commands list
            $this->replyWithMessage([
                'text' => "We catch some error :^( " . $e->getMessage(),
                'parse_mode' => 'HTML'
            ]);
        }
    }
}