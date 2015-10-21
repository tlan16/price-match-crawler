<?php
/**
 * This is the Nutrition details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class DetailsController extends DetailsPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'product.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Product';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		if(!AccessControl::canAccessAllergentDetailPage(Core::getRole()))
			die('You do NOT have access to this page');
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= "pageJs.setPreData(" . json_encode(array()) . ");";
		$js .= "pageJs._containerIds=" . json_encode(array(
				'name' => 'name_div'
				,'description' => 'description_div'
				,'barcode' => 'barcode_div'
				,'unitPrice' => 'unitPrice_div'
				,'size' => 'size_div'
				,'useByVariance' => 'useByVariance_div'
				,'labelVersion' => 'labelVersion_div'
				,'categories' => 'categories_div'
				,'materials' => 'materials_div'
				,'stores' => 'stores_div'
				,'saveBtn' => 'save_btn'
		)) . ";";
		$js .= "pageJs.load();";
		$js .= "pageJs.bindAllEventNObjects();";
		if(!AccessControl::canEditAllergentDetailPage(Core::getRole()))
			$js .= "pageJs.readOnlyMode();";
		return $js;
	}
	/**
	 * save the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function saveItem($sender, $params)
	{
		$results = $errors = array();
		try
		{
			Dao::beginTransaction();
			$entity = null;
			$focusEntity = $this->getFocusEntity();
			if (isset ( $params->CallbackParameter->id ) && !($entity = $focusEntity::get(intval($params->CallbackParameter->id))) instanceof $focusEntity )
				throw new Exception ( 'System Error: invalid id passed in.' );

			if (!isset ( $params->CallbackParameter->name ) || ($name = trim ( $params->CallbackParameter->name )) === '')
				throw new Exception ( 'System Error: invalid name passed in.' );
			$description = isset ( $params->CallbackParameter->description ) ? trim($params->CallbackParameter->description) : '';
			$size = isset ( $params->CallbackParameter->size ) ? trim($params->CallbackParameter->size) : '';
			$barcode = isset ( $params->CallbackParameter->barcode ) ? trim($params->CallbackParameter->barcode) : '';
			$labelVersionNo = isset ( $params->CallbackParameter->labelVersionNo ) ? trim($params->CallbackParameter->labelVersionNo) : '';
			$useByVariance = isset ( $params->CallbackParameter->useByVariance ) ? intval($params->CallbackParameter->useByVariance) : '';
			$unitPrice = StringUtilsAbstract::getValueFromCurrency(isset ( $params->CallbackParameter->unitPrice ) ? trim(isset ( $params->CallbackParameter->unitPrice)) : 0);
			$allStores = (isset ( $params->CallbackParameter->allStores ) && intval($params->CallbackParameter->allStores) === 1);
			$materials = $this->_idsToObjs($params->CallbackParameter, 'materials', 'Material');
			$categories = $this->_idsToObjs($params->CallbackParameter, 'categories', 'Category');
			$stores = $this->_idsToObjs($params->CallbackParameter, 'stores', 'Store');

			if(!$entity instanceof $focusEntity) {
				$entity = Product::createWithParams($name, $description, $barcode, $size, $useByVariance, $unitPrice, $labelVersionNo, $materials, $categories);
			} else {
				$entity->setBarcode($barcode)
					->setSize($size)
					->setUsedByVariance($useByVariance)
					->setLabelVersionNo($labelVersionNo)
					->clearMaterial()
					->clearCategory()
					->clearStore()
					->setName($name)
					->setDescription($description);
				foreach($materials as $material)
					$entity->addMaterial($material);
				foreach($categories as $category)
					$entity->addCategory($category);
			}
			if($allStores === true)	{
				$entity->addToAllStore();
			} else {
			    if(count($stores) === 0)
			        throw new Exception('You need at least one store for this product!');
				foreach($stores as $store)
					$entity->addStore($store);
			}
			$results ['item'] = $entity->getJson();
			Dao::commitTransaction ();
		}
		catch(Exception $ex)
		{
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}

	private function _idsToObjs($param, $name, $entityName)
	{
		if(!isset($param->$name) || ($ids = trim($param->$name)) === 0 || count($ids = explode(',', $ids)) === 0)
			return array();
		return $entityName::getAllByCriteria('id IN ('.implode(", ", array_fill(0, count($ids), '?')).')', $ids);
	}
}
?>
