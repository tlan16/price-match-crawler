<?php

class StoreInfo extends InfoAbstract
{
	private $store;
	
	/**
	 * Getter funciton for Store
	 * @return Store
	 */
	public function getStore()
	{
		return $this->store; 	
	}
	
	/**
	 * Setter function for Store
	 * @param Store $store
	 * @return StoreInfo
	 */
	public function setStore(Store $store)
	{
		$this->store = $store;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'str_info');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
}