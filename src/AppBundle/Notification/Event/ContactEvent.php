<?php

namespace AppBundle\Notification\Event;

use Symfony\Component\EventDispatcher\Event;

final class ContactEvent extends Event
{
    const EVENT_NAME = 'app.event.contact';

    /**
     * @var string
     */
    private $fromEmail;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $fromEmail
     * @param string $subject
     * @param string $message
     */
    public function __construct($fromEmail, $subject, $message)
    {

        $this->fromEmail = $fromEmail;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
