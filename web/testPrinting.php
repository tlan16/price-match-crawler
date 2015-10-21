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

	$materials = createEntity('Material', 4);
	$ingredients = createEntity('Ingredient', 3);
	$allergents = createEntity('Allergent', 2);
	$serveMeasurements = createEntity('ServeMeasurement', 3);
	$nutritions = createEntity('Nutrition', 3);

	//create ingredient
	foreach($materials as $material) {
	    foreach($ingredients as $ingredient) {
    	    foreach($allergents as $allergent) {
    	        $ingredient->addAllergent($allergent);
    	    }
    	    $material->addIngredient($ingredient);
    	    foreach($nutritions as $nutrition) {
        	    foreach($serveMeasurements as $index => $serveMeasurement) {
        	        $material->addNutrition($nutrition, $index, $serveMeasurement);
        	    }
    	    }
	    }
	}

	$obj = Product::createWithParams('test', 'test description', '9348466001232', '123', '+3 day', '23.40', 'v1', Material::getAll());
	$newLabel = null;
	$obj->printLabel(null, null, $newLabel);
	$imgPath = $newLabel->generateImg(270, 800);
	header('Content-Type: image/jpeg');
	echo file_get_contents($imgPath);

	if($transStarted === false)
		Dao::rollbackTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}