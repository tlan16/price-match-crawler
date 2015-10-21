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
				
			$serachCriteria = isset($param->CallbackParameter->searchCriteria) ? json_decode(json_encode($param->CallbackParameter->searchCriteria), true) : array();
	
			$where = array(1);
			$params = array();
			$query = $class::getQuery();
			foreach($serachCriteria as $field => $value)
			{
				if((is_array($value) && count($value) === 0) || (is_string($value) && ($value = trim($value)) === ''))
					continue;
	
				switch ($field)
				{
					case 'pro.name':
					case 'pro.description':
					case 'pro.barcode':
					case 'pro.usedByVariance':
					case 'pro.description':
					case 'pro.description':
						{
							$searchTokens = array();
							StringUtilsAbstract::permute(preg_split("/[\s,]+/", $value), $searchTokens);
							$likeArray = array();
							foreach($searchTokens as $index => $tokenArray) 
							{
								$key = md5($field . $index);
								$params[$key] = '%' . implode('%', $tokenArray) . '%';
								$likeArray[] = $field . " like :" . $key;
							}
								
							$where[] = '(' . implode(' OR ', $likeArray) . ')';
							break;
						}
					case 'pro.size':
					case 'pro.unitPrice':
					case 'pro.labelVersionNo':
						{
							$key = md5($field);
							$where[] =  $field . " = :" . $key;
							$params[$key] = $value;
							break;
						}
				}
			}
			$stats = array();
			
			$keys['pro_info.typeId'] = md5('pro_info.typeId');
			$params[$keys['pro_info.typeId']] = ProductInfoType::ID_STORE;
			
			$keys['pro_info.entityName'] = md5('pro_info.entityName');
			$params[$keys['pro_info.entityName']] = ProductInfoType::ENTITY_NAME_STORE;
			
			$keys['pro_info.entityId'] = md5('pro_info.entityId');
			$params[$keys['pro_info.entityId']] = Core::getStore()->getId();
			
			$query->eagerLoad('Product.infos', 'inner join', 'pro_info', '(pro_info.productId = pro.id and pro_info.active = 1 and pro_info.typeId = :' . $keys['pro_info.typeId'] . ' and pro_info.entityName = :' . $keys['pro_info.entityName'] . ' and (pro_info.entityId = :' . $keys['pro_info.entityId'] . ' or pro_info.entityId = 0))');
			
			$objects = $class::getAllByCriteria(implode(' AND ', $where), $params, false, $pageNo, $pageSize, array('id' => 'desc'), $stats);
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
			$imgFile = $newLabel->generateImg(300, 800);
			$results['item'] = base64_encode(file_get_contents($imgFile));
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}