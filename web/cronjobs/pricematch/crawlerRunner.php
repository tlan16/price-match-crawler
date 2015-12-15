<?php
ini_set('memory_limit','1024M');
require_once dirname(__FILE__) . '/testAbstract.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

$productIds = Dao::getResultsNative('SELECT `id` FROM `product`');
$productIds = array_map(create_function('$a', 'return intval($a["id"]);'), $productIds);
$started = array();
$started['time'] = UDate::now()->getUnixTimeStamp();
$started['count'] = Record::countByCriteria('active = 1');

foreach ($productIds as $productId)
{
    if(($productId = intval($productId)) !== 0 && Product::get($productId) instanceof Product)
    {
        $output = array();
        exec($cmd, $output);
    }
    //statics
    $totalRecord = intval(Record::countByCriteria('active = 1')) - intval($started['count']);
    $timeDiff= intval(UDate::now()->getUnixTimeStamp()) - intval($started['time']);
    if($timeDiff !== 0)
        echo '***report***' . __CLASS__ . '::' . __FUNCTION__ . ': '
            . 'current product id: ' . $productId
            . ', ' . trim($totalRecord) . ' records in ' . trim($timeDiff) . ' seconds'
            . ', ' . trim(round($totalRecord / $timeDiff, 4)) . ' records/s'
            . ', ' . self::get_memory_usage_string()
            . PHP_EOL;
}