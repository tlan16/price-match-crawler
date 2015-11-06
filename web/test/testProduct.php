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
		
			$product = Product::create('test_product' . UDate::now(UDate::TIME_ZONE_MELB)->__toString(), md5(UDate::now()));
			var_dump($product);
			
			if($transStarted === false)
				Dao::commitTransaction();
			else echo "***warning*** $transStarted !== false";
		} catch (Exception $ex) {
			if($transStarted === false)
					Dao::rollbackTransaction();
			throw $ex;
		}
	}
}

testProduct::run();