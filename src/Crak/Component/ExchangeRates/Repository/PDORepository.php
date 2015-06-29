<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Repository;

/**
 * Class DatabaseRepository
 *
 * @package Crak\Component\ExchangeRates\Repository
 */
class PDORepository implements RatesRepository
{
    /** @var \PDO $db */
    private $db;

    /** @var string $table */
    private $table;

    /**
     * @param Database\Database $db
     * @param string $table
     */
    public function __construct(\PDO $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * @param \DateTime $date
     * @return array
     */
    public function getRates(\DateTime $date)
    {
        $date = $date->format('Y-m-d');

        $query = "SELECT currency, usd_rate FROM {$this->table} WHERE date = '{$date}'";
        $res = $this->db->query($query);
        $rates = $res->fetchAll();

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

        $query = "INSERT into {$this->table} (date, currency, usd_rate) VALUES ";

        $values = [];
        foreach ($rates as $currency => $rate) {
            $values[] = "('{$date}','{$currency}',$rate)";
        }

        $query .= implode(',', $values);
        $query .= " ON DUPLICATE KEY UPDATE usd_rate=VALUES(usd_rate)";

        return $this->db->exec($query);
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
