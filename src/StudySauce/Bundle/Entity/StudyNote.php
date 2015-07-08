<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="study_note", uniqueConstraints={@ORM\UniqueConstraint(name="remote_idx", columns={"remote_id","user_id"})})
 * @ORM\HasLifecycleCallbacks()
 */
class StudyNote
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, name="remote_id", nullable=true)
     */
    protected $remoteId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="text", name="title", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", name="content", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="blob", name="thumbnail", nullable=true)
     */
    protected $thumbnail;

    /**
     * @ORM\Column(type="array", name="properties")
     */
    protected $properties;

    /**
     * @ORM\Column(type="datetime", name="created", nullable=true)
     */
    protected $created = null;

    /**
     * @ORM\Column(type="datetime", name="updated", nullable=true)
     */
    protected $updated = null;

    /**
     * @ORM\Column(type="datetime", name="remote_updated", nullable=true)
     */
    protected $remoteUpdated = null;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }



    /**
     * Set id
     *
     * @param string $id
     * @return StudyNote
     */
    public function setRemoteId($id)
    {
        $this->remoteId = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return StudyNote
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
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
     * Set properties
     *
     * @param array $properties
     * @return StudyNote
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Get properties
     *
     * @return array 
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return StudyNote
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return StudyNote
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return StudyNote
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
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return StudyNote
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return resource
     */
    public function getThumbnail()
    {
        if(is_resource($this->thumbnail))
            return stream_get_contents($this->thumbnail);
        return $this->thumbnail;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return StudyNote
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set remoteUpdated
     *
     * @param \DateTime $remoteUpdated
     * @return StudyNote
     */
    public function setRemoteUpdated($remoteUpdated)
    {
        $this->remoteUpdated = $remoteUpdated;

        return $this;
    }

    /**
     * Get remoteUpdated
     *
     * @return \DateTime 
     */
    public function getRemoteUpdated()
    {
        return $this->remoteUpdated;
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
}
