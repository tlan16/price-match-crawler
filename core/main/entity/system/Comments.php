<?php
/** Comments Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Comments extends BaseEntityAbstract
{
	/**
	 * The id of the entity
	 *
	 * @var int
	 */
	private $entityId;
	/**
	 * The name of the entity
	 *
	 * @var string
	 */
	private $entityName;
	/**
	 * Getter for EntityId
	 *
	 * @return int
	 */
	public function getEntityId()
	{
		return $this->entityId;
	}
	/**
	 * Setter for entity
	 *
	 * @param int $value The entity id
	 *
	 * @return Comments
	 */
	public function setEntityId($value)
	{
		$this->entityId = $value;
		return $this;
	}
	/**
	 * Getter for entityName
	 *
	 * @return string
	 */
	public function getEntityName()
	{
		return $this->entityName;
	}
	/**
	 * Setter for entityName
	 *
	 * @param string $value The entityName
	 *
	 * @return Comments
	 */
	public function setEntityName($value)
	{
		$this->entityName = $value;
		return $this;
	}
	/**
	 * add Comments
	 *
	 * @param BaseEntityAbstract $entity   The entity
	 * @param string             $title    The title
	 * @param string             $content  The comemnts
	 * @param UserAccount        $author   The author of the comments
	 * @param string             $groupId  The groupId
	 */
	public static function addComments(BaseEntityAbstract $entity, $title, $content, UserAccount $author = null)
	{
		$className = __CLASS__;
		$en = new $className();
		return $en->setEntityId($entity->getId())
			->setEntityName(get_class($entity))
			->setTitle($title)
			->setContent($content)
			->setAuthor($author)
			->save();
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
// 		$array = $extra;
// 	    if(!$this->isJsonLoaded($reset))
// 	    {
// 	    }
	    return parent::getJson($array, $reset);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'comm');
				
		parent::__loadDaoMap();
		
		DaoMap::setIntType('entityId');
		DaoMap::setStringType('entityName','varchar', 100);

		DaoMap::commit();
	}
}