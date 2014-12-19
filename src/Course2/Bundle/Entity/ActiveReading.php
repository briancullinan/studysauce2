<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="active_reading")
 * @ORM\HasLifecycleCallbacks()
 */
class ActiveReading
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="activeReading")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="text", name="what_reading", nullable=true)
     */
    protected $whatReading;

    /**
     * @ORM\Column(type="boolean", name="highlighting", nullable=true)
     */
    protected $highlighting;

    /**
     * @ORM\Column(type="boolean", name="skimming", nullable=true)
     */
    protected $skimming;

    /**
     * @ORM\Column(type="boolean", name="self_explanation", nullable=true)
     */
    protected $selfExplanation;

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
     * Set whatReading
     *
     * @param string $whatReading
     * @return ActiveReading
     */
    public function setWhatReading($whatReading)
    {
        $this->whatReading = $whatReading;

        return $this;
    }

    /**
     * Get whatReading
     *
     * @return string 
     */
    public function getWhatReading()
    {
        return $this->whatReading;
    }

    /**
     * Set highlighting
     *
     * @param boolean $highlighting
     * @return ActiveReading
     */
    public function setHighlighting($highlighting)
    {
        $this->highlighting = $highlighting;

        return $this;
    }

    /**
     * Get highlighting
     *
     * @return boolean 
     */
    public function getHighlighting()
    {
        return $this->highlighting;
    }

    /**
     * Set skimming
     *
     * @param boolean $skimming
     * @return ActiveReading
     */
    public function setSkimming($skimming)
    {
        $this->skimming = $skimming;

        return $this;
    }

    /**
     * Get skimming
     *
     * @return boolean 
     */
    public function getSkimming()
    {
        return $this->skimming;
    }

    /**
     * Set selfExplanation
     *
     * @param boolean $selfExplanation
     * @return ActiveReading
     */
    public function setSelfExplanation($selfExplanation)
    {
        $this->selfExplanation = $selfExplanation;

        return $this;
    }

    /**
     * Get selfExplanation
     *
     * @return boolean 
     */
    public function getSelfExplanation()
    {
        return $this->selfExplanation;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ActiveReading
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
     * @return ActiveReading
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
