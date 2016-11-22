<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Recipe;
use AppBundle\Entity\Reservation;
use AppBundle\Exception\ReservationManagerException;
use AppBundle\Filter\PaginateFilter;
use AppBundle\Filter\SearchEventFilter;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Form\Type\RecipeType;
use AppBundle\Form\Type\SearchType;
use AppBundle\Model\Application;
use AppBundle\Service\ReservationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{
    /**
     * @Route("/event/list", name="event_list", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $filter = new SearchEventFilter($request->request->all());
            $events = $this->get('app.repository.event')->getFilteredEvents($filter);

            return new JsonResponse(
                array(
                    'finished' => count($events) === 0,
                    'data' => $this->get('templating')->render(
                        ':event:list_items.html.twig',
                        array(
                            'events' => $events,
                            'page' => $filter->getFilter('page'),
                        )
                    ),
                )
            );
        }

        $type = $request->get('type', null);
        $initialData = [];
        if ($type === 'amateur') {
            $initialData = array('participatorType' => Event::STATUS_APPLICANT_REGISTRATION_OPENED);
        }

        if ($type === 'gouteur') {
            $initialData = array('participatorType' => Event::STATUS_RESERVATIONS_OPENED);
        }

        $form = $this->createForm(new SearchType(), $initialData);
        $form->handleRequest($request);

        return $this->render(
            ':event:list.html.twig',
            array(
                'searchType' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/event/details/{id}", name="event_details")
     * @ParamConverter("event", class="AppBundle:Event", options={"repository_method" = "getEvent"})
     *
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction(Request $request, Event $event)
    {
        $user = $this->getUser();

        $modal = $request->query->get('modal');

        $applicantRepository = $this->get('app.repository.applicant');
        if ($event->isVotingPossible()) {
            $applicants = $applicantRepository->getAllApplicants($event);
        } else {
            $applicants = $applicantRepository->getSelectedApplicants($event);
        };

        if ($user && $event->isVotingPossible()) {
            $applicantIds = array_map(
                function (Applicant $applicant) {
                    return $applicant->getId();
                },
                $applicants
            );
            $member = $user->getMember();
            $votedFor = $this->get('app.repository.applicant_vote')->checkMemberVoted($applicantIds, $member);
        } else {
            $votedFor = [];
        }

        if ($user) {
            $member = $user->getMember();
            $reserved = $this->get('app.manager.reservation')->memberHasReservationForEvent($member, $event);
            $applied = $this->get('app.manager.application')->applicationAlreadyExists($event, $member);
        } else {
            $reserved = false;
            $applied = false;
        }


        return $this->render(
            ':event:details.html.twig',
            array(
                'event' => $event,
                'applicants' => $applicants,
                'votedFor' => $votedFor,
                'reserved' => $reserved,
                'applied' => $applied,
                'modal' => $modal
            )
        );
    }

    /**
     * @Route("/event/reserve/{slug}", name="event_reserve", options={"expose"=true})
     *
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reserveAction(Event $event)
    {
        return $this->render(
            ':event:reserve.html.twig',
            array(
                'event' => $event,
            )
        );
    }

    /**
     * @Route("/event/vote", name="event_applicant_vote", options={"expose"=true})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function voteAction(Request $request)
    {
        $user = $this->getUser();

        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException('Not supported');
        }

        if (!$user) {
            throw $this->createAccessDeniedException('Please login to vote');
        }

        $applicantId = $request->request->get('applicantId');
        $applicantRepository = $this->get('app.repository.applicant');
        $applicantVoteRepository = $this->get('app.repository.applicant_vote');

        $applicant = $applicantRepository->find($applicantId);

        /** @var Applicant $applicant */
        if (!$applicantId || !$applicant) {
            throw $this->createNotFoundException('Applicant not found');
        }

        if (!in_array(
            $applicant->getEvent()->getStatus(),
            [
                Event::STATUS_APPLICANT_REGISTRATION_OPENED,
                Event::STATUS_APPLICANT_REGISTRATION_CLOSED,
            ]
        )) {
            throw $this->createAccessDeniedException('Voting is closed');
        }

        $applicantVoteRepository->registerVote($applicant, $user->getMember());

        return new JsonResponse(array(
            'status' => 'voted',
            'voteCount' => $applicant->getNrVotes(),
        ));
    }

    /**
     * @Route("/event/reserve-process/{id}/{places}", name="event_reserve_process", options={"expose"=true})
     * @Method({"POST"})
     *
     * @param Request $request
     * @param Event $event
     * @param int $places
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reserveProcessAction(Request $request, Event $event, $places = 1)
    {
        $member = $this->getUser()->getMember();
        $tableCode = $request->request->get('tableCode', null);

        /**
         * @var ReservationManager $reservationManager
         */
        $reservationManager = $this->get('app.manager.reservation');

        try {
            $paymentForm = $reservationManager->createReservation($member, $event, $tableCode, $places);
        } catch (ReservationManagerException $exception) {
            return new JsonResponse(
                array(
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                )
            );
        }

        return new JsonResponse(
            array(
                'status' => 'success',
                'message' => $paymentForm,
            )
        );
    }

    /**
     * @Route("/event/reserve-cancel/{id}", name="event_reserve_cancel", options={"expose"=true})
     *
     * @param Reservation $reservation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelReservationAction(Reservation $reservation)
    {
        /** @var Member $member */
        $member = $this->getUser()->getMember();

        if ($reservation->getMember()->getId() !== $member->getId()) {
            return $this->createAccessDeniedException();
        }

        /**
         * @var ReservationManager $reservationManager
         */
        $reservationManager = $this->get('app.manager.reservation');
        $reservationManager->cancelReservation($reservation);

        return $this->redirectToRoute('member_profile', ['slug' => $member->getSlug()]);
    }

    /**
     * @Route("/event/apply-to/{id}", name="event_apply_to")
     *
     * @param Request $request
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function applicationAction(Request $request, Event $event)
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        $member = $user->getMember();

        $application = Application::create($member, $event);

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('app.manager.application')->process($application);

            return $this->redirect($this->generateUrl("member_profile", array( "slug" => $member->getSlug())) . "#applicant_success");
        }

        $recipeType = $this->createForm(new RecipeType(), new Recipe());

        return $this->render(
            ':event:apply_to.html.twig',
            array(
                'event' => $event,
                'member' => $member,
                'form' => $form->createView(),
                'recipeType' => $recipeType->createView(),
            )
        );
    }


    /**
     * @Route("/event/photos/{id}", name="event_photo_items", options={"expose"=true})
     *
     * @param Request $request
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function photoAction(Request $request, Event $event)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createAccessDeniedException();
        }

        $filter = new PaginateFilter($request->request->all());
        $photos = $this->get('app.repository.member.event.media')->getEventMediaFiltered($event, $filter);

        return new JsonResponse(
            array(
                'finished' => count($photos) === 0,
                'data' => $this->get('templating')->render(
                    ':event:photo_items.html.twig',
                    array(
                        'photos' => $photos,
                        'page' => $filter->getFilter('page'),
                    )
                ),
            )
        );
    }
}
