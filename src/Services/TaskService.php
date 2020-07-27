<?php
namespace App\Services;

use App\Entity\ChatUser;
use App\Entity\Task;
use App\Entity\TaskEffort;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class TaskService
{
    /** @var EntityManager  */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param ChatUser $user
     * @param array $data
     * @return Task
     */
    public function createTask($user, $text, $dateDue)
    {
        $task = new Task();
        $task->setDateCreated(new \DateTime());
        $task->setDateDue($dateDue);
        $task->setName($text);
        $task->setUserId($user->getId());
        $task->setUniqByUser($this->getNewUserUniqId($user));
        $this->em->persist($task);
        $this->em->flush($task);
        return $task;
    }

    /**
     * @param Task $task
     * @return string
     */
    public function printTaskInfo($task, $from = null, $to = null)
    {
        $response = '#task' . $task->getUniqByUser() . ' ' . $task->getName() . PHP_EOL;
        $response .= 'Due at <strong>'.$task->getDateDue()->format('d.m.Y').'</strong>' . PHP_EOL;
        $response .= 'Efforts: ' . $this->getEffortTimeForTask($task, $from, $to) . PHP_EOL;
        $response .= 'Status: <strong>' . $this->printStatusString($task) .'</strong>';
        return $response;
    }

    /**
     * @param ChatUser $user
     * @return array
     */
    public function getUserTasks($user, $type = 'active')
    {
        $statuses = [Task::STATUS_INPROGRESS, Task::STATUS_BACKLOG];
        if ($type === 'done') {
            $statuses = [Task::STATUS_DONE];
        } elseif ($type === 'failed') {
            $statuses = [Task::STATUS_FAILED];
        }
        return $this->em->getRepository(Task::class)
            ->findBy([
                'user_id' => $user->getId(),
                'is_deleted' => false,
                'status' => $statuses
            ], [
                'date_due' => 'DESC'
            ]);
    }
    /**
     * @param ChatUser $user
     * @param integer $uniqId
     * @return object|null
     */
    public function getUserTask($user, $uniqId)
    {
        return $this->em->getRepository(Task::class)
            ->findOneBy([
                'user_id' => $user->getId(),
                'uniq_by_user' => $uniqId,
                'is_deleted' => false
            ]);
    }

    /**
     * @param Task $task
     * @return string
     */
    public function printStatusString($task)
    {
        switch ($task->getStatus()) {
            case Task::STATUS_FAILED:
                return 'Task failed';
                break;
            case Task::STATUS_DONE:
                return 'Task done';
                break;
            case Task::STATUS_BACKLOG:
            case Task::STATUS_INPROGRESS:
                return 'Task in progress';
                break;
            default:
                return 'Task\'s status not found';
                break;
        }
    }

    /**
     * @param Task $task
     * @param string $status
     * @return string
     */
    public function closeTask($task, $status)
    {
        if(!in_array($status, [
            Task::STATUS_BACKLOG,
            Task::STATUS_CLOSED,
            Task::STATUS_DONE,
            Task::STATUS_FAILED,
            Task::STATUS_INPROGRESS
        ], true)) {
            return false;
        }
        $task->setStatus($status);
        $task->setDateClose(new \DateTime());
        $task->setDateUpdated(new \DateTime());
        $this->em->flush();
        return true;
    }

    /**
     * @param Task $task
     * @return string
     */
    public function getEffortTimeForTask($task, $from = null, $to = null)
    {
        $efforts = $this->em->getRepository(TaskEffort::class)
            ->createQueryBuilder('e')
            ->andWhere('e.user_id = :userId')
            ->andWhere('e.task_id = :taskId')
            ->setParameter('userId', $task->getUserId())
            ->setParameter('taskId', $task->getId());
        if($from) {
            $efforts->andWhere('e.date_effort >= :dtFrom');
            $efforts->setParameter('dtFrom', $from);
        }
        if($to) {
            $efforts->andWhere('e.date_effort <= :dtTo');
            $efforts->setParameter('dtTo', $to);
        }
        $efforts = $efforts->addOrderBy('e.date_created','ASC')->getQuery()->getResult();
        $hours = $minutes = 0;
        /** @var TaskEffort $effort */
        foreach ($efforts as $effort)
        {
            if($effort->getReverse()) {
                $hours -= $effort->getEffortHour();
                $minutes -= $effort->getEffortMinutes();
            } else {
                $hours += $effort->getEffortHour();
                $minutes += $effort->getEffortMinutes();
            }
        }
        if($minutes >= 60) {

        }
        $returnStr = '';
        if($hours > 0) {
            $returnStr .= $hours . 'h';
        }
        $returnStr .= $minutes . 'm';
        return $returnStr;
    }

    /**
     * @param Task $task
     * @return void
     */
    public function deleteTask($task)
    {
        $task->setIsDeleted(true);
        $task->setDateUpdated(new \DateTime());
        $this->em->flush();
    }

    /**
     * @param ChatUser $user
     * @return integer
     */
    public function getNewUserUniqId($user)
    {
        /** @var Task $lastTask */
        $lastTask = $this->em->getRepository(Task::class)
            ->findOneBy([
                'user_id' => $user->getId()
            ], [
                'id' => 'DESC'
            ]);
        if($lastTask === null) {
            return 1;
        }
        return $lastTask->getUniqByUser() + 1;
    }

    /**
     * @param ChatUser $user
     * @return array
     */
    public function getTasksWithEfforts($user, $from, $to)
    {
        $efforts = $this->em->getRepository(TaskEffort::class)
            ->createQueryBuilder('e')
            ->select('e.task_id')
            ->andWhere('e.user_id = :user_id')
            ->setParameter('user_id', $user->getId());
        if($from) {
            $efforts->andWhere('e.date_effort >= :dtFrom');
            $efforts->setParameter('dtFrom', $from);
        }
        if($to) {
            $efforts->andWhere('e.date_effort <= :dtTo');
            $efforts->setParameter('dtTo', $to);
        }
        $efforts = $efforts->getQuery()->getArrayResult();
        $taskIds = [];
        foreach ($efforts as $effort) {
            $taskIds[] = $effort['task_id'];
        }
        if(empty($taskIds)) {
            return [];
        }
        return $this->em->getRepository(Task::class)
            ->findBy([
                'id' => $taskIds,
                'is_deleted' => false
            ]);
    }

    /**
     * @param Task $task
     * @param string $effortString
     */
    public function effortForTask($task, $effortString, $reverse = false)
    {
        $taskEffort = new TaskEffort();
        $taskEffort->setUserId($task->getUserId());
        $taskEffort->setTaskId($task->getId());
        $taskEffort->setDateCreated(new \DateTime());
        $taskEffort->setDateEffort(new \DateTime());
        $taskEffort->setEffortString($effortString);
        $taskEffort->setReverse($reverse);
        list($hours, $minutes) = $this->parseEffortString($effortString);
        $taskEffort->setEffortHour($hours);
        $taskEffort->setEffortMinutes($minutes);
        $this->em->persist($taskEffort);
        $this->em->flush();
        return $taskEffort;
    }

    public function parseEffortString($effortString)
    {
        $arr = explode('h', $effortString);
        $minutes = isset($arr[1])?(int)$arr[1]:0;
        if(strpos($arr[0],'m') === false) {
            $hours = (int)$arr[0];
        } else {
            $hours = 0;
            $minutes = (int)$arr[0];
        }
        return [$hours,$minutes];
    }
}