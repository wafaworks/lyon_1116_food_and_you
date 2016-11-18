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
use Symfony\Component\Routing\RouterInterface;

class MemberEditListener
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;


    /**
     * MemberEditListener constructor.
     * @param FactoryInterface $formFactory
     * @param UserManagerInterface $userManager
     * @param EventDispatcherInterface $dispatcher
     * @param RouterInterface $router
     * @param FormErrorSerializer $serializer
     */
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
     */
    public function onProfileEdit(UserEvent $baseEvent)
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
                $this->dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $this->userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->router->generate('member_profile', array(
                        'slug' => $user->getMember()->getSlug(),
                    ));

                    $response = new JsonResponse(
                        array(
                            'status' => 'success',
                            'redirect' => $url,
                        )
                    );
                }

                $baseEvent->setResponse($response);

                $this->dispatcher->dispatch(
                    FOSUserEvents::PROFILE_EDIT_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response)
                );
            } elseif ($form->isSubmitted()) {
                $errors = $this->serializer->serializeFormErrors($form);
                $response = new JsonResponse(
                    array(
                        'status' => 'error',
                        'errors' => $errors,
                    )
                );

                $baseEvent->setResponse($response);
            }
        }
    }
}
