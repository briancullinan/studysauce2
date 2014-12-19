<?php

namespace Course2\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="strategies")
 * @ORM\HasLifecycleCallbacks()
 */
class Strategies
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course2", inversedBy="strategies")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="simple_array", length=256, name="self_testing")
     */
    protected $selfTesting = [];

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
     * Set selfTesting
     *
     * @param array $selfTesting
     * @return Strategies
     */
    public function setSelfTesting($selfTesting)
    {
        $this->selfTesting = $selfTesting;

        return $this;
    }

    /**
     * Get selfTesting
     *
     * @return array 
     */
    public function getSelfTesting()
    {
        return $this->selfTesting;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Strategies
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
     * @return Strategies
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
}
