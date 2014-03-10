<?php
/**
 * The Horde_Currencies and Horde_CurrenciesMapper classes provide Rdo
 * extensions used for dealing with currency data.
 *
 * $Horde: incubator/Horde_Currencies/Currencies.php,v 1.29 2010/02/01 10:32:05 jan Exp $
 *
 * Copyright 2006-2009 Duck <duck@obala.net>
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Duck <duck@obala.net>
 * @package Horde_Currencies
 */
class Horde_Currencies extends Horde_Rdo_Base {

    /**
     * Formats prices for display purposes.
     *
     * Will create a string with the correct number of decimal places, decimal
     * separator, thousands separator, currency symbol and currency symbol
     * spacing.
     *
     * @param float $price     The price value to format.
     * @param array $info      Currency format parameters.
     * @param boolean $symbol  Whether to add the currency symbol.
     * @param boolean $decimals  Respect currency decimal places
     *
     * @return string  Formatted price string.
     */
    public static function formatPrice($price, $info = null, $symbol = true, $decimals = true)
    {
        if (!$info) {
            $info = Horde_CurrenciesMapper::getDefaultCurrency();
        } elseif (is_object($info)) {
            $info = iterator_to_array($info);
        }

        if (!$symbol) {
            return number_format($price,
                                 ($decimals === true) ? $info['frac_digits'] : self::_decimals($price, $decimals),
                                 $info['mon_decimal_point'],
                                 $info['mon_thousands_sep']);
        }

        $space = $info['p_sep_by_space'] ? ' ' : '';
        if ($info['p_cs_precedes']) {
            $info['format'] = '%1$s' . $space . '%2$s';
        } else {
            $info['format'] = '%2$s' . $space . '%1$s';
        }

        /* Use dot if no decimal point specified. */
        if (empty($info['mon_decimal_point'])) {
            $info['mon_decimal_point'] = '.';
        }

        return sprintf($info['format'],
                       $info['currency_symbol'],
                       number_format($price,
                                     ($decimals === true) ? $info['frac_digits'] : self::_decimals($price, $decimals),
                                     $info['mon_decimal_point'],
                                     $info['mon_thousands_sep']));
    }

    /**
     * Get number of decimals
     *
     * @param float $price     The price value to format.
     * @param boolean $decimals  Respect currency decimal places
     *
     * @return string  Formatted price string.
     */
    private static function _decimals($price, $decimals)
    {
        if (is_numeric($decimals)) {

            if (!$info) {
                $info = Horde_CurrenciesMapper::getDefaultCurrency();
            } elseif (is_object($info)) {
                $info = iterator_to_array($info);
            }

            return $decimals;
        }

        $pos = max((int)strrpos($price, ','), (int)strrpos($price, '.'));
        return strlen($price) - $pos - 1;
    }

    /**
     * Parse curerncy formated string to float
     *
     * @param string $price     The price value to format.
     * @param array $info      Currency format parameters.
     *
     * @return flaot  Parse value
     */
    static function toFloat($price, $info = null)
    {
        if (is_numeric($price)) {
            return $price;
        }

        if (!$info) {
            $info = Horde_CurrenciesMapper::getDefaultCurrency();
        } elseif (is_object($info)) {
            $info = iterator_to_array($info);
        }

        // Remove thousands separator
        if (strpos($price, $info['mon_thousands_sep']) !== false) {
            $price = str_replace($info['mon_thousands_sep'], '', $price);
        }

        // Remove thousands separator
        if ($info['mon_decimal_point'] != '.' &&
            strpos($price, $info['mon_decimal_point']) !== false) {
            $price = str_replace($info['mon_decimal_point'], '.', $price);
        }

        // Remove thousands separator
        if (strpos($price, $info['currency_symbol']) !== false) {
            $part = $info['p_sep_by_space'] ? ' ' : '';
            if ($info['p_cs_precedes']) {
                $part = $info['currency_symbol'] . $part;
            } else {
                $part .= $info['currency_symbol'];
            }
            $price = str_replace($part, '', $price);
        }

        return (float)trim($price);
    }
}

/**
 * @package Horde_Currencies
 */
class Horde_CurrenciesMapper extends Horde_Rdo_Mapper {

    /**
     * The name of the SQL table.
     *
     * @var string
     */
    protected $_table = 'horde_currencies';

    /**
     * Tricks Horde SQL config for PDO Adapter.
     *
     * @return Horde_Rdo_Adapter_Pdo
     */
    public function getAdapter()
    {
        $GLOBALS['conf']['sql']['adapter'] = 'pdo_' . $GLOBALS['conf']['sql']['phptype'];

        return Horde_Db_Adapter::factory($GLOBALS['conf']['sql']);
    }

    /**
     * Return all currencies.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->find();
    }

    /**
     * Return a certain currency.
     *
     * @param string $curreny_symbol  A currency name.
     *
     * @return array
     */
    public function getOne($currency_symbol)
    {
        return $this->findOne(array('currency_symbol' => $currency_symbol));
    }

    /**
     * Returns the default currency.
     *
     * @param string $type  Type of the returned value. One of:<pre>
     *                      'object' - Horde_Currencies object
     *                      'data'   - Hash currency data
     *                      'symbol' - Currency symbol string
     *
     * @return mixed
     */
    public function getDefault($type = null)
    {
        $currency = $this->findOne(array('exchange_rate' => 1));

        switch ($type) {

        case 'array':
            if (empty($currency)) {
                return array();
            } else {
                return iterator_to_array($currency);
            }

        case 'data':
        case 'object':
            return $currency;

        default:
            return $currency->currency_symbol;
        }
    }

    /**
     * Returns the field metadata.
     *
     * @return array
     */
    public function formMeta()
    {
        return array(
            'id' => array('humanName' => _("Id")),
            'exchange_rate' => array(
                'humanName' => _("Exchange")),
            'decimal_point' => array(
                'humanName' => _("Decimal point character")),
            'thousands_sep' => array(
                'humanName' => _("Thousands separator")),
            'int_curr_symbol' => array(
                'humanName' => _("International currency symbol (i.e. USD)")),
            'currency_symbol' => array(
                'humanName' => _("Local currency symbol (i.e. $)")),
            'mon_decimal_point' => array(
                'humanName' => _("Monetary decimal point character")),
            'mon_thousands_sep' => array(
                'humanName' => _("Monetary thousands separator")),
            'positive_sign' => array(
                'humanName' => _("Sign for positive values")),
            'negative_sign' => array(
                'humanName' => _("Sign for negative values")),
            'int_frac_digits' => array(
                'humanName' => _("International fractional digits"),
                'type' => 'int'),
            'frac_digits' => array(
                'humanName' => _("Local fractional digits"),
                'type' => 'int'),
            'p_cs_precedes' => array(
                'humanName' => _("Currency symbol precedes a positive value"),
                'type' => 'boolean'),
            'p_sep_by_space' => array(
                'humanName' => _("Space separates currency symbol"),
                'type' => 'boolean'),
            'n_cs_precedes' => array(
                'humanName' => _("Currency symbol precedes a negative value"),
                'type' => 'boolean'),
            'n_sep_by_space' => array(
                'humanName' => _("Space separates currency symbol from a negative value"),
                'type' => 'boolean'),
            'sort' => array(
                'humanName' => _("Sort"),
                'type' => 'int'),
            'p_sign_posn' => array(
                'humanName' => _("Positive parentheses"),
                'type' => 'enum',
                'params' => array(
                    array(0 => _("Parentheses surround the quantity and currency_symbol"),
                          1 => _("The sign string precedes the quantity and currency_symbol"),
                          2 => _("The sign string succeeds the quantity and currency_symbol"),
                          3 => _("The sign string immediately precedes the currency_symbol"),
                          4 => _("The sign string immediately succeeds the currency_symbol")))),
            'n_sign_posn' => array(
                'humanName' => _("Negative parentheses"),
                'type' => 'enum',
                'params' => array(
                    array(0 => _("Parentheses surround the quantity and currency_symbol"),
                          1 => _("The sign string precedes the quantity and currency_symbol"),
                          2 => _("The sign string succeeds the quantity and currency_symbol"),
                          3 => _("The sign string immediately precedes the currency_symbol"),
                          4 => _("The sign string immediately succeeds the currency_symbol")))));
    }

    /**
     * Get Currencies
     *
     * @return array currencies data
     */
    static public function getCurrencies()
    {
        static $data;

        if ($data) {
            return $data;
        }

        if (($data = self::getCache())) {
            return $data;
        }

        $data = array();
        $currencies = new Horde_CurrenciesMapper();
        foreach ($currencies->getAll() as $currency) {
            $data[$currency->int_curr_symbol] = iterator_to_array($currency);
        }

        self::setCache($data);

        return $data;
    }

    /**
     * Returns the default currency.
     *
     * @param boolean $symbol  Return symbol only or all data?
     *
     * @return mixex currency code or data
     */
    static public function getDefaultCurrency($symbol = false)
    {
        static $currency;

        if (!$currency) {
            foreach (self::getCurrencies() as $currency) {
                if ((int)$currency['exchange_rate'] == 1) {
                    break;
                }
            }
        }

        return $symbol ? $value['int_curr_symbol'] : $currency;
    }

    /**
     * Wrap to expire cache
     */
    public function create($fields)
    {
        parent::create($fields);
        self::expireCache('Horde_Currencies');
    }

    public function update($object, $fields = null)
    {
        parent::update($object, $fields);
        self::expireCache('Horde_Currencies');
    }

    public function delete($object)
    {
        parent::delete($object);
        self::expireCache('Horde_Currencies');
    }

    /**
     * Retreive cache
     *
     * @return mixed    data or false cache key not exists
     */
    private static function getCache()
    {
        $data = $GLOBALS['injector']
            ->getInstance('Horde_Cache')
            ->get('Horde_Currencies', $GLOBALS['conf']['cache']['driver']);
        if ($data) {
            return unserialize($data);
        } else {
            return false;
        }
    }

    /**
     * Store cache
     *
     * @param  mixed  $data Data to save
     *
     * @return boolean if the cache was saved
     */
    private static function setCache($data)
    {
        return $GLOBALS['injector']
            ->getInstance('Horde_Cache')
            ->set('Horde_Currencies', serialize($data));
    }

    /**
     * Delete cache
     *
     * @return boolean if the cache was expired
     */
    private static function expireCache()
    {
        return $GLOBALS['injector']
            ->getInstance('Horde_Cache')
            ->expire('Horde_Currencies');
    }
}
