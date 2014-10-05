<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="checkin")
 */
class Checkin
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="checkins")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="datetime", name="checkin", options={"default" = 0})
     */
    protected $checkin;

    /**
     * @ORM\Column(type="datetime", name="utc_checkin", options={"default" = 0})
     */
    protected $utc_checkin;


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
     * Set checkin
     *
     * @param \DateTime $checkin
     * @return Checkin
     */
    public function setCheckin($checkin)
    {
        $this->checkin = $checkin;

        return $this;
    }

    /**
     * Get checkin
     *
     * @return \DateTime 
     */
    public function getCheckin()
    {
        return $this->checkin;
    }

    /**
     * Set utc_checkin
     *
     * @param \DateTime $utcCheckin
     * @return Checkin
     */
    public function setUtcCheckin($utcCheckin)
    {
        $this->utc_checkin = $utcCheckin;

        return $this;
    }

    /**
     * Get utc_checkin
     *
     * @return \DateTime 
     */
    public function getUtcCheckin()
    {
        return $this->utc_checkin;
    }

    /**
     * Set course
     *
     * @param \StudySauce\Bundle\Entity\Course $course
     * @return Checkin
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
