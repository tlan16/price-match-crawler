<?php
require_once dirname(__FILE__) . '/../bootstrap.php';
class postTest {

	protected  static $_api = array('URL' => "http://192.168.1.103:8107/api/", 'token' => '');
// $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/api/';

// $url = $baseUrl . 'UserAccount/login';
// $params = array('username' => 'helin16', 'password' => '262bab1f48755709edd4c9c8774ec2d0d97857e7');
// $result = post($url, $params);
// var_dump($result);

// $array = json_decode($result, true);
// var_dump($array);

// $params = array('token' => $array['token'] );
// $url = $baseUrl . 'Product/dataFeedImport';
// $result = post($url, $params);
// var_dump($result);
// $result=getMatchPrice();
// var_dump($result);
	public static function run()
	{
		$result=getMatchPrice();
		var_dump($result);
	}

	public static function getMatchPrice()
	{
		$result = array();
		//$priceMatchResults = HTMLParser::getPriceListForProduct($this->base_url, $this->sku);
		$sku='GS108T-200AUS' ;
		$priceMatchResults=self::getPrices($sku);
		$priceMatchResults = $priceMatchResults['items'];
	
		foreach($priceMatchResults as $priceMatchResult)
		{
			if(($name = trim($priceMatchResult['name'])) === '')
				continue;
	
				$price = str_replace(' ', '', str_replace('$', '', str_replace(',', '', $priceMatchResult['price']) ) );
				$url = $priceMatchResult['url'];
					
				foreach (PriceMatchCompany::getAll() as $company)
				{
					if(strtolower($name) === strtolower($company->getCompanyAlias()))
					{
						$result[] = array('PriceMatchCompany'=> $company, 'price'=> $price, 'name'=> $name, 'url'=> $url);
						if($this->debug === true)
							echo $company->getCompanyName() . '(id=' . $company->getId() . "), $" . $price . "\n";
					}
				}
		}
		return $result;
	}

	
	public static function _login()
	{
		//self::logOut(__FILE__, __FUNCTION__, __LINE__, '=== start api _login =====');
		Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
	
		if(!isset($_api['URL']) || ($apiUrl = trim($_api['URL'])) === '')
			throw new Exception('No API URL set!');
			$url = $apiUrl . 'UserAccount/login';
			$data = json_encode(array('username' => Core::getUser()->getUserName(), 'password' => Core::getUser()->getPassword()));
	
			_postJson($url, $data);
			if(trim($_api['token']) === '')
				throw new Exception('Invalid token');
	
				//self::logOut(__FILE__, __FUNCTION__, __LINE__, '=== end api _login =====');
	
	}
	public static function _postJson($url, $data)
	{
		//global $_api;
		//self::logOut(__FILE__, __FUNCTION__, __LINE__, '=== start api _postJson =====');
	
		$extraOptions = array( CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen($data),
						'MAGE_API:' . $_api['token']
				)
		);
	
		$result = ComScriptCURL::readUrl($url, null, array(), '', $extraOptions);
	
		$result = json_decode($result, true);
		if (isset($result['token']) && ($token = trim($result['token'])) !== '')
		{
			self::$_api['token'] = $token;
			
			//return $result;
		}
		else
		{
			
			$result = null;
		}
		//self::logOut(__FILE__, __FUNCTION__, __LINE__, '=== end api _postJson =====');
		return $result;
	}
	public static function getPrices($sku)
	{
		//global $_api;
		$params = $results = array();
			
		self::_login();
	
		$apiUrl = trim($_api['URL']);
			
		$api_url = $apiUrl . 'PriceMatch/getPrices';
	
		// first try to find product by manufacturerPartNo
		$params = array('searchTxt' => 'sku = ?',
				'searchParams' =>  array($sku),
		);
	
		$data = json_encode($params);
		$results_api = self::_postJson($api_url, $data);
		var_dump($results_api);
		
	
		$token = trim($results_api['token']);
		if ($token !== '')
		{
			//found then get the stockOnHand in .5
			$results = $results_api;
		}
			
		return $results;
	}
}

postTest::run();