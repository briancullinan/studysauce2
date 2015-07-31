<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Controller\CalcController;

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
     * What kind of grades do you want?
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
     * @ORM\Column(type="array", name="alerts", nullable = true)
     */
    protected $alerts;

    /**
     * @ORM\Column(type="json_array", name="grade_scale")
     */
    protected $gradeScale = [];

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="term", nullable=true)
     */
    protected $term;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="schedule", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"start" = "DESC"})
     */
    protected $events;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClasses()
    {
        return new ArrayCollection(array_values($this->getCourses()->filter(function (Course $b) {return !$b->getDeleted() && $b->getType() == 'c';})->toArray()));
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOthers()
    {
        return new ArrayCollection(array_values($this->getCourses()->filter(function (Course $b) {return !$b->getDeleted() && $b->getType() == 'o';})->toArray()));
    }

    /**
     * @return string
     */
    public function getGPA()
    {
        $hours = $this->getCreditHours();
        if(empty($hours) || !$this->getClasses()->exists(function ($x, Course $c) {return $c->getGPA() !== null;}))
            return null;
        $score = array_sum($this->getClasses()->map(function (Course $c) {
            return floatval($c->getGPA()) * $c->getCreditHours();
        })->toArray());
        return number_format($score / $hours, 2);
    }

    /**
     * @return number
     */
    public function getCreditHours()
    {
        return array_sum($this->getClasses()->map(function (Course $c) {
            return $c->getCreditHours();
        })->toArray());
    }

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
     * Set term
     *
     * @param \DateTime $term
     * @return User
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getTerm()
    {
        return $this->term;
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
     * Set gradeScale
     *
     * @param array $gradeScale
     * @return Schedule
     */
    public function setGradeScale($gradeScale)
    {
        $this->gradeScale = $gradeScale;

        return $this;
    }

    /**
     * Get gradeScale
     *
     * @return array
     */
    public function getGradeScale()
    {
        return $this->gradeScale;
    }

    /**
     * Set alerts
     *
     * @param array $alerts
     * @return Schedule
     */
    public function setAlerts($alerts)
    {
        $this->alerts = $alerts;

        return $this;
    }

    /**
     * Get alerts
     *
     * @return array 
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    public function getStart()
    {
        $start = date_timestamp_set(
            new \DateTime(),
            min(
                array_map(
                    function (Course $c) {
                        return $c->getStartTime()->getTimestamp();
                    },
                    $this->getClasses()->toArray()
                )
            )
        );
        return $start;
    }

    public function getEnd()
    {
        $end = date_timestamp_set(
            new \DateTime(),
            max(
                array_map(
                    function (Course $c) {
                        return $c->getEndTime()->getTimestamp();
                    },
                    $this->getClasses()->toArray()
                )
            )
        );
        return $end;
    }
}
