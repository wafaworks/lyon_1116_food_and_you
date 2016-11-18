<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Embeddables\Capacity;
use AppBundle\Entity\Event;
use AppBundle\Entity\Restaurant;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadEventData extends AbstractFixture implements OrderedFixtureInterface
{
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

        for ($i = 1; $i < 200; $i++) {
            $eventDate = $faker->dateTimeBetween('- 45 days', '+ 45 days');
            $applicationEndDate = clone $eventDate;
            $applicationEndDate->modify('- 14 days');

            $event = new Event();
            $event->setStartDate($eventDate);
            $event->setApplicationEndDate($applicationEndDate);
            $event->setPrice($this->getRandomPriceCategory());
            $event->setCapacity(new Capacity($faker->numberBetween(15, 25), $faker->numberBetween(40, 50)));
            $event->setRestaurant($this->getRandomRestaurant());
            $this->updateEventStatus($event);
            $manager->persist($event);
            $manager->flush();
            $this->addReference('event-' . $i, $event);
        }
    }

    /**
     * {@inheritDoc}
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @return \Faker\Generator
     */
    private function getFaker()
    {
        return Factory::create('fr_FR');
    }

    /**
     * @return mixed
     */
    private function getRandomPriceCategory()
    {
        $priceCategories = array(
            Event::PRICE_1,
            Event::PRICE_2,
            Event::PRICE_3,
        );

        return $priceCategories[$this->getFaker()->numberBetween(0, 2)];
    }

    private function updateEventStatus(Event $event)
    {
        if ($event->getApplicationEndDate() > new DateTime()) {
            $event->setStatus(Event::STATUS_APPLICANT_REGISTRATION_OPENED);
        } elseif ($event->getStartDate() > new DateTime('+ 1 hour')) {
            $event->setStatus(Event::STATUS_RESERVATIONS_OPENED);
        } elseif ($event->getStartDate() > new DateTime()) {
            $event->setStatus(Event::STATUS_RESERVATIONS_CLOSED);
        } else {
            $event->setStatus(Event::STATUS_FINISHED);
        }
    }

    /**
     * @return Restaurant
     */
    private function getRandomRestaurant()
    {
        return $this->getReference('restaurant-' . $this->getFaker()->numberBetween(0, 50));
    }
}
