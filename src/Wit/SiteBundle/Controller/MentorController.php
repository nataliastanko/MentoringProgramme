<?php

namespace Wit\SiteBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wit\Program\Admin\QuestionnaireBundle\Entity\Answer;
use Wit\Program\Admin\QuestionnaireBundle\Entity\Question;
use Wit\Program\Admin\EditionBundle\Entity\Mentor;
use Wit\SiteBundle\Form\MentorType;

/**
 * @Route("/mentor")
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

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

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

        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $repo = $em->getRepository('WitProgramAdminQuestionnaireBundle:Question');
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
                'WitSiteBundle:Mentor:thankyou.html.twig',
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
