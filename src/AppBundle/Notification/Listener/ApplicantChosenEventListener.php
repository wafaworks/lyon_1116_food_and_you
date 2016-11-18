<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\ApplicantChosenEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ApplicantChosenEventListener
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
     * @param ApplicantChosenEvent $event
     */
    public function handle(ApplicantChosenEvent $event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param ApplicantChosenEvent $event
     * @return Email
     */
    protected function generateEmail(ApplicantChosenEvent $event)
    {
        $email = new Email();
        $email->setSender($this->fromEmail);
        $email->setRecipient($event->getApplicant()->getMember()->getAuthentication()->getEmail());
        $email->setSubject($this->translator->trans('applicant_chosen.subject', array(), 'email'));
        $email->setReplyTo($this->fromEmail);
        $email->setBody($this->templating->render(
            ':email:applicant_chosen.html.twig',
            array(
                'applicant' => $event->getApplicant()
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:applicant_chosen.txt.twig',
            array(
                'applicant' => $event->getApplicant()
            )
        ));

        return $email;
    }
}
