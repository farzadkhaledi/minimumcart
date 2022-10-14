<?php
if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

use Illuminate\Database\Capsule\Manager as Capsule;

function minimumcart_config()
{
    $currencies = WHMCS\Billing\Currency::pluck('code');
    if (!is_array($currencies)) {
        $currencies = $currencies->toArray();
    }
    $cf = [];
    foreach ($currencies as $key => $code) {
        $cf['min_' . $code] = array("FriendlyName" => $code . " Minimum Required Total", "Type" => "text", "Size" => "10", "Description" => "Leave blank to disable minimum order for this currency",);
    }
    $configarray = array(
        "name" => "Minimum Cart Total",
        "description" => "Minimum Cart Total module allows you to setup minimum required total per currency.",
        "version" => "1.0",
        "author" => "Farzad Khaledi",
        "language" => "english",
        "fields" => $cf
    );
    return $configarray;
}

function minimumcart_activate()
{

    return array("status" => "success", "description" => "Minimum Order has been activated.");
}

function minimumcart_deactivate()
{
    return array("status" => "success", "description" => "Minimum Order has been deactivated.");
}

function minimumcart_output($vars)
{

}
