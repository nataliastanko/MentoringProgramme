<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Repository\Annotation\OrganizationAware;

/**
 * Mentor
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * Apply as a Mentor
 *
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields="email",
 *     message="email.taken",
 *     groups={"settings", "mentor_apply"}
 * )
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(
 *     name="mentors",
 *     indexes={
 *         @ORM\Index(
 *             name="mentor_name",
 *             columns={"name"}
 *         ),
 *         @ORM\Index(
 *             name="mentor_last_name",
 *             columns={"last_name"}
 *         ),
 *         @ORM\Index(
 *             name="mentor_email",
 *             columns={"email"}
 *         )
 *    }
 * )
 * @ORM\Entity(repositoryClass="Repository\MentorMultifunctionalRepository")
 * @OrganizationAware(organizationFieldName="organization_id")
 */
class Mentor
{
    use Translatable;

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
     * @Assert\NotBlank(
     *     message = "mentor.name.notBlank",
     *     groups={"settings", "mentor_apply"}
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="numbers.notAllowed",
     *     groups={"settings", "mentor_apply"}
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message = "mentor.notBlank",
     *     groups={"settings", "mentor_apply"}
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="numbers.notAllowed",
     *     groups={"settings", "mentor_apply"}
     * )
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * Email address.
     *
     * @var                       string
     * @Assert\NotBlank(
     *     message = "email.notBlank",
     *     groups={"settings", "mentor_apply"}
     * )
     * @Gedmo\Versioned
     * @Assert\Email(
     *     message = "email.notMatch",
     *     groups={"settings", "mentor_apply"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Assert\Url(
     *    message = "url.not_match",
     *    protocols = {"http", "https"},
     *    checkDNS = true,
     *    groups={"settings", "mentor_apply"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Assert\Url(
     *    message = "url.not_match",
     *    protocols = {"http", "https"},
     *    checkDNS = true,
     *    groups={"settings", "mentor_apply"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $personalUrl;

    /**
     * mentors company name
     *
     * @var string
     * @ORM\Column(name="company", type="string", length=255, nullable=true)
     */
    private $company;

    /*
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * This will store the UploadedFile object after the form is submitted.
     * This should not be persisted to the database, but you do need to annotate it.
     *
     * The UploadableField annotation has a few required options. They are as follows:
     *
     * mapping: the mapping name specified in the bundle configuration to use
     *
     * fileNameProperty: the property that will contained the name of the uploaded file.
     * This is the only property that is saved in the database.
     *
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"},
     *     groups={"settings", "mentor_apply"}
     * )
     * @Vich\UploadableField(mapping="mentor_photo", fileNameProperty="photoName")
     *
     * @var File
     */
    private $photoFile;

    /**
     * Field which will be stored to the database as a string.
     * This will hold the filename of the uploaded file.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $photoName;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="mentor")
     **/
    private $persons;

    /**
     * @ORM\ManyToMany(targetEntity="Edition", inversedBy="mentors")
     * @ORM\JoinTable(name="mentors_editions")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $editions;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Answer",
     *     mappedBy="mentor",
     *     cascade={"all"}
     * )
     **/
    private $answers;

    /**
     * Person can have exactly one invtitation
     * https://developer.happyr.com/choose-owning-side-in-onetoone-relation
     * Make the Person/Mentr entity the owning entity.
     * It makes sense to eagerly fetch the Person when you fetch an Invitation.
     * The entity that owns the relation does not need the relation.
     * The inverse side is depended on the relation and will fetch it’s owner.
     * @ORM\OneToOne(targetEntity="Invitation", inversedBy="mentor")
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

    public function __construct()
    {
        $this->editions = new ArrayCollection();
        $this->answers = new ArrayCollection();
    }

    /**
     * The entity form element doesn't use that __call function.
     * After adding a getBio() to entity
     * the entity form element behaves as expected
     */
    public function __call($method, $arguments)
    {
        return PropertyAccess::createPropertyAccessor()
            ->getValue($this->translate(), $method);
    }

    /**
     * https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/usage.md.
     *
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setPhotoFile(File $image = null)
    {
        $this->photoFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * @param string $photoName
     */
    public function setPhotoName($photoName)
    {
        $this->photoName = $photoName;
    }

    /**
     * @return string
     */
    public function getPhotoName()
    {
        return $this->photoName;
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
     * @return Mentor
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
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return Mentor
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
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
     * @return Mentor
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
     * Set url.
     *
     * @param string $url
     *
     * @return Mentor
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Mentor
     */
    public function setPersonalUrl($url)
    {
        $this->personalUrl = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getPersonalUrl()
    {
        return $this->personalUrl;
    }

    /**
     * Set company name.
     *
     * @param string $url
     *
     * @return Mentor
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company name.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
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

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getPersons()
    {
        return $this->persons;
    }

    public function setEditions($editions) {
        $this->editions = $editions;
    }

    public function getEditions() {
        return $this->editions;
    }

    public function getAnswers() {
        return $this->answers;
    }

    public function setInvitation($var)
    {
        $this->invitation = $var;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function setOrganization($var)
    {
        $this->organization = $var;

        return $this;
    }

    /**
     * 'en' is a default locale
     * Bio is a translatable field (in another table)
     * @Assert\Callback(groups={"mentor_apply", "settings"})
     */
    public function atLeastEnBio(ExecutionContextInterface $context)
    {
        // if (!($this->translate('en', false)->getBio() || $this->translate('pl', false)->getBio())) {
        if (!($this->translate('en', false)->getBio())) {
            $context->buildViolation('mentor.bio.atLeastOne')
                    ->atPath('bio')
                    ->addViolation();
        }
    }

    /**
     * 'en' is a default locale
     * Occupation is a translatable field (in another table)
     * @Assert\Callback(groups={"settings"})
     */
    public function atLeastEnOccupation(ExecutionContextInterface $context)
    {
        if ( !$this->translate('en', false)->getOccupation() ) {
            $context->buildViolation('mentor.occupation.atLeastEn')
                    ->atPath('occupation')
                    ->addViolation();
        }
    }

    /**
     * Do we have to take care of assigning an edition
     * @return boolean
     */
    public function hasEditionAssigned()
    {
        if (count($this->editions) > 0) {
            return true;
        }
        return false;
    }

    /**
     * When mentor applies,
     * she/he is not assigned to any edition,
     * waiting for admin approval
     *
     * @Assert\Callback(groups={"settings"})
     */
    public function atLeastOneEdition(ExecutionContextInterface $context)
    {
        if (count($this->editions) < 1) {
            $context->buildViolation('edition.atLeastOne')
                ->atPath('editions')
                ->addViolation();
        }
    }

    /**
     * When url are the exact same
     * @Assert\Callback(groups={"mentor_apply", "settings"})
     */
    public function urlsNotTheSame(ExecutionContextInterface $context)
    {
        if ($this->url && $this->personalUrl) {
            if ($this->url === $this->personalUrl) {
                $context->buildViolation('url.notTheSame')
                    ->atPath('personalUrl')
                    ->addViolation();
            }
        }
    }

    /**
     * Find person chosen to mentor
     * @return Person
     */
    public function findChosenPerson()
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria
            ->where(
                $expr->eq('isChosen',true)
            )
            ->setMaxResults(1)
        ;

        return $this->persons->matching($criteria)->first();
    }

    /**
     * Find person accepted to mentor
     * @return Person
     */
    public function findAcceptedMentees()
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria
            ->where(
                $expr->eq('isAccepted', true)
            )
        ;

        return $this->persons->matching($criteria);
    }

}
