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
	 * Setter function for Unit Price
	 * @param Int $unitPrice
	 * @return Product
	 */
	public function setUnitPrice($unitPrice)
	{
		$unitPrice = StringUtilsAbstract::getValueFromCurrency($unitPrice);
		if(!is_numeric($unitPrice))
			throw new Exception('Unit Price must be numeric');

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
	 * Getting the categories
	 *
	 * @return multitype:
	 */
	public function getCategories()
	{
		$categoryIds = array();
		$piArray = ProductInfo::getAllByCriteria('productId = ? and typeId = ? and entityName = ?', array($this->getId(), ProductInfoType::ID_CATEGORY, 'Category'));
		foreach($piArray as $productInfo)
			$categoryIds[] = (trim($productInfo->getEntityId()) !== '' ? trim($productInfo->getEntityId()) : trim($productInfo->getValue()));
		if(count($categoryIds) === 0)
			return array();
		return Category::getAllByCriteria('id IN (' . implode(", ", array_fill(0, count($categoryIds), '?')) . ')', $categoryIds);
	}
	/**
	 * adding to a category
	 * @param Category $category
	 * @return Product
	 */
	public function addCategory(Category $category)
	{
		if(ProductInfo::countByCriteria('active = 1 and productId = ? and typeId = ? and entityName = ? and entityId = ? ', array($this->getId(), ProductInfoType::ID_CATEGORY, get_class($category), trim($category->getId()))) > 0)
			return $this;
		$this->addInfo(ProductInfoType::get(ProductInfoType::ID_CATEGORY), $category);
		return $this;
	}
	/**
	 * removing a category
	 * @return Product
	 */
	public function removeCategory(Category $category)
	{
		ProductInfo::updateByCriteria('active = ?', 'productId = ? and typeId = ? and entityName = ? and entityId = ?', array(0, $this->getId(), ProductInfoType::ID_CATEGORY, get_class($category), trim($category->getId())));
		return $this;
	}
	/**
	 * Remove all category(s) for a product
	 * THis actually removes all the ProductInfo(s) for a product and type ProductInfoType::ID_MATERIAL
	 *
	 * @return Product
	 */
	public function clearCategory()
	{
		ProductInfo::updateByCriteria('active = ?', 'productId = ? and typeId = ? and entityName = ?', array(0, $this->getId(), ProductInfoType::ID_CATEGORY, ProductInfoType::ENTITY_NAME_CATEGORY));
		return $this;
	}

	/**
	 * Getting all the Materials
	 * 
	 * @return array
	 */
	public function getMaterials()
	{
		$materialIdArray = array();
		$piArray = ProductInfo::getAllByCriteria('productId = ? and typeId = ? and entityName = ?', array($this->getId(), ProductInfoType::ID_MATERIAL, ProductInfoType::ENTITY_NAME_MATERIAL));
		foreach($piArray as $pi)
			$materialIdArray[] = (trim($pi->getEntityId()) !== '' ? trim($pi->getEntityId()) : trim($pi->getValue()));

		if(count($materialIdArray) === 0)
			return array();
		
		$materialIdArray = array_unique($materialIdArray);
		return Material::getAllByCriteria('id IN ('.implode(", ", array_fill(0, count($materialIdArray), '?')).')', $materialIdArray);
	}
	/**
	 * Adding material
	 *
	 * @param Material $material
	 *
	 * @return Product
	 */
	public function addMaterial(Material $material)
	{

		if(ProductInfo::countByCriteria('active = 1 and productId = ? and typeId = ? and entityName = ? AND entityId = ?', array($this->getId(), ProductInfoType::ID_MATERIAL, get_class($material), $material->getId())) > 0)
			return $this;
		$this->addInfo(ProductInfoType::get(ProductInfoType::ID_MATERIAL), $material);
		return $this;
	}
	/**
	 * removing a material
	 *
	 * @param Material $material
	 *
	 * @return Product
	 */
	public function removeMaterial(Material $material)
	{
		ProductInfo::updateByCriteria('active = ?', 'productId = ? and typeId = ? and entityName = ? and entityId = ?', array(0, $this->getId(), ProductInfoType::ID_MATERIAL, get_class($material), trim($material->getId())));
		return $this;
	}

	/**
	 * Remove all Material(s) for a product
	 * THis actually removes all the ProductInfo(s) for a product and type ProductInfoType::ID_MATERIAL
	 *
	 * @return Product
	 */
	public function clearMaterial()
	{
		ProductInfo::updateByCriteria('active = ?', 'productId = ? and typeId = ? and entityName = ?', array(0, $this->getId(), ProductInfoType::ID_MATERIAL, ProductInfoType::ENTITY_NAME_MATERIAL));
		return $this;
	}
	/**
	 * Getting all the stores
	 *
	 * @param Store  $store
	 *
	 * @return Product
	 */
	public function getStores()
	{
		$storeInfos = ProductInfo::getAllByCriteria('productId = ? and typeId = ? and entityName = ?', array($this->getId(), ProductInfoType::ID_STORE, ProductInfoType::ENTITY_NAME_STORE));
		$ids = array();
		foreach($storeInfos as $info)
			$ids[] = (trim($info->getEntityId()) !== '' ? trim($info->getEntityId()) : trim($pi->getValue()));
		
		if(count($ids) === 0)
			return array();
		$ids = array_unique($ids);
		return Store::getAllByCriteria('id IN ('.implode(", ", array_fill(0, count($ids), '?')).')', $ids);
	}
	/**
	 * Adding a store with unit price
	 *
	 * @param Store  $store
	 * @param number $unitprice
	 *
	 * @return Product
	 */
	public function addStore(Store $store, $unitprice = 0)
	{
		$this->addInfo(ProductInfoType::get(ProductInfoType::ID_STORE), $store, $unitprice, true);
		return $this;
	}
	/**
	 * Adding this product to all stores
	 * 
	 * @return Product
	 */
	public function addToAllStore()
	{
		$store = new Store();
		$store->setId(0);
		$this->addInfo(ProductInfoType::get(ProductInfoType::ID_STORE), $store, 0, true);
		return $this;
	}
	/**
	 * remove a storece
	 *
	 * @param Store  $store
	 *
	 * @return Product
	 */
	public function removeStore(Store $store)
	{
		ProductInfo::updateByCriteria('active = ?', 'productId = ? and typeId = ? and entityName = ? and entityId = ?', array(0, $this->getId(), ProductInfoType::ID_STORE, get_class($store), trim($store->getId())));
		return $this;
	}
	/**
	 * remove from all stores
	 *
	 * @return Product
	 */
	public function clearStore()
	{
		ProductInfo::updateByCriteria('active = ?', 'productId = ? and typeId = ? and entityName = ?', array(0, $this->getId(), ProductInfoType::ID_STORE, ProductInfoType::ENTITY_NAME_STORE));
		return $this;
	}
	public function isSellinAllStores()
	{
		return ProductInfo::countByCriteria('typeId = ? and entityName = ? and productId = ? and active = 1 and entityId = 0', array(ProductInfoType::ID_STORE, ProductInfoType::ENTITY_NAME_STORE, $this->getId())) > 0;
	}
	/**
	 * Print a label
	 *
	 * @param UDate       $printedDate
	 * @param UserAccount $printedBy
	 * @param Label       $newLabel
	 *
	 * @return Product
	 */
	public function printLabel(UDate $printedDate = null, UserAccount $printedBy = null, Label &$newLabel = null)
	{
		$newLabel = Label::create($this, $printedDate, $printedBy);
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see InfoEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
		$array['materials'] = array_map(create_function('$a', 'return $a->getJson();'), $this->getMaterials());
		$array['categories'] = array_map(create_function('$a', 'return $a->getJson();'), $this->getCategories());
		$array['sellInAllStores'] = $this->isSellinAllStores();
		if($array['sellInAllStores'] !== true) {
			$array['stores'] = array();
			foreach($this->getStores() as $store)
				$array['stores'][] = $store->getJson();
		}
		return parent::getJson($array, $reset);
	}

	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'pro');
		DaoMap::setStringType('barcode','varchar', 20);
		DaoMap::setIntType('size','int', 8);
		DaoMap::setStringType('usedByVariance','varchar', 20);
		DaoMap::setIntType('unitPrice', 'double', '10,4');
		DaoMap::setStringType('labelVersionNo','varchar', 10);

		/// load the dao map for InfoEntityAbstract ///
		parent::__loadDaoMap();

		DaoMap::createIndex('barcode');
		DaoMap::commit();
	}
	/**
	 * Creating a product
	 *
	 * @param string $name
	 * @param string $description
	 * @param string $barcode
	 * @param int    $size
	 * @param string $usedByVar
	 * @param double $unitPrice
	 * @param string $labelVersionNo
	 * @param array  $materials
	 * @param array  $categories
	 *
	 * @return Product
	 */
	public static function createWithParams($name, $description, $barcode, $size, $usedByVar, $unitPrice, $labelVersionNo, array $materials = array(), array $categories = array())
	{
		$product = new Product();
		$product->setBarcode($barcode)
			->setSize($size)
			->setUsedByVariance($usedByVar)
			->setUnitPrice($unitPrice)
			->setLabelVersionNo($labelVersionNo)
			->setName($name)
			->setDescription($description)
			->save();
		foreach($materials as $material)
			$product->addMaterial($material);
		foreach($categories as $category)
			$product->addCategory($category);
		return $product;
	}
}