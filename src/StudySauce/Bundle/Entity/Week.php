<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * TODO: unique on start, end, and type when all overlaps are removed?
 * @ORM\Table(name="week",uniqueConstraints={
 *     @ORM\UniqueConstraint(name="week_schedule_idx", columns={"schedule_id", "week", "hash", "year"})},indexes={
 *     @ORM\Index(name="hash_idx", columns={"week", "hash", "year"})})
 * @ORM\HasLifecycleCallbacks()
 */
class Week
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="week")
     */
    protected $week;

    /**
     * @ORM\Column(type="string", length=64, name="hash")
     */
    protected $hash;

    /**
     * @ORM\Column(type="integer", name="year")
     */
    protected $year;

    /**
     * @ORM\Column(type="datetime", name="start")
     */
    protected $start;

    /**
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="weeks")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
     */
    protected $schedule;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="week")
     * @ORM\OrderBy({"start" = "DESC"})
     */
    protected $events;

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
     * Set week
     *
     * @param integer $week
     * @return Week
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return integer 
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Week
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Week
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return Week
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
     * Set created
     *
     * @param \DateTime $created
     * @return Week
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
     * @param Schedule $schedule
     * @return Week
     */
    public function setSchedule(Schedule $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add events
     *
     * @param \StudySauce\Bundle\Entity\Event $events
     * @return Week
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
}
