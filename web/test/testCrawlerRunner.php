<?php
require_once dirname(__FILE__) . '/testAbstract.php';
ini_set('memory_limit','2048M');

class testCrawlerRunner extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		if(isset($argv) && isset($argv[1]) && ($productId = intval($argv[1])) !== 0 && ($product = Product::get($productId)) instanceof Product)
		{
			unset($product);
			try {
				$output = '';
				$timeout = 60; // in seconds
				$cmd = 'php ' . dirname(__FILE__). '/testCrawler.php ' . $productId;
				$output = self::ExecWaitTimeout($cmd, $timeout);
				
				echo print_r($output, true) . PHP_EOL;
			} catch (Exception $ex) {
				echo '***warning***' . $ex->getMessage() . PHP_EOL . $ex->getTraceAsString() . PHP_EOL;
				continue;
			}
		}
	}
}

testCrawlerRunner::run();