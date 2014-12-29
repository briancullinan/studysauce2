<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course")
 * @ORM\HasLifecycleCallbacks()
 */
class Course
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=256, name="name")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=1, name="type")
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=64, name="study_type", nullable=true)
     */
    protected $studyType;

    /**
     * @ORM\Column(type="string", length=64, name="study_difficulty", nullable=true)
     */
    protected $studyDifficulty;

    /**
     * @ORM\Column(type="simple_array", length=256, name="dotw", nullable=true)
     */
    protected $dotw = [];

    /**
     * @ORM\Column(type="datetime", name="start_time")
     */
    protected $startTime;

    /**
     * @ORM\Column(type="datetime", name="end_time")
     */
    protected $endTime;

    /**
     * @ORM\OneToMany(targetEntity="Checkin", mappedBy="course")
     * @ORM\OrderBy({"checkin" = "ASC"})
     */
    protected $checkins;

    /**
     * @ORM\OneToMany(targetEntity="Deadline", mappedBy="course")
     * @ORM\OrderBy({"dueDate" = "ASC"})
     */
    protected $deadlines;

    /**
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="courses")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
     */
    protected $schedule;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted = false;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $events;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->checkins = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Course
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
     * @return Course
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
     * Set studyType
     *
     * @param string $studyType
     * @return Course
     */
    public function setStudyType($studyType)
    {
        $this->studyType = $studyType;

        return $this;
    }

    /**
     * Get studyType
     *
     * @return string 
     */
    public function getStudyType()
    {
        return $this->studyType;
    }

    /**
     * Set studyDifficulty
     *
     * @param string $studyDifficulty
     * @return Course
     */
    public function setStudyDifficulty($studyDifficulty)
    {
        $this->studyDifficulty = $studyDifficulty;

        return $this;
    }

    /**
     * Get studyDifficulty
     *
     * @return string 
     */
    public function getStudyDifficulty()
    {
        return $this->studyDifficulty;
    }

    /**
     * Set dotw
     *
     * @param array $dotw
     * @return Course
     */
    public function setDotw($dotw)
    {
        $this->dotw = $dotw;

        return $this;
    }

    /**
     * Get dotw
     *
     * @return array 
     */
    public function getDotw()
    {
        return $this->dotw;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Course
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Course
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Course
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
     * @return Course
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
     * Add checkins
     *
     * @param \StudySauce\Bundle\Entity\Checkin $checkins
     * @return Course
     */
    public function addCheckin(\StudySauce\Bundle\Entity\Checkin $checkins)
    {
        $this->checkins[] = $checkins;

        return $this;
    }

    /**
     * Remove checkins
     *
     * @param \StudySauce\Bundle\Entity\Checkin $checkins
     */
    public function removeCheckin(\StudySauce\Bundle\Entity\Checkin $checkins)
    {
        $this->checkins->removeElement($checkins);
    }

    /**
     * Get checkins
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCheckins()
    {
        return $this->checkins;
    }

    /**
     * Set schedule
     *
     * @param \StudySauce\Bundle\Entity\Schedule $schedule
     * @return Course
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
     * Add events
     *
     * @param \StudySauce\Bundle\Entity\Event $events
     * @return Course
     */
    public function addEvent(\StudySauce\Bundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \StudySauce\Bundle\Entity\Event $events
     */
    public function removeEvent(\StudySauce\Bundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add deadlines
     *
     * @param \StudySauce\Bundle\Entity\Deadline $deadlines
     * @return Course
     */
    public function addDeadline(\StudySauce\Bundle\Entity\Deadline $deadlines)
    {
        $this->deadlines[] = $deadlines;

        return $this;
    }

    /**
     * Remove deadlines
     *
     * @param \StudySauce\Bundle\Entity\Deadline $deadlines
     */
    public function removeDeadline(\StudySauce\Bundle\Entity\Deadline $deadlines)
    {
        $this->deadlines->removeElement($deadlines);
    }

    /**
     * Get deadlines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeadlines()
    {
        return $this->deadlines;
    }
}
