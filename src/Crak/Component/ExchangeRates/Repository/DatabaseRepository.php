<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Repository;

use Crak\Component\DatasourceBalancer\Database;

/**
 * Class DatabaseRepository
 *
 * @package Crak\Component\ExchangeRates\Repository
 */
class DatabaseRepository implements RatesRepository
{
    /**
     * @var Database\Database $db
     */
    private $db;

    const TABLE = 'crakmedia.currency_exchange_rate_history';

    /**
     * @param Database\Database $db
     */
    public function __construct(Database\Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param \DateTime $date
     *
     * @return array
     */
    public function getRates(\DateTime $date)
    {
        $date = $date->format('Y-m-d');

        $query = "SELECT currency, usd_rate FROM " . self::TABLE . " WHERE date = '{$date}'";
        $rates = $this->db->getArray($this->db->query($query));

        return $this->formatRate($rates);
    }

    /**
     * @param \DateTime $date
     * @param array     $rates
     *
     * @return bool|mixed
     */
    public function saveRates(\DateTime $date, array $rates)
    {
        $date = $date->format('Y-m-d');

        $query = "INSERT into " . self::TABLE . " (date, currency, usd_rate) VALUES ";

        $values = [];
        foreach ($rates as $currency => $rate) {
            $values[] = "('{$date}','{$currency}',$rate)";
        }

        $query .= implode(',', $values);
        $query .= " ON DUPLICATE KEY UPDATE usd_rate=VALUES(usd_rate)";

        return $this->db->query($query);
    }

    /**
     * @param array $rates
     *
     * @return array
     */
    private function formatRate(array $rates)
    {
        $finalArray = [];
        foreach ($rates as $rate) {
            $finalArray[$rate['currency']] = $rate['usd_rate'];
        }

        return $finalArray;
    }
}
