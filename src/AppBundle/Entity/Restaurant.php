<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Embeddables\ContactInfo;
use AppBundle\Entity\Embeddables\SocialInfo;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RestaurantRepository")
 * @ORM\Table(name="restaurant")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Restaurant
{
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_VALIDATED = 'validated';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="restaurants")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @var Member
     */
    private $owner;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 1000,
     * )
     *
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     *
     * @var DateTime
     */
    private $openingDate;

    /**
     * @ORM\Column(name="address_street", type = "string")
     *
     * @Assert\NotBlank()
     *
     */
    private $street;

    /**
     * @ORM\Column(name="address_postal_code", type = "string")
     *
     * @Assert\NotBlank()
     *
     */
    private $postalCode;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City", inversedBy="restaurants")
     * @ORM\JoinColumn(name="address_city_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @var City
     */
    private $city;

    /**
     * @ORM\Embedded(class = "AppBundle:Embeddables\SocialInfo")
     *
     * @Assert\Valid()
     *
     * @var SocialInfo
     */
    private $socialInfo;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="restaurant", cascade={"remove"})
     *
     * @var ArrayCollection | Event[]
     */
    private $events;

    /**
     * @ORM\Embedded(class = "AppBundle:Embeddables\ContactInfo")
     *
     * @Assert\Valid()
     *
     * @var ContactInfo
     */
    private $contactInfo;

    /**
     * @ORM\OneToOne(targetEntity="Gallery", orphanRemoval=true, cascade={"remove"})
     *
     * @var Gallery
     */
    private $gallery;

    /**
     * @ORM\ManyToOne(targetEntity="Cuisine", inversedBy="restaurants")
     * @ORM\JoinColumn(name="cuisine_id", referencedColumnName="id")
     *
     * @var Cuisine
     */
    private $cuisine;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $status;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->events = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * Get the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Member
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Member $owner
     */
    public function setOwner(Member $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getOpeningDate()
    {
        return $this->openingDate;
    }

    /**
     * @param mixed $openingDate
     */
    public function setOpeningDate($openingDate)
    {
        $this->openingDate = $openingDate;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return SocialInfo
     */
    public function getSocialInfo()
    {
        return $this->socialInfo;
    }

    /**
     * @param SocialInfo $socialInfo
     */
    public function setSocialInfo($socialInfo)
    {
        $this->socialInfo = $socialInfo;
    }

    /**
     * @return Event[]|ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param Event[]|ArrayCollection $events
     */
    public function setEvents($events)
    {
        foreach ($events as $event) {
            $event->setRestaurant($this);
        }

        $this->events = $events;
    }

    /**
     * @return ContactInfo
     */
    public function getContactInfo()
    {
        return $this->contactInfo;
    }

    /**
     * @param ContactInfo $contactInfo
     */
    public function setContactInfo($contactInfo)
    {
        $this->contactInfo = $contactInfo;
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
                self::STATUS_PENDING,
                self::STATUS_REJECTED,
                self::STATUS_VALIDATED,
            )
        )
        ) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    /**
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return Cuisine
     */
    public function getCuisine()
    {
        return $this->cuisine;
    }

    /**
     * @param Cuisine $cuisine
     */
    public function setCuisine(Cuisine $cuisine)
    {
        $this->cuisine = $cuisine;
    }

    /**
     * @return int
     */
    public function getNrEvents()
    {
        return count($this->events);
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        $statuses =  array(
            self::STATUS_VALIDATED,
            self::STATUS_PENDING,
            self::STATUS_REJECTED
        );

        return array_combine($statuses, $statuses);
    }
}
