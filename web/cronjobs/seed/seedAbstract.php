<?php
/**
 * Seed Abstract
 * 
 * @author Frank-Desktop
 *
 */
abstract class seedAbstract
{
	public static function run()
	{
		require_once dirname(__FILE__) . '/../../bootstrap.php';
		Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
		echo 'START ' . get_called_class() . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;
	}
	public static function readData($filePath, $delimiter)
	{
		$result = array();
		$data = file_get_contents($filePath);
		$data = explode($delimiter, $data);
		foreach ($data as $row)
		{
			if(($row = trim($row)) !== '')
				$result[] = $row;
		}
		return $result;
	}
}