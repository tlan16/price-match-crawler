<?php
/**
 * Common entity class
 *
 * @package Core
 * @subpackage Entity
 */
abstract class BaseEntityAbstract
{
	/**
	 * The registry of json array
	 *
	 * @var array
	 */
	protected $_jsonArray = array();
    /**
     * Internal id used by all application entities
     *
     * @var int
     */
    protected $id = null;
    /**
     * @var bool
     */
    protected $active;
    /**
     * @var UDate
     */
    protected $created;
    /**
     * @var UserAccount
     */
    protected $createdBy;
    /**
     * @var UDate
     */
    protected $updated;
    /**
     * @var UserAccount
     */
    protected $updatedBy;
    /**
     * Is this a proxy object?
     *
     * @var bool
     */
    protected $proxyMode = false;
    /**
     * The entity level runtime cache
     *
     * @var array
     */
    private static $_entityCache = array();
    /**
     * __constructor
     */
    public function __construct()
    {

    }
    /**
     * Set the primary key for this entity
     *
     * @param int $id
     *
     * @return BaseEntityAbstract
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * Get the primary key for this entity
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set when this entity was created
     *
     * @param string $created The UDate time string
     *
     * @return BaseEntityAbstract
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }
    /**
     * When was this entity created
     *
     * @return UDate
     */
    public function getCreated()
    {
        if (is_string($this->created))
        $this->created = new UDate($this->created);
        return $this->created;
    }
    /**
     * Set who created this entity
     *
     * @param UserAccount $user The new CreatedBy useraccount
     *
     * @return BaseEntityAbstract
     */
    public function setCreatedBy(UserAccount $user)
    {
        $this->createdBy = $user;
        return $this;
    }
    /**
     * Who created this entity
     *
     * @return UserAccount
     */
    public function getCreatedBy()
    {
        $this->loadManyToOne('createdBy');
        return $this->createdBy;
    }
    /**
     * Set when this entity was last updated
     *
     * @param string $updated The UDate time string
     *
     * @return BaseEntityAbstract
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }
    /**
     * When was this entity last updated
     *
     * @return UDate
     */
    public function getUpdated()
    {
        if (is_string($this->updated))
        $this->updated = new UDate($this->updated);
        return $this->updated;
    }
    /**
     * Set who last updated this entity
     *
     * @param UserAccount $user The UpdatedBy useraccount
     *
     * @return BaseEntityAbstract
     */
    public function setUpdatedBy(UserAccount $user)
    {
        $this->updatedBy = $user;
        return $this;
    }
    /**
     * Who last updated this entity
     *
     * @return UserAccount
     */
    public function getUpdatedBy()
    {
        $this->loadManyToOne('updatedBy');
        return $this->updatedBy;
    }
    /**
     * @return bool
     */
    public function isActive()
    {
        return trim($this->active) === '1';
    }
    /**
     * Setter for whether the entity is active
     *
     * @param bool $active whether the entity is active
     *
     * @return BaseEntityAbstract
     */
    public function setActive($active)
    {
        $this->active = intval($active);
        return $this;
    }
    /**
     * Getter for whether the entity is active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }
    /**
     * Dictates if the entity is a proxy object or not for lazy loading purposes
     *
     * @param bool $bool Whether we are in proxy mode
     *
     * @return BaseEntityAbstract
     */
    public function setProxyMode($bool)
    {
        $this->proxyMode = (bool)$bool;
        return $this;
    }
    /**
     * Check if an entity is a proxy object
     *
     * @return bool
     */
    public function getProxyMode()
    {
        return $this->proxyMode;
    }
    /**
     * Lazy load a one-to-many relationship
     *
     * @param string $property The property that we are trying to load
     *
     * @return BaseEntityAbstract
     */
    protected function loadOneToMany($property)
    {
        // Figure out what the object type is on the many side
        $this->__loadDaoMap();
        $thisClass = get_class($this);
        $cls = DaoMap::$map[strtolower($thisClass)][$property]['class'];

        DaoMap::loadMap($cls);
        $alias = DaoMap::$map[strtolower($cls)]['_']['alias'];
        $field = StringUtilsAbstract::lcFirst($thisClass);
        $this->$property = Dao::findByCriteria(new DaoQuery($cls), sprintf('%s.`%sId`=?', $alias, $field), array($this->getId()));

        return $this;
    }
    /**
     * Lazy load a one-to-one relationship
     *
     * @param string $property
     */
    protected function loadOneToOne($property)
    {
        return $this->loadManyToOne($property);
    }
    /**
     * Lazy load a many-to-one relationship
     *
     * @param string $property The property that we are trying to load
     *
     * @return BaseEntityAbstract
     */
    protected function loadManyToOne($property)
    {
        $this->__loadDaoMap();
        if (is_null($this->$property))
        {
            //if the proerty is allow to have null value, then let it be
            if (DaoMap::$map[strtolower(get_class($this))][$property]['nullable'])
            {
                $this->$property = null;
                return $this;
            }
            //if the property is one of these, as when we are trying to save them, we don't have the iniated value
            if (in_array($property, array('createdBy', 'updatedBy')))
            $this->$property = Core::getUser();
            else
            throw new Exception('Property (' . get_class($this) . '::' . $property . ') must be initialised to integer or proxy prior to lazy loading.', 1);
        }

        // Load the DAO map for this entity
        $cls = DaoMap::$map[strtolower(get_class($this))][$property]['class'];
        if (!$this->$property instanceof BaseEntityAbstract)
        throw new DaoException('The property(' . $property . ') for "' . get_class($this) . '" is NOT a BaseEntity!');
        $qry = new DaoQuery($cls);
        $qry->setSelectActiveOnly(false);
        $this->$property = Dao::findById($qry, $this->$property->getId());
        return $this;
    }
    /**
     * Lazy load a many-to-many relationship
     *
     * @param string $property The property that we are trying to load
     *
     * @return BaseEntityAbstract
     */
    protected function loadManyToMany($property)
    {
        // Grab the DaoMap data for both ends of the join
        $this->__loadDaoMap();
        $cls = DaoMap::$map[strtolower(get_class($this))][$property]['class'];
        $obj = new $cls;
        $obj->__loadDaoMap();

        $thisClass = get_class($this);
        $qry = new DaoQuery($cls);
        $qry->eagerLoad($cls . '.' . strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1) . 's');

        // Load this end with an array of entities typed to the other end
        DaoMap::loadMap($cls);
        $alias = DaoMap::$map[strtolower($cls)]['_']['alias'];
        $field = strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1);
        $this->$property = Dao::findByCriteria($qry, sprintf('`%sId`=?', $field), array($this->getId()));
        return $this;
    }
    /**
     * This behaviour is blocked
     *
     * @param string $var The property of the entity
     *
     * @throws Exception
     */
    public function __get($var)
    {
        $class = get_class($this);
        throw new EntityException("Attempted to get variable $class::$var directly and it is either inaccessable or doesnt exist");
    }
    /**
     * This behaviour is blocked
     *
     * @param string $var The property of the entity
     *
     * @throws Exception
     */
    public function __set($var, $value)
    {
        $class = get_class($this);
        throw new EntityException("Attempted to set variable $class::$var directly and it is either inaccessable or doesnt exist");
    }
    /**
     * getting the Json array from all the private memebers of the entity
     *
     * @param bool $reset Forcing the function to fetch data from the database again
     *
     * @return array The associative arary for json
     */
    public function getJson($extra = array(), $reset = false)
    {
    	if(!$this->isJsonLoaded($reset))
    	{
    		$array = array('id' => trim($this->getId()));
	        DaoMap::loadMap(get_class($this));
	        foreach(DaoMap::$map[strtolower(get_class($this))] as $field => $fieldMap)
	        {
	            if($field === '_' || isset($fieldMap['rel']))
	                continue;
	            $getterMethod = 'get' . ucfirst($field);
	            if(!method_exists($this, $getterMethod))
	            	continue;
	            $value = $this->$getterMethod();
	            switch(trim($fieldMap['type']))
	            {
	            	case 'bool':
	            		{
	            			$array[$field] = (trim($value) === '1' ? true : false);
	            			break;
	            		}
	            	default:
	            		{
	            			$array[$field] = trim($value);
	            			break;
	            		}
	            }
	        }
	        $this->_jsonArray = array_merge($array, $extra);
    	}
        return $this->_jsonArray;
    }
    /**
     * Whether the $this->_jsonArray is loaded
     *
     * @return bool
     */
    protected function isJsonLoaded($reset = false)
    {
    	if($reset === true)
    		$this->_jsonArray = array();
    	return (is_array($this->_jsonArray) && count($this->_jsonArray) > 0 );
    }
    /**
     * Adding a log to this entity
     *
     * @param string $msg
     * @param string $type
     * @param string $comments
     * @param string $funcName
     *
     * @return BaseEntityAbstract
     */
    public function addLog($msg, $type, $comments = '', $funcName = '')
    {
    	Log::LogEntity($this, $msg, $type, $comments, $funcName);
    	return $this;
    }
    /**
     * Default toString implementation
     *
     * @return string
     */
    public function __toString()
    {
        return get_called_class() . ' [' . $this->getId() . ']';
    }
    /**
     * load the default elments of the base entity
     */
    protected function __loadDaoMap($getThrough = false)
    {
        DaoMap::setBoolType('active', 'bool', 1);
        DaoMap::setDateType('created');
        DaoMap::setManyToOne('createdBy', 'UserAccount');
        DaoMap::setDateType('updated', 'timestamp', false, 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        DaoMap::setManyToOne('updatedBy', 'UserAccount');
    }
    /**
     * validates all rules before save in EntityDao!!!
     *
     * @todo need to be implemented!!!!!
     *
     * @return boolean
     */
    public function validateAll()
    {
        $errorMsgs = array();
        return $errorMsgs;
    }
    /**
     * function before save the entity
     */
    public function preSave() {}
    /**
     * function after save the entity
     */
    public function postSave() {}
    /**
     * Saving the current entity
     *
     * @return BaseEntityAbstract
     */
    public function save()
    {
    	return FactoryAbastract::dao(get_class($this))->save($this);
    }
    /**
     * Find all entities
     *
     * @param bool  	$activeOnly
     * @param int		$pageNo
     * @param int 		$pageSize
     * @param array 	$orderBy
     * @param array   	$stats
     *
     * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
     */
    public static function getAll($activeOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
    {
    	$result = FactoryAbastract::dao(get_called_class())->findAll($activeOnly, $pageNo, $pageSize, $orderBy);
    	$stats = FactoryAbastract::dao(get_called_class())->getPageStats();
    	return $result;
    }
    /**
     * Find all entities with criterias
     *
     * @param string	$criteria
     * @param array		$params
     * @param bool		$activeOnly
     * @param int		$pageNo
     * @param int		$pageSize
     * @param array		$orderBy
     * @param array   	$stats
     *
     * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
     */
    public static function getAllByCriteria($criteria, $params = array(), $activeOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
    {
    	$result = FactoryAbastract::dao(get_called_class())->findByCriteria($criteria, $params, (intval($activeOnly) === 1), $pageNo, $pageSize, $orderBy);
    	$stats = FactoryAbastract::dao(get_called_class())->getPageStats();
    	return $result;
    }
    /**
     * Getting entity with an id
     *
     * @param int $id The id of the entity
     *
     * @return Ambigous <BaseEntityAbstract, NULL, SimpleXMLElement>
     */
    public static function get($id)
    {
    	return FactoryAbastract::dao(get_called_class())->findById($id);
    }
    /**
     * Updating a table for the search criteria
     *
     * @param string $setClause The set clause
     * @param string $criteria  The where clause
     * @param array  $params    The parameters
     *
     * @return int
     */
    public static function updateByCriteria($setClause, $criteria, $params = array())
    {
    	return FactoryAbastract::dao(get_called_class())->updateByCriteria($setClause, $criteria, $params);
    }
    /**
     * delete a table for the search criteria
     *
     * @param string   $criteria The where clause
     * @param array    $params   The parameters
     *
     * @return int
     */
    public static function deleteByCriteria($criteria, $params = array())
    {
    	return FactoryAbastract::dao(get_called_class())->deleteByCriteria($criteria, $params);
    }
    /**
     * Count By Criteria
     *
     * @param string   $criteria The where clause
     * @param array    $params   The parameters
     *
     * @return number
     */
    public static function countByCriteria($criteria, $params = array())
    {
    	return FactoryAbastract::dao(get_called_class())->countByCriteria($criteria, $params);
    }
    /**
     * Getting the DaoQuery
     *
     * @return DaoQuery
     */
    public static function getQuery()
    {
    	return FactoryAbastract::dao(get_called_class())->getQuery();
    }
    /**
     * Add a join table record for many to many relationship
     *
     * @param BaseEntityAbstract $leftEntity  The left entity
     * @param BaseEntityAbstract $rightEntity The right entity
     *
     * @return int
     */
    public static function saveManyToManyJoin(BaseEntityAbstract &$leftEntity, BaseEntityAbstract $rightEntity)
    {
    	FactoryAbastract::dao(get_called_class())->saveManyToManyJoin($leftEntity, $rightEntity);
    	return $leftEntity;
    }
    /**
     * Remove a join table record for many to many relationship
     *
     * @param BaseEntityAbstract $leftEntity  The left entity
     * @param BaseEntityAbstract $rightEntity The right entity
     *
     * @return int
     */
    public function deleteManyToManyJoin(BaseEntityAbstract &$leftEntity, BaseEntityAbstract $rightEntity)
    {
    	FactoryAbastract::dao(get_called_class())->deleteManyToManyJoin($leftEntity, $rightEntity);
    	return $leftEntity;
    }
    /**
     * Getting the runtime cache
     *
     * @param string $key The key of the cache
     *
     * @param mixed
     */
    protected static function getCache($key)
    {
    	if(!self::cacheExsits($key))
    		return null;
    	$class = get_called_class();
    	return BaseEntityAbstract::$_entityCache[$class][$key];
    }
    /**
     * adding the runtime cache
     *
     * @param string $key  The key of the cache
     * @param mixed  $data The data of the cache
     *
     * @param mixed
     */
    protected static function addCache($key, $data)
    {
    	$class = get_called_class();
    	BaseEntityAbstract::$_entityCache[$class][$key] = $data;
    	return BaseEntityAbstract::$_entityCache[$class][$key];
    }
    /**
     * Check whether the key exsits in the runtime cache
     *
     * @param string $key The key of the cache
     *
     * @return boolean
     */
    protected static function cacheExsits($key)
    {
    	$class = get_called_class();
    	return isset(BaseEntityAbstract::$_entityCache[$class]) && isset(BaseEntityAbstract::$_entityCache[$class][$key]);
    }
    /**
     * remove the cache from runtime cache
     *
     * @param string $key The key of the cache
     *
     * @return boolean
     */
    protected static function removeCache($key)
    {
    	if(!self::cacheExsits($key))
    		return false;
    	$class = get_called_class();
    	unset(BaseEntityAbstract::$_entityCache[$class][$key]);
    	return true;
    }
    public static function getDailyCreatedCounts(UDate $from, UDate $to, $interval = "5 minutes")
    {
    	$result = array();
    	$date = UDate::zeroDate();
    	$maxDate = UDate::maxDate();
    	$maxDate->setDate(1, 1, 1);
    	$class = get_called_class();
    	while ($date->beforeOrEqualTo($maxDate) === true)
    	{
    		$loop_from_time = $date->format('H:i:s');
    		$date->modify('+'.$interval);
    		$loop_to_time = $date->format('H:i:s');
			$where = 'active = 1 and created >= ? and created < ? and TIME(created) >= ? and TIME(created) < ?';
			$param = array(trim($from), trim($to), $loop_from_time, $loop_to_time);
    		$count = $class::countByCriteria($where, $param);
    		if(!(trim($loop_to_time) === '00:00:00' && intval($count) === 0))
    			$result[] = array(intval(trim($date->getUnixTimeStamp())."000"), intval($count));
    	}
    	return $result;
    }
    public static function getCreatedCounts(UDate $from, UDate $to, $interval = "1 day") 
    {
    	$result = array();
    	$date = $from;
    	$class = get_called_class();
    	while ($date->beforeOrEqualTo($to) === true)
    	{
    		$loop_from_date = new UDate($date->__toString());
    		$loop_to_date = $date->modify('+1 day');
	    	$count = $class::countByCriteria('active = 1 and created >= ? and created < ?', array($loop_from_date, $loop_to_date));
	    	$result[] = array(intval(trim($loop_to_date->getUnixTimeStamp())."000"), intval($count));
    	}
    	return $result;
    }
}