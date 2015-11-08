<?php
require_once dirname(__FILE__) . '/testAbstract.php';

class testCrawler extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		foreach (Product::getAll() as $product)
			staticiceConnector::getPrices($product, $debug);
	}
}

testCrawler::run();