<?php
/**
 * Entity for Vendor
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Vendor extends ResourceAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap($getThrough = false)
	{
		DaoMap::begin($this, 'vndr');
		parent::__loadDaoMap();
		
		DaoMap::commit();
	}
	/**
	 * create new record
	 * 
	 * @param string $name
	 * @param string $description
	 * @param bool $active
	 * @return Vendor
	 */
	public static function create($name, $description = '', $active = true)
	{
		return parent::createBasic($name, $description, $active);
	}
}