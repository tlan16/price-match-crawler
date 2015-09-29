<?php
class InfoTypeAbstract extends BaseEntityAbstract
{
	/**
	 * The cache of the object
	 * 
	 * @var array
	 */
	protected static $_cache;
	/**
	 * The name of the type
	 * 
	 * @var string
	 */
	private $name;
	/**
	 * The description
	 *
	 * @var string
	 */
	private $description = '';
	
	/**
	 * Getter for the name
	 * 
	 * @return string
	 */
	public function getName() 
	{
	    return $this->name;
	}
	/**
	 * Setter for the name
	 * 
	 * @param string $value The name of the type
	 * 
	 * @return InfoTypeAbstract
	 */
	public function setName($value) 
	{
	    $this->name = $value;
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
	 * @return InfoTypeAbstract
	 */
	public function setDescription($description)
	{
	    $this->description = $description;
	    return $this;
	}
	/**
	 * Getting object
	 * 
	 * @param int $typeId The id of the type
	 * 
	 * @return InfoTypeAbstract
	 */
	public static function get($typeId)
	{
		if(!isset(self::$_cache[$typeId]))
		{
			self::$_cache[$typeId] = parent::get($typeId);
		}
		return self::$_cache[$typeId];
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::setStringType('name','varchar', 100);
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
	 * @return InfoTypeAbstract
	 * @throws Exception
	 */
	public static function create($name, $description = '', $active = true) 
	{
		$class = get_called_class();
		if(($name = trim($name)) === '')
			throw new Exception('Name for a ' . $class . ' must not be empty');
		$description = trim($description);
		$active = (intval($active) === 1);
		$objs = $class::getAllByCriteria('name = ?', array($name), $active, 1, 1);
		$obj = count($objs) > 0 ? $objs[0] : new $class();
		$obj->setName($name)
			->setActive($active)
			->save();
		return $obj;
	}
}