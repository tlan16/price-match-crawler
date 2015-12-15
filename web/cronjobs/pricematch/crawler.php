<?php
ini_set('memory_limit','512M');
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

if(isset($argv) && isset($argv[1]) && ($productId = intval($argv[1])) !== 0 && ($product = Product::get($productId)) instanceof Product)
{
    try {
        staticiceConnector::getPrices($product, $debug);
        unset($product);
    } catch (Exception $ex) {
        echo '***warning***' . $ex->getMessage() . PHP_EOL . $ex->getTraceAsString() . PHP_EOL;
    }
}