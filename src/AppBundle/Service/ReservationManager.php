<?php

namespace AppBundle\Service;

use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Repository\ReservationRepository;
use AppBundle\Entity\Reservation;
use AppBundle\Event\ReservationCancelledEvent;
use AppBundle\Event\ReservationConfirmedEvent;
use AppBundle\Exception\ReservationManagerException;
use Soluti\SogenactifBundle\Entity\Transaction;
use Soluti\SogenactifBundle\Service\TransactionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ReservationManager
{
    /**
     * @var ReservationRepository
     */
    private $reservationRepository;

    /**
     * @var TableTokenManager
     */
    private $tableTokenManager;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ReservationRepository $repository
     * @param TableTokenManager $tableTokenManager
     * @param TransactionManager $transactionManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ReservationRepository $repository,
        TableTokenManager $tableTokenManager,
        TransactionManager $transactionManager,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->reservationRepository = $repository;
        $this->tableTokenManager = $tableTokenManager;
        $this->transactionManager = $transactionManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Create a reservation for current user for given event
     *
     * @param Member $member
     * @param Event $event
     * @param null $tableOwnerCode
     *
     * @param int $places
     * @return string
     * @throws ReservationManagerException
     * @throws \Soluti\SogenactifBundle\Exception\PaymentException
     */
    public function createReservation(Member $member, Event $event, $tableOwnerCode = null, $places = 1)
    {
        if ($this->memberHasReservationForEvent($member, $event)) {
            throw new ReservationManagerException('already_reserved');
        }

        if ($this->reservationRepository->eventReachedMaxCapacity($event)) {
            throw new ReservationManagerException('event_full');
        }

        $tableMember = $this->getTableMember($member, $event, $tableOwnerCode);
        $transaction = $this->transactionManager->create(
            $this->getPrice($event, $places)
        );
        $reservation = $this->generateReservation($member, $event, $tableMember, $transaction, $places);
        $this->reservationRepository->save($reservation);

        return $this->transactionManager->generateCode($transaction);
    }

    /**
     * Cancel a given reservation
     *
     * @param Reservation $reservation
     *
     * @return Reservation
     *
     * @throws \Exception
     */
    public function cancelReservation(Reservation $reservation)
    {
        if ($reservation->getStatus() == Reservation::STATUS_CONFIRMED) {
            $this->dispatcher->dispatch(
                ReservationCancelledEvent::EVENT_NAME,
                new ReservationCancelledEvent($reservation)
            );

            return $this->toRefundReservation($reservation);
        }

        $reservation->setStatus(Reservation::STATUS_CANCELLED);
        $this->reservationRepository->save($reservation);

        return $reservation;
    }

    /**
     * @param Reservation $reservation
     *
     * @return Reservation
     *
     * @throws \Exception
     */
    public function confirmReservation(Reservation $reservation)
    {
        $reservation->setStatus(Reservation::STATUS_CONFIRMED);

        $this->dispatcher->dispatch(
            ReservationConfirmedEvent::EVENT_NAME,
            new ReservationConfirmedEvent($reservation)
        );

        $reservation->setConfirmationSent(true);
        $this->reservationRepository->save($reservation);

        return $reservation;
    }

    /**
     * @param Reservation $reservation
     *
     * @return Reservation
     */
    public function toRefundReservation(Reservation $reservation)
    {
        $reservation->setStatus(Reservation::STATUS_TO_REFUND);
        $this->reservationRepository->save($reservation);

        return $reservation;
    }

    /**
     * Check if current user has reservation for event
     *
     * @param Member $member
     * @param Event $event
     * @return bool
     */
    public function memberHasReservationForEvent(Member $member, Event $event)
    {
        $reservation = $this
            ->reservationRepository
            ->getReservationForMember($member, $event, [Reservation::STATUS_CONFIRMED]);

        return $reservation instanceof Reservation;
    }

    /**
     * Get the member at which table the reservation will be done
     *
     * @param Member $member
     * @param Event $event
     * @param $tableOwnerCode
     * @return Member|null
     * @throws ReservationManagerException
     */
    private function getTableMember(Member $member, Event $event, $tableOwnerCode)
    {
        $tableMember = $member;

        if ($tableOwnerCode) {
            $tableMember = $this
                ->tableTokenManager
                ->findMemberByToken($tableOwnerCode);

            if (!$tableMember) {
                throw new ReservationManagerException('wrong_table_code');
            }

            if (!$this->memberHasReservationForEvent($tableMember, $event)) {
                throw new ReservationManagerException('no_reservation');
            }
        }

        return $tableMember;
    }

    /**
     * @param Member $member
     * @param Event $event
     * @param Member $tableOwner
     * @param Transaction $transaction
     * @param $places
     *
     * @return Reservation
     */
    private function generateReservation(Member $member, Event $event, Member $tableOwner, Transaction $transaction, $places)
    {
        $reservation = new Reservation();
        $reservation->setMember($member);
        $reservation->setEvent($event);
        $reservation->setTableOwner($tableOwner);
        $reservation->setTransaction($transaction);
        $reservation->setPlaces($places);

        return $reservation;
    }

    /**
     * @param Event $event
     * @param int $places
     *
     * @return float
     */
    private function getPrice(Event $event, $places)
    {
        return $event->getPrice() * (int) $places;
    }


}
