<?php

namespace Wit\Program\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EventNote
 *
 * @ORM\Table(name="event_notes")
 * @ORM\Entity
 */
class EventNote
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
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535)
     */
    private $comment;

    /**
     * Events have author
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(
     *     targetEntity="Wit\Program\Account\UserBundle\Entity\User",
     *     inversedBy="eventsNotes"
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
     * Events have notes
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(
     *     targetEntity="Event",
     *     inversedBy="notes"
     * )
     * @ORM\JoinColumn(
     *     name="event_id",
     *     referencedColumnName="id"
     * )
     **/
    private $event;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return EventNote
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
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
     * @return Wit\Program\Account\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set event.
     *
     * @param string $event
     *
     * @return EventNote
     */
    public function setEvent($var)
    {
        $this->event = $var;

        return $this;
    }

    /**
     * Get event.
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}

