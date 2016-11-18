<?php

namespace AppBundle\Command;

use AppBundle\Entity\Notification;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifySendCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:notify:send')
            ->setDescription('Send push notifications');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $RMSnotifier = $this->getContainer()->get('rms_push_notifications');
        $notificationRepository = $this->getContainer()->get('app.repository.notification');
        $translator = $this->getContainer()->get('translator');

        $notifications = $notificationRepository->getUnsent();

        foreach ($notifications as $row) {
            /** @var Notification $notification */
            $notification = $row[0];

            $message = new iOSMessage();
            $message->setMessage($translator->trans($notification->getMessageKey(), $notification->getMessageParameters(), 'push'));
            $message->setDeviceIdentifier($notification->getDevice()->getToken());

            $result = $RMSnotifier->send($message);

            if ($result) {
                $notification->setSent(true);
                $notification->setSendTime(new \DateTime());

                $em->persist($notification);
                $em->flush();
                $em->clear();
            }
        }

    }
}
