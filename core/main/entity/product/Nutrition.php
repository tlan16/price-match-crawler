<?php
/** Nutrition Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Nutrition extends ResourceAbstract
{
	/**
	 * The display order of the nutrition
	 * 
	 * @var integer
	 */
	private $order = 0;
	/**
	 * getter for order
	 *
	 * @return int
	 */
	public function getOrder()
	{
	    return $this->order;
	}
	/**
	 * Setter for order
	 *
	 * @return Nutrition
	 */
	public function setOrder($order)
	{
	    $this->order = $order;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'nut');
		DaoMap::setIntType('order', 'TINYINT', 1, true, false, 0);		
		
		parent::__loadDaoMap();

		DaoMap::commit();
	}
	
}