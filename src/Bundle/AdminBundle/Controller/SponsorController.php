<?php

namespace AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Entity\Sponsor;
use Annotation\Controller\SectionEnabled;

/**
 * Admin Sponsor controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/sponsor")
 * @Security("is_granted('ROLE_ADMIN')")
 * @SectionEnabled(name="sponsors")
 */
class SponsorController extends Controller
{
    /**
     * Set sponsor sortable position.
     *
     * @Route("/sort", name="sponsor_sort")
     * @Method("POST")
     * @Template("AdminBundle:sponsor:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Entity:Sponsor');

        $sponsorId = $request->request->get('id');
        $position = $request->request->get('position');

        $sponsor = $repo->findOneById($sponsorId);
        $sponsor->setPosition($position);
        $em->persist($sponsor);
        $em->flush();

        $sponsors = $repo->getAll();

        return [
            'sponsors' => $sponsors,
        ];
    }

    /**
     * Lists all Sponsor entities.
     *
     * @Route("/", name="sponsor_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $sponsors = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Sponsor')
            ->getAll();

        return [
            'sponsors' => $sponsors,
        ];
    }

    /**
     * Creates a new Sponsor entity.
     *
     * @Route("/new", name="sponsor_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $sponsor = new Sponsor();
        $form = $this->createForm('AdminBundle\Form\SponsorType', $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sponsor);
            $em->flush();

            return $this->redirectToRoute('sponsor_show', array('id' => $sponsor->getId()));
        }

        return [
            'sponsor' => $sponsor,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Sponsor entity.
     *
     * @Route("/{id}", name="sponsor_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Sponsor $sponsor)
    {
        $deleteForm = $this->createDeleteForm($sponsor);

        return [
            'sponsor' => $sponsor,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Sponsor entity.
     *
     * @Route("/{id}/edit", name="sponsor_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Sponsor $sponsor)
    {
        $deleteForm = $this->createDeleteForm($sponsor);
        $editForm = $this->createForm('AdminBundle\Form\SponsorType', $sponsor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sponsor);
            $em->flush();

            return $this->redirectToRoute('sponsor_show', array('id' => $sponsor->getId()));
        }

        return [
            'sponsor' => $sponsor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Sponsor entity.
     *
     * @Route("/{id}", name="sponsor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Sponsor $sponsor)
    {
        $form = $this->createDeleteForm($sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sponsor);
            $em->flush();
        }

        return $this->redirectToRoute('sponsor_index');
    }

    /**
     * Creates a form to delete a Sponsor entity.
     *
     * @param Sponsor $sponsor The Sponsor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Sponsor $sponsor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sponsor_delete', array('id' => $sponsor->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
