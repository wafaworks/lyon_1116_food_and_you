<?php

namespace AppBundle\Event\Listener;

use AppBundle\Form\FormErrorSerializer;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class RegistrationListener
{
    /**
     * @var FactoryInterface
     */
    private $formFactory;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormErrorSerializer
     */
    private $serializer;

    public function __construct(
        FactoryInterface $formFactory,
        UserManagerInterface $userManager,
        EventDispatcherInterface $dispatcher,
        RouterInterface $router,
        FormErrorSerializer $serializer
    ) {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
        $this->router = $router;
        $this->serializer = $serializer;
    }

    /**
     * @param UserEvent $baseEvent
     * @return null|Response
     */
    public function onRegistrationInitialise(UserEvent $baseEvent)
    {
        $request = $baseEvent->getRequest();
        $user = $baseEvent->getUser();
        $response = null;

        if ($request->isXmlHttpRequest()) {
            $form = $this->formFactory->createForm();
            $form->setData($user);

            $form->handleRequest($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $this->userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    //TODO: modify to needed registration step
                    $url = $this->router->generate('fos_user_registration_confirmed');
                    $response = new JsonResponse(
                        array(
                            'status' => 'success',
                            'redirect' => $url
                        )
                    );
                }

                $baseEvent->setResponse($response);

                $this->dispatcher->dispatch(
                    FOSUserEvents::REGISTRATION_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response)
                );
            } elseif ($form->isSubmitted()) {
                $errors = $this->serializer->serializeFormErrors($form);
                $response = new JsonResponse(
                    array(
                        'status' => 'error',
                        'errors' => $errors
                    )
                );

                $baseEvent->setResponse($response);
            }
        }
    }
}
