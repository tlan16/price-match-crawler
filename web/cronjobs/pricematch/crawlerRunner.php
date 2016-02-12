<?php
ini_set('memory_limit','1024M');
require_once dirname(__FILE__) . '/../../bootstrap.php';
require dirname(__FILE__) . '/MultiProcess.php';

//if (! function_exists('pcntl_fork')) die('PCNTL functions not available on this PHP installation');
//$i = 0;
$maxChild = 100;

Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
echo "Begin at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
$products = Dao::getResultsNative('SELECT `id`,`sku` FROM `product` where active = 1 order by `id` desc');
$totalCount=count($products);



$size = ceil($totalCount / $maxChild);
echo "Got [ " . $totalCount . " ] products, size of array [ $size ] . " . "\n";

//$products = array_chunk($products, $size);

//$productIds = array_map(create_function('$a', 'return intval($a["id"]);'), $productIds);

echo "    starting to archive old data : " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
$result = Dao::getResultsNative('Call PreparePriceMatch()');
echo "    Finished archiving old data : " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
//$started = array();
//$started['time'] = UDate::now();
//$started['count'] = Record::countByCriteria('active = 1');

$job = new Multiprocess($products, 'getPriceFromStaticice', $maxChild);
$job->run();


// for ($i=0; $i<$maxChild; $i++)
// {

// 	$pid = pcntl_fork();
// 	if ($pid == -1)
// 	{
// 		// Fork failed
// 		echo ' Fork failed !!!';
// 		exit(1);
// 	}
// 	elseif ($pid)
// 	{
// 		// parent
		
// 	}
// 	else
// 	{
// 		// child
// 		$childPid = getmypid();
// 		getPriceFromStaticice($products[$i], $childPid);
// 		exit($childPid);
// 	}

// }

// while (pcntl_waitpid(0, $status) != -1)
// {
// // 	if (pcntl_wifexited($status)) 
// // 	{
// // 		echo "Child exited normally";
// // 	} 
// // 	else if (pcntl_wifstopped($status)) 
// // 	{
// // 		echo "Signal: ", pcntl_wstopsig($status), " caused this child to stop.";
// // 	} 
// // 	else if (pcntl_wifsignaled($status)) 
// // 	{
// // 		echo "Signal: ",pcntl_wtermsig($status)," caused this child to exit with return code: ", pcntl_wexitstatus($status);
// // 	}
// 	$status = pcntl_wexitstatus($status);
// 	echo "Child $status completed\n";
// }

echo "End at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";

function getPriceFromStaticice($products, $childNo)
{
	$count = 0;
	foreach ($products as $product)
	{
		$count++;
		$productId = $product["id"];
		$sku = $product["sku"];
		echo "+++ Child:[$childNo] No." . $count . " ProudctId [ " . $productId ." ], Sku [ " . $sku . " ] " . "\n";
		if(($productId = intval($productId)) !== 0)
		{
			$output = array();
			$cmd = 'php ' . dirname(__FILE__). '/crawler.php ' . $productId;
			exec($cmd, $output);
			foreach ($output as $line)
				echo "\t" . $line . PHP_EOL;
		}
	}
}

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