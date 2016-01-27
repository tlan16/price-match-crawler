<?php
class magentoB2BProductConnector extends magentoB2BconnectorAbstract
{
	public $prefix = 'Product/getAllSku';
	/**
	 * get product by id
	 * 
	 * @param int $id
	 * @param bool $debug
	 * 
	 * @return array
	 */
	public static function getById($id, $debug = false)
	{
		return self::getAllByCriteria('id = :id', array('id' => $id));
	}
	public function callback($data) {
		if(isset($data['sku']) && ($sku = $data['sku']) !== '')
		{
			$product = Product::create($sku, isset($data['shortDescription']) ? $data['shortDescription'] : '');
			SystemSettings::getByType('last_succ_product_sync')->setValue($data['updated'])->save();
			if($this->debug)
				self::log('DONE Product[' . $product->getId() . '] ' . $product->getSku() . ' updated : ' . $data['updated']);
		}
	}
	public static function importProducts($baseurl, $username, $password, $debug = false) 
	{
		$connector = new magentoB2BProductConnector($baseurl, $username, $password, $debug = true);
		$connector->getAllByCriteria($connector->baseurl . $connector->prefix, 'updated > "' . SystemSettings::getByType('last_succ_product_sync')->getValue(). '"') ;
	}
}