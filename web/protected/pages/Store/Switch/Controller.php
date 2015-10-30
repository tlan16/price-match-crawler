<?php
class Controller extends BPCPageAbstract
{
	public $menuItem = 'switch.store';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$user = Core::getUser();
		$currentStoreId = intval(Core::getStore()->getId());
		$stores = $user->getStores();
		$storesJson = array();
		foreach ($stores as $store)
			$storesJson[] = $store->getJson(intval($store->getId()) === $currentStoreId ? array('selected' => true) : array());
		
		$js = parent::_getEndJs();
		$js .= 'pageJs._preData=(' . json_encode(array(
				'stores' => $storesJson
				,'containerId' => 'resultDiv'
		)) . ');';
		$js .= 'pageJs.load();';
		$js .= 'pageJs.setCallbackId("switchStore", "' . $this->switchStoreBtn->getUniqueID(). '");';
		return $js;
	}
	public function switchStore($sender, $param)
	{
		$results = $errors = array();
		try
		{
			if(!isset($param->CallbackParameter->store) || !($store = Store::get(intval($param->CallbackParameter->store))) instanceof Store)
				throw new Exception('Invalid Store Passed in');
			Core::setUser(Core::getUser(), Core::getRole(), $store);
			$results['item'] = $store;
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
