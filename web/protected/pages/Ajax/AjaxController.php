<?php
/**
 * Ajax Controller
 *
 * @package	web
 * @subpackage	Controller-Page
 *
 * @version	1.0
 *
 * @todo :NOTE If anyone copies this controller, then you require this method to profile ajax requests
 */
class AjaxController extends TService
{
  	/**
  	 * Run
  	 */
  	public function run()
  	{
//   		if(!($this->getUser()->getUserAccount() instanceof UserAccount))
//   			die (BPCPageAbstract::show404Page('Invalid request',"No defined access."));

  		$results = $errors = array();
		try
		{
  			$method = '_' . ((isset($this->Request['method']) && trim($this->Request['method']) !== '') ? trim($this->Request['method']) : '');
            if(!method_exists($this, $method))
                throw new Exception('No such a method: ' . $method . '!');
			$results = $this->$method($_REQUEST);
		}
		catch (Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$this->getResponse()->flush();
        $this->getResponse()->appendHeader('Content-Type: application/json');
        $this->getResponse()->write(StringUtilsAbstract::getJson($results, $errors));
  	}
  	/**
  	 * Getting an entity
  	 *
  	 * @param unknown $params
  	 *
  	 * @throws Exception
  	 * @return multitype:
  	 */
  	private function _get($params)
  	{
  		if(!isset($params['entityName']) || ($entityName = trim($params['entityName'])) === '')
  			throw new Exception('What are we going to get?');
  		if(!isset($params['entityId']) || ($entityId = trim($params['entityId'])) === '')
  			throw new Exception('What are we going to get with?');
  		return ($entity = $entityName::get($entityId)) instanceof BaseEntityAbstract ? $entity->getJson() : array();
  	}
  	/**
  	 * Getting All for entity
  	 *
  	 * @param unknown $params
  	 *
  	 * @throws Exception
  	 * @return multitype:multitype:
  	 */
  	private function _getAll($params)
  	{
  		if(!isset($params['entityName']) || ($entityName = trim($params['entityName'])) === '')
  			throw new Exception('What are we going to get? (invalid entityName provided)');
  		$searchTxt = trim(isset($params['searchTxt']) ? trim($params['searchTxt']) : '');
  		$searchParams = isset($params['searchParams']) ? $params['searchParams'] : array();
  		$pageNo = isset($params['pageNo']) ? trim($params['pageNo']) : null;
  		$pageSize = isset($params['pageSize']) ? trim($params['pageSize']) : DaoQuery::DEFAUTL_PAGE_SIZE;
  		$active = isset($params['active']) ? (intval($params['active']) === 1) : true;
  		$orderBy = isset($params['orderBy']) ? trim($params['orderBy']) : array();

  		$stats = array();
//   		Dao::$debug = true;
  		$items = $entityName::getAllByCriteria($searchTxt, $searchParams, $active, $pageNo, $pageSize, $orderBy, $stats);
//   		Dao::$debug = false;
  		return array('items' => array_map(create_function('$a', 'return $a->getJson();'), $items), 'pagination' => $stats);
  	}

}
?>