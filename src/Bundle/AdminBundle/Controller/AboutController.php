<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Entity\About;
use Annotation\Controller\SectionEnabled;

/**
 * Admin About controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/about")
 * @Security("is_granted('ROLE_ADMIN')")
 * @SectionEnabled(name="about")
 */
class AboutController extends Controller
{
    /**
     * Set mentor sortable position.
     *
     * @Route("/sort", name="about_sort")
     * @Method("POST")
     * @Template("AdminBundle:about:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('Entity:About');

        $aboutId = $request->request->get('id');
        $position = $request->request->get('position');

        $text = $repo->findOneById($aboutId);
        $text->setPosition($position);

        $em->persist($text);
        $em->flush();

        $about = $repo->getAll();

        return [
            'about' => $about,
        ];
    }

    /**
     * Lists all About entities.
     *
     * @Route("/",    name="about_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $about = $this->getDoctrine()->getManager()
            ->getRepository('Entity:About')
            ->getAll();

        return [
            'about' => $about,
        ];
    }

    /**
     * Creates a new About entity.
     *
     * @Route("/new",  name="about_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $about = new About();
        $form = $this->createForm('AdminBundle\Form\AboutType', $about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($about);
            $em->flush();

            return $this->redirectToRoute('about_show', array('id' => $about->getId()));
        }

        return [
            'about' => $about,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a About entity.
     *
     * @Route("/{id}", name="about_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(About $about)
    {
        $deleteForm = $this->createDeleteForm($about);

        return [
            'about' => $about,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing About entity.
     *
     * @Route("/{id}/edit", name="about_edit")
     * @Method({"GET",      "POST"})
     * @Template
     */
    public function editAction(Request $request, About $about)
    {
        $deleteForm = $this->createDeleteForm($about);
        $editForm = $this->createForm('AdminBundle\Form\AboutType', $about);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($about);
            $em->flush();

            return $this->redirectToRoute('about_index', array('id' => $about->getId()));
        }

        return [
            'about' => $about,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a About entity.
     *
     * @Route("/{id}",   name="about_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, About $about)
    {
        $form = $this->createDeleteForm($about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($about);
            $em->flush();
        }

        return $this->redirectToRoute('about_index');
    }

    /**
     * Creates a form to delete a About entity.
     *
     * @param About $about The About entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(About $about)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('about_delete', array('id' => $about->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
