<?php

namespace AppBundle\Entity\Embeddables;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class SocialInfo
{
    /**
     * @ORM\Column(type = "string", nullable = true)
     * @Assert\Url()
     */
    private $site;

    /**
     * @ORM\Column(type = "string", nullable = true)
     * @Assert\Url()
     */
    private $tripAdvisor;

    /**
     * @ORM\Column(type = "string", nullable = true)
     * @Assert\Url()
     */
    private $facebook;

    /**
     * Address constructor.
     * @param $site
     * @param $tripAdvisor
     * @param $facebook
     */
    public function __construct($site, $tripAdvisor, $facebook)
    {
        $this->site = $site;
        $this->tripAdvisor = $tripAdvisor;
        $this->facebook = $facebook;
    }
    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return mixed
     */
    public function getTripAdvisor()
    {
        return $this->tripAdvisor;
    }

    /**
     * @return mixed
     */
    public function getFacebook()
    {
        return $this->facebook;
    }
}
