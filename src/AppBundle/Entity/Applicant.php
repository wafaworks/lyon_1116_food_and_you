<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ApplicantRepository")
 * @ORM\Table(name="applicant")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Applicant
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="applicants")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     *
     * @var Event
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="applications")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     * @var Member
     */
    private $member;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $nrVotes;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $appliedAt;

    /**
     * @ORM\OneToMany(targetEntity="ApplicantRecipe", mappedBy="applicant", cascade={"remove"})
     *
     * @var ArrayCollection | ApplicantRecipe[]
     */
    private $recipes;

    /**
     * @ORM\OneToMany(targetEntity="ApplicantCookWith", mappedBy="applicant", cascade={"remove"})
     *
     * @var ArrayCollection | ApplicantCookWith[]
     */
    private $cookWith;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->recipes = new ArrayCollection();
        $this->cookWith = new ArrayCollection();
    }

    public function __toString()
    {
        $name = $this->getMember() ? $this->getMember()->getFullName() . ' | ' : '';
        $name .= $this->getEvent() ? $this->getEvent()->__toString() : '';

        return $name;
    }

    /**
     * Get the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member $member
     */
    public function setMember($member)
    {
        $this->member = $member;
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
                self::STATUS_ACCEPTED,
                self::STATUS_REJECTED,
            )
        )) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getNrVotes()
    {
        return $this->nrVotes;
    }

    /**
     * @param int $nrVotes
     */
    public function setNrVotes($nrVotes)
    {
        $this->nrVotes = $nrVotes;
    }

    /**
     * @return DateTime
     */
    public function getAppliedAt()
    {
        return $this->appliedAt;
    }

    /**
     * @param DateTime $appliedAt
     */
    public function setAppliedAt(DateTime $appliedAt)
    {
        $this->appliedAt = $appliedAt;
    }

    /**
     * @return ApplicantRecipe[]|ArrayCollection
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param ApplicantRecipe[]|ArrayCollection $recipes
     */
    public function setRecipes(ArrayCollection $recipes)
    {
        foreach ($recipes as $recipe) {
            $recipe->setApplicant($this);
        }

        $this->recipes = $recipes;
    }

    /**
     * @return ApplicantCookWith[]|ArrayCollection
     */
    public function getCookWith()
    {
        return $this->cookWith;
    }

    /**
     * @param ApplicantCookWith[]|ArrayCollection $cookWith
     */
    public function setCookWith($cookWith)
    {
        $this->cookWith = $cookWith;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        $prices =  array(
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_PENDING
        );

        return array_combine($prices, $prices);
    }

    /**
     * Used for Admin
     *
     * @return int
     */
    public function getNrRecipes()
    {
        return count($this->recipes);
    }
}
