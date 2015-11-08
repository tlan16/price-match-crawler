<?php
abstract class magentoB2BconnectorAbstract
{
	/**
	 * The page number
	 * 
	 * @var integer
	 */
	protected $pageNo = 1;
	/**
	 * The page size
	 * 
	 * @var integer
	 */
	protected $pageSize = 30;
	/**
	 * the base url of the magento b2b connector
	 * 
	 * @var string
	 */
	protected static $baseUrl = 'http://app.budgetpc.com.au/api/';
	/**
	 * The user name
	 * 
	 * @var string
	 */
	protected $username = 'frank';
	/**
	 * The password
	 * 
	 * @var string
	 */
	protected $password = 'b85b2c37a170c7fa6100dcca17ba66d370207744';
	/**
	 * The token
	 * 
	 * @var string
	 */
	protected $token = '';
	/**
	 * the time out of curl
	 * 
	 * @var int|null
	 */
	protected static $timeout = null;
	/**
	 * the debug flag
	 * 
	 * @var bool
	 */
	protected $debug = true;
	
	protected function login()
	{
		$prefix = 'userAccount/login';
		$url = self::$baseUrl . $prefix;
		$params = array(
				'username' => $this->username,
				'password' => $this->password
		);
		$url = $url . '?' . http_build_query($params);
		$response = json_decode($tmp = ComScriptCURL::readUrl($url, self::$timeout, $params), true);
		if(!isset($response['token']) || ($token = trim($response['token'])) === '')
			throw new Exception('login failed: cannot retrieve token from remote system.' . PHP_EOL . print_r($tmp, true));
		if($this->debug === true)
			self::log('received token ' . $token);
		$this->token = $token;
	}
	public static function log($msg)
	{
		$msg = $msg . PHP_EOL;
		echo $msg;
	}
}