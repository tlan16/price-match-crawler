<?php
/**
 * SystemSettings
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class SystemSettings extends BaseEntityAbstract
{
	const TYPE_LAST_PRODUCT_SYNC = 'last_succ_product_sync';
	const TYPE_MAGENTO_B2B_USERNAME = 'magento_b2b_username';
	const TYPE_MAGENTO_B2B_PASSWORD = 'magento_b2b_password';
	/**
	 * The value of the setting
	 * 
	 * @var string
	 */
	private $value;
	/**
	 * The type of the setting
	 * 
	 * @var string
	 */
	private $type;
	/**
	 * The description
	 * 
	 * @var string
	 */
	private $description;
	/**
	 * The cache
	 * 
	 * @var array
	 */
	private static $_cache = array();
	/**
	 * Getting Settings Object
	 * 
	 * @param string $type The type string
	 * 
	 * @return String
	 */
	public static function getSettings($type)
	{
		if(!isset(self::$_cache[$type]))
		{
			$settings = self::getAllByCriteria('type = ?', array($type), false, 1, 1);
			self::$_cache[$type] = trim(count($settings) === 0 ? '' : $settings[0]->getValue());
		}
		return self::$_cache[$type];
	}
	public function __toString()
	{
		return $this->value;
	}
	/**
	 * adding a new Settings Object
	 * 
	 * @param string $type The type string
	 */
	public static function addSettings($type, $value, $description = '')
	{
		$class = __CLASS__;
		$settings = self::getAllByCriteria('type=?', array($type), false, 1, 1);
		$setting = ((count($settings) === 0 ? new $class() : $settings[0]));
		$setting->setType($type)
			->setValue($value)
			->setDescription($description)
			->setActive(true)
			->save();
		self::$_cache[$type] = $value;
	}
	/**
	 * Removing Settings Object
	 * 
	 * @param string $type The type string
	 */
	public static function removeSettings($type)
	{
		self::updateByCriteria('set active = 0', 'type = ?', array($type));
		self::$_cache[$type] = null;
		array_filter(self::$_cache);
	}
	/**
	 * Getter for value
	 *
	 * @return int
	 */
	public function getValue() 
	{
	    return $this->value;
	}
	/**
	 * Setter for value
	 *
	 * @param sting $value The value
	 *
	 * @return SystemSettings
	 */
	public function setValue($value) 
	{
	    $this->value = $value;
	    return $this;
	}
	/**
	 * Getter for type
	 *
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}
	/**
	 * Setter for type
	 *
	 * @param sting $type The type
	 *
	 * @return SystemSettings
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}
	/**
	 * Getter for description
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
	 * @param string $value The description
	 *
	 * @return SystemSettings
	 */
	public function setDescription($value) 
	{
	    $this->description = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap($getThrough = false)
	{
		DaoMap::begin($this, 'syssettings');
	
		DaoMap::setStringType('type','varchar', 50);
		DaoMap::setStringType('value','varchar', 255);
		DaoMap::setStringType('description','varchar', 100);
	
		parent::__loadDaoMap();
	
		DaoMap::createUniqueIndex('type');
		DaoMap::commit();
	}
	/**
	 * get by type
	 * 
	 * @param string $type
	 * @param bool $activeOnly
	 * @return SystemSettings|null
	 */
	public static function getByType($type, $activeOnly = true)
	{
		$type = trim($type);
		$activeOnly = (intval($activeOnly) === 1);
		$objs = self::getAllByCriteria('type = ?', array($type), $activeOnly, 1, 1);
		return count($objs) > 0 ? $objs[0] : null;
	}
}