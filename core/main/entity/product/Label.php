<?php

class Label extends BaseEntityAbstract
{
	/**
	 * @var String
	 */
	private $name;

	/**
	 * @var UDate
	 */
	private $printedDate;

	/**
	 * @var UDate
	 */
	private $useByDate;

	/**
	 * @var UserAccount
	 */
	protected $printedBy;

	/**
	 * @var Double
	 */
	private $printedPrice;

	/**
	 * @var Integer
	 */
	private $versionNo;
	/**
	 * The product
	 *
	 * @var Product
	 */
	protected $product;

	public function getProduct()
	{
		$this->loadManyToOne('product');
		return $this->product;
	}

	public function setProduct(Product $product)
	{
		$this->product = $product;
		return $this;
	}
	/**
	 * Getter for the name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getPrintedDate()
	{
		return $this->printedDate;
	}

	public function setPrintedDate($date)
	{
		if(($date = UDate::validateDate($date)) === false)
			throw new Exception('Invalid Use By Date ['.$date.'] provided');

		$this->printedDate = trim($date);
		return $this;
	}

	public function getUseByDate()
	{
		return $this->useByDate;
	}

	public function setUseByDate($date)
	{
		if(($date = UDate::validateDate($date)) === false)
			throw new Exception('Invalid Use By Date ['.$date.'] provided');

		$this->useByDate = trim($date);
		return $this;
	}

	public function getPrintedBy()
	{
		$this->loadManyToOne('printedBy');
		return $this->printedBy;
	}

	public function setPrintedBy(UserAccount $user)
	{
		$this->printedBy = $user;
		return $this;
	}

	public function getVersionNo()
	{
		return $this->versionNo;
	}

	public function setVersionNo($versionNo)
	{
		$this->versionNo = $versionNo;
		return $this;
	}

	public function getPrintedPrice()
	{
		return $this->printedPrice;
	}

	public function setPrintedPrice($printedPrice)
	{
		$printedPrice = StringUtilsAbstract::getValueFromCurrency($printedPrice);

		if(!is_numeric($printedPrice))
			throw new Exception('A valid price must be provided to set the Printed Price');

		$this->printedPrice = $printedPrice;
		return $this;
	}
	public function generateImg($width, $height)
	{
		$img = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($img, 0, 255, 255);
		imagefill($img, 0, 0, $white);

		$black = imagecolorallocate($img, 0, 0, 0);
		$productName = $this->getProduct()->getName();
		$baseFont = 9;
		$lineNo = 1;
		$lineHeight = 24;
		$fontFile = dirname(__FILE__) . '/../../../3rdParty/arial.ttf';
		$this->_imagecenteredstring($img, $baseFont + 4, $width, $lineHeight * $lineNo, $productName, $black, $fontFile);
		$lineNo++;
		$this->_imagecenteredstring($img, $baseFont, $width, $lineHeight * $lineNo, 'Price', $black, $fontFile);
		$lineNo++;
		$this->_imagecenteredstring($img, $baseFont + 2, $width, $lineHeight * $lineNo, '$12.50', $black, $fontFile);
		$lineNo++;
		imagettftext($img, $baseFont + 2, 0, 5, $lineHeight * $lineNo, $black, $fontFile, 'Use By: 22/10/2015');
		$lineNo++;
		$this->_imagecenteredstring($img, $baseFont, $width, $lineHeight * $lineNo, 'Keep Refrigerated', $black, $fontFile);
		$lineNo++;
		imagettftext($img, $baseFont + 2, 0, 5, $lineHeight * $lineNo, $black, $fontFile, 'Allergen Warning:');
		$lineNo++;
		$this->_imagecenteredstring($img, $baseFont, $width, $lineHeight * $lineNo, 'Contain: FISH', $black, $fontFile);
		$lineNo++;
		imagettftext($img, $baseFont + 2, 0, 5, $lineHeight * $lineNo, $black, $fontFile, 'Ingredients:');
		$ingredientsTxtArr = array();
		foreach($this->getProduct()->getMaterials() as $material) {
		    foreach($material->getIngredients() as $ingredient)
		        $ingredientsTxtArr[] = $ingredient->getName();
		}
		$lineNo++;
		$this->_imagecenteredstring($img, $baseFont, $width, $lineHeight * $lineNo, implode(', ', $ingredientsTxtArr), $black, $fontFile);

		// Output the image
		$file = '/tmp/label_' . md5('Label' . '|' . trim(UDate::now()));
		imagejpeg($img, $file, 75);

		// Free up memory
		imagedestroy($img);
		return $file;
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
	private function _imagecenteredstring ( &$img, $fontSize, $xMax, $y, $str, $color, $fontFile = null ) {
		$textWidth = imagefontwidth( $fontSize ) * strlen( $str );
		$xLoc = ( $xMax - 0 - $textWidth ) / 2;
		imagettftext($img, $fontSize, 0, $xLoc, $y, $color, $fontFile, $str);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::preSave()
	 */
	public function preSave()
	{
		if(trim($this->getId()) === '') { //creating a new one
			if(trim($this->getName()) === '')
				$this->setName($this->getProduct()->getName());
			if(trim($this->getPrintedDate()) === '' || trim($this->getPrintedDate()) === trim(UDate::zeroDate()))
				$this->setPrintedDate(UDate::now());
			if(trim($this->getUseByDate()) === '' || trim($this->getUseByDate()) === trim(UDate::zeroDate()))
				$this->setUseByDate(UDate::now()->modify($this->getProduct()->getUsedByVariance()));
			if(!$this->printedBy instanceof UserAccount)
				$this->setPrintedBy(Core::getUser());
			if(trim($this->getVersionNo()) === '')
				$this->setVersionNo($this->getProduct()->getLabelVersionNo());
			if(trim($this->getPrintedPrice()) === '')
				$this->setPrintedPrice($this->getProduct()->getUnitPrice());
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'lbl');
		DaoMap::setStringType('name', 'varchar', 100);
		DaoMap::setDateType('printedDate');
		DaoMap::setDateType('useByDate');
		DaoMap::setManyToOne('printedBy', 'UserAccount');
		DaoMap::setStringType('versionNo', 'varchar', 10);
		DaoMap::setIntType('printedPrice', 'double', '10,4');
		DaoMap::setManyToOne('product', 'Product', 'pro');
		parent::__loadDaoMap();

		DaoMap::createIndex('useByDate');
		DaoMap::createIndex('name');
		DaoMap::createIndex('versionNo');
		DaoMap::commit();
	}
	/**
	 * Creating a label
	 * @param Product     $product
	 * @param UDate       $printedDate
	 * @param UserAccount $printedBy
	 * @return BaseEntityAbstract
	 */
	public static function create(Product $product, UDate $printedDate = null, UserAccount $printedBy = null)
	{
		$label = new Label();
		$label->setProduct($product);
		if($printedDate instanceof UDate)
			$label->setPrintedDate($printedDate);
		if($printedBy instanceof UserAccount)
			$label->setPrintedBy($printedBy);
		return $label->save();
	}

}