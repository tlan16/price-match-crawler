<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

$username = SystemSettings::getByType(SystemSettings::TYPE_MAGENTO_B2B_USERNAME)->getValue();
$password = SystemSettings::getByType(SystemSettings::TYPE_MAGENTO_B2B_PASSWORD)->getValue();
$baseurl = "http://app.budgetpc.com.au/api/";

echo "Begin at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
magentoB2BProductConnector::importProducts($baseurl, $username, $password);
echo "End at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";