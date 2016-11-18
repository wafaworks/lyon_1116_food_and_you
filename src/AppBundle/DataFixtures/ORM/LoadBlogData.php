<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Media;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Xaben\BlogBundle\Entity\Category;
use Xaben\BlogBundle\Entity\Post;

class LoadBlogData extends AbstractFixture implements OrderedFixtureInterface
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
        $category = new Category();
        $category->setTitle('General');
        $manager->persist($category);
        $manager->flush();


        $faker = $this->getFaker();
        for ($i=0; $i<=50; $i++) {
            $blog = new Post();
            $blog->setTitle($faker->sentence());
            $blog->setCategory($category);
            $blog->setContent($faker->paragraph(4));
            $blog->setCover($this->getMedia($manager));
            $blog->setStatus($this->getStatus());
            $blog->setVisibility($this->getVisibility());
            $blog->setPublished($faker->dateTimeBetween('-15 days', 'now'));

            $manager->persist($blog);
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
     * @return mixed
     */
    private function getVisibility()
    {
        $visibilities = Post::getVisibilities();

        $index = $this->getFaker()->numberBetween(1, count($visibilities));

        return $index;
    }

    /**
     * @return mixed
     */
    private function getStatus()
    {
        $statuses = Post::getStatuses();
        $index = $this->getFaker()->numberBetween(1, count($statuses));

        return $index;
    }

    /**
     * @param ObjectManager $manager
     * @return Media
     */
    protected function getMedia(ObjectManager $manager)
    {
        $media = new Media();
        $media->setName('blog-cover');
        $media->setEnabled(true);
        $media->setContext('blog');
        $media->setProviderName('sonata.media.provider.image');
        $media->setAuthorName('fixtures');
        $imagePath = dirname(__FILE__) . '/../images/blog' . $this->getFaker()->numberBetween(1, 3) . '.jpg';
        $media->setBinaryContent($imagePath);
        $media->setWidth('200');
        $media->setHeight('200');
        $manager->persist($media);

        return $media;
    }
}
