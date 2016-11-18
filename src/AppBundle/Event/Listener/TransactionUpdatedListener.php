<?php

namespace AppBundle\Event\Listener;

use AppBundle\Entity\Repository\ReservationRepository;
use AppBundle\Entity\Reservation;
use AppBundle\Service\ReservationManager;
use Soluti\SogenactifBundle\Entity\Transaction;
use Soluti\SogenactifBundle\Event\TransactionUpdatedEvent;
use Soluti\SogenactifBundle\Model\TransactionStatus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class TransactionUpdatedListener
{
    /**
     * @var ReservationRepository
     */
    private $reservationRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    /**
     * @param ReservationRepository $reservationRepository
     * @param RouterInterface $router
     * @param ReservationManager $reservationManager
     */
    public function __construct(
        ReservationRepository $reservationRepository,
        RouterInterface $router,
        ReservationManager $reservationManager
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->router = $router;
        $this->reservationManager = $reservationManager;
    }

    /**
     * @param TransactionUpdatedEvent $transactionUpdatedEvent
     * @throws \Exception
     */
    public function onTransactionUpdate(TransactionUpdatedEvent $transactionUpdatedEvent)
    {
        $transaction = $transactionUpdatedEvent->getTransaction();
        $reservation = $this->reservationRepository->getByTransaction($transaction);

        if (!$reservation) {
            throw new \Exception('Reservation not found for transaction: ' . print_r($transaction));
        }

        $reservation = $this->updateReservationStatus($transaction, $reservation);

        $this->prepareResponse($transactionUpdatedEvent, $reservation);
    }

    /**
     * @param Transaction $transaction
     * @param Reservation $reservation
     *
     * @throws \Exception
     *
     * @return Reservation
     */
    private function updateReservationStatus(Transaction $transaction, Reservation $reservation)
    {
        if ($reservation->getStatus() === Reservation::STATUS_DRAFT &&
            $transaction->getResponseCode() === TransactionStatus::STATUS_ACCEPTED
        ) {
            $this->reservationManager->confirmReservation($reservation);
        }

        if ($reservation->getStatus() === Reservation::STATUS_CANCELLED &&
            $transaction->getResponseCode() === TransactionStatus::STATUS_ACCEPTED
        ) {
            if ($this->eventHasCapacity($reservation)) {
                $this->reservationManager->confirmReservation($reservation);
            } else {
                $this->reservationManager->toRefundReservation($reservation);
            }
        }

        if ($reservation->getStatus() === Reservation::STATUS_DRAFT &&
            $transaction->getResponseCode() !== TransactionStatus::STATUS_ACCEPTED
        ) {
            $this->reservationManager->cancelReservation($reservation);
        }

        return $reservation;
    }

    /**
     * @param Reservation $reservation
     * @return bool
     */
    private function eventHasCapacity(Reservation $reservation)
    {
        $nrPlacesReserved = $this->reservationRepository->getNrPlacesReserved(
            $reservation->getEvent()
        );
        $eventMaxCapacity = $reservation->getEvent()->getCapacity()->getMaximum();

        return $nrPlacesReserved + $reservation->getPlaces() <= $eventMaxCapacity;
    }

    /**
     * Prepare response on transaction update
     *
     * @param TransactionUpdatedEvent $event
     * @param Reservation $reservation
     */
    private function prepareResponse(TransactionUpdatedEvent $event, Reservation $reservation)
    {
         if ($reservation->getStatus() === Reservation::STATUS_CONFIRMED) {
             $url = $this->router->generate(
                 'member_profile',
                 [
                     'slug' => $reservation->getMember()->getSlug(),
                     'modal' => 'reservation-confirmed',
                     'reservationId' => $reservation->getId(),
                 ]
             );
         } else {
             $url = $this->router->generate(
                 'event_details',
                 [
                     'id' => $reservation->getEvent()->getId(),
                     'modal' => 'reservation-cancelled',
                 ]
             );
         }

        $event->setResponse(new RedirectResponse($url));
    }
}
