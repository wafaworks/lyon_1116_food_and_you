<?php

namespace AppBundle\Notification\Listener;

use AppBundle\Model\Email;
use AppBundle\Notification\Event\LevelUpEvent;
use AppBundle\Service\Mailer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LevelUpEventListener
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
     * @param LevelUpEvent $event
     */
    public function handle(LevelUpEvent $event)
    {
        $email = $this->generateEmail($event);

        $this->mailer->send($email);
    }

    /**
     * @param LevelUpEvent $event
     * @return Email
     */
    protected function generateEmail(LevelUpEvent $event)
    {
        $email = new Email();
        $email->setSender($this->sender);
        $email->setRecipient($event->getMember()->getAuthentication()->getEmail());
        $levelLabel = $this->translator->trans('member.level.' . $event->getMember()->getLevel(), [], 'member_profile');
        $email->setSubject($this->translator->trans('level_up.subject', array('%level%' => $levelLabel), 'email'));
        $email->setReplyTo($this->sender);
        $email->setBody($this->templating->render(
            ':email:level_up.html.twig',
            array(
                'member' => $event->getMember(),
            )
        ));
        $email->setPlainBody($this->templating->render(
            ':email:level_up.txt.twig',
            array(
                'member' => $event->getMember(),
            )
        ));

        return $email;
    }
}
