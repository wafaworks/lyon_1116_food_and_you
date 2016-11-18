<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Authentication;
use AppBundle\Entity\Member;
use AppBundle\Form\Type\EditIncompleteAuthenticationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MemberController
 * @package AppBundle\Controller
 */
class MemberController extends Controller
{
    /**
     * @Route(path="/foodie/{slug}", name="member_profile")
     * @ParamConverter("member", options={"mapping": {"slug": "slug"}})
     *
     * @param Request $request
     * @param Member $member
     *
     * @return Response
     */
    public function profileAction(Request $request, Member $member)
    {
        $modal = $request->query->get('modal');
        $reservationId = $request->query->get('reservationId', 0);
        $currentMember = $this->getUser() ? $this->getUser()->getMember() : null;
        $eventRepository = $this->get('app.repository.event');
        $nextReservedEvents = $eventRepository->getFutureEventsReservationBy($member);
        $pastReservedEvents = $eventRepository->getPastEventsReservationBy($member);
        $givenRates = $this
            ->get('app.repository.applicant_recipe_rating')
            ->getMemberRates($member);

        $openedEvents = $eventRepository->getOpenedEventsByMember($member);

        $eventsWhereMemberCooked = $eventRepository->getEventsWhereMemberCooked($member);
        $getEventsWhereMemberWillCook = $eventRepository->getEventsWhereMemberWillCook($member);

        $votedFor = $currentMember ? $this->get('app.repository.applicant_vote')->checkVotes($member, $currentMember) : [];

        return $this->render(
            ':member:profile.html.twig',
            array(
                'member' => $member,
                'nextReservedEvents' => $nextReservedEvents,
                'pastReservedEvents' => $pastReservedEvents,
                'givenRates' => $givenRates,
                'openedEvents' => $openedEvents,
                'eventsWhereMemberCooked' => $eventsWhereMemberCooked,
                'getEventsWhereMemberWillCook' => $getEventsWhereMemberWillCook,
                'votedFor' => $votedFor,
                'modal' => $modal,
                'reservationId' => $reservationId
            )
        );
    }

    /**
     * Return key value pair id, first + last name for Postulez cook with
     *
     * @Route(path="/utilisateur/search", name="member_search", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $userRepository = $this->get('app.repository.member');
        $query = $request->get('query');

        if (!$query) {
            return new JsonResponse([]);
        }

        $labels = $userRepository->getMembersWithNameLike($query);

        $result = array_map(
            function ($label) {
                return array(
                    'text' => $label['firstName'] . ' ' . $label['lastName'],
                    'id' => $label['id'],
                );
            },
            $labels
        );

        return new JsonResponse([
            'items' => $result,
            'results' => count($result),
        ]);
    }

    /**
     * Return first name and last name by ID
     *
     * @Route(path="/utilisateur/search-id", name="member_search_id", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return Response
     */
    public function searchIdAction(Request $request)
    {
        $userRepository = $this->get('app.repository.member');
        $id = $request->get('id');

        if (!$id) {
            return new JsonResponse([]);
        }

        /** @var Member $member */
        $member = $userRepository->find($id);

        return new JsonResponse([
            'id' => $id,
            'text' => $member->getFirstName() . ' ' . $member->getLastName()
        ]);
    }

    /**
     *
     * @Route(path="/user/exist", name="user_exist")
     *
     * @param Request $request
     * @return Response
     */
    public function memberAlreadyExistAction(Request $request)
    {
        $user = $this->get('app.repository.authentication')->findOneBy(array(
            'email' => $request->request->get("email")
        ));

        if ($user) {
            return new JsonResponse(array(
                $this->get('translator')->trans('fos_user.email.exist', array(), 'validators')
            ));
        }

        return new JsonResponse("true");
    }

    /**
     * @Route(path="/user/oauth/register", name="user_oauth_register")
     *
     * @param Request $request
     * @return Response
     */
    public function registerOauthWithoutEmail(Request $request)
    {
        /** @var Authentication $user */
        $user = $this->getUser();
        $form = $this->createForm(new EditIncompleteAuthenticationType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->removeRole('ROLE_INCOMPLETE_USER');
            $this->get('app.repository.authentication')->save($user);

            $this->get('security.token_storage')->getToken()->getRoles();

            return $this->redirect($this->generateUrl('homepage'));
        }
        return $this->render('member/incomplete_profile.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
