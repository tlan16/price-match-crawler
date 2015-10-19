<?php

class StoreInfoType extends InfoTypeAbstract
{
	const ID_USERACCOUNTID = 1;
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'store_info_type');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
}