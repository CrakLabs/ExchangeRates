<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Repository;


/**
 * Interface RatesRepository
 * @package Crak\Bundle\CronBundle\Repository
 */
interface RatesRepository
{
    public function getRates(\DateTime $date = null);
    public function saveRates(\DateTime $date, $rates);
}
