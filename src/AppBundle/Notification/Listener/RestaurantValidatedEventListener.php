<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\RestaurantValidatedEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RestaurantValidatedEventListener
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
     * @param RestaurantValidatedEvent $event
     */
    public function handle(RestaurantValidatedEvent $event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param RestaurantValidatedEvent $event
     * @return Email
     */
    protected function generateEmail(RestaurantValidatedEvent $event)
    {
        $email = new Email();
        $email->setSender($this->sender);
        $email->setRecipient($event->getRestaurant()->getOwner()->getAuthentication()->getEmail());
        $email->setSubject($this->translator->trans('restaurant_validated.subject', array(), 'email'));
        $email->setReplyTo($this->sender);
        $email->setBody($this->templating->render(
            ':email:restaurant_validated.html.twig',
            array(
                'restaurant' => $event->getRestaurant(),
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:restaurant_validated.txt.twig',
            array(
                'restaurant' => $event->getRestaurant(),
            )
        ));

        return $email;
    }
}
