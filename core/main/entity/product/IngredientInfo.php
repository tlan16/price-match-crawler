<?php
/** IngredientInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class IngredientInfo extends InfoAbstract
{
	/**
	 * The Ingredient of the IngredientInfo
	 * @var Ingredient
	 */
	protected $ingredient;
	/**
	 * getter for ingredient
	 *
	 * @return Ingredient
	 */
	public function getIngredient()
	{
		$this->loadManyToOne('ingredient');
	    return $this->ingredient;
	}
	/**
	 * Setter for ingredient
	 *
	 * @return IngredientInfo
	 */
	public function setIngredient($ingredient)
	{
	    $this->ingredient = $ingredient;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'ingr_info');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}