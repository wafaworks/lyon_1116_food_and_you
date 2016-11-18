<?php

namespace AppBundle\Notification\Event;

use AppBundle\Entity\Event;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

final class MinCapacityNotReachedEvent extends BaseEvent
{
    const EVENT_NAME = 'app.event.min_capacity_not_reached';

    /**
     * @var Event
     */
    private $event;

    /**
     * MinCapacityNotReachedEvent constructor.
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
