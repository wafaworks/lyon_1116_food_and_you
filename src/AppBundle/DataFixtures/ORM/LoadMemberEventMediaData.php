<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\ApplicantVote;
use AppBundle\Entity\Event;
use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use AppBundle\Entity\MemberEventMedia;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMemberEventMediaData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $faker = $this->getFaker();
        $eventRepository = $this->container->get('app.repository.event');
        $memberRepository = $this->container->get('app.repository.member');
        $applicantRecipeRepository = $this->container->get('app.repository.applicant_recipe');

        $events = $eventRepository->findAll();
        foreach ($events as $event) {
            /** @var Event $event */
            if ($event->getStatus() == Event::STATUS_FINISHED) {
                $raters = $memberRepository->getMembersHaveReservationFor($event);
                $winningRecipes = $applicantRecipeRepository->getRecipesChosenForEvent($event);

                foreach ($raters as $key => $rater) {
                    if ($key % 3 === 0) {
                        if (count($winningRecipes) > 0) {
                            for ($i=0; $i < $faker->numberBetween(0, count($winningRecipes)); $i++) {
                                $manager->persist(
                                    $this->createMedia(
                                        $manager,
                                        $event,
                                        $rater,
                                        MemberEventMedia::TYPE_RECIPE,
                                        $winningRecipes[$i]
                                    )
                                );
                            }
                        }
                        $manager->persist(
                            $this->createMedia($manager, $event, $rater, MemberEventMedia::TYPE_RESTAURANT)
                        );
                        $manager->persist(
                            $this->createMedia($manager, $event, $rater, MemberEventMedia::TYPE_EVENT)
                        );
                    }
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
        return 11;
    }

    /**
     * @param ObjectManager $manager
     * @param Event $event
     * @param Member $member
     * @param $type
     * @param null $applicantRecipe
     * @return ApplicantVote
     */
    private function createMedia(ObjectManager $manager, Event $event, Member $member, $type, $applicantRecipe = null)
    {
        $eventMedia = new MemberEventMedia();
        $eventMedia->setMember($member);
        $eventMedia->setEvent($event);
        $eventMedia->setType($type);
        if ($applicantRecipe instanceof ApplicantRecipe) {
            $eventMedia->setApplicantRecipe($applicantRecipe);
        }
        $eventMedia->setMedia($this->getMedia($manager));

        return $eventMedia;
    }

    /**
     * @param ObjectManager $manager
     * @return Media
     */
    private function getMedia(ObjectManager $manager)
    {
        $media = new Media();
        $media->setName('event-member-photo');
        $media->setEnabled(true);
        $media->setContext('event_photo');
        $media->setProviderName('sonata.media.provider.image');
        $media->setAuthorName('fixtures');
        $imagePath = dirname(__FILE__) . '/../images/eventphoto' . $this->getFaker()->numberBetween(1, 9) . '.jpg';
        $media->setBinaryContent($imagePath);
        $media->setWidth('200');
        $media->setHeight('200');
        $manager->persist($media);

        return $media;
    }
}
