<?php

@define('HORDE_BASE', dirname(__FILE__) . '/../..');
require_once HORDE_BASE . '/lib/base.php';
require_once dirname(__FILE__) . '/Currencies.php';

$mapper = new Horde_CurrenciesMapper();
$currencies = $mapper->getAll();
var_dump($currencies);

try {
    $default_symbol = $mapper->getDefault();
    var_dump($default_symbol);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

try {
    $default_object = $mapper->getDefault('object');
    var_dump($default_object);

    $price = Horde_Currencies::formatPrice(100.14888, $default_object);
    var_dump($price);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
