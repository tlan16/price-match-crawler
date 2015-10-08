<?php

class ProductListController extends CRUDPageAbstract
{
	public function __construct()
	{
		parent::__construct();
		if(!AccessControl::checkIfCanAccessPage(PageHandler::PRODUCT_LISTING_PAGE, Core::getRole()))
			die('You cannot access this page');
		else
			die('fsdfd');
		
		$this->_focusEntity = 'Product';
		var_dump($this->_focusEntity); die();
	}
	
	public function onInit($param)
	{
		parent::onInit($param);
	}
	
	public function onLoad($param)
	{
		parent::onLoad($param);
	}
	
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= "alert('loaded')";
		return $js;
	} 
	
}