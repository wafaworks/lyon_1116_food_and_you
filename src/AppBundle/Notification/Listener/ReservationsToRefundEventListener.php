<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\ReservationsToRefundEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ReservationsToRefundEventListener
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
     * @var array
     */
    private $admins;

    /**
     * ContactEventListener constructor.
     * @param Mailer $mailer
     * @param EngineInterface $templating
     * @param TranslatorInterface $translator
     * @param string $sender
     * @param array $admins
     */
    public function __construct(Mailer $mailer, EngineInterface $templating, TranslatorInterface $translator, $sender, $admins)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->sender = $sender;
        $this->admins = $admins;
    }

    /**
     * @param ReservationsToRefundEvent $event
     */
    public function handle(ReservationsToRefundEvent $event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param ReservationsToRefundEvent $event
     * @return Email
     */
    protected function generateEmail(ReservationsToRefundEvent $event)
    {
        $email = new Email();
        $email->setSender($this->sender);
        $email->setRecipient($this->admins);
        $email->setSubject($this->translator->trans('reservation_to_refund.subject', array(), 'email'));
        $email->setReplyTo($this->sender);
        $email->setBody($this->templating->render(
            ':email:reservation_refund.html.twig',
            array(
                'count' => $event->getCount(),
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:reservation_refund.txt.twig',
            array(
                'count' => $event->getCount(),
            )
        ));

        return $email;
    }
}
