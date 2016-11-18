<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseResettingController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller managing the resetting of the password
 */
class ResettingController extends BaseResettingController
{
    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return new JsonResponse(
                array(
                    'status' => 'error',
                    'errors' => array(
                        'global' => array($this->get('translator')->trans(
                            'resetting.request.invalid_username',
                            array('%username%' => $username),
                            'FOSUserBundle'
                        )),
                        'fields' => array(),
                    ),
                )
            );
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new JsonResponse(
                array(
                    'status' => 'error',
                    'errors' => array(
                        'global' => array($this->get('translator')->trans(
                            'resetting.password_already_requested',
                            array('%username%' => $username),
                            'FOSUserBundle'
                        )),
                        'fields' => array(),
                    ),
                )
            );
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);


        return new JsonResponse(
            array(
                'status' => 'success',
                'message' => $this->get('translator')->trans(
                    'resetting.check_email',
                    array('%email%' => $this->getObfuscatedEmail($user)),
                    'FOSUserBundle'
                ),
            )
        );
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('homepage'));
        }

        return $this->render('FOSUserBundle:Resetting:checkEmail.html.twig', array(
            'email' => $email,
        ));
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(
                    array(
                        'status' => 'error',
                        'errors' => array(
                            'global' => array($this->get('translator')->trans(
                                'resetting.flash.invalid_token',
                                array('%token%' => $token),
                                'FOSUserBundle'
                            )),
                            'fields' => array(),
                        ),
                    )
                );
            } else {
                return new RedirectResponse($this->get('router')->generate('homepage'));
            }
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(
                    array(
                        'status' => 'error',
                        'errors' => array(
                            'global' => array(
                                $this->get('translator')->trans(
                                    'resetting.flash.error',
                                    array(),
                                    'FOSUserBundle'
                                )
                            ),
                            'fields' => array(),
                        ),
                    )
                );
            } else {
                return $event->getResponse();
            }
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if ($request->isXmlHttpRequest()) {
                $response = new JsonResponse(
                    array(
                        'status' => 'success',
                        'message' => $this->get('translator')->trans(
                            'resetting.flash.success',
                            array(),
                            'FOSUserBundle'
                        ),
                    )
                );
            } else {
                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }
            }

            $dispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $response;
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array());
        }

        return $this->render('FOSUserBundle:Resetting:reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }
}