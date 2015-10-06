<?php
/** MaterialInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class MaterialInfo extends InfoAbstract
{
	/**
	 * The Material of the MaterialInfo
	 * @var Ingredient
	 */
	protected $material;
	/**
	 * getter for ingredient
	 *
	 * @return Material
	 */
	public function getMaterial()
	{
		$this->loadManyToOne('material');
	    return $this->material;
	}
	/**
	 * Setter for Material
	 *
	 * @return MaterialInfo
	 */
	public function setMaterial(Material $material)
	{
	    $this->material = $material;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'mat_info');
		parent::__loadDaoMap();
		
		DaoMap::commit();
	}
}