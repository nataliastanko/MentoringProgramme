<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Annotation\Doctrine\OrganizationAware;

/**
 * Person
 * Apply as a Mentee
 *
 * @ORM\Table(
 *     name="persons",
 *     indexes={
 *         @ORM\Index(
 *             name="person_name",
 *             columns={"name"}
 *         ),
 *         @ORM\Index(
 *             name="person_last_name",
 *             columns={"last_name"}
 *         ),
 *         @ORM\Index(
 *             name="person_email",
 *             columns={"email"}
 *         )
 *    }
 * )
 * @UniqueEntity(
 *     fields={"email", "edition"},
 *     message="email.duplicatedInEdition", groups={"add"}
 * )
 * @UniqueEntity(
 *     fields="video_url",
 *     message="user.url.taken", groups={"add"}
 * )
 * @ORM\Entity(repositoryClass="Repository\PersonRepository")
 * @OrganizationAware(organizationFieldName="organization_id")
 */
class Person
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message = "user.name.notBlank", groups={"add"})
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="numbers.notAllowed",
     *     groups={"add"}
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(message = "user.lastName.notBlank", groups={"add"})
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="numbers.notAllowed",
     *     groups={"add"}
     * )
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @var string
    /**
     * Email address.
     *
     * @var string
     * @Assert\NotBlank(
     *     message = "user.email.notBlank",
     *     groups={"add"}
     * )
     * @Assert\Email(
     *     message = "user.email.notMatch",
     *     groups={"add"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank(message = "user.age.notBlank", groups={"add"})
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}.",
     *     groups={"add"}
     * )
     * @Assert\GreaterThanOrEqual(value = 16, groups={"add"})
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message = "user.education.notBlank",
     *     groups={"add"}
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $education;

    /**
     * @var string
     * @Assert\Url(
     *    message = "url.not_match",
     *    protocols = {"http", "https"},
     *    checkDNS = true
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video_url;

    /*
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Answer",
     *     mappedBy="person",
     *     cascade={"all"}
     * )
     **/
    private $answers;

    /**
     * @Assert\NotBlank(
     *     message = "choose",
     *     groups={"settings", "changeMentor"}
     * )
     * @ORM\ManyToOne(targetEntity="Mentor", inversedBy="persons")
     * @ORM\JoinColumn(name="mentor_id", referencedColumnName="id")
     **/
    private $mentor;

    /**
     * Can be inserted, can not be updated.
     * @var string
     * @Assert\NotBlank(
     *     message = "person.originalMentorChoice.notBlank",
     *     groups={"changeMentor"}
     * )
     * @ORM\Column(name="original_mentor_choice", type="string", length=255, nullable=true)
     */
    private $originalMentorChoice;

    /**
     * If is accepted to the program by admin
     * @ORM\Column(name="is_accepted", type="boolean", options={"default" = false})
     */
    private $isAccepted;

    /**
     * If is chosen to the program by mentor
     * @ORM\Column(name="is_chosen", type="boolean", options={"default" = false})
     */
    private $isChosen;

    /**
     * Assert\NotBlank(message = "choose", groups={"settings"}).
     *
     * @ORM\ManyToOne(
     *     targetEntity="Edition",
     *     inversedBy="persons"
     * )
     * @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     **/
    private $edition;

    /**
     * Person can have exactly one invtitation
     * https://developer.happyr.com/choose-owning-side-in-onetoone-relation
     * Make the Person entity the owning entity.
     * It makes sense to eagerly fetch the Person when you fetch an Invitation.
     * The entity that owns the relation does not need the relation.
     * The inverse side is depended on the relation and will fetch it’s owner.
     * @ORM\OneToOne(targetEntity="Invitation", inversedBy="person")
     * @ORM\JoinColumn(name="invitation_id", referencedColumnName="id")
     */
    private $invitation;

    /**
     * Belongs to organization
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     */
    private $organization;

    /**
     * !!! performance ORM\OneToOne !!!!
     * https://stackoverflow.com/questions/12362901/doctrine2-one-to-one-relation-auto-loads-on-query
     *
     * ORM\OneToOne(
     *     targetEntity="User",
     *     mappedBy="person"
     * )
     */

    const POS1 = 'mentee.education.1';
    const POS2 = 'mentee.education.2';
    const POS3 = 'mentee.education.3';

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->isAccepted = false;
        $this->isChosen = false;
    }

    public static function getEducationChoices()
    {
        return [
            self::POS1 => self::POS1,
            self::POS2 => self::POS2,
            self::POS3 => self::POS3,
        ];
    }

    /**
     * Get full name.
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->getName().' '.$this->getLastName();
    }

    public function getAnswers()
    {
        return $this->answers;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Person
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
     * @return Person
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

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get age.
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set age.
     *
     * @param string $var
     *
     * @return Person
     */
    public function setAge($var)
    {
        $this->age = $var;

        return $this;
    }

    /**
     * Set education.
     *
     * @param string $var
     *
     * @return Person
     */
    public function setEducation($var)
    {
        $this->education = $var;

        return $this;
    }

    /**
     * Get education.
     *
     * @return string
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Person
     */
    public function setVideoUrl($url)
    {
        $this->video_url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     * Set education.
     *
     * @param Mentor $var
     *
     * @return Person
     */
    public function setMentor($var)
    {
        $this->mentor = $var;

        return $this;
    }

    /**
     * Get education.
     *
     * @return string
     */
    public function getMentor()
    {
        return $this->mentor;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
    }

    public function getEdition()
    {
        return $this->edition;
    }

    public function setIsAccepted($var)
    {
        $this->isAccepted = $var;
    }

    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    public function setIsChosen($var)
    {
        $this->isChosen = $var;
    }

    public function getIsChosen()
    {
        return $this->isChosen;
    }

    public function setInvitation($var)
    {
        $this->invitation = $var;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function setOriginalMentorChoice($var)
    {
        $this->originalMentorChoice = $var;
    }

    public function getOriginalMentorChoice()
    {
        return $this->originalMentorChoice;
    }

    public function setOrganization($var)
    {
        $this->organization = $var;

        return $this;
    }
}
