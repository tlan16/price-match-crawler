<?php
class magentoB2BProductConnector extends magentoB2BconnectorAbstract
{
	public static function importProducts($debug = true) 
	{
		$connector = new self();
		$connector->debug = (intval($debug) === 1);
		$connector->login();
		$prefix = 'product';
		$url = self::$baseUrl . $prefix;
		$systemSetting = SystemSettings::getByType('last_succ_product_sync');
		if(!$systemSetting instanceof SystemSettings || ($lastSync = trim($systemSetting->getValue())) === '')
			throw new Exception('cannot get last success product sync date from system setting');
			
		$response = array();
		while ($connector->pageNo === 1 || count($items) > 0)
		{
			$params = array(
					'token' => $connector->token,
					'pageSize' => $connector->pageSize,
					'pageNo' => $connector->pageNo,
					'searchTxt' => 'created > :created_from',
					'searchParams' => array('created_from' => $lastSync),
					'orderBy' => array('created' => 'ASC')
			);
			if($connector->debug === true)
				self::log('getting product with pageNo=' . $params['pageNo'] . ', pageSize=' . $params['pageSize'] . ', token=' . $params['token']);
			
			$url = $url . '?' . http_build_query($params);
			
			$response = json_decode(ComScriptCURL::readUrl($url, self::$timeout, $params, 'GET'), true);
			$items = array();
			if(isset($response['items']) && is_array($tmp = $response['items']) )
				$items = $tmp;
			if($connector->debug === true)
				self::log('received ' . count($items) . ' items');
			foreach ($items as $item)
			{
				try {
					$transStarted = false;
					try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
				
					if(isset($item['sku']) && ($sku = trim($item['sku'])) !== '')
						$product = Product::create($sku);
					
					if($transStarted === false)
					{
						Dao::commitTransaction();
						if($connector->debug === true)
							self::log('Product imported with sku=' . $product->getSku());
						if(isset($item['created']) && ($created = trim($item['created'])) !== '')
						{
							$systemSetting->setValue($created)->save();
							if($connector->debug === true)
								self::log('last_succ_product_sync now set to ' . $created);
						}
					}
				} catch (Exception $ex) {
					if($transStarted === false)
						Dao::rollbackTransaction();
					throw $ex;
				}
			}
			if(isset($response['token']) && ($token = trim($response['token'])) !== '')
			{
				$connector->token = $token;
				if($connector->debug === true)
					self::log('received token ' . $connector->token);
			}
			$connector->pageNo++;
		}
	}
}