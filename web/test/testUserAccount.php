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
		
			$password = 'test';
			$obj = UserAccount::getAllByCriteria('username = ?', array('frank'))[0];
			$obj->setUserName('testuser')->setPassword(password_hash($password, PASSWORD_DEFAULT))->save();
			var_dump($obj);
			
			if($transStarted === false)
			{
				Dao::commitTransaction();
				echo 'success ' . PHP_EOL;
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