<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\PasswordResetEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PasswordResetEventListener
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $sender;

    /**
     * ContactEventListener constructor.
     * @param Mailer $mailer
     * @param EngineInterface $templating
     * @param TranslatorInterface $translator
     * @param $sender
     */
    public function __construct(Mailer $mailer, EngineInterface $templating, TranslatorInterface $translator, $sender)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->sender = $sender;
    }

    /**
     * @param PasswordResetEvent $event
     */
    public function handle(PasswordResetEvent $event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param PasswordResetEvent $event
     * @return Email
     */
    protected function generateEmail(PasswordResetEvent $event)
    {
        $email = new Email();
        $email->setSender($this->sender);
        $email->setRecipient($event->getUser()->getEmail());
        $email->setSubject($this->translator->trans('password_reset.subject', [], 'email'));
        $email->setReplyTo($this->sender);
        $email->setBody($this->templating->render(
            ':email:password_reset.html.twig',
            array(
                'user' => $event->getUser(),
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:password_reset.txt.twig',
            array(
                'user' => $event->getUser(),
            )
        ));

        return $email;
    }
}
