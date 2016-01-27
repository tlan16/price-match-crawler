<?php
abstract class magentoB2BconnectorAbstract
{
	const LOG_FILE_PATH = "/tmp/magentoB2Bconnector.log";
	/**
	 * The page number
	 * 
	 * @var integer
	 */
	protected $pageNo = null;
	/**
	 * The page size
	 * 
	 * @var integer
	 */
	protected $pageSize = 30;
	/*
	 * the prefix after base url
	 * 
	 * @var string
	 */
	public $prefix = '';
	/**
	 * the login url prefix
	 * 
	 * @var string
	 */
	private $loginPrefix = 'userAccount/login';
	/**
	 * the base url
	 * 
	 * @var string
	 */
	public $baseurl = '';
	/**
	 * The user name
	 * 
	 * @var string
	 */
	protected $username = '';
	/**
	 * The password
	 * 
	 * @var string
	 */
	protected $password = '';
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
	
	public function __construct($baseurl, $username, $password, $debug = false)
	{
		$this->username = $username;
		$this->password = $password;
		$this->baseurl = $baseurl;
		$this->debug = $debug;
		$this->login();
	}
	private function login()
	{
		$url = $this->baseurl . $this->loginPrefix;
		$params = array(
				'username' => $this->username,
				'password' => $this->password
		);
		$response = json_decode(ComScriptCURL::readUrl($url, self::$timeout, $params), true);
		if(!isset($response['token']) || ($token = trim($response['token'])) === '')
			throw new Exception('login failed: cannot retrieve token from remote system.' . PHP_EOL . print_r($tmp, true));
		$this->token = $token;
		if($this->debug === true)
			self::log('Login success (received token ' . $this->token . ')');
	}
	/**
	 * Find all entities with criterias
	 *
	 * @param string	$url
	 * @param string	$criteria
	 * @param array		$params
	 * @param bool		$activeOnly
	 * @param int		$pageNo
	 * @param int		$pageSize
	 * @param array		$orderBy
	 * @param array   	$stats
	 * @param bool		$debug
	 *
	 * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
	 */
	public function getAllByCriteria($url, $criteria = '', $params = array(), $activeOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array('updated' => 'asc'), &$stats = array())
	{
		// initialise
		$criteria = trim($criteria);
		$activeOnly = (intval($activeOnly) === 1);
	
		// last sync from system setting
		if(!($lastSync = SystemSettings::getByType('last_succ_product_sync')) instanceof SystemSettings)
			throw new Exception('system setting "last_succ_product_sync" not found');
	
		//do {
			$data = array(
					'searchTxt' => $criteria,
					'searchParams' => $params,
					'pageNo' => $this->pageNo,
					'pageSize' => $this->pageSize,
					'active' => $activeOnly,
					'orderBy' => $orderBy,
			);
				
			if($this->debug === true)
				self::log('START retrieving data from ' . $url . ' (pageNo:' . $this->pageNo . ' pageSize:' . $this->pageSize . ')');
			$response = json_decode($this->readUrl($url, self::$timeout, $data, 'GET'), true);
			//$this->pageNo++;
			// validate response from curl
			if(isset($response['items']) && is_array($response = $response['items']) )
			{
				if($this->debug === true)
					self::log('\treceived ' . count($response) . ' items');
				// process received items
				foreach ($response as $item)
					$this->callback($item);
			} else {
				if($this->debug === true)
					self::log('FINISHED retrieving data from ' . $url);
				unset($response);
			}
		//} while( isset($response) && count($response) > 0 );
	}
	public static function log($msg, $newline = PHP_EOL)
	{
		$msg = $msg . $newline;
		file_put_contents(self::LOG_FILE_PATH, 'UTC ' . trim(UDate::now()) . ' ' . get_called_class() . ': ' . $msg, FILE_APPEND);
		echo $msg;
	}
	public function callback($data) { 
		if($this->debug === true)
			self::log(print_r($data), true);
	}
	public function readUrl($url, $timeout = null, array $data = array(), $customerRequest = '', $extraOptions = array())
	{
		$extraOptions[CURLOPT_HTTPHEADER] = array(
				'MAGE_API:' . $this->token
		);
		if($customerRequest === 'GET')
			$url = ($url . (StringUtilsAbstract::endsWith($url, "?") ? '' : "?") . http_build_query($data));
		return ComScriptCURL::readUrl($url, $timeout, $data, $customerRequest, $extraOptions);
	}
}