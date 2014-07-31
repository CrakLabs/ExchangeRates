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
    /**
     * @param \DateTime $date
     *
     * @return array
     */
    public function getRates(\DateTime $date);

    /**
     * @param \DateTime $date
     * @param array     $rates
     *
     * @return bool
     */
    public function saveRates(\DateTime $date, array $rates);
}
