<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Repository;

/**
 * Class CurlRepository
 *
 * @package Crak\Component\ExchangeRates\Repository
 */
class CurlRepository
{
    const CLASS_NAME = __CLASS__;

    /**
     * @param string $url
     *
     * @return bool|string
     */
    public function get($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HEADER, false);

        $return = curl_exec($ch);

        curl_close($ch);

        return $return;
    }
}
