<?php

namespace AppBundle\Command;


use AppBundle\Entity\Event;
use AppBundle\Notification\Event\MinCapacityNotReachedEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyMinCapacityNotReachedCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:notify:min-capacity-not-reached')
            ->setDescription('Notify if minimum capacity for event is not reached');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $eventRepository = $this->getContainer()->get('app.repository.event');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $events = $eventRepository->getEventsWhereMinCapacityNotReached();

        /** @var Event $event */
        foreach($events as $event) {
            $dispatcher->dispatch(
                MinCapacityNotReachedEvent::EVENT_NAME,
                new MinCapacityNotReachedEvent($event)
            );

            $event->setNotifiedMinCapacityNotReached(true);
            $em->persist($event);
        }

        $em->flush();
    }
}