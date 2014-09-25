<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="session")
 */
class Schedule
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", mappedBy="schedules")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $uid;

    /**
     * @ORM\OneToMany(targetEntity="Preference", mappedBy="schedule")
     * @ORM\JoinColumn(name="preferences", referencedColumnName="id")
     */
    protected $preferences;

    /**
     * @ORM\OneToMany(targetEntity="Class", mappedBy="schedule")
     * @ORM\JoinColumn(name="classes", referencedColumnName="id")
     */
    protected $classes;

    /**
     * @ORM\Column(type="string", name="university")
     */
    protected $university;

}
