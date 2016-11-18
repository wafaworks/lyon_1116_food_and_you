<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Gallery;
use AppBundle\Entity\GalleryHasMedia;
use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use AppBundle\Entity\Restaurant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class RestaurantRepository extends EntityRepository
{
    /**
     * @param Restaurant $restaurant
     */
    public function save(Restaurant $restaurant)
    {
        $em = $this->getEntityManager();
        $gallery = $restaurant->getGallery();

        if ($gallery) {
            $this->sanitizeGallery($gallery);
        }

        $em->persist($restaurant);
        $em->flush();
    }

    /**
     * @param $gallery
     */
    private function sanitizeGallery(Gallery $gallery)
    {
        $em = $this->getEntityManager();

        /** @var ArrayCollection $ghms */
        $ghms = $gallery->getGalleryHasMedias();
        $new = [];
        foreach ($ghms as $ghm) {
            /** @var GalleryHasMedia $ghm */
            $media = $ghm->getMedia();
            if ((!$media instanceof Media) || (!$media->getBinaryContent() instanceof UploadedFile && !$media->getId())) {
                continue;
            } else {
                $new[] = $ghm;
            }
        }
        $gallery->setGalleryHasMedias($new);
        $em->persist($gallery);
    }

    public function searchByLetters($term, $city_id)
    {
        if (!$term) {
            return array();
        }

        $restaurants = $this
            ->createQueryBuilder('r')
            ->select('r.name')
            ->innerJoin('r.city', 'c')
            ->where("r.name LIKE :term")
            ->andWhere('c.id = :city_id')
            ->andWhere('r.status = :rStatus')
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('city_id', $city_id)
            ->setParameter('rStatus', Restaurant::STATUS_VALIDATED)
            ->getQuery()
            ->getScalarResult();

        return array_map(
            function ($element) {
                return $element['name'];
            },
            $restaurants
        );
    }

    /**
     * @param $member
     * @return array | Restaurant[]
     */
    public function getOwnedRestaurants(Member $member)
    {
        return $this
            ->createQueryBuilder('r')
            ->select('r')
            ->innerJoin('r.owner', 'm')
            ->where("m = :member")
            ->setParameter('member', $member)
            ->getQuery()
            ->getResult();
    }
}
