<?php

namespace StudySauce\Bundle\Entity;

use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="ss_user",uniqueConstraints={
 *     @ORM\UniqueConstraint(name="email_idx", columns={"email"}),
 *     @ORM\UniqueConstraint(name="username_idx", columns={"username"})})
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser implements EncoderAwareInterface
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
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $payments;

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
     * @ORM\OneToMany(targetEntity="PartnerInvite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $partnerInvites;

    /**
     * @ORM\OneToMany(targetEntity="ParentInvite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $parentInvites;

    /**
     * @ORM\OneToMany(targetEntity="StudentInvite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $studentInvites;

    /**
     * @ORM\OneToMany(targetEntity="GroupInvite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $groupInvites;

    /**
     * @ORM\OneToMany(targetEntity="PartnerInvite", mappedBy="partner")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedPartners;

    /**
     * @ORM\OneToMany(targetEntity="ParentInvite", mappedBy="parent")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedParents;

    /**
     * @ORM\OneToMany(targetEntity="StudentInvite", mappedBy="student")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedStudents;

    /**
     * @ORM\OneToMany(targetEntity="GroupInvite", mappedBy="student")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedGroups;

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
     * @ORM\OneToMany(targetEntity="Course2\Bundle\Entity\Course2", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $course2s;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="string", length=256, name="first")
     */
    protected $first = '';

    /**
     * @ORM\Column(type="string", length=256, name="last")
     */
    protected $last = '';

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
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="ss_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;

    /** @ORM\Column(name="properties", type="array", nullable=true) */
    protected $properties;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * @return null|string
     */
    public function getEncoderName() {

        if($this->getSalt()[0] == '$' && $this->getSalt()[2] == '$') {
            return 'drupal_encoder';
        }

        return NULL;
    }

    /**
     * @param $prop
     * @param $value
     */
    public function setProperty($prop, $value)
    {
        $this->properties[$prop] = $value;
    }

    /**
     * @param $prop
     * @return null
     */
    public function getProperty($prop)
    {
        if(isset($this->properties[$prop]))
            return $this->properties[$prop];
        return null;
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
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->course1s = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->partnerInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->parentInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->studentInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedParents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedPartners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedStudents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedGroups = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();
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
     * Set first
     *
     * @param string $first
     * @return User
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
     * @return User
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
     * Set properties
     *
     * @param string $properties
     * @return User
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Get properties
     *
     * @return string 
     */
    public function getProperties()
    {
        return $this->properties;
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
     * Add payments
     *
     * @param \StudySauce\Bundle\Entity\Payment $payments
     * @return User
     */
    public function addPayment(\StudySauce\Bundle\Entity\Payment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \StudySauce\Bundle\Entity\Payment $payments
     */
    public function removePayment(\StudySauce\Bundle\Entity\Payment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
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
     * Add partnerInvites
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $partnerInvites
     * @return User
     */
    public function addPartnerInvite(\StudySauce\Bundle\Entity\PartnerInvite $partnerInvites)
    {
        $this->partnerInvites[] = $partnerInvites;

        return $this;
    }

    /**
     * Remove partnerInvites
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $partnerInvites
     */
    public function removePartnerInvite(\StudySauce\Bundle\Entity\PartnerInvite $partnerInvites)
    {
        $this->partnerInvites->removeElement($partnerInvites);
    }

    /**
     * Get partnerInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartnerInvites()
    {
        return $this->partnerInvites;
    }

    /**
     * Add parentInvites
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $parentInvites
     * @return User
     */
    public function addParentInvite(\StudySauce\Bundle\Entity\ParentInvite $parentInvites)
    {
        $this->parentInvites[] = $parentInvites;

        return $this;
    }

    /**
     * Remove parentInvites
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $parentInvites
     */
    public function removeParentInvite(\StudySauce\Bundle\Entity\ParentInvite $parentInvites)
    {
        $this->parentInvites->removeElement($parentInvites);
    }

    /**
     * Get parentInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParentInvites()
    {
        return $this->parentInvites;
    }

    /**
     * Add studentInvites
     *
     * @param StudentInvite $studentInvites
     * @return User
     */
    public function addStudentInvite(StudentInvite $studentInvites)
    {
        $this->studentInvites[] = $studentInvites;

        return $this;
    }

    /**
     * Remove studentInvites
     *
     * @param StudentInvite $studentInvites
     */
    public function removeStudentInvite(StudentInvite $studentInvites)
    {
        $this->studentInvites->removeElement($studentInvites);
    }

    /**
     * Get studentInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStudentInvites()
    {
        return $this->studentInvites;
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

    /**
     * Add groupInvites
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $groupInvites
     * @return User
     */
    public function addGroupInvite(\StudySauce\Bundle\Entity\GroupInvite $groupInvites)
    {
        $this->groupInvites[] = $groupInvites;

        return $this;
    }

    /**
     * Remove groupInvites
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $groupInvites
     */
    public function removeGroupInvite(\StudySauce\Bundle\Entity\GroupInvite $groupInvites)
    {
        $this->groupInvites->removeElement($groupInvites);
    }

    /**
     * Get groupInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupInvites()
    {
        return $this->groupInvites;
    }

    /**
     * Add invitedPartners
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $invitedPartners
     * @return User
     */
    public function addInvitedPartner(\StudySauce\Bundle\Entity\PartnerInvite $invitedPartners)
    {
        $this->invitedPartners[] = $invitedPartners;

        return $this;
    }

    /**
     * Remove invitedPartners
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $invitedPartners
     */
    public function removeInvitedPartner(\StudySauce\Bundle\Entity\PartnerInvite $invitedPartners)
    {
        $this->invitedPartners->removeElement($invitedPartners);
    }

    /**
     * Get invitedPartners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedPartners()
    {
        return $this->invitedPartners;
    }

    /**
     * Add invitedParents
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $invitedParents
     * @return User
     */
    public function addInvitedParent(\StudySauce\Bundle\Entity\ParentInvite $invitedParents)
    {
        $this->invitedParents[] = $invitedParents;

        return $this;
    }

    /**
     * Remove invitedParents
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $invitedParents
     */
    public function removeInvitedParent(\StudySauce\Bundle\Entity\ParentInvite $invitedParents)
    {
        $this->invitedParents->removeElement($invitedParents);
    }

    /**
     * Get invitedParents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedParents()
    {
        return $this->invitedParents;
    }

    /**
     * Add invitedStudents
     *
     * @param \StudySauce\Bundle\Entity\StudentInvite $invitedStudents
     * @return User
     */
    public function addInvitedStudent(\StudySauce\Bundle\Entity\StudentInvite $invitedStudents)
    {
        $this->invitedStudents[] = $invitedStudents;

        return $this;
    }

    /**
     * Remove invitedStudents
     *
     * @param \StudySauce\Bundle\Entity\StudentInvite $invitedStudents
     */
    public function removeInvitedStudent(\StudySauce\Bundle\Entity\StudentInvite $invitedStudents)
    {
        $this->invitedStudents->removeElement($invitedStudents);
    }

    /**
     * Get invitedStudents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedStudents()
    {
        return $this->invitedStudents;
    }

    /**
     * Add invitedGroups
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $invitedGroups
     * @return User
     */
    public function addInvitedGroup(\StudySauce\Bundle\Entity\GroupInvite $invitedGroups)
    {
        $this->invitedGroups[] = $invitedGroups;

        return $this;
    }

    /**
     * Remove invitedGroups
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $invitedGroups
     */
    public function removeInvitedGroup(\StudySauce\Bundle\Entity\GroupInvite $invitedGroups)
    {
        $this->invitedGroups->removeElement($invitedGroups);
    }

    /**
     * Get invitedGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedGroups()
    {
        return $this->invitedGroups;
    }

    /**
     * Add course2s
     *
     * @param \Course2\Bundle\Entity\Course2 $course2s
     * @return User
     */
    public function addCourse2(\Course2\Bundle\Entity\Course2 $course2s)
    {
        $this->course2s[] = $course2s;

        return $this;
    }

    /**
     * Remove course2s
     *
     * @param \Course2\Bundle\Entity\Course2 $course2s
     */
    public function removeCourse2(\Course2\Bundle\Entity\Course2 $course2s)
    {
        $this->course2s->removeElement($course2s);
    }

    /**
     * Get course2s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourse2s()
    {
        return $this->course2s;
    }
}
