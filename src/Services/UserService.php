<?php
namespace App\Services;

use App\Entity\ChatUser;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class UserService
{
    /** @var EntityManager  */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param integer $telegramId
     * @param array $data
     */
    public function getUserOrCreate($telegramId, $data)
    {
        $user = $this->em->getRepository(ChatUser::class)
            ->findOneBy([
                "telegram_id" => $telegramId
            ]);
        if(empty($user)) {
            $user = new ChatUser();
            $user->setTelegramId($telegramId);
            $this->em->persist($user);
        }
        $user->setName($data['name']);
        $user->setNickname($data['nickname']);
        $this->em->flush();
        return $user;
    }

    /**
     * @return array
     */
    public function getUserWithNotify()
    {
        return $this->em->getRepository(ChatUser::class)
            ->findBy([
                "notify_enabled" => true
            ]);
    }
}