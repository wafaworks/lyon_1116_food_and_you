<?php

namespace AppBundle\Service;

use AppBundle\Model\Email;

class Mailer
{
    /** @var  \Swift_Mailer */
    protected $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Email $email)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($email->getSubject())
            ->setFrom($email->getSender())
            ->setTo($email->getRecipient())
            ->setBody(
                $email->getBody(),
                'text/html'
            )
            ->addPart(
                $email->getPlainBody(),
                'text/plain'
            )
        ;

        if ($email->getReplyTo()) {
            $message->setReplyTo($email->getReplyTo());
        }

        return $this->mailer->send($message);
    }
}
