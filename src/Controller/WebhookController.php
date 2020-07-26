<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;

class WebhookController extends BaseController
{
    /**
     * @Route("/webhook/telegram/{sole}", name="webhook_telegram")
     */
    public function telegramAction(Request $request)
    {
        $telegram = $this->telegramService->getApiWithCommands();
        $update = $telegram->commandsHandler(true);
        return new Response("ok");
    }
}