<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Event.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Table(name="events")
 * @ORM\Entity(repositoryClass="Repository\EventRepository")
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /*
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @var \DateTime
     *
     * Warning: do not use datetimetz (changed back to datetime)
     * https://github.com/doctrine/doctrine2/issues/3810
     * MySQL does not have a native type for DateTimeTz,
     * therefore the mapping always falls back to the native DATETIME type.
     * See the DBAL documentation:
     * http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#mapping-matrix
     *
     * @ORM\Column(name="startTime", type="datetime")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="datetime")
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * Events have author
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(
     *     targetEntity="User",
     *     inversedBy="eventsCreated"
     * )
     * @ORM\JoinColumn(
     *     name="author_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     **/
    private $author;

    /**
     * Event can have participants
     *
     * Owning Side
     *
     * EXTRA_LAZY added for counting participants query - fetch="EXTRA_LAZY",
     *
     * ORM\ManyToMany(
     *     targetEntity="User",
     *     inversedBy="eventsParticipatedIn",
     *     cascade={"persist", "remove"}
     * )
     * ORM\JoinTable(
     *     name="events_participants",
     *     joinColumns={
     *         ORM\JoinColumn(name="event_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *     }
     * )
     * ORM\OrderBy({"name" = "ASC"})
     *
     * @ORM\ManyToOne(
     *     targetEntity="User",
     *     inversedBy="eventsParticipatedIn"
     * )
     * @ORM\JoinColumn(name="participant_id", referencedColumnName="id")
     */
    private $participant;

    /**
     * @ORM\OneToMany(targetEntity="EventNote", mappedBy="event")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $notes;

    public function __construct()
    {
        // $this->participants = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set startTime.
     *
     * @param \DateTime $startTime
     *
     * @return Event
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime.
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime.
     *
     * @param \DateTime $endTime
     *
     * @return Event
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime.
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author.
     *
     * @param string $author creator
     *
     * @return Event
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Load participants to edit form, event details
     *
     * return ArrayCollection|User[] array of users
     * @return User
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param Event $var
     */
    public function setParticipant($participant)
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * Load event notes
     *
     * @return ArrayCollection|EventNote[] array of notes
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param Event $var
     */
    public function setNotes($var)
    {
        $this->notes = $var;

        return $this;
    }

}
