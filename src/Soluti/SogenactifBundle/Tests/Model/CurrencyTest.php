<?php

namespace Soluti\SogenactifBundle\Tests\Service;

use Soluti\SogenactifBundle\Model\Currency;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider formatAmountProvider
     * @param $amount
     * @param $expected
     * @param $currency
     */
    public function testFormatAmount($amount, $expected, $currency)
    {
        $this->assertEquals($expected, Currency::formatAmount($amount, $currency));
    }

    public function testGetCode()
    {
        $this->assertEquals(978, Currency::getCode('EUR'));
        $this->assertEquals(840, Currency::getCode('USD'));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetCodeException()
    {
        $this->assertEquals(373, Currency::getCode('MDL'));
    }

    public function formatAmountProvider()
    {
        return [
            ['1,10 USD', 110, 'USD'],
            ['1 000 000.00', 100000000, 'EUR'],
            ['$1 000 000.21', 100000021, 'USD'],
            ['£1.10', 110, 'GBP'],
            ['$123 456 789', 12345678900, 'USD'],
            ['$123,456,789.12', 12345678912, 'USD'],
            ['$123 456 789,12', 12345678912, 'USD'],
            ['1.10', 110, 'EUR'],
            [',,,,.10', 10, 'EUR'],
            ['1.000', 100000, 'EUR'],
            ['1,000', 100000, 'EUR'],
            ['106.55', 106, 'XOF'],
            ['106.55', 10655, 'EUR'],
            ['106,55', 10655, 'EUR'],
        ];
    }
}
