<?php

namespace AppBundle\Notification\Event;

use AppBundle\Entity\Authentication;
use Symfony\Component\EventDispatcher\Event;

class RegistrationOauthEvent extends Event
{
    const EVENT_NAME = 'app.event.registration_oauth';

    /**
     * @var Authentication
     */
    private $user;

    /**
     * RegistrationOauth constructor.
     * @param Authentication $user
     */
    public function __construct(Authentication $user)
    {
        $this->user = $user;
    }

    /**
     * @return Authentication
     */
    public function getUser()
    {
        return $this->user;
    }
}