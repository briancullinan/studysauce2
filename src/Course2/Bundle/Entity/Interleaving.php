<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="interleaving")
 * @ORM\HasLifecycleCallbacks()
 */
class Interleaving
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="interleaving")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="text", length=256, name="multiple_sessions", nullable=true)
     */
    protected $multipleSessions;

    /**
     * @ORM\Column(type="text", length=256, name="other_name", nullable=true)
     */
    protected $otherName;

    /**
     * @ORM\Column(type="boolean", name="types_courses", nullable=true)
     */
    protected $typesCourses;

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
     * @return Interleaving
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
     * @return Interleaving
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
     * Set multipleSessions
     *
     * @param string $multipleSessions
     * @return Interleaving
     */
    public function setMultipleSessions($multipleSessions)
    {
        $this->multipleSessions = $multipleSessions;

        return $this;
    }

    /**
     * Get multipleSessions
     *
     * @return string 
     */
    public function getMultipleSessions()
    {
        return $this->multipleSessions;
    }

    /**
     * Set otherName
     *
     * @param string $otherName
     * @return Interleaving
     */
    public function setOtherName($otherName)
    {
        $this->otherName = $otherName;

        return $this;
    }

    /**
     * Get otherName
     *
     * @return string 
     */
    public function getOtherName()
    {
        return $this->otherName;
    }

    /**
     * Set typesCourses
     *
     * @param boolean $typesCourses
     * @return Interleaving
     */
    public function setTypesCourses($typesCourses)
    {
        $this->typesCourses = $typesCourses;

        return $this;
    }

    /**
     * Get typesCourses
     *
     * @return boolean 
     */
    public function getTypesCourses()
    {
        return $this->typesCourses;
    }
}
