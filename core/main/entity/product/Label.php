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
		return new UDate(trim($this->useByDate));
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
		return LabelPrinter::generateImg($this, $width, $height);
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
			if(trim($this->getUseByDate()) === '' || trim($this->getUseByDate()) === trim(UDate::zeroDate())) {
				$useByDate = UDate::now();
				if( ($variance = intval($this->getProduct()->getUsedByVariance())) > 0)
					$useByDate->modify('+' . $variance + ' day');
				$this->setUseByDate($useByDate);
			}
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