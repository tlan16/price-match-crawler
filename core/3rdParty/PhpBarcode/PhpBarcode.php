<?php
require_once dirname(__FILE__) . '/barcodegen/class/BCGFontFile.php';
require_once dirname(__FILE__) . '/barcodegen/class/BCGColor.php';
require_once dirname(__FILE__) . '/barcodegen/class/BCGDrawing.php';
require_once dirname(__FILE__) . '/barcodegen/class/BCGean13.barcode.php';

class PhpBarcode
{
    public static function getBarcodeImg($text, $debug = false)
    {
        try
        {
            $font = new BCGFontFile(dirname(__FILE__) . '/barcodegen/font/Arial.ttf', 12);
            $color_black = new BCGColor(0, 0, 0);
            $color_white = new BCGColor(255, 255, 255);

            // Barcode Part
            $code = new BCGean13();
            $code->setScale(2);
            $code->setThickness(30);
            $code->setForegroundColor($color_black);
            $code->setBackgroundColor($color_white);
            $code->setFont($font);
            $code->parse($text);

            // Drawing Part
            $drawing = new BCGDrawing('', $color_white);
            $drawing->setBarcode($code);
            $drawing->draw();
            $tmpFile = '/tmp/barcode_' . md5($text . trim(UDate::now()));
            $drawing->setFilename($tmpFile);
            $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

			return imagecreatefrompng($tmpFile);
        }
        catch(Exception $ex)
        {
                throw $ex;
        }
    }
}
?>