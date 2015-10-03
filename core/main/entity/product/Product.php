<?php
/** Product Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Product extends InfoEntityAbstract
{
	private $barcode;
	
	private $size;
	
	private $usedByVariance;
	
	private $unitPrice;
	
	private $labelVersionNo;
	
	/**
	 * Getter function for barcode 
	 */
	public function getBarcode()
	{
		return $this->barcode;
	}
	
	/**
	 * Setter function for barcode
	 * @param String $barcode
	 * @return Product
	 */
	public function setBarcode($barcode)
	{
		$this->barcode = $barcode;
		return $this;
	}
	
	/**
	 * Getter function for size
	 */
	public function getSize()
	{
		return $this->size;
	}
	
	/**
	 * Setter function for size
	 * @param int $size
	 * @return Product
	 */
	public function setSize($size)
	{
		$this->size = $size;
		return $this;
	}
	
	/**
	 * Getter function for used_by_variance
	 */
	public function getUsedByVariance()
	{
		return $this->usedByVariance;
	}
	
	/**
	 * Setter function for used_by_variance
	 * @param String $variance
	 * @return Product
	 */
	public function setUsedByVariance($variance)
	{
		$this->usedByVariance = $variance;
		return $this;
	}
	
	/**
	 * Getter function for Unit Price
	 */
	public function getUnitPrice()
	{
		return $this->unitPrice;
	}
	
	/**
	 * Setter function for Unit Price	?????
	 * @param Int $unitPrice
	 * @return Product
	 */
	public function setUnitPrice($unitPrice)
	{
		$this->unitPrice = $unitPrice;
		return $this;
	}
	
	/**
	 * Getter function for label version no
	 */
	public function getLabelVersionNo()
	{
		return $this->labelVersionNo;		
	}
	
	/**
	 * Setter function for labe version no
	 * @param Int $versionNo
	 * @return Product
	 */
	public function setLabelVersionNo($versionNo)
	{
		$this->labelVersionNo = $versionNo;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'pro');
		DaoMap::setStringType('barcode','varchar', 100);
		DaoMap::setIntType('size','int', 8);
		DaoMap::setStringType('usedByVariance','varchar', 100);
		DaoMap::setIntType('unitPrice', 'double', '10,4');
		DaoMap::setIntType('labelVersionNo','int', 3);
		
		/// load the dao map for InfoEntityAbstract /// 
		parent::__loadDaoMap();
		DaoMap::commit();
	}
	
}