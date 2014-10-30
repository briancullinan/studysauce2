<?php

namespace Course1\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz1")
 * @ORM\HasLifecycleCallbacks()
 */
class Quiz1
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course1", inversedBy="quiz1s")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="string", length=256, name="education", nullable=true)
     */
    protected $education;

    /**
     * @ORM\Column(type="string", length=256, name="mindset", nullable=true)
     */
    protected $mindset;

    /**
     * @ORM\Column(type="string", length=256, name="time_management", nullable=true)
     */
    protected $timeManagement;

    /**
     * @ORM\Column(type="string", length=256, name="devices", nullable=true)
     */
    protected $devices;

    /**
     * @ORM\Column(type="string", length=256, name="study_much", nullable=true)
     */
    protected $studyMuch;

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
     * Set education
     *
     * @param string $education
     * @return Quiz1
     */
    public function setEducation($education)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return string 
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set mindset
     *
     * @param string $mindset
     * @return Quiz1
     */
    public function setMindset($mindset)
    {
        $this->mindset = $mindset;

        return $this;
    }

    /**
     * Get mindset
     *
     * @return string 
     */
    public function getMindset()
    {
        return $this->mindset;
    }

    /**
     * Set timeManagement
     *
     * @param string $timeManagement
     * @return Quiz1
     */
    public function setTimeManagement($timeManagement)
    {
        $this->timeManagement = $timeManagement;

        return $this;
    }

    /**
     * Get timeManagement
     *
     * @return string 
     */
    public function getTimeManagement()
    {
        return $this->timeManagement;
    }

    /**
     * Set devices
     *
     * @param string $devices
     * @return Quiz1
     */
    public function setDevices($devices)
    {
        $this->devices = $devices;

        return $this;
    }

    /**
     * Get devices
     *
     * @return string 
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Set studyMuch
     *
     * @param string $studyMuch
     * @return Quiz1
     */
    public function setStudyMuch($studyMuch)
    {
        $this->studyMuch = $studyMuch;

        return $this;
    }

    /**
     * Get studyMuch
     *
     * @return string 
     */
    public function getStudyMuch()
    {
        return $this->studyMuch;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Quiz1
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
     * @return Quiz1
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
