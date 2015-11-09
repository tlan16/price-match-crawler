<?php
require_once dirname(__FILE__) . '/testAbstract.php';
ini_set('memory_limit','2048M');

class testCrawlerRunner extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		$productIds = Dao::getResultsNative('SELECT `id` FROM `product`');
		$productIds = array_map(create_function('$a', 'return intval($a["id"]);'), $productIds);
		$started = array();
		$started['time'] = UDate::now();
		$started['count'] = Record::countByCriteria('active = 1');
		
		foreach ($productIds as $productId)
		{
			if(($productId = intval($productId)) !== 0 && ($product = Product::get($productId)) instanceof Product)
			{
				unset($product);
				try {
					$output = '';
					$timeout = 120; // in seconds
					$cmd = 'php ' . dirname(__FILE__). '/testCrawler.php ' . $productId;
					$output = self::ExecWaitTimeout($cmd, $timeout);
					
					echo print_r($output, true) . PHP_EOL;
				} catch (Exception $ex) {
					echo '***warning***' . $ex->getMessage() . PHP_EOL . $ex->getTraceAsString() . PHP_EOL;
					continue;
				}
			}
			echo  '***report***' . __CLASS__ . '::' . __FUNCTION__ . ': Total Records = ' . ($totalRecord = Record::countByCriteria('active = 1') ) 
				. ', Product = ' . trim($productId) 
				. ', ' . trim( (intval($totalRecord) - intval($started['count'])) / (intval(UDate::now()->getUnixTimeStamp() - $started['time']->getUnixTimeStamp()) !== 0 ?: 1) ) . ' records/s'
				. PHP_EOL;
		}
	}
}

while(1)
{ testCrawlerRunner::run(); }