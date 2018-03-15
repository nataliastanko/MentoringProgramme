<?php

namespace Wit\Program\Admin\CalendarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wit\Program\CalendarBundle\Entity\Event;

/**
 * Event controller.
 *
 * @TODO check permissions to show/edit/delete event
 *
 * @Route("/event")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class EventController extends Controller
{
    /**
     * Lists Event entities.
     *
     * @Route(
     *     "/list/{page}",
     *     name="admin_event_index",
     *     requirements={"page" = "\d+"},
     *     defaults={"page" = 1}
     * )
     * @Method("GET")
     * @Template
     */
    public function indexAction($page, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT e FROM WitProgramCalendarBundle:Event e order by e.startTime desc');

        $events = $this->get('knp_paginator')->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', $page) /*page number*/,
            $this->get('service_container')->getParameter('paginate.limit') /*limit per page*/
        );

        return [
            'events' => $events,
        ];
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="admin_event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Event $event)
    {
        return [
            'event' => $event,
        ];
    }

}
