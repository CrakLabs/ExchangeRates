<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 * @author Julien Maitrehenry <julien@crakmedia.com>
 */

namespace Crak\Component\ExchangeRates\Repository;

use Crak\Component\ExchangeRates\Exception\CannotRetrieveIMFRatesException;

/**
 * Class IMFRepository
 *
 * @package Crak\Component\ExchangeRates\Repository
 */
class IMFRepository implements RatesRepository
{
    const URL = "http://www.imf.org/external/np/fin/data/rms_five.aspx?tsvflag=Y";

    /**
     * @var CurlRepository $curlRepository
     */
    private $curlRepository;

    /**
     * @param CurlRepository $curlRepository = null
     */
    public function __construct(CurlRepository $curlRepository = null)
    {
        if (is_null($curlRepository)) {
            $curlRepository = new CurlRepository();
        }

        $this->curlRepository = $curlRepository;
    }

    /**
     * @param \DateTime $dates
     *
     * @return array
     *
     * @throws CannotRetrieveIMFRatesException
     */
    public function getRates(\DateTime $dates)
    {
        $csv = $this->curlRepository->get(self::URL);;

        if (!$csv) {
            throw new CannotRetrieveIMFRatesException();
        }

        return $this->parseCSV($csv);
    }

    /**
     * @param \DateTime $date
     * @param array     $rates
     *
     * @return bool
     */
    public function saveRates(\DateTime $date, array $rates)
    {
        return true;
    }

    /**
     * @param $csv
     *
     * @return array
     */
    private function parseCSV($csv)
    {
        $rates = array();

        $countries = array('Euro'            => 'EUR', 'Japanese Yen' => 'JPY', 'U.K. Pound Sterling' => 'GBP', 'U.S. Dollar' => 'USD', 'Algerian Dinar' => 'DZD', 'Argentine Peso' => 'ARS', 'Australian Dollar' => 'AUD',
                           'Bahrain Dinar'   => 'BHD', 'Botswana Pula' => 'BWP', 'Brazilian Real' => 'BRL', 'Brunei Dollar' => 'BND', 'Canadian Dollar' => 'CAD', 'Chilean Peso' => 'CLP', 'Chinese Yuan' => 'CNY',
                           'Colombian Peso'  => 'COP', 'Czech Koruna' => 'CZK', 'Danish Krone' => 'DKK', 'Hungarian Forint' => 'HUF', 'Icelandic Krona' => 'ISK', 'Indian Rupee' => 'INR', 'Indonesian Rupiah' => 'IDR',
                           'Iranian Rial'    => 'IRR', 'Israeli New Sheqel' => 'ILS', 'Kazakhstani Tenge' => 'KZT', 'Korean Won' => 'KPW', 'Kuwaiti Dinar' => 'KWD', 'Libyan Dinar' => 'LYD', 'Malaysian Ringgit' => 'MYR',
                           'Mauritian Rupee' => 'MUR', 'Mexican Peso' => 'MXN', 'Nepalese Rupee' => 'NPR', 'New Zealand Dollar' => 'NZD', 'Norwegian Krone' => 'NOK', 'Pakistani Rupee' => 'PKR', 'Nuevo Sol' => 'PEN',
                           'Philippine Peso' => 'PHP', 'Polish Zloty' => 'PLN', 'Qatar Riyal' => 'QAR', 'Russian Ruble' => 'RUB', 'Saudi Arabian Riyal' => 'SAR', 'Singapore Dollar' => 'SGD', 'South African Rand' => 'ZAR',
                           'Sri Lanka Rupee' => 'LKR', 'Swedish Krona' => 'SEK', 'Swiss Franc' => 'CHF', 'Thai Baht' => 'THB', 'Trinidad And Tobago Dollar' => 'TTD', 'Tunisian Dinar' => 'TND', 'U.A.E. Dirham' => 'AED',
                           'Peso Uruguayo'   => 'UYU', 'Bolivar Fuerte' => 'VEF');

        preg_match('/SDRs per Currency unit \(2\)\s+(?P<rates>.*)Currency units per SDR\(3\)/isU', $csv, $csv);
        $records = explode("\n", trim($csv['rates']));

        //  SDR Values
        foreach ($records as $record) {

            $record = preg_replace('/\t+/', "\t", $record);
            $fields = str_getcsv($record, "\t", '"', '\\');
            if (!empty($fields[0])) {
                $countries[$fields[0]] = !empty($countries[$fields[0]]) ? $countries[$fields[0]] : $fields[0];
                $rates[$countries[$fields[0]]] = $fields[1];
            }
        }

        //  USD Exchange rates
        $baseRate = $rates['USD'];
        if ($baseRate > 0) {
            foreach ($rates as $currency => $rate) {
                if ($rate != 0) {
                    $rates[$currency] = $rate / $baseRate;
                }
            }
        }

        return $rates;
    }
}
