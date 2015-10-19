<?php
/** Material Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Material extends InfoEntityAbstract
{
	/**
	 * This function gets the MaterialNutrition of the material based on the nutrition
	 * @param Nutrition $nutrition
	 * @return
	 * 		MaterialNutrition	--- if found
	 * 		FALSE				--- if not found
	 */
	public function getMaterialNutrition(Nutrition $nutrition)
	{
		$mnArray = MaterialNutrition::getAllByCriteria('materialId = ? and nutritionId = ?', array($this->getId(), $nutrition->getId()), true, 1, 1);
		return (count($mnArray) > 0 && $mnArray[0] instanceof MaterialNutrition) ? $mnArray[0] : false;
	}

	/**
	 * This function finds all the nutritions of a material
	 * @return Array[] of MaterialNutrition
	 */
	public function getAllMaterialNutritions()
	{
		return MaterialNutrition::getAllByCriteria('materialId = ?', array($this->getId()), true);
	}

	/**
	 * THis function removes the nutrition of a material
	 * @param Nutrition $nutrition
	 * @return Material
	 */
	public function removeMaterialNutrition(Nutrition $nutrition)
	{
		MaterialNutrition::updateByCriteria('active = ?', 'materialId = ? and nutritionId = ?', array(0, $this->getId(), $nutrition->getId()));
		return $this;
	}

	/**
	 * This function adds Nutrition to a Material
	 *
	 * @param Nutrition $nutrition
	 * @param int $qty
	 * @param ServeMeasurement $serveMeasurement
	 * @return Material
	 */
	public function addNutrition(Nutrition $nutrition, $qty, ServeMeasurement $serveMeasurement)
	{
		$materialNutrition = $this->getMaterialNutrition($nutrition);

		if(!$materialNutrition instanceof MaterialNutrition)
			$materialNutrition = new MaterialNutrition();

		$materialNutrition->setMaterial($this)
						  ->setNutrition($nutrition)
						  ->setQty($qty)
				  		  ->setServeMeasurement($serveMeasurement)
						  ->save();
		return $this;
	}

	public function getIngredients()
	{
		$materialInfoArray = MaterialInfo::getAllByCriteria('materialId = ? and typeId = ?', array($this->getId(), MaterialInfoType::ID_INGREDIENT));
		$ingredientIdArray = array();
		foreach($materialInfoArray as $mi)
			$ingredientIdArray[] = (trim($mi->getEntityId()) !== '' ? trim($mi->getEntityId()) : trim($mi->getValue()));

		if(count($ingredientIdArray) <= 0)
			return array();
		$ingredientIdArray = array_unique($ingredientIdArray);
		$criteria = "id IN (".implode(", ", array_fill(0, count($ingredientIdArray), '?')).")";
		return Ingredient::getAllByCriteria($criteria, $ingredientIdArray);
	}

	public function setIngredient(Ingredient $ingredient)
	{
		return $this->addIngredient($ingredient);
	}
	public function addIngredient(Ingredient $ingredient)
	{
		if(MaterialInfo::countByCriteria('materialId = ? and typeId = ? and entityId = ? and entityName =?', array($this->getId(), MaterialInfoType::ID_INGREDIENT, $ingredient->getId(), get_class($ingredient))) > 0)
			return $this;
		$this->addInfo(MaterialInfoType::get(MaterialInfoType::ID_INGREDIENT), $ingredient);
		return $this;
	}
	public function clearIngredients()
	{
		MaterialInfo::deleteByCriteria('materialId = ? and typeId = ? and entityName = ?',
										array($this->getId(), MaterialInfoType::ID_INGREDIENT, 'Ingredient') );
		return $this;
	}
	public function removeIngredient(Ingredient $ingredient)
	{
		MaterialInfo::updateByCriteria("active = ?", 'materialId = ? and typeId = ?  and entityId = ? and entityName = ?',
										array(0, $this->getId(), MaterialInfoType::ID_INGREDIENT, $ingredient->getId(), get_class($ingredient)));
		return $this;
	}

	/**
	 * This function removes all the Ingredients of a Material
	 * @return Material
	 */
	public function removeAllIngredients()
	{
		MaterialInfo::updateByCriteria('active = ?', 'materialId = ? and entityName = ? and typeId = ?', array(0, $this->getId(), 'Ingredient', MaterialInfoType::ID_INGREDIENT));
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see InfoEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
		$array['infos'] = array();
		$array['infos']['material_nutrition'] = array();

		$mnArray = $this->getAllMaterialNutritions();
		foreach($mnArray as $mn)
		{
			$tmp = array();
			$tmp['nutrition'] = $mn->getNutrition()->getJson();
			$tmp['qty'] = $mn->getQty();
			$tmp['serveMeasurement'] = $mn->getServeMeasurement()->getJson();

			$array['infos']['material_nutrition'][] = $tmp;
		}

		$array['infos']['ingredients'] = (count(($ingredients = $this->getIngredients())) > 0 ? array_map(create_function('$a', 'return $a->getJson();'), $ingredients) : array());

		return parent::getJson($array, $reset);
	}

	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'mat');

		parent::__loadDaoMap();

		DaoMap::commit();
	}
	/**
	 * Creating a material with params
	 *
	 * @param string $name
	 * @param string $description
	 * @param array  $ingredients
	 *
	 * @return Material
	 */
	public static function createWithParams($name, $description, array $ingredients = array())
	{
	    $material = parent::create($name, $description);
	    foreach($ingredients as $ingredient)
	        $material->addIngredient($ingredient);
	    return $material;
	}
}