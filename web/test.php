<?php
require_once dirname(__FILE__) . '/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}

	$obj = Ingredient::create('fish');
	var_dump($obj);
	$obj->addAllergent(Allergent::get(3));
	
	if($transStarted === false)
		Dao::commitTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}