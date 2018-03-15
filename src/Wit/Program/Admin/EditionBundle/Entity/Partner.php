<?php

namespace Wit\Program\Admin\EditionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Partner.
 *
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields="name",
 *     message="sponsor.name.taken",
 *     groups={"settings"}
 * )
 * @UniqueEntity(
 *     fields="email",
 *     message="email.taken", groups={"settings"}
 * )
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="partners")
 * @ORM\Entity(repositoryClass="Wit\Program\Admin\EditionBundle\Repository\MultifunctionalRepository")
 */
class Partner
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
     * Name.
     *
     * @var                     string
     * @Gedmo\Versioned
     * @Assert\NotBlank(
     *     message = "name.notBlank",
     *     groups={"settings"}
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Email address.
     *
     * @var                       string
     * Assert\NotBlank(
     *     message = "email.notBlank",
     *     groups={"settings"}
     * )
     * @Gedmo\Versioned
     * @Assert\Email(
     *     message = "email.notMatch",
     *     groups={"settings"}
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
     *    checkDNS = true
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

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
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     *     groups={"settings"}
     * )
     * @Vich\UploadableField(mapping="partner_photo", fileNameProperty="photoName")
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
     * @ORM\ManyToMany(targetEntity="Wit\Program\Admin\EditionBundle\Entity\Edition", inversedBy="partners")
     * @ORM\JoinTable(name="partners_editions")
     **/
    private $editions;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->editions = new ArrayCollection();
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
     * @return Partner
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
     * Set email.
     *
     * @param string $email
     *
     * @return Partner
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
     * @return Partner
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

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setEditions($editions)
    {
        $this->editions = $editions;
    }

    public function getEditions()
    {
        return $this->editions;
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

    public function __call($method, $arguments)
    {
        return \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor()
            ->getValue($this->translate(), $method);
    }
}
