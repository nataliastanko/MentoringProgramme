<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Organization
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 * @Vich\Uploadable
 * @ORM\Table(name="organizations")
 * @ORM\Entity(repositoryClass="Repository\OrganizationRepository")
 */
class Organization
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
     *
     * @ORM\Column(name="subdomain", type="string", length=15, unique=true)
     */
    private $subdomain;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array one-dimensional array (simple array)
     * @see http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#simple-array
     *
     * @ORM\Column(name="locales", type="simple_array", length=65535)
     */
    private $locales;

    /**
     * @var string
     *
     * @ORM\Column(name="default_locale", type="string", length=5)
     */
    private $defaultLocale;

    /**
     * @var array one-dimensional array (simple array)
     * @see http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#simple-array
     *
     * @ORM\Column(name="required_locales", type="simple_array", length=65535)
     */
    private $requiredLocales;

    /**
     * If is accepted to the program by superadmin
     * @ORM\Column(name="is_accepted", type="boolean", options={"default" = false})
     */
    private $isAccepted;

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
     * @Vich\UploadableField(mapping="organization_logo", fileNameProperty="logoFileName")
     *
     * @var File
     */
    private $logoFile;

    /**
     * Field which will be stored to the database as a string.
     * This will hold the filename of the uploaded file.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $logoFileName;

    /**
     * @var string
     * @Assert\Url(
     *    message = "url.not_match",
     *    protocols = {"http", "https"},
     *    checkDNS = true,
     *    groups={"settings"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * Facebook page url
     *
     * @var string
     * @Assert\Url(
     *    message = "url.not_match",
     *    protocols = {"http", "https"},
     *    checkDNS = true,
     *    groups={"settings"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fbUrl;

    /**
     * Email contact address.
     *
     * @var string
     * @Assert\NotBlank(
     *     message = "email.notBlank",
     *     groups={"settings"}
     * )
     * @Gedmo\Versioned
     * @Assert\Email(
     *     message = "email.notMatch",
     *     groups={"settings"}
     * )
     * @ORM\Column(type="string", name="contact_email", length=255)
     */
    private $contactEmail;

    /**
     * Partners apply email address.
     *
     * @var string
     *
     * @Gedmo\Versioned
     * @Assert\Email(
     *     message = "email.notMatch",
     *     groups={"settings"}
     * )
     * @ORM\Column(type="string", name="partners_email", length=255, nullable=true)
     */
    private $partnersEmail;

    /**
     * @var string
     * @Assert\Url(
     *    message = "url.not_match",
     *    protocols = {"https"},
     *    checkDNS = true,
     *    groups={"settings"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $menteesExternalSignupUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /*
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @ORM\OneToMany(targetEntity="SectionConfig", mappedBy="organization")
     **/
    private $sections;

    public function __construct()
    {
        $this->isAccepted = false;
        $this->locale = ['en'];
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
    public function setLogoFile(File $image = null)
    {
        $this->logoFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /**
     * @param string $logoFileName
     */
    public function setLogoFileName($logoFileName)
    {
        $this->logoFileName = $logoFileName;
    }

    /**
     * @return string
     */
    public function getLogoFileName()
    {
        return $this->logoFileName;
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
     * Get possible locale list
     *
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Set possible locale list
     * @var array $array
     * @return Organization
     */
    public function setLocales($array)
    {
        $this->locales = $array;

        return $this;
    }

    /**
     * Get default locale list
     *
     * @return array
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * Set default locale
     * @var string $var
     * @return Organization
     */
    public function setDefaultLocale($var)
    {
        $this->defaultLocale = $var;

        return $this;
    }

    /**
     * Get required locale list
     *
     * @return array
     */
    public function getRequiredLocales()
    {
        return $this->requiredLocales;
    }

    /**
     * Set required locale list
     * @var array $array
     * @return Organization
     */
    public function setRequiredLocales($array)
    {
        $this->requiredLocales = $array;

        return $this;
    }

    /**
     * Set subdomain.
     *
     * @param string $subdomain
     *
     * @return Organization
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get subdomain.
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return Organization
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Organization
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

    public function setIsAccepted($var)
    {
        $this->isAccepted = $var;
    }

    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    /**
     * Set partners email.
     *
     * @param string $email
     *
     * @return Organization
     */
    public function setPartnersEmail($email)
    {
        $this->partnersEmail = $email;

        return $this;
    }

    /**
     * Get partners email.
     *
     * @return string
     */
    public function getPartnersEmail()
    {
        return $this->partnersEmail;
    }

    /**
     * Set contact email.
     *
     * @param string $email
     *
     * @return Orgaization
     */
    public function setContactEmail($email)
    {
        $this->contactEmail = $email;

        return $this;
    }

    /**
     * Get contact email.
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Organization
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
     * Set fbUrl.
     *
     * @param string $url
     *
     * @return Organization
     */
    public function setFbUrl($url)
    {
        $this->fbUrl = $url;

        return $this;
    }

    /**
     * Get fbUrl.
     *
     * @return string
     */
    public function getFbUrl()
    {
        return $this->fbUrl;
    }

    /**
     * Set external link for mentees signup.
     *
     * @param string $url
     *
     * @return Organization
     */
    public function setMenteesExternalSignupUrl($url)
    {
        $this->menteesExternalSignupUrl = $url;

        return $this;
    }

    /**
     * Get external link for mentees signup.
     *
     * @return string
     */
    public function getMenteesExternalSignupUrl()
    {
        return $this->menteesExternalSignupUrl;
    }

    /**
     * Get menu sections
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Get enabled organization's sections as collection
     * raw db results
     * @return ArrayCollection
     */
    public function getSectionsEnabled()
    {
        $criteria = Criteria::create()->where(
            Criteria::expr()->eq('isEnabled', true)
        );

        $sections = $this->getSections()->matching($criteria);

        return $sections;
    }

    /**
     * Get enabled organization's sections as array
     * raw db results
     * @return array
     */
    public function getSectionsEnabledArray()
    {
        $enabledSections = [];

        foreach ($this->getSectionsEnabled() as $i) {
            $enabledSections[$i->getSection()] = true;
        }

        return $enabledSections;
    }

    /**
     * Get organization's main page buttons that are enabled
     * based on enabled sections
     * @return array
     */
    public function getButtonsSectionsEnabledArray()
    {
        $enabledButtons = [];

        foreach ($this->getSectionsEnabled() as $i) {
            if (
                (   // menteesExternalSignup checked first
                    // as it has higher priority of mentees section settings
                    // and only one of them can be enabled
                    $i->getSection() === SectionConfig::menteesExternalSignup
                    ||
                    $i->getSection() === SectionConfig::sectionMentees
                )
                ||
                $i->getSection() === SectionConfig::sectionPartners
                ||
                $i->getSection() === SectionConfig::sectionMentors
            ) {
                $enabledButtons[$i->getSection()] = true;
            }
        }

        return $enabledButtons;
    }
}
