<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Authentication;
use AppBundle\Entity\Member;
use AppBundle\Entity\Restaurant;
use AppBundle\Form\Type\RestaurantType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestaurantController extends Controller
{
    /**
     * @param $request Request
     *
     * @Route("/restaurants/name/list", name="restaurants_name_list", options={"expose"=true})
     *
     * @return JsonResponse
     */
    public function restaurantsNameListAction(Request $request)
    {
        $restaurants = $this
            ->get('app.repository.restaurant')
            ->searchByLetters(
                $request->get('query', null),
                $request->get('city', 0)
            );

        return new JsonResponse($restaurants);
    }

    /**
     * @param $request Request
     *
     * @Route("/restaurants/create", name="restaurant_create")
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function newAction(Request $request)
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

            $user->addRole('ROLE_OWNER');
            $this->get('app.repository.authentication')->save($user);

            return $this->redirectToRoute('restaurant_owner_admin_event_new');
        }

        return $this->render(
            ':restaurant:create.html.twig',
            array(
                'member' => $member,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param $request Request
     * @param Restaurant $restaurant
     *
     * @return Response
     *
     * @Route("/restaurants/details/{id}", name="restaurant_details")
     *
     */
    public function detailsAction(Request $request, Restaurant $restaurant)
    {
        $upcomingEvents = $this
            ->get('app.repository.event')
            ->getUpcomingEvents($restaurant)
        ;

        return $this->render(
            ':restaurant:details.html.twig',
            array(
                'restaurant' => $restaurant,
                'upcomingEvents' => $upcomingEvents,
            )
        );
    }
}
