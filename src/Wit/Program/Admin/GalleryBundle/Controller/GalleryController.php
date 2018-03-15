<?php

namespace Wit\Program\Admin\GalleryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wit\Program\Admin\GalleryBundle\Entity\Image;

/**
 * Gallery controller.
 *
 * @Route("/gallery")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class GalleryController extends Controller
{
    /**
     * Set question sortable position.
     *
     * @Route("/sort", name="gallery_sort")
     * @Method("POST")
     * @Template("WitProgramAdminGalleryBundle:Image:_sort.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('WitProgramAdminGalleryBundle:Image');

        $imageId = $request->request->get('id');
        $position = $request->request->get('position');

        $image = $repo->findOneById($imageId);
        $image->setPosition($position);
        $em->persist($image);
        $em->flush();

        $images = $repo->getAll();

        return [
            'images' => $images,
        ];
    }

    /**
     * Lists all Gallery entities.
     *
     * @Route("/", name="gallery_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('WitProgramAdminGalleryBundle:Image');
        $images = $repo->getAll();

        return [
            'images' => $images,
        ];
    }

    /**
     * Creates a new Image entity.
     *
     * @Route("/new", name="gallery_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $image = new Image();
        $form = $this->createForm('Wit\Program\Admin\GalleryBundle\Form\ImageType', $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('gallery_show', array('id' => $image->getId()));
        }

        return [
            'image' => $image,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Image entity.
     *
     * @Route("/{id}", name="gallery_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Image $image)
    {
        $deleteForm = $this->createDeleteForm($image);

        return [
            'image' => $image,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Image entity.
     *
     * @Route("/{id}/edit", name="gallery_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Image $image)
    {
        $deleteForm = $this->createDeleteForm($image);
        $editForm = $this->createForm('Wit\Program\Admin\GalleryBundle\Form\ImageType', $image);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('gallery_index', array('id' => $image->getId()));
        }

        return [
            'image' => $image,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Image entity.
     *
     * @Route("/{id}", name="gallery_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Image $image)
    {
        $form = $this->createDeleteForm($image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }

        return $this->redirectToRoute('gallery_index');
    }

    /**
     * Creates a form to delete a Image entity.
     *
     * @param Image $image The Image entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Image $image)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gallery_delete', array('id' => $image->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
