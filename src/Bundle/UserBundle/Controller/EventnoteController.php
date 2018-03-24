<?php

namespace UserBundle\Controller;

use Entity\EventNote;
use Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Security\Annotation\SectionEnabled;

/**
 * Eventnote controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("eventnote")
 * @Security("is_granted('ROLE_USER')")
 * @SectionEnabled(name="calendar")
 *
 * @TODO only mentor and mentee, not mentor! ROLE_USER is a mentor too
 */
class EventnoteController extends Controller
{
    /**
     * Creates a new eventNote entity.
     *
     * @Route("/{id}/new", name="eventnote_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Event $event, Request $request)
    {
        $events = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Event')
            ->filterEvents($this->getUser());

        if (!$events->contains($event) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        $eventNote = new EventNote();
        $form = $this->createForm(
            'UserBundle\Form\EventNoteType',
            $eventNote,
            [
                'action' => $this->generateUrl('eventnote_new', ['id' => $event->getId()]),
                'method' => 'POST',
            ]
        );

        $eventNote->setAuthor($this->getUser()); // before handleRequest to pass validation
        $eventNote->setEvent($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventNote);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $eventNote->getEvent()->getId()));
        }

        return [
            'eventNote' => $eventNote,
            'form' => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing eventNote entity.
     *
     * @Route("/{id}/edit", name="eventnote_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, EventNote $eventNote)
    {
        if (!$this->getUser()->getEventsNotes()->contains($eventNote) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        $deleteForm = $this->createDeleteForm($eventNote);
        $editForm = $this->createForm('UserBundle\Form\EventNoteType', $eventNote);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_show', array('id' => $eventNote->getEvent()->getId()));
        }

        return [
            'delete_form' => $deleteForm->createView(),
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a eventNote entity.
     *
     * @Route("/{id}", name="eventnote_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EventNote $eventNote)
    {
        if (!$this->getUser()->getEventsNotes()->contains($eventNote) ) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        $form = $this->createDeleteForm($eventNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($eventNote);
            $em->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a eventNote entity.
     *
     * @param EventNote $eventNote The eventNote entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EventNote $eventNote)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eventnote_delete', array('id' => $eventNote->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
