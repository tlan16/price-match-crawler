<?php
/** ProductInfoType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductInfoType extends InfoTypeAbstract
{
	const ID_MATERIAL = 1;
	const ENTITY_NAME_MATERIAL = 'Material';
	
	const ID_CATEGORY = 2;
	const ENTITY_NAME_CATEGORY = 'Category';
	
	const ID_STORE = 3;
	const ENTITY_NAME_STORE = 'Store';
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'pro_info_type');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}