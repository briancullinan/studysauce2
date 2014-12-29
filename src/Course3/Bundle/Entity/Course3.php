<?php

namespace Course3\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course3")
 * @ORM\HasLifecycleCallbacks()
 */
class Course3
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="StudySauce\Bundle\Entity\User", inversedBy="Course3s")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Course3\Bundle\Entity\Strategies", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $strategies;

    /**
     * @ORM\OneToMany(targetEntity="Course3\Bundle\Entity\GroupStudy", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $groupStudy;

    /**
     * @ORM\OneToMany(targetEntity="Course3\Bundle\Entity\Teaching", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $teaching;

    /**
     * @ORM\OneToMany(targetEntity="Course3\Bundle\Entity\ActiveReading", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $activeReading;

    /**
     * @ORM\OneToMany(targetEntity="Course3\Bundle\Entity\SpacedRepetition", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $spacedRepetition;

    /**
     * @ORM\Column(type="text", name="group_goals", nullable=true)
     */
    protected $groupGoals;

    /**
     * @ORM\Column(type="text", name="feedback", nullable=true)
     */
    protected $feedback;

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
        $this->activeReading = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupStudy = new \Doctrine\Common\Collections\ArrayCollection();
        $this->spacedRepetition = new \Doctrine\Common\Collections\ArrayCollection();
        $this->teaching = new \Doctrine\Common\Collections\ArrayCollection();
        $this->strategies = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Course3
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
     * @return Course3
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
     * @return Course3
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
     * @return Course3
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
     * @return Course3
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
     * @return Course3
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
     * @return Course3
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
     * @param \Course3\Bundle\Entity\Interleaving $interleaving
     * @return Course3
     */
    public function addInterleaving(\Course3\Bundle\Entity\Interleaving $interleaving)
    {
        $this->interleaving[] = $interleaving;

        return $this;
    }

    /**
     * Remove interleaving
     *
     * @param \Course3\Bundle\Entity\Interleaving $interleaving
     */
    public function removeInterleaving(\Course3\Bundle\Entity\Interleaving $interleaving)
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
     * @param \Course3\Bundle\Entity\StudyPlan $studyPlan
     * @return Course3
     */
    public function addStudyPlan(\Course3\Bundle\Entity\StudyPlan $studyPlan)
    {
        $this->studyPlan[] = $studyPlan;

        return $this;
    }

    /**
     * Remove studyPlan
     *
     * @param \Course3\Bundle\Entity\StudyPlan $studyPlan
     */
    public function removeStudyPlan(\Course3\Bundle\Entity\StudyPlan $studyPlan)
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
     * @param \Course3\Bundle\Entity\TestTaking $testTaking
     * @return Course3
     */
    public function addTestTaking(\Course3\Bundle\Entity\TestTaking $testTaking)
    {
        $this->testTaking[] = $testTaking;

        return $this;
    }

    /**
     * Remove testTaking
     *
     * @param \Course3\Bundle\Entity\TestTaking $testTaking
     */
    public function removeTestTaking(\Course3\Bundle\Entity\TestTaking $testTaking)
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
     * @param \Course3\Bundle\Entity\StudyTests $studyTests
     * @return Course3
     */
    public function addStudyTest(\Course3\Bundle\Entity\StudyTests $studyTests)
    {
        $this->studyTests[] = $studyTests;

        return $this;
    }

    /**
     * Remove studyTests
     *
     * @param \Course3\Bundle\Entity\StudyTests $studyTests
     */
    public function removeStudyTest(\Course3\Bundle\Entity\StudyTests $studyTests)
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
     * @param \Course3\Bundle\Entity\StudyMetrics $studyMetrics
     * @return Course3
     */
    public function addStudyMetric(\Course3\Bundle\Entity\StudyMetrics $studyMetrics)
    {
        $this->studyMetrics[] = $studyMetrics;

        return $this;
    }

    /**
     * Remove studyMetrics
     *
     * @param \Course3\Bundle\Entity\StudyMetrics $studyMetrics
     */
    public function removeStudyMetric(\Course3\Bundle\Entity\StudyMetrics $studyMetrics)
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
     * Set lesson6
     *
     * @param integer $lesson6
     * @return Course3
     */
    public function setLesson6($lesson6)
    {
        $this->lesson6 = $lesson6;

        return $this;
    }

    /**
     * Get lesson6
     *
     * @return integer 
     */
    public function getLesson6()
    {
        return $this->lesson6;
    }

    /**
     * Set lesson7
     *
     * @param integer $lesson7
     * @return Course3
     */
    public function setLesson7($lesson7)
    {
        $this->lesson7 = $lesson7;

        return $this;
    }

    /**
     * Get lesson7
     *
     * @return integer 
     */
    public function getLesson7()
    {
        return $this->lesson7;
    }

    /**
     * Set lesson8
     *
     * @param integer $lesson8
     * @return Course3
     */
    public function setLesson8($lesson8)
    {
        $this->lesson8 = $lesson8;

        return $this;
    }

    /**
     * Get lesson8
     *
     * @return integer 
     */
    public function getLesson8()
    {
        return $this->lesson8;
    }

    /**
     * Set lesson9
     *
     * @param integer $lesson9
     * @return Course3
     */
    public function setLesson9($lesson9)
    {
        $this->lesson9 = $lesson9;

        return $this;
    }

    /**
     * Get lesson9
     *
     * @return integer 
     */
    public function getLesson9()
    {
        return $this->lesson9;
    }

    /**
     * Set lesson10
     *
     * @param integer $lesson10
     * @return Course3
     */
    public function setLesson10($lesson10)
    {
        $this->lesson10 = $lesson10;

        return $this;
    }

    /**
     * Get lesson10
     *
     * @return integer 
     */
    public function getLesson10()
    {
        return $this->lesson10;
    }

    /**
     * Set testTypes
     *
     * @param string $testTypes
     * @return Course3
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

    /**
     * Add strategies
     *
     * @param \Course3\Bundle\Entity\Strategies $strategies
     * @return Course3
     */
    public function addStrategy(\Course3\Bundle\Entity\Strategies $strategies)
    {
        $this->strategies[] = $strategies;

        return $this;
    }

    /**
     * Remove strategies
     *
     * @param \Course3\Bundle\Entity\Strategies $strategies
     */
    public function removeStrategy(\Course3\Bundle\Entity\Strategies $strategies)
    {
        $this->strategies->removeElement($strategies);
    }

    /**
     * Get strategies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * Set groupGoals
     *
     * @param string $groupGoals
     * @return Course3
     */
    public function setGroupGoals($groupGoals)
    {
        $this->groupGoals = $groupGoals;

        return $this;
    }

    /**
     * Get groupGoals
     *
     * @return string 
     */
    public function getGroupGoals()
    {
        return $this->groupGoals;
    }

    /**
     * Add groupStudy
     *
     * @param \Course3\Bundle\Entity\GroupStudy $groupStudy
     * @return Course3
     */
    public function addGroupStudy(\Course3\Bundle\Entity\GroupStudy $groupStudy)
    {
        $this->groupStudy[] = $groupStudy;

        return $this;
    }

    /**
     * Remove groupStudy
     *
     * @param \Course3\Bundle\Entity\GroupStudy $groupStudy
     */
    public function removeGroupStudy(\Course3\Bundle\Entity\GroupStudy $groupStudy)
    {
        $this->groupStudy->removeElement($groupStudy);
    }

    /**
     * Get groupStudy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupStudy()
    {
        return $this->groupStudy;
    }

    /**
     * Add teaching
     *
     * @param \Course3\Bundle\Entity\Teaching $teaching
     * @return Course3
     */
    public function addTeaching(\Course3\Bundle\Entity\Teaching $teaching)
    {
        $this->teaching[] = $teaching;

        return $this;
    }

    /**
     * Remove teaching
     *
     * @param \Course3\Bundle\Entity\Teaching $teaching
     */
    public function removeTeaching(\Course3\Bundle\Entity\Teaching $teaching)
    {
        $this->teaching->removeElement($teaching);
    }

    /**
     * Get teaching
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeaching()
    {
        return $this->teaching;
    }

    /**
     * Add activeReading
     *
     * @param \Course3\Bundle\Entity\ActiveReading $activeReading
     * @return Course3
     */
    public function addActiveReading(\Course3\Bundle\Entity\ActiveReading $activeReading)
    {
        $this->activeReading[] = $activeReading;

        return $this;
    }

    /**
     * Remove activeReading
     *
     * @param \Course3\Bundle\Entity\ActiveReading $activeReading
     */
    public function removeActiveReading(\Course3\Bundle\Entity\ActiveReading $activeReading)
    {
        $this->activeReading->removeElement($activeReading);
    }

    /**
     * Get activeReading
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActiveReading()
    {
        return $this->activeReading;
    }

    /**
     * Add spacedRepetition
     *
     * @param \Course3\Bundle\Entity\SpacedRepetition $spacedRepetition
     * @return Course3
     */
    public function addSpacedRepetition(\Course3\Bundle\Entity\SpacedRepetition $spacedRepetition)
    {
        $this->spacedRepetition[] = $spacedRepetition;

        return $this;
    }

    /**
     * Remove spacedRepetition
     *
     * @param \Course3\Bundle\Entity\SpacedRepetition $spacedRepetition
     */
    public function removeSpacedRepetition(\Course3\Bundle\Entity\SpacedRepetition $spacedRepetition)
    {
        $this->spacedRepetition->removeElement($spacedRepetition);
    }

    /**
     * Get spacedRepetition
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSpacedRepetition()
    {
        return $this->spacedRepetition;
    }

    /**
     * Set feedback
     *
     * @param string $feedback
     * @return Course3
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback
     *
     * @return string 
     */
    public function getFeedback()
    {
        return $this->feedback;
    }
}
