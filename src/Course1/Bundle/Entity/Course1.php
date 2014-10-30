<?php

namespace Course1\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="course1")
 * @ORM\HasLifecycleCallbacks()
 */
class Course1
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="StudySauce\Bundle\Entity\User", inversedBy="course1s")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Quiz1", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quiz1s;

    /**
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Quiz2", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quiz2s;

    /**
     * @ORM\Column(type="integer", name="level")
     */
    protected $level = 0;

    /**
     * @ORM\Column(type="integer", name="step")
     */
    protected $step = 0;

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
        $this->quiz1s = new \Doctrine\Common\Collections\ArrayCollection();
        $this->quiz2s = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set level
     *
     * @param integer $level
     * @return Course1
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set step
     *
     * @param integer $step
     * @return Course1
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step
     *
     * @return integer 
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Course1
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
     * @return Course1
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
     * Add quiz1s
     *
     * @param \Course1\Bundle\Entity\Quiz1 $quiz1s
     * @return Course1
     */
    public function addQuiz1(\Course1\Bundle\Entity\Quiz1 $quiz1s)
    {
        $this->quiz1s[] = $quiz1s;

        return $this;
    }

    /**
     * Remove quiz1s
     *
     * @param \Course1\Bundle\Entity\Quiz1 $quiz1s
     */
    public function removeQuiz1(\Course1\Bundle\Entity\Quiz1 $quiz1s)
    {
        $this->quiz1s->removeElement($quiz1s);
    }

    /**
     * Get quiz1s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuiz1s()
    {
        return $this->quiz1s;
    }

    /**
     * Add quiz2s
     *
     * @param \Course1\Bundle\Entity\Quiz2 $quiz2s
     * @return Course1
     */
    public function addQuiz2(\Course1\Bundle\Entity\Quiz2 $quiz2s)
    {
        $this->quiz2s[] = $quiz2s;

        return $this;
    }

    /**
     * Remove quiz2s
     *
     * @param \Course1\Bundle\Entity\Quiz2 $quiz2s
     */
    public function removeQuiz2(\Course1\Bundle\Entity\Quiz2 $quiz2s)
    {
        $this->quiz2s->removeElement($quiz2s);
    }

    /**
     * Get quiz2s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuiz2s()
    {
        return $this->quiz2s;
    }
}
