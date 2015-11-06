<?php
/**
 * UserAccountInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class UserAccountInfo extends InfoAbstract
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
	 * @param UserAccount $userAccount The UserAccount that this UserAccountInfoType belongs to
	 *
	 * @return UserAccountInfoType
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
    public function __loadDaoMap($getThrough = false)
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
    public static function getRolesByUserAccount(UserAccount $userAccount, Store $store = null)
    {
    	$objs = self::getAllByCriteria('entityName = :eName and typeId = :typeId and userAccountId = :uid' . ($store instanceof Store ? ' and value='. $store->getId() : ''), array('eName' => 'Role', 'typeId' => UserAccountInfoType::ID_ROLE, 'uid' => $userAccount->getId()));
    	$roleIds = array();
    	foreach($objs as $obj)
    		$roleIds[] = $obj->getEntityId();
    	if(count($roleIds) === 0)
    		return array();
    	return Role::getAllByCriteria('id in (' . implode(', ', $roleIds) . ')');
    }
    /**
     * get store by user account
     *
     * @param UserAccount $userAccount
     *
     * @return array Role
     */
    public static function getStoresByUserAccount(UserAccount $userAccount, Role $role = null)
    {
    	$objs = self::getAllByCriteria('entityName = :eName and typeId = :typeId and userAccountId = :uid' . ($role instanceof Role ? ' and entityId='. $role->getId() : ''), array('eName' => 'Role', 'typeId' => UserAccountInfoType::ID_ROLE, 'uid' => $userAccount->getId()));
    	$storeIds = array();
    	foreach($objs as $obj)
    		$storeIds[] = $obj->getValue();

    	if(count($storeIds) === 0)
    		return array();
    	return Store::getAllByCriteria('id in (' . implode(', ', $storeIds) . ')');
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
     * @return UserAccountInfo
     */
    public static function addRoleByUserAccount(UserAccount $userAccount, Role $role, Store $store)
    {
    	return self::create($userAccount, UserAccountInfoType::get(UserAccountInfoType::ID_ROLE), $store->getId(), $role);
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
    	self::deleteByCriteria($where, array($userAccount->getId(), UserAccountInfoType::ID_ROLE));
    }
}
?>
