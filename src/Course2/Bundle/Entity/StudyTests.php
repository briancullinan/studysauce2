<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="study_tests")
 * @ORM\HasLifecycleCallbacks()
 */
class StudyTests
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="studyTests")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="simple_array", length=256, name="types_tests", nullable=true)
     */
    protected $typesTests = [];

    /**
     * @ORM\Column(type="text", name="most_important", nullable=true)
     */
    protected $mostImportant;

    /**
     * @ORM\Column(type="text", name="open_tips1", nullable=true)
     */
    protected $openTips1;

    /**
     * @ORM\Column(type="text", name="open_tips2", nullable=true)
     */
    protected $openTips2;

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
     * @return StudyTests
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
     * @return StudyTests
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
     * Set mostImportant
     *
     * @param string $mostImportant
     * @return StudyTests
     */
    public function setMostImportant($mostImportant)
    {
        $this->mostImportant = $mostImportant;

        return $this;
    }

    /**
     * Get mostImportant
     *
     * @return string 
     */
    public function getMostImportant()
    {
        return $this->mostImportant;
    }

    /**
     * Set openTips1
     *
     * @param string $openTips1
     * @return StudyTests
     */
    public function setOpenTips1($openTips1)
    {
        $this->openTips1 = $openTips1;

        return $this;
    }

    /**
     * Get openTips1
     *
     * @return string 
     */
    public function getOpenTips1()
    {
        return $this->openTips1;
    }

    /**
     * Set openTips2
     *
     * @param string $openTips2
     * @return StudyTests
     */
    public function setOpenTips2($openTips2)
    {
        $this->openTips2 = $openTips2;

        return $this;
    }

    /**
     * Get openTips2
     *
     * @return string 
     */
    public function getOpenTips2()
    {
        return $this->openTips2;
    }

    /**
     * Set typesTests
     *
     * @param array $typesTests
     * @return StudyTests
     */
    public function setTypesTests($typesTests)
    {
        $this->typesTests = $typesTests;

        return $this;
    }

    /**
     * Get typesTests
     *
     * @return array 
     */
    public function getTypesTests()
    {
        return $this->typesTests;
    }
}
