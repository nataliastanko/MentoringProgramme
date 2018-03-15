<?php

namespace Wit\Program\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Calendar controller.
 *
 * @Route("/calendar")
 * @Security("is_granted('ROLE_USER')")
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
        $events = $this->getDoctrine()->getManager()
            ->getRepository('WitProgramCalendarBundle:Event')
            ->filterByMonth($this->getUser(), new \DateTime('now'));

        return [
            'events' => $events,
        ];
    }

}
