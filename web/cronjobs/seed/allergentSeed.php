<?php
require_once dirname(__FILE__) . '/seedAbstract.php';
class allergentSeed extends seedAbstract
{
	public static function run()
	{
		parent::run();
		$class = "Allergent";
		$filePath = dirname(__FILE__) . '/allergent.list';
		$data = self::readData($filePath, "\n");
		foreach ($data as $name)
		{
			if(($name = trim($name)) === '')
				continue;
			$obj = $class::create($name);
			echo $class . '[' . $obj->getId() . '] with name "' . $name . '" has been created/updated' . PHP_EOL;
		}
	}
}

allergentSeed::run();