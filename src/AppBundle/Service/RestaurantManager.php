<?php

namespace AppBundle\Service;

use AppBundle\Entity\Gallery;
use AppBundle\Entity\GalleryHasMedia;
use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use AppBundle\Entity\Repository\RestaurantRepository;
use AppBundle\Entity\Restaurant;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class RestaurantManager
{
    /**
     * @var RestaurantRepository
     */
    private $restaurantRepository;

    /**
     * @param RestaurantRepository $restaurantRepository
     */
    public function __construct(
        RestaurantRepository $restaurantRepository
    ) {
        $this->restaurantRepository = $restaurantRepository;
    }

    /**
     * Returns a new or an existing restaurant
     * @param Member $member
     * @return Restaurant
     */
    public function getMemberRestaurant(Member $member)
    {
        $ownedRestaurants = $this->restaurantRepository->getOwnedRestaurants($member);

        if (count($ownedRestaurants) >= 1) {
            $restaurant = $ownedRestaurants[0];
        } else {
            $restaurant = new Restaurant();
            $restaurant->setOwner($member);
        }

        if (!$restaurant->getGallery() instanceof Gallery) {
            $gallery = new Gallery();
            $gallery->setName('restaurant-gallery');
            $gallery->setContext('restaurant');
            $gallery->setEnabled(true);
            $gallery->setDefaultFormat('big');

            $restaurant->setGallery($gallery);
        }

        while (count($restaurant->getGallery()->getGalleryHasMedias()) < 4) {
            $media = new Media();
            $media->setName('restaurant-photo');
            $media->setEnabled(true);
            $media->setContext('restaurant');
            $media->setProviderName('sonata.media.provider.image');
            $media->setAuthorName($member->getFullName());

            $ghm = new GalleryHasMedia();
            $ghm->setMedia($media);
            $ghm->setEnabled(true);
            $restaurant->getGallery()->addGalleryHasMedias($ghm);
        }

        return $restaurant;
    }


    public function process(Restaurant $restaurant)
    {
        $this->restaurantRepository->save($restaurant);
    }
}
