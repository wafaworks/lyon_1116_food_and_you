<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MemberInfo
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MemberEventRatingsRepository")
 * @ORM\Table(name="member_event_ratings")
 */
class MemberEventRatings
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int $id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Event $event
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     *
     * @var Member $member
     */
    private $member;

    /**
     * @ORM\Column(name="event_rating", type="smallint", options={"default"=0})
     *
     * @var integer $eventRating
     */
    private $eventRating;

    /**
     * @ORM\Column(name="restaurant_rating", type="smallint", options={"default"=0})
     *
     * @var integer $restaurantRating
     */
    private $restaurantRating;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    private $processed;

    /**
     * MemberEventRatings constructor.
     */
    public function __construct()
    {
        $this->eventRating = 0;
        $this->restaurantRating = 0;
        $this->processed = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return integer
     */
    public function getEventRating()
    {
        return $this->eventRating;
    }

    /**
     * @param integer $eventRating
     */
    public function setEventRating($eventRating)
    {
        $this->eventRating = $eventRating;
    }

    /**
     * @return integer
     */
    public function getRestaurantRating()
    {
        return $this->restaurantRating;
    }

    /**
     * @param integer $restaurantRating
     */
    public function setRestaurantRating($restaurantRating)
    {
        $this->restaurantRating = $restaurantRating;
    }

    /**
     * @return boolean
     */
    public function isProcessed()
    {
        return $this->processed;
    }

    /**
     * @param boolean $processed
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;
    }
}
