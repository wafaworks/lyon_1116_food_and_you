<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Recipe;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class LoadApplicantData extends AbstractFixture implements OrderedFixtureInterface
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
            $memberId = $faker->numberBetween(0, 21);
            $winner = $faker->numberBetween(1, 3);
            for ($j = $memberId; $j < $memberId + 9; $j++) {
                /** @var Event $event */
                $event = $this->getReference('event-' . $i);
                /** @var Member $member */
                $member = $this->getReference('member-' . $j);

                $applicant = new Applicant();
                $applicant->setEvent($event);
                $applicant->setMember($member);
                $applicant->setNrVotes(0);

                if ($j - $memberId < 3) {
                    $applicant->setStatus(Applicant::STATUS_ACCEPTED);
                } elseif ($event->getStatus() === Event::STATUS_APPLICANT_REGISTRATION_OPENED) {
                    $applicant->setStatus(Applicant::STATUS_PENDING);
                } else {
                    $applicant->setStatus(Applicant::STATUS_REJECTED);
                }

                $winnerMarked = (bool) ($j - $memberId == $winner);

                $endDate = clone $event->getApplicationEndDate();
                $endDate->modify(sprintf('-%d days', $faker->numberBetween(3, 10)));
                $applicant->setAppliedAt($endDate);

                $manager->persist($applicant);

                $this->loadRecipeData($faker, $j, $applicant, $manager, $winnerMarked);
                //$this->addReference(sprintf('applicant-%d', (($i - 1) * 5) + $j), $applicant);
            }
        }

        $manager->flush();
    }

    protected function loadRecipeData(Generator $faker, $memberId, $applicant, ObjectManager $manager, $winnerMarked)
    {
        $recipeNumbersSubmitted = $faker->randomElements(array(0, 1, 2, 3, 4), $faker->numberBetween(1, 5));
        $first = true;
        foreach ($recipeNumbersSubmitted as $recipeNumber) {
            /** @var Recipe $recipe */
            $recipe = $this->getReference(sprintf('recipe-%d', $memberId * 5 + $recipeNumber));

            $applicantRecipe = new ApplicantRecipe();
            $applicantRecipe->setApplicant($applicant);
            $applicantRecipe->setRecipe($recipe);
            if ($first) {
                $first = false;
                $applicantRecipe->setSelected(true);
                $applicantRecipe->setWinner($winnerMarked);
            }
            $manager->persist($applicantRecipe);
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
        return 6;
    }
}
