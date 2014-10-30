<?php

namespace StudySauce\Bundle\Entity;

use Course1\Bundle\Entity\Quiz1;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ss_user")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\OrderBy({"time" = "DESC"})
     */
    protected $sessions;

    /**
     * @ORM\OneToMany(targetEntity="Visit", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $visits;

    /**
     * @ORM\OneToMany(targetEntity="Goal", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $goals;

    /**
     * @ORM\OneToMany(targetEntity="Deadline", mappedBy="user")
     * @ORM\OrderBy({"dueDate" = "ASC"})
     */
    protected $deadlines;

    /**
     * @ORM\OneToMany(targetEntity="Partner", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $partners;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $files;

    /**
     * @ORM\OneToMany(targetEntity="Mail", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $emails;

    /**
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Course1", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $course1s;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="string", length=256, name="first")
     */
    protected $firstName = '';

    /**
     * @ORM\Column(type="string", length=256, name="last")
     */
    protected $lastName = '';

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable = true)
     */
    protected $photo;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     */
    protected $groups;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->visits = new \Doctrine\Common\Collections\ArrayCollection();
        $this->goals = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deadlines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->partners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->course1s = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set google_id
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get google_id
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set google_access_token
     *
     * @param string $googleAccessToken
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;

        return $this;
    }

    /**
     * Get google_access_token
     *
     * @return string 
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
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
     * Add visits
     *
     * @param \StudySauce\Bundle\Entity\Visit $visits
     * @return User
     */
    public function addVisit(\StudySauce\Bundle\Entity\Visit $visits)
    {
        $this->visits[] = $visits;

        return $this;
    }

    /**
     * Remove visits
     *
     * @param \StudySauce\Bundle\Entity\Visit $visits
     */
    public function removeVisit(\StudySauce\Bundle\Entity\Visit $visits)
    {
        $this->visits->removeElement($visits);
    }

    /**
     * Get visits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Add goals
     *
     * @param \StudySauce\Bundle\Entity\Goal $goals
     * @return User
     */
    public function addGoal(\StudySauce\Bundle\Entity\Goal $goals)
    {
        $this->goals[] = $goals;

        return $this;
    }

    /**
     * Remove goals
     *
     * @param \StudySauce\Bundle\Entity\Goal $goals
     */
    public function removeGoal(\StudySauce\Bundle\Entity\Goal $goals)
    {
        $this->goals->removeElement($goals);
    }

    /**
     * Get goals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add deadlines
     *
     * @param \StudySauce\Bundle\Entity\Deadline $deadlines
     * @return User
     */
    public function addDeadline(\StudySauce\Bundle\Entity\Deadline $deadlines)
    {
        $this->deadlines[] = $deadlines;

        return $this;
    }

    /**
     * Remove deadlines
     *
     * @param \StudySauce\Bundle\Entity\Deadline $deadlines
     */
    public function removeDeadline(\StudySauce\Bundle\Entity\Deadline $deadlines)
    {
        $this->deadlines->removeElement($deadlines);
    }

    /**
     * Get deadlines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeadlines()
    {
        return $this->deadlines;
    }

    /**
     * Add partners
     *
     * @param \StudySauce\Bundle\Entity\Partner $partners
     * @return User
     */
    public function addPartner(\StudySauce\Bundle\Entity\Partner $partners)
    {
        $this->partners[] = $partners;

        return $this;
    }

    /**
     * Remove partners
     *
     * @param \StudySauce\Bundle\Entity\Partner $partners
     */
    public function removePartner(\StudySauce\Bundle\Entity\Partner $partners)
    {
        $this->partners->removeElement($partners);
    }

    /**
     * Get partners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartners()
    {
        return $this->partners;
    }

    /**
     * Add files
     *
     * @param \StudySauce\Bundle\Entity\File $files
     * @return User
     */
    public function addFile(\StudySauce\Bundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \StudySauce\Bundle\Entity\File $files
     */
    public function removeFile(\StudySauce\Bundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add emails
     *
     * @param \StudySauce\Bundle\Entity\Mail $emails
     * @return User
     */
    public function addEmail(\StudySauce\Bundle\Entity\Mail $emails)
    {
        $this->emails[] = $emails;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param \StudySauce\Bundle\Entity\Mail $emails
     */
    public function removeEmail(\StudySauce\Bundle\Entity\Mail $emails)
    {
        $this->emails->removeElement($emails);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Add course1s
     *
     * @param \Course1\Bundle\Entity\Course1 $course1s
     * @return User
     */
    public function addCourse1(\Course1\Bundle\Entity\Course1 $course1s)
    {
        $this->course1s[] = $course1s;

        return $this;
    }

    /**
     * Remove course1s
     *
     * @param \Course1\Bundle\Entity\Course1 $course1s
     */
    public function removeCourse1(\Course1\Bundle\Entity\Course1 $course1s)
    {
        $this->course1s->removeElement($course1s);
    }

    /**
     * Get course1s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourse1s()
    {
        return $this->course1s;
    }

    /**
     * Set photo
     *
     * @param \StudySauce\Bundle\Entity\File $photo
     * @return User
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

    /**
     * Add groups
     *
     * @param \StudySauce\Bundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \StudySauce\Bundle\Entity\Group $groups
     */
    public function removeGroup(GroupInterface $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
