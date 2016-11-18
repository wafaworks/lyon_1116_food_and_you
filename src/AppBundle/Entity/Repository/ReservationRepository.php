<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Reservation;
use Doctrine\ORM\EntityRepository;
use Soluti\SogenactifBundle\Entity\Transaction;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class ReservationRepository extends EntityRepository
{
    /**
     * Get Member reservation for Event
     *
     * @param Member $member
     * @param Event $event
     *
     * @param array $status
     * @return Reservation|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getReservationForMember(Member $member, Event $event, array $status)
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('r')
            ->where('r.event = :event')
            ->andWhere('r.member = :member')
            ->andWhere('r.status IN (:status)')
            ->setParameter('member', $member)
            ->setParameter('event', $event)
            ->setParameter('status', $status);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Persist reservation to DB
     *
     * @param Reservation $reservation
     */
    public function save(Reservation $reservation)
    {
        $this->getEntityManager()->persist($reservation);
        $this->getEntityManager()->flush();
    }

    /**
     * True if event is full
     *
     * @param Event $event
     * @return bool
     */
    public function eventReachedMaxCapacity(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('SUM(r.places)')
            ->where('r.event = :event')
            ->andWhere('r.status = :status')
            ->setParameter('event', $event)
            ->setParameter('status', Reservation::STATUS_CONFIRMED)
        ;

        $count = $qb->getQuery()->getSingleScalarResult();

        return $event->getCapacity()->getMaximum() <= $count;
    }

    /**
     * True if event has minimum nr of reservations
     *
     * @param Event $event
     * @return bool
     */
    public function eventReachedMinCapacity(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.event = :event')
            ->setParameter('event', $event);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $event->getCapacity()->getMinimum() <= $count;
    }

    public function isFriendRegisteredAtTheSameEventAsUser($user, $friend, $event)
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.event = :event')
            ->andWhere('r.status = :status')
            ->andWhere('r.member IN(:members)')
            ->setParameter('members', array(
                $user,
                $friend,
            ))
            ->setParameter('status', Reservation::STATUS_CONFIRMED)
            ->setParameter('event', $event);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count >= 2;

    }

    /**
     * @param Event $event
     *
     * @param array $status
     * @return array
     */
    public function findAllByEvent(Event $event, array $status = array())
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('r')
            ->innerJoin('r.event', 'e')
            ->leftJoin('r.member', 'm')
            ->leftJoin('r.tableOwner', 't')
            ->where('e = :event')
            ->setParameters(array(
                'event' => $event,
            ))
            ->orderBy('t.tableCode', 'DESC')
            ->addOrderBy('m.firstName', 'ASC')
            ->addOrderBy('m.lastName', 'ASC');

        if (!empty($status)) {
            $qb
                ->andWhere('r.status IN (:status)')
                ->setParameter('status', $status)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function getReservationIdsByEvent(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('r.id')
            ->innerJoin('r.event', 'e')
            ->where('e = :event')
            ->setParameters(array(
                'event' => $event,
            ));

        $orig = $qb->getQuery()->getArrayResult();

        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($orig));

        return iterator_to_array($it, false);
    }

    /**
     * @param Transaction $transaction
     * @return Reservation|null
     */
    public function getByTransaction(Transaction $transaction)
    {
        return $this->findOneBy(['transaction' => $transaction]);
    }

    /**
     * @param Event $event
     *
     * @return integer
     */
    public function getNrPlacesReserved(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('SUM(r.places)')
            ->where('r.event = :event')
            ->andWhere('r.status IN (:status)')
            ->setParameter('event', $event)
            ->setParameter(
                'status',
                [
                    Reservation::STATUS_CONFIRMED,
                ]
            );

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Event|null $event
     *
     * @return integer
     */
    public function getNrReservationsToRefund(Event $event = null)
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.status IN (:status)')
            ->setParameter(
                'status',
                [
                    Reservation::STATUS_TO_REFUND,
                ]
            );

        if ($event) {
            $qb
                ->andWhere('r.event = :event')
                ->setParameter('event', $event)
            ;
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
