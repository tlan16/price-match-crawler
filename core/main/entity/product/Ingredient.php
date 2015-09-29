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
	public function addAllergent($name, $description = '')
	{
		$allergent = Allergent::create($name, $description);
		if(!$allergent instanceof Allergent)
			throw new Exception('system error');
		$infoType = IngredientInfoType::get(IngredientInfoType::ID_ALLERGENT);
		if(!$infoType instanceof IngredientInfoType)
			throw new Exception('system error');
		$info = IngredientInfo::create($this, $infoType, null, $allergent);
		return $this;
	}
}