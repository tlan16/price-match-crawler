<?php

class ListController extends CRUDPageAbstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_focusEntity = 'Product';
	}
	/**
	 * (non-PHPdoc)
	 * @see CRUDPageAbstract::_getEndJs()
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= "pageJs.getResults(true, " . $this->pageSize . ");";
		$js .= "pageJs.loadSelect2();";
		$js .= "pageJs._bindSearchKey();";
		$js .= 'pageJs.setCallbackId("printLabel", "' . $this->printLabelBtn->getUniqueID(). '")';
		$js .= '.setCallbackId("updateItem", "' . $this->updateItemBtn->getUniqueID(). '");';
		return $js;
	}
	/**
	 * Getting the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function getItems($sender, $param)
	{
		$results = $errors = array();
		try
		{
			$class = trim($this->_focusEntity);
			$pageNo = 1;
			$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
			if(isset($param->CallbackParameter->pagination))
			{
				$pageNo = $param->CallbackParameter->pagination->pageNo;
				$pageSize = $param->CallbackParameter->pagination->pageSize;
			}
			$stats = array();
			$class::getQuery()->eagerLoad('Product.infos', 'inner join', 'pro_info', '(pro_info.productId = pro.id and pro_info.active = 1 and pro_info.typeId = ? and pro_info.entityName = ? and (pro_info.entityId = ? or pro_info.entityId = 0))');
			$objects = $class::getAllByCriteria('pro.active = 1', array(ProductInfoType::ID_STORE, ProductInfoType::ENTITY_NAME_STORE, Core::getStore()->getId()), true, $pageNo, $pageSize, array(), $stats);
			
			$results['pageStats'] = $stats;
			$results['items'] = array();
			foreach($objects as $obj)
				$results['items'][] = $obj->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	/**
	 * Getting the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function printLabel($sender, $params)
	{
		$results = $errors = array();
		try
		{
			$focusEntity = trim($this->_focusEntity);
			if (!isset ( $params->CallbackParameter->id ) || !($entity = $focusEntity::get(intval($params->CallbackParameter->id))) instanceof $focusEntity )
				throw new Exception ( 'System Error: invalid id passed in.' );
			$newLabel = null;
			$entity->printLabel(null, null, $newLabel);
			$imgFile = $newLabel->generateImg(200, 600);
			$results['item'] = base64_encode(file_get_contents($imgFile));
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}