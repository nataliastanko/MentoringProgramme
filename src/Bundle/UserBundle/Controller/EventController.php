<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Entity\Event;
use Entity\EventNote;
use Annotation\Controller\SectionEnabled;

/**
 * Event controller for mentors and mentees.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @TODO check permissions to show/edit/delete event
 *
 * @Route("/event")
 * @Security("is_granted('ROLE_USER')")
 * @SectionEnabled(name="calendar")
 */
class EventController extends Controller
{
    /**
     * Lists Event entities.
     *
     * @Route("/", name="event_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        return $this->forward('UserBundle:calendar:index', []);
    }

    /**
     * Creates a new Event entity.
     *
     * @Route("/new", name="event_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // last edition
        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $event = new Event();
        $form = $this->createForm(
            'UserBundle\Form\EventType',
            $event,
            [
                'ac' => $this->get('security.authorization_checker'),
                'editionId' => $edition->getId(),
                'user' => $this->getUser()
            ]
        );
        $event->setAuthor($this->getUser()); // before handleRequest to pass validation
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return [
            'event' => $event,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Event $event)
    {
        $events = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Event')
            ->filterEvents($this->getUser());

        if (!$events->contains($event) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        $deleteForm = $this->createDeleteForm($event);

        // event notes
        $eventNote = new EventNote();
        $form = $this->createForm(
            'UserBundle\Form\EventNoteType',
            $eventNote,
            [
                'action' => $this->generateUrl('eventnote_new', ['id' => $event->getId()]),
                'method' => 'POST',
            ]
        );

        return [
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            'form' => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Method({"GET", "POST"})
     * @Template("UserBundle:event:edit.html.twig")
     */
    public function editAction(Request $request, Event $event)
    {
        if (!$this->getUser()->getEventsCreated()->contains($event) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        $em = $this->getDoctrine()->getManager();

        // last edition
        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $deleteForm = $this->createDeleteForm($event);

        $editForm = $this->createForm(
            'UserBundle\Form\EventType',
            $event,
            [
                'ac' => $this->get('security.authorization_checker'),
                'editionId' => $edition->getId(),
                'user' => $this->getUser()
            ]
        );
        $event->setAuthor($this->getUser()); // before handleRequest to pass validation
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return [
            'event' => $event,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}", name="event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Event $event)
    {
        if (!$this->getUser()->getEventsCreated()->contains($event) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        // do not remove if has notes (autored by participants)
        if (count($event->getNotes())) {
            $request->getSession()->getFlashBag()
                ->add('warning', 'You can not delete this event');
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a Event entity.
     *
     * @param Event $event The Event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
