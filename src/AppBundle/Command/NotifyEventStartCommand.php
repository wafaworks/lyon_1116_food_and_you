<?php

namespace AppBundle\Command;

use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyEventStartCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:notify:event-start')
            ->setDescription('Create Push notifications that event starts');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventRepository = $this->getContainer()->get('app.repository.event');
        $deviceRepository = $this->getContainer()->get('app.repository.device');
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $events = $eventRepository->getStartingEvents();

        $i = 0;
        $batchSize = 20;
        foreach ($events as $event) {
            $devices = $deviceRepository->getDevicesForEvent($event);
            foreach ($devices as $device) {
                $notification = new Notification();
                $notification->setDevice($device);
                $notification->setMessageKey('event.start');
                $notification->setMessageParameters([]);

                $entityManager->persist($notification);
                if ($i % $batchSize == 0) {
                    $entityManager->flush();
                }
            }
            $event->setNotifiedStart(true);
            $entityManager->persist($event);
        }
        $entityManager->flush();
    }
}
