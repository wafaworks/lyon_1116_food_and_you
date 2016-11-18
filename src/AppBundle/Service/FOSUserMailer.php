<?php

namespace AppBundle\Service;

use AppBundle\Notification\Event\PasswordResetEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FOSUserMailer implements MailerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        // do nothing as confirmation is not enabled, welcome email is sent on register success
    }

    /**
     * @inheritdoc
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $this->dispatcher->dispatch(
            PasswordResetEvent::EVENT_NAME,
            new PasswordResetEvent($user)
        );
    }
}
