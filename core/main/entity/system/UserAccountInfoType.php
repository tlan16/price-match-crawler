<?php
/**
 * UserAccountInfoType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class UserAccountInfoType extends InfoTypeAbstract
{
	const ID_ROLE = 1;
    /**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'user_info_type');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
    /**
     * create a new UserAccountInfoType
     *
     * @param string $name
     * @param bool	 $active
     *
     * @throws Exception
     * @return UserAccountInfoType
     */
    public static function create($name, $description = '')
    {
    	if(($name = trim($name)) === '')
    		throw new Exception('Name cannot be empty to create a new ' . __CLASS__);
    	$active = (intval($active) === 1);
    	$obj = self::getByName($name);
    	$obj = $obj instanceof self ? $obj : new self();
    	$obj->setName($name)
	    	->save();
    	return $obj;
    }
    /**
     * get UserAccountInfoType by name
     *
     * @param string $name
     * @param bool	 $activeOnly
     *
     * @return UserAccountInfoType|null
     */
    public static function getByName($name, $activeOnly = true)
    {
    	$name = trim($name);
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('name like ?', array($name), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
}
?>
