<?php

class StoreInfo extends InfoAbstract
{
	protected $store;
	
	/**
	 * Getter funciton for Store
	 * @return Store
	 */
	public function getStore()
	{
		$this->loadManyToOne('store');
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
		DaoMap::begin($this, 'store_info');
		DaoMap::setManyToOne('store', 'Store');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
}