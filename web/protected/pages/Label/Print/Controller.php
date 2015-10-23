<?php
/**
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Controller extends BPCPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'label.print';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Label';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	public function getLabelHTML()
	{
	    $labelId = trim($this->Request['id']);
	    if(!($label = Label::get($this->Request['id'])) instanceof Label)
	        return '<h3>No label for ID: ' . $labelId;
	    $width = (isset($_REQUEST['width']) ? intval($_REQUEST['width']) : 270);
	    $height = (isset($_REQUEST['height']) ? intval($_REQUEST['height']) : 800);
	    return LabelPrinter::generateHTML($label, $width, $height);
	}
}
?>
