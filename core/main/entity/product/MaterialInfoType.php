<?php
/** MaterialInfoType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class MaterialInfoType extends InfoTypeAbstract
{
	const ID_INGREDIENT = 1;
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'mat_info_type');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}