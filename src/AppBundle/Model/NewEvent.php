<?php

namespace AppBundle\Model;

use AppBundle\Entity\Restaurant;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class NewEvent
{
    /**
     * @Assert\NotBlank(
     *     message="form.new_event.dates.blank"
     * )
     *
     * @var string $type
     */
    private $dates;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+;\d+$/")
     *
     * @var string $type
     */
    private $placesRange;

    /**
     * @Assert\NotBlank()
     *
     * @var string $type
     */
    private $price;

    /**
     * @Assert\Time(
     *     message="form.new_event.hour.invalid"
     * )
     *
     * @var string $type
     */
    private $hour;

    /**
     * @Assert\NotBlank()
     *
     * @var string $type
     */
    private $restaurant;

    /**
     * @return mixed
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * @param mixed $dates
     */
    public function setDates($dates)
    {
        $this->dates = $dates;
    }

    /**
     * @return mixed
     */
    public function getPlacesRange()
    {
        return $this->placesRange;
    }

    /**
     * @param mixed $placesRange
     */
    public function setPlacesRange($placesRange)
    {
        $this->placesRange = $placesRange;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * @param mixed $hour
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
    }

    /**
     * @return mixed
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param Restaurant $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
    }
}
