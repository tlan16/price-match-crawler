<?php
ini_set('memory_limit','1024M');
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
echo "Begin at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
$productIds = Dao::getResultsNative('SELECT `id` FROM `product` where active = 1 order by `id` desc');
$productIds = array_map(create_function('$a', 'return intval($a["id"]);'), $productIds);

echo "    starting to archive old data : " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
$result = Dao::getResultsNative('Call PreparePriceMatch()');
echo "    Finished archiving old data : " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
//$started = array();
//$started['time'] = UDate::now();
//$started['count'] = Record::countByCriteria('active = 1');
echo "Got [ " . count($productIds) . " ] products . " . "\n";
$count = 1;
$sku = '';
foreach ($productIds as $productId)
{
	$product = Product::get($productId);
	$sku = '';
	if ($product instanceof Product)
	{
		$sku = $product->getSku();
	}
	echo "+++ No." . $count . " ProudctId [ " . $productId ." ], Sku [ " . $sku . " ] " . "\n";
    if(($productId = intval($productId)) !== 0 && $product instanceof Product)
    {
        $output = array();
        $cmd = 'php ' . dirname(__FILE__). '/crawler.php ' . $productId;
        exec($cmd, $output);
        foreach ($output as $line)
        	echo "\t" . $line . PHP_EOL;
    }
    $count++;
    //statics
    //$totalRecord = intval(Record::countByCriteria('active = 1')) - intval($started['count']);
//     $timeDiff= intval(UDate::now()->getUnixTimeStamp()) - intval($started['time']->getUnixTimeStamp());
//     $timeDiffHuman = get_date_diff(trim($started['time']), trim(UDate::now()));
//     if($timeDiff !== 0)
//         echo 'current product id: ' . $productId
//             . ', ' . trim($totalRecord) . ' records in ' . $timeDiffHuman
//             . ', ' . trim(round($totalRecord / $timeDiff, 4)) . ' records/s'
//             . ', ' . get_memory_usage_string()
//             . PHP_EOL;
}

echo "End at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";


function get_date_diff( $time1, $time2, $precision = 2 ) {
    // If not numeric then convert timestamps
    if( !is_int( $time1 ) ) {
        $time1 = strtotime( $time1 );
    }
    if( !is_int( $time2 ) ) {
        $time2 = strtotime( $time2 );
    }
    // If time1 > time2 then swap the 2 values
    if( $time1 > $time2 ) {
        list( $time1, $time2 ) = array( $time2, $time1 );
    }
    // Set up intervals and diffs arrays
    $intervals = array( 'year', 'month', 'day', 'hour', 'minute', 'second' );
    $diffs = array();
    foreach( $intervals as $interval ) {
        // Create temp time from time1 and interval
        $ttime = strtotime( '+1 ' . $interval, $time1 );
        // Set initial values
        $add = 1;
        $looped = 0;
        // Loop until temp time is smaller than time2
        while ( $time2 >= $ttime ) {
            // Create new temp time from time1 and interval
            $add++;
            $ttime = strtotime( "+" . $add . " " . $interval, $time1 );
            $looped++;
        }
        $time1 = strtotime( "+" . $looped . " " . $interval, $time1 );
        $diffs[ $interval ] = $looped;
    }
    $count = 0;
    $times = array();
    foreach( $diffs as $interval => $value ) {
        // Break if we have needed precission
        if( $count >= $precision ) {
            break;
        }
        // Add value and interval if value is bigger than 0
        if( $value > 0 ) {
            if( $value != 1 ){
                $interval .= "s";
            }
            // Add value and interval to times array
            $times[] = $value . " " . $interval;
            $count++;
        }
    }
    // Return string with times
    return implode( ", ", $times );
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