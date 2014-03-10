<?php

require_once 'Horde/Form/Renderer.php';

/**
 * Horde_UI_VarRenderer_datetime_xhtml class, extends Horde_Ui_VarRenderer_Html.
 *
 * $Horde: incubator/Horde_Currencies/UI/VarRenderer/currency_xhtml.php,v 1.5 2009/12/10 17:42:31 jan Exp $
 *
 * Copyright 2004-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsdl.php.
 *
 * @author  Chuck Hagenbuch
 * @package Whups
 */
class Horde_UI_VarRenderer_currency_xhtml extends Horde_Ui_VarRenderer_Html {

    function _renderVarInput_currency($form, &$var, &$vars)
    {
        $value = $var->getValue($vars);
        $currency = $var->type->getProperty('currency');

        if (!empty($currency['mon_decimal_point'])) {
            $value = str_replace('.', $currency['mon_decimal_point'], $value);
        }

        $varname = @htmlspecialchars($var->getVarName(), ENT_QUOTES, $this->_charset);
        return sprintf('<input type="text" size="10" name="%s" id="%s" value="%s"%s /> %s',
                       $varname,
                       $varname,
                       $value,
                       $this->_getActionScripts($form, $var),
                       $currency['currency_symbol']);
    }

}
