<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="grade")
 * @ORM\HasLifecycleCallbacks()
 */
class Grade
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="grades")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="string", length=256, name="assignment")
     */
    protected $assignment;

    /**
     * @ORM\Column(type="integer", name="percent", nullable=true)
     */
    protected $percent;

    /**
     * @ORM\Column(type="integer", name="score", nullable=true)
     */
    protected $score;

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
     * @return string
     */
    public function getGrade()
    {
        if(empty($this->getCourse()))
            return null;
        if($this->getCourse()->getSchedule()->getGradeScale())
        {
            if($this->getScore() >= 97)
                return 'A+';
            elseif($this->getScore() >= 93)
                return 'A';
            elseif($this->getScore() >= 90)
                return 'A-';
            elseif($this->getScore() >= 87)
                return 'B+';
            elseif($this->getScore() >= 83)
                return 'B';
            elseif($this->getScore() >= 80)
                return 'B-';
            elseif($this->getScore() >= 77)
                return 'C+';
            elseif($this->getScore() >= 73)
                return 'C';
            elseif($this->getScore() >= 70)
                return 'C-';
            elseif($this->getScore() >= 67)
                return 'D+';
            elseif($this->getScore() >= 63)
                return 'D';
            elseif($this->getScore() >= 60)
                return 'D-';
            else
                return 'F';
        }
        else
        {
            if($this->getScore() >= 90)
                return 'A';
            elseif($this->getScore() >= 80)
                return 'B';
            elseif($this->getScore() >= 70)
                return 'C';
            elseif($this->getScore() >= 60)
                return 'D';
            else
                return 'F';
        }
    }

    /**
     * @return string
     */
    public function getGPA()
    {
        if(empty($this->getCourse()))
            return null;
        if($this->getCourse()->getSchedule()->getGradeScale())
        {
            if($this->getScore() >= 97)
                return '4.0';
            elseif($this->getScore() >= 93)
                return '4.0';
            elseif($this->getScore() >= 90)
                return '3.7';
            elseif($this->getScore() >= 87)
                return '3.3';
            elseif($this->getScore() >= 83)
                return '3.0';
            elseif($this->getScore() >= 80)
                return '2.7';
            elseif($this->getScore() >= 77)
                return '2.3';
            elseif($this->getScore() >= 73)
                return '2.0';
            elseif($this->getScore() >= 70)
                return '1.7';
            elseif($this->getScore() >= 67)
                return '1.3';
            elseif($this->getScore() >= 63)
                return '1.0';
            elseif($this->getScore() >= 60)
                return '0.7';
            else
                return '0.0';
        }
        else
        {
            if($this->getScore() >= 90)
                return '4.0';
            elseif($this->getScore() >= 80)
                return '3.0';
            elseif($this->getScore() >= 70)
                return '2.0';
            elseif($this->getScore() >= 60)
                return '1.0';
            else
                return '0.0';
        }
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
     * Set assignment
     *
     * @param string $assignment
     * @return Grade
     */
    public function setAssignment($assignment)
    {
        $this->assignment = $assignment;

        return $this;
    }

    /**
     * Get assignment
     *
     * @return string 
     */
    public function getAssignment()
    {
        return $this->assignment;
    }

    /**
     * Set percent
     *
     * @param integer $percent
     * @return Grade
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return integer 
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return Grade
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Grade
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
     * @param \StudySauce\Bundle\Entity\Course $course
     * @return Grade
     */
    public function setCourse(\StudySauce\Bundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \StudySauce\Bundle\Entity\Course 
     */
    public function getCourse()
    {
        return $this->course;
    }
}
