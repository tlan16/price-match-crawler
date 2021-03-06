<?php
/**
 * The HiStock Library Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class HiStock extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		$clientScript = $this->getPage()->getClientScript();
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR);
			// Add jQuery library
			// Add mousewheel plugin (this is optional)
			$clientScript->registerScriptFile('histock.js', "http://code.highcharts.com/stock/highstock.js");
			$clientScript->registerScriptFile('exporting.js', "http://code.highcharts.com/stock/modules/exporting.js");
		}
	}
}