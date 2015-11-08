<?php
require_once dirname(__FILE__) . '/testAbstract.php';
ini_set('memory_limit','2048M');

class testCrawler extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		if(isset($argv) && isset($argv[1]) && ($productId = intval($argv[1])) !== 0 && ($product = Product::get($productId)) instanceof Product)
		{
			try {
				staticiceConnector::getPrices($product, $debug);
				unset($product);
			} catch (Exception $ex) {
				echo '***warning***' . $ex->getMessage() . PHP_EOL . $ex->getTraceAsString() . PHP_EOL;
				continue;
			}
		}
	}
}

testCrawler::run();