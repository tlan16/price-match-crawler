<?php
/**
 * UserProfile Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class UserProfile extends InfoAbstract
{
	/**
     * The UserAccount
     *
     * @var UserAccount
     */
    protected $userAccount;
	/**
	 * getter UserAccount
	 *
	 * @return UserAccount
	 */
	public function getUserAccount()
	{
		$this->loadManyToOne("userAccount");
		return $this->userAccount;
	}
	/**
	 * Setter UserAccount
	 *
	 * @param UserAccount $userAccount The UserAccount that this UserProfileType belongs to
	 *
	 * @return UserProfileType
	 */
	public function setUserAccount(UserAccount $userAccount)
	{
		$this->userAccount = $userAccount;
		return $this;
	}
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'up');
        DaoMap::setManyToOne("userAccount", "UserAccount", "u");
        parent::__loadDaoMap();
        DaoMap::commit();
    }
    /**
     * get roles by user account
     * 
     * @param UserAccount $userAccount
     * 
     * @return array Role
     */
    public static function getRolesByUserAccount(UserAccount $userAccount, Store $store)
    {
    	$result = array();
    	$objs = self::getAllByCriteria('entityName = :eName and typeId = :typeId and userAccountId = :uid and value = :storeId', array('eName' => 'Role', 'typeId' => UserProfileType::ID_ROLE, 'uid' => $userAccount->getId(), 'storeId' => $store->getId()));
    	if(!is_array($objs) || count($objs) === 0)
    		return $result;
    	foreach ($objs as $obj)
    	{
    		$role = Role::get(intval($obj->getEntityId()));
    		if(!$role instanceof Role)
    			continue;
    		$result[] = $role;
    	}
    	return $result;
    }
    /**
     * clear all roles for a user account
     * 
     * @param UserAccount $userAccount
     */
    public static function clearRolesByUserAccount(UserAccount $userAccount, Store $store = null)
    {
    	self::removeRoleByUserAccount($userAccount, null, $store);
    }
    /**
     * add role to a user account
     * 
     * @param UserAccount $userAccount
     * @param Role $role
     * 
     * @return UserProfile
     */
    public static function addRoleByUserAccount(UserAccount $userAccount, Role $role, Store $store)
    {
    	return self::create($userAccount, UserProfileType::get(UserProfileType::ID_ROLE), $store->getId(), $role);
    }
    /**
     * remove a role for a user account
     * 
     * @param UserAccount $userAccount
     * @param Role $role
     */
    public static function removeRoleByUserAccount(UserAccount $userAccount, Role $role = null, Store $store = null)
    {
    	$where = 'userAccountId = ? and typeId = ?';
    	if($role instanceof Role) {
    		$where .= 'and entityId = ' . $role->getId() . ' and entityName = "' . get_class($role) . '"';
    	}
    	if($store instanceof Store)
    		$where .= ' AND value = ' . $store->getId();
    	self::deleteByCriteria($where, array($userAccount->getId(), UserProfileType::ID_ROLE));
    }
}
?>
