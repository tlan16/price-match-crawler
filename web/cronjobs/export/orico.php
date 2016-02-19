<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

$skuList = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'orico.list');
$skuList = explode(PHP_EOL, $skuList);
$result = array();
$resultFile = __DIR__ . DIRECTORY_SEPARATOR . 'export.csv';

foreach($skuList as $sku) {
    $sku = trim($sku);
    if(!($product = Product::getBySku($sku)) instanceof Product)
        continue;
    $records = Record::getAllByCriteria('productId = ?', array($product->getId()));
    foreach($records as $record)
        $result[] = array($product->getSku(), $record->getVendor()->getName(), '$'.$record->getPrice());
}

$fp = fopen($resultFile, 'w');

foreach ($result as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

echo "Begin at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";
echo "End at MELB TIME: " . UDate::now(UDate::TIME_ZONE_MELB) . "\n";