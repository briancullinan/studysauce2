<?php

namespace Course1\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz6")
 * @ORM\HasLifecycleCallbacks()
 */
class Quiz6
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course1", inversedBy="quiz6s")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\Column(type="simple_array", name="help", nullable=true)
     */
    protected $help = [];

    /**
     * @ORM\Column(type="string", length=64, name="attribute", nullable=true)
     */
    protected $attribute;

    /**
     * @ORM\Column(type="string", length=4096, name="often", nullable=true)
     */
    protected $often;

    /**
     * @ORM\Column(type="simple_array", name="partner_usage", nullable=true)
     */
    protected $usage = [];

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
     * Set help
     *
     * @param array $help
     * @return Quiz6
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help
     *
     * @return array 
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set attribute
     *
     * @param string $attribute
     * @return Quiz6
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return string 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set often
     *
     * @param string $often
     * @return Quiz6
     */
    public function setOften($often)
    {
        $this->often = $often;

        return $this;
    }

    /**
     * Get often
     *
     * @return string 
     */
    public function getOften()
    {
        return $this->often;
    }

    /**
     * Set usage
     *
     * @param array $usage
     * @return Quiz6
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;

        return $this;
    }

    /**
     * Get usage
     *
     * @return array 
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Quiz6
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
     * @param \Course1\Bundle\Entity\Course1 $course
     * @return Quiz6
     */
    public function setCourse(\Course1\Bundle\Entity\Course1 $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Course1\Bundle\Entity\Course1 
     */
    public function getCourse()
    {
        return $this->course;
    }
}
