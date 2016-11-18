<?php

namespace Soluti\SogenactifBundle\Event;

use Soluti\SogenactifBundle\Entity\Transaction;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class TransactionUpdatedEvent extends Event
{
    const EVENT_NAME = 'soluti_sogenactif.transaction.updated';

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}
