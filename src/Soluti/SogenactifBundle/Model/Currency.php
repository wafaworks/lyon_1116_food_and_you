<?php

namespace Soluti\SogenactifBundle\Model;

class Currency
{
    const CURRENCY_MAP = array(
        'EUR' => array('code' => 978, 'digits' => 2),
        'USD' => array('code' => 840, 'digits' => 2),
        'CHF' => array('code' => 756, 'digits' => 2),
        'GBP' => array('code' => 826, 'digits' => 2),
        'CAD' => array('code' => 124, 'digits' => 2),
        'JPY' => array('code' => 392, 'digits' => 0),
        'MXN' => array('code' => 484, 'digits' => 2),
        'TRY' => array('code' => 949, 'digits' => 2),
        'AUD' => array('code' => 036, 'digits' => 2),
        'NZD' => array('code' => 554, 'digits' => 2),
        'NOK' => array('code' => 578, 'digits' => 2),
        'BRL' => array('code' => 986, 'digits' => 2),
        'ARS' => array('code' => 032, 'digits' => 2),
        'KHR' => array('code' => 116, 'digits' => 2),
        'TWD' => array('code' => 901, 'digits' => 2),
        'SEK' => array('code' => 752, 'digits' => 2),
        'DKK' => array('code' => 208, 'digits' => 2),
        'KRW' => array('code' => 410, 'digits' => 0),
        'SGD' => array('code' => 702, 'digits' => 2),
        'XPF' => array('code' => 953, 'digits' => 0),
        'XOF' => array('code' => 952, 'digits' => 0),
    );

    /**
     * Get numeric code for string currency
     *
     * @param $string
     *
     * @throws \Exception
     */
    public static function getCode($string)
    {
        if (!array_key_exists($string, self::CURRENCY_MAP)) {
            throw new \Exception('Currency string is not supported');
        }

        return self::CURRENCY_MAP[$string]['code'];
    }

    /**
     * Format number according to rules
     *
     * @param string $amount
     * @param string $currency_code
     *
     * @return int
     */
    public static function formatAmount($amount, $currency_code)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $amount);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $amount);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

        $finalAmount = str_replace(',', '.', $removedThousandSeparator);

        if (self::CURRENCY_MAP[$currency_code]['digits'] === 2) {
            $finalAmount = (float) $finalAmount * 100;
        }

        return (int) $finalAmount;
    }

    /**
     * Format amount charged as string currency
     *
     * @param integer $amount
     * @param string $currency_code
     *
     * @return string
     */
    public static function formatCurrencyAmount($amount, $currency_code)
    {
        $code = array_filter(self::CURRENCY_MAP, function($var) use ($currency_code){
            return $var['code']  === $currency_code;
        });

        $keys = array_keys($code);
        $key = array_pop($keys);

        if (self::CURRENCY_MAP[$key]['digits'] === 2) {
            $amount = $amount / 100;
        }

        return number_format($amount, 2, ',', '');
    }
}
