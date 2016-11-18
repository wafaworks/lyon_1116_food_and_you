<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Event\ReservationCancelledEvent;
use AppBundle\Model\Email;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ReservationCancelledEventListener
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
     * @param ReservationCancelledEvent $event
     */
    public function handle(ReservationCancelledEvent $event)
    {
        $email = $this->generateEmail($event);
        $this->mailer->send($email);

        $restaurateurEmail = $this->generateRestaurateurEmail($event);
        if ($restaurateurEmail instanceof Email) {
            $this->mailer->send($restaurateurEmail);
        }
    }

    /**
     * @param ReservationCancelledEvent $event
     * @return Email
     */
    protected function generateEmail(ReservationCancelledEvent $event)
    {
        $email = new Email();
        $email->setSender($this->fromEmail);
        $email->setRecipient($event->getReservation()->getMember()->getAuthentication()->getEmail());
        $email->setSubject($this->translator->trans('reservation_cancelled.subject', array(), 'email'));
        $email->setReplyTo($this->fromEmail);
        $email->setBody($this->templating->render(
            ':email:reservation_cancelled.html.twig',
            array(
                'reservation' => $event->getReservation()
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:reservation_cancelled.txt.twig',
            array(
                'reservation' => $event->getReservation()
            )
        ));

        return $email;
    }

    /**
     * @param ReservationCancelledEvent $event
     * @return Email|null
     */
    protected function generateRestaurateurEmail(ReservationCancelledEvent $event)
    {
        try {
            $email = new Email();
            $email->setSender($this->fromEmail);
            $email->setRecipient($event->getReservation()->getEvent()->getRestaurant()->getOwner()->getAuthentication()->getEmail());
            $email->setSubject($this->translator->trans('reservation_cancelled.subject', array(), 'email'));
            $email->setReplyTo($this->fromEmail);
            $email->setBody($this->templating->render(
                ':email:reservation_cancelled_restaurateur.html.twig',
                array(
                    'reservation' => $event->getReservation()
                )
            ));
            $email->setPlainBody($this->templating->render(
                ':email:reservation_cancelled_restaurateur.txt.twig',
                array(
                    'reservation' => $event->getReservation()
                )
            ));
        } catch(\Exception $e){
            return null;
        }

        return $email;
    }
}
