<?php

namespace Wit\Program\Admin\SiteContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wit\Program\Admin\SiteContentBundle\Entity\MentorFaq;

/**
 * Admin MentorFaq controller.
 *
 * @Route("/mentorfaq")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class MentorFaqController extends Controller
{
    /**
     * Set mentor sortable position.
     *
     * @Route("/sort", name="mentorfaq_sort")
     * @Method("POST")
     * @Template("WitProgramAdminSiteContentBundle:MentorFaq:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('WitProgramAdminSiteContentBundle:MentorFaq');

        $mentorFaqId = $request->request->get('id');
        $position = $request->request->get('position');

        $text = $repo->findOneById($mentorFaqId);
        $text->setPosition($position);

        $em->persist($text);
        $em->flush();

        $mentorFaq = $repo->getAll();

        return [
            'mentorfaq' => $mentorFaq,
        ];
    }

    /**
     * Lists all MentorFaq entities.
     *
     * @Route("/", name="mentorfaq_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $mentorFaq = $this->getDoctrine()->getManager()
            ->getRepository('WitProgramAdminSiteContentBundle:MentorFaq')
            ->getAll();

        return [
            'mentorfaq' => $mentorFaq,
        ];
    }

    /**
     * Creates a new MentorFaq entity.
     *
     * @Route("/new",  name="mentorfaq_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $mentorFaq = new MentorFaq();
        $form = $this->createForm('Wit\Program\Admin\SiteContentBundle\Form\MentorFaqType', $mentorFaq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mentorFaq);
            $em->flush();

            return $this->redirectToRoute('mentorfaq_show', array('id' => $mentorFaq->getId()));
        }

        return [
            'mentorfaq' => $mentorFaq,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a MentorFaq entity.
     *
     * @Route("/{id}", name="mentorfaq_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(MentorFaq $mentorFaq)
    {
        $deleteForm = $this->createDeleteForm($mentorFaq);

        return [
            'mentorfaq' => $mentorFaq,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing MentorFaq entity.
     *
     * @Route("/{id}/edit", name="mentorfaq_edit")
     * @Method({"GET",      "POST"})
     * @Template
     */
    public function editAction(Request $request, MentorFaq $mentorFaq)
    {
        $deleteForm = $this->createDeleteForm($mentorFaq);
        $editForm = $this->createForm('Wit\Program\Admin\SiteContentBundle\Form\MentorFaqType', $mentorFaq);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mentorFaq);
            $em->flush();

            return $this->redirectToRoute('mentorfaq_index', array('id' => $mentorFaq->getId()));
        }

        return [
            'mentorfaq' => $mentorFaq,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a MentorFaq entity.
     *
     * @Route("/{id}",   name="mentorfaq_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MentorFaq $mentorFaq)
    {
        $form = $this->createDeleteForm($mentorFaq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mentorFaq);
            $em->flush();
        }

        return $this->redirectToRoute('mentorfaq_index');
    }

    /**
     * Creates a form to delete a MentorFaq entity.
     *
     * @param MentorFaq $mentorFaq The MentorFaq entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MentorFaq $mentorFaq)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mentorfaq_delete', array('id' => $mentorFaq->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
