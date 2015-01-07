<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course2")
 * @ORM\HasLifecycleCallbacks()
 */
class Course2
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="StudySauce\Bundle\Entity\User", inversedBy="Course2s")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Course2\Bundle\Entity\Interleaving", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $interleaving;

    /**
     * @ORM\OneToMany(targetEntity="Course2\Bundle\Entity\StudyPlan", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $studyPlan;

    /**
     * @ORM\OneToMany(targetEntity="Course2\Bundle\Entity\TestTaking", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $testTaking;

    /**
     * @ORM\OneToMany(targetEntity="Course2\Bundle\Entity\StudyTests", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $studyTests;

    /**
     * @ORM\OneToMany(targetEntity="Course2\Bundle\Entity\StudyMetrics", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $studyMetrics;

    /**
     * @ORM\Column(type="text", name="test_types", nullable=true)
     */
    protected $testTypes;

    /**
     * @ORM\Column(type="integer", name="lesson1", options={"default"=0})
     */
    protected $lesson1 = 0;

    /**
     * @ORM\Column(type="integer", name="lesson2", options={"default"=0})
     */
    protected $lesson2 = 0;

    /**
     * @ORM\Column(type="integer", name="lesson3", options={"default"=0})
     */
    protected $lesson3 = 0;

    /**
     * @ORM\Column(type="integer", name="lesson4", options={"default"=0})
     */
    protected $lesson4 = 0;

    /**
     * @ORM\Column(type="integer", name="lesson5", options={"default"=0})
     */
    protected $lesson5 = 0;

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
     * Constructor
     */
    public function __construct()
    {
        $this->interleaving = new \Doctrine\Common\Collections\ArrayCollection();
        $this->studyPlan = new \Doctrine\Common\Collections\ArrayCollection();
        $this->testTaking = new \Doctrine\Common\Collections\ArrayCollection();
        $this->studyTests = new \Doctrine\Common\Collections\ArrayCollection();
        $this->studyMetrics = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set lesson1
     *
     * @param integer $lesson1
     * @return Course2
     */
    public function setLesson1($lesson1)
    {
        $this->lesson1 = $lesson1;

        return $this;
    }

    /**
     * Get lesson1
     *
     * @return integer 
     */
    public function getLesson1()
    {
        return $this->lesson1;
    }

    /**
     * Set lesson2
     *
     * @param integer $lesson2
     * @return Course2
     */
    public function setLesson2($lesson2)
    {
        $this->lesson2 = $lesson2;

        return $this;
    }

    /**
     * Get lesson2
     *
     * @return integer 
     */
    public function getLesson2()
    {
        return $this->lesson2;
    }

    /**
     * Set lesson3
     *
     * @param integer $lesson3
     * @return Course2
     */
    public function setLesson3($lesson3)
    {
        $this->lesson3 = $lesson3;

        return $this;
    }

    /**
     * Get lesson3
     *
     * @return integer 
     */
    public function getLesson3()
    {
        return $this->lesson3;
    }

    /**
     * Set lesson4
     *
     * @param integer $lesson4
     * @return Course2
     */
    public function setLesson4($lesson4)
    {
        $this->lesson4 = $lesson4;

        return $this;
    }

    /**
     * Get lesson4
     *
     * @return integer 
     */
    public function getLesson4()
    {
        return $this->lesson4;
    }

    /**
     * Set lesson5
     *
     * @param integer $lesson5
     * @return Course2
     */
    public function setLesson5($lesson5)
    {
        $this->lesson5 = $lesson5;

        return $this;
    }

    /**
     * Get lesson5
     *
     * @return integer 
     */
    public function getLesson5()
    {
        return $this->lesson5;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Course2
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
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return Course2
     */
    public function setUser(\StudySauce\Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \StudySauce\Bundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add interleaving
     *
     * @param \Course2\Bundle\Entity\Interleaving $interleaving
     * @return Course2
     */
    public function addInterleaving(\Course2\Bundle\Entity\Interleaving $interleaving)
    {
        $this->interleaving[] = $interleaving;

        return $this;
    }

    /**
     * Remove interleaving
     *
     * @param \Course2\Bundle\Entity\Interleaving $interleaving
     */
    public function removeInterleaving(\Course2\Bundle\Entity\Interleaving $interleaving)
    {
        $this->interleaving->removeElement($interleaving);
    }

    /**
     * Get interleaving
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterleaving()
    {
        return $this->interleaving;
    }

    /**
     * Add studyPlan
     *
     * @param \Course2\Bundle\Entity\StudyPlan $studyPlan
     * @return Course2
     */
    public function addStudyPlan(\Course2\Bundle\Entity\StudyPlan $studyPlan)
    {
        $this->studyPlan[] = $studyPlan;

        return $this;
    }

    /**
     * Remove studyPlan
     *
     * @param \Course2\Bundle\Entity\StudyPlan $studyPlan
     */
    public function removeStudyPlan(\Course2\Bundle\Entity\StudyPlan $studyPlan)
    {
        $this->studyPlan->removeElement($studyPlan);
    }

    /**
     * Get studyPlan
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStudyPlan()
    {
        return $this->studyPlan;
    }

    /**
     * Add testTaking
     *
     * @param \Course2\Bundle\Entity\TestTaking $testTaking
     * @return Course2
     */
    public function addTestTaking(\Course2\Bundle\Entity\TestTaking $testTaking)
    {
        $this->testTaking[] = $testTaking;

        return $this;
    }

    /**
     * Remove testTaking
     *
     * @param \Course2\Bundle\Entity\TestTaking $testTaking
     */
    public function removeTestTaking(\Course2\Bundle\Entity\TestTaking $testTaking)
    {
        $this->testTaking->removeElement($testTaking);
    }

    /**
     * Get testTaking
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTestTaking()
    {
        return $this->testTaking;
    }

    /**
     * Add studyTests
     *
     * @param \Course2\Bundle\Entity\StudyTests $studyTests
     * @return Course2
     */
    public function addStudyTest(\Course2\Bundle\Entity\StudyTests $studyTests)
    {
        $this->studyTests[] = $studyTests;

        return $this;
    }

    /**
     * Remove studyTests
     *
     * @param \Course2\Bundle\Entity\StudyTests $studyTests
     */
    public function removeStudyTest(\Course2\Bundle\Entity\StudyTests $studyTests)
    {
        $this->studyTests->removeElement($studyTests);
    }

    /**
     * Get studyTests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStudyTests()
    {
        return $this->studyTests;
    }

    /**
     * Add studyMetrics
     *
     * @param \Course2\Bundle\Entity\StudyMetrics $studyMetrics
     * @return Course2
     */
    public function addStudyMetric(\Course2\Bundle\Entity\StudyMetrics $studyMetrics)
    {
        $this->studyMetrics[] = $studyMetrics;

        return $this;
    }

    /**
     * Remove studyMetrics
     *
     * @param \Course2\Bundle\Entity\StudyMetrics $studyMetrics
     */
    public function removeStudyMetric(\Course2\Bundle\Entity\StudyMetrics $studyMetrics)
    {
        $this->studyMetrics->removeElement($studyMetrics);
    }

    /**
     * Get studyMetrics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStudyMetrics()
    {
        return $this->studyMetrics;
    }

    /**
     * Set testTypes
     *
     * @param string $testTypes
     * @return Course2
     */
    public function setTestTypes($testTypes)
    {
        $this->testTypes = $testTypes;

        return $this;
    }

    /**
     * Get testTypes
     *
     * @return string 
     */
    public function getTestTypes()
    {
        return $this->testTypes;
    }

}
