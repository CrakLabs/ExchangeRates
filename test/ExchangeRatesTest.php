<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Tests;

use Crak\Component\DatasourceBalancer\Database\Database;
use Crak\Component\ExchangeRates\ExchangeRates;
use Crak\Component\ExchangeRates\Repository\CurlRepository;
use Crak\Component\ExchangeRates\Repository\DatabaseRepository;
use Crak\Component\ExchangeRates\Repository\IMFRepository;

class ExchangeRatesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $db
     */
    private $db;

    public function setUp()
    {
        $this->db = $this->getMock(Database::INTERFACE_NAME);
    }

    public function testShouldReturnRates()
    {
        $repository = new IMFRepository();
        $exchangeRate = new ExchangeRates($repository);

        $rates = $exchangeRate->getRates();
        $this->assertNotEmpty($rates['EUR']);
        $this->assertNotEmpty($rates['GBP']);
    }

    public function testShouldReturnRateFromDatabaseForGivenDate()
    {
        $date = new \DateTime();

        $this->db
            ->expects($this->once())
            ->method('query')
            ->with("SELECT currency, usd_rate FROM " . DatabaseRepository::TABLE . " WHERE date = '{$date->format('Y-m-d')}'");;

        $this->db
            ->expects($this->once())
            ->method('getArray')
            ->will($this->returnValue(
                array(
                    array('currency' => 'EUR', 'usd_rate' => '1.36030'),
                    array('currency' => 'GBP', 'usd_rate' => '1.71144'),
                )
            ));

        $repository = new DatabaseRepository($this->db);
        $exchangeRate = new ExchangeRates($repository);

        $rates = $exchangeRate->getRates($date);

        $this->assertEquals('1.36030', $rates['EUR']);
        $this->assertEquals('1.71144', $rates['GBP']);
    }

    public function testShouldSaveRateFromDatabase()
    {
        $rates = array(
            'EUR' => '1.36030',
            'GBP' => '1.71144',
        );

        $this->db
            ->expects($this->once())
            ->method('query')
            ->with($this->equalTo("INSERT into " . DatabaseRepository::TABLE . " (date, currency, usd_rate) VALUES ('2014-07-09','EUR',1.36030),('2014-07-09','GBP',1.71144) ON DUPLICATE KEY UPDATE usd_rate=VALUES(usd_rate)"))
            ->willReturn(true);

        $repository = new DatabaseRepository($this->db);
        $this->assertTrue($repository->saveRates(new \DateTime('2014-07-09'), $rates));
    }

    /**
     * @expectedException \Crak\Component\ExchangeRates\Exception\CannotRetrieveIMFRatesException
     */
    public function testShouldThrowExceptionWhenIMFRepositoryCantGetRates()
    {
        $curlRepository = $this->getMock(CurlRepository::CLASS_NAME);

        $curlRepository
            ->expects($this->once())
            ->method('get')
            ->willReturn(false);

        $repository = new IMFRepository($curlRepository);
        $repository->getRates(new \DateTime());
    }

    public function testShouldDoNothing()
    {
        $repository = new IMFRepository();
        $this->assertTrue($repository->saveRates(new \DateTime, array()));
    }

    public function testShouldGetRatesFromIMF()
    {
        $this->db
            ->expects($this->exactly(2))
            ->method('query');

        $this->db
            ->expects($this->once())
            ->method('getArray')
            ->willReturn(array());

        $repository = new DatabaseRepository($this->db);
        $exchangeRate = new ExchangeRates($repository);

        $rates = $exchangeRate->getRates();
        $this->assertNotEmpty($rates['EUR']);
        $this->assertNotEmpty($rates['GBP']);
    }
}
