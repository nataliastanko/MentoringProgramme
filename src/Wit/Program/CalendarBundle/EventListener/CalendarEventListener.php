<?php

namespace Wit\Program\CalendarBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CalendarEventListener
{
    /** @var EntityManager*/
    private $em;
    /** @var Router*/
    private $router;
    /** @var TokenStorage*/
    private $tokenStorage;

    public function __construct(EntityManager $em, Router $router, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {
        // calendar scope dates, current month by default
        $startDate = $calendarEvent->getStartDateTime();
        $endDate = $calendarEvent->getEndDatetime();

        // The original request so you can get filters from the calendar
        // Use the filter in your query for example

        $request = $calendarEvent->getRequest();
        // this is data injected with js set in calendar-settings.js
        // $filter = $request->get('filter');

        // load events using your custom logic here,
        // for instance, retrieving events from a repository

        // filter with logged user
        $user = $this->tokenStorage->getToken()->getUser();

        $meetingEvents = $this->em
            ->getRepository('WitProgramCalendarBundle:Event')
            ->filterEvents($user, $startDate, $endDate);

        // $meetingEvents = [];

        // $meetingEvents and $meetingEvent in this example
        // represent entities from your database, NOT instances of EventEntity
        // within this bundle.
        //
        // Create EventEntity instances and populate it's properties with data
        // from your own entities/database values.

        foreach ($meetingEvents as $meetingEvent) {

            // create an event with a start/end time, or an all day event
            // if ($meetingEvent->getAllDayEvent() === false) {
            //     $eventEntity = new EventEntity($meetingEvent->getTitle(), $meetingEvent->getStartTime(), $meetingEvent->getEndTime());
            // } else {
            //     $eventEntity->setAllDay(true); // default is false, set to true if this is an all day event
            //     $eventEntity = new EventEntity($meetingEvent->getTitle(), $meetingEvent->getStartTime(), null, true);
            // }

            $eventEntity = new EventEntity(
                $meetingEvent->getTitle(),
                $meetingEvent->getStartTime(),
                $meetingEvent->getEndTime()
            );

            //optional calendar event settings
            $eventEntity->setBgColor('#18bc9c'); //set the background color of the event's label
            $eventEntity->setFgColor('#000'); //set the foreground color of the event's label
            $eventEntity->setUrl(
                $this->router->generate('event_show', ['id' => $meetingEvent->getId()])
            ); // url to send user to when event label is clicked
            // $eventEntity->setCssClass('my-custom-class'); // a custom class you may want to apply to event labels

            //finally, add the event to the CalendarEvent for displaying on the calendar
            $calendarEvent->addEvent($eventEntity);
        }
    }
}
