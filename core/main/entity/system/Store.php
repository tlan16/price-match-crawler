<?php

class Store extends ResourceAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'str');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
}