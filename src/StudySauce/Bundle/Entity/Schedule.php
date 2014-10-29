<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="schedule")
 * @ORM\HasLifecycleCallbacks()
 */
class Schedule
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="schedules")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Course", mappedBy="schedule")
     */
    protected $courses;

    /**
     * @ORM\Column(type="string", length=256, name="university", nullable = true)
     */
    protected $university;

    /**
     * @ORM\Column(type="string", length=10, name="grades", nullable = true)
     */
    protected $grades;

    /**
     * @ORM\Column(type="string", length=10, name="weekends", nullable = true)
     */
    protected $weekends;

    /**
     * @ORM\Column(type="integer", length=10, name="sharp6am11am", nullable = true)
     */
    protected $sharp6am11am;

    /**
     * @ORM\Column(type="integer", length=10, name="sharp11am4pm", nullable = true)
     */
    protected $sharp11am4pm;

    /**
     * @ORM\Column(type="integer", length=10, name="sharp4pm9pm", nullable = true)
     */
    protected $sharp4pm9pm;

    /**
     * @ORM\Column(type="integer", length=10, name="sharp9pm2am", nullable = true)
     */
    protected $sharp9pm2am;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="schedule")
     * @ORM\OrderBy({"start" = "DESC"})
     */
    protected $events;

    /**
     * @ORM\OneToMany(targetEntity="ActiveStrategy", mappedBy="schedule")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $active;

    /**
     * @ORM\OneToMany(targetEntity="OtherStrategy", mappedBy="schedule")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $other;

    /**
     * @ORM\OneToMany(targetEntity="PreworkStrategy", mappedBy="schedule")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $prework;

    /**
     * @ORM\OneToMany(targetEntity="TeachStrategy", mappedBy="schedule")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $teach;

    /**
     * @ORM\OneToMany(targetEntity="SpacedStrategy", mappedBy="schedule")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $spaced;

    /**
     * @ORM\OneToMany(targetEntity="Week", mappedBy="schedule")
     * @ORM\OrderBy({"start" = "DESC"})
     */
    protected $weeks;

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
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->weeks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set university
     *
     * @param string $university
     * @return Schedule
     */
    public function setUniversity($university)
    {
        $this->university = $university;

        return $this;
    }

    /**
     * Get university
     *
     * @return string 
     */
    public function getUniversity()
    {
        return $this->university;
    }

    /**
     * Set grades
     *
     * @param string $grades
     * @return Schedule
     */
    public function setGrades($grades)
    {
        $this->grades = $grades;

        return $this;
    }

    /**
     * Get grades
     *
     * @return string 
     */
    public function getGrades()
    {
        return $this->grades;
    }

    /**
     * Set weekends
     *
     * @param string $weekends
     * @return Schedule
     */
    public function setWeekends($weekends)
    {
        $this->weekends = $weekends;

        return $this;
    }

    /**
     * Get weekends
     *
     * @return string 
     */
    public function getWeekends()
    {
        return $this->weekends;
    }

    /**
     * Set sharp6am11am
     *
     * @param integer $sharp6am11am
     * @return Schedule
     */
    public function setSharp6am11am($sharp6am11am)
    {
        $this->sharp6am11am = $sharp6am11am;

        return $this;
    }

    /**
     * Get sharp6am11am
     *
     * @return integer 
     */
    public function getSharp6am11am()
    {
        return $this->sharp6am11am;
    }

    /**
     * Set sharp11am4pm
     *
     * @param integer $sharp11am4pm
     * @return Schedule
     */
    public function setSharp11am4pm($sharp11am4pm)
    {
        $this->sharp11am4pm = $sharp11am4pm;

        return $this;
    }

    /**
     * Get sharp11am4pm
     *
     * @return integer 
     */
    public function getSharp11am4pm()
    {
        return $this->sharp11am4pm;
    }

    /**
     * Set sharp4pm9pm
     *
     * @param integer $sharp4pm9pm
     * @return Schedule
     */
    public function setSharp4pm9pm($sharp4pm9pm)
    {
        $this->sharp4pm9pm = $sharp4pm9pm;

        return $this;
    }

    /**
     * Get sharp4pm9pm
     *
     * @return integer 
     */
    public function getSharp4pm9pm()
    {
        return $this->sharp4pm9pm;
    }

    /**
     * Set sharp9pm2am
     *
     * @param integer $sharp9pm2am
     * @return Schedule
     */
    public function setSharp9pm2am($sharp9pm2am)
    {
        $this->sharp9pm2am = $sharp9pm2am;

        return $this;
    }

    /**
     * Get sharp9pm2am
     *
     * @return integer 
     */
    public function getSharp9pm2am()
    {
        return $this->sharp9pm2am;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Schedule
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
     * @return Schedule
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
     * Add courses
     *
     * @param \StudySauce\Bundle\Entity\Course $courses
     * @return Schedule
     */
    public function addCourse(\StudySauce\Bundle\Entity\Course $courses)
    {
        $this->courses[] = $courses;

        return $this;
    }

    /**
     * Remove courses
     *
     * @param \StudySauce\Bundle\Entity\Course $courses
     */
    public function removeCourse(\StudySauce\Bundle\Entity\Course $courses)
    {
        $this->courses->removeElement($courses);
    }

    /**
     * Get courses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Add events
     *
     * @param \StudySauce\Bundle\Entity\Event $events
     * @return Schedule
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
     * Add weeks
     *
     * @param \StudySauce\Bundle\Entity\Week $weeks
     * @return Schedule
     */
    public function addWeek(\StudySauce\Bundle\Entity\Week $weeks)
    {
        $this->weeks[] = $weeks;

        return $this;
    }

    /**
     * Remove weeks
     *
     * @param \StudySauce\Bundle\Entity\Week $weeks
     */
    public function removeWeek(\StudySauce\Bundle\Entity\Week $weeks)
    {
        $this->weeks->removeElement($weeks);
    }

    /**
     * Get weeks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWeeks()
    {
        return $this->weeks;
    }

    /**
     * Add active
     *
     * @param \StudySauce\Bundle\Entity\ActiveStrategy $active
     * @return Schedule
     */
    public function addActive(\StudySauce\Bundle\Entity\ActiveStrategy $active)
    {
        $this->active[] = $active;

        return $this;
    }

    /**
     * Remove active
     *
     * @param \StudySauce\Bundle\Entity\ActiveStrategy $active
     */
    public function removeActive(\StudySauce\Bundle\Entity\ActiveStrategy $active)
    {
        $this->active->removeElement($active);
    }

    /**
     * Get active
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add other
     *
     * @param \StudySauce\Bundle\Entity\OtherStrategy $other
     * @return Schedule
     */
    public function addOther(\StudySauce\Bundle\Entity\OtherStrategy $other)
    {
        $this->other[] = $other;

        return $this;
    }

    /**
     * Remove other
     *
     * @param \StudySauce\Bundle\Entity\OtherStrategy $other
     */
    public function removeOther(\StudySauce\Bundle\Entity\OtherStrategy $other)
    {
        $this->other->removeElement($other);
    }

    /**
     * Get other
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * Add prework
     *
     * @param \StudySauce\Bundle\Entity\PreworkStrategy $prework
     * @return Schedule
     */
    public function addPrework(\StudySauce\Bundle\Entity\PreworkStrategy $prework)
    {
        $this->prework[] = $prework;

        return $this;
    }

    /**
     * Remove prework
     *
     * @param \StudySauce\Bundle\Entity\PreworkStrategy $prework
     */
    public function removePrework(\StudySauce\Bundle\Entity\PreworkStrategy $prework)
    {
        $this->prework->removeElement($prework);
    }

    /**
     * Get prework
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrework()
    {
        return $this->prework;
    }

    /**
     * Add teach
     *
     * @param \StudySauce\Bundle\Entity\TeachStrategy $teach
     * @return Schedule
     */
    public function addTeach(\StudySauce\Bundle\Entity\TeachStrategy $teach)
    {
        $this->teach[] = $teach;

        return $this;
    }

    /**
     * Remove teach
     *
     * @param \StudySauce\Bundle\Entity\TeachStrategy $teach
     */
    public function removeTeach(\StudySauce\Bundle\Entity\TeachStrategy $teach)
    {
        $this->teach->removeElement($teach);
    }

    /**
     * Get teach
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeach()
    {
        return $this->teach;
    }

    /**
     * Add spaced
     *
     * @param \StudySauce\Bundle\Entity\SpacedStrategy $spaced
     * @return Schedule
     */
    public function addSpaced(\StudySauce\Bundle\Entity\SpacedStrategy $spaced)
    {
        $this->spaced[] = $spaced;

        return $this;
    }

    /**
     * Remove spaced
     *
     * @param \StudySauce\Bundle\Entity\SpacedStrategy $spaced
     */
    public function removeSpaced(\StudySauce\Bundle\Entity\SpacedStrategy $spaced)
    {
        $this->spaced->removeElement($spaced);
    }

    /**
     * Get spaced
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSpaced()
    {
        return $this->spaced;
    }
}
