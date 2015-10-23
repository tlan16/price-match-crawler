<?php
abstract class LabelPrinter
{
    public static function generateHTML(Label $label, $width, $height)
    {
        $bottomBoxHeight = 270;
        $topBoxHeight = 320;
        $html = "";
        $html .= "<div style='margin-left: auto; margin-right: auto; margin-top: 12px;width: " . $width . "px; height: " . $height . "px'>";
            $html .= "<div style='text-align: right; font-size: 9px;'>";
            $html .= $label->getVersionNo();
            $html .= "</div>";
            $html .= "<div style='text-align: center; font-size: 16px; font-weight: bold;'>";
                $html .= $label->getProduct()->getName();
            $html .= "</div>";

            $html .= "<div style='text-align: center; font-size: 12px; margin: 5px 0;'>Price</div>";
            $html .= "<div style='text-align: center; font-size: 16px; font-weight: bold;'>";
                $html .= StringUtilsAbstract::getCurrency($label->getPrintedPrice());
            $html .= "</div>";

            $html .= "<div style='text-align: center;'>";
                $qrImgFile = self::_qrCodeImage('http://www.sushiandco.com.au/');
                $html .= '<img src="data:image/png;base64,' . base64_encode(file_get_contents($qrImgFile)) . '" />';
                unlink($qrImgFile);
            $html .= "</div>";

            $html .= "<div style='text-align: left; font-size: 16px; font-weight: bold;'>";
                $html .= 'Use By: &nbsp;&nbsp;' . $label->getUseByDate()->format('d / m / Y');
            $html .= "</div>";
            $html .= "<div style='text-align: center; font-size: 12px;'>Keep Refrigerated</div>";

            $html .= "<div style='text-align: left; font-size: 16px; font-weight: bold;'>Allergent Warning: </div>";
            $html .= "<div style='text-align: center; font-size: 10px;'>";
                $alleNames = self::_getAllergentNames($label->getProduct());
                $alleText = "Contains: " . implode(', ', $alleNames);
                $html .= count($alleNames) > 0  ? $alleText : '&nbsp;';
            $html .= "</div>";
            if(ceil(strlen($alleText) / ($width * (88/300))) > 1) //(88/300) is the right ratio tested with width is at 300;
                $topBoxHeight = $topBoxHeight + 16;

            $html .= "<div style='text-align: left; font-size: 16px; font-weight: bold;'>Ingredients: </div>";
            $html .= "<div style='text-align: center; font-size: 10px; max-height: " . ($mheight = $height -  $topBoxHeight - $bottomBoxHeight) . "px; min-height: " . $mheight . "px;'>";
                $ingredientsTxtArr = self::_getIngredientNames($label->getProduct());
                $html .= count($ingredientsTxtArr) > 0  ? (implode(', ', $ingredientsTxtArr)) : '&nbsp;';
            $html .= "</div>";

            $html .= "<div style='background: transparent;height: " . $bottomBoxHeight . "px; vertical-align: bottom; display: table-cell; width: " . $width . "px'>";
                $html .= "<div style='background: transparent; text-align: center; font-size: 16px; font-weight: bold; vertical-align: bottom;'>Nutrition Panel: </div>";
                $html .= "<div style='background: transparent; text-align: left; margin-bottom: 15px;'>";
                    $mNutritions = self::_getMaterialNutrions($label->getProduct());
                    foreach($mNutritions as $mNutrition) {
                        $html .= "<span style='text-align: left; width: 80%; display: inline-block;'>";
                            $html .= $mNutrition->getNutrition()->getName() . ' (' . $mNutrition->getServeMeasurement()->getName() . ')';
                        $html .= "</span>";
                        $html .= "<span style='text-align: left; width: 15%; display: inline-block;'>";
                            $html .= $mNutrition->getQty();
                        $html .= "</span>";
                    }
                $html .= "</div>";

                $html .= "<div style='background: transparent;text-align: center; vertical-align: bottom;'>";
                    $barcodeImgFile = PhpBarcode::getBarcodeImg($label->getProduct()->getBarcode(), true);
                    $html .= '<img src="data:image/png;base64,' . base64_encode(file_get_contents($barcodeImgFile)) . '" />';
                    unlink($barcodeImgFile);
                $html .= "</div>";
            $html .= "</div>";

            $html .= "<hr />";
        $html .= "</div>";
        return $html;
    }
    /**
     * Generate the img
     *
     * @param Label   $label
     * @param int     $width
     * @param int     $height
     * @return string
     */
    public static function generateImg(Label $label, $width, $height)
    {
        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        $lineNo = 2;
        $lineHeight = 24;
        $startX = 10;
        $baseFont = 9;
        $black = imagecolorallocate($img, 0, 0, 0);
        $fontFile = dirname(__FILE__) . '/arial.ttf';
        $versionNo = $label->getVersionNo();
        $dimensions = imagettfbbox(7, 0, $fontFile, $versionNo);
        imagettftext($img, 7, 0, $width - abs($dimensions[4] - $dimensions[0]) - $startX, 18, $black, $fontFile, $versionNo);
        $startY = abs($dimensions[7] - $dimensions[1]) - 10;

        self::_imagecenteredstring($img, $baseFont + 5, $width, $startY + $lineHeight * ($lineNo++), $label->getProduct()->getName(), $black, $fontFile);
        self::_imagecenteredstring($img, $baseFont, $width, $startY + $lineHeight * ($lineNo++), 'Price', $black, $fontFile);
        self::_imagecenteredstring($img, $baseFont + 5, $width, $startY + $lineHeight * ($lineNo), StringUtilsAbstract::getCurrency($label->getPrintedPrice()), $black, $fontFile);
        $qrImgFile = self::_qrCodeImage('http://www.sushiandco.com.au/');
        $qrCodeImg = imagecreatefrompng($qrImgFile);
        list($qrCodeImg_width, $qrCodeImg_height) = getimagesize($qrImgFile);
        $yPos = $startY + $lineHeight * $lineNo;
        imagecopy($img, $qrCodeImg, ($width - $qrCodeImg_width)/2, $yPos, 0, 0, $qrCodeImg_width, $qrCodeImg_height);
        $startY = $yPos + $qrCodeImg_height + 10;
        $lineNo = 0;
        imagettftext($img, $baseFont + 5, 0, $startX, $startY + $lineHeight * ($lineNo++), $black, $fontFile, 'Use By: ' . $label->getUseByDate()->format('d/m/Y'));
        self::_imagecenteredstring($img, $baseFont +2, $width, $startY + $lineHeight * ($lineNo++), 'Keep Refrigerated', $black, $fontFile);
        imagettftext($img, $baseFont + 5, 0, $startX, $startY + $lineHeight * ($lineNo++), $black, $fontFile, 'Allergent Warning:');
        $alleNames = self::_getAllergentNames($label->getProduct());
        $alleTexts = wordwrap('Contains: ' . implode(', ', $alleNames), 35, "\n");
        foreach(explode("\n", $alleTexts) as $index => $textLine) {
	        self::_imagecenteredstring($img, $baseFont, $width, $startY + $lineHeight * ($lineNo++) - ($index === 0 ? 5: 15), $textLine, $black, $fontFile);
        }

        imagettftext($img, $baseFont + 5, 0, $startX, $startY + $lineHeight * ($lineNo++), $black, $fontFile, 'Ingredients:');
        $ingredientsTxtArr = self::_getIngredientNames($label->getProduct());
        $ingreText = wordwrap(implode(', ', $ingredientsTxtArr), 35, "\n");
        foreach(explode("\n", $ingreText) as $index => $textLine) {
	        self::_imagecenteredstring($img, $baseFont, $width, $startY + $lineHeight * ($lineNo++) - ($index === 0 ? 5: 15), $textLine, $black, $fontFile);
        }


        //start from the bottom now
        //draw the barcode
        $barcodeImg = PhpBarcode::getBarcodeImg($label->getProduct()->getBarcode());
        $barcodeImg_width = imagesx ($barcodeImg);
        $barcodeImg_height = imagesy ($barcodeImg);
        $bottomBase_y = $height - $barcodeImg_height - 1;
        imagecopy($img, $barcodeImg, ($width/2 - $barcodeImg_width/2), $bottomBase_y, 0, 0, $barcodeImg_width, $barcodeImg_height);

        $mNutritions = self::_getMaterialNutrions($label->getProduct());
        $startY = $bottomBase_y - (count($mNutritions) + 1) * $lineHeight;
        $lineNo = 0;
        self::_imagecenteredstring($img, $baseFont + 5, $width, $startY + $lineHeight * ($lineNo++), 'Nutrition Panel', $black, $fontFile);
        foreach($mNutritions as $mNutrition) {
            imagettftext($img, $baseFont, 0, $startX, $startY + $lineHeight * $lineNo, $black, $fontFile, $mNutrition->getNutrition()->getName() . ' (' . $mNutrition->getServeMeasurement()->getName() . ')');
            imagettftext($img, $baseFont, 0, $startX + ($width * 0.85), $startY + $lineHeight * ($lineNo++), $black, $fontFile, $mNutrition->getQty());
        }

        // Output the image
        $file = '/tmp/label_' . md5('Label' . '|' . trim(UDate::now()) . (Core::getUser() instanceof UserAccount ? Core::getUser()->getId() : rand(0, 1000)));
        imagejpeg($img, $file, 100);

        // Free up memory
        imagedestroy($img);
        unlink($qrImgFile);
        return $file;
    }
    private static function _qrCodeImage($text) {
        $file = '/tmp/label_qr_' . md5($text . trim(UDate::now()) . (Core::getUser() instanceof UserAccount ? Core::getUser()->getId() : rand(0, 1000)));
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