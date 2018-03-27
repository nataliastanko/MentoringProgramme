<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AdminBundle\Security\Annotation\SectionEnabled;

/**
 * Calendar controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/calendar")
 * @Security("is_granted('ROLE_USER')")
 * @SectionEnabled(name="calendar")
 */
class CalendarController extends Controller
{
    /**
     * List only events I participate in or I created (?)
     *
     * @Route("/", name="calendar_index", options={"expose"=true})
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        // calendar events
        $events = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Event')
            ->filterByMonth($this->getUser(), new \DateTime('now'));

        // all events in a list
        /** @todo pagination and filter by edition */
        $eventsList = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Event')
            ->filterEvents($this->getUser());

        return [
            'events' => $events,
            'eventsList' => $eventsList,
        ];
    }

}
