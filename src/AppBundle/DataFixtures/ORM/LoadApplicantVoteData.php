<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantVote;
use AppBundle\Entity\Member;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadApplicantVoteData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $applicantRepository = $this->container->get('app.repository.applicant');
        $faker = $this->getFaker();

        $events = $eventRepository->findAll();
        foreach ($events as $event) {
            $voters = $memberRepository->getMembersHaveReservationFor($event);
            $applicants = $applicantRepository->getAllApplicants($event);

            foreach ($applicants as $applicant) {
                $voted = $faker->numberBetween(0, count($voters) - 1);

                for ($i=0; $i < $voted; $i++) {
                    $vote = $this->createVote($applicant, $voters[$i]);
                    $manager->persist($vote);
                }
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
        return 10;
    }

    /**
     * @param Applicant $applicant
     * @param Member $voter
     * @return ApplicantVote
     */
    private function createVote(Applicant $applicant, Member $voter)
    {
        $vote = new ApplicantVote();
        $vote->setVoter($voter);
        $vote->setApplicant($applicant);

        return $vote;
    }
}
