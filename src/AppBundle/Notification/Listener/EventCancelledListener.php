<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Entity\Applicant;
use AppBundle\Entity\Event;
use AppBundle\Model\Email;
use AppBundle\Notification\Event\EventCancelled;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EventCancelledListener
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
     */
    public function __construct(Mailer $mailer, EngineInterface $templating, TranslatorInterface $translator, $sender)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->sender = $sender;
    }

    /**
     * @param EventCancelled $event
     */
    public function handle(EventCancelled $event)
    {
        foreach ($event->getEvent()->getApplicants() as $applicant) {
            $email = $this->generateEmail($event->getEvent(), $applicant);

            $this->mailer->send($email);
        }
    }

    /**
     * @param Event $event
     * @param Applicant $applicant
     *
     * @return Email
     */
    protected function generateEmail(Event $event, Applicant $applicant)
    {
        $email = new Email();
        $email->setSender($this->sender);
        $email->setRecipient($applicant->getMember()->getAuthentication()->getEmail());
        $email->setSubject($this->translator->trans('event_cancelled.subject', array(), 'email'));
        $email->setReplyTo($this->sender);
        $email->setBody($this->templating->render(
            ':email:event_cancelled.html.twig',
            array(
                'event' => $event,
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:event_cancelled.txt.twig',
            array(
                'event' => $event,
            )
        ));

        return $email;
    }
}
