<?php

namespace Course3\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="group_study")
 * @ORM\HasLifecycleCallbacks()
 */
class GroupStudy
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course3", inversedBy="groupStudy")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="simple_array", length=256, name="bad_times", nullable=true)
     */
    protected $badTimes = [];

    /**
     * @ORM\Column(type="text", length=256, name="building", nullable=true)
     */
    protected $building;

    /**
     * @ORM\Column(type="text", name="group_role", nullable=true)
     */
    protected $groupRole;

    /**
     * @ORM\Column(type="text", length=256, name="group_breaks", nullable=true)
     */
    protected $groupBreaks;

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
     * Set badTimes
     *
     * @param array $badTimes
     * @return GroupStudy
     */
    public function setBadTimes($badTimes)
    {
        $this->badTimes = $badTimes;

        return $this;
    }

    /**
     * Get badTimes
     *
     * @return array 
     */
    public function getBadTimes()
    {
        return $this->badTimes;
    }

    /**
     * Set building
     *
     * @param string $building
     * @return GroupStudy
     */
    public function setBuilding($building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return string 
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set groupRole
     *
     * @param string $groupRole
     * @return GroupStudy
     */
    public function setGroupRole($groupRole)
    {
        $this->groupRole = $groupRole;

        return $this;
    }

    /**
     * Get groupRole
     *
     * @return string 
     */
    public function getGroupRole()
    {
        return $this->groupRole;
    }

    /**
     * Set groupBreaks
     *
     * @param string $groupBreaks
     * @return GroupStudy
     */
    public function setGroupBreaks($groupBreaks)
    {
        $this->groupBreaks = $groupBreaks;

        return $this;
    }

    /**
     * Get groupBreaks
     *
     * @return string 
     */
    public function getGroupBreaks()
    {
        return $this->groupBreaks;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return GroupStudy
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
     * @param \Course3\Bundle\Entity\Course3 $course
     * @return GroupStudy
     */
    public function setCourse(\Course3\Bundle\Entity\Course3 $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Course3\Bundle\Entity\Course3
     */
    public function getCourse()
    {
        return $this->course;
    }
}
