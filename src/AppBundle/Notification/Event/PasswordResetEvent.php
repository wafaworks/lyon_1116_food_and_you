<?php

namespace AppBundle\Notification\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

final class PasswordResetEvent extends BaseEvent
{
    const EVENT_NAME = 'app.event.password_reset';

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
