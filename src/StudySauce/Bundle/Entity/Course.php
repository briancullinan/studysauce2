<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course")
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
     * @ORM\Column(type="simple_array", length=256, name="dotw")
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
     */
    protected $checkins;

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
     * Set dotw
     *
     * @param string $dotw
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
     * @return string 
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
}
