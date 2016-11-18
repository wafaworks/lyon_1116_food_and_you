<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;

/**
 * @ORM\Entity()
 * @ORM\Table(name="media__gallery")
 * @ORM\EntityListeners({"AppBundle\Event\Listener\GalleryListener"})
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Gallery extends BaseGallery
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCover()
    {
        /** @var ArrayCollection $ghms */
        $ghms = $this->getGalleryHasMedias();

        if ($ghms->count() > 0) {
            /** @var GalleryHasMedia $ghm */
            foreach ($ghms as $ghm) {
                if ($ghm->getPosition() == 0) {
                    return $ghm->getMedia();
                }
            }

            return $ghms->first()->getMedia();
        }

        return null;
    }

    public function getGalleryOrdered()
    {
        $ghm = $this->getGalleryHasMedias();

        if ($ghm->count() > 0) {
            $array = $ghm->getValues();
            
            usort($array, function ($a, $b) {
                return strcmp($a->getPosition(), $b->getPosition());
            });

            return $array;
        }

        return null;

    }
}
