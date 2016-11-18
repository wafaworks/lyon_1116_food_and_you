<?php

namespace AppBundle\Entity\Embeddables;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class ContactInfo
{
    /**
     * @ORM\Column(type = "string")
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="10",
     *     max="20"
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type = "string")
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="10",
     *     max="20"
     * )
     */
    private $mobilePhone;

    /**
     * @ORM\Column(type = "string")
     *
     * @Assert\NotBlank()
     *
     * @Assert\Email(
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * Address constructor.
     * @param $phone
     * @param $mobilePhone
     * @param $email
     */
    public function __construct($phone, $mobilePhone, $email)
    {
        $this->phone = $phone;
        $this->mobilePhone = $mobilePhone;
        $this->email = $email;
    }
    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
}
