<?php

namespace AppBundle\Event\Listener;

use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RouterListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var Router
     */
    private $router;

    /**
     * RouterListener constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Router $router
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        Router $router
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        if ($token instanceof OAuthToken) {
            if ($event->getRequest()->isXmlHttpRequest()) {
                return;
            }

            if ($event->getRequest()->attributes->get('_route') == 'user_oauth_register') {
                return;
            }

            $user = $this->tokenStorage->getToken()->getUser();

            foreach ($user->getRoles() as $role) {
                if ($role == 'ROLE_INCOMPLETE_USER') {
                    $redirect = new RedirectResponse($this->router->generate('user_oauth_register'));
                    $event->setResponse($redirect);
                }
            }
        }
    }
}