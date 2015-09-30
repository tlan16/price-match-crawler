<?php

class MaterialNutrition extends BaseEntityAbstract
{
	protected $material;
	
	protected $nutrition;
	
	protected $qty;
	
	protected $serveManagement;
	
	public function getMaterial()
	{
		$this->loadManyToOne('material');
		return $this->material;
	}
	
	public function setMaterial(Material $material)
	{
		$this->material = $material;
		return $this;
	}
	
	public function getNutrition()
	{
		$this->loadManyToOne('nutrition');
		return $this->nutrition;
	}
	
	public function setNutrition(Nutrition $nutrition)
	{
		$this->nutrition = $nutrition;
		return $this;
	}
	
	public function getQty()
	{
		return $this->qty;
	}
	
	public function setQty($qty)
	{
		if(!is_numeric($qty))
			throw new Exception('Quantity must be numeric');
		
		$this->qty = $qty;
		return $this;
	}
	
	public function getServeMeasurement()
	{
		$this->loadManyToOne('serveMeasurement');
		return $this->serveManagement;
	}
	
	public function setServeMeasurement(ServeManagement $serveMeasurement)
	{
		$this->serveManagement = $serveMeasurement;
		return $this;
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
		DaoMap::setManyToOne("serveManagement", "ServeManagement", "srv_mgm");
		DaoMap::setIntType('qty', 'int', 5);
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
	
}