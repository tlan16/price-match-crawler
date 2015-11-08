<?php
require_once dirname(__FILE__) . '/testAbstract.php';

class testProduct extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		try {
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
		
			$vendor = Vendor::create('new_vendor_' . trim(UDate::now(UDate::TIME_ZONE_MELB)));
			var_dump($vendor);
			
			if($transStarted === false)
			{
				Dao::commitTransaction();
				echo 'success imported Vendor name=' . $vendor->getName() . PHP_EOL;
			}
			else echo "***warning*** $transStarted !== false";
		} catch (Exception $ex) {
			if($transStarted === false)
				Dao::rollbackTransaction();
			throw $ex;
		}
	}
}

testProduct::run();