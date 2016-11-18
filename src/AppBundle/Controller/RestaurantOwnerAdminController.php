<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Member;
use AppBundle\Entity\Reservation;
use AppBundle\Form\Type\NewEventType;
use AppBundle\Form\Type\RestaurantType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/restaurateur")
 * @Security("has_role('ROLE_OWNER')")
 * Class RestaurantOwnerAdminController
 */
class RestaurantOwnerAdminController extends Controller
{
    const PAGINATION_LIMIT = 5;

    /**
     * @Route(path="/events/list/{page}", name="restaurant_owner_admin_event_list")
     * @Template(template="restaurant_owner_admin/list.html.twig")
     *
     * @param $page
     * @param Request $request
     * @internal param int $page
     *
     * @return array
     */
    public function listAction(Request $request, $page = 1)
    {
        /** @var Member $member */
        $member = $this->getUser()->getMember();

        $query = $this->get('app.repository.event')->getRestaurantOwnerEventsQuery($member, $request);

        $restaurant = count($member->getRestaurants()) === 1 ? $member->getRestaurants()->first() : null;

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            self::PAGINATION_LIMIT
        );

        return array(
            'restaurant' => $restaurant,
            'events' => $pagination,
        );
    }

    /**
     * @Route(path="/edit-capacity/{event}", name="restaurant_owner_admin_edit_capacity", options={"expose"=true},
     *     condition="request.isXmlHttpRequest()")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function editCapacityForEvent(Request $request, Event $event)
    {
        $member = $this->getUser()->getMember();

        if (!$this->isEventCreatedByMember($event, $member)) {
            throw new AccessDeniedException();
        }

        $event->getCapacity()->setMaximum($request->request->get('max'));
        $event->getCapacity()->setMinimum($request->request->get('min'));

        $this->get('app.repository.event')->editCapacity($event, $request);

        return new JsonResponse();
    }

    /**
     * @Route(path="/applicants/list/{event}", name="restaurant_owner_admin_applicants_list", options={"expose"=true},
     *     condition="request.isXmlHttpRequest()")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return string
     */
    public function listApplicantsAction(Request $request, Event $event)
    {
        $direction = $request->query->get('direction', 'DESC');
        $applicants = $this
            ->get('app.repository.applicant_recipe')
            ->getBoApplicants($event, $direction)
        ;

        return $this->render(
            ':restaurant_owner_admin:applicants_list.html.twig',
            array(
                'applicantRecipes' => $applicants,
                'event'      => $event,
            )
        );
    }

    /**
     * @Route(
     *     path="/reservations/list/{event}",
     *     name="restaurant_owner_admin_reservations_list",
     *     options={"expose"=true},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return Response
     */
    public function listReservationsAction(Request $request, Event $event)
    {
        $member = $this->getUser()->getMember();

        if (!$this->isEventCreatedByMember($event, $member)) {
            throw new AccessDeniedException();
        }

        $reservations = $this->get('app.repository.reservation')->findAllByEvent($event, [Reservation::STATUS_CONFIRMED, Reservation::STATUS_TO_REFUND, Reservation::STATUS_REFUNDED]);

        return new Response($this->renderView(':restaurant_owner_admin:reservations_list.html.twig', array(
            'reservations' => $reservations,
            'event'        => $event,
        )));
    }

    /**
     * @Route(path="/applicants/save/{event}", name="restaurant_owner_admin_applicants_save", options={"expose"=true},
     *     condition="request.isXmlHttpRequest()")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return string
     */
    public function saveApplicants(Request $request, Event $event)
    {
        $member = $this->getUser()->getMember();

        if (!$this->isEventCreatedByMember($event, $member)) {
            throw new AccessDeniedException();
        }

        $this
            ->get('app.manager.application_recipe')
            ->markSelectedRecipes($event, $request->request->get('ids', []))
        ;

        if ($event->getStatus() !== Event::STATUS_RESERVATIONS_OPENED) {
            $event->setStatus(Event::STATUS_RESERVATIONS_OPENED);
            $this->get('app.repository.event')->save($event);
        }

        return $this->listApplicantsAction($request, $event);
    }

    /**
     * @Route(path="/event/close/{event}", name="restaurant_owner_admin_event_close", options={"expose"=true},
     *     condition="request.isXmlHttpRequest()")
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return string
     */
    public function closeEvent(Request $request, Event $event)
    {
        $member = $this->getUser()->getMember();

        if (!$this->isEventCreatedByMember($event, $member)) {
            throw new AccessDeniedException();
        }

        $recipeRepository = $this->get('app.repository.applicant_recipe');
        $ratingRepository = $this->get('app.repository.applicant_recipe_rating');

        $applicantRecipeIds = $recipeRepository->getUnaccountedRecipeIds($event);

        foreach ($applicantRecipeIds as $recipeId) {
            $ratingRepository->updateRating($recipeId);
        }

        $this->get('app.repository.applicant_recipe')->chooseWinner($event);

        $this->get('app.repository.event')->closeEvent($event);

        return $this->listApplicantsAction($request, $event);
    }

    /**
     * @Route("/event/create", name="restaurant_owner_admin_event_new", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Template(template="restaurant_owner_admin/event_new.html.twig")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newEventAction(Request $request)
    {
        $member = $this->getUser()->getMember();
        $eventManager = $this->get('app.manager.event');
        $event = $eventManager->getNewEvent($member);

        $form = $this->createForm(NewEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $eventManager->process($event);

            return $this->redirectToRoute('restaurant_owner_admin_event_list');
        }

        return array(
            'member' => $member,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route(
     *     path="/reservations/cancel/{event}",
     *     name="restaurant_owner_admin_reservations_cancel",
     *     options={"expose"=true},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @Method(methods={"POST"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return Response
     */
    public function cancelReservationsAction(Request $request, Event $event)
    {
        $member = $this->getUser()->getMember();

        if (!$this->isEventCreatedByMember($event, $member)) {
            throw new AccessDeniedException();
        }

        $reservationsIds = $this->get('app.repository.reservation')->getReservationIdsByEvent($event);

        foreach ($request->request->all() as $reservationId) {
            if (in_array($reservationId, $reservationsIds)) {
                /** @var Reservation $reservation */
                $reservation = $this->get('app.repository.reservation')->find($reservationId);
                $this->get('app.manager.reservation')->cancelReservation($reservation);
            }
        }

        return $this->listReservationsAction($request, $event);
    }

    /**
     * @param Event $event
     * @param $member
     * @return mixed
     */
    private function isEventCreatedByMember(Event $event, $member)
    {
        return $this->get('app.repository.event')->isEventCreatedByMember($event, $member);
    }

    /**
     * @Route(
     *     path="/restaurant/edit",
     *     name="restaurant_owner_restaurant_edit",
     *     options={"expose"=true},
     * )
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function editRestaurantAction(Request $request)
    {
        /** @var Authentication $user */
        $user = $this->getUser();
        /** @var Member $member */
        $member = $user->getMember();

        $restaurantManager = $this->get('app.manager.restaurant');
        $restaurant = $restaurantManager->getMemberRestaurant($member);

        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $restaurantManager->process($restaurant);

            return $this->redirectToRoute('restaurant_owner_admin_event_list');
        }

        return $this->render(
            ':restaurant_owner_admin:restaurant_create.html.twig',
            array(
                'member' => $member,
                'form' => $form->createView(),
                'admin' => true,
            )
        );
    }
}
