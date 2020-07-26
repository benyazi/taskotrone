<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $uniq_by_user;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $is_deleted = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_due;

    const STATUS_BACKLOG = 'backlog';
    const STATUS_INPROGRESS = 'inprogress';
    const STATUS_DONE = 'done';
    const STATUS_CLOSED = 'closed';
    const STATUS_FAILED = 'failed';

    const END_STATUSES = [
        self::STATUS_DONE,
        self::STATUS_FAILED
    ];
    /**
     * @ORM\Column(type="string", length=255, options={"default":"backlog"})
     */
    private $status = self::STATUS_BACKLOG;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_close;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_updated;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Task
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUniqByUser()
    {
        return $this->uniq_by_user;
    }

    /**
     * @param mixed $uniq_by_user
     * @return Task
     */
    public function setUniqByUser($uniq_by_user)
    {
        $this->uniq_by_user = $uniq_by_user;
        return $this;
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
     * @return Task
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateDue()
    {
        return $this->date_due;
    }

    /**
     * @param mixed $date_due
     * @return Task
     */
    public function setDateDue($date_due)
    {
        $this->date_due = $date_due;
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
     * @return Task
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }

    /**
     * @param mixed $date_updated
     * @return Task
     */
    public function setDateUpdated($date_updated)
    {
        $this->date_updated = $date_updated;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsDeleted(): int
    {
        return $this->is_deleted;
    }

    /**
     * @param int $is_deleted
     * @return Task
     */
    public function setIsDeleted(int $is_deleted): Task
    {
        $this->is_deleted = $is_deleted;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Task
     */
    public function setStatus(string $status): Task
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateClose()
    {
        return $this->date_close;
    }

    /**
     * @param mixed $date_close
     * @return Task
     */
    public function setDateClose($date_close)
    {
        $this->date_close = $date_close;
        return $this;
    }
}