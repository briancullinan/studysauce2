<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="student_invite")
 * @ORM\HasLifecycleCallbacks()
 */
class StudentInvite implements Invite
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="studentInvites")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitedStudents")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true)
     */
    protected $student;

    /**
     * @ORM\Column(type="string", length=256, name="first")
     */
    protected $first;

    /**
     * @ORM\Column(type="string", length=256, name="last")
     */
    protected $last;

    /**
     * @ORM\Column(type="string", length=256, name="email")
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=256, name="from_first", nullable=true)
     */
    protected $fromFirst;

    /**
     * @ORM\Column(type="string", length=256, name="from_last", nullable=true)
     */
    protected $fromLast;

    /**
     * @ORM\Column(type="string", length=256, name="from_email", nullable=true)
     */
    protected $fromEmail;

    /**
     * @ORM\Column(type="boolean", name="activated")
     */
    protected $activated = false;

    /**
     * @ORM\Column(type="string", length=64, name="code")
     */
    protected $code;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="reminder", nullable = true)
     */
    protected $reminder;

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
     * Set first
     *
     * @param string $first
     * @return ParentInvite
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get first
     *
     * @return string 
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set last
     *
     * @param string $last
     * @return ParentInvite
     */
    public function setLast($last)
    {
        $this->last = $last;

        return $this;
    }

    /**
     * Get last
     *
     * @return string 
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ParentInvite
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     * @return ParentInvite
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean 
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return ParentInvite
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ParentInvite
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
     * Set reminder
     *
     * @param \DateTime $reminder
     * @return ParentInvite
     */
    public function setReminder($reminder)
    {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * Get reminder
     *
     * @return \DateTime 
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return ParentInvite
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
     * Set student
     *
     * @param \StudySauce\Bundle\Entity\User $student
     * @return StudentInvite
     */
    public function setStudent(\StudySauce\Bundle\Entity\User $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \StudySauce\Bundle\Entity\User 
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set fromFirst
     *
     * @param string $fromFirst
     * @return StudentInvite
     */
    public function setFromFirst($fromFirst)
    {
        $this->fromFirst = $fromFirst;

        return $this;
    }

    /**
     * Get fromFirst
     *
     * @return string 
     */
    public function getFromFirst()
    {
        return $this->fromFirst;
    }

    /**
     * Set fromLast
     *
     * @param string $fromLast
     * @return StudentInvite
     */
    public function setFromLast($fromLast)
    {
        $this->fromLast = $fromLast;

        return $this;
    }

    /**
     * Get fromLast
     *
     * @return string 
     */
    public function getFromLast()
    {
        return $this->fromLast;
    }

    /**
     * Set fromEmail
     *
     * @param string $fromEmail
     * @return StudentInvite
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * Get fromEmail
     *
     * @return string 
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }
}
