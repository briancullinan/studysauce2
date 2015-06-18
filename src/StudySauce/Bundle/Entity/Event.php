<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * TODO: unique on start, end, and type when all overlaps are removed?
 * @ORM\Table(name="event", uniqueConstraints={@ORM\UniqueConstraint(name="remote_idx", columns={"remote_id"})})
 * @ORM\HasLifecycleCallbacks()
 */
class Event
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, name="remote_id", nullable=true)
     */
    protected $remoteId;

    /**
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="events")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
     */
    protected $schedule;

    /**
     * @ORM\Column(type="string", length=256, name="name")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=3, name="type")
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=256, name="location", nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="integer", name="alert", nullable=true)
     */
    protected $alert;

    /**
     * @ORM\Column(type="datetime", name="start")
     */
    protected $start;

    /**
     * @ORM\Column(type="datetime", name="end")
     */
    protected $end;

    /**
     * Used for sr,p,c,o event types
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="events")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", nullable=true)
     */
    protected $course;

    /**
     * Used for d,r event types
     * @ORM\ManyToOne(targetEntity="Deadline", inversedBy="events")
     * @ORM\JoinColumn(name="deadline_id", referencedColumnName="id", nullable=true)
     */
    protected $deadline;

    /**
     * @ORM\Column(type="boolean", name="completed")
     */
    protected $completed = false;

    /**
     * @ORM\Column(type="boolean", name="moved")
     */
    protected $moved = false;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

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
     * Set name
     *
     * @param string $name
     * @return Event
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
     * Set type
     *
     * @param string $type
     * @return Event
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return Event
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Event
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set completed
     *
     * @param boolean $completed
     * @return Event
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
     * Set moved
     *
     * @param boolean $moved
     * @return Event
     */
    public function setMoved($moved)
    {
        $this->moved = $moved;

        return $this;
    }

    /**
     * Get moved
     *
     * @return boolean 
     */
    public function getMoved()
    {
        return $this->moved;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Event
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
     * Set created
     *
     * @param \DateTime $created
     * @return Event
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
     * Set schedule
     *
     * @param \StudySauce\Bundle\Entity\Schedule $schedule
     * @return Event
     */
    public function setSchedule(\StudySauce\Bundle\Entity\Schedule $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \StudySauce\Bundle\Entity\Schedule 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set course
     *
     * @param \StudySauce\Bundle\Entity\Course $course
     * @return Event
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

    /**
     * Set deadline
     *
     * @param \StudySauce\Bundle\Entity\Deadline $deadline
     * @return Event
     */
    public function setDeadline(\StudySauce\Bundle\Entity\Deadline $deadline = null)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \StudySauce\Bundle\Entity\Deadline 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Event
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set alert
     *
     * @param integer $alert
     * @return Event
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;

        return $this;
    }

    /**
     * Get alert
     *
     * @return integer 
     */
    public function getAlert()
    {
        if(empty($this->alert) && $this->alert !== 0) {
            $schedule = $this->getSchedule()->getAlerts();
            if(isset($schedule[$this->type]))
                return $schedule[$this->type];
        }
        return $this->alert;
    }

    /**
     * Set remoteId
     *
     * @param string $remoteId
     * @return Event
     */
    public function setRemoteId($remoteId)
    {
        $this->remoteId = $remoteId;

        return $this;
    }

    /**
     * Get remoteId
     *
     * @return string 
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }
}
