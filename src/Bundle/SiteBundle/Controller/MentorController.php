<?php

namespace SiteBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Entity\Answer;
use Entity\Question;
use Entity\Mentor;
use SiteBundle\Form\MentorType;
use AdminBundle\Security\Annotation\SectionEnabled;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/mentor")
 * @SectionEnabled(name="mentors")
 */
class MentorController extends Controller
{
    /**
     * @Route(
     *     "/apply",
     *     name="mentor_apply"
     * )
     * @Method({"GET", "POST"})
     *
     * @Template()
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('Entity:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if (!$config->getIsSignupMentorsEnabled()) {
            // flash message
            $request->getSession()->getFlashBag()
                ->add('warning', 'config.signup.closed');
            return $this->redirectToRoute('homepage');
            // throw $this->createNotFoundException('config.signup.not.enabled');
        }

        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $repo = $em->getRepository('Entity:Question');
        $questions = $repo->getFromType(Question::TYPE_MENTOR);

        // var_dump(count($questions));exit;
        $mentor = new Mentor();

        // mentor has no answerChoises for now, we can skip it
        // new mentor who applies has no edition for now, we can skip it

        foreach ($questions as $question) {
            $answer = new Answer();
            $answer->setQuestion($question);
            $answer->setMentor($mentor);

            $mentor->getAnswers()->add($answer); // add to collection
        }

        $form = $this->createForm(MentorType::class, $mentor);

        $form->handleRequest($request);

        $twigArr = [
            'form' => $form->createView(),
            'edition' => $edition
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // new mentor who applies has no edition for now, we can skip it

            $em->persist($mentor);
            $em->flush();

            return $this->render(
                'SiteBundle:mentor:thankyou.html.twig',
                $twigArr
            );
        }

        // foreach ($form->getErrors() as $key => $value) {
        //     var_dump($value->getMessage());
        // }
        // exit;

        return $twigArr;
    }
}
