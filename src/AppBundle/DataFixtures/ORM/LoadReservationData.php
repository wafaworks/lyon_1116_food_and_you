<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Reservation;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Soluti\SogenactifBundle\Entity\Transaction;

class LoadReservationData extends AbstractFixture implements OrderedFixtureInterface
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
            $memberReserved = array();
            for ($j = 0; $j < 15; $j++) {
                $memberId = $faker->numberBetween(0, 49);
                while (in_array($memberId, $memberReserved)) {
                    $memberId = $faker->numberBetween(0, 49);
                }

                $memberReserved[] = $memberId;

                $reservation = new Reservation();
                $reservation->setMember($this->getReference('member-' . $memberId));
                $reservation->setEvent($this->getReference('event-'. $i));
                $amount = $this->getReference('event-'. $i)->getPrice();
                $transaction = new Transaction($amount, 'EUR');
                $reservation->setTransaction($transaction);

                $manager->persist($transaction);
                $manager->persist($reservation);
            }
            $manager->flush();
        }
    }

    /**
     * @return \Faker\Generator
     */
    private function getFaker()
    {
        return Factory::create('fr_FR');
    }

    /**
     * {@inheritDoc}
     * @return int
     */
    public function getOrder()
    {
        return 7;
    }
}
