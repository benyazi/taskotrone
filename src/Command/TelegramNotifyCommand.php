<?php
namespace App\Command;

use App\Commands\TaskEffortCommand;
use App\Services\TaskService;
use App\Services\TelegramService;
use App\Services\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Telegram\Bot\Api;

class TelegramNotifyCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'taskbot:notify';

    private $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
        parent::__construct();
    }

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->telegramService->sendTaskNotify();
        return 0;
    }
}