<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\Event;
use AppBundle\Entity\Restaurant;
use AppBundle\Form\Type\ContactCompanyType;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\SearchType;
use AppBundle\Model\ContactCompany;
use AppBundle\Model\Email;
use AppBundle\Notification\Event\ContactEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Cocur\Slugify\Slugify;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template(template="default/index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $homeVideos = array(
            'BANDEAUWEB1',
            'BANDEAUWEBBONUS',
            'BANDEAUWEB6',
            'BANDEAUWEB5',
            'BANDEAUWEB4',
            'BANDEAUWEB10',
            'BANDEAUWEB9',
        );

        $blogPost = $this->get('xaben.service.blog_provider')->getPosts(1);

        $upcomingEvents = $this->get('app.repository.event')->getUpcomingEvents();

        return array(
            'blogPost' => $blogPost,
            'homeVideos' => $homeVideos,
            'upcomingEvents' => $upcomingEvents,
            'searchType' => $this->createForm(new SearchType())->createView(),
        );
    }

    /**
     * Devenir restaurateur page
     *
     * @Route("/restaurateur/accueil", name="restaurateur_benefits")
     */
    public function benefitsAction()
    {
        return $this->render(
            ':default:benefits.html.twig',
            array()
        );
    }

    /**
     * Simulateur CA
     *
     * @Route("/restaurateur/simulateur", name="restaurateur_simulateur")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function calculatorAction()
    {
        $user = $this->getUser();

        if ($user) {
            $member = $user->getMember();
            $restaurantRepository = $this->get('app.repository.restaurant');

            $ownedRestaurants = $restaurantRepository->getOwnedRestaurants($member);
        } else {
            $ownedRestaurants = [];
        }

        return $this->render(
            ':default:calculator.html.twig',
            array(
                'isOwner' => count($ownedRestaurants) > 0,
            )
        );
    }

    /**
     * @Route(path="/cgu", name="cgu")
     */
    public function cguAction()
    {
        return $this->render(':default:cgu.html.twig');
    }

    /**
     * @Route(path="/regelement_du_jeu ", name="rules")
     */
    public function rulesAction()
    {
        return $this->render(':default:rules.html.twig');
    }

    /**
     * @Route(path="/qui_sommes_nous", name="about_us")
     */
    public function aboutUsAction()
    {
        return $this->render(':default:about_us.html.twig');
    }

    /**
     * @Route(path="/charte", name="charte")
     */
    public function charteAction()
    {
        return $this->render(':default:charte.html.twig');
    }

    /**
     * @Route(path="/contact", name="contact")
     *
     * @param Request $request
     *
     * @return array
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(new ContactType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('event_dispatcher')->dispatch(
                ContactEvent::EVENT_NAME,
                new ContactEvent(
                    $form->get('mail')->getData(),
                    $form->get('subject')->getData(),
                    $form->get('message')->getData()
                )
            );

            return new JsonResponse(
                array(
                    'status' => 'success'
                )
            );
        }

        $errors = $this->get('app.form.error_serializer')->serializeFormErrors($form);

        return new JsonResponse(
            array(
                'status' => 'error',
                'errors' => $errors,
            )
        );
    }

    /**
     * @Route(path="/contact-company", name="contact_company")
     *
     * @param Request $request
     *
     * @return array
     */
    public function contactCompanyAction(Request $request)
    {
        $form = $this->createForm(new ContactCompanyType(), new ContactCompany());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $email = new Email();
            $email->setSender($form->get('email')->getData());
            $email->setRecipient($this->getParameter('contact_company_email'));
            $email->setSubject($this->get('translator')->trans('contact_company.subject', array(), 'email'));
            $email->setBody($this->render(
                ':email:contact_company.html.twig',
                array(
                    'form' => $form->getData(),
                )
            ));
            $email->setPlainBody($this->render(
                ':email:contact_company.html.twig',
                array(
                    'form' => $form->getData(),
                )
            ));

            $this->get('app.service.mailer')->send($email);

            return new JsonResponse(
                array(
                    'status' => 'success',
                )
            );
        }

        $errors = $this->get('app.form.error_serializer')->serializeFormErrors($form);

        return new JsonResponse(
            array(
                'status' => 'error',
                'errors' => $errors,
            )
        );
    }

    /**
     * @Route(path="/restaurants/list", name="restaurants_list", options={"expose"=true})
     *
     * @param Request $request
     * @return string
     */
    public function listRestaurantsAction(Request $request)
    {
        $restaurantsByCity = $this->get('app.repository.city')->getRestaurants();

        $list = [];
        $i = 0;
        $isFirst = true;

        if ($restaurantsByCity) {
            /** @var City $city */
            foreach ($restaurantsByCity as $city) {
                $index = $city->getName();
                /** @var Restaurant $restaurant */
                foreach ($city->getRestaurants() as $restaurant) {
                    if ($isFirst) {
                        $firstRestaurant = $restaurant;
                    }
                    $list[$index][$i][] = $restaurant;

                    if (count($list[$index][$i]) == 8) {
                        $i++;
                    }
                    $isFirst = false;
                }

            }

            return $this->render(':partials/header:list-restaurants-body.html.twig', array(
                'list' => $list,
                'firstRestaurant' => $firstRestaurant
            ));
        }
    }

    /**
     * @Route(path="/email-test", name="email_test")
     *
     */
    public function emailAction()
    {
        //TODO: remove after testing
        return $this->render(':email:layout.html.twig');
    }

}
