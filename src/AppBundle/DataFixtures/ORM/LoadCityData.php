<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\City;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCityData extends AbstractFixture implements OrderedFixtureInterface
{
    private $cities = array (
        'Lyon',
        'Paris',
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
        foreach ($this->cities as $key => $city) {
            $cityObject = new City();
            $cityObject->setName($city);
            $manager->persist($cityObject);
            $this->addReference('city-' . $key, $cityObject);
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
