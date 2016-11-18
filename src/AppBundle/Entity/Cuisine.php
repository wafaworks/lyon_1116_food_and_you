<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CuisineRepository")
 * @ORM\Table()
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Cuisine
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Restaurant", mappedBy="cuisine")
     *
     * @var ArrayCollection | Restaurant[]
     */
    private $restaurants;

    public function __construct()
    {
        $this->restaurants = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
            $restaurant->setCuisine($this);
        }
        $this->restaurants = $restaurants;
    }
}
