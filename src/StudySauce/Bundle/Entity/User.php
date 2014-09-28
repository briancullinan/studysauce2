<?php
// src/Acme/UserBundle/Entity/User.php

namespace StudySauce\Bundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ss_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="user")
     */
    protected $schedules;

    /**
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $sessions;

    /**
     * @ORM\Column(type="datetime", name="created", options={"default" = 0})
     */
    protected $created;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
     * Add schedules
     *
     * @param \StudySauce\Bundle\Entity\Schedule $schedules
     * @return User
     */
    public function addSchedule(\StudySauce\Bundle\Entity\Schedule $schedules)
    {
        $this->schedules[] = $schedules;

        return $this;
    }

    /**
     * Remove schedules
     *
     * @param \StudySauce\Bundle\Entity\Schedule $schedules
     */
    public function removeSchedule(\StudySauce\Bundle\Entity\Schedule $schedules)
    {
        $this->schedules->removeElement($schedules);
    }

    /**
     * Get schedules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Add sessions
     *
     * @param \StudySauce\Bundle\Entity\Session $sessions
     * @return User
     */
    public function addSession(\StudySauce\Bundle\Entity\Session $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \StudySauce\Bundle\Entity\Session $sessions
     */
    public function removeSession(\StudySauce\Bundle\Entity\Session $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
}
