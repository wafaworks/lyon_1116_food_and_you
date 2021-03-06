<?php

namespace AppBundle\Event;

use AppBundle\Entity\Reservation;
use Symfony\Component\EventDispatcher\Event;

class ReservationConfirmedEvent extends Event
{
    const EVENT_NAME = 'app.event.reservation_confirmed';

    /**
     * @var Reservation
     */
    private $reservation;

    /**
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * @return Reservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }
}
