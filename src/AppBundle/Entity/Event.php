<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Embeddables\Capacity;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventRepository")
 * @ORM\Table(name="event")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Event
{
    const STATUS_APPLICANT_REGISTRATION_OPENED = 'applicant_registration_open';
    const STATUS_APPLICANT_REGISTRATION_CLOSED = 'applicant_registration_closed';
    const STATUS_RESERVATIONS_OPENED = 'reservations_opened';
    const STATUS_RESERVATIONS_CLOSED = 'reservations_closed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FINISHED = 'finished';

    const PRICE_1 = 29.90;
    const PRICE_2 = 34.90;
    const PRICE_3 = 39.90;

    const MAX_LIMIT_RESERVATION = 6;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     *
     * @ORM\Column(length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $applicationEndDate;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $price;

    /**
     * @ORM\Embedded(class = "AppBundle:Embeddables\Capacity")
     *
     * @var Capacity
     */
    private $capacity;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="events")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     *
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Applicant", mappedBy="event", cascade={"remove"})
     * @var ArrayCollection | Applicant[]
     */
    private $applicants;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="event", cascade={"remove"})
     *
     * @var ArrayCollection | Reservation[]
     */
    private $reservations;

    /**
     * @ORM\Column(name="confirmed_reservations", type="smallint")
     *
     * @var integer
     */
    private $confirmedReservations;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MemberEventMedia", mappedBy="event", cascade={"remove"})
     *
     * @var ArrayCollection $memberEventMedias
     */
    private $memberEventMedias;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    private $restaurantRating;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    private $eveningRating;

    /**
     * @ORM\Column(name="notified_start", type="boolean", options={"default"=false})
     *
     * @var bool
     */
    private $notifiedStart;

    /**
     * @ORM\Column(name="notified_min_capacity_not_reached", type="boolean", options={"default"=false})
     *
     * @var boolean
     */
    private $notifiedMinCapacityNotReached;

    public function __construct()
    {
        $this->applicants = new ArrayCollection();
        $this->memberEventMedias = new ArrayCollection();
        $this->restaurantRating = 0;
        $this->eveningRating = 0;
        $this->confirmedReservations = 0;
        $this->notifiedStart = false;
        $this->notifiedMinCapacityNotReached = false;
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->getRestaurant()->getName());
    }

    public function __toString()
    {
        return $this->getRestaurant() instanceof Restaurant ?
            sprintf("%s - %s", $this->getRestaurant()->getName(), $this->getStartDate()->format('d/m/Y')) :
            $this->getStartDate()->format('d/m/Y');
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTime
     */
    public function getApplicationEndDate()
    {
        return $this->applicationEndDate;
    }

    /**
     * @param DateTime $applicationEndDate
     */
    public function setApplicationEndDate($applicationEndDate)
    {
        $this->applicationEndDate = $applicationEndDate;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        if (!in_array(
            $price,
            array(
                self::PRICE_1,
                self::PRICE_2,
                self::PRICE_3,
            )
        )
        ) {
            throw new \InvalidArgumentException("Invalid price category");
        }

        $this->price = $price;
    }

    /**
     * @return Capacity
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param Capacity $capacity
     */
    public function setCapacity(Capacity $capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param Restaurant $restaurant
     */
    public function setRestaurant(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if (!in_array(
            $status,
            array(
                self::STATUS_APPLICANT_REGISTRATION_OPENED,
                self::STATUS_APPLICANT_REGISTRATION_CLOSED,
                self::STATUS_RESERVATIONS_OPENED,
                self::STATUS_RESERVATIONS_CLOSED,
                self::STATUS_IN_PROGRESS,
                self::STATUS_CANCELLED,
                self::STATUS_FINISHED,
            )
        )
        ) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->status = $status;
    }

    /**
     * @return Applicant[]|ArrayCollection
     */
    public function getApplicants()
    {
        return $this->applicants;
    }

    /**
     * @param Applicant[]|ArrayCollection $applicants
     */
    public function setApplicants(ArrayCollection $applicants)
    {
        foreach ($applicants as $applicant) {
            $applicant->setEvent($this);
        }

        $this->applicants = $applicants;
    }

    /**
     * @return Reservation[]|ArrayCollection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * @param Reservation[]|ArrayCollection $reservations
     */
    public function setReservations($reservations)
    {
        foreach ($reservations as $reservation) {
            $reservation->setEvent($this);
        }

        $this->reservations = $reservations;
    }

    /**
     * @return int
     */
    public function getConfirmedReservations()
    {
        return $this->confirmedReservations;
    }

    /**
     * @param int $confirmedReservations
     */
    public function setConfirmedReservations($confirmedReservations)
    {
        $this->confirmedReservations = $confirmedReservations;
    }

    /**
     * @return bool
     */
    public function isVotingPossible()
    {
        return $votingPossible = in_array($this->getStatus(), [
            Event::STATUS_APPLICANT_REGISTRATION_OPENED,
            Event::STATUS_APPLICANT_REGISTRATION_CLOSED,
        ]);
    }

    /**
     * Display photo block or not
     *
     * @return bool
     */
    public function arePhotosAvailable()
    {
        return in_array($this->getStatus(), [
            Event::STATUS_IN_PROGRESS,
            Event::STATUS_FINISHED,
        ]);
    }

    public static function getPrices()
    {
        return array(
            number_format(self::PRICE_1, 2, '.', '') => number_format(self::PRICE_1, 2, ',', '.'),
            number_format(self::PRICE_2, 2, '.', '') => number_format(self::PRICE_2, 2, ',', '.'),
            number_format(self::PRICE_3, 2, '.', '') => number_format(self::PRICE_3, 2, ',', '.')
        );
    }

    public static function getStatuses()
    {
        $prices = array(
            self::STATUS_APPLICANT_REGISTRATION_OPENED,
            self::STATUS_APPLICANT_REGISTRATION_CLOSED,
            self::STATUS_RESERVATIONS_OPENED,
            self::STATUS_RESERVATIONS_CLOSED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_FINISHED,
            self::STATUS_CANCELLED
        );

        return array_combine($prices, $prices);
    }

    /**
     * @return ArrayCollection
     */
    public function getMemberEventMedias()
    {
        return $this->memberEventMedias;
    }

    /**
     * @param ArrayCollection $memberEventMedias
     */
    public function setMemberEventMedias($memberEventMedias)
    {
        $this->memberEventMedias = $memberEventMedias;
    }

    /**
     * Used in Admin listing
     *
     * @return int
     */
    public function getNrApplicants()
    {
        return count($this->applicants);
    }

    /**
     * @return float
     */
    public function getRestaurantRating()
    {
        return $this->restaurantRating;
    }

    /**
     * @param float $restaurantRating
     */
    public function setRestaurantRating($restaurantRating)
    {
        $this->restaurantRating = $restaurantRating;
    }

    /**
     * @return mixed
     */
    public function getEveningRating()
    {
        return $this->eveningRating;
    }

    /**
     * @param mixed $eveningRating
     */
    public function setEveningRating($eveningRating)
    {
        $this->eveningRating = $eveningRating;
    }

    /**
     * @return boolean
     */
    public function isNotifiedStart()
    {
        return $this->notifiedStart;
    }

    /**
     * @param boolean $notifiedStart
     */
    public function setNotifiedStart($notifiedStart)
    {
        $this->notifiedStart = $notifiedStart;
    }

    /**
     * @return boolean
     */
    public function isNotifiedMinCapacityNotReached()
    {
        return $this->notifiedMinCapacityNotReached;
    }

    /**
     * @param boolean $notifiedMinCapacityNotReached
     */
    public function setNotifiedMinCapacityNotReached($notifiedMinCapacityNotReached)
    {
        $this->notifiedMinCapacityNotReached = $notifiedMinCapacityNotReached;
    }
}
