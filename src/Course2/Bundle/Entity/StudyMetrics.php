<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="study_metrics")
 * @ORM\HasLifecycleCallbacks()
 */
class StudyMetrics
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="studyMetrics")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="simple_array", name="track_hours", nullable=true)
     */
    protected $trackHours = [];

    /**
     * @ORM\Column(type="boolean", name="doing_well", nullable=true)
     */
    protected $doingWell;

    /**
     * @ORM\Column(type="text", name="all_together", nullable=true)
     */
    protected $allTogether;

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
     * @return StudyMetrics
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
     * @return StudyMetrics
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
     * Set trackHours
     *
     * @param array $trackHours
     * @return StudyMetrics
     */
    public function setTrackHours($trackHours)
    {
        $this->trackHours = $trackHours;

        return $this;
    }

    /**
     * Get trackHours
     *
     * @return array 
     */
    public function getTrackHours()
    {
        return $this->trackHours;
    }

    /**
     * Set doingWell
     *
     * @param boolean $doingWell
     * @return StudyMetrics
     */
    public function setDoingWell($doingWell)
    {
        $this->doingWell = $doingWell;

        return $this;
    }

    /**
     * Get doingWell
     *
     * @return boolean 
     */
    public function getDoingWell()
    {
        return $this->doingWell;
    }

    /**
     * Set allTogether
     *
     * @param boolean $allTogether
     * @return StudyMetrics
     */
    public function setAllTogether($allTogether)
    {
        $this->allTogether = $allTogether;

        return $this;
    }

    /**
     * Get allTogether
     *
     * @return boolean 
     */
    public function getAllTogether()
    {
        return $this->allTogether;
    }
}
