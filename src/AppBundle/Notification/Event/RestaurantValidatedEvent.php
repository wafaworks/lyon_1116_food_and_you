<?php

namespace AppBundle\Notification\Event;

use AppBundle\Entity\Restaurant;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

final class RestaurantValidatedEvent extends BaseEvent
{
    const EVENT_NAME = 'app.event.restaurant_validated';
    /**
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @param Restaurant $restaurant
     */
    public function __construct(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }
}
