<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ApplicantVote;
use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\MemberEventRatings;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMemberEventRatingData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load fixture
     *
     * @param ObjectManager $manager Manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $eventRepository = $this->container->get('app.repository.event');
        $memberRepository = $this->container->get('app.repository.member');

        $events = $eventRepository->findAll();
        foreach ($events as $event) {
            $raters = $memberRepository->getMembersHaveReservationFor($event);

            foreach ($raters as $rater) {
                $memberEventRating = $this->createRating($event, $rater);
                $manager->persist($memberEventRating);
            }
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
        return 11;
    }

    /**
     * @param Event $event
     * @param Member $member
     * @return ApplicantVote
     */
    private function createRating(Event $event, Member $member)
    {
        $faker = $this->getFaker();
        $rating = new MemberEventRatings();
        $rating->setMember($member);
        $rating->setEvent($event);
        $rating->setEventRating($faker->numberBetween(0, 5));
        $rating->setRestaurantRating($faker->numberBetween(0, 5));

        return $rating;
    }
}
