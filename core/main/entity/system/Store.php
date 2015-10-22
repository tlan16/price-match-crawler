<?php

class Store extends InfoEntityAbstract
{
    const ID_HEADQUQRTER = 1;
	/**
	 * Address of the store
	 *
	 * @var Address
	 */
	protected $address;
	/**
	 * Getter for the address
	 *
	 * @return Address
	 */
	public function getAddress()
	{
		$this->loadManyToOne('address');
		return $this->address;
	}
	/**
	 * Setter for the Address
	 *
	 * @param Address $address
	 *
	 * @return Store
	 */
	public function setAddress(Address $address)
	{
		$this->address = $address;
		return $this;
	}
	/**
	 *
	 * @return array
	 */
	public function getAllStoreInfos()
	{
		return StoreInfo::getAllByCriteria('storeId = ?', array($this->getId()), true);
	}
	/**
	 * adding a user
	 *
	 * @param UserAccount $user
	 *
	 * @return Store
	 */
	public function giveAccess(UserAccount $user)
	{
		if(StoreInfo::countByCriteria('storeId = ? and typeId = ? and entityId = ? and entityName = ?', array(trim($this->getId()), trim(StoreInfoType::ID_USERACCOUNTID), trim($user->getId()), get_class($user))) > 0) {
			return $this;
		}

		return $this->addInfo(StoreInfoType::get(StoreInfoType::ID_USERACCOUNTID), $user);
	}
	/**
	 * removed a user
	 *
	 * @param UserAccount $user
	 *
	 * @return Store
	 */
	public function clearAccess(UserAccount $user)
	{
		StoreInfo::deleteByCriteria('storeId = ? and typeId = ? and entityId = ? and entityName = ?', array(trim($this->getId()), trim(StoreInfoType::ID_USERACCOUNTID), trim($user->getId()), get_class($user)));
		return $this;
	}
	/***
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
		$array['info'] = array();
		$array['address'] = $this->getAddress()->getJson();

		$storeInfoArray = $this->getAllStoreInfos();
		foreach($storeInfoArray as $storeInfo)
		{
			$storeInfoType = $storeInfo->getType();

			if(!isset($array['info'][$storeInfoType->getId()]))
				$array['info'][$storeInfoType->getId()] = array();

			$array['info'][$storeInfoType->getId()][$storeInfo->getId()] = array();
			$array['info'][$storeInfoType->getId()][$storeInfo->getId()]['value'] = $storeInfo->getValue();
			$array['info'][$storeInfoType->getId()][$storeInfo->getId()]['entityId'] = $storeInfo->getEntityId();
			$array['info'][$storeInfoType->getId()][$storeInfo->getId()]['entityName'] = $storeInfo->getEntityName();
		}

		return parent::getJson($extra, $reset);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::preSave()
	 */
	public function preSave()
	{
		if(self::countByCriteria('name = ? and id !=?', array(trim($this->getName()), trim($this->getId()))) > 0)
			throw new Exception('There is a store called "' . $this->getName() . '" already!');
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'store');
		DaoMap::setManyToOne('address', 'Address', 'addr');

		parent::__loadDaoMap();
		DaoMap::commit();
	}
	/**
	 * Getting object
	 *
	 * @param int $id The id of the store
	 *
	 * @return Store
	 */
	public static function get($id)
	{
		if(!self::cacheExsits($id))
			self::addCache($id, parent::get($id));
		return self::getCache($id);
	}
	/**
	 *
	 * @param unknown $name
	 * @param unknown $description
	 * @param Address $addr
	 *
	 * @return Store
	 */
	public static function createWithParams($name, $description, Address $addr, array $userAccounts = array())
	{
		$store = new Store();
		$store = $store->setName($name)
			->setDescription($description)
			->setAddress($addr)
			->save();
		foreach($userAccounts as $userAccount) {
			if(!$userAccount instanceof UserAccount)
				continue;
			$store->giveAccess($userAccount);
		}
		return $store;
	}
}