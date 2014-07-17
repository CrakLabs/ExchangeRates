<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Repository;

use Crak\Component\DatasourceBalancer;

class DatabaseRepository implements RatesRepository
{
    private $db;
    const TABLE = 'crakmedia.currency_exchange_rate_history';

    public function __construct(DatasourceBalancer\Database\Database $db) {
        $this->db = $db;
    }

    public function getRates(\DateTime $date = null) {
        $date = $date->format('Y-m-d');
        $query = "SELECT currency, usd_rate FROM " . self::TABLE . " WHERE date = '{$date}'";
        $ressource = $this->db->query($query);
        $rates = $this->db->getArray($ressource);

        return $this->formatRate($rates);
    }

    public function saveRates(\DateTime $date, $rates)
    {
        $date = $date->format('Y-m-d');
        $query = "INSERT into " . self::TABLE . " (date, currency, usd_rate) VALUES ";
        $values = [];

        foreach($rates as $currency => $rate) {
            $values[] = "('{$date}','{$currency}',$rate)";
        }

        $query .= implode(',', $values);
        $query .= "ON DUPLICATE KEY UPDATE usd_rate=VALUEs(usd_rate)";

        return $this->db->query($query);
    }

    private function formatRate($rates) {
        $finalArray = [];
        foreach($rates as $rate) {
            $finalArray[$rate['currency']] = $rate['usd_rate'];
        }

        return $finalArray;
    }
}
