<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\DeviceRepository")
 * @ORM\Table()
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Device
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Member
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Member", inversedBy="devices")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $member;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     *
     */
    protected $token;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     *
     */
    protected $os;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="device")
     *
     * @var ArrayCollection|Notification[]
     */
    protected $notifications;

    /**
     * Device constructor.
     */
    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
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
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param string $os
     */
    public function setOs($os)
    {
        $this->os = $os;
    }

    /**
     * @return Notification[]|ArrayCollection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param Notification[]|ArrayCollection $notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    }
}
