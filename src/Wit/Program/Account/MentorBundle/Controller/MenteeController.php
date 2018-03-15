<?php

namespace Wit\Program\Account\MentorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wit\Program\Account\UserBundle\Entity\User;
use Wit\Program\Admin\EditionBundle\Entity\Person;

/**
 * Account mentor controller.
 * @Route("/mentee")
 * @Security("is_granted('ROLE_MENTOR')")
 */
class MenteeController extends Controller
{
    /**
     * @Route("/", name="account_mentor_mentees_list")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // last edition
        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        $mentor = $this->getUser()->getInvitation()->getMentor();

        $persons = $this->getDoctrine()->getManager()
            ->getRepository('WitProgramAdminEditionBundle:Person')
            ->filterByMentorAndEdition($mentor, $edition, true); // only accepted persons

        $mentor = $this->getUser()->getInvitation()->getMentor();

        return [
            'mentor' => $mentor,
            'persons' => $persons,
            'config' => $config,
        ];
    }

    /**
     * @Route("/{id}", name="account_mentor_mentee_show")
     * @Template
     */
    public function showAction(Person $person)
    {
        $mentor = $this->getUser()->getInvitation()->getMentor();
        $acceptedMentees = $mentor->findAcceptedMentees();

        if (!$acceptedMentees->contains($person) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        // person answers
        $answers = $em->getRepository('WitProgramAdminQuestionnaireBundle:Answer')->filterByPerson($person->getId());

        try {
            $videoEmbed = new \IvoPetkov\VideoEmbed($person->getVideoUrl());
        } catch (\Exception $e) {
            $videoEmbed = null;
        }

        $mentor = $this->getUser()->getInvitation()->getMentor();
        $chooseForm = $this->createChooseForm($person);

        return [
            'person' => $person,
            'mentor' => $mentor,
            'answers' => $answers,
            'config' => $config,
            'videoEmbed' => $videoEmbed,
            'choose_form' => $chooseForm->createView(),
        ];
    }

    /**
     * Mark person/mentee as chosen
     *
     * @Route(
     *     "/choose/{id}",
     *     name="account_mentor_mentee_choose"
     * )
     * @Method("PUT")
     * @Template
     */
    public function chooseAction(Request $request, Person $person)
    {
        $mentor = $this->getUser()->getInvitation()->getMentor();

        if ($mentor->findChosenPerson()) {
            $request->getSession()->getFlashBag()
                ->add('warning', 'That mentor did already choose a mentee');
            return $this->redirectToRoute('account');
        }

        $acceptedMentees = $mentor->findAcceptedMentees();

        if (!$acceptedMentees->contains($person) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if ($config->getIsSignupMenteesEnabled()) {
            throw $this->createNotFoundException('config.chooseMentee.not.enabled');
        }

        $form = $this->createChooseForm($person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $person->getIsAccepted()) {
            $em = $this->getDoctrine()->getManager();
            $person->setIsChosen(true);
            // after this assignment you won't be able to change mentor for this person anymore
            // unless you make her not chosen again
            $em->persist($person);
            $em->flush();
        }

        return $this->redirectToRoute('account_mentor_mentee_show', ['id' => $person->getId()]);
    }

    /**
     * Creates a form to choose a Person.
     *
     * @param Person $person
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createChooseForm(Person $person)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('account_mentor_mentee_choose', array('id' => $person->getId())))
            ->setMethod('PUT')
            ->getForm();
    }
}
