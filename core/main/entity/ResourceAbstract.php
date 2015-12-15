<?php
/**
 * ResourceAbstract Abstract Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ResourceAbstract extends BaseEntityAbstract
{
    /**
     * The name 
     * @var string
     */
    private $name;
    /**
     * The description 
     * @var string
     */
    private $description = "";
    
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
     * @return Unit
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
     * @return ResourceAbstract
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__toString()
     */
    public function __toString()
    {
    	return  get_class($this) . '[' . $this->getId() . '] ' . $this->getName();
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap($getThrough = false)
    {
        DaoMap::setStringType('name', 'varchar', 100);
        DaoMap::setStringType('description', 'varchar', 255);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('name');
    }
    /**
     * create a new self
     *
     * @param string $name
     * @param string $description
     * @param bool	 $active
     * 
     * @throws Exception
     * @return ResourceAbstract
     */
    public static function createBasic($name, $description = '', $active = true)
    {
    	$class = get_called_class();
    	if(($name = trim($name)) === '')
    		throw new Exception('Name cannot be empty to create a new ' . __CLASS__);
    	$description = trim($description);
    	$active = (intval($active) === 1);
    	
    	if(($obj = $class::getByName($name,false)) instanceof $class)
    		$obj = $obj;
    	else $obj = new $class();
    	
    	$obj->setName($name)
    		->setDescription($description)
	    	->setActive($active)
	    	->save();
    	return $obj;
    }
    /**
     * get a self by name
     * 
     * @param string $name
     * @param bool	 $activeOnly
     */
    public static function getByName($name, $activeOnly = true)
    {
    	$name = trim($name);
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('name = ?', array($name), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
}
?>