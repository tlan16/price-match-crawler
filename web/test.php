<?php
require_once dirname(__FILE__) . '/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

function createEntity($class, $howMany) {
    $return = array();
    for($i = 0; $i < $howMany; $i++) {
        $entity = new $class();
        $return[] = $entity->setName(substr($class, 0, 1) . '_' . $i)
            ->setDescription('')
            ->save();
    }
    return $return;
}

try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}

	$user = UserAccount::get(24);
	$user->clearRoles();
	$user->addRole(Role::get(Role::ID_SYSTEM_ADMIN), Store::get(1));
	$user->addRole(Role::get(Role::ID_SYSTEM_DEVELOPER), Store::get(1));
	$user->addRole(Role::get(Role::ID_SYSTEM_DEVELOPER), Store::get(2));
	
	echo print_r($user->getJson(), true);
	
	if($transStarted === false)
		Dao::commitTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}