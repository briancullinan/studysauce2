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
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Quiz3", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quiz3s;

    /**
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Quiz4", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quiz4s;

    /**
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Quiz5", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quiz5s;

    /**
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Quiz6", mappedBy="course")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quiz6s;

    /**
     * @ORM\Column(type="boolean", name="enjoyed", nullable = true)
     */
    protected $enjoyed;

    /**
     * @ORM\Column(type="string", name="why_study", nullable = true)
     */
    protected $whyStudy;

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
        $this->quiz3s = new \Doctrine\Common\Collections\ArrayCollection();
        $this->quiz4s = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Add quiz3s
     *
     * @param \Course1\Bundle\Entity\Quiz3 $quiz3s
     * @return Course1
     */
    public function addQuiz3(\Course1\Bundle\Entity\Quiz3 $quiz3s)
    {
        $this->quiz3s[] = $quiz3s;

        return $this;
    }

    /**
     * Remove quiz3s
     *
     * @param \Course1\Bundle\Entity\Quiz3 $quiz3s
     */
    public function removeQuiz3(\Course1\Bundle\Entity\Quiz3 $quiz3s)
    {
        $this->quiz3s->removeElement($quiz3s);
    }

    /**
     * Get quiz3s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuiz3s()
    {
        return $this->quiz3s;
    }

    /**
     * Add quiz4s
     *
     * @param \Course1\Bundle\Entity\Quiz4 $quiz4s
     * @return Course1
     */
    public function addQuiz4(\Course1\Bundle\Entity\Quiz4 $quiz4s)
    {
        $this->quiz4s[] = $quiz4s;

        return $this;
    }

    /**
     * Remove quiz4s
     *
     * @param \Course1\Bundle\Entity\Quiz4 $quiz4s
     */
    public function removeQuiz4(\Course1\Bundle\Entity\Quiz4 $quiz4s)
    {
        $this->quiz4s->removeElement($quiz4s);
    }

    /**
     * Get quiz4s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuiz4s()
    {
        return $this->quiz4s;
    }

    /**
     * Set whyStudy
     *
     * @param string $whyStudy
     * @return Course1
     */
    public function setWhyStudy($whyStudy)
    {
        $this->whyStudy = $whyStudy;

        return $this;
    }

    /**
     * Get whyStudy
     *
     * @return string 
     */
    public function getWhyStudy()
    {
        return $this->whyStudy;
    }

    /**
     * Add quiz5s
     *
     * @param \Course1\Bundle\Entity\Quiz5 $quiz5s
     * @return Course1
     */
    public function addQuiz5(\Course1\Bundle\Entity\Quiz5 $quiz5s)
    {
        $this->quiz5s[] = $quiz5s;

        return $this;
    }

    /**
     * Remove quiz5s
     *
     * @param \Course1\Bundle\Entity\Quiz5 $quiz5s
     */
    public function removeQuiz5(\Course1\Bundle\Entity\Quiz5 $quiz5s)
    {
        $this->quiz5s->removeElement($quiz5s);
    }

    /**
     * Get quiz5s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuiz5s()
    {
        return $this->quiz5s;
    }

    /**
     * Add quiz6s
     *
     * @param \Course1\Bundle\Entity\Quiz6 $quiz6s
     * @return Course1
     */
    public function addQuiz6(\Course1\Bundle\Entity\Quiz6 $quiz6s)
    {
        $this->quiz6s[] = $quiz6s;

        return $this;
    }

    /**
     * Remove quiz6s
     *
     * @param \Course1\Bundle\Entity\Quiz6 $quiz6s
     */
    public function removeQuiz6(\Course1\Bundle\Entity\Quiz6 $quiz6s)
    {
        $this->quiz6s->removeElement($quiz6s);
    }

    /**
     * Get quiz6s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuiz6s()
    {
        return $this->quiz6s;
    }

    /**
     * Set enjoyed
     *
     * @param boolean $enjoyed
     * @return Course1
     */
    public function setEnjoyed($enjoyed)
    {
        $this->enjoyed = $enjoyed;

        return $this;
    }

    /**
     * Get enjoyed
     *
     * @return boolean 
     */
    public function getEnjoyed()
    {
        return $this->enjoyed;
    }
}
