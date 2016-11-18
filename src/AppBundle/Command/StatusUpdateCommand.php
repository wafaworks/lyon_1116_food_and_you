<?php

namespace AppBundle\Command;

use AppBundle\Entity\Event;
use AppBundle\Entity\Reservation;
use AppBundle\Notification\Event\EventCancelled;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class StatusUpdateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:status-update')
            ->setDescription('Update event status');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processRegistrationClose($output);
        $this->processReservationOpen($output);
        $this->processReservationClose($output);
        $this->processInProgress($output);
    }

    /**
     * @param OutputInterface $output
     */
    private function processRegistrationClose(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $eventRepository = $this->getContainer()->get('app.repository.event');

        $closed = 0;
        $canceled = 0;
        $eventsToCloseRegistrationFor = $eventRepository->getEventsToCloseApplicantRegistration();
        foreach ($eventsToCloseRegistrationFor as $event) {
            /** @var $event Event */
            if ($eventRepository->hasOneApplicantRecipeOfEach($event)) {
                $event->setStatus(Event::STATUS_APPLICANT_REGISTRATION_CLOSED);
                $closed++;
            } else {
                $this->getContainer()->get('event_dispatcher')->dispatch(
                    EventCancelled::EVENT_NAME,
                    new EventCancelled($event)
                );
                $event->setStatus(Event::STATUS_CANCELLED);
                $canceled++;
            }
            $em->persist($event);
        }
        $em->flush();

        $output->writeln(
            sprintf(
                'Processed %d events. Applicant registration closed: %d. Events canceled: %d.',
                count($eventsToCloseRegistrationFor),
                $closed,
                $canceled
            )
        );
    }

    /**
     * @param OutputInterface $output
     */
    private function processReservationOpen(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $eventRepository = $this->getContainer()->get('app.repository.event');

        $opened = 0;
        $canceled = 0;
        $eventsToCloseRegistrationFor = $eventRepository->getEventsToOpenReservations();
        foreach ($eventsToCloseRegistrationFor as $event) {
            /** @var $event Event */
            if ($eventRepository->hasSelectedRecipes($event)) {
                $event->setStatus(Event::STATUS_RESERVATIONS_OPENED);
                $opened++;
            } else {
                $this->getContainer()->get('event_dispatcher')->dispatch(
                    EventCancelled::EVENT_NAME,
                    new EventCancelled($event)
                );
                $event->setStatus(Event::STATUS_CANCELLED);
                $canceled++;
            }
            $em->persist($event);
        }
        $em->flush();

        $output->writeln(
            sprintf(
                'Processed %d events. Reservation opened: %d. Events canceled: %d.',
                count($eventsToCloseRegistrationFor),
                $opened,
                $canceled
            )
        );
    }

    /**
     * @param OutputInterface $output
     */
    private function processReservationClose(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $eventRepository = $this->getContainer()->get('app.repository.event');

        $closed = 0;
        $canceled = 0;
        $eventsToCloseRegistrationFor = $eventRepository->getEventsToCloseReservations();
        foreach ($eventsToCloseRegistrationFor as $event) {
            /** @var $event Event */
            // Disabled temporarily see FYOU-715
//            if ($eventRepository->hasEnoughReservations($event)) {
                $event->setStatus(Event::STATUS_RESERVATIONS_CLOSED);
                $closed++;
//            } else {
//                $this->cancelReservations($event);
                  //TODO: send notification to chosen 3 candidates
//                $event->setStatus(Event::STATUS_CANCELLED);
//                $canceled++;
//            }
            $em->persist($event);
        }
        $em->flush();

        $output->writeln(
            sprintf(
                'Processed %d events. Reservation closed: %d. Events canceled: %d.',
                count($eventsToCloseRegistrationFor),
                $closed,
                $canceled
            )
        );
    }

    /**
     * @param OutputInterface $output
     */
    private function processInProgress(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $sql = 'UPDATE AppBundle:Event e
                SET e.status = :newStatus
                WHERE e.status = :oldStatus AND e.startDate < :dateZ';
        $rows = $em
            ->createQuery($sql)
            ->execute(
                array(
                    'oldStatus' => Event::STATUS_RESERVATIONS_CLOSED,
                    'newStatus' => Event::STATUS_IN_PROGRESS,
                    'dateZ' => new DateTime(),
                )
            );

        $output->writeln(sprintf('Marked %d events as in progress', $rows));
    }

    /**
     * @param Event $event
     */
    private function cancelReservations(Event $event)
    {
        $reservationRepository = $this->getContainer()->get('app.repository.reservation');
        $reservationManager = $this->getContainer()->get('app.manager.reservation');

        $reservations = $reservationRepository->findAllByEvent($event, [
            Reservation::STATUS_CONFIRMED,
            Reservation::STATUS_DRAFT,
        ]);

        foreach ($reservations as $reservation) {
            $reservationManager->cancelReservation($reservation);
        }
    }
}
