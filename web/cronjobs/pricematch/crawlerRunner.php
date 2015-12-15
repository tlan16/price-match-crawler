<?php
ini_set('memory_limit','1024M');
require_once dirname(__FILE__) . '/../../bootstrap.php';
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
        $cmd = 'php ' . dirname(__FILE__). '/crawler.php ' . $productId;
        exec($cmd, $output);
        foreach ($output as $line)
        	echo "\t" . $line . PHP_EOL;
    }
    //statics
    $totalRecord = intval(Record::countByCriteria('active = 1')) - intval($started['count']);
    $timeDiff= intval(UDate::now()->getUnixTimeStamp()) - intval($started['time']);
    if($timeDiff !== 0)
        echo 'current product id: ' . $productId
            . ', ' . trim($totalRecord) . ' records in ' . trim($timeDiff) . ' seconds'
            . ', ' . trim(round($totalRecord / $timeDiff, 4)) . ' records/s'
            . ', ' . get_memory_usage_string()
            . PHP_EOL;
}

function get_memory_usage_string() {
	$mem_usage = memory_get_usage(true);
	$mem_peak_usage = memory_get_peak_usage(true);
	return 'memory usage: ' . human($mem_usage) . '(peak ' . human($mem_peak_usage) . ')';
}
function human($size)
{
	$unit=array('B','KB','MB','GB','TB','PB');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}