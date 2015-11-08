<?php
require_once dirname(__FILE__) . '/testAbstract.php';

class testProduct extends testAbstract
{
	public static function run($debug = true)
	{
		parent::run();
		
		$dataFile = dirname(__FILE__) . '/data/product_list_2015_11_08_12_43_20.json';
		$data = json_decode(file_get_contents($dataFile), true);
		
		$rowCount = 0;
		foreach ($data as $row)
		{
			echo 'row: ' . $rowCount . PHP_EOL;
			try {
				$transStarted = false;
				try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			
				if(!isset($row['sku']) || ($sku = trim($row['sku'])) === '')
					continue;
				$name = '';
				if(isset($row['name']) && ($tmp = trim($row['name'])) !== '')
					$name = $tmp;
				
				$product = Product::create($sku, $name);
				
				if($transStarted === false)
				{
					Dao::commitTransaction();
					echo 'success imported product sku=' . $sku . ', description=' . $name . PHP_EOL;
					$rowCount++;
				}
				else echo "***warning*** $transStarted !== false";
			} catch (Exception $ex) {
				if($transStarted === false)
						Dao::rollbackTransaction();
				throw $ex;
			}
		}
	}
}

testProduct::run();