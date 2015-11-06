<?php
class Product extends InfoEntityAbstract
{
	/**
	 * __constructor
	 */
	public function __construct()
	{
		parent::__construct();
		unset($this->name);
		unset($this->getName);
		unset($this->setName);
	}
	/**
	 * the sku of product
	 * 
	 * @var string
	 */
	private $sku;
	/**
	 * getter for sku
	 *
	 * @return string
	 */
	public function getSku()
	{
	    return $this->sku;
	}
	/**
	 * Setter for sku
	 *
	 * @return Product
	 */
	public function setSku($sku)
	{
	    $this->sku = $sku;
	    return $this;
	}
    /**
     * (non-PHPdoc)
     * @see InfoEntityAbstract::getJson()
     */
    public function getJson($extra = array(), $reset = false)
    {
    	$array = $extra;
    	return parent::getJson($array, $reset);
    }
    /**
     * (non-PHPdoc)
     * @see InfoEntityAbstract::__toString()
     */
    public function __toString()
    {
    	return '[' . $this->getId() . ']' . $this->getSku() . '(' . $this->getDescription() . ')';
    }
    /**
     * (non-PHPdoc)
     * @see InfoEntityAbstract::preSave()
     */
    public function preSave()
    {
		if (intVal ( $this->getActive () ) === 1) 
		{
			$sku = trim ( $this->getSku () );
			$where = array ( 'sku = ?' );
			$params = array ( $sku );
			if (($id = trim ( $this->getId () )) !== '')
			{
				$where [] = 'id != ?';
				$params [] = $id;
			}
			$exsitingSKU = self::countByCriteria ( implode ( ' AND ', $where ), $params );
			if ($exsitingSKU > 0)
				throw new EntityException ( 'The SKU(=' . $sku . ') is already exists!' );
		}
	}
    /**
     * (non-PHPdoc)
     * @see InfoEntityAbstract::__loadDaoMap()
     */
    public function __loadDaoMap($getThrough = false)
    {
    	DaoMap::begin($this, 'pro');
    	
        DaoMap::setOneToMany("infos", get_class($this) . "Info", strtolower(get_class($this)) . "_info");
        DaoMap::setStringType('sku', 'varchar', 255);
        DaoMap::setStringType('description','varchar', 255);
        
        parent::__loadDaoMap(true);
        
        DaoMap::createUniqueIndex('sku');
        DaoMap::commit();
    }
    /**
     * To create a new Product
     * 
     * @param string $sku
     * @param string $description
     * @param bool	 $active
     * @return Product|NULL
     */
    public static function create($sku, $description = '', $active = true) {
    	$sku = trim($sku);
    	$description = trim($description);
    	$active = (intval($active) === 1);
    	
    	if(($obj = self::getBySku($sku)) instanceof self)
    		$obj = $obj;
    	else $obj = new self();
    	
    	$obj->setSku($sku)->setDescription($description)->setActive($active)->save();
    	
    	return $obj;
    }
    /**
     * get product by sku
     * 
     * @param string $sku
     * @param bool $activeOnly
     * 
     * @return Product|null
     */
    public static function getBySku($sku, $activeOnly = true)
    {
    	return count($objs = self::getAllByCriteria('sku = :sku', array('sku' => $sku), (intval($activeOnly) === 1), 1, 1)) > 0 ? $objs[0] : null;
    }
}