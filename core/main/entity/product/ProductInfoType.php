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
	
	const ID_CATEGORY = 2;
	
	const ID_STORE = 3;
	
	const ID_LABEL = 4;
	
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