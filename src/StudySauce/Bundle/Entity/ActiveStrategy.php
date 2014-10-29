<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="active_strategy")
 * @ORM\HasLifecycleCallbacks()
 */
class ActiveStrategy
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Event", inversedBy="active")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    protected $event;

    /**
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="active")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
     */
    protected $schedule;

    /**
     * @ORM\Column(type="string", name="skim")
     */
    protected $skim;

    /**
     * @ORM\Column(type="string", name="why")
     */
    protected $why;

    /**
     * @ORM\Column(type="string", name="questions")
     */
    protected $questions;

    /**
     * @ORM\Column(type="string", name="summarize")
     */
    protected $summarize;

    /**
     * @ORM\Column(type="string", name="exam")
     */
    protected $exam;

    /**
     * @ORM\Column(type="boolean", name="is_default")
     */
    protected $isDefault = false;

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
     * Set skim
     *
     * @param string $skim
     * @return ActiveStrategy
     */
    public function setSkim($skim)
    {
        $this->skim = $skim;

        return $this;
    }

    /**
     * Get skim
     *
     * @return string 
     */
    public function getSkim()
    {
        return $this->skim;
    }

    /**
     * Set why
     *
     * @param string $why
     * @return ActiveStrategy
     */
    public function setWhy($why)
    {
        $this->why = $why;

        return $this;
    }

    /**
     * Get why
     *
     * @return string 
     */
    public function getWhy()
    {
        return $this->why;
    }

    /**
     * Set questions
     *
     * @param string $questions
     * @return ActiveStrategy
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions
     *
     * @return string 
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set summarize
     *
     * @param string $summarize
     * @return ActiveStrategy
     */
    public function setSummarize($summarize)
    {
        $this->summarize = $summarize;

        return $this;
    }

    /**
     * Get summarize
     *
     * @return string 
     */
    public function getSummarize()
    {
        return $this->summarize;
    }

    /**
     * Set exam
     *
     * @param string $exam
     * @return ActiveStrategy
     */
    public function setExam($exam)
    {
        $this->exam = $exam;

        return $this;
    }

    /**
     * Get exam
     *
     * @return string 
     */
    public function getExam()
    {
        return $this->exam;
    }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     * @return ActiveStrategy
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean 
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ActiveStrategy
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
     * Set event
     *
     * @param \StudySauce\Bundle\Entity\Event $event
     * @return ActiveStrategy
     */
    public function setEvent(\StudySauce\Bundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \StudySauce\Bundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set schedule
     *
     * @param \StudySauce\Bundle\Entity\Schedule $schedule
     * @return ActiveStrategy
     */
    public function setSchedule(\StudySauce\Bundle\Entity\Schedule $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \StudySauce\Bundle\Entity\Schedule 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
}
