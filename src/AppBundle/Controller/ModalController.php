<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reservation;
use AppBundle\Form\Type\ContactCompanyType;
use AppBundle\Form\Type\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ModalController extends Controller
{
    private $simpleTemplates = array(
        'video-homepage',
        'member-edit',
        'legal-notice',
        'reservation-cancelled',
        'applicant-success',
        'info-table-code',
    );

    /**
     * @Route("/modal/basic/{template}", name="modal_simple", options={"expose"=true})
     *
     * @param $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function simpleAction($template)
    {
        if (!in_array($template, $this->simpleTemplates)) {
            return $this->createNotFoundException();
        }

        $template = str_replace('-', '_', $template);

        return $this->render(':modal:' . $template . '.html.twig');
    }

    /**
     * @Route("/modal/login", name="modal_login", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        return $this->render(':modal:login.html.twig');
    }

    /**
     * @Route(path="/modal/contact", name="modal_contact", options={"expose"=true})
     *
     * @param Request $request
     *
     * @return string
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(new ContactType());

        return $this->render(':modal:contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route(path="/modal/contact-company", name="modal_contact_company", options={"expose"=true})
     *
     * @param Request $request
     *
     * @return string
     */
    public function contactCompanyAction(Request $request)
    {
        $form = $this->createForm(new ContactCompanyType());

        return $this->render(':modal:contact-company.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/modal/resetting_request", name="modal_resetting_request", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resettingRequestAction()
    {
        return $this->render(':modal:resetting_request.html.twig');
    }

    /**
     * @Route("/modal/reservation-confirmed/{id}", name="modal_reservation_confirmed", options={"expose"=true})
     *
     * @param Reservation $reservation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reservationConfirmedAction(Reservation $reservation)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->createAccessDeniedException();
        }

        $member = $user->getMember();

        return $this->render(':modal:reservation_confirmed.html.twig', ['member' => $member, 'reservation' => $reservation]);
    }
}
