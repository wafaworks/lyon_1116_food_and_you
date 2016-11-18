<?php

namespace AppBundle\Command;

use AppBundle\Notification\Event\ReservationsToRefundEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReservationNotifyRefundCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:reservation:notify-refund')
            ->setDescription('Notifies admins about the number of reservations that have to be refunded');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reservationRepository = $this->getContainer()->get('app.repository.reservation');

        $reservationsToRefund = $reservationRepository->getNrReservationsToRefund();

        $this->getContainer()->get('event_dispatcher')->dispatch(
            ReservationsToRefundEvent::EVENT_NAME,
            new ReservationsToRefundEvent($reservationsToRefund)
        );
    }
}
