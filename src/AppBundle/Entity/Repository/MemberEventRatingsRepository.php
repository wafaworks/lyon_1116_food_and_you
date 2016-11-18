<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\MemberEventMedia;
use AppBundle\Entity\MemberEventRatings;
use Doctrine\ORM\EntityRepository;

class MemberEventRatingsRepository extends EntityRepository
{
    /**
     * @param Member $member
     * @param Event $event
     * @param $rating
     *
     * @param $type
     * @param null $memberEventRating
     * @return MemberEventRatings
     */
    public function save(Member $member, Event $event, $rating, $type, $memberEventRating = null)
    {
        $em = $this->getEntityManager();

        if (!$memberEventRating) {
            $memberEventRating = new MemberEventRatings();
            $memberEventRating->setMember($member);
            $memberEventRating->setEvent($event);
        }


        if ($type == MemberEventMedia::TYPE_RESTAURANT) {
            $memberEventRating->setRestaurantRating($rating);
        }

        if ($type == MemberEventMedia::TYPE_EVENT) {
            $memberEventRating->setEventRating($rating);

        }

        $memberEventRating->setProcessed(false);
        $em->persist($memberEventRating);
        $em->flush();

        return $memberEventRating;
    }

    /**
     * @param $eventId
     */
    public function updateRating($eventId)
    {
        $qbRestaurant = $this
            ->createQueryBuilder('mr')
            ->select('AVG(mr.restaurantRating) as restaurantRating')
            ->innerJoin('mr.event', 'e')
            ->where('e.id = :eventId')
            ->andWhere('mr.restaurantRating > 0')
            ->setParameter('eventId', $eventId);
        $restaurantRating = $qbRestaurant->getQuery()->getSingleScalarResult();

        $qbEvent = $this
            ->createQueryBuilder('mr')
            ->select('AVG(mr.eventRating) as eventRating')
            ->innerJoin('mr.event', 'e')
            ->where('e.id = :eventId')
            ->andWhere('mr.eventRating > 0')
            ->setParameter('eventId', $eventId);
        $eventRating = $qbEvent->getQuery()->getSingleScalarResult();

        $query = $this->getEntityManager()->createQuery("
              UPDATE AppBundle:Event e
              SET e.eveningRating = :eveningRating, e.restaurantRating = :restaurantRating
              WHERE e.id = :id
            ");

        $query->execute(array(
            'id'     => $eventId,
            'eveningRating' => $eventRating,
            'restaurantRating' => $restaurantRating,
        ));

        $query2 = $this->getEntityManager()->createQuery("
              UPDATE AppBundle:MemberEventRatings mr
              SET mr.processed = TRUE
              WHERE mr.event = :id
            ");

        $query2->execute(array(
            'id' => $eventId,
        ));
    }
}
