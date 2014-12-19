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
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="testTaking")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="boolean", name="idea_cram")
     */
    protected $ideaCram;

    /**
     * @ORM\Column(type="text", name="breathing")
     */
    protected $breathing;

    /**
     * @ORM\Column(type="text", name="skimming")
     */
    protected $skimming;

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
     * @return TestTaking
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
     * @return TestTaking
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
     * Set ideaCram
     *
     * @param boolean $ideaCram
     * @return TestTaking
     */
    public function setIdeaCram($ideaCram)
    {
        $this->ideaCram = $ideaCram;

        return $this;
    }

    /**
     * Get ideaCram
     *
     * @return boolean 
     */
    public function getIdeaCram()
    {
        return $this->ideaCram;
    }

    /**
     * Set breathing
     *
     * @param string $breathing
     * @return TestTaking
     */
    public function setBreathing($breathing)
    {
        $this->breathing = $breathing;

        return $this;
    }

    /**
     * Get breathing
     *
     * @return string 
     */
    public function getBreathing()
    {
        return $this->breathing;
    }

    /**
     * Set skimming
     *
     * @param string $skimming
     * @return TestTaking
     */
    public function setSkimming($skimming)
    {
        $this->skimming = $skimming;

        return $this;
    }

    /**
     * Get skimming
     *
     * @return string 
     */
    public function getSkimming()
    {
        return $this->skimming;
    }
}
