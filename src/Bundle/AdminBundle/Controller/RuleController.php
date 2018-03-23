<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Entity\Rule;

/**
 * Admin Rule controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/rule")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class RuleController extends Controller
{
    /**
     * Set Rule sortable position.
     *
     * @Route("/sort", name="rule_sort")
     * @Method("POST")
     * @Template("AdminBundle:rule:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Entity:Rule');

        $ruleId = $request->request->get('id');
        $position = $request->request->get('position');

        $rule = $repo->findOneById($ruleId);
        $rule->setPosition($position);
        $em->persist($rule);
        $em->flush();

        $rules = $repo->getAll();

        return [
            'rules' => $rules,
        ];
    }

    /**
     * Lists all Rule entities.
     *
     * @Route("/", name="rule_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('Entity:Rule');
        $rules = $repo->getAll();

        return [
            'rules' => $rules,
        ];
    }

    /**
     * Creates a new Rule entity.
     *
     * @Route("/new", name="rule_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $rule = new Rule();
        $form = $this->createForm('AdminBundle\Form\RuleType', $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rule);
            $em->flush();

            return $this->redirectToRoute('rule_show', array('id' => $rule->getId()));
        }

        return [
            'rule' => $rule,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Rule entity.
     *
     * @Route("/{id}", name="rule_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Rule $rule)
    {
        $deleteForm = $this->createDeleteForm($rule);

        return [
            'rule' => $rule,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Rule entity.
     *
     * @Route("/{id}/edit", name="rule_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Rule $rule)
    {
        $deleteForm = $this->createDeleteForm($rule);
        $editForm = $this->createForm('AdminBundle\Form\RuleType', $rule);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rule);
            $em->flush();

            return $this->redirectToRoute('rule_index', array('id' => $rule->getId()));
        }

        return [
            'rule' => $rule,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Rule entity.
     *
     * @Route("/{id}", name="rule_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Rule $rule)
    {
        $form = $this->createDeleteForm($rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rule);
            $em->flush();
        }

        return $this->redirectToRoute('rule_index');
    }

    /**
     * Creates a form to delete a Rule entity.
     *
     * @param Rule $rule The Rule entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rule $rule)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rule_delete', array('id' => $rule->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
