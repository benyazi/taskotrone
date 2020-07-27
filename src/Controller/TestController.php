<?php
namespace App\Controller;

use App\Commands\NewTaskCommand;
use App\Commands\StartCommand;
use App\Entity\ChatUser;
use App\Entity\Task;
use App\Services\TaskService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;
use Telegram\Bot\Methods\Chat;

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
    /**
     * @Route("/test/data", name="test_data")
     */
    public function dataAction(Request $request, TaskService $ts, EntityManagerInterface $em)
    {
        /** @var ChatUser $user */
        $user = $em->getRepository(ChatUser::class)->find(1);
        $tasks = $ts->getTasksWithEfforts($user, (Carbon::now())->setTime(0,0,0), null);
//        $response = print_r($efforts, true);
        $response = '';
        /** @var Task $task */
        foreach ($tasks as $task) {
            $response .= $ts->printTaskInfo($task, (Carbon::now())->setTime(0,0,0), null) . PHP_EOL . PHP_EOL;
        }
        return new Response('<pre>'.$response.'</pre>');
    }
}