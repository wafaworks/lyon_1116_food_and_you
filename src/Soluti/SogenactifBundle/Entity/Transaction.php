<?php

namespace Soluti\SogenactifBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Soluti\SogenactifBundle\Exception\PaymentException;
use Soluti\SogenactifBundle\Model\Currency;

/**
 * @ORM\Entity(repositoryClass="Soluti\SogenactifBundle\Entity\TransactionRepository")
 * @ORM\Table(name="transaction")
 *
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class Transaction
{
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $merchant_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $merchant_country;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $payment_means;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $transmission_date;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $payment_date;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $response_code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $payment_certificate;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $authorisation_id;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $currency_code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $card_number;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $cvv_flag;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $cvv_response_code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $bank_response_code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $complementary_code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $complementary_info;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $return_context;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $caddie;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $receipt_complement;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $merchant_language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $customer_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $order_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $customer_email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $customer_ip_address;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $capture_day;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $capture_mode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $order_validity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $transaction_condition;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $statement_reference;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $card_validity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $score_value;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $score_color;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $score_info;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $score_threshold;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $score_profile;

    /**
     * @param string $transaction_id
     * @param string $amount
     * @param string $currency_code
     */
    public function __construct($amount, $currency_code)
    {
        $this->amount = Currency::formatAmount($amount, $currency_code);
        $this->currency_code = Currency::getCode($currency_code);
    }

    /**
     * @param array $response
     * @throws PaymentException
     */
    public function bindResponseData(array $response)
    {
        if ((int)$response[6] !== $this->getId() ||
            (int)$response[5] !== $this->getAmount() ||
            (int)$response[14] !== $this->getCurrencyCode()
        ) {
            throw new PaymentException('Response data does not match Transaction Request data');
        }

        $this->setCode($response[1]);
        $this->setError($response[2]);
        $this->setMerchantId($response[3]);
        $this->setMerchantCountry($response[4]);
        $this->setPaymentMeans($response[7]);
        if ($response[8]) {
            $this->setTransmissionDate(DateTime::createFromFormat('YmdHis', $response[8]));
        }
        if ($response[10] && $response[9]) {
            $this->setPaymentDate(DateTime::createFromFormat('YmdHis', $response[10].$response[9]));
        }
        $this->setResponseCode($response[11]);
        $this->setPaymentCertificate($response[12]);
        $this->setAuthorisationId($response[13]);
        $this->setCardNumber($response[15]);
        $this->setCvvFlag($response[16]);
        $this->setCvvResponseCode($response[17]);
        $this->setBankResponseCode($response[18]);
        $this->setComplementaryCode($response[19]);
        $this->setComplementaryInfo($response[20]);
        $this->setReturnContext($response[21]);
        $this->setCaddie($response[22]);
        $this->setReceiptComplement($response[23]);
        $this->setMerchantLanguage($response[24]);
        $this->setLanguage($response[25]);
        $this->setCustomerId($response[26]);
        $this->setOrderId($response[27]);
        $this->setCustomerEmail($response[28]);
        $this->setCustomerIpAddress($response[29]);
        $this->setCaptureDay($response[30]);
        $this->setCaptureMode($response[31]);
        $this->setData($response[32]);
        $this->setOrderValidity($response[33]);
        $this->setTransactionCondition($response[34]);
        $this->setStatementReference($response[35]);
        $this->setCardValidity($response[36]);
        $this->setScoreValue($response[37]);
        $this->setScoreColor($response[38]);
        $this->setScoreInfo($response[39]);
        $this->setScoreThreshold($response[40]);
        $this->setScoreProfile($response[41]);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @param string $merchant_id
     */
    public function setMerchantId($merchant_id)
    {
        $this->merchant_id = $merchant_id;
    }

    /**
     * @return string
     */
    public function getMerchantCountry()
    {
        return $this->merchant_country;
    }

    /**
     * @param string $merchant_country
     */
    public function setMerchantCountry($merchant_country)
    {
        $this->merchant_country = $merchant_country;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param integer $amount
     */
    private function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getFormattedAmount()
    {
        return Currency::formatCurrencyAmount($this->getAmount(), $this->getCurrencyCode());
    }

    /**
     * @return string
     */
    public function getPaymentMeans()
    {
        return $this->payment_means;
    }

    /**
     * @param string $payment_means
     */
    public function setPaymentMeans($payment_means)
    {
        $this->payment_means = $payment_means;
    }

    /**
     * @return DateTime
     */
    public function getTransmissionDate()
    {
        return $this->transmission_date;
    }

    /**
     * @param DateTime $transmission_date
     */
    public function setTransmissionDate($transmission_date)
    {
        $this->transmission_date = $transmission_date;
    }

    /**
     * @return DateTime
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
    }

    /**
     * @param DateTime $payment_date
     */
    public function setPaymentDate($payment_date)
    {
        $this->payment_date = $payment_date;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    /**
     * @param string $response_code
     */
    public function setResponseCode($response_code)
    {
        $this->response_code = $response_code;
    }

    /**
     * @return string
     */
    public function getPaymentCertificate()
    {
        return $this->payment_certificate;
    }

    /**
     * @param string $payment_certificate
     */
    public function setPaymentCertificate($payment_certificate)
    {
        $this->payment_certificate = $payment_certificate;
    }

    /**
     * @return string
     */
    public function getAuthorisationId()
    {
        return $this->authorisation_id;
    }

    /**
     * @param string $authorisation_id
     */
    public function setAuthorisationId($authorisation_id)
    {
        $this->authorisation_id = $authorisation_id;
    }

    /**
     * @return integer
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param integer $currency_code
     */
    private function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->card_number;
    }

    /**
     * @param string $card_number
     */
    public function setCardNumber($card_number)
    {
        $this->card_number = $card_number;
    }

    /**
     * @return string
     */
    public function getCvvFlag()
    {
        return $this->cvv_flag;
    }

    /**
     * @param string $cvv_flag
     */
    public function setCvvFlag($cvv_flag)
    {
        $this->cvv_flag = $cvv_flag;
    }

    /**
     * @return string
     */
    public function getCvvResponseCode()
    {
        return $this->cvv_response_code;
    }

    /**
     * @param string $cvv_response_code
     */
    public function setCvvResponseCode($cvv_response_code)
    {
        $this->cvv_response_code = $cvv_response_code;
    }

    /**
     * @return string
     */
    public function getBankResponseCode()
    {
        return $this->bank_response_code;
    }

    /**
     * @param string $bank_response_code
     */
    public function setBankResponseCode($bank_response_code)
    {
        $this->bank_response_code = $bank_response_code;
    }

    /**
     * @return string
     */
    public function getComplementaryCode()
    {
        return $this->complementary_code;
    }

    /**
     * @param string $complementary_code
     */
    public function setComplementaryCode($complementary_code)
    {
        $this->complementary_code = $complementary_code;
    }

    /**
     * @return string
     */
    public function getComplementaryInfo()
    {
        return $this->complementary_info;
    }

    /**
     * @param string $complementary_info
     */
    public function setComplementaryInfo($complementary_info)
    {
        $this->complementary_info = $complementary_info;
    }

    /**
     * @return string
     */
    public function getReturnContext()
    {
        return $this->return_context;
    }

    /**
     * @param string $return_context
     */
    public function setReturnContext($return_context)
    {
        $this->return_context = $return_context;
    }

    /**
     * @return string
     */
    public function getCaddie()
    {
        return $this->caddie;
    }

    /**
     * @param string $caddie
     */
    public function setCaddie($caddie)
    {
        $this->caddie = $caddie;
    }

    /**
     * @return string
     */
    public function getReceiptComplement()
    {
        return $this->receipt_complement;
    }

    /**
     * @param string $receipt_complement
     */
    public function setReceiptComplement($receipt_complement)
    {
        $this->receipt_complement = $receipt_complement;
    }

    /**
     * @return string
     */
    public function getMerchantLanguage()
    {
        return $this->merchant_language;
    }

    /**
     * @param string $merchant_language
     */
    public function setMerchantLanguage($merchant_language)
    {
        $this->merchant_language = $merchant_language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param string $customer_id
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @param string $order_id
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customer_email;
    }

    /**
     * @param string $customer_email
     */
    public function setCustomerEmail($customer_email)
    {
        $this->customer_email = $customer_email;
    }

    /**
     * @return string
     */
    public function getCustomerIpAddress()
    {
        return $this->customer_ip_address;
    }

    /**
     * @param string $customer_ip_address
     */
    public function setCustomerIpAddress($customer_ip_address)
    {
        $this->customer_ip_address = $customer_ip_address;
    }

    /**
     * @return string
     */
    public function getCaptureDay()
    {
        return $this->capture_day;
    }

    /**
     * @param string $capture_day
     */
    public function setCaptureDay($capture_day)
    {
        $this->capture_day = $capture_day;
    }

    /**
     * @return string
     */
    public function getCaptureMode()
    {
        return $this->capture_mode;
    }

    /**
     * @param string $capture_mode
     */
    public function setCaptureMode($capture_mode)
    {
        $this->capture_mode = $capture_mode;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getOrderValidity()
    {
        return $this->order_validity;
    }

    /**
     * @param string $order_validity
     */
    public function setOrderValidity($order_validity)
    {
        $this->order_validity = $order_validity;
    }

    /**
     * @return string
     */
    public function getTransactionCondition()
    {
        return $this->transaction_condition;
    }

    /**
     * @param string $transaction_condition
     */
    public function setTransactionCondition($transaction_condition)
    {
        $this->transaction_condition = $transaction_condition;
    }

    /**
     * @return string
     */
    public function getStatementReference()
    {
        return $this->statement_reference;
    }

    /**
     * @param string $statement_reference
     */
    public function setStatementReference($statement_reference)
    {
        $this->statement_reference = $statement_reference;
    }

    /**
     * @return string
     */
    public function getCardValidity()
    {
        return $this->card_validity;
    }

    /**
     * @param string $card_validity
     */
    public function setCardValidity($card_validity)
    {
        $this->card_validity = $card_validity;
    }

    /**
     * @return string
     */
    public function getScoreValue()
    {
        return $this->score_value;
    }

    /**
     * @param string $score_value
     */
    public function setScoreValue($score_value)
    {
        $this->score_value = $score_value;
    }

    /**
     * @return string
     */
    public function getScoreColor()
    {
        return $this->score_color;
    }

    /**
     * @param string $score_color
     */
    public function setScoreColor($score_color)
    {
        $this->score_color = $score_color;
    }

    /**
     * @return string
     */
    public function getScoreInfo()
    {
        return $this->score_info;
    }

    /**
     * @param string $score_info
     */
    public function setScoreInfo($score_info)
    {
        $this->score_info = $score_info;
    }

    /**
     * @return string
     */
    public function getScoreThreshold()
    {
        return $this->score_threshold;
    }

    /**
     * @param string $score_threshold
     */
    public function setScoreThreshold($score_threshold)
    {
        $this->score_threshold = $score_threshold;
    }

    /**
     * @return string
     */
    public function getScoreProfile()
    {
        return $this->score_profile;
    }

    /**
     * @param string $score_profile
     */
    public function setScoreProfile($score_profile)
    {
        $this->score_profile = $score_profile;
    }
}
