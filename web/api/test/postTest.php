<?php
require_once dirname(__FILE__) . '/../bootstrap.php';


$_api = array('URL' => "http://localhost:8107/api/", 'token' => '');
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
$result=getMatchPrice();
var_dump($result);

function getMatchPrice()
{
	$result = array();
	//$priceMatchResults = HTMLParser::getPriceListForProduct($this->base_url, $this->sku);
	$sku='GS108T-200AUS' ;
	$priceMatchResults=getPrices($sku);
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




function post($url, $data)
{
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($data));
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function _login()
{
	global $_api;
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
function _postJson($url, $data)
{
	global $_api;
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
		$_api['token'] = $token;
		//return $result;
	}
	else
	{
		
		$result = null;
	}
	//self::logOut(__FILE__, __FUNCTION__, __LINE__, '=== end api _postJson =====');
	return $result;
}
function getPrices($sku)
{
	global $_api;
	$params = $results = array();
	$stockOnHand = 0;

	_login();

	$apiUrl = trim($_api['URL']);
		
	$api_url = $apiUrl . 'PriceMatch/getPrices';

	// first try to find product by manufacturerPartNo
	$params = array('searchTxt' => 'sku = ?',
			'searchParams' =>  array($sku),
	);

	$data = json_encode($params);
	$results_api = _postJson($api_url, $data);
	var_dump($results_api);
	

	$token = trim($results_api['token']);
	if ($token !== '')
	{
		//found then get the stockOnHand in .5
		$results = $results_api;
	}
		
	return $results;
}