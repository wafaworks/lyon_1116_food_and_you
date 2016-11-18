<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\NotificationRepository")
 * @ORM\Table()
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Notification
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Device", inversedBy="notifications")
     *
     * @var Device
     */
    protected $device;

    /**
     * @ORM\Column(name="message_key", type="string")
     *
     * @var string
     */
    protected $messageKey;

    /**
     * @ORM\Column(name="message_parameters", type="text")
     *
     * @var string
     */
    protected $messageParameters;

    /**
     * @ORM\Column(name="sent", type="boolean")
     *
     * @var bool
     */
    protected $sent;

    /**
     * @ORM\Column(name="send_time", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $sendTime;

    /**
     * Notification constructor.
     */
    public function __construct()
    {
        $this->sent = false;
    }

    /**
     * Get the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param Device $device
     */
    public function setDevice($device)
    {
        $this->device = $device;
    }

    /**
     * @return string
     */
    public function getMessageKey()
    {
        return $this->messageKey;
    }

    /**
     * @param string $messageKey
     */
    public function setMessageKey($messageKey)
    {
        $this->messageKey = $messageKey;
    }

    /**
     * @return string
     */
    public function getMessageParameters()
    {
        return unserialize($this->messageParameters);
    }

    /**
     * @param string $messageParameters
     */
    public function setMessageParameters($messageParameters)
    {
        $this->messageParameters = serialize($messageParameters);
    }

    /**
     * @return boolean
     */
    public function isSent()
    {
        return $this->sent;
    }

    /**
     * @param boolean $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * @return \DateTime
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * @param \DateTime $sendTime
     */
    public function setSendTime($sendTime)
    {
        $this->sendTime = $sendTime;
    }
}
