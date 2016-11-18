<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Soluti\SogenactifBundle\Entity\Transaction;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ReservationRepository")
 * @ORM\Table(name="reservation")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Reservation
{
    const STATUS_DRAFT = 'draft';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_TO_REFUND = 'to_refund';
    const STATUS_REFUNDED = 'refunded';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="reservations")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     *
     * @var Member
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="reservations")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     *
     * @var Event
     */
    private $event;

    /**
     * @ORM\Column(name="status", type="string", options={"default"="draft"})
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="table_owner_id", referencedColumnName="id")
     *
     * @var Member
     */
    private $tableOwner;

    /**
     * @ORM\Column(name="places", type="smallint", options={"default"="1"})
     *
     * @var integer
     */
    private $places;

    /**
     * @ORM\OneToOne(targetEntity="Soluti\SogenactifBundle\Entity\Transaction")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", nullable=false)
     *
     * @var Transaction
     */
    private $transaction;

    /**
     * @ORM\Column(name="confirmation_sent", type="boolean")
     *
     * @var boolean
     */
    private $confirmationSent;

    public function __construct()
    {
        $this->setStatus(self::STATUS_DRAFT);
        $this->places = 1;
        $this->confirmationSent = false;
    }

    public function __toString()
    {
        return $this->getId() ? (string) $this->getId() : '';
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        $reservationStatuses = [
            self::STATUS_DRAFT,
            self::STATUS_CONFIRMED,
            self::STATUS_CANCELLED,
            self::STATUS_TO_REFUND,
            self::STATUS_REFUNDED,
        ];

        return array_combine($reservationStatuses, $reservationStatuses);
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
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member $member
     */
    public function setMember(Member $member)
    {
        $this->member = $member;
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
    public function setEvent(Event $event)
    {
        $this->event = $event;
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
     * @throws Exception
     */
    public function setStatus($status)
    {
        if (!in_array(
            $status,
            $this->getStatuses()
        )) {
            throw new Exception('Trying to set a undefined status to Reservation');
        }

        $this->status = $status;
    }

    /**
     * @return Member
     */
    public function getTableOwner()
    {
        return $this->tableOwner;
    }

    /**
     * @param Member $tableOwner
     */
    public function setTableOwner(Member $tableOwner)
    {
        $this->tableOwner = $tableOwner;
    }

    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param Transaction $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return int
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param int $places
     */
    public function setPlaces($places)
    {
        $this->places = $places;
    }

    public function getInvoiceNumber()
    {
        return sprintf(
            'FY%d%d',
            $this->getEvent()->getStartDate()->format('Ymd'),
            $this->getTransaction()->getId()
        );
    }

    /**
     * @return boolean
     */
    public function isConfirmationSent()
    {
        return $this->confirmationSent;
    }

    /**
     * @param boolean $confirmationSent
     */
    public function setConfirmationSent($confirmationSent)
    {
        $this->confirmationSent = $confirmationSent;
    }
}
