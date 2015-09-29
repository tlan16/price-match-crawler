<?php
/** IngredientInfoType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class IngredientInfoType extends InfoTypeAbstract
{
	const ID_ALLERGENT = 1;
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'ingr_info_type');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}