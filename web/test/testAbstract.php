<?php
require_once dirname(__FILE__) . '/../bootstrap.php';

abstract class testAbstract
{
	public static function createEntity($class, $howMany) {
	    $return = array();
	    for($i = 0; $i < $howMany; $i++) {
	        $entity = new $class();
	        $return[] = $entity->setName(substr($class, 0, 1) . '_' . $i)
	            ->setDescription('')
	            ->save();
	    }
	    return $return;
	}
	public static function run()
	{
		Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
	}
}
