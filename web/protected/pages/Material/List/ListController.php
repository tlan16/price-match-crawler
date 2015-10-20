<?php
/**
 * This is the listing page for ProductCodeType
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ListController extends CRUDPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'material';
	protected $_focusEntity = 'Material';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		if(!AccessControl::canAccessAllergentListingPage(Core::getRole()))
			die('You do NOT have access to this page');
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
		$js .= 'pageJs.setCallbackId("updateItem", "' . $this->updateItemBtn->getUniqueID(). '");';
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
			foreach($serachCriteria as $field => $value)
			{
				if((is_array($value) && count($value) === 0) || (is_string($value) && ($value = trim($value)) === ''))
					continue;
				
				$query = $class::getQuery();
				switch ($field)
				{
					case 'mat.name':
					case 'mat.description':
						{
							$searchTokens = array();
							StringUtilsAbstract::permute(preg_split("/[\s,]+/", $value), $searchTokens);
							$likeArray = array();
							foreach($searchTokens as $index => $tokenArray) {
								$key = md5($field . $index);
								$params[$key] = '%' . implode('%', $tokenArray) . '%';
								$likeArray[] = $field . " like :" . $key;
							}
							
							$where[] = '(' . implode(' OR ', $likeArray) . ')';
							break;
						}
					case 'mat.active':
						{
							$value = intval($value);
							if($value === 0 || $value === 1)
							{
								$where[] =  $field . " = :active ";
								$params['active'] = $value;
							}
							break;
						}
					case 'mat.ingredients':
						{
							$ingredients = explode(',', trim($value));
							if(count($value) > 0)
							{
								$ps = array();
								$keys = array();
								foreach($ingredients as $index => $value){
									$key = md5($field . '_' . $index);
									$keys[] = ':' . $key;
									$ps[$key] = trim($value);
								}
								$key = md5($field . '_' . 'entityName');
								$ps[$key] = 'Ingredient';
								$query->eagerLoad('Material.infos', 'inner join', 'mat_info_ingr', 'mat.id = mat_info_ingr.materialId and mat_info_ingr.entityName = :' . $key . ' and mat_info_ingr.entityId in (' . implode(',', $keys) . ')');
								$params = array_merge($params, $ps);
							}
							break;
						}
					case 'mat.serveMeasurements':
						{
							break;
						}
					case 'mat.nutritions':
						{
							break;
						}
					case 'mat.qty':
						{
							break;
						}
				}
			}
			$stats = array();
			
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
}
?>
