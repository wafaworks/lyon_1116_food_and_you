<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadMemberData extends AbstractFixture implements OrderedFixtureInterface
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
        $member = new Member();
        $member->setFirstName('Soluti');
        $member->setLastName('Dev');
        $member->setBirthDate(new \DateTime('1985-01-01 01:01:01'));
        $member->setBiography($faker->randomElement(array($faker->paragraph(),'')));
        $member->setSignature($faker->randomElement(array($faker->sentence(8),'')));
        $member->setPhoto($this->getMedia($manager));
        $member->setAuthentication($this->getReference('authentication-soluti'));
        $manager->persist($member);
        $this->addReference('member-dev', $member);

        for ($i = 0; $i < 70; $i++) {
            $member = new Member();
            $member->setFirstName($faker->firstName());
            $member->setLastName($faker->lastName());
            $member->setBirthDate($faker->dateTimeThisCentury());
            $member->setBiography($faker->randomElement(array($faker->paragraph(),'')));
            $member->setSignature($faker->randomElement(array($faker->sentence(8),'')));
            $member->setRestaurants(array($this->getReference('restaurant-' . $i)));
            $member->setPhoto($this->getMedia($manager));
            $member->setAuthentication($this->getReference('authentication-' . $i));
            $member->setRating($faker->numberBetween(1, 5));
            $manager->persist($member);

            $this->addReference('member-' . $i, $member);
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
        return 4;
    }

    /**
     * @param ObjectManager $manager
     * @return Media
     */
    protected function getMedia(ObjectManager $manager)
    {
        $media = new Media();
        $media->setName('photo-user');
        $media->setEnabled(true);
        $media->setContext('user');
        $media->setProviderName('sonata.media.provider.image');
        $media->setAuthorName('fixtures');
        $imagePath = dirname(__FILE__) . '/../images/man' . $this->getFaker()->numberBetween(1, 3) . '.jpg';
        $media->setBinaryContent($imagePath);
        $media->setWidth('200');
        $media->setHeight('200');
        $manager->persist($media);

        return $media;
    }
}
