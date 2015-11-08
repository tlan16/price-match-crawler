<?php
require_once dirname(__FILE__) . '/testAbstract.php';

class testCrawler extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		$rowCount = 0;
		foreach (Product::getAll() as $product)
		{
			try {
				staticiceConnector::getPrices($product, $rowCount, $debug);
			} catch (Exception $ex) {
				echo '***warning***' . $ex->getMessage() . PHP_EOL . $ex->getTraceAsString() . PHP_EOL;
				continue;
			}
		}
	}
}

testCrawler::run();