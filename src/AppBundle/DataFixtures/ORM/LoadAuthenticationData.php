<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Authentication;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadAuthenticationData extends AbstractFixture implements OrderedFixtureInterface
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
        $authentication = new Authentication();
        $authentication->setUsername('soluti');
        $authentication->setEnabled(true);
        $authentication->setEmail('soluti@soluti.fr');
        $authentication->setPlainPassword('recette');
        $authentication->setLocked(false);
        $authentication->setRoles(array('ROLE_SUPER_ADMIN'));
        $manager->persist($authentication);
        $this->addReference('authentication-soluti', $authentication);

        $faker = $this->getFaker();
        for ($i = 0; $i < 70; $i++) {
            $authentication = new Authentication();
            $authentication->setUsername($faker->userName());
            $authentication->setEnabled(true);
            $authentication->setEmail($faker->email());
            $authentication->setPlainPassword('recette');
            $authentication->setLocked(false);
            $authentication->setRoles(array('ROLE_USER', 'ROLE_OWNER'));
            $manager->persist($authentication);
            $this->addReference('authentication-' . $i, $authentication);
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
     * {@inheritDoc}
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }
}
