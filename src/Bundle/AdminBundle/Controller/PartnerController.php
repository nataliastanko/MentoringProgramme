<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Entity\Partner;
use AdminBundle\Security\Annotation\SectionEnabled;

/**
 * Admin Partner controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/partner")
 * @Security("is_granted('ROLE_ADMIN')")
 * @SectionEnabled(name="partners")
 */
class PartnerController extends Controller
{
    /**
     * Set partner sortable position.
     *
     * @Route("/sort", name="partner_sort")
     * @Method("POST")
     * @Template("AdminBundle:partner:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Entity:Partner');

        $partnerId = $request->request->get('id');
        $position = $request->request->get('position');

        $partner = $repo->findOneById($partnerId);
        $partner->setPosition($position);
        $em->persist($partner);
        $em->flush();

        $partners = $repo->getAll();

        return [
            'partners' => $partners,
        ];
    }

    /**
     * Lists all Partner entities.
     *
     * @Route("/",    name="partner_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $partners = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Partner')
            ->getAll();

        return [
            'partners' => $partners,
        ];
    }

    /**
     * Creates a new Partner entity.
     *
     * @Route("/new",  name="partner_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $partner = new Partner();
        $form = $this->createForm('AdminBundle\Form\PartnerType', $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partner);
            $em->flush();

            return $this->redirectToRoute('partner_show', array('id' => $partner->getId()));
        }

        return [
            'partner' => $partner,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Partner entity.
     *
     * @Route("/{id}", name="partner_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Partner $partner)
    {
        $deleteForm = $this->createDeleteForm($partner);

        return [
            'partner' => $partner,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Partner entity.
     *
     * @Route("/{id}/edit", name="partner_edit")
     * @Method({"GET",      "POST"})
     * @Template
     */
    public function editAction(Request $request, Partner $partner)
    {
        $deleteForm = $this->createDeleteForm($partner);
        $editForm = $this->createForm('AdminBundle\Form\PartnerType', $partner);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partner);
            $em->flush();

            return $this->redirectToRoute('partner_show', array('id' => $partner->getId()));
        }

        return [
            'partner' => $partner,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Partner entity.
     *
     * @Route("/{id}",   name="partner_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Partner $partner)
    {
        $form = $this->createDeleteForm($partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($partner);
            $em->flush();
        }

        return $this->redirectToRoute('partner_index');
    }

    /**
     * Creates a form to delete a Partner entity.
     *
     * @param Partner $partner The Partner entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Partner $partner)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('partner_delete', array('id' => $partner->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
