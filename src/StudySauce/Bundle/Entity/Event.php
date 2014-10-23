<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * TODO: unique on start, end, and type when all overlaps are removed?
 * @ORM\Table(name="event",indexes={
 *     @ORM\Index(name="week_idx", columns={"week_id", "schedule_id"})})
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
     * @ORM\Column(type="datetime", name="start")
     */
    protected $start;

    /**
     * @ORM\Column(type="datetime", name="end")
     */
    protected $end;

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
     * @ORM\ManyToOne(targetEntity="Week", inversedBy="events")
     * @ORM\JoinColumn(name="week_id", referencedColumnName="id")
     */
    protected $week;

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
     * Set week
     *
     * @param \StudySauce\Bundle\Entity\Week $week
     * @return Event
     */
    public function setWeek(\StudySauce\Bundle\Entity\Week $week = null)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return \StudySauce\Bundle\Entity\Week 
     */
    public function getWeek()
    {
        return $this->week;
    }
}
