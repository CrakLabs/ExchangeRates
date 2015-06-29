<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates;

use Crak\Component\ExchangeRates\Repository\PDORepository;
use Crak\Component\ExchangeRates\Repository\RatesRepository;

/**
 * Class ExchangeRates
 *
 * @package Crak\Component\ExchangeRates
 */
class ExchangeRates
{
    /**
     * @var RatesRepository $repository
     */
    private $repository;

    /**
     * @param RatesRepository $repository
     */
    public function __construct(RatesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param \DateTime $date = null
     *
     * @return array
     */
    public function getRates(\DateTime $date = null)
    {
        if (is_null($date)) {
            $date = new \DateTime();
        }

        $rates = $this->repository->getRates($date);
        if (empty($rates) && $this->repository instanceof PDORepository) {
            $repository = new Repository\IMFRepository();
            $rates = $repository->getRates($date);
            $this->repository->saveRates($date, $rates);
        }

        return $rates;
    }
}
