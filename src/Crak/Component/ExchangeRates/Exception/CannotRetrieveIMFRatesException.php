<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * Copyright 2014 Crakmedia
 */
namespace Crak\Component\ExchangeRates\Exception;

/**
 * Class CannotRetrieveIMFRatesException
 *
 * @author  bcolucci <bcolucci@crakmedia.com>
 * @package Crak\Component\ExchangeRates\Exception
 */
class CannotRetrieveIMFRatesException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Cannot get the online currencies exchange rates');
    }
} 