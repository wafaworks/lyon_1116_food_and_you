<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Cuisine;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCuisineData extends AbstractFixture implements OrderedFixtureInterface
{
    protected $cuisineNames = array(
        'Français',
        'Italien',
        'Indien',
    );

    /**
     * Load fixture
     *
     * @param ObjectManager $manager Manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->cuisineNames as $key => $name) {
            $cuisine = new Cuisine();
            $cuisine->setName($name);
            $manager->persist($cuisine);
            $this->setReference('cuisine-'.$key, $cuisine);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }
}
