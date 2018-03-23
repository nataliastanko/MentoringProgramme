<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Entity\Answer;
use Entity\Question;
use Doctrine\ORM\EntityRepository;

class AnswerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, function (FormEvent $event) {

                $form = $event->getForm();

                /* @var Answer */
                $data = $event->getData();

                if (count($data->getQuestion()->getAnswerChoices())) {
                    // pytanie zamkniete
                    $question = $data->getQuestion();

                    // załaduj tłumaczenia
                    $form->add(
                        'answerChoice', EntityType::class, [
                        'class' => 'Entity:AnswerChoice',
                        'choice_label' => 'name',
                        'placeholder' => 'choice.choose',
                        'query_builder' => function (EntityRepository $er) use ($question) {
                            /* @FIXME reduce number of queries*/
                            return $er->createQueryBuilder('c')
                                ->leftJoin('c.question', 'q')
                                ->where('q.id = ?1')
                                ->setParameter('1', $question->getId())
                                ->orderBy('c.id', 'ASC');
                        },
                        ]
                    );
                } else {
                    // pytanie otwarte
                    $form->add(
                        'content', TextareaType::class, [
                            'attr' => [
                                'rows' => '10'
                            ],
                            'label' => 'answer.name', // overriden in template by question.name
                        ]
                    );
                }

            }
        );

        // $builder->addEventListener(
        //     FormEvents::SUBMIT, function (FormEvent $event) {

        //         // set Person or Mentee depending on Answer type

        //         /* @var Answer */
        //         $answer = $event->getData();

        //         $question = $answer->getQuestion();

        //         // var_dump($answer->getMentor()->getEmail());exit;
        //         if ($question->getType() == Question::TYPE_MENTOR) {
        //             // $answer->setMentor();
        //         } elseif ($question->getType() == Question::TYPE_MENTEE) {
        //             // $answer->setPerson();
        //         }
        //     }
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'validation_groups' => ['add'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'answers';
    }
}
