<?php

class ProductDetailsController extends CRUDPageAbstract
{
	public function __construct()
	{
		parent::__construct();
		
		if(!AccessControl::checkIfCanAccessPage(PageHandler::PRODUCT_DETAILS_PAGE))
			die('You do not acces to this page');
		
		$this->_focusEntity = 'Product';
	}
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		
		if(!$this->isCallBack && !$this->isPostBack)
		{
			echo "fsafsdf";
		}
	}
}