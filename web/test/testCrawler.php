<?php
require_once dirname(__FILE__) . '/testAbstract.php';
ini_set('memory_limit','2048M');

class testCrawler extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		$productIds = Dao::getResultsNative('SELECT `id` FROM `product`');
		$productIds = array_map(create_function('$a', 'return intval($a["id"]);'), $productIds);
		echo 'Product: id=' . Product::get($argv[1])->getId() . ', sku="' . Product::get($argv[1])->getSku() . '"' . "\n\n";
		
		foreach ($productIds as $productId)
		{
			try {
				$product = Product::get($productId);
				if(!$product instanceof Product)
					continue;
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