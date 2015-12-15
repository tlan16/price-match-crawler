<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

$username = SystemSettings::getByType(SystemSettings::TYPE_MAGENTO_B2B_USERNAME)->getValue();
$password = SystemSettings::getByType(SystemSettings::TYPE_MAGENTO_B2B_PASSWORD)->getValue();
$baseurl = "http://app.budgetpc.com.au/api/";

magentoB2BProductConnector::importProducts($baseurl, $username, $password);