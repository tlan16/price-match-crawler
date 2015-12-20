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
	 * The price
	 * 
	 * @var double
	 */
	private $price;
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
	 * getter for price
	 *
	 * @return double
	 */
	public function getPrice()
	{
	    return $this->price;
	}
	/**
	 * Setter for price
	 *
	 * @return Record
	 */
	public function setPrice($price)
	{
	    $this->price = $price;
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
		DaoMap::setIntType('price', 'float');
		parent::__loadDaoMap();
		
		DaoMap::commit();
	}

	/**
	 * @param Product $product
	 * @param Vendor $vendor
	 * @param string $url
	 * @param double $price
	 * @param bool $active
	 * @return Record
	 */
	public static function create(Product $product, Vendor $vendor, $url = '', $price, $active = true)
	{
		$url = trim($url);

		$obj = new self();
		$obj->setProduct($product)->setVendor($vendor)->setUrl($url)->setPrice($price)->setActive(intval($active) === 1);
		$obj->save();
		return $obj;
	}

	/**
	 * @param array $extra
	 * @param bool $reset
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
		$array['product'] = $this->getProduct()->getJson();
		$array['vendor'] = $this->getVendor()->getJson();
		return parent::getJson($array, $reset);
	}
}