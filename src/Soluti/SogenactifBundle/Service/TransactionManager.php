<?php

namespace Soluti\SogenactifBundle\Service;

use Soluti\SogenactifBundle\Entity\Repository\TransactionCallbackRepository;
use Soluti\SogenactifBundle\Entity\Transaction;
use Soluti\SogenactifBundle\Entity\TransactionCallback;
use Soluti\SogenactifBundle\Entity\TransactionRepository;
use Soluti\SogenactifBundle\Exception\PaymentException;

class TransactionManager
{
    /**
     * @var RequestGenerator
     */
    private $requestGenerator;

    /**
     * @var array
     */
    private $config;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var TransactionCallbackRepository
     */
    private $transactionCallbackRepository;

    /**
     * @param RequestGenerator $requestGenerator
     * @param TransactionRepository $transactionRepository
     * @param TransactionCallbackRepository $transactionCallbackRepository
     * @param array $config
     */
    public function __construct(
        RequestGenerator $requestGenerator,
        TransactionRepository $transactionRepository,
        TransactionCallbackRepository $transactionCallbackRepository,
        array $config)
    {
        $this->requestGenerator = $requestGenerator;
        $this->config = $config;
        $this->transactionRepository = $transactionRepository;
        $this->transactionCallbackRepository = $transactionCallbackRepository;
    }

    /**
     * Create a new transaction Object with needed amount + currency
     *
     * @param $amount
     * @param $currency
     *
     * @return Transaction
     */
    public function create($amount, $currency = 'EUR')
    {
        $transaction = new Transaction($amount, $currency);

        $this->transactionRepository->save($transaction);

        return $transaction;
    }

    /**
     * Generate HTML code that needs to be embedded based on Transaction and additional options send into the Request
     *
     * @param Transaction $transaction
     * @param array $options
     *
     * @throws PaymentException
     *
     * @return string
     */
    public function generateCode(Transaction $transaction, array $options = array())
    {
        $config = array_merge(
            $this->config['settings'],
            $options,
            array(
                'transaction_id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'currency_code' => $transaction->getCurrencyCode(),
            )
        );
        $requestString = $this->requestGenerator->getRequestParameters($config);

        $requestString = escapeshellcmd($requestString);
        $result = exec(sprintf('%s %s', $this->config['request_bin'], $requestString));

        list($ignored, $code, $error, $message) = array_pad(explode("!", "$result", 5), 4, '');

        if (($code == "") && ($error == "")) {
            throw new PaymentException('Erreur appel request: executable request non trouve ' . $this->config['request_bin']);
        }

        if ($code != 0) {
            throw new PaymentException('Erreur appel API de paiement: message erreur' . strip_tags($error));
        }

        if (defined('APPDEBUG') && APPDEBUG === true) {
            //var_dump($error); // -- it contains debug info if all OK
        }

        // ugly hack to align separators to top
        $message = str_replace('INTERVAL.gif"', 'INTERVAL.gif" style="vertical-align: top;"', $message);

        return $message;
    }

    /**
     * Process manual response
     *
     * @param $responseString
     *
     * @throws PaymentException
     *
     * @return Transaction
     */
    public function processResponse($responseString)
    {
        $responseString = escapeshellcmd($responseString);
        $response = exec(sprintf(
            '%s pathfile=%s message=%s',
            $this->config['response_bin'],
            $this->config['settings']['pathfile'],
            $responseString));

        $this->transactionCallbackRepository->save($response);
        $response = explode("!", $response);
        /** @var Transaction $transaction */
        $transaction = $this->transactionRepository->find($response[6]);
        if (!$transaction) {
            throw new PaymentException('Transaction not found. Response provided ' . print_r($response, true));
        }

        $transaction->bindResponseData($response);
        $this->transactionRepository->save($transaction);

        return $transaction;
    }
}
