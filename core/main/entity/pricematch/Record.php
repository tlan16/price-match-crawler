<?php
/**
 * Entity for Record
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Record extends BaseEntityAbstract
{
	/**
	 * The Product for Record
	 * 
	 * @var Product
	 */
	protected $product;
	/**
	 * The vendor for Record
	 * 
	 * @var Vendor
	 */
	protected $vendor;
	/**
	 * the url
	 * 
	 * @var string
	 */
	private $url = '';
	/**
	 * The logo image base64 of the vendo
	 * 
	 * @var string
	 */
	private $logo = '';
	
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
	 * @return Record
	 */
	public function setProduct(Product $product)
	{
	    $this->product = $product;
	    return $this;
	}
	/**
	 * getter for vendor
	 *
	 * @return Vendor
	 */
	public function getVendor()
	{
		$this->loadManyToOne('vendor');
	    return $this->vendor;
	}
	/**
	 * Setter for vendor
	 *
	 * @return Record
	 */
	public function setVendor(Vendor $vendor)
	{
	    $this->vendor = $vendor;
	    return $this;
	}
	/**
	 * getter for url
	 *
	 * @return string
	 */
	public function getUrl()
	{
	    return $this->url;
	}
	/**
	 * Setter for url
	 *
	 * @return Record
	 */
	public function setUrl($url)
	{
	    $this->url = $url;
	    return $this;
	}
	/**
	 * getter for logo
	 *
	 * @return string
	 */
	public function getLogo()
	{
	    return $this->logo;
	}
	/**
	 * Setter for logo
	 *
	 * @return Record
	 */
	public function setLogo($logo)
	{
	    $this->logo = $logo;
	    return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap($getThrough = false)
	{
		DaoMap::begin($this, 'rcrd');
		DaoMap::setManyToOne('product', 'Product', 'rcrd_pro');
		DaoMap::setManyToOne('vendor', 'Vendor', 'rcrd_vndr');
		DaoMap::setStringType('url', 'varchar', 255);
		DaoMap::setStringType('logo', 'TEXT');
		parent::__loadDaoMap();
		
		DaoMap::commit();
	}
	public static function create(Product $product, Vendor $vendor, $url = '', $logo = '', $active = true)
	{
		$url = trim($url);
		$logo = trim($logo);
		
		$obj = new self();
		$obj->setProduct($product)->setVendor($vendor)->setUrl($url)->setLogo($logo)->setActive(intval($active) === 1)->save();
		return $obj;
	}
}