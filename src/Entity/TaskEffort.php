<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TaskEffort
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $task_id;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $reverse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $effort_string;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $effort_hour = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $effort_minutes = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_effort;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return TaskEffort
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReverse()
    {
        return $this->reverse;
    }

    /**
     * @param mixed $reverse
     * @return TaskEffort
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEffortString()
    {
        return $this->effort_string;
    }

    /**
     * @param mixed $effort_string
     * @return TaskEffort
     */
    public function setEffortString($effort_string)
    {
        $this->effort_string = $effort_string;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEffortHour()
    {
        return $this->effort_hour;
    }

    /**
     * @param mixed $effort_hour
     * @return TaskEffort
     */
    public function setEffortHour($effort_hour)
    {
        $this->effort_hour = $effort_hour;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateEffort()
    {
        return $this->date_effort;
    }

    /**
     * @param mixed $date_effort
     * @return TaskEffort
     */
    public function setDateEffort($date_effort)
    {
        $this->date_effort = $date_effort;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * @param mixed $date_created
     * @return TaskEffort
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
        return $this;
    }

    /**
     * @return int
     */
    public function getEffortMinutes(): int
    {
        return $this->effort_minutes;
    }

    /**
     * @param int $effort_minutes
     * @return TaskEffort
     */
    public function setEffortMinutes(int $effort_minutes): TaskEffort
    {
        $this->effort_minutes = $effort_minutes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaskId()
    {
        return $this->task_id;
    }

    /**
     * @param mixed $task_id
     * @return TaskEffort
     */
    public function setTaskId($task_id)
    {
        $this->task_id = $task_id;
        return $this;
    }
}