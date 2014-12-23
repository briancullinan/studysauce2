<?php

namespace Course3\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="teaching")
 * @ORM\HasLifecycleCallbacks()
 */
class Teaching
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course3", inversedBy="teaching")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="text", name="new_language", nullable=true)
     */
    protected $newLanguage;

    /**
     * @ORM\Column(type="boolean", name="memorizing", nullable=true)
     */
    protected $memorizing;

    /**
     * @ORM\Column(type="text", name="videotaping", nullable=true)
     */
    protected $videotaping;

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
     * Set newLanguage
     *
     * @param string $newLanguage
     * @return Teaching
     */
    public function setNewLanguage($newLanguage)
    {
        $this->newLanguage = $newLanguage;

        return $this;
    }

    /**
     * Get newLanguage
     *
     * @return string 
     */
    public function getNewLanguage()
    {
        return $this->newLanguage;
    }

    /**
     * Set memorizing
     *
     * @param boolean $memorizing
     * @return Teaching
     */
    public function setMemorizing($memorizing)
    {
        $this->memorizing = $memorizing;

        return $this;
    }

    /**
     * Get memorizing
     *
     * @return boolean 
     */
    public function getMemorizing()
    {
        return $this->memorizing;
    }

    /**
     * Set videotaping
     *
     * @param string $videotaping
     * @return Teaching
     */
    public function setVideotaping($videotaping)
    {
        $this->videotaping = $videotaping;

        return $this;
    }

    /**
     * Get videotaping
     *
     * @return string 
     */
    public function getVideotaping()
    {
        return $this->videotaping;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Teaching
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
     * @param \Course3\Bundle\Entity\Course3 $course
     * @return Teaching
     */
    public function setCourse(\Course3\Bundle\Entity\Course3 $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Course3\Bundle\Entity\Course3
     */
    public function getCourse()
    {
        return $this->course;
    }
}
