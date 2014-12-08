<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="partner_invite")
 * @ORM\HasLifecycleCallbacks()
 */
class PartnerInvite implements Invite
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="partnerInvites")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitedPartners")
     * @ORM\JoinColumn(name="partner_id", referencedColumnName="id", nullable=true)
     */
    protected $partner;

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
     * @ORM\Column(type="simple_array", length=256, name="permissions")
     */
    protected $permissions;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable = true)
     */
    protected $photo;

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
     * @return PartnerInvite
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
     * @return PartnerInvite
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
     * @return PartnerInvite
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
     * Set permissions
     *
     * @param array $permissions
     * @return PartnerInvite
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return array 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     * @return PartnerInvite
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
     * @return PartnerInvite
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
     * @return PartnerInvite
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
     * @return PartnerInvite
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
     * @return PartnerInvite
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
     * Set partner
     *
     * @param \StudySauce\Bundle\Entity\User $partner
     * @return PartnerInvite
     */
    public function setPartner(\StudySauce\Bundle\Entity\User $partner = null)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Get partner
     *
     * @return \StudySauce\Bundle\Entity\User 
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * Set photo
     *
     * @param \StudySauce\Bundle\Entity\File $photo
     * @return PartnerInvite
     */
    public function setPhoto(\StudySauce\Bundle\Entity\File $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \StudySauce\Bundle\Entity\File 
     */
    public function getPhoto()
    {
        return $this->photo;
    }
}
