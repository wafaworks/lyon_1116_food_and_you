<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MemberRepository")
 * @ORM\EntityListeners({"AppBundle\Event\Listener\MemberListener"})
 * @ORM\Table(name="member")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Member
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Authentication", inversedBy="member")
     * @ORM\JoinColumn(name="authentication_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Authentication
     */
    private $authentication;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $lastName;

    /**
     * @Gedmo\Slug(fields={"firstName"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     * @var DateTime
     */
    private $birthDate;

    /**
     * @ORM\OneToOne(targetEntity="Media", orphanRemoval=true, cascade={"persist"})
     *
     * @var Media
     */
    private $photo;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    private $biography;

    /**
     * @ORM\OneToMany(targetEntity="Restaurant", mappedBy="owner")
     *
     * @var ArrayCollection | Restaurant[]
     */
    private $restaurants;

    /**
     * @ORM\OneToMany(targetEntity="Applicant", mappedBy="member")
     *
     * @var ArrayCollection | Applicant[]
     */
    private $applications;

    /**
     * @ORM\OneToMany(targetEntity="Recipe", mappedBy="member", cascade={"remove"})
     *
     * @var ArrayCollection | Recipe[]
     */
    private $recipes;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="member")
     *
     * @var ArrayCollection | Reservation[]
     */
    private $reservations;

    /**
     * @ORM\Column(name="signature", type="string", nullable=true)
     *
     * @var string
     */
    private $signature;

    /**
     * @ORM\Column(name="table_code", type="string")
     *
     * @var string
     */
    private $tableCode;

    /**
     * @ORM\Column(name="rating", type="float", nullable=true)
     *
     * @var float
     */
    private $rating;

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     *
     * @var string
     */
    private $phone;

    /**
     * @ORM\Column(name="level", type="smallint")
     *
     * @var integer
     */
    private $level;

    /**
     * @ORM\Column(name="participations", type="smallint")
     *
     * @var integer
     */
    private $participations;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Device", mappedBy="member")
     *
     * @var ArrayCollection
     */
    private $devices;

    /**
     * @ORM\Column(name="profession", type="string", nullable=true)
     *
     * @var string
     */
    private $profession;

    public function __construct()
    {
        $this->restaurants = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->devices = new ArrayCollection();
        $this->level = 0;
        $this->participations = 0;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    /*
     * Get the ID
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
     * @return Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param DateTime $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return Media
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param Media $photo
     */
    public function setPhoto(Media $photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param string $biography
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    /**
     * @return Restaurant[]|ArrayCollection
     */
    public function getRestaurants()
    {
        return $this->restaurants;
    }

    /**
     * @param Restaurant[]|ArrayCollection $restaurants
     */
    public function setRestaurants($restaurants)
    {
        foreach ($restaurants as $restaurant) {
            $restaurant->setOwner($this);
        }

        $this->restaurants = $restaurants;
    }

    /**
     * @return Applicant[]|ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param Applicant[]|ArrayCollection $applications
     */
    public function setApplications($applications)
    {
        foreach ($applications as $applicant) {
            $applicant->setMember($this);
        }

        $this->applications = $applications;
    }

    /**
     * @return Recipe[]|ArrayCollection
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param Recipe[]|ArrayCollection $recipes
     */
    public function setRecipes($recipes)
    {
        foreach ($recipes as $recipe) {
            $recipe->setMember($this);
        }

        $this->recipes = $recipes;
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
            $reservation->setMember($this);
        }

        $this->reservations = $reservations;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public function setUploadedImage(UploadedFile $file)
    {
        $media = new Media();
        $media->setName('photo-user');
        $media->setEnabled(true);
        $media->setContext('user');
        $media->setProviderName('sonata.media.provider.image');
        $media->setBinaryContent($file);
        $this->setPhoto($media);
    }

    public function getUploadedImage()
    {

    }

    /**
     * @return string
     */
    public function getTableCode()
    {
        return $this->tableCode;
    }

    /**
     * @param string $tableCode
     */
    public function setTableCode($tableCode)
    {
        $this->tableCode = $tableCode;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @param int $participations
     */
    public function setParticipations($participations)
    {
        $this->participations = $participations;
    }

    /**
     * @return int
     */
    public function getNrReservations()
    {
        return count($this->reservations);
    }

    /**
     * @return int
     */
    public function getNrApplications()
    {
        return count($this->applications);
    }

    /**
     * @return int
     */
    public function getNrRecipes()
    {
        return count($this->recipes);
    }

    /**
     * @return ArrayCollection
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * @param ArrayCollection $devices
     */
    public function setDevices($devices)
    {
        $this->devices = $devices;
    }

    /**
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param string $profession
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;
    }
}
