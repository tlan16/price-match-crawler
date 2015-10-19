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
	private $printedBy;
	
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
	public static function generateImg($width, $height)
	{
		$img = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($img, 0, 255, 255);
		imagefill($img, 0, 0, $white);
		
		$black = imagecolorallocate($img, 0, 0, 0);
// 		$productName = $this->getProduct()->getName();
		$productName = 'A Simple Text String';
		$baseFont = 2;
		$lineNo = 0;
		$lineHeight = 20;
		self::imagecenteredstring($img, $baseFont + 2, $width, $lineHeight * $lineNo, $productName, $black);
		$lineNo++;
		self::imagecenteredstring($img, $baseFont, $width, $lineHeight * $lineNo, 'Price', $black);
		$lineNo++;
		self::imagecenteredstring($img, $baseFont + 2, $width, $lineHeight * $lineNo, '$12.50', $black);
		$lineNo++;
		imagestring( $img, $baseFont + 2, 5, $lineHeight * $lineNo, 'Use By: 22/10/2015', $black );
		$lineNo++;
		self::imagecenteredstring($img, $baseFont, $width, $lineHeight * $lineNo, 'Keep Refrigerated', $black);
		$lineNo++;
		imagestring( $img, $baseFont + 2, 5, $lineHeight * $lineNo, 'Allergen Warning:', $black );
		$lineNo++;
		self::imagecenteredstring($img, $baseFont, $width, $lineHeight * $lineNo, 'Contain: FISH', $black);
		$lineNo++;
		imagestring( $img, $baseFont + 2, 5, $lineHeight * $lineNo, 'Ingredients:', $black );
		
		
		// Output the image
		$file = '/tmp/label_' . md5('Label' . '|' . trim(UDate::now()));
		imagejpeg($img, $file, 75);
		
		// Free up memory
		imagedestroy($img);
		return $file;
	}
	public static function imagecenteredstring ( &$img, $font, $xMax, $y, $str, $color ) {
		$textWidth = imagefontwidth( $font ) * strlen( $str );
		$xLoc = ( $xMax - 0 - $textWidth ) / 2 + 0 + $font;
		imagestring( $img, $font, $xLoc, $y, $str, $color );
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::preSave()
	 */
	public function preSave()
	{
		if(trim($this->getId()) === '') { //creating a new one
			if($this->getName() === '')
				$this->setName($this->getProduct()->getName());
			if(trim($this->getPrintedDate()) === '' || trim($this->getPrintedDate()) === trim(UDate::zeroDate()))
				$this->setPrintedDate(UDate::now());
			if(trim($this->getUseByDate()) === '' || trim($this->getUseByDate()) === trim(UDate::zeroDate()))
				$this->setUseByDate(UDate::now()->modify($this->getProduct()->getUsedByVariance()));
			if(!$this->getPrintedBy() instanceof UserAccount)
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