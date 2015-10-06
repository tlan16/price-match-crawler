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
	
}