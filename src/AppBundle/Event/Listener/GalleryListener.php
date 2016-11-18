<?php

namespace AppBundle\Event\Listener;

use AppBundle\Entity\Gallery;
use AppBundle\Entity\GalleryHasMedia;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GalleryListener implements ContainerAwareInterface
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @ORM\PreRemove
     * @param Gallery $gallery
     * @param LifecycleEventArgs $event
     */
    public function preRemoveHandler(Gallery $gallery, LifecycleEventArgs $event)
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        foreach ($gallery->getGalleryHasMedias() as $galleryHasMedia) {
            /** @var $galleryHasMedia GalleryHasMedia */
            $entityManager->remove($galleryHasMedia->getMedia());
        }
        $entityManager->flush();
    }
}
