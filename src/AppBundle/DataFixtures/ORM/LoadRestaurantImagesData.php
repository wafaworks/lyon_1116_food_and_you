<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\City;
use AppBundle\Entity\Cuisine;
use AppBundle\Entity\Gallery;
use AppBundle\Entity\GalleryHasMedia;
use AppBundle\Entity\Media;
use AppBundle\Entity\Restaurant;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadRestaurantImagesData extends AbstractFixture implements OrderedFixtureInterface
{
    protected $restaurantStatusPool = array(
        Restaurant::STATUS_VALIDATED,
        Restaurant::STATUS_PENDING,
        Restaurant::STATUS_REJECTED,
    );

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
        for ($i = 0; $i < 70; $i++) {
            $gallery = new Gallery();
            $gallery->setName('restaurant-gallery');
            $gallery->setContext('restaurant');
            $gallery->setEnabled(true);
            $gallery->setDefaultFormat('big');
            $manager->persist($gallery);

            $this->attachMediaToGallery($gallery, $manager, $faker);

            $this->addReference('restaurant-gallery-' . $i, $gallery);
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
     * @return Cuisine
     */
    protected function getCuisine()
    {
        $index = $this->getFaker()->numberBetween(0, 2);

        return $this->getReference('cuisine-' . $index);
    }

    /**
     * @return City
     */
    protected function getCity()
    {
        $index = $this->getFaker()->numberBetween(0, 1);

        return $this->getReference('city-' . $index);
    }

    /**
     * @return mixed
     */
    protected function getStatus()
    {
        return $this->restaurantStatusPool[$this->getFaker()->numberBetween(0, 2)];
    }

    /**
     * {@inheritDoc}
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * @param Gallery $gallery
     * @param ObjectManager $manager
     * @param $faker
     * @return Media
     */
    protected function attachMediaToGallery(Gallery $gallery, ObjectManager $manager, $faker)
    {
        for ($i = 0; $i < 4; $i++) {
            $media = new Media();
            $media->setName('restaurant-photo');
            $media->setEnabled(true);
            $media->setContext('restaurant');
            $media->setProviderName('sonata.media.provider.image');
            $media->setAuthorName('fixtures');
            $imagePath = dirname(__FILE__) . '/../images/resto' . $faker->numberBetween(1, 9) . '.jpg';
            $media->setBinaryContent($imagePath);
            $media->setWidth('200');
            $media->setHeight('200');
            $manager->persist($media);

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setGallery($gallery);
            $galleryHasMedia->setPosition($i);
            $manager->persist($galleryHasMedia);
            $manager->flush();
        }
    }
}
