<?php
class InfoEntityAbstract extends BaseEntityAbstract
{
	/**
	 * The name
	 *
	 * @var string
	 */
	private $name;
	/**
	 * The description
	 *
	 * @var string
	 */
	private $description;
	/**
	 * The cache for info
	 *
	 * @var array
	 */
	protected $_cache;
	/**
	 * The array of information
	 *
	 * @var multiple:InfoAbstract
	 */
	protected $infos;
	
	/**
	 * getter for name
	 *
	 * @return string
	 */
	public function getName()
	{
	    return $this->name;
	}
	/**
	 * Setter for name
	 *
	 * @return InfoEntityAbstract
	 */
	public function setName($name)
	{
	    $this->name = $name;
	    return $this;
	}
	/**
	 * getter for description
	 *
	 * @return string
	 */
	public function getDescription()
	{
	    return $this->description;
	}
	/**
	 * Setter for description
	 *
	 * @return InfoEntityAbstract
	 */
	public function setDescription($description)
	{
	    $this->description = $description;
	    return $this;
	}
	/**
	 * Getting all the information
	 *
	 * @return array
	 */
	public function getInfos()
	{
		$this->loadOneToMany('infos');
	    return $this->infos;
	}
	/**
	 * Setter for the information
	 *
	 * @param array $value The array of InfoAbstract
	 *
	 * @return InfoEntityAbstract
	 */
	public function setInfos($value)
	{
	    $this->infos = $value;
	    return $this;
	}
	/**
	 * Getting the
	 * @param int $typeId
	 * @param string $reset
	 * @throws EntityException
	 */
	protected function getInfo($typeId, $entityName = null, $entityId = null, $value = null, $reset = false)
	{
		DaoMap::loadMap($this);
		$cacheKey = trim($typeId) . trim($entityName) . trim($entityId);
		if(!isset($this->_cache[$cacheKey]) || $reset === true)
		{
			if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
				throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

			$sql = 'select id from ' . strtolower($class) . ' `info` where `info`.active = 1 and `info`.' . strtolower(get_class($this)) . 'Id = ? and `info`.typeId = ?';
			$params =  array($this->getId(), $typeId);
			if($entityName === null || trim($entityName) !== '')
			{
				$sql .= $entityName === null ? ' and `info`.entityName is NULL' : ' and `info`.entityName = ?';
				if($entityName !== null)
					$params[] =  trim($entityName);
			}
			if($entityId === null || intval($entityId) !== 0)
			{
				$sql .= $entityId === null ? ' and `info`.entityId is NULL' : ' and `info`.entityId = ?';
				if($entityId !== null)
					$params[] = intval($entityId);
			}
			if($value === null || trim($value) !== '')
			{
				$sql .= $value === null ? ' and `info`.value is NULL' : ' and `info`.value = ?';
				if($value !== null)
					$params[] = trim($value);
			}
			$result = Dao::getResultsNative($sql, $params, PDO::FETCH_NUM);
			$this->_cache[$cacheKey] = array_map(create_function('$row', 'return ' . $class . '::get($row[0]);'), $result);
		}
		return $this->_cache[$cacheKey];
	}
	/**
	 * adding new value to this entity
	 *
	 * @param int  $typeId
	 * @param int  $value
	 * @param bool $overRideValue Whether we over write the value when we found one: clear all other value, and keep this new one
	 *
	 * @return InfoEntityAbstract
	 */
	public function addInfo($typeId, $entity = null, $value = "", $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

		$InfoTypeClass = $class . 'Type';
		$infoType = $InfoTypeClass::get($typeId);
		$typeId = ($typeId === null ? null : intval($typeId));
		$value = trim($value);
		$entityId = $entity instanceof BaseEntityAbstract ? $entity->getId() : 0;
		$entityName = $entity instanceof BaseEntityAbstract ? get_class($entity) : "";
		if($overRideValue === true)
		{
			//clear all info
			$this->removeInfo($typeId);
			//create a new
			$info = $class::create($this, $infoType, $value, $entity);
		}
		else
		{
			//check whether we have one already
			$infos = $this->getInfo($typeId, $entityName, $entityId, $value);
			$info = count($infos) > 0 ? $infos[0] : $class::create($this, $infoType, $value, $entity);		
			$info->setActive(true)->save();
		}

		//referesh cache
		$this->getInfo($typeId, $entityName, $entityId, true);
		return $this;
	}
	/**
	 * removing all information for that type
	 *
	 * @param int $typeId The type id
	 *
	 * @return InfoEntityAbstract
	 */
	public function removeInfo($typeId)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

		$class::updateByCriteria('active = 0', 'typeId = ? and ' . strtolower(get_class($this)) . 'Id = ?', array($typeId, $this->getId()));
		unset($this->_cache[$typeId]);
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
		if(!$this->isJsonLoaded($reset))
		{
			$array['createdBy'] = array('id'=> $this->getCreatedBy()->getId(), 'person' => $this->_getPersonJson($this->getCreatedBy()->getPerson()) );
			$array['updatedBy'] = array('id'=> $this->getUpdatedBy()->getId(), 'person' => $this->_getPersonJson($this->getUpdatedBy()->getPerson()) );
		}
		return parent::getJson($array, $reset);
	}
	private function _getPersonJson(Person $person)
	{
		return array('id'=> $person->getId(), 
					'firstName'=> $person->getFirstName(),
					'lastName'=> $person->getLastName(),
					'fullName'=> $person->getFullName(),
					'email'=> $person->getEmail()
					);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::setOneToMany("infos", get_class($this) . "Info", strtolower(get_class($this)) . "_info");
		DaoMap::setStringType('name', 'varchar', 100);
		DaoMap::setStringType('description','varchar', 255);
		
		parent::__loadDaoMap();
		
		DaoMap::createIndex('name');
	}
/**
	 * To create a new self
	 * 
	 * @param string $name
	 * @param string $description
	 * @param bool	 $active
	 * 
	 * @return InfoEntityAbstract
	 * @throws Exception
	 */
	public static function create($name, $description = '', $active = true) 
	{
		$class = get_called_class();
		if(($name = trim($name)) === '')
			throw new Exception('Name for a ' . $class . ' must not be empty');
		$description = trim($description);
		$active = (intval($active) === 1);
		$objs = $class::getAllByCriteria('name = ?', array($name), false, 1, 1);
		$obj = count($objs) > 0 ? $objs[0] : new $class();
		$obj->setName($name)
			->setDescription($description)
			->setActive($active)
			->save();
		return $obj;
	}
}