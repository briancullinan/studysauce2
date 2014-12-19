<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="spaced_repetition")
 * @ORM\HasLifecycleCallbacks()
 */
class SpacedRepetition
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="spacedRepetition")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="boolean", name="space_out", nullable=true)
     */
    protected $spaceOut;

    /**
     * @ORM\Column(type="text", name="forgetting", nullable=true)
     */
    protected $forgetting;

    /**
     * @ORM\Column(type="text", length=256, name="revisiting", nullable=true)
     */
    protected $revisiting;

    /**
     * @ORM\Column(type="text", length=256, name="another_name", nullable=true)
     */
    protected $anotherName;

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
     * Set created
     *
     * @param \DateTime $created
     * @return SpacedRepetition
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
     * @return SpacedRepetition
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

    /**
     * Set spaceOut
     *
     * @param boolean $spaceOut
     * @return SpacedRepetition
     */
    public function setSpaceOut($spaceOut)
    {
        $this->spaceOut = $spaceOut;

        return $this;
    }

    /**
     * Get spaceOut
     *
     * @return boolean 
     */
    public function getSpaceOut()
    {
        return $this->spaceOut;
    }

    /**
     * Set forgetting
     *
     * @param string $forgetting
     * @return SpacedRepetition
     */
    public function setForgetting($forgetting)
    {
        $this->forgetting = $forgetting;

        return $this;
    }

    /**
     * Get forgetting
     *
     * @return string 
     */
    public function getForgetting()
    {
        return $this->forgetting;
    }

    /**
     * Set revisiting
     *
     * @param string $revisiting
     * @return SpacedRepetition
     */
    public function setRevisiting($revisiting)
    {
        $this->revisiting = $revisiting;

        return $this;
    }

    /**
     * Get revisiting
     *
     * @return string 
     */
    public function getRevisiting()
    {
        return $this->revisiting;
    }

    /**
     * Set anotherName
     *
     * @param string $anotherName
     * @return SpacedRepetition
     */
    public function setAnotherName($anotherName)
    {
        $this->anotherName = $anotherName;

        return $this;
    }

    /**
     * Get anotherName
     *
     * @return string 
     */
    public function getAnotherName()
    {
        return $this->anotherName;
    }
}
