<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\City;
use AppBundle\Entity\Cuisine;
use AppBundle\Entity\Embeddables\ContactInfo;
use AppBundle\Entity\Embeddables\SocialInfo;
use AppBundle\Entity\Restaurant;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadRestaurantData extends AbstractFixture implements OrderedFixtureInterface
{
    protected $restaurantStatusPool = array(
        Restaurant::STATUS_VALIDATED,
        Restaurant::STATUS_PENDING,
        Restaurant::STATUS_REJECTED,
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
        $faker = $this->getFaker();
        for ($i=0; $i<70; $i++) {
            $restaurant = new Restaurant();
            $restaurant->setName($faker->company());
            $restaurant->setDescription($faker->paragraph());
            $restaurant->setOpeningDate($faker->dateTimeThisDecade());
            $restaurant->setCity($this->getCity());
            $restaurant->setStreet($faker->streetAddress());
            $restaurant->setPostalCode($faker->postcode());

            $restaurant->setSocialInfo(
                new SocialInfo(
                    $faker->url(),
                    'http://www.tripadvisor.com/' . $faker->slug(),
                    'http://www.facebook.com/page/' . $faker->slug()
                )
            );
            $restaurant->setContactInfo(
                new ContactInfo(
                    $faker->phoneNumber(),
                    $faker->phoneNumber(),
                    $faker->email()
                )
            );
            $restaurant->setStatus($this->getStatus());
            $restaurant->setCuisine($this->getCuisine());
            $restaurant->setGallery($this->getReference('restaurant-gallery-' . $i));

            $manager->persist($restaurant);
            $this->addReference('restaurant-' . $i, $restaurant);

        }
        $manager->flush();

    }

    /**
     * @return \Faker\Generator
     */
    private function getFaker()
    {
        return Factory::create('fr_FR');
    }

    /**
     * @return Cuisine
     */
    protected function getCuisine()
    {
        $index = $this->getFaker()->numberBetween(0, 2);

        return $this->getReference('cuisine-'. $index);
    }

    /**
     * @return City
     */
    protected function getCity()
    {
        $index = $this->getFaker()->numberBetween(0, 1);

        return $this->getReference('city-'. $index);
    }

    /**
     * @return mixed
     */
    protected function getStatus()
    {
        return $this->restaurantStatusPool[$this->getFaker()->numberBetween(0, 2)];
    }

    /**
     * {@inheritDoc}
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
