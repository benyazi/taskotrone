<?php

namespace App\Commands;

use App\Entity\Task;
use App\Services\TaskService;
use App\Services\UserService;
use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Message;
use function GuzzleHttp\Psr7\str;

class TaskEndCommand extends Command
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
    protected $name = "task_end";

    /**
     * @var string Command Description
     */
    protected $description = "Close task with ID and status";

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
        $status = (string) $args[1];
        if($this->taskService->closeTask($task, $status)) {
            // Reply with the commands list
            $this->replyWithMessage([
                'text' => '#task'. (int)$args[0] . ' closed with status <strong>' . $status . '</strong>',
                'parse_mode' => 'HTML'
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Choice one of allowed end statuses: '. implode(',', Task::END_STATUSES),
                'parse_mode' => 'HTML'
            ]);
        }
    }
}