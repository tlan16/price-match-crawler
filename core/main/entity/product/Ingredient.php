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
}