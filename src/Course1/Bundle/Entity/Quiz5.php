<?php

namespace Course1\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz5")
 * @ORM\HasLifecycleCallbacks()
 */
class Quiz5
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course1", inversedBy="quiz5")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="boolean", name="bed", nullable=true)
     */
    protected $bed;

    /**
     * @ORM\Column(type="boolean", name="mozart", nullable=true)
     */
    protected $mozart;

    /**
     * @ORM\Column(type="boolean", name="nature", nullable=true)
     */
    protected $nature;

    /**
     * @ORM\Column(type="boolean", name="breaks", nullable=true)
     */
    protected $breaks;

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
     * Set bed
     *
     * @param boolean $bed
     * @return Quiz5
     */
    public function setBed($bed)
    {
        $this->bed = $bed;

        return $this;
    }

    /**
     * Get bed
     *
     * @return boolean 
     */
    public function getBed()
    {
        return $this->bed;
    }

    /**
     * Set mozart
     *
     * @param boolean $mozart
     * @return Quiz5
     */
    public function setMozart($mozart)
    {
        $this->mozart = $mozart;

        return $this;
    }

    /**
     * Get mozart
     *
     * @return boolean 
     */
    public function getMozart()
    {
        return $this->mozart;
    }

    /**
     * Set nature
     *
     * @param boolean $nature
     * @return Quiz5
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature
     *
     * @return boolean 
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set breaks
     *
     * @param boolean $breaks
     * @return Quiz5
     */
    public function setBreaks($breaks)
    {
        $this->breaks = $breaks;

        return $this;
    }

    /**
     * Get breaks
     *
     * @return boolean 
     */
    public function getBreaks()
    {
        return $this->breaks;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Quiz5
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
     * @return Quiz5
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
