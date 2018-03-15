<?php

namespace Wit\Program\Admin\EditionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wit\Program\Admin\EditionBundle\Entity\Mentor;
use Wit\Program\Admin\EditionBundle\Entity\Edition;

/**
 * Admin Mentor controller.
 *
 * @Route("/mentor")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class MentorController extends Controller
{
    /**
     * Set/change mentor sortable position.
     * called by AJAX
     *
     * @Route("/sort", name="mentor_sort")
     * @Method("POST")
     *
     * Refresh list of mentors
     * @Template("WitProgramAdminEditionBundle:Mentor:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('WitProgramAdminEditionBundle:Mentor');

        $mentorId = $request->request->get('id');
        $position = $request->request->get('position');
        $editionId = $request->request->get('edition');

        $mentor = $repo->findOneById($mentorId);
        $mentor->setPosition($position);
        $em->persist($mentor);
        $em->flush();

        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findOneById($editionId);

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        $mentorsViewWithEdition = true;

        $mentors = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
            ->getFromEdition($edition->getId());

        $inviteForms = [];
        foreach ($mentors as $entity) {
          $inviteForms[$entity->getId()] =
            $this->createInviteForm($entity)->createView();
        }

        return [
            'mentors' => $mentors,
            'config' => $config,
            'mentorsViewWithEdition' => $mentorsViewWithEdition,
            'inviteForms' => $inviteForms,
        ];
    }

    /**
     * Search Mentor entities.
     *
     * @Route("/search", name="mentor_search",
     * defaults={"page":1},
     * requirements={"page":"\d+"}
     * )
     * @Method("POST")
     * @Template
     */
    public function searchAction($page, Request $request)
    {
        // $search = $request->get('search');
        // $queryBuilder = $this->get('petkopara_multi_search.builder')
        //     ->searchEntity(
        //         $queryBuilder,
        //         'WitProgramAdminEditionBundle:Mentor',
        //         $search,
        //         ['name', 'lastName', 'email'],
        //         'wildcard'
        //     );

        $filterForm = $this->createForm('Wit\Program\Admin\EditionBundle\Form\Filter\MentorSearch');

        // Bind values from the request
        $filterForm->handleRequest($request);

        if ($filterForm->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // @TODO add to mentors repository (service repo)
            $qb = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
                ->createQueryBuilder('m')
            ;
            // Build the query from the given form object
            $qb = $this->get('petkopara_multi_search.builder')
                ->searchForm($qb, $filterForm->get('search'))
            ;
            // kolejność ma znaczenie, searchForm() musi być wyołany wczesniej
            // albo nici z optymalizacji
            $qb
                ->select('m')
                ->leftJoin('m.editions', 'e') // inner join wont get mentors without edition
                ->addSelect('e') // reduces number of queries
                // ->orderBy('m.createdAt', 'desc')
                ->orderBy('m.id', 'desc') // order by submission order
            ;

            $mentors = $qb->getQuery()->getResult();

            $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

            if (!$config) {
                throw $this->createNotFoundException('No config found');
            }

            $mentorsViewWithEdition = false;

        } else {
            return $this->forward(
                'WitProgramAdminEditionBundle:Mentor:index',
                [
                    'page' => 1
                ]
            );
        }

        $inviteForms = [];
        foreach ($mentors as $entity) {
            $inviteForms[$entity->getId()] =
                $this->createInviteForm($entity)->createView();
        }

        return [
            'mentors' => $mentors,
            'config' => $config,
            'mentorsViewWithEdition' => $mentorsViewWithEdition,
            'inviteForms' => $inviteForms,
            'filterForm' => $filterForm->createView()
        ];
    }

    /**
     * Lists all Mentor entities.
     *
     * @Route("/", name="mentor_index",
     * defaults={"page":1},
     * requirements={"page":"\d+"}
     * )
     * @Method("GET")
     * @Template
     */
    public function indexAction($page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        // last edition
        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        $mentors = $em
            ->getRepository('WitProgramAdminEditionBundle:Mentor')
            ->getFromEdition($edition->getId(), true, true);
        $mentorsViewWithEdition = true;

        // $mentorsQuery = $em
        //     ->getRepository('WitProgramAdminEditionBundle:Mentor')
        //     ->listSubmissionsQuery();
        //     // ->getResult();

        // $mentors = $this->get('knp_paginator')->paginate(
        //     $mentorsQuery, /* query NOT result */
        //     $request->query->getInt('page', $page) /*page number*/,
        //     $this->get('service_container')->getParameter('paginate.limit') /*limit per page*/
        // );

        $filterForm = $this->createSearchForm();

        // to display select options
        $editions = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->getBySortableGroupsQuery()->getResult();

        $inviteForms = [];
        foreach ($mentors as $entity) {
            $inviteForms[$entity->getId()] =
                $this->createInviteForm($entity)->createView();
        }

        return [
            'mentors' => $mentors,
            'config' => $config,
            'mentorsViewWithEdition' => $mentorsViewWithEdition,
            'editions' => $editions,
            'inviteForms' => $inviteForms,
            'filterForm' => $filterForm->createView()
        ];
    }

    /**
     * Get mentors from given edition
     * called by AJAX
     * @Route(
     *     "/edition/{id}",
     *     name="edition_mentors_list"
     * )
     * @Method("GET")
     * @Template("WitProgramAdminEditionBundle:Mentor:_list.html.twig")
     */
    public function listReloadAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if ($id === 'hasNoEdition') {
            // without edition
            $mentors = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
                ->getWithoutEdition();

            $mentorsViewWithEdition = false;
        } else {
            $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')->findOneById($id);

            if (!$edition) {
                throw $this->createNotFoundException('No edition found');
            }

            $mentors = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
                ->getFromEdition($edition->getId());

            $mentorsViewWithEdition = true;
        }

        $inviteForms = [];
        foreach ($mentors as $entity) {
            $inviteForms[$entity->getId()] =
                $this->createInviteForm($entity)->createView();
        }

        // to display select options
        $editions = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->getBySortableGroupsQuery()->getResult();

        return [
            'mentors' => $mentors,
            'config' => $config,
            'mentorsViewWithEdition' => $mentorsViewWithEdition,
            'inviteForms' => $inviteForms,
            'editions' => $editions,
        ];
    }

    /**
     * Get mentor's from given edition
     * called by AJAX
     * @Route(
     *     "/edition/{id}/sort",
     *     name="edition_mentors_sort"
     * )
     * @Method("GET")
     * @Template("WitProgramAdminEditionBundle:Mentor:_sort.html.twig")
     */
    public function sortReloadAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if ($id === 'hasNoEdition') {
            // without edition
            $mentors = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
                ->getWithoutEdition();

            $mentorsViewWithEdition = false;
        } else {
            $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')->findOneById($id);

            if (!$edition) {
                throw $this->createNotFoundException('No edition found');
            }

            $mentors = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
                ->getFromEdition($edition->getId());

            $mentorsViewWithEdition = true;
        }

        // to display select options
        $editions = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->getBySortableGroupsQuery()->getResult();

        return [
            'mentors' => $mentors,
            'config' => $config,
            'editions' => $editions,
            'mentorsViewWithEdition' => $mentorsViewWithEdition,
        ];
    }

    private function createSearchForm() {
        return $this->createForm(
            'Wit\Program\Admin\EditionBundle\Form\Filter\MentorSearch',
            null,
            [
                'action' => $this->generateUrl('mentor_search'),
                'method' => 'POST'
            ]
        );
    }

    /**
     * Creates a new Mentor entity.
     *
     * @Route("/new",  name="mentor_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $mentor = new Mentor();
        $form = $this->createForm('Wit\Program\Admin\EditionBundle\Form\MentorType', $mentor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mentor);
            $em->flush();

            return $this->redirectToRoute('mentor_show', array('id' => $mentor->getId()));
        }

        return [
            'mentor' => $mentor,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Mentor entity.
     *
     * @Route("/{id}", name="mentor_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Mentor $mentor)
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        // mentors answers
        $answers = $question = $em->getRepository('WitProgramAdminQuestionnaireBundle:Answer')->filterByMentor($mentor->getId());

        $persons = $this->getDoctrine()->getManager()
            ->getRepository('WitProgramAdminEditionBundle:Person')
            ->filterByMentorAndEdition($mentor, $edition);

        $deleteForm = $this->createDeleteForm($mentor);
        $inviteForm = $this->createInviteForm($mentor);

        return [
            'mentor' => $mentor,
            'config' => $config,
            'answers' => $answers,
            'persons' => $persons,
            'edition' => $edition,
            'delete_form' => $deleteForm->createView(),
            'invite_form' => $inviteForm->createView(),
        ];
    }

    /**
     * Get mentor's persons
     *
     * @Route(
     *     "/{id}/{edition}/persons",
     *     name="mentor_persons"
     * )
     * @Method("GET")
     * @Template("WitProgramAdminEditionBundle:Person:_list.html.twig")
     */
    public function personsAction(Request $request, Mentor $mentor, Edition $edition)
    {
        $persons = $this->getDoctrine()->getManager()
            ->getRepository('WitProgramAdminEditionBundle:Person')
            ->filterByMentorAndEdition($mentor, $edition);

        return [
            'mentor' => $mentor,
            'edition' => $edition,
            'persons' => $persons
        ];
    }

    /**
     * Export mentees answers from given mentor to pdf
     *
     * @Route(
     *     "/{id}/{edition}/export/mentees/answers",
     *     name="mentor_export_mentees_answers"
     * )
     * @Method("GET")
     * @Template("WitProgramAdminEditionBundle:Mentor/Export:persons_answers.html.twig")
     */
    public function exportPersonsAnswersAction(Request $request, Mentor $mentor, Edition $edition)
    {
        $persons = $this->getDoctrine()->getManager()
            ->getRepository('WitProgramAdminEditionBundle:Person')
            ->filterByMentorAndEdition($mentor, $edition);

        $dataToPdf = [
                'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                'mentor' => $mentor,
                'edition' => $edition,
                'persons' => $persons
            ];

        // return $dataToPdf;

        $html = $this->renderView(
            'WitProgramAdminEditionBundle:Mentor/Export:persons_answers.html.twig',
            $dataToPdf
        );

        $filename = preg_replace('/\s+/', '', trim($mentor->getFullname()));

        $this->get('knp_snappy.pdf')->setOption('footer-center', '[page]');
        $this->get('knp_snappy.pdf')->setOption('footer-font-size', '10');
        $this->get('knp_snappy.pdf')->setOption('title', "Tech Leaders Mentor / {$mentor->getFullname()}");

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
     * Displays a form to edit an existing Mentor entity.
     *
     * @Route(
     *     "/{id}/edit",
     *     name="mentor_edit"
     * )
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Mentor $mentor)
    {
        $deleteForm = $this->createDeleteForm($mentor);
        $editForm = $this->createForm('Wit\Program\Admin\EditionBundle\Form\MentorType', $mentor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mentor);
            $em->flush();

            return $this->redirectToRoute('mentor_show', array('id' => $mentor->getId()));
        }

        return [
            'mentor' => $mentor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Mentor entity.
     *
     * @Route("/{id}", name="mentor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Mentor $mentor)
    {
        $form = $this->createDeleteForm($mentor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mentor);
            $em->flush();
        }

        return $this->redirectToRoute('mentor_index');
    }

    /**
     * Creates a form to delete a Mentor entity.
     *
     * @param Mentor $mentor The Mentor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Mentor $mentor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mentor_delete', array('id' => $mentor->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Creates a form to invite a Mentor to the program (send registration mail).
     *
     * @param Mentor $mentor The Mentor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createInviteForm(Mentor $mentor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('invite_mentor', array('id' => $mentor->getId())))
            ->setMethod('PUT')
            ->getForm();
    }

}
