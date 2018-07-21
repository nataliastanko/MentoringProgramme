<?php

namespace AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Entity\Organization;
use Annotation\Controller\SectionEnabled;
use Service\EventSubscriber\SubdomainAwareSubscriber;

/**
 * Admin Organization controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/organization")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class OrganizationController extends Controller
{
    /**
     * Lists all Organization entities.
     *
     * Route("/", name="organization_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $organizations = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Organization')
            ->getAll();

        return [
            'organizations' => $organizations,
        ];
    }

    /**
     * Creates a new Organization entity.
     *
     * Route("/new", name="organization_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $organization = new Organization();
        $form = $this->createForm('AdminBundle\Form\OrganizationType', $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            return $this->redirectToRoute('organization_show', array('id' => $organization->getId()));
        }

        return [
            'organization' => $organization,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays an Organization entity.
     *
     * @Route("/", name="organization_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(SubdomainAwareSubscriber $subdomainDetection)
    {
        // allow to edit only current organization
        $currentOrganization = $subdomainDetection->getOrganization();

        return [
            'organization' => $currentOrganization,
        ];
    }

    /**
     * Displays a form to edit an existing Organization entity.
     *
     * @Route("/{id}/edit", name="organization_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Organization $organization, SubdomainAwareSubscriber $subdomainDetection)
    {
        $currentOrganization = $subdomainDetection->getOrganization();

        /**
         * @todo create voters
         */
        if (
            $currentOrganization->getId() !== $organization->getId()
            &&
            !$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
            throw $this->createAccessDeniedException('No authorization to edit organization');
        }

        // $deleteForm = $this->createDeleteForm($organization);
        $editForm = $this->createForm(
            'AdminBundle\Form\OrganizationType',
            $organization
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            return $this->redirectToRoute('organization_show');
        }

        return [
            'organization' => $organization,
            'edit_form' => $editForm->createView(),
            // 'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes an Organization entity.
     *
     * Route("/{id}",   name="organization_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Organization $organization)
    {
        $form = $this->createDeleteForm($organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($organization);
            $em->flush();
        }

        return $this->redirectToRoute('organization_index');
    }

    /**
     * Creates a form to delete a Organization entity.
     *
     * @param Organization $organization The Organization entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Organization $organization)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('organization_delete', array('id' => $organization->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
