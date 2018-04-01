<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Annotation\Doctrine\OrganizationAware;

/**
 * Question.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(
 *     name="questions",
 *     indexes={
 *         @ORM\Index(name="type_idx", columns={
 *             "type"
 *             }
 *         )
 *     }
 * )
 * @ORM\Entity(repositoryClass="Repository\QuestionRepository")
 * @OrganizationAware(organizationFieldName="organization_id")
 */
class Question
{
    use Translatable;

    const TYPE_MENTOR = 'mentor';
    const TYPE_MENTEE = 'mentee';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="type", type="string", options={"default" = "mentee"})
     */
    private $type;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\Column(name="is_visible_for_mentor", type="boolean", options={"default" = true})
     */
    private $isVisibleForMentor;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question", cascade={"all"})
     **/
    private $answers;

    /**
     * Fixed answers to question that Person can choose
     * @ORM\OneToMany(targetEntity="AnswerChoice", mappedBy="question", cascade={"all"})
     **/
    private $answerChoices;

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
        $this->answerChoices = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function __call($method, $arguments)
    {
        return PropertyAccess::createPropertyAccessor()
            ->getValue($this->translate(), $method);
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
     * Set answerChoices.
     *
     * @param string $answerChoices
     *
     * @return Question
     */
    public function setAnswerChoices($answerChoices)
    {
        $this->answerChoices = $answerChoices;

        return $this;
    }

    /**
     * Get answerChoices.
     *
     * @return string
     */
    public function getAnswerChoices()
    {
        return $this->answerChoices;
    }

    public function addAnswerChoice(AnswerChoice $choice)
    {
        $this->answerChoices->add($choice);
        $choice->setQuestion($this);
    }

    // public function removeAnswerChoice(Tag $choice)
    // {
    //     $this->answerChoices->removeElement($choice);
    // }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setIsVisibleForMentor($var)
    {
        $this->isVisibleForMentor = $var;
    }

    public function getIsVisibleForMentor()
    {
        return $this->isVisibleForMentor;
    }

    public function setType($var)
    {
        $this->type = $var;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setOrganization($var)
    {
        $this->organization = $var;

        return $this;
    }

    /**
     * Name is a translatable and mandatory field (in another table)
     * @Assert\Callback(groups={"settings"})
     */
    public function atLeastOneQuestionTranslation(ExecutionContextInterface $context)
    {
        /* @FIXME hardcoded locale list*/
        if ( !$this->translate('en', false)->getName() || !$this->translate('pl', false)->getName() ) {
            $context->buildViolation('question.name.bothNeeded')
                    ->atPath('name')
                    ->addViolation();
        }
    }

}
