<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="deadline")
 */
class Deadline
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="deadlines")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=256, name="name")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=256, name="assignment")
     */
    protected $assignment;

    /**
     * @ORM\Column(type="string", length=256, name="reminder")
     */
    protected $reminder;

    /**
     * @ORM\Column(type="datetime", name="due_date", options={"default" = 0})
     */
    protected $dueDate;

    /**
     * @ORM\Column(type="integer", name="percent", options={"default" = 0})
     */
    protected $percent;

    /**
     * @ORM\Column(type="boolean", name="completed", options={"default" = 0})
     */
    protected $completed;

    /**
     * @ORM\Column(type="string", length=256, name="reminder_sent")
     */
    protected $reminderSent;
    
    /**
     * @ORM\Column(type="datetime", name="created", options={"default" = 0})
     */
    protected $created;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Deadline
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set assignment
     *
     * @param string $assignment
     * @return Deadline
     */
    public function setAssignment($assignment)
    {
        $this->assignment = $assignment;

        return $this;
    }

    /**
     * Get assignment
     *
     * @return string 
     */
    public function getAssignment()
    {
        return $this->assignment;
    }

    /**
     * Set reminder
     *
     * @param string $reminder
     * @return Deadline
     */
    public function setReminder($reminder)
    {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * Get reminder
     *
     * @return string 
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     * @return Deadline
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set percent
     *
     * @param integer $percent
     * @return Deadline
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return integer 
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set completed
     *
     * @param boolean $completed
     * @return Deadline
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed
     *
     * @return boolean 
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set reminderSent
     *
     * @param string $reminderSent
     * @return Deadline
     */
    public function setReminderSent($reminderSent)
    {
        $this->reminderSent = $reminderSent;

        return $this;
    }

    /**
     * Get reminderSent
     *
     * @return string 
     */
    public function getReminderSent()
    {
        return $this->reminderSent;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Deadline
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return Deadline
     */
    public function setUser(\StudySauce\Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \StudySauce\Bundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
