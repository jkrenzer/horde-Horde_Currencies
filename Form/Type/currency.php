<?php
require_once 'Horde/Form.php';

/**
 * $Horde: incubator/Horde_Currencies/Form/Type/currency.php,v 1.4 2008/12/04 18:28:00 duck Exp $
 */
class Horde_Form_Type_currency extends Horde_Form_Type {

    var $_currency;

    function init($currency = null)
    {
        if (is_null($currency)) {
            // Nonting passed get the default currency
            $this->_currency = $this->_getDefaultCurrency();
        } elseif (is_int($currency)) {
            // A frac_digits value passed, load the default currency
            $this->_currency = $this->_getDefaultCurrency();
            if ($currency < 0) {
                // Allow unlimited decimals if -1 passed
                unset($this->_currency['frac_digits']);
            } else {
                // Set the digits count
                $this->_currency['frac_digits'] = $currency;
            }
        } elseif (!is_array($currency)) {
            // Horde_Currencies Object was passed
            $this->_currency = iterator_to_array($currency);
        } else {
            // An array with currency data was passed
            $this->_currency = $currency;
        }
    }

    function isValid(&$var, &$vars, $value, &$message)
    {
        if ($var->isRequired() && empty($value) && ((string)(double)$value !== $value)) {
            $message = _("This field is required.");
            return false;
        } elseif (empty($value)) {
            return true;
        }

        /* If matched, then this is a correct numeric value. */
        if (preg_match($this->_getValidationPattern(), $value)) {
            return true;
        }

        $message = _("This field must be a valid currnecy formatted value.");
        return false;
    }

    function _getValidationPattern()
    {
        static $pattern = '';
        if (!empty($pattern)) {
            return $pattern;
        }

        /* Build the pattern. */
        $pattern = '(-)?';

        /* Only check thousands separators if locale has any. */
        if (!empty($this->_currency['mon_thousands_sep'])) {
            /* Regex to check for correct thousands separators (if any). */
            $pattern .= '((\d+)|((\d{0,3}?)([' . $this->_currency['mon_thousands_sep'] . ']\d{3})*?))';
        } else {
            /* No locale thousands separator, check for only digits. */
            $pattern .= '(\d+)';
        }
        /* If no decimal point specified default to dot. */
        if (empty($this->_currency['mon_decimal_point'])) {
            $this->_currency['mon_decimal_point'] = '.';
        }
        /* Regex to check for correct decimals (if any). */
        if (empty($this->_currency['frac_digits'])) {
            $fraction = '*';
        } else {
            $fraction = '{0,' . $this->_currency['frac_digits'] . '}';
        }
        $pattern .= '([' . $this->_currency['mon_decimal_point'] . '](\d' . $fraction . '))?';

        /* Put together the whole regex pattern. */
        $pattern = '/^' . $pattern . '$/';

        return $pattern;
    }

    function _getDefaultCurrency()
    {
        require_once HORDE_BASE . '/incubator/Horde_Currencies/Currencies.php';

        return Horde_CurrenciesMapper::getDefaultCurrency();
    }

    function getInfo(&$vars, &$var, &$info)
    {
        $value = $vars->get($var->getVarName());
        $value = str_replace($this->_currency['mon_thousands_sep'], '', $value);
        $info = str_replace($this->_currency['mon_decimal_point'], '.', $value);
    }

    /**
     * Return info about field type.
     */
    function about()
    {
        return array('name' => _("Currency value"));
    }

}
