<?php

namespace AppBundle\Service\Api;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\Event as EventEntity;
use AppBundle\Entity\Member;
use AppBundle\Entity\MemberEventMedia;
use AppBundle\Entity\MemberEventRatings;
use AppBundle\Entity\Repository\ApplicantRecipeRatingRepository;
use AppBundle\Entity\Repository\ApplicantRepository;
use AppBundle\Entity\Repository\EventRepository;
use AppBundle\Entity\Repository\MemberEventMediaRepository;
use AppBundle\Entity\Repository\MemberEventRatingsRepository;
use Sonata\MediaBundle\Provider\ImageProvider;
use Sonata\MediaBundle\Provider\MediaProviderInterface;

class Event
{
    const NO_RATING = -1;
    const DEFAULT_RATING = 0;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var ImageProvider
     */
    protected $imageProvider;

    /**
     * @var ApplicantRepository
     */
    protected $applicantRepository;

    /**
     * @var ApplicantRecipeRatingRepository
     */
    protected $applicantRecipeRatingRepository;

    /**
     * @var MemberEventRatingsRepository
     */
    protected $memberEventRatingsRepository;

    /**
     * Api constructor.
     *
     * @param EventRepository $eventRepository
     * @param ApplicantRepository $applicantRepository
     * @param ApplicantRecipeRatingRepository $applicantRecipeRatingRepository
     * @param MemberEventRatingsRepository $memberEventRatingsRepository
     * @param MemberEventMediaRepository $memberEventMediaRepository
     * @param MediaProviderInterface $imageProvider
     */
    public function __construct(
        EventRepository $eventRepository,
        ApplicantRepository $applicantRepository,
        ApplicantRecipeRatingRepository $applicantRecipeRatingRepository,
        MemberEventRatingsRepository $memberEventRatingsRepository,
        MemberEventMediaRepository $memberEventMediaRepository,
        MediaProviderInterface $imageProvider
    ) {
        $this->eventRepository = $eventRepository;
        $this->applicantRepository = $applicantRepository;
        $this->applicantRecipeRatingRepository = $applicantRecipeRatingRepository;
        $this->memberEventRatingsRepository = $memberEventRatingsRepository;
        $this->memberEventMediaRepository = $memberEventMediaRepository;
        $this->imageProvider = $imageProvider;
    }

    /**
     * @param Member $member
     * @return array
     */
    public function getFutureEventsReservationBy(Member $member)
    {
        $nextReservedEvents = $this->eventRepository->getNotFinishedEventsReservationBy($member);

        $result = [];
        foreach ($nextReservedEvents as $key => $nextReservedEvent) {
            $result[$key]['event_id'] = $nextReservedEvent->getId();
            $result[$key]['event_start_date'] = $nextReservedEvent->getStartDate()->getTimestamp();
            $result[$key]['restaurant_name'] = $nextReservedEvent->getRestaurant()->getName();
            $result[$key]['restaurant_street'] = $nextReservedEvent->getRestaurant()->getStreet();

            if ($nextReservedEvent->getRestaurant()->getGallery() &&
                $nextReservedEvent->getRestaurant()->getGallery()->getGalleryHasMedias()->count() > 0
            ) {
                $result[$key]['restaurant_picture'] = $this->imageProvider->generatePublicUrl(
                    $nextReservedEvent->getRestaurant()->getGallery()->getGalleryHasMedias()[0]->getMedia(),
                    "reference"
                );
            }
        }

        return $result;
    }


    public function getSelectedApplicants(EventEntity $event)
    {
        $selectedApplicants = $this->applicantRepository->getSelectedApplicants($event);

        $result = [];

        /** @var Applicant $applicant */
        foreach ($selectedApplicants as $key => $applicant) {
            /**
             * @var ApplicantRecipe $applicantRecipe
             */
            $applicantRecipe = $applicant->getRecipes()->first();
            $recipe = $applicantRecipe->getRecipe();
            $result[$key]['applicant_recipe_id'] = $applicantRecipe->getId();
            $result[$key]['dish_name'] = $recipe->getName();
            $result[$key]['dish_type'] = $recipe->getType();
            $result[$key]['dish_image_url'] = $this->imageProvider->generatePublicUrl(
                $recipe->getPhoto(),
                "reference"
            );
        }

        return $result;
    }

    public function getEventDetails(Member $member, EventEntity $event)
    {
        $result = [];

        $result['member']['member_id'] = $member->getId();
        $result['member']['first_name'] = $member->getFirstName();
        $result['member']['last_name'] = $member->getLastName();

        /** @var MemberEventRatings $memberEventRatings */
        $memberEventRatings = $this->memberEventRatingsRepository->findOneBy(array(
            'member' => $member,
            'event' => $event,
        ));

        if ($memberEventRatings) {
            $result['member_event_ratings']['restaurant'] = $memberEventRatings->getRestaurantRating();
            $result['member_event_ratings']['event'] = $memberEventRatings->getEventRating();
        } else {
            $result['member_event_ratings']['restaurant'] = $result['member_event_ratings']['event'] = Event::NO_RATING;
        }

        $restaurantEventMedia = $this->memberEventMediaRepository->getRestaurantEventMedia($member, $event);

        /** @var MemberEventMedia $media */
        foreach ($restaurantEventMedia as $media) {
            $mediaUrl = $this->imageProvider->generatePublicUrl(
                $media->getMedia(),
                "reference"
            );

            if ($media->getType() == MemberEventMedia::TYPE_EVENT) {
                $result['event_image'] = $mediaUrl;
            } else {
                $result['restaurant_image'] = $mediaUrl;
            }
        }

        $selectedApplicants = $this->getSelectedApplicants($event);
        $selectedApplicantsID = array_column($selectedApplicants, 'applicant_recipe_id');

        $memberRatesByApplicantRecipeIds = $this->applicantRecipeRatingRepository
            ->getMemberRatesByApplicantRecipeIds($member, $selectedApplicantsID);

        foreach ($memberRatesByApplicantRecipeIds as $key => $memberRate) {
            //TODO refactor
            $currentItem['rating']['visual'] = $memberRate['visual'];
            $currentItem['rating']['taste'] = $memberRate['taste'];
            $currentItem['applicant_recipe_id'] = $memberRate['applicant_recipe_id'];

            $result['dishes'][$memberRate['applicant_recipe_id']] = $currentItem;
        }

        $eventMediaByApplicationRecipeIds = $this->memberEventMediaRepository
            ->getEventMediaByApplicationRecipeIds($member, $selectedApplicantsID);

        /** @var MemberEventMedia $eventMedia */
        foreach ($eventMediaByApplicationRecipeIds as $eventMedia) {
            $applicantRecipeId = $eventMedia->getApplicantRecipe()->getId();
            $result['dishes'][$applicantRecipeId]['image'] =
                $this->imageProvider->generatePublicUrl(
                    $eventMedia->getMedia(),
                    "reference"
                );
        }

        return $result;
    }
}
