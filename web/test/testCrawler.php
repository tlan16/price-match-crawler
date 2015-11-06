<?php
require_once dirname(__FILE__) . '/testAbstract.php';

class testCrawler extends testAbstract
{
	public static function run($debug = true)
	{
		try {
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
		
			$prices = staticiceConnector::getPrices('vs247h', $debug);
			print_r($prices);
			
			if($transStarted === false)
				Dao::commitTransaction();
		} catch (Exception $ex) {
			if($transStarted === false)
					Dao::rollbackTransaction();
			throw $ex;
		}
	}
}

testCrawler::run();