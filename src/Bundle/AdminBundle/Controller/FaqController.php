<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Entity\Faq;
use AdminBundle\Security\Annotation\SectionEnabled;

/**
 * Admin Faq controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/faq")
 * @Security("is_granted('ROLE_ADMIN')")
 * @SectionEnabled(name="faq")
 */
class FaqController extends Controller
{
    /**
     * Set Faq sortable position.
     *
     * @Route("/sort", name="faq_sort")
     * @Method("POST")
     * @Template("AdminBundle:faq:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Entity:Faq');

        $faqId = $request->request->get('id');
        $position = $request->request->get('position');

        $faq = $repo->findOneById($faqId);
        $faq->setPosition($position);
        $em->persist($faq);
        $em->flush();

        $faqs = $repo->getAll();

        return [
            'faqs' => $faqs,
        ];
    }

    /**
     * Lists all Faq entities.
     *
     * @Route("/", name="faq_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('Entity:Faq');
        $faqs = $repo->getAll();

        return [
            'faqs' => $faqs,
        ];
    }

    /**
     * Creates a new Faq entity.
     *
     * @Route("/new", name="faq_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $faq = new Faq();
        $form = $this->createForm('AdminBundle\Form\FaqType', $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            return $this->redirectToRoute('faq_show', array('id' => $faq->getId()));
        }

        return [
            'faq' => $faq,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Faq entity.
     *
     * @Route("/{id}", name="faq_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Faq $faq)
    {
        $deleteForm = $this->createDeleteForm($faq);

        return [
            'faq' => $faq,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Faq entity.
     *
     * @Route("/{id}/edit", name="faq_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Faq $faq)
    {
        $deleteForm = $this->createDeleteForm($faq);
        $editForm = $this->createForm('AdminBundle\Form\FaqType', $faq);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            return $this->redirectToRoute('faq_index', array('id' => $faq->getId()));
        }

        return [
            'faq' => $faq,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Faq entity.
     *
     * @Route("/{id}", name="faq_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Faq $faq)
    {
        $form = $this->createDeleteForm($faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faq);
            $em->flush();
        }

        return $this->redirectToRoute('faq_index');
    }

    /**
     * Creates a form to delete a Faq entity.
     *
     * @param FAQ $faq The Faq entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Faq $faq)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('faq_delete', array('id' => $faq->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
