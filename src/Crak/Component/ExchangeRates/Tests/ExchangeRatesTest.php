<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Tests;

use Crak\Component\ExchangeRates;

class ExchangeRatesTest extends \PHPUnit_Framework_TestCase
{

    public function __construct()
    {
    }

    public function testShouldReturnRates() {
        $repository = new ExchangeRates\Repository\IMFRepository();
        $exchangeRate = new ExchangeRates\ExchangeRates($repository);

        $rates = $exchangeRate->getRates();
        $this->assertNotEmpty($rates);
        $this->assertNotEmpty($rates['EUR']);
        $this->assertNotEmpty($rates['GBP']);
        $this->assertNotEmpty($rates['JPY']);
        $this->assertNotEmpty($rates['USD']);
    }

    public function testShouldReturnRateFromDatabase() {
        $db = $this->getMock('\Crak\Component\DatasourceBalancer\Database\MySQL');
        $db->expects($this->once())
            ->method('query')
            ->with("SELECT currency, usd_rate FROM " . ExchangeRates\Repository\DatabaseRepository::TABLE . " WHERE date = '2014-07-09'");;

        $db->expects($this->once())
            ->method('getArray')
            ->will($this->returnValue(
                array(
                    array('currency' => 'EUR', 'usd_rate' => '1.36030'),
                    array('currency' => 'GBP', 'usd_rate' => '1.71144'),
                    array('currency' => 'JPY', 'usd_rate' => '0.00984'),
                    array('currency' => 'USD', 'usd_rate' => '1.00000'),
                )
            ));

        $repository = new ExchangeRates\Repository\DatabaseRepository($db);
        $exchangeRate = new ExchangeRates\ExchangeRates($repository);
        $rates = $exchangeRate->getRates(new \DateTime('2014-07-09'));

        $this->assertNotEmpty($rates);
        $this->assertEquals('1.36030', $rates['EUR']);
        $this->assertEquals('1.71144', $rates['GBP']);
        $this->assertEquals('0.00984', $rates['JPY']);
        $this->assertEquals('1.00000', $rates['USD']);
    }

    public function testShouldSaveRateFromDatabase() {
        $rates = array (
            'EUR' => '1.36030',
            'GBP' => '1.71144',
            'JPY' => '0.00984',
            'USD' => '1.00000'
        );

        $db = $this->getMock('\Crak\Component\DatasourceBalancer\Database\MySQL');
        $db->expects($this->once())
            ->method('query')
            ->with($this->equalTo("INSERT into crakmedia.currency_exchange_rate_history (date, currency, usd_rate) VALUES ('2014-07-09','EUR',1.36030),('2014-07-09','GBP',1.71144),('2014-07-09','JPY',0.00984),('2014-07-09','USD',1.00000)ON DUPLICATE KEY UPDATE usd_rate=VALUEs(usd_rate)"))
            ->willReturn(true);


        $repository = new ExchangeRates\Repository\DatabaseRepository($db);
        $this->assertTrue($repository->saveRates(new \DateTime('2014-07-09'), $rates));

    }

    /**
     * @expectedException \Exception
     */
    public function testShouldThrowExceptionWhenIMFRepositoryCantGetRates() {
        $curlRepository = $this->getMock('\Crak\Component\ExchangeRates\Repository\CurlRepository');
        $curlRepository->expects($this->once())
            ->method('get')
            ->willReturn(false);

        $repository = new ExchangeRates\Repository\IMFRepository($curlRepository);
        $repository->getRates();
    }

    public function testShouldDoNothing() {
        $repository = new ExchangeRates\Repository\IMFRepository();
        $this->assertTrue($repository->saveRates(new \DateTime, array()));
    }

    public function testShouldGetRatesFromIMF() {
        $db = $this->getMock('\Crak\Component\DatasourceBalancer\Database\MySQL');
        $db->expects($this->exactly(2))
            ->method('query');



        $db->expects($this->once())
            ->method('getArray')
            ->willReturn(array());

        $repository = new ExchangeRates\Repository\DatabaseRepository($db);
        $exchangeRate = new ExchangeRates\ExchangeRates($repository);

        $rates = $exchangeRate->getRates();
        $this->assertNotEmpty($rates);
        $this->assertNotEmpty($rates['EUR']);
        $this->assertNotEmpty($rates['GBP']);
        $this->assertNotEmpty($rates['JPY']);
        $this->assertNotEmpty($rates['USD']);
    }
}
