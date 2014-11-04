<?php

namespace Course1\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz4")
 * @ORM\HasLifecycleCallbacks()
 */
class Quiz4
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course1", inversedBy="quiz4s")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="string", length=256, name="multitask", nullable=true)
     */
    protected $multitask;

    /**
     * @ORM\Column(type="string", length=256, name="downside", nullable=true)
     */
    protected $downside;

    /**
     * @ORM\Column(type="string", length=256, name="lower_score", nullable=true)
     */
    protected $lowerScore;

    /**
     * @ORM\Column(type="string", length=256, name="distraction", nullable=true)
     */
    protected $distraction;

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
     * Set multitask
     *
     * @param string $multitask
     * @return Quiz4
     */
    public function setMultitask($multitask)
    {
        $this->multitask = $multitask;

        return $this;
    }

    /**
     * Get multitask
     *
     * @return string 
     */
    public function getMultitask()
    {
        return $this->multitask;
    }

    /**
     * Set downside
     *
     * @param string $downside
     * @return Quiz4
     */
    public function setDownside($downside)
    {
        $this->downside = $downside;

        return $this;
    }

    /**
     * Get downside
     *
     * @return string 
     */
    public function getDownside()
    {
        return $this->downside;
    }

    /**
     * Set lowerScore
     *
     * @param string $lowerScore
     * @return Quiz4
     */
    public function setLowerScore($lowerScore)
    {
        $this->lowerScore = $lowerScore;

        return $this;
    }

    /**
     * Get lowerScore
     *
     * @return string 
     */
    public function getLowerScore()
    {
        return $this->lowerScore;
    }

    /**
     * Set distraction
     *
     * @param string $distraction
     * @return Quiz4
     */
    public function setDistraction($distraction)
    {
        $this->distraction = $distraction;

        return $this;
    }

    /**
     * Get distraction
     *
     * @return string 
     */
    public function getDistraction()
    {
        return $this->distraction;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Quiz4
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
     * @return Quiz4
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
