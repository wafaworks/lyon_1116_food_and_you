<?php

namespace AppBundle\Notification\Subscriber;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\RegistrationOauthEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RegistrationSubscriber implements EventSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            RegistrationOauthEvent::EVENT_NAME => 'onRegistrationCompleted',
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    /**
     * @param FilterUserResponseEvent | RegistrationOauthEvent $event
     */
    public function onRegistrationCompleted($event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param FilterUserResponseEvent | RegistrationOauthEvent $event
     * @return Email
     */
    protected function generateEmail($event)
    {
        $email = new Email();
        $email->setSender($this->sender);
        $email->setRecipient($event->getUser()->getEmail());
        $email->setSubject($this->translator->trans('user_registered.subject', array(), 'email'));
        $email->setReplyTo($this->sender);
        $email->setBody($this->templating->render(
            ':email:user_registered.html.twig',
            array(
                'user' => $event->getUser(),
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:user_registered.txt.twig',
            array(
                'user' => $event->getUser(),
            )
        ));

        return $email;
    }
}