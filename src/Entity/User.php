<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use FOS\UserBundle\Model\User as FosUser;
// use Repository\UserRepository;
use Repository\Annotation\OrganizationAware;

/**
 * User.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Table(
 *     name="users",
 *     indexes={
 *         @ORM\Index(
 *             name="user_name",
 *             columns={"name"}
 *         ),
 *         @ORM\Index(
 *             name="user_last_name",
 *             columns={"last_name"}
 *         ),
 *         @ORM\Index(
 *             name="user_email",
 *             columns={"email"}
 *         )
 *    }
 * )
 * @UniqueEntity(
 *     fields="email",
 *     message="email.taken",
 *     groups={"settings", "register"}
 * )
 * @ORM\Entity(repositoryClass="Repository\UserRepository")
 * @OrganizationAware(organizationFieldName="organization_id")
 *
 */
class User extends FosUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

     /*
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message = "user.name.notBlank",
     *     groups={"settings"}
     * )
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message = "user.lastName.notBlank",
     *     groups={"settings"}
     * )
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(name="invitation_id", referencedColumnName="id")
     * @Assert\NotNull(
     *     message="user.invitation.empty",
     *     groups={"Registration"}
     * )
     */
    protected $invitation;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="author", cascade={"persist"})
     **/
    protected $eventsCreated;

    /**
     * @ORM\OneToMany(targetEntity="EventNote", mappedBy="author", cascade={"persist"})
     **/
    protected $eventsNotes;

    /**
     * User can participate in events
     *
     * Inversed Side
     *
     * ORM\ManyToMany(
     *     targetEntity="Event",
     *     mappedBy="participants",
     *     cascade={"persist"}
     * )
     * RM\JoinTable(
     *     name="events_participants",
     *     joinColumns={
     *         ORM\JoinColumn(
     *             name="user_id",
     *             referencedColumnName="id"
     *         )
     *     },
     *     inverseJoinColumns={
     *         ORM\JoinColumn(
     *             name="event_id",
     *             referencedColumnName="id"
     *         )
     *     }
     * )
     * ORM\OrderBy({"startTime" = "ASC"})
     *
     *
     * @ORM\OneToMany(
     *     targetEntity="Event",
     *     mappedBy="participant"
     * )
     */
    protected $eventsParticipatedIn;

    /**
     * @ORM\Column(name="locale", type="string", length=5, nullable=false, options={"default"="en"})
     */
    protected $locale;

    /**
     * Belongs to organization
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     */
    protected $organization;

    public function __construct()
    {
        parent::__construct();
        $this->roles = ['ROLE_USER'];
        $this->locale = 'en';
    }

    // public function setEmail($email)
    // {
    //     $email = is_null($email) ? '' : $email;
    //     parent::setEmail($email);
    //     $this->setUsername($email);
    // }

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
     * Get full name.
     * Return email address in case of empty fullname.
     *
     * @return string
     */
    public function getFullname()
    {
        $fullname = trim($this->getName().' '.$this->getLastName());
        return (!$fullname) ? $this->getEmail() : $fullname;
    }

    /**
     * Return roles except ROLE_USER
     * @return array
     */
    public function getUserTypes()
    {
        $roles = array_merge(array(), $this->roles);

        $roles = $this->array_remove($roles, 'ROLE_USER');

        return $roles;
    }

    /**
     * Remove each instance of a value within an array
     * @param array $array
     * @param mixed $value
     * @return array
     */
    public function array_remove(&$array, $value)
    {
        return array_filter($array, function($a) use($value) {
            return $a !== $value;
        });
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set last name.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setInvitation(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function getEventsParticipatedIn()
    {
        return $this->eventsParticipatedIn;
    }

    public function getEventsCreated()
    {
        return $this->eventsCreated;
    }

    public function getEventsNotes()
    {
        return $this->eventsNotes;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function setOrganization($var)
    {
        $this->organization = $var;

        return $this;
    }

    public function getRoleName()
    {
        /**
         * @TODO translation!
         */

        if (in_array('ROLE_ADMIN', $this->roles)) {
            return 'Admin';
        }

        if (in_array('ROLE_MENTOR', $this->roles)) {
            return 'Mentor';
        }

        if (in_array('ROLE_MENTEE', $this->roles)) {
            return 'Mentee';

        }

        return null;
    }

    /**
     * Will not work until Doctrine 2.6 release
     * Get events created or participated in
     *
     * @return ArrayCollection
     */
    // public function getEvents(\DateTime $startDate = null, \DateTime $endDate = null) {
    //   if ($startDate && $endDate) {

    //     return $this->eventsParticipatedIn->matching(
    //             UserRepository::createEventsCriteria($startDate, $endDate)
    //         )
    //     ;
    //   }

    //   return $this->eventsParticipatedIn;
    // }

}
