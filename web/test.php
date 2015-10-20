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

	$obj = Material::create('testMaterial1', 'des1');
	$obj->addIngredient(Ingredient::get(1));
	$obj->addNutrition(Nutrition::get(1), 10, ServeMeasurement::get(1));

	if($transStarted === false)
		Dao::commitTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}