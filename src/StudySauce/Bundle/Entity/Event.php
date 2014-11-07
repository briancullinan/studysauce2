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
     * @ORM\OneToOne(targetEntity="ActiveStrategy", inversedBy="event")
     * @ORM\JoinColumn(name="active_id", referencedColumnName="id", nullable=true)
     */
    protected $active;

    /**
     * @ORM\OneToOne(targetEntity="PreworkStrategy", inversedBy="event")
     * @ORM\JoinColumn(name="prework_id", referencedColumnName="id", nullable=true)
     */
    protected $prework;

    /**
     * @ORM\OneToOne(targetEntity="OtherStrategy", inversedBy="event")
     * @ORM\JoinColumn(name="other_id", referencedColumnName="id", nullable=true)
     */
    protected $other;

    /**
     * @ORM\OneToOne(targetEntity="SpacedStrategy", inversedBy="event")
     * @ORM\JoinColumn(name="spaced_id", referencedColumnName="id", nullable=true)
     */
    protected $spaced;

    /**
     * @ORM\OneToOne(targetEntity="TeachStrategy", inversedBy="event")
     * @ORM\JoinColumn(name="teach_id", referencedColumnName="id", nullable=true)
     */
    protected $teach;

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
     * Set active
     *
     * @param \StudySauce\Bundle\Entity\ActiveStrategy $active
     * @return Event
     */
    public function setActive(\StudySauce\Bundle\Entity\ActiveStrategy $active = null)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return \StudySauce\Bundle\Entity\ActiveStrategy 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set prework
     *
     * @param \StudySauce\Bundle\Entity\PreworkStrategy $prework
     * @return Event
     */
    public function setPrework(\StudySauce\Bundle\Entity\PreworkStrategy $prework = null)
    {
        $this->prework = $prework;

        return $this;
    }

    /**
     * Get prework
     *
     * @return \StudySauce\Bundle\Entity\PreworkStrategy 
     */
    public function getPrework()
    {
        return $this->prework;
    }

    /**
     * Set other
     *
     * @param \StudySauce\Bundle\Entity\OtherStrategy $other
     * @return Event
     */
    public function setOther(\StudySauce\Bundle\Entity\OtherStrategy $other = null)
    {
        $this->other = $other;

        return $this;
    }

    /**
     * Get other
     *
     * @return \StudySauce\Bundle\Entity\OtherStrategy 
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * Set spaced
     *
     * @param \StudySauce\Bundle\Entity\SpacedStrategy $spaced
     * @return Event
     */
    public function setSpaced(\StudySauce\Bundle\Entity\SpacedStrategy $spaced = null)
    {
        $this->spaced = $spaced;

        return $this;
    }

    /**
     * Get spaced
     *
     * @return \StudySauce\Bundle\Entity\SpacedStrategy 
     */
    public function getSpaced()
    {
        return $this->spaced;
    }

    /**
     * Set teach
     *
     * @param \StudySauce\Bundle\Entity\TeachStrategy $teach
     * @return Event
     */
    public function setTeach(\StudySauce\Bundle\Entity\TeachStrategy $teach = null)
    {
        $this->teach = $teach;

        return $this;
    }

    /**
     * Get teach
     *
     * @return \StudySauce\Bundle\Entity\TeachStrategy 
     */
    public function getTeach()
    {
        return $this->teach;
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
