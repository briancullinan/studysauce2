<?php

namespace StudySauce\Bundle\Entity;

use Course1\Bundle\Entity\Quiz1;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Quiz1", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quiz1s;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="string", length=256, name="first")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=256, name="last")
     */
    protected $lastName;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable = true)
     */
    protected $photo;


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
        parent::__construct();
        $this->schedules = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->goals = new ArrayCollection();
        $this->deadlines = new ArrayCollection();
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
     * Add schedules
     *
     * @param Schedule $schedules
     * @return User
     */
    public function addSchedule(Schedule $schedules)
    {
        $this->schedules[] = $schedules;

        return $this;
    }

    /**
     * Remove schedules
     *
     * @param Schedule $schedules
     */
    public function removeSchedule(Schedule $schedules)
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
     * @param Session $sessions
     * @return User
     */
    public function addSession(Session $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param Session $sessions
     */
    public function removeSession(Session $sessions)
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
     * Add goals
     *
     * @param Goal $goals
     * @return User
     */
    public function addGoal(Goal $goals)
    {
        $this->goals[] = $goals;

        return $this;
    }

    /**
     * Remove goals
     *
     * @param Goal $goals
     */
    public function removeGoal(Goal $goals)
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
     * @param Deadline $deadlines
     * @return User
     */
    public function addDeadline(Deadline $deadlines)
    {
        $this->deadlines[] = $deadlines;

        return $this;
    }

    /**
     * Remove deadlines
     *
     * @param Deadline $deadlines
     */
    public function removeDeadline(Deadline $deadlines)
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
     * Add partners
     *
     * @param Partner $partners
     * @return User
     */
    public function addPartner(Partner $partners)
    {
        $this->partners[] = $partners;

        return $this;
    }

    /**
     * Remove partners
     *
     * @param Partner $partners
     */
    public function removePartner(Partner $partners)
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
     * @param File $files
     * @return User
     */
    public function addFile(File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param File $files
     */
    public function removeFile(File $files)
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
     * Set photo
     *
     * @param File $photo
     * @return User
     */
    public function setPhoto(File $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return File
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Add emails
     *
     * @param Mail $emails
     * @return User
     */
    public function addEmail(Mail $emails)
    {
        $this->emails[] = $emails;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param Mail $emails
     */
    public function removeEmail(Mail $emails)
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
     * Add visits
     *
     * @param Visit $visits
     * @return User
     */
    public function addVisit(Visit $visits)
    {
        $this->visits[] = $visits;

        return $this;
    }

    /**
     * Remove visits
     *
     * @param Visit $visits
     */
    public function removeVisit(Visit $visits)
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
     * Add quiz1s
     *
     * @param Quiz1 $quiz1s
     * @return User
     */
    public function addQuiz1(Quiz1 $quiz1s)
    {
        $this->quiz1s[] = $quiz1s;

        return $this;
    }

    /**
     * Remove quiz1s
     *
     * @param Quiz1 $quiz1s
     */
    public function removeQuiz1(Quiz1 $quiz1s)
    {
        $this->quiz1s->removeElement($quiz1s);
    }

    /**
     * Get quiz1s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuiz1s()
    {
        return $this->quiz1s;
    }
}
