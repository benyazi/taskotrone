<?php
namespace App\Controller;

use App\Commands\NewTaskCommand;
use App\Commands\StartCommand;
use App\Commands\TaskListCommand;
use App\Services\TaskService;
use App\Services\TelegramService;
use App\Services\UserService;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;

class BaseController extends AbstractController
{
    protected $telegramService;
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
}