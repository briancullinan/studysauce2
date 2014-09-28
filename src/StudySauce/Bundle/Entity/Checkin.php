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
     * Constructor
     */
    public function __construct()
    {
        $this->course = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add course
     *
     * @param \StudySauce\Bundle\Entity\Course $course
     * @return Checkin
     */
    public function addCourse(\StudySauce\Bundle\Entity\Course $course)
    {
        $this->course[] = $course;

        return $this;
    }

    /**
     * Remove course
     *
     * @param \StudySauce\Bundle\Entity\Course $course
     */
    public function removeCourse(\StudySauce\Bundle\Entity\Course $course)
    {
        $this->course->removeElement($course);
    }

    /**
     * Get course
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourse()
    {
        return $this->course;
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
}
