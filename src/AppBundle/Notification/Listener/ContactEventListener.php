<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\ContactEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ContactEventListener
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string
     */
    private $toEmail;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ContactEventListener constructor.
     * @param Mailer $mailer
     * @param EngineInterface $templating
     * @param TranslatorInterface $translator
     * @param string $toEmail
     */
    public function __construct(Mailer $mailer, EngineInterface $templating, TranslatorInterface $translator, $toEmail)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->toEmail = $toEmail;
        $this->translator = $translator;
    }

    /**
     * @param ContactEvent $event
     */
    public function handle(ContactEvent $event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param ContactEvent $event
     * @return Email
     */
    protected function generateEmail(ContactEvent $event)
    {
        $email = new Email();
        $email->setSender($event->getFromEmail());
        $email->setRecipient($this->toEmail);
        $email->setSubject($this->translator->trans('contact.subject', array(), 'email'));
        $email->setReplyTo($event->getFromEmail());
        $email->setBody($this->templating->render(
            ':email:contact.html.twig',
            array(
                'subject' => $event->getSubject(),
                'message' => $event->getMessage(),
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:contact.txt.twig',
            array(
                'subject' => $event->getSubject(),
                'message' => $event->getMessage(),
            )
        ));

        return $email;
    }
}
