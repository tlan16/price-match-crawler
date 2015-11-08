<?php
require_once dirname(__FILE__) . '/testAbstract.php';

class testB2BConnector extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		magentoB2BProductConnector::importProducts();
	}
}

testB2BConnector::run();