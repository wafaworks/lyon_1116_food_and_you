<?php

namespace AppBundle\Entity\Embeddables;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Capacity
{
    /**
     * @ORM\Column(type = "integer")
     */
    private $minimum;

    /**
     * @ORM\Column(type = "integer")
     */
    private $maximum;

    /**
     * @param $minimum
     * @param $maximum
     */
    public function __construct($minimum, $maximum)
    {
        $this->minimum = $minimum;
        $this->maximum = $maximum;
    }

    public function __toString()
    {
        return sprintf('%d/%d', $this->getMinimum(), $this->getMaximum());
    }

    /**
     * @return mixed
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * @return mixed
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @param mixed $minimum
     */
    public function setMinimum($minimum)
    {
        if (is_null($minimum) || $minimum < 0) {
            return;
        }

        $this->minimum = $minimum > $this->getMaximum() ? $this->getMaximum() : $minimum;
    }

    /**
     * @param mixed $maximum
     */
    public function setMaximum($maximum)
    {
        if (is_null($maximum) || $maximum < 0) {
            return;
        }

        $this->maximum = $maximum < $this->getMinimum() ? $this->getMinimum() : $maximum;
    }
}
