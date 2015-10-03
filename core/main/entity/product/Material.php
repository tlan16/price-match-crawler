<?php
/** Material Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Material extends InfoEntityAbstract
{
	public function getNutrition(Nutrition $nutrition)
	{
		$mnArray = MaterialNutrition::getAllByCriteria('materialId = ? and nutritionId = ?', array($this->getId(), $nutrition->getId()));
		return (count($mnArray) > 0 && $mnArray[0] instanceof MaterialNutrition) ? $mnArray[0] : false; 
	}
	
	/**
	 * This function finds all the nutritions of a material
	 * @return Array[] of MaterialNutrition
	 */
	public function getAllNutritions()
	{
		return MaterialNutrition::getAllByCriteria('materialId = ?', array($this->getId()), true);
	}
	
	public function removeNutrition(Nutrition $nutrition)
	{
		
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
		$materialNutrition = $this->getNutrition($nutrition);
		
		if($materialNutrition instanceof MaterialNutrition)
		{
			$materialNutrition->setNutrition($nutrition)
							  ->setQty($qty)
							  ->setServeMeasurement($serveMeasurement)
							  ->save();
		}
		else
		{
			$materialNutrition = new MaterialNutrition();
			$materialNutrition->setMaterial($this)
							  ->setNutrition($nutrition)
							  ->setQty($qty)
							  ->setServeMeasurement($serveMeasurement)
							  ->setActive(true)
							  ->save();
		}
		
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