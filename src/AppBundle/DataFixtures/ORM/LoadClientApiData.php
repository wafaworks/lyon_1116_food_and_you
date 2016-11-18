<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Api\Client;
use AppBundle\Entity\City;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadClientApiData extends AbstractFixture implements OrderedFixtureInterface
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
        $client = new Client();
        $client->setRandomId("5xwbnpsjv0kk8wok8g0sg4k4kowkck8sw0cc8go4c4socc4wwk");
        $client->setRedirectUris(array(
            "http://foodandyou.soluti.fr",
        ));
        $client->setSecret("66ig8g2ozbk8ko0okws8ko44gks4sswk80s0oogocoooskko40");
        $client->setAllowedGrantTypes(array(
            "authorization_code",
            "password",
            "refresh_token",
            "token",
            "client_credentials",
            "http://fy.com/facebook",
        ));

        $manager->persist($client);
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
