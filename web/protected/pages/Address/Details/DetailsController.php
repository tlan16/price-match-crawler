<?php
/**
 * This is the Nutrition details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class DetailsController extends DetailsPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'address.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Address';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		if(!AccessControl::canAccessAllergentDetailPage(Core::getRole()))
			die('You do NOT have access to this page');
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= "pageJs.setPreData(" . json_encode(array()) . ");";
		$js .= "pageJs._containerIds=" . json_encode(array(
				'name' => 'contactName_div'
				,'contactName' => 'contactName_div'
				,'contactNo' => 'contactNo_div'
				,'street' => 'street_div'
				,'city' => 'city_div'
				,'region' => 'region_div'
				,'country' => 'country_div'
				,'postCode' => 'postCode_div'
				,'comments' => 'comments_div'
				,'saveBtn' => 'save_btn'
		)) . ";";
		$js .= "pageJs.load();";
		$js .= "pageJs.bindAllEventNObjects();";
		if(!AccessControl::canEditAllergentDetailPage(Core::getRole()))
			$js .= "pageJs.readOnlyMode();";
		return $js;
	}
	/**
	 * save the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function saveItem($sender, $params)
	{

		$results = $errors = array();
		try
		{
			$focusEntity = $this->getFocusEntity();
			
			$contactName = '';
			if (isset ( $params->CallbackParameter->contactName ) )
				$contactName = trim($params->CallbackParameter->contactName);
			
			$contactNo = '';
			if (isset ( $params->CallbackParameter->contactNo ) )
				$contactNo = trim($params->CallbackParameter->contactNo);
			
			$street = '';
			if (isset ( $params->CallbackParameter->street ) )
				$street = trim($params->CallbackParameter->street);
			
			$city = '';
			if (isset ( $params->CallbackParameter->city ) )
				$city = trim($params->CallbackParameter->city);
			
			$region = '';
			if (isset ( $params->CallbackParameter->region ) )
				$region = trim($params->CallbackParameter->region);
			
			$country = '';
			if (isset ( $params->CallbackParameter->country ) )
				$country = trim($params->CallbackParameter->country);
			
			$postCode = '';
			if (isset ( $params->CallbackParameter->postCode ) )
				$postCode = trim($params->CallbackParameter->postCode);
			
			if (isset ( $params->CallbackParameter->id ) && !($entity = $focusEntity::get(intval($params->CallbackParameter->id))) instanceof $focusEntity )
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			Dao::beginTransaction();
			
			if(!isset($entity) || !$entity instanceof $focusEntity)
				$entity = $focusEntity::create($street, $city, $region, $country, $postCode, $contactName, $contactNo);
			else $entity->setStreet($street)
						->setCity($city)
						->setRegion($region)
						->setCountry($country)
						->setPostCode($postCode)
						->setContactName($contactName)
						->setContactNo($contactNo);
			
			$results ['item'] = $entity->save()->getJson ();
			Dao::commitTransaction ();
		}
		catch(Exception $ex)
		{
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>
