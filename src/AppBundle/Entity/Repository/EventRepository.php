<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Recipe;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Restaurant;
use AppBundle\Filter\FilterInterface;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class EventRepository extends EntityRepository
{
    const UPCOMING_EVENTS_LIMIT = 3;
    const UPCOMING_EVENTS_OFFSET = 0;

    const EVENTS_PER_PAGE = 12;

    /**
     * Get upcoming events
     *
     *
     * @param Restaurant $restaurant
     * @return array
     */
    public function getUpcomingEvents(Restaurant $restaurant = null)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e,r')
            ->leftJoin('e.restaurant', 'r')
            ->where('e.status IN (:statuses)')
            ->andWhere('e.startDate > :now')
            ->andWhere('r.status = :rStatus')
            ->setParameter('now', new DateTime())
            ->orderBy('e.startDate', 'ASC')
            ->setParameter('rStatus', Restaurant::STATUS_VALIDATED)
            ->setFirstResult(self::UPCOMING_EVENTS_OFFSET)
            ->setMaxResults(self::UPCOMING_EVENTS_LIMIT);

        if ($restaurant instanceof Restaurant) {
            $query
                ->addSelect('a, are, re')
                ->innerJoin('e.applicants', 'a')
                ->innerJoin('a.recipes', 'are')
                ->innerJoin('are.recipe', 're')
                ->andWhere('a.status = :applicantStatus')
                ->andWhere('are.selected = true')
                ->andWhere('r = :restaurant')
                ->addOrderBy('re.type', 'ASC')
                ->setParameter('applicantStatus', Applicant::STATUS_ACCEPTED)
                ->setParameter('restaurant', $restaurant)
                ->setParameter(
                    'statuses',
                    array(
                        Event::STATUS_RESERVATIONS_OPENED,
                    )
                );
        } else {
            $query
                ->setParameter(
                    'statuses',
                    array(
                        Event::STATUS_APPLICANT_REGISTRATION_OPENED,
                        Event::STATUS_RESERVATIONS_OPENED,
                    )
                );
        }

        return $query->getQuery()->getResult();
    }

    public function getOpenedEventsByMember(Member $member)
    {
        $qb = $this->createQueryBuilder('e');
        $query = $qb
            ->select('e, a, ar, r, m')
            ->innerJoin('e.applicants', 'a')
            ->innerJoin('a.member', 'm')
            ->innerJoin('a.recipes', 'ar')
            ->innerJoin('ar.recipe', 'r')
            ->where('m = :member')
            ->andWhere('e.status IN (:events)')
            ->setParameters(array(
                'member' => $member,
                'events' => array(
                    Event::STATUS_APPLICANT_REGISTRATION_OPENED,
                    Event::STATUS_APPLICANT_REGISTRATION_CLOSED,
                ),
            ))
            ->orderBy('e.startDate')
            ->getQuery();

        return $query->getResult();

    }

    /**
     * @return array
     */
    public function getFinishedEvents()
    {
        $query = $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.status =:status')
            ->setParameter('status', Event::STATUS_FINISHED)
            ->getQuery();

        return $query->getResult();
    }

    public function getFilteredEvents(FilterInterface $filter)
    {
        $queryBuilder = $this->createQueryBuilder('event')
            ->select('event, restaurant')
            ->leftJoin('event.restaurant', 'restaurant')
            ->where('1 = 1')
            ->andWhere('restaurant.status = :rStatus')
            ->setParameter('rStatus', Restaurant::STATUS_VALIDATED)
            ->orderBy('event.startDate', 'ASC');

        $this
            ->filterByRestaurantName($queryBuilder, $filter)
            ->filterByCity($queryBuilder, $filter)
            ->filterByDate($queryBuilder, $filter)
            ->filterByType($queryBuilder, $filter)
            ->filterPaginate($queryBuilder, $filter);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getEventsWhereMemberCooked(Member $member)
    {
        $qb = $this->createQueryBuilder('e');

        $query = $qb->select('e', 'r', 'a', 'ar')
            ->innerJoin('e.applicants', 'a')
            ->innerJoin('a.recipes', 'ar')
            ->leftJoin('e.restaurant', 'r')
            ->innerJoin('a.member', 'm')
            ->where('m = :member')
            ->andWhere('e.status = :status_finished')
            ->andWhere('ar.selected = TRUE')
            ->setParameters(array(
                'member' => $member,
                'status_finished' => Event::STATUS_FINISHED,
            ))
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface $filters
     *
     * @return EventRepository
     */
    protected function filterByRestaurantName(QueryBuilder $queryBuilder, FilterInterface $filters)
    {
        if (!$filters->isFilterSet('restaurant')) {
            return $this;
        }

        $queryBuilder
            ->andWhere('restaurant.name = :restaurantName')
            ->setParameter('restaurantName', $filters->getFilter('restaurant'));

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface $filters
     *
     * @return EventRepository
     */
    protected function filterByCity(QueryBuilder $queryBuilder, FilterInterface $filters)
    {
        if (!$filters->isFilterSet('city')) {
            return $this;
        }

        if (!in_array('city', $queryBuilder->getAllAliases())) {
            $queryBuilder->join('restaurant.city', 'city');
        }

        $queryBuilder
            ->andWhere('city.id = :city')
            ->setParameter('city', $filters->getFilter('city'));

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface $filters
     * @return $this
     */
    protected function filterByDate(QueryBuilder $queryBuilder, FilterInterface $filters)
    {
        $now = new DateTime();
        $now->setTime(0, 0, 0);

        if (!$filters->isFilterSet('eventDate')) {
            $queryBuilder
                ->andWhere('event.startDate > :now')
                ->setParameter('now', $now);

            return $this;
        }

        $eventDate = $filters->getFilter('eventDate');
        $startDate = DateTime::createFromFormat('Y-m-d', $eventDate);
        if ($startDate instanceof DateTime) {
            $startDate->setTime(0, 0, 0);
        }
        if (!$startDate || $startDate < $now) {
            $startDate = $now;
        }

        $queryBuilder
            ->andWhere('event.startDate > :startDate')
            ->setParameter('startDate', $startDate);

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface $filters
     * @return $this
     */
    private function filterByType(QueryBuilder $queryBuilder, FilterInterface $filters)
    {
        $status = $filters->getFilter('participatorType');
        if (!in_array(
            $status,
            array(
                Event::STATUS_APPLICANT_REGISTRATION_OPENED,
                Event::STATUS_RESERVATIONS_OPENED,
            )
        )
        ) {
            $eventStatuses = Event::getStatuses();
            unset($eventStatuses[Event::STATUS_FINISHED]);
            unset($eventStatuses[Event::STATUS_CANCELLED]);

            $queryBuilder
                ->andWhere('event.status IN(:eventStatuses)')
                ->setParameter('eventStatuses', $eventStatuses);

            return $this;
        }

        $queryBuilder
            ->andWhere('event.status = :status')
            ->setParameter('status', $status);

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface $filters
     */
    private function filterPaginate(QueryBuilder $queryBuilder, FilterInterface $filters)
    {
        $page = $filters->getFilter('page');
        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * self::EVENTS_PER_PAGE;

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults(self::EVENTS_PER_PAGE);
    }

    /**
     * List of events that did not start nor finished, and a user have a reservation for
     *
     * @param Member $member
     * @return \AppBundle\Entity\Event[]
     */
    public function getFutureEventsReservationBy(Member $member)
    {
        $eventStatus = array(
            Event::STATUS_RESERVATIONS_OPENED,
            Event::STATUS_RESERVATIONS_CLOSED,
        );

        return $this->getEventsReservationBy($member, $eventStatus);
    }

    /**
     * List of events that did not finish yet, and a user have a reservation for
     *
     * @param Member $member
     * @return \AppBundle\Entity\Event[]
     */
    public function getNotFinishedEventsReservationBy(Member $member)
    {
        $eventStatus = array(
            Event::STATUS_RESERVATIONS_OPENED,
            Event::STATUS_RESERVATIONS_CLOSED,
            Event::STATUS_IN_PROGRESS,
        );

        return $this->getEventsReservationBy($member, $eventStatus);
    }

    /**
     * List of events that finished, and a user had a reservation for
     *
     * @param Member $member
     * @return \AppBundle\Entity\Event[]
     */
    public function getPastEventsReservationBy(Member $member)
    {
        $eventStatus = array(
            Event::STATUS_FINISHED,
        );

        return $this->getEventsReservationBy($member, $eventStatus);
    }

    /**
     * List of events a user has reservation for, that have the following status
     * @param Member $member
     * @param array $eventStatus
     * @return \AppBundle\Entity\Event[]
     */
    protected function getEventsReservationBy(Member $member, array $eventStatus)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('e, rs, a, ar, re, p, g, gms, gm, r, to')
            ->innerJoin('e.restaurant', 'rs')
            ->innerJoin('e.reservations', 'r')
            ->innerJoin('r.member', 'm')
            ->innerJoin('e.applicants', 'a')
            ->innerJoin('a.recipes', 'ar')
            ->innerJoin('ar.recipe', 're')
            ->leftJoin('re.photo', 'p')
            ->leftJoin('rs.gallery', 'g')
            ->leftJoin('g.galleryHasMedias', 'gms')
            ->leftJoin('gms.media', 'gm')
            ->leftJoin('r.tableOwner', 'to')
            ->where('m = :member')
            ->andWhere('e.status IN (:eventStats)')
            ->andWhere('a.status = :applicantStatus')
            ->andWhere('ar.selected = true')
            ->andWhere('r.status = :reservationStatus')
            ->setParameter('member', $member)
            ->setParameter('eventStats', $eventStatus)
            ->setParameter('applicantStatus', Applicant::STATUS_ACCEPTED)
            ->setParameter('reservationStatus', Reservation::STATUS_CONFIRMED)
            ->orderBy('e.startDate', 'ASC')
            ->addOrderBy('re.type', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get event with winning dish if any
     * @param $id
     * @return \AppBundle\Entity\Event[]
     */
    public function getEvent($id)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('e, rs, g, gms, gm')
            ->innerJoin('e.restaurant', 'rs')
            ->leftJoin('rs.gallery', 'g')
            ->leftJoin('g.galleryHasMedias', 'gms')
            ->leftJoin('gms.media', 'gm')
            ->where('e.id = :eventId')
            ->setParameter('eventId', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get the events that we need to process with CRON (close applicant registration)
     */
    public function getEventsToCloseApplicantRegistration()
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->where('e.status = :status')
            ->andWhere('e.applicationEndDate < :datetime')
            ->setParameter('status', Event::STATUS_APPLICANT_REGISTRATION_OPENED)
            ->setParameter('datetime', new DateTime());

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the events that we need to process with CRON (open reservations)
     */
    public function getEventsToOpenReservations()
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->where('e.status = :status')
            ->andWhere('e.applicationEndDate < :datetime')
            ->setParameter('status', Event::STATUS_APPLICANT_REGISTRATION_CLOSED)
            ->setParameter('datetime', new DateTime('-3 days'));

        return $qb->getQuery()->getResult();
    }

    public function getEventsToCloseReservations()
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->where('e.status = :status')
            ->andWhere('e.startDate < :datetime')
            ->setParameter('status', Event::STATUS_RESERVATIONS_OPENED)
            ->setParameter('datetime', new DateTime('+1 hour'));

        return $qb->getQuery()->getResult();
    }

    /**
     * Check if event has 1 entry, main and dessert applied
     * @param Event $event
     * @return bool
     */
    public function hasOneApplicantRecipeOfEach(Event $event)
    {
        return ($this->hasOneApplicantRecipeOfType($event, Recipe::TYPE_ENTRY) &&
            $this->hasOneApplicantRecipeOfType($event, Recipe::TYPE_MAIN) &&
            $this->hasOneApplicantRecipeOfType($event, Recipe::TYPE_DESSERT));
    }

    /**
     * @param Event $event
     * @param $type
     * @return bool
     */
    private function hasOneApplicantRecipeOfType(Event $event, $type)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('COUNT(re)')
            ->innerJoin('e.applicants', 'a')
            ->innerJoin('a.recipes', 'r')
            ->innerJoin('r.recipe', 're')
            ->where('e = :event')
            ->andWhere('re.type = :type')
            ->setParameter('event', $event)
            ->setParameter('type', $type);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if event has 3 types of dishes selected
     *
     * @param $event
     * @return bool
     */
    public function hasSelectedRecipes(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('COUNT(DISTINCT re.type)')
            ->innerJoin('e.applicants', 'a')
            ->innerJoin('a.recipes', 'r')
            ->innerJoin('r.recipe', 're')
            ->where('e = :event')
            ->andWhere('a.status = :applicantStatus')
            ->andWhere('r.selected = true')
            ->setParameter('applicantStatus', Applicant::STATUS_ACCEPTED)
            ->setParameter('event', $event);

        return $qb->getQuery()->getSingleScalarResult() == 3;
    }

    /**
     * Check if minimum nr of reservations were made
     *
     * @param Event $event
     * @return bool
     */
    public function hasEnoughReservations(Event $event)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('COUNT(re)')
            ->innerJoin('e.reservations', 're')
            ->where('e = :event')
            ->setParameter('event', $event);

        $result = $qb->getQuery()->getSingleScalarResult();
        $minimumCapacity = $event->getCapacity()->getMinimum();

        return $result > $minimumCapacity;
    }

    /**
     * @param Member $member
     * @param Request $request
     * @return \Doctrine\ORM\Query
     */
    public function getRestaurantOwnerEventsQuery(Member $member, Request $request)
    {
        $direction = "DESC";

        if ($request->query->get('direction')) {
            $direction = $request->query->get('direction');
        }

        $qb = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->innerJoin('e.restaurant', 'r')
            ->leftJoin('e.reservations', 'rs')
            ->leftJoin('e.applicants', 'a')
            ->where('r.owner = :member')
            ->orderBy('e.startDate', $direction)
            ->setParameter('member', $member);

        $result = $qb->getQuery();

        return $result;
    }

    /**
     * @param Event $event
     * @param Member $member
     * @return mixed
     */
    public function isEventCreatedByMember(Event $event, Member $member)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->innerJoin('e.restaurant', 'r')
            ->where('r.owner = :member')
            ->andWhere('e = :event')
            ->setParameter('member', $member)
            ->setParameter('event', $event);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Event $event
     * @param Request $request
     */
    public function editCapacity(Event $event, Request $request)
    {
        $max = $request->request->get('max');

        if ($max) {
            $event->getCapacity()->setMaximum($max);
        }

        $min = $request->request->get('min');

        if ($min) {
            $event->getCapacity()->setMinimum($min);
        }

        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Event $event
     */
    public function closeEvent(Event $event)
    {
        $event->setStatus(Event::STATUS_FINISHED);
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function save(Event $event)
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array
     */
    public function getUnaccountedEventIds()
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('e.id')
            ->from('AppBundle:MemberEventRatings', 'mr')
            ->innerJoin('mr.event', 'e')
            ->where('mr.processed = false')
            ->groupBy('e');

        return array_map(
            function ($element) {
                return $element['id'];
            },
            $qb->getQuery()->getArrayResult()
        );
    }

    public function getEventsWhereMemberWillCook(Member $member)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('e.id')
            ->innerJoin('e.applicants', 'a')
            ->innerJoin('a.recipes', 'ar')
            ->innerJoin('e.restaurant', 'r')
            ->leftJoin('a.member', 'm')
            ->where('m = :member')
            ->andWhere('e.status IN(:statuses)')
            ->andWhere('a.status = :selected')
            ->andWhere('ar.selected = TRUE')
            ->setParameters(array(
                'member' => $member,
                'statuses' => array(
                    Event::STATUS_RESERVATIONS_OPENED,
                    Event::STATUS_RESERVATIONS_CLOSED
                ),
                'selected' => Applicant::STATUS_ACCEPTED
            ));

        $events = array_map(
            function ($element) {
                return $element['id'];
            },
            $qb->getQuery()->getArrayResult()
        );

        $query = $this
            ->createQueryBuilder('e')
            ->select('e, a, ar, e')
            ->innerJoin('e.applicants', 'a')
            ->innerJoin('a.recipes', 'ar')
            ->innerJoin('ar.recipe', 'arr')
            ->innerJoin('e.restaurant', 'r')
            ->where('e.id IN (:events)')
            ->andWhere('e.status IN(:statuses)')
            ->andWhere('a.status = :selected')
            ->andWhere('ar.selected = TRUE')
            ->setParameters(array(
                'events' => $events,
                'statuses' => array(
                    Event::STATUS_RESERVATIONS_OPENED,
                    Event::STATUS_RESERVATIONS_CLOSED
                ),
                'selected' => Applicant::STATUS_ACCEPTED

            ))
            ->orderBy('e.startDate', 'ASC')
            ->addOrderBy('arr.type', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function getEventsIterated(array $statuses)
    {
        $query = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->andWhere('e.status IN (:statuses)')
            ->setParameter('statuses', $statuses)
            ->orderBy('e.startDate', 'ASC')
            ->getQuery();

        return $query->iterate();
    }

    /**
     * Get events that are going to start in the next x minutes
     *
     * @param int $minutes
     *
     * @return Event[]
     */
    public function getStartingEvents($minutes = 15)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.status IN (:statuses)')
            ->andWhere('e.notifiedStart = false')
            ->andWhere('e.startDate < :startTime')
            ->setParameters(array(
                'statuses' => [
                    Event::STATUS_RESERVATIONS_CLOSED,
                    Event::STATUS_IN_PROGRESS
                ],
                'startTime' => new DateTime("+$minutes minutes"),
            ));

        return $qb->getQuery()->getResult();
    }

    public function getEventsWhereMinCapacityNotReached()
    {
        $now = new \DateTime();
        $limit = clone $now;
        $limit->add(new \DateInterval('P4D'));

        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.capacity.minimum >= e.confirmedReservations * 2')
            ->andWhere('e.applicationEndDate < :limit')
            ->andWhere('e.applicationEndDate > :now')
            ->andWhere('e.status IN(:statuses)')
            ->andWhere('e.notifiedMinCapacityNotReached = FALSE')
            ->setParameters(array(
                'limit' => $limit,
                'now' => $now,
                'statuses' => array(
                    Event::STATUS_RESERVATIONS_OPENED
                )
            ));
        ;

        return $qb->getQuery()->getResult();
    }
}
