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
			staticiceConnector::getPrices($product, $rowCount, $debug);
		}
	}
}

testCrawler::run();