<?php

namespace AppBundle\Notification\Event;

use AppBundle\Entity\Event;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

final class EventCancelled extends BaseEvent
{
    const EVENT_NAME = 'app.event.event_cancelled';

    /**
     * @var Event
     */
    private $event;

    /**
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
