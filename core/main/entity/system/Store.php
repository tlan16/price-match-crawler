<?php

class Store extends ResourceAbstract
{
	public function getAllStoreInfos()
	{
		return StoreInfo::getAllByCriteria('storeId = ?', array($this->getId()), true);
	}
	
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
		$array['info'] = array();
		
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
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'str');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
}