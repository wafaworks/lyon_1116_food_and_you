<?php

namespace AppBundle\Service;

use AppBundle\Entity\Embeddables\Capacity;
use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Repository\EventRepository;
use AppBundle\Entity\Repository\RestaurantRepository;
use AppBundle\Model\NewEvent;
use DateTime;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class EventManager
{
    /**
     * @var RestaurantRepository
     */
    private $restaurantRepository;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @param RestaurantRepository $restaurantRepository
     * @param EventRepository $eventRepository
     */
    public function __construct(
        RestaurantRepository $restaurantRepository,
        EventRepository $eventRepository
    ) {
        $this->restaurantRepository = $restaurantRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Returns a new or an existing restaurant
     * @param Member $member
     * @return NewEvent
     * @throws \Exception
     */
    public function getNewEvent(Member $member)
    {
        $ownedRestaurants = $this->restaurantRepository->getOwnedRestaurants($member);

        if (count($ownedRestaurants) !== 1) {
            throw new \Exception('Member does not have restaurants attached');
        }

        $restaurant = $ownedRestaurants[0];
        $event = new NewEvent();
        $event->setRestaurant($restaurant);

        return $event;
    }

    /**
     * @param NewEvent $newEvent
     */
    public function process(NewEvent $newEvent)
    {
        $capacity = explode(';', $newEvent->getPlacesRange());
        $dates = explode(',', $newEvent->getDates());

        foreach ($dates as $date) {
            if (empty($date)) {
                continue;
            }

            $startDate = DateTime::createFromFormat(
                'd/m/Y H:i:s',
                $date . ' ' . $newEvent->getHour()
            );
            $applicationEndDate = clone $startDate;
            $applicationEndDate->modify('-14 days');

            $event = new Event();
            $event->setRestaurant($newEvent->getRestaurant());
            $event->setPrice($newEvent->getPrice());
            $event->setCapacity(new Capacity($capacity[0], $capacity[1]));
            $event->setStatus(Event::STATUS_APPLICANT_REGISTRATION_OPENED);
            $event->setStartDate($startDate);
            $event->setApplicationEndDate($applicationEndDate);

            $this->eventRepository->save($event);
        }
    }
}
