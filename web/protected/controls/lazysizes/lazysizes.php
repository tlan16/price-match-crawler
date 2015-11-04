<?php
/**
 * The lazysizes Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class lazysizes extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$clientScript = $this->getPage()->getClientScript();
			$clientScript->registerHeadScriptFile('lazysizes.js', "https://cdnjs.cloudflare.com/ajax/libs/lazysizes/1.3.1/lazysizes.min.js");
		}
	}
}