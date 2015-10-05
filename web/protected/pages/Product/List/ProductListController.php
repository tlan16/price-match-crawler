<?php

class ProductListController extends CRUDPageAbstract
{
	public function __construct()
	{
		parent::__construct();
		if(!AccessControl::checkIfCanAccessPage(PageHandler::PRODUCT_LISTING_PAGE, Core::getRole()))
			die('You cannot access this page');
		
		$this->_focusEntity = 'Product';
	}
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->IsPostBack && !$this->IsCallback)
		{
			$className = trim($this->_focusEntity);
			$productArray = $className::getAllByCriteria();
		}
		
		echo "fasfsdf";
	}
	
}