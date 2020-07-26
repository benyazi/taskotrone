<?php
namespace App\Controller;

use App\Commands\NewTaskCommand;
use App\Commands\StartCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;

class TestController extends BaseController
{
    /**
     * @Route("/test/telegram", name="test_telegram")
     */
    public function telegramAction(Request $request)
    {
        $telegram = new Api($_ENV['TELEGRAM_BOT_KEY']);
        $response = $telegram->getMe();
        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();
        return new Response("BOT ID: $botId <br> First Name: $firstName <br> UserName: $username");
    }
    /**
     * @Route("/test/telegram2", name="test_telegram")
     */
    public function telegram2Action(Request $request)
    {
        $telegram = $this->telegramService->getApiWithCommands();
        $update = $telegram->commandsHandler();
//        $updates = $telegram->getWebhookUpdates();
//        $updates = $telegram->getUpdates();
        return new Response("ok");
    }
}