<?php

namespace AppBundle\Command;

use AppBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReservationRecountCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:reservation:recount')
            ->setDescription('Recount confirmed reservations');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start recount of reservations');
        // iterate events
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $eventRepository = $this->getContainer()->get('app.repository.event');
        $reservationRepository = $this->getContainer()->get('app.repository.reservation');

        $batchSize = 20;
        $i = 0;
        $iterableResult = $eventRepository->getEventsIterated(array(
            Event::STATUS_RESERVATIONS_OPENED,
            Event::STATUS_RESERVATIONS_CLOSED,
            Event::STATUS_IN_PROGRESS,
        ));
        foreach ($iterableResult as $row) {
            /** @var Event $event */
            $event = $row[0];
            $event->setConfirmedReservations($reservationRepository->getNrPlacesReserved($event));
            if (($i % $batchSize) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();
        $output->writeln("Recounted reservations for $i events");
    }
}
