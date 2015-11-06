<?php
class ProductInfo extends InfoAbstract
{
	protected $product;
	/**
	 * getter for product
	 *
	 * @return Product
	 */
	public function getProduct()
	{
		$this->loadManyToOne('product');
	    return $this->product;
	}
	/**
	 * Setter for product
	 *
	 * @return ProductInfo
	 */
	public function setProduct($product)
	{
	    $this->product = $product;
	    return $this;
	}
	/**
     * (non-PHPdoc)
     * @see ResourceAbstract::__toString()
     */
    public function __toString()
    {
    	return parent::__toString() . '(' . $this->getDescription() . ')';
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
     * @see BaseEntityAbstract::preSave()
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
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap($getThrough = false)
    {
    	DaoMap::begin($this, 'pro');
        DaoMap::setStringType('sku', 'varchar', 255);
        
        parent::__loadDaoMap();
        
        DaoMap::createUniqueIndex('sku');
        DaoMap::commit();
    }
}