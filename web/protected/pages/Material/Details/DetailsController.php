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
			if (isset ( $params->CallbackParameter->id ) && !($entity = $focusEntity::get(intval($params->CallbackParameter->id))) instanceof $focusEntity )
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			$ingredients = array();
			foreach ($ingredientIds as $ingredientId)
			{
				if(($ingredientId = intval($ingredientId)) !== 0 && ($ingredient = Ingredient::get($ingredientId)) instanceof Ingredient)
					$ingredients[] = $ingredient;
			}
				
			$material_nutritions = array();
			foreach ($params->CallbackParameter->material_nutrition as $material_nutrition)
			{
				if (!isset ($material_nutrition->nutrition) || ($nutritionId = intval( $material_nutrition->nutrition )) === 0 || !($nutrition = Nutrition::get($nutritionId)) instanceof Nutrition)
					continue;
				if (!isset ($material_nutrition->qty) || ($qty = trim ( $material_nutrition->qty )) === '')
					continue;
				if (!isset ($material_nutrition->serveMeasurement) || ($serveMeasurementId = intval ( $material_nutrition->serveMeasurement )) === 0 || !($serveMeasurement = ServeMeasurement::get($serveMeasurementId)) instanceof ServeMeasurement)
					continue;
				$material_nutritions[] = array('nutrition' => $nutrition, 'qty' => $qty, 'serveMeasurement' => $serveMeasurement);
			}
				
			Dao::beginTransaction();
			
			if(!isset($entity) || !$entity instanceof $focusEntity)
				$entity = $focusEntity::createWithParams($name, $description, $ingredients);
			else {
				$entity->setName($name)->setDescription($description)->clearIngredients();
				foreach ($ingredients as $ingredient)
					$entity->addIngredient($ingredient);
			}
			
			$entity->clearMaterialNutrition();
			foreach ($material_nutritions as $material_nutrition)
				$entity->addNutrition($material_nutrition['nutrition'], $material_nutrition['qty'], $material_nutrition['serveMeasurement']);
			
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
