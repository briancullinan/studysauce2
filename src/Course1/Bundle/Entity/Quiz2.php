<?php

namespace Course1\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz2")
 * @ORM\HasLifecycleCallbacks()
 */
class Quiz2
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course1", inversedBy="quiz2")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="string", length=256, name="goal_performance", nullable=true)
     */
    protected $goalPerformance;

    /**
     * @ORM\Column(type="string", length=256, name="acronym_specific", nullable=true)
     */
    protected $specific;

    /**
     * @ORM\Column(type="string", length=256, name="acronym_measurable", nullable=true)
     */
    protected $measurable;

    /**
     * @ORM\Column(type="string", length=256, name="acronym_achievable", nullable=true)
     */
    protected $achievable;

    /**
     * @ORM\Column(type="string", length=256, name="acronym_relevant", nullable=true)
     */
    protected $relevant;

    /**
     * @ORM\Column(type="string", length=256, name="acronym_time_bound", nullable=true)
     */
    protected $timeBound;

    /**
     * @ORM\Column(type="string", length=256, name="intrinsic", nullable=true)
     */
    protected $intrinsic;

    /**
     * @ORM\Column(type="string", length=256, name="extrinsic", nullable=true)
     */
    protected $extrinsic;

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
     * Set goalPerformance
     *
     * @param string $goalPerformance
     * @return Quiz2
     */
    public function setGoalPerformance($goalPerformance)
    {
        $this->goalPerformance = $goalPerformance;

        return $this;
    }

    /**
     * Get goalPerformance
     *
     * @return string 
     */
    public function getGoalPerformance()
    {
        return $this->goalPerformance;
    }

    /**
     * Set specific
     *
     * @param string $specific
     * @return Quiz2
     */
    public function setSpecific($specific)
    {
        $this->specific = $specific;

        return $this;
    }

    /**
     * Get specific
     *
     * @return string 
     */
    public function getSpecific()
    {
        return $this->specific;
    }

    /**
     * Set measurable
     *
     * @param string $measurable
     * @return Quiz2
     */
    public function setMeasurable($measurable)
    {
        $this->measurable = $measurable;

        return $this;
    }

    /**
     * Get measurable
     *
     * @return string 
     */
    public function getMeasurable()
    {
        return $this->measurable;
    }

    /**
     * Set achievable
     *
     * @param string $achievable
     * @return Quiz2
     */
    public function setAchievable($achievable)
    {
        $this->achievable = $achievable;

        return $this;
    }

    /**
     * Get achievable
     *
     * @return string 
     */
    public function getAchievable()
    {
        return $this->achievable;
    }

    /**
     * Set relevant
     *
     * @param string $relevant
     * @return Quiz2
     */
    public function setRelevant($relevant)
    {
        $this->relevant = $relevant;

        return $this;
    }

    /**
     * Get relevant
     *
     * @return string 
     */
    public function getRelevant()
    {
        return $this->relevant;
    }

    /**
     * Set timeBound
     *
     * @param string $timeBound
     * @return Quiz2
     */
    public function setTimeBound($timeBound)
    {
        $this->timeBound = $timeBound;

        return $this;
    }

    /**
     * Get timeBound
     *
     * @return string 
     */
    public function getTimeBound()
    {
        return $this->timeBound;
    }

    /**
     * Set intrinsic
     *
     * @param string $intrinsic
     * @return Quiz2
     */
    public function setIntrinsic($intrinsic)
    {
        $this->intrinsic = $intrinsic;

        return $this;
    }

    /**
     * Get intrinsic
     *
     * @return string 
     */
    public function getIntrinsic()
    {
        return $this->intrinsic;
    }

    /**
     * Set extrinsic
     *
     * @param string $extrinsic
     * @return Quiz2
     */
    public function setExtrinsic($extrinsic)
    {
        $this->extrinsic = $extrinsic;

        return $this;
    }

    /**
     * Get extrinsic
     *
     * @return string 
     */
    public function getExtrinsic()
    {
        return $this->extrinsic;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Quiz2
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
     * @return Quiz2
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
