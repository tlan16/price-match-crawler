<?php

class MaterialNutrition extends BaseEntityAbstract
{
	protected $material;
	
	protected $nutrition;
	
	protected $qty;
	
	protected $serveMeasurement;
	
	/**
	 * Getter function for Material
	 * @return Material
	 */
	public function getMaterial()
	{
		$this->loadManyToOne('material');
		return $this->material;
	}
	
	/**
	 * Setter function for Material
	 * @param Material $material
	 * @return MaterialNutrition
	 */
	public function setMaterial(Material $material)
	{
		$this->material = $material;
		return $this;
	}
	
	/**
	 * Getter function for Nutrition
	 * @return Nutrition
	 */
	public function getNutrition()
	{
		$this->loadManyToOne('nutrition');
		return $this->nutrition;
	}
	
	/**
	 * Setter function for Nutrition
	 * @param Nutrition $nutrition
	 * @return MaterialNutrition
	 */
	public function setNutrition(Nutrition $nutrition)
	{
		$this->nutrition = $nutrition;
		return $this;
	}
	
	/**
	 * Getter function for Qty
	 */
	public function getQty()
	{
		return $this->qty;
	}
	
	/**
	 * Setter function for Qty
	 * @param int $qty
	 * 
	 * @throws Exception
	 * @return MaterialNutrition
	 */
	public function setQty($qty)
	{
		if(!is_numeric($qty))
			throw new Exception('Quantity must be numeric');
		
		$this->qty = $qty;
		return $this;
	}
	
	/**
	 * Getter function for ServeManagement
	 * @return ServeMeasurement
	 */
	public function getServeMeasurement()
	{
		$this->loadManyToOne('serveMeasurement');
		return $this->serveMeasurement;
	}
	
	/**
	 * Setter function for ServeManagement
	 * @param ServeMeasurement $serveMeasurement
	 * @return MaterialNutrition
	 */
	public function setServeMeasurement(ServeMeasurement $serveMeasurement)
	{
		$this->serveMeasurement = $serveMeasurement;
		return $this;
	}
	
	/**
	 * This is the create function for MaterialNutrition
	 * @param Material $material
	 * @param Nutrition $nutrition
	 * @param int $qty
	 * @param ServeMeasurement $serveMeasurement
	 * 
	 * @throws Exception
	 * @return MaterialNutrition
	 */
	public static function create(Material $material, Nutrition $nutrition, $qty, ServeMeasurement $serveMeasurement)
	{
		if(!is_numeric($qty) || $qty <= 0)
			throw new Exception('Qty must be a non-zero numeric value');
		
		$mn = new MaterialNutrition();
		$mn->setMaterial($material)
		   ->setNutrition($nutrition)
		   ->setQty($qty)
		   ->setServeMeasurement($serveMeasurement)
		   ->setActive(true)
		   ->save();
		
		return $mn;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'mat_nut');
		DaoMap::setManyToOne("material", "Material", "mat");
		DaoMap::setManyToOne("nutrition", "Nutrition", "nut");
		DaoMap::setManyToOne("serveMeasurement", "ServeMeasurement", "srv_mgm");
		DaoMap::setIntType('qty', 'int', 5);
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
	
}