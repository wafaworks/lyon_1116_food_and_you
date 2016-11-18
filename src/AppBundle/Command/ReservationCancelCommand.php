<?php

namespace AppBundle\Command;

use Soluti\SogenactifBundle\Entity\Transaction;
use Soluti\SogenactifBundle\Event\TransactionUpdatedEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReservationCancelCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:reservation:cancel')
            ->setDescription('Cancel reservations if not paid after 15min');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(date("Y/m/d H:i:s")."Start unprocessed transaction cancelling");
        $transactionRepository = $this->getContainer()->get('soluti_sogenactif.repository.transaction');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $batchSize = 20;
        $i = 0;
        $iterableResult = $transactionRepository->getTransactionsWithNoResponse(new \DateTime('-15 min'));
        foreach ($iterableResult as $row) {
            /** @var Transaction $transaction */
            $transaction = $row[0];
            $transaction->setResponseCode('17');

            $dispatcher->dispatch(
                TransactionUpdatedEvent::EVENT_NAME,
                new TransactionUpdatedEvent($transaction)
            );

            if (($i % $batchSize) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();

        $output->writeln("Cancelled $i reservations");
    }
}
