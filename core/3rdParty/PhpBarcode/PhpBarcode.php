<?php
require_once dirname(__FILE__) . '/PhpBarcode_Code39.php';
class PhpBarcode
{
    public static function getBarcodeImg($text, $textPos="", $noText = false, $type = 'Code39', $imgtype = 'png', $debug = false)
    {
    	$textPos = (trim ( $textPos ) == "") ? PhpBarcode_Code39::$TextPos_Below : "";

		// Make sure no bad files are included
		if (! preg_match ( '/^[a-zA-Z0-9_-]+$/', $type ))
			throw new Exception ( 'Invalid barcode type ' . $type );
		if (! include_once (dirname ( __FILE__ ) . '/PhpBarcode_' . $type . '.php'))
			throw new Exception ( $type . ' barcode is not supported' );

		$classname = 'PhpBarcode_' . $type;
		if (! in_array ( 'draw', get_class_methods ( $classname ) ))
			throw new Exception ( "Unable to find draw method in '$classname' class" );
		$text = strtoupper ( trim ( $text ) );
		$obj = new $classname( $text, $textPos );
		return $obj->draw ( $text, $noText, $imgtype );
    }
}
?>
