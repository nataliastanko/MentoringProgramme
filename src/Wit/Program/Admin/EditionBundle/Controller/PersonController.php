<?php

namespace Wit\Program\Admin\EditionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wit\Program\Admin\EditionBundle\Entity\Person;
use Wit\Program\Admin\EditionBundle\Entity\Edition;

/**
 * Person controller.
 *
 * @Route("/person")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class PersonController extends Controller
{
    /**
     * Lists all Person entities.
     *
     * @Route(
     *     "/",
     *     name="person_index"
     * )
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // $query = $em
        //     ->createQuery('SELECT a FROM WitProgramAdminEditionBundle:Person a order by a.createdAt desc');

        // $persons = $this->get('knp_paginator')->paginate(
        //     $query, /* query NOT result */
        //     $request->query->getInt('page', $page) /*page number*/,
        //     $this->get('service_container')->getParameter('paginate.limit') /*limit per page*/
        // );

        // last edition
        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $persons = $em->getRepository('WitProgramAdminEditionBundle:Person')
            ->getFromEdition($edition->getId());

        // to display select options
        $editions = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->getBySortableGroupsQuery()->getResult();

        return [
            'editions' => $editions,
            'edition' => $edition,
            'persons' => $persons,
        ];
    }

    /**
     * Get persons from given edition
     * called by AJAX
     * @Route(
     *     "/edition/{id}",
     *     name="edition_persons_list"
     * )
     * @Method("GET")
     * @Template("WitProgramAdminEditionBundle:Person:_list.html.twig")
     */
    public function listReloadAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')->findOneById($id);

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $persons = $em->getRepository('WitProgramAdminEditionBundle:Person')
            ->getFromEdition($edition->getId());

        // to display select options
        $editions = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->getBySortableGroupsQuery()->getResult();

        return [
            'persons' => $persons,
            'editions' => $editions,
        ];
    }

    /**
     * Finds and displays a Person entity.
     *
     * Change person's mentor choice
     * only when recrutation for edition is finished
     *
     * @Route("/{id}", name="person_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Person $person)
    {
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

        $deleteForm = $this->createDeleteForm($person);
        $acceptForm = $this->createAcceptForm($person);
        $chooseForm = $this->createChooseForm($person, $person->getMentor());
        $inviteForm = $this->createInviteForm($person);

        return [
            'person' => $person,
            'answers' => $answers,
            'config' => $config,
            'videoEmbed' => $videoEmbed,
            'choose_form' => $chooseForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'accept_form' => $acceptForm->createView(),
            'invite_form' => $inviteForm->createView(),
        ];
    }

    /**
     * Export person's answers to pdf
     *
     * @Route(
     *     "/{id}/export/answers",
     *     name="person_export_answers"
     * )
     * @TODO do not save pdf on server, return streamed document to save
     * @Method("GET")
     * @Template("WitProgramAdminEditionBundle:Person/Export:answers.html.twig")
     */
    public function exportAnswersAction(Request $request, Person $person)
    {
        $dataToPdf = [
                'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                'person' => $person
            ];

        // return $dataToPdf;

        $html = $this->renderView(
            'WitProgramAdminEditionBundle:Person/Export:answers.html.twig',
            $dataToPdf
        );

        $filename = preg_replace('/\s+/', '', trim($person->getFullname()));

        $this->get('knp_snappy.pdf')->setOption('footer-center', '[page]');
        $this->get('knp_snappy.pdf')->setOption('footer-font-size', '10');
        $this->get('knp_snappy.pdf')->setOption('title', "Tech Leaders Mentor / {$person->getMentor()->getFullname()}");

        $filename = 'attachment; filename="'.$filename.'.pdf"';

        // $this->get('knp_snappy.pdf')->setOption('header-html', 'http://techleaders.eu');
            // ->setOption('footer-html', 'http://techleaders.eu');
            ;

        return new Response(
            $this->get('knp_snappy.pdf')
                ->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => $filename
            )
        );
    }

    /**
     * Export persons list frome dition
     *
     * @Route("/export/list/{edition}", name="person_export_list")
     * @Method("GET")
     */
    public function exportListAction(Edition $edition, Request $request)
    {
        try {
            /**
             * @var StreamedResponse
             */
            $response = $this->get('program.excel.export')->exportEditionMenteesList($edition);
            // adding headers
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'edition-'.$edition->getName().'-mentees-export-'.date('Y-m-d').'.xls' // filename
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response;

        } catch (\Exception $e) {

            // logger
            $exportLogger = $this->get('monolog.logger.export');
            $exportLogger->critical( // content: mail.CRITICAL:
                'Export mentees edition list xls: '.$e->getMessage()
            );

            // flash message
            $request->getSession()->getFlashBag()
                ->add('warning', 'Unable to generate xls file');
            return $this->redirectToRoute('person_index');
        }
    }

    /**
     * Export winners of edition
     *
     * @Route("/export/chosen/{edition}", name="person_export_chosen")
     * @Method("GET")
     * TODO method POST
     */
    public function exportChosenAction(Edition $edition, Request $request)
    {
        try {
            /**
             * @var StreamedResponse
             */
            $response = $this->get('program.excel.export')->exportEditionChosen($edition);
            // adding headers
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'edition-'.$edition->getName().'-chosen-mentees-'.date('Y-m-d').'.xls' // filename
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response;

        } catch (\Exception $e) {

            // logger
            $exportLogger = $this->get('monolog.logger.export');
            $exportLogger->critical( // content: mail.CRITICAL:
                'Export chosen mentees in edition xls: '.$e->getMessage()
            );

            // flash message
            $request->getSession() ->getFlashBag()
                ->add('warning', 'Unable to generate xls file');
            return $this->redirectToRoute('person_index');
        }
    }

    /**
     * Mark person/mentee as accepted to the program
     *
     * @Route(
     *     "/accept/{id}",
     *     name="person_accept"
     * )
     * @Method("PUT")
     * @Template
     */
    public function acceptAction(Request $request, Person $person)
    {
        $form = $this->createAcceptForm($person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person->setIsAccepted(true);
            $em->persist($person);
            $em->flush();
        }

        return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
    }

    /**
     * Change person's mentor choice
     * only when recrutation for edition is finished
     *
     * @Route(
     *     "/change-mentor/{id}",
     *     name="person_change_mentor"
     * )
     * @Method({"GET", "POST"})
     * @Template
     */
    public function changeMentorAction(Request $request, Person $person)
    {
        $em = $this->getDoctrine()->getManager();

        if ($person->getIsChosen()) {
            // you can not change mentor if the mentee is already marked as chosen
            throw $this->createNotFoundException('you can not change mentor if the mentee is  marked as chosen');
        }

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if ($config->getIsSignupMenteesEnabled()) {
            throw $this->createNotFoundException('config.changeMentor.not.enabled');
        }

        $editForm = $this->createForm(
            'Wit\Program\Admin\EditionBundle\Form\PersonChangeMentorType',
            //PersonChangeMentorType::class,
            $person,
            // wybór mentorów tylko z edycji w której osoba startowała
            ['editionId' => $person->getEdition()->getId()]
        );

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Form to choose this person as a mentee on behalf of mentor
     *
     * @Route(
     *     "/{id}/choose",
     *     name="person_choose"
     * )
     * @Method("PUT")
     * @Template
     */
    public function chooseAction(Request $request, Person $person)
    {
        if ($person->getMentor()->findChosenPerson()) {
            $request->getSession()->getFlashBag()
                ->add('warning', 'That mentor did already choose a mentee');
            return $this->redirectToRoute('mentor_show', ['id' => $person->getMentor()->getId()]);
        }

        $em = $this->getDoctrine()->getManager();

        if (!$person->getIsAccepted()) {
            throw $this->createNotFoundException('You can not choose person that is not accepted to the program');
        }

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if ($config->getIsSignupMenteesEnabled()) {
            throw $this->createNotFoundException('config.chooseMentee.not.enabled');
        }

        $chooseForm = $this->createChooseForm($person);
        $chooseForm->handleRequest($request);

        if ($chooseForm->isSubmitted() && $chooseForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person->setIsChosen(true);
            // after this assignment you won't be able to change mentor for this person
            // unless you make her not chosen again
            $em->persist($person);
            $em->flush();

        }

        return $this->redirectToRoute('person_show', array('id' => $person->getId()));
    }

    /**
     * Deletes a Person entity.
     *
     * @Route("/{id}",   name="person_delete")
     * @Method("DELETE")
     *
     * Will throw a normal AccessDeniedException:
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, Person $person)
    {
        // if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
        //     throw $this->createAccessDeniedException('GET OUT!');
        // }

        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createDeleteForm($person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($person);
            $em->flush();
        }

        return $this->redirectToRoute('person_index');
    }

    /**
     * Creates a form to mark this mentee as chosen
     *
     * @param Person $mentor The Person entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createChooseForm(Person $person)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'person_choose',
                    [
                        'id' => $person->getId(),
                    ]
                )
            )
            ->setMethod('PUT')
            ->getForm();
    }

    /**
     * Creates a form to delete a Person.
     *
     * @param Person $person
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Person $person)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('person_delete', array('id' => $person->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Creates a form to accept a Person.
     *
     * @param Person $person
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAcceptForm(Person $person)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('person_accept', array('id' => $person->getId())))
            ->setMethod('PUT')
            ->getForm();
    }

    /**
     * Creates a form to invite a Mentor to the program (send registration mail).
     *
     * @param Mentor $mentor The Person entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createInviteForm(Person $person)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('invite_mentee', array('id' => $person->getId())))
            ->setMethod('PUT')
            ->getForm();
    }
}
