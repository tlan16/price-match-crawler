<?php
class APIPriceMatchService extends APIServiceAbstract
{
   protected $entityName = 'Product';
   
   
   public function post_getPrices($params)
   {
   	return $this->getall($params);
   	 
   }
   
   public function put_getPrices($params)
   {
   	return $this->getall($params);
   	 
   }

   /**
    * Getting All for entity
    *
    * @param unknown $params
    *
    * @throws Exception
    * @return multitype:multitype:
    */
   private function getall($params)
   {
   	
   	$strSql = 'select t.productId,p.sku, t.vendorId, v.name, r.price, r.url, t.id
				from
				(
					select productId,vendorId,Max(id) as id 
					from record
					group by productId,vendorId
				) as t, product p, record r, vendor v
				where p.id = r.productId and r.active=1 and p.active=1 and v.id = r.vendorId
				and p.sku = ? and t.productId=r.productId and t.vendorId=r.vendorId
				and r.id = t.id
				order by vendorId';
   	
	$this->_runner->log('PriceMatch: ', __CLASS__ . '::' . __FUNCTION__);
   	$searchParams = $this->_getPram($params, 'searchParams', array());
//    	ob_start();
//    	var_dump($searchParams);
//    	$content = ob_get_contents();
//    	ob_end_clean();
	if (count($searchParams) === 0)
	{
		$this->_runner->log('sku parameter is null', __CLASS__ . '::' . __FUNCTION__);
		return null;
	}
   	
   	
  	$items = Dao::getResultsNative($strSql, $searchParams);
   	
  		
  	$results = array('items' => $items);
  	
  	ob_start();
  	var_dump($results);
  	$content = ob_get_contents();
  	ob_end_clean();
  	//file_put_contents('/tmp/datafeed/web.log', __FILE__ .':' . __FUNCTION__ . ':' . __LINE__ . ':' . $content . PHP_EOL, FILE_APPEND | LOCK_EX);
  	
  	$this->_runner->log($content, __CLASS__ . '::' . __FUNCTION__);
   	return $results;

   }
   

      
}