<?php
/** Ingredient Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Ingredient extends InfoEntityAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'ingr');
				
		parent::__loadDaoMap();

		DaoMap::commit();
	}
	
	/**
	 * Gets all the allergens of the ingredient
	 * @return Array of Allergent
	 */
	public function getAllergents()
	{
		$allergentArray = array();
		
		$ingredientInfoArray = IngredientInfo::getAllByCriteria('ingredientId = ? and typeId = ?', 
																	array($this->getId(), IngredientInfoType::ID_ALLERGENT));
		if(count($ingredientInfoArray) > 0)
		{
			foreach($ingredientInfoArray as $ingredientInfo)
				$allergentArray[] = ((trim($ingredientInfo->getEntityId()) !== '') ? trim($ingredientInfo->getEntityId()) : trim($ingredientInfo->getValue()));

			$allergentArray = array_unique($allergentArray);
			
			$criteria = array_fill(0, count($allergentArray), '?');
			$criteria = 'id IN ('.implode(", ", $criteria).')';
			
			$allergentArray = Allergent::getAllByCriteria($criteria, $allergentArray);
		}
		
		return $allergentArray;
	}
	
	/**
	 * Clear all the Allergents of the ingredient
	 * @return Ingredient
	 */
	public function clearAllergents()
	{
		IngredientInfo::deleteByCriteria('ingredientId = ? and ingredientInfoTypeId = ?', array($this->getId(), IngredientInfoType::ID_ALLERGENT));
		return $this;
	}
	
	/**
	 * add a allergent to self
	 * 
	 * @param string $name
	 * @param string $description
	 * 
	 * @return Ingredient
	 * @throws Exception
	 */
	public function addAllergent(Allergent $allergent)
	{
		$infoType = IngredientInfoType::get(IngredientInfoType::ID_ALLERGENT);
		
		$this->addInfo($infoType, $allergent, '', false);
		/*
		if(!$infoType instanceof IngredientInfoType)
			throw new Exception('system error');
		$info = IngredientInfo::create($this, $infoType, null, $allergent);
		*/
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
		
		$array['infos']['allergents'] = ( (count($allgergents = $this->getAllergents())) === 0 ? array() : array_map(create_function('$a', 'return $a->getJson();'), $allgergents) );
		
		return parent::getJson($array, $reset);	
	}
}