<?php
/** ProductInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductInfo extends InfoAbstract
{
	/**
	 * The Product of the ProductInfo
	 * @var Product
	 */
	protected $product;
	
	/**
	 * getter for product
	 *
	 * @return Ingredient
	 */
	public function getProduct()
	{
		$this->loadManyToOne('product');
	    return $this->product;
	}
	/**
	 * Setter for Product
	 *
	 * @return ProductInfo
	 */
	public function setProduct(Product $product)
	{
	    $this->product = $product;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'pro_info');
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}