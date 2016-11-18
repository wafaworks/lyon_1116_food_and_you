<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\MinCapacityNotReachedEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MinCapacityNotReachedListener
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
     * MinCapacityNotReachedListener constructor.
     *
     * @param Mailer $mailer
     * @param EngineInterface $templating
     * @param TranslatorInterface $translator
     * @param $sender
     * @param $admins
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
     * @param MinCapacityNotReachedEvent $event
     */
    public function handle(MinCapacityNotReachedEvent $event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param MinCapacityNotReachedEvent $event
     * @return Email
     */
    protected function generateEmail(MinCapacityNotReachedEvent $event)
    {
        $email = new Email();
        $email->setSender($this->sender);
        $email->setRecipient($this->admins);
        $email->setSubject($this->translator->trans('min_capacity_not_reached.subject', array(), 'email'));
        $email->setReplyTo($this->sender);
        $email->setBody($this->templating->render(
            ':email:min_capacity_not_reached.html.twig',
            array(
                'event' => $event->getEvent()
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:min_capacity_not_reached.txt.twig',
            array(
                'event' => $event->getEvent()
            )
        ));

        return $email;
    }
}
