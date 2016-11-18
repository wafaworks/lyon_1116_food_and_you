<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Event\ReservationConfirmedEvent;
use AppBundle\Model\Email;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ReservationConfirmedEventListener
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
    private $fromEmail;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ContactEventListener constructor.
     * @param Mailer $mailer
     * @param EngineInterface $templating
     * @param TranslatorInterface $translator
     * @param $fromEmail
     */
    public function __construct(Mailer $mailer, EngineInterface $templating, TranslatorInterface $translator, $fromEmail)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->fromEmail = $fromEmail;
        $this->translator = $translator;
    }

    /**
     * @param ReservationConfirmedEvent $event
     */
    public function handle(ReservationConfirmedEvent $event)
    {
        if (!$event->getReservation()->isConfirmationSent()) {
            $email = $this->generateEmail($event);

            $this->mailer->send($email);
        }
    }

    /**
     * @param ReservationConfirmedEvent $event
     * @return Email
     */
    protected function generateEmail(ReservationConfirmedEvent $event)
    {
        $email = new Email();
        $email->setSender($this->fromEmail);
        $email->setRecipient($event->getReservation()->getMember()->getAuthentication()->getEmail());
        $email->setSubject($this->translator->trans('reservation_confirmed.subject', array(), 'email'));
        $email->setReplyTo($this->fromEmail);
        $email->setBody($this->templating->render(
            ':email:reservation_confirmed.html.twig',
            array(
                'reservation' => $event->getReservation()
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:reservation_confirmed.txt.twig',
            array(
                'reservation' => $event->getReservation()
            )
        ));

        return $email;
    }
}
