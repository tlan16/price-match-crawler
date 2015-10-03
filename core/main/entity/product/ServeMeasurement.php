<?php
/** ServeManagement Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ServeMeasurement extends ResourceAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'srv_mgm');
				
		parent::__loadDaoMap();

		DaoMap::commit();
	}
	
}