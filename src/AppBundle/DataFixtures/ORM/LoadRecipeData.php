<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use AppBundle\Entity\Recipe;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadRecipeData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

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
        $types = array(
            Recipe::TYPE_ENTRY,
            Recipe::TYPE_MAIN,
            Recipe::TYPE_DESSERT
        );

        for ($i = 0; $i < 30; $i++) {
            /** @var Member $member */
            $member = $this->getReference('member-' . $i);
            $typeNr = $i % 3;

            for ($j = 0; $j < 5; $j++) {
                $recipe = new Recipe();
                $recipe->setMember($member);
                $recipe->setName($faker->sentence(5));
                $recipe->setType($types[$typeNr]);
                $recipe->setPublicDescription($faker->paragraph(2));
                $recipe->setPrivateDescription($faker->paragraph(2));
                $recipe->setPhoto($this->getRecipeImage($manager));
                $manager->persist($recipe);

                $this->addReference(sprintf('recipe-%d', $i * 5 + $j), $recipe);
            }
            $manager->flush();
        }

        $member = $this->getReference('member-dev');
        $typeNr = $i % 3;

        for ($j = 0; $j < 5; $j++) {
            $recipe = new Recipe();
            $recipe->setMember($member);
            $recipe->setName($faker->sentence(5));
            $recipe->setType($types[$typeNr]);
            $recipe->setPublicDescription($faker->paragraph(2));
            $recipe->setPrivateDescription($faker->paragraph(2));
            $recipe->setPhoto($this->getRecipeImage($manager));
            $manager->persist($recipe);

            $this->addReference(sprintf('recipe-%d', $i * 5 + $j), $recipe);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @return Media
     */
    protected function getRecipeImage(ObjectManager $manager)
    {
        $media = new Media();
        $media->setName('recipe-photo');
        $media->setEnabled(true);
        $media->setContext('recipe');
        $media->setProviderName('sonata.media.provider.image');
        $media->setAuthorName('fixtures');
        $imagePath = dirname(__FILE__) . '/../images/recipe' . $this->getFaker()->numberBetween(1, 3) . '.jpg';
        $media->setBinaryContent($imagePath);
        $media->setWidth('200');
        $media->setHeight('200');
        $manager->persist($media);

        return $media;
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
        return 5;
    }
}
