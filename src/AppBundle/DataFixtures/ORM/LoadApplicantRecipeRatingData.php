<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\ApplicantRecipeRating;
use AppBundle\Entity\Member;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadApplicantRecipeRatingData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $recipeRepository = $this->container->get('app.repository.applicant_recipe');

        $finishedEvents = $eventRepository->getFinishedEvents();

        foreach ($finishedEvents as $event) {
            $voters = $memberRepository->getMembersHaveReservationFor($event);
            $recipes = $recipeRepository->getRecipesChosenForEvent($event);

            foreach ($recipes as $recipe) {
                foreach ($voters as $voter) {
                    $rating = $this->createRating($recipe, $voter);
                    $manager->persist($rating);
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
     * @param ApplicantRecipe $recipe
     * @param Member $voter
     * @return ApplicantRecipeRating
     */
    private function createRating(ApplicantRecipe $recipe, Member $voter)
    {
        $rating = new ApplicantRecipeRating();
        $rating->setVoter($voter);
        $rating->setApplicantRecipe($recipe);
        $rating->setTasteRating($this->getFaker()->numberBetween(1, 5));
        $rating->setVisualRating($this->getFaker()->numberBetween(1, 5));

        return $rating;
    }
}
