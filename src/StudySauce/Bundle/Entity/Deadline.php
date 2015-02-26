<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="deadline")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="deadlines")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", nullable=true)
     */
    protected $course;

    /**
     * @ORM\Column(type="string", length=256, name="assignment")
     */
    protected $assignment;

    /**
     * @ORM\Column(type="simple_array", length=256, name="reminder")
     */
    protected $reminder;

    /**
     * @ORM\Column(type="datetime", name="due_date")
     */
    protected $dueDate;

    /**
     * @ORM\Column(type="integer", name="percent")
     */
    protected $percent = 0;

    /**
     * @ORM\Column(type="boolean", name="completed")
     */
    protected $completed = false;

    /**
     * @ORM\Column(type="simple_array", length=256, name="reminder_sent", nullable=true)
     */
    protected $reminderSent = [];
    
    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted = false;

    /**
     * @return string
     */
    public function getDaysUntilDue()
    {
        if(empty($this->getDueDate()))
            return '';
        $timespan = floor(($this->getDueDate()->getTimestamp() - time()) / 86400);
        if ($timespan <= 0) {
            $days = 'today';
        } elseif ($timespan > 1) {
            $days = $timespan . ' days';
        } else {
            $days = 'tomorrow';
        }
        return $days;
    }

    /**
     * @return bool
     */
    public function shouldSend()
    {
        // due in one day
        if(in_array('86400', $this->getReminder()) && $this->getDueDate()->getTimestamp() > time() + 86400 && $this->getDueDate()->getTimestamp() < time() + 86400 * 2 &&
            !in_array('86400', $this->getReminderSent()))
            return true;
        // due in two days
        if(in_array('172800', $this->getReminder()) && $this->getDueDate()->getTimestamp() > time() + 86400 * 2 && $this->getDueDate()->getTimestamp() < time() + 86400 * 3 &&
                !in_array('86400', $this->getReminderSent()) &&
                !in_array('172800', $this->getReminderSent()))
            return true;
        // due in four days
        if(in_array('345600', $this->getReminder()) && $this->getDueDate()->getTimestamp() > time() + 86400 * 4 && $this->getDueDate()->getTimestamp() < time() + 86400 * 5 &&
                !in_array('86400',$this->getReminderSent()) &&
                !in_array('172800', $this->getReminderSent()) &&
                !in_array('345600', $this->getReminderSent()))
            return true;
        // due in a week
        if(in_array('604800', $this->getReminder()) && $this->getDueDate()->getTimestamp() > time() + 86400 * 7 && $this->getDueDate()->getTimestamp() < time() + 86400 * 8 &&
                !in_array('86400', $this->getReminderSent()) &&
                !in_array('172800', $this->getReminderSent()) &&
                !in_array('345600', $this->getReminderSent()) &&
                !in_array('604800', $this->getReminderSent()))
            return true;
        // due in two weeks
        if(in_array('1209600', $this->getReminder()) && $this->getDueDate()->getTimestamp() > time() + 86400 * 14 && $this->getDueDate()->getTimestamp() < time() + 86400 * 15 &&
                !in_array('86400',$this->getReminderSent()) &&
                !in_array('172800', $this->getReminderSent()) &&
                !in_array('345600', $this->getReminderSent()) &&
                !in_array('604800', $this->getReminderSent()) &&
                !in_array('1209600', $this->getReminderSent()))
            return true;
        return false;
    }
    
    public function markSent()
    {
        $timeSpan = floor(($this->getDueDate()->getTimestamp() - time()) / 86400);
        foreach ([1, 2, 4, 7, 14] as $i => $t) {
            if ($timeSpan - $t <= 0) {
                $sent = $this->getReminderSent();
                $sent[] = $t * 86400;
                $this->setReminderSent(array_unique($sent));
                break;
            }
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }


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
     * @param array $reminder
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
     * @return array 
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
     * @param array $reminderSent
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
     * @return array 
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Deadline
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
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

    /**
     * Set course
     *
     * @param \StudySauce\Bundle\Entity\Course $course
     * @return Deadline
     */
    public function setCourse(\StudySauce\Bundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \StudySauce\Bundle\Entity\Course 
     */
    public function getCourse()
    {
        return $this->course;
    }
}
