<?php
/**
 * This is the Question details page
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
	public $menuItem = 'material.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Material';
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
				'name' => 'name_div'
				,'description' => 'description_div'
				,'ingredients' => 'ingredients_div'
				,'new_material_nutrition' => 'new_material_nutrition_div'
				,'material_nutrition' => 'material_nutrition_div'
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
			if (!isset ( $params->CallbackParameter->name ) || ($name = trim ( $params->CallbackParameter->name )) === '')
				throw new Exception ( 'System Error: invalid name passed in.' );
			$description = '';
			if (isset ( $params->CallbackParameter->description ) )
				$description = trim($params->CallbackParameter->description);
			$ingredientIds = array();
			if (isset ( $params->CallbackParameter->ingredients ) && ($tmp = trim($params->CallbackParameter->ingredients)) !== '' )
				$ingredientIds = explode(',', $tmp);
			else $description = trim($params->CallbackParameter->description);
			if (isset ( $params->CallbackParameter->id ) && !($entity = $focusEntity::get(intval($params->CallbackParameter->id))) instanceof $focusEntity )
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			Dao::beginTransaction();
			
			if(!isset($entity) || !$entity instanceof $focusEntity)
				$entity = $focusEntity::create($name,$description);
			else $entity->setName($name)->setDescription($description);
			
			$entity->clearIngredients();
			foreach ($ingredientIds as $ingredientId)
			{
				if(($ingredient = Ingredient::get($ingredientId)) instanceof Ingredient)
					$entity->addIngredient($ingredient);
			}
				
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
