<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="study_plan")
 * @ORM\HasLifecycleCallbacks()
 */
class StudyPlan
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="studyPlan")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="integer", name="multiply", nullable=true)
     */
    protected $multiply;

    /**
     * @ORM\Column(type="text", name="procrastination", nullable=true)
     */
    protected $procrastination;

    /**
     * @ORM\Column(type="text", name="study_sessions", nullable=true)
     */
    protected $studySessions;

    /**
     * @ORM\Column(type="text", name="stick_plan", nullable=true)
     */
    protected $stickPlan;

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
     * Set multiply
     *
     * @param integer $multiply
     * @return StudyPlan
     */
    public function setMultiply($multiply)
    {
        $this->multiply = $multiply;

        return $this;
    }

    /**
     * Get multiply
     *
     * @return integer 
     */
    public function getMultiply()
    {
        return $this->multiply;
    }

    /**
     * Set procrastination
     *
     * @param string $procrastination
     * @return StudyPlan
     */
    public function setProcrastination($procrastination)
    {
        $this->procrastination = $procrastination;

        return $this;
    }

    /**
     * Get procrastination
     *
     * @return string 
     */
    public function getProcrastination()
    {
        return $this->procrastination;
    }

    /**
     * Set studySessions
     *
     * @param string $studySessions
     * @return StudyPlan
     */
    public function setStudySessions($studySessions)
    {
        $this->studySessions = $studySessions;

        return $this;
    }

    /**
     * Get studySessions
     *
     * @return string 
     */
    public function getStudySessions()
    {
        return $this->studySessions;
    }

    /**
     * Set stickPlan
     *
     * @param string $stickPlan
     * @return StudyPlan
     */
    public function setStickPlan($stickPlan)
    {
        $this->stickPlan = $stickPlan;

        return $this;
    }

    /**
     * Get stickPlan
     *
     * @return string 
     */
    public function getStickPlan()
    {
        return $this->stickPlan;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return StudyPlan
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
     * Set course
     *
     * @param \Course2\Bundle\Entity\Course2 $course
     * @return StudyPlan
     */
    public function setCourse(\Course2\Bundle\Entity\Course2 $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Course2\Bundle\Entity\Course2 
     */
    public function getCourse()
    {
        return $this->course;
    }
}
