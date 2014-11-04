<?php

namespace Course1\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz3")
 * @ORM\HasLifecycleCallbacks()
 */
class Quiz3
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course1", inversedBy="quiz3s")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="string", length=256, name="active_memory", nullable=true)
     */
    protected $activeMemory;

    /**
     * @ORM\Column(type="string", length=256, name="reference_memory", nullable=true)
     */
    protected $referenceMemory;

    /**
     * @ORM\Column(type="string", length=256, name="study_goal", nullable=true)
     */
    protected $studyGoal;

    /**
     * @ORM\Column(type="string", length=256, name="procrastinating", nullable=true)
     */
    protected $procrastinating;

    /**
     * @ORM\Column(type="string", length=256, name="deadlines", nullable=true)
     */
    protected $deadlines;

    /**
     * @ORM\Column(type="string", length=256, name="plan", nullable=true)
     */
    protected $plan;

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
     * Set activeMemory
     *
     * @param string $activeMemory
     * @return Quiz3
     */
    public function setActiveMemory($activeMemory)
    {
        $this->activeMemory = $activeMemory;

        return $this;
    }

    /**
     * Get activeMemory
     *
     * @return string 
     */
    public function getActiveMemory()
    {
        return $this->activeMemory;
    }

    /**
     * Set referenceMemory
     *
     * @param string $referenceMemory
     * @return Quiz3
     */
    public function setReferenceMemory($referenceMemory)
    {
        $this->referenceMemory = $referenceMemory;

        return $this;
    }

    /**
     * Get referenceMemory
     *
     * @return string 
     */
    public function getReferenceMemory()
    {
        return $this->referenceMemory;
    }

    /**
     * Set studyGoal
     *
     * @param string $studyGoal
     * @return Quiz3
     */
    public function setStudyGoal($studyGoal)
    {
        $this->studyGoal = $studyGoal;

        return $this;
    }

    /**
     * Get studyGoal
     *
     * @return string 
     */
    public function getStudyGoal()
    {
        return $this->studyGoal;
    }

    /**
     * Set procrastinating
     *
     * @param string $procrastinating
     * @return Quiz3
     */
    public function setProcrastinating($procrastinating)
    {
        $this->procrastinating = $procrastinating;

        return $this;
    }

    /**
     * Get procrastinating
     *
     * @return string 
     */
    public function getProcrastinating()
    {
        return $this->procrastinating;
    }

    /**
     * Set deadlines
     *
     * @param string $deadlines
     * @return Quiz3
     */
    public function setDeadlines($deadlines)
    {
        $this->deadlines = $deadlines;

        return $this;
    }

    /**
     * Get deadlines
     *
     * @return string 
     */
    public function getDeadlines()
    {
        return $this->deadlines;
    }

    /**
     * Set plan
     *
     * @param string $plan
     * @return Quiz3
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return string 
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Quiz3
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
     * @param \Course1\Bundle\Entity\Course1 $course
     * @return Quiz3
     */
    public function setCourse(\Course1\Bundle\Entity\Course1 $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Course1\Bundle\Entity\Course1 
     */
    public function getCourse()
    {
        return $this->course;
    }
}
