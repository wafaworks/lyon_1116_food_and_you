<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ApplicantRecipe;
use AppBundle\Entity\Authentication;
use AppBundle\Entity\Event;
use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use AppBundle\Entity\MemberEventMedia;
use AppBundle\Entity\MemberEventRatings;
use AppBundle\Exception\Api\ApiBadRequestException;
use AppBundle\Exception\Api\ApiUnauthorizedException;
use AppBundle\Form\Type\DeviceTokenType;
use AppBundle\Form\Type\MemberEventMediaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(path="/api/v1/")
 *
 * Class ApiController
 * @package AppBundle\Controller
 */
class ApiController extends Controller
{
    /**
     * @Route(path="event/list", name="api_event_list")
     * @Method(methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getNextEventAction()
    {
        /** @var Authentication $user */
        $user = $this->getUser();

        $nextReservedEvents = $this->get('app.api.event')->getFutureEventsReservationBy(
            $user->getMember()
        );

        return new JsonResponse($nextReservedEvents);
    }

    /**
     * @Route(path="event/get/{event}", name="api_event")
     * @Method(methods={"GET"})
     *
     * @param Event $event
     * @return JsonResponse
     */
    public function getEventAction(Event $event)
    {
        $selectedApplicants = $this->get('app.api.event')->getSelectedApplicants(
            $event
        );

        return new JsonResponse($selectedApplicants);
    }

    /**
     * @Route(path="event/ratings/get/{event}", name="api_event_ratings_get")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     * @return JsonResponse
     */
    public function getEventRatingsAction(Request $request, Event $event)
    {
        /** @var Member $member */
        $member = $this->getMember($request, $event);

        $eventDetails = $this->get('app.api.event')->getEventDetails(
            $member,
            $event
        );

        return new JsonResponse($eventDetails);
    }

    /**
     * @Route(path="event/ratings/{event}", name="api_event_ratings_post")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     * @return JsonResponse
     *
     * @throws ApiBadRequestException
     * @throws ApiUnauthorizedException
     *
     * //TODO REFACTOR rating save action
     */
    public function saveEventRatingsAction(Request $request, Event $event)
    {
        /** @var Member $member */
        $member = $this->getMember($request, $event);

        $type = $request->request->get('type');
        $rating = $request->request->get('rating');
        $applicantRecipeId = $request->request->get('applicant_recipe_id');

        if (!$type || !$rating) {
            throw new ApiBadRequestException();
        }

        if ($type == MemberEventMedia::TYPE_RECIPE && !$applicantRecipeId) {
            throw new ApiBadRequestException();
        }

        if ($type == MemberEventMedia::TYPE_RECIPE) {
            /** @var ApplicantRecipe $applicantRecipe */
            $applicantRecipe = $this->get('app.repository.applicant_recipe')->find($applicantRecipeId);
            $applicantRecipeRepository = $this->get('app.repository.applicant_recipe_rating');

            $applicantRecipeRating = $applicantRecipeRepository->findOneBy(array(
                'applicantRecipe' => $applicantRecipe,
                'voter' => $member
            ));

            $applicantRecipeRepository->save($member, $applicantRecipe, $rating, $applicantRecipeRating);
            return new JsonResponse();
        }

        if ($type == MemberEventMedia::TYPE_EVENT || $type == MemberEventMedia::TYPE_RESTAURANT) {
            $memberEventRatingsRepository = $this->get('app.repository.member.event.ratings');
            $memberEventRating = $memberEventRatingsRepository->findOneBy(array(
                'member' => $member,
                'event'  => $event,
            ));

            if ($memberEventRating instanceof MemberEventRatings) {
                $memberEventRatingsRepository->save($member, $event, $rating, $type, $memberEventRating);
            } else {
                $memberEventRatingsRepository->save($member, $event, $rating, $type);
            }

            return new JsonResponse();
        }

        throw new ApiBadRequestException();
    }

    /**
     * @Route(path="device", name="api_device_token")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @throws ApiBadRequestException
     */
    public function deviceTokenAction(Request $request)
    {
        $member = $this->getUser()->getMember();

        $deviceRepository = $this->get('app.repository.device');
        $device = $deviceRepository->create(
            $request->request->get('device_token', array())
        );
        $device->setMember($member);

        $form = $this->createForm(DeviceTokenType::class, $device);
        $form->handleRequest($request);
        if (!$form->isValid()) {
            throw new ApiBadRequestException();
        }

        $deviceRepository->save($device);

        return new JsonResponse(
            array('status' => 'ok'),
            201
        );
    }

    /**
     * @Route(path="event/image/{event}", name="api_event_image_post")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     * @return JsonResponse
     */
    public function saveEventImageAction(Request $request, Event $event)
    {
        /** @var Member $member */
        $member = $this->getMember($request, $event);

        $mediaFile = $request->files->get('media');
        $type = $request->request->get('type');

        $memberEventMediaModel = new \AppBundle\Model\MemberEventMedia();
        $form = $this->createForm(MemberEventMediaType::class, $memberEventMediaModel);

        $form->submit(array_merge(
            $request->request->all(),
            $request->files->all()
        ));

        if ($form->isValid()) {
            $memberEventMediaRepository = $this->get('app.repository.member.event.media');

            $applicantRecipe = $form->get('applicantRecipe')->getData();

            $memberEventMedia = $memberEventMediaRepository->findOneBy(array(
                'member'          => $member,
                'event'           => $event,
                'type'            => $type,
                'applicantRecipe' => $applicantRecipe,
            ));

            if (!$memberEventMedia instanceof MemberEventMedia) {
                $memberEventMedia = new MemberEventMedia();
                $memberEventMedia->setType($type);
                $memberEventMedia->setEvent($event);
                $memberEventMedia->setMember($member);
                $memberEventMedia->setApplicantRecipe($applicantRecipe);
            }

            $media = $this->createMedia($mediaFile);
            $memberEventMedia->setMedia($media);

            $this
                ->get('app.repository.member.event.media')
                ->save($memberEventMedia);

            return new JsonResponse(
                array(
                    'status' => 200,
                    'media'  => $this->get('sonata.media.provider.image')->generatePublicUrl(
                        $media,
                        "reference"
                    ),
                )
            );
        }

        $errors = $this->get('app.form.error_serializer')->serializeFormErrors($form);

        return new JsonResponse(
            array(
                'status' => 401,
                'errors' => $errors,
            )
        );
    }


    /**
     * Get current member, or return Error
     *
     * @param Request $request
     * @param Event $event
     *
     * @return JsonResponse
     * @throws ApiUnauthorizedException
     * @internal param $friendEmail
     */
    private function getMember(Request $request, Event $event)
    {
        $member = $this->getUser()->getMember();
        $friendEmail = $request->request->get('friendEmail');

        if ($friendEmail) {
            /** @var Authentication $friend */
            $friend = $this
                ->get('app.repository.authentication')
                ->findOneBy(
                    array(
                        'email' => $friendEmail,
                    )
                );

            if (!$friend) {
                $message = $this->get('translator')->trans('api.error.email_invalid', [], 'messages');
                throw new ApiUnauthorizedException($message);
            }

            $isFriendRegisteredAtTheSameEventAsUser = $this
                ->get('app.repository.reservation')
                ->isFriendRegisteredAtTheSameEventAsUser(
                    $member,
                    $friend->getMember(),
                    $event
                );

            if (!$isFriendRegisteredAtTheSameEventAsUser) {
                $message = $this->get('translator')->trans('api.error.user_not_attending', [], 'messages');
                throw new ApiUnauthorizedException($message);
            }

            $member = $friend->getMember();
        }

        return $member;
    }

    /**
     * @param UploadedFile $mediaFile
     * @return Media
     */
    private function createMedia(UploadedFile $mediaFile)
    {
        $media = new Media();
        $media->setBinaryContent($mediaFile);
        $media->setContext('event_photo');
        $media->setEnabled(true);
        $media->setProviderName('sonata.media.provider.image');

        return $media;
    }
}
