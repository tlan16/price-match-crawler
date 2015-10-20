<?php
abstract class LabelPrinter
{
    public static function generateImg(Label $label, $width, $height)
    {
        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        $startX = 10;
        $baseFont = 9;
        $black = imagecolorallocate($img, 0, 0, 0);
        $fontFile = dirname(__FILE__) . '/arial.ttf';
        $versionNo = $label->getVersionNo();
        $dimensions = imagettfbbox(7, 0, $fontFile, $versionNo);
        imagettftext($img, 7, 0, $width - abs($dimensions[4] - $dimensions[0]) - $startX, 15, $black, $fontFile, $versionNo);
        $startY = abs($dimensions[7] - $dimensions[1]);
        $lineNo = 1;
        $lineHeight = 24;
        self::_imagecenteredstring($img, $baseFont + 5, $width, $startY + $lineHeight * ($lineNo++), $label->getProduct()->getName(), $black, $fontFile);
        self::_imagecenteredstring($img, $baseFont, $width, $startY + $lineHeight * ($lineNo++), 'Price', $black, $fontFile);
        self::_imagecenteredstring($img, $baseFont + 2, $width, $startY + $lineHeight * ($lineNo), StringUtilsAbstract::getCurrency($label->getPrintedPrice()), $black, $fontFile);
        $qrImgFile = self::_qrCodeImage('http://www.sushiandco.com.au/');
        $qrCodeImg = imagecreatefrompng($qrImgFile);
        list($qrCodeImg_width, $qrCodeImg_height) = getimagesize($qrImgFile);
        $yPos = $startY + $lineHeight * $lineNo;
        imagecopy($img, $qrCodeImg, ($width - $qrCodeImg_width)/2, $yPos, 0, 0, $qrCodeImg_width, $qrCodeImg_height);
        $startY = $yPos + $qrCodeImg_height + 10;
        $lineNo = 0;
        imagettftext($img, $baseFont + 2, 0, $startX, $startY + $lineHeight * ($lineNo++), $black, $fontFile, 'Use By: ' . $label->getUseByDate()->format('d/m/Y'));
        self::_imagecenteredstring($img, $baseFont, $width, $startY + $lineHeight * ($lineNo++), 'Keep Refrigerated', $black, $fontFile);
        imagettftext($img, $baseFont + 2, 0, $startX, $startY + $lineHeight * ($lineNo++), $black, $fontFile, 'Allergen Warning:');
        $alleNames = self::_getAllergentNames($label->getProduct());
        self::_imagecenteredstring($img, $baseFont, $width, $startY + $lineHeight * ($lineNo++), ('Contain: ' . implode(', ', $alleNames)), $black, $fontFile);
        imagettftext($img, $baseFont + 2, 0, $startX, $startY + $lineHeight * ($lineNo++), $black, $fontFile, 'Ingredients:');
        $ingredientsTxtArr = self::_getIngredientNames($label->getProduct());
        self::_imagecenteredstring($img, $baseFont, $width, $startY + $lineHeight * $lineNo, wordwrap(implode(', ', $ingredientsTxtArr), 35, "\n"), $black, $fontFile);
        $lineNo = $lineNo + 5;
        self::_imagecenteredstring($img, $baseFont + 2, $width, $startY + $lineHeight * ($lineNo++), 'Nutrition Panel', $black, $fontFile);
        $mNutritions = self::_getMaterialNutrions($label->getProduct());
        foreach($mNutritions as $mNutrition) {
            imagettftext($img, $baseFont, 0, $startX, $startY + $lineHeight * $lineNo, $black, $fontFile, $mNutrition->getNutrition()->getName() . ' (' . $mNutrition->getServeMeasurement()->getName() . ')');
            imagettftext($img, $baseFont, 0, $startX + ($width * 0.85), $startY + $lineHeight * ($lineNo++), $black, $fontFile, $mNutrition->getQty());
        }
        $barcodeImg = PhpBarcode::getBarcodeImg($label->getProduct()->getBarcode());
        $barcodeImg_width = imagesx ($barcodeImg);
        $barcodeImg_height = imagesy ($barcodeImg);
        imagecopy($img, $barcodeImg, ($width/2 - $barcodeImg_width/2), $height - $barcodeImg_height, 0, 0, $barcodeImg_width, $barcodeImg_height);
        // Output the image
        $file = '/tmp/label_' . md5('Label' . '|' . trim(UDate::now()));
        imagejpeg($img, $file, 75);

        // Free up memory
        imagedestroy($img);
        unlink($qrImgFile);
        return $file;
    }
    private static function _qrCodeImage($text) {
        $file = '/tmp/label_qr_' . md5($text . trim(UDate::now()));
        QRcode::png($text, $file);
        return $file;
    }
    /**
     * getting the ingredient names
     *
     * @param Product $product
     *
     * @return array
     */
    private static function _getIngredientNames(Product $product) {
        $ingredientsTxtArr = array();
        foreach($product->getMaterials() as $material) {
            foreach($material->getIngredients() as $ingredient)
                $ingredientsTxtArr[] = $ingredient->getName();
        }
        return array_unique($ingredientsTxtArr);
    }
    /**
     * Getting the AllergentNames
     *
     * @param Product $product
     *
     * @return array
     */
    private static function _getAllergentNames(Product $product) {
        $names = array();
        foreach($product->getMaterials() as $material) {
            foreach($material->getIngredients() as $ingredient) {
                foreach($ingredient->getAllergents() as $allergent) {
                    $names[] = $allergent->getName();
                }
            }
        }
        return array_unique($names);
    }
    /**
     * Getting the MaterialNutrtion
     *
     * @param Product $product
     *
     * @return array
     */
    private static function _getMaterialNutrions(Product $product) {
        $mNutritions = array();
        foreach($product->getMaterials() as $material) {
            if(!$material instanceof Material)
                continue;
            foreach($material->getAllMaterialNutritions() as $mNutrition) {
                if(!$mNutrition->getNutrition() instanceof Nutrition || !$mNutrition->getServeMeasurement() instanceof ServeMeasurement)
                    continue;
                $mNutritions[$mNutrition->getNutrition()->getId() . '|' . $mNutrition->getServeMeasurement()->getId() . '|' . intval($mNutrition->getQty())] = $mNutrition;
            }
        }
        return $mNutritions;
    }
    /**
     * centering the text for the iamge
     *
     * @param unknown $img
     * @param unknown $fontSize
     * @param unknown $xMax
     * @param unknown $y
     * @param unknown $str
     * @param unknown $color
     * @param string $fontFile
     */
    private static function _imagecenteredstring ( &$img, $fontSize, $xMax, $y, $str, $color, $fontFile = null ) {
        $text_box = imagettfbbox($fontSize, 0, $fontFile, $str);
        $text_width = $text_box[2]-$text_box[0];
        // 		$text_height = $text_box[7]-$text_box[1];
        $xLoc = ($xMax/2) - ($text_width/2);
        imagettftext($img, $fontSize, 0, $xLoc, $y, $color, $fontFile, $str);
    }
}