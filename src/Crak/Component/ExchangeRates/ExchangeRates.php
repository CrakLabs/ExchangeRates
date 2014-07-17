<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates;

class ExchangeRates
{
    private $repository;

    public function __construct(Repository\RatesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRates(\DateTime $date = null)
    {
        if(is_null($date)) {
            $date = new \DateTime();
        }

        $rates = $this->repository->getRates($date);
        if(empty($rates) && $this->repository instanceof Repository\DatabaseRepository) {
            $repository = new Repository\IMFRepository();
            $rates = $repository->getRates($date);
            $this->repository->saveRates($date, $rates);
        }

        return $rates;
    }
}
