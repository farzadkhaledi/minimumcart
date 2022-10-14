<?php
if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

use Illuminate\Database\Capsule\Manager as Capsule;

add_hook('AfterCalculateCartTotals', 1, function ($vars) {
    if (!defined('CLIENTAREA')) {
        return;
    }
    if (isset($_SESSION['adminid'])) {
        return;
    }
    $_SESSION['minimumcarttotal'] = $vars['total']->toNumeric();
});

add_hook('ShoppingCartValidateCheckout', 1, function ($vars) {
    if (!defined('CLIENTAREA')) {
        return;
    }
    if (!isset($_SESSION['minimumcarttotal'])) {
        return;
    }
    if (isset($_SESSION['adminid'])) {
        return;
    }
    $userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : "";
    $currencyid = isset($_SESSION["currency"]) ? $_SESSION["currency"] : "";
    $currency = getCurrency($userid, $currencyid);
    $minorder = Capsule::table('tbladdonmodules')->where('module', 'minimumcart')->where('setting', 'min_' . $currency['code'])->value('value');
    if ($minorder && $minorder > 0 && $_SESSION['minimumcarttotal'] < $minorder) {
        $lang = \Lang::getName();
        global $CONFIG;
        if (file_exists(ROOTDIR . "/modules/addons/minimumcart/lang/" . $lang . ".php")) {
            include(ROOTDIR . "/modules/addons/minimumcart/lang/" . $lang . ".php");
        } elseif (file_exists(ROOTDIR . "/modules/addons/minimumcart/lang/" . $CONFIG['Language'] . ".php")) {
            include(ROOTDIR . "/modules/addons/minimumcart/lang/" . $CONFIG['Language'] . ".php");
        } elseif (file_exists(ROOTDIR . "/modules/addons/minimumcart/lang/english.php")) {
            include(ROOTDIR . "/modules/addons/minimumcart/lang/english.php");
        }
        return [
            sprintf($_ADDONLANG['minimumcart'], formatCurrency($minorder)),
        ];
    }
});

add_hook('AfterShoppingCartCheckout', 1, function ($vars) {
    if (isset($_SESSION['adminid'])) {
        return;
    }
    unset($_SESSION['minimumcarttotal']);
});
