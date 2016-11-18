<?php

namespace AppBundle\Event\Subscriber;

use AppBundle\Entity\Repository\EventRepository;
use AppBundle\Event\ReservationCancelledEvent;
use AppBundle\Event\ReservationConfirmedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReservationSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ReservationCancelledEvent::EVENT_NAME => 'onReservationCancelled',
            ReservationConfirmedEvent::EVENT_NAME => 'onReservationConfirmed',
        );
    }

    /**
     * @param ReservationCancelledEvent $reservationCancelledEvent
     */
    public function onReservationCancelled(ReservationCancelledEvent $reservationCancelledEvent)
    {
        $reservation = $reservationCancelledEvent->getReservation();

        // decrease event places
        $event = $reservation->getEvent();
        $event->setConfirmedReservations(
            $event->getConfirmedReservations() - $reservation->getPlaces()
        );
        $this->eventRepository->save($event);
    }

    /**
     * @param ReservationConfirmedEvent $reservationConfirmedEvent
     */
    public function onReservationConfirmed(ReservationConfirmedEvent $reservationConfirmedEvent)
    {
        $reservation = $reservationConfirmedEvent->getReservation();

        // increase event places
        if (!$reservationConfirmedEvent->getReservation()->isConfirmationSent()) {
            $event = $reservation->getEvent();
            $event->setConfirmedReservations(
                $event->getConfirmedReservations() + $reservation->getPlaces()
            );
            $this->eventRepository->save($event);
        }
    }
}
