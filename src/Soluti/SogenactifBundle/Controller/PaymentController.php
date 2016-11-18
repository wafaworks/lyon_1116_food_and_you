<?php

namespace Soluti\SogenactifBundle\Controller;

use Soluti\SogenactifBundle\Event\TransactionUpdatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Soluti\SogenactifBundle\Exception\PaymentException
     */
    public function normalAction(Request $request)
    {
        $data = $request->request->get('DATA','');
        $transaction = $this->get('soluti_sogenactif.transaction_manager')->processResponse($data);

        $event = new TransactionUpdatedEvent($transaction);
        $this->get('event_dispatcher')->dispatch(
            TransactionUpdatedEvent::EVENT_NAME,
            $event
        );

        return $event->getResponse() ?: new Response();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Soluti\SogenactifBundle\Exception\PaymentException
     */
    public function cancelAction(Request $request)
    {
        $data = $request->request->get('DATA', '');
        $transaction = $this->get('soluti_sogenactif.transaction_manager')->processResponse($data);

        $event = new TransactionUpdatedEvent($transaction);
        $this->get('event_dispatcher')->dispatch(
            TransactionUpdatedEvent::EVENT_NAME,
            $event
        );

        return $event->getResponse() ?: new Response();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Soluti\SogenactifBundle\Exception\PaymentException
     */
    public function autoAction(Request $request)
    {
        $data = $request->request->get('DATA','');

        $transaction = $this->get('soluti_sogenactif.transaction_manager')->processResponse($data);
        $this->get('event_dispatcher')->dispatch(
            TransactionUpdatedEvent::EVENT_NAME,
            new TransactionUpdatedEvent($transaction)
        );

        return new Response();
    }
}
