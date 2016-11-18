<?php

namespace AppBundle\Notification\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

final class ReservationsToRefundEvent extends BaseEvent
{
    const EVENT_NAME = 'app.event.reservations_to_refund';

    /**
     * @var
     */
    private $count;

    /**
     * @param integer $count
     */
    public function __construct($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }
}
