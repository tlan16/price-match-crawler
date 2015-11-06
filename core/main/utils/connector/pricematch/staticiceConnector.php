<?php
class staticiceConnector extends pricematchConnectorAbstract {
	const BASE_URL = 'http://staticice.com.au/';
	const URL_PREFIX = 'cgi-bin/search.cgi';
	protected static $search_panel_indicators = array (
			// tr[valign="top"] could filter out all the exceptions
			
// 			'What are you shopping for'
// 			,'advanced search'
// 			,'Ads by Google'
// 			,'advertisement'
// 			,'Related searches:'
// 			,'&nbsp;'
// 			,'PriceDescription' 
// 			,'Download the staticICE Android or iPhone app' 
// 			,'Stores in bold are staticICE registered members'
// 			,'staticICE. All Rights Reserved.'
// 			,'Visit staticICE New Zealand'
// 			,'Visit staticICE United Kingdom'
// 			,'Visit staticICE United States'
// 			,'top of page'
// 			,''
// 			,''
// 			,''
	);
	
	public static function getPrices($productName, $debug = true) {
		if (($productName = trim ( $productName )) === '')
			throw new Exception ( "Product name cannit be empty" );
		
		$outputArray = array ();
		
		try {
			$array = array (
					'start' => 1,
					'links' => PHP_INT_MAX,
					'showadres' => 1,
					'q' => $productName 
			);
			$data = self::readUrl ( self::getUrlHeader (), self::CURL_TIMEOUT, $array, self::CURL_CUSTOM_REQUEST );
			
			if ($debug === true)
				print_r ( PHP_EOL . PHP_EOL . __FUNCTION__ . PHP_EOL . PHP_EOL );
			
			$rowCount = 0;
			foreach ( $data->find ( 'table tbody tr[valign="top"] ' ) as $tr ) {
				if (($text = trim ( htmlspecialchars_decode ( $tr->plaintext ) )) === '' || self::isSearchPanel ( $text ) === true)
					continue;
				if($debug === true)
					print_r ( PHP_EOL . str_repeat ( '=', 100 ) . PHP_EOL . json_decode ( json_encode ( $tr->plaintext ), true ) . PHP_EOL . str_repeat ( '=', 100 ) . PHP_EOL );
				
				$priceEl = $tr->find ('td[align="left"] a');
				if(count($priceEl) === 0)
					continue;
				$priceEl = $priceEl[0];
				$price = StringUtilsAbstract::getValueFromCurrency($priceEl->plaintext);
				if(($price = doubleval($price)) <= doubleval(0))
					continue;
				
				$descriptionEl = $tr->find ('td[valign="bottom"]');
				$description = $companyLink = $img = $companyName = '';
				if(count($descriptionEl) > 0)
				{
					$descriptionEl = $descriptionEl[0];
					$description = $descriptionEl->plaintext;
					$subDescriptionEl = $descriptionEl->find ('font');
					if(count($subDescriptionEl) > 0)
					{
						$subDescriptionEl = $subDescriptionEl[0];
						$subDescription = $subDescriptionEl->plaintext;
						$description = str_replace($subDescription, '', $description);
						$description = str_replace(PHP_EOL, '', $description);
						
						$linkEl = $subDescriptionEl->find ('a[href]');
						if(count($linkEl) > 0)
						{
							$linkEl = $linkEl[0];
							$img = (count($imgEl = $linkEl->find('img')) > 0 ? ($imgEl[0]->src) : '');
							$img = (strpos($img, self::BASE_URL) === false ? self::BASE_URL . $img : $img);
							$companyName = (count($companyNameEl = $linkEl->find('b font')) > 0 ? ($companyNameEl[0]->plaintext) : '');
							$companyLink = $linkEl->href;
						}
					}
				}
				
				$rowResult = array(
					'price' => $price
					,'description' => $description
					,'company' => $companyName
					,'company link' => $companyLink
					,'company image' => $img
				);
				
				if($debug === true)
					print_r($rowResult);
				$outputArray[] = $rowResult;
				$rowCount++;
			}
		} catch ( Exception $ex ) {
			throw $ex;
		}
		
		return $outputArray;
	}
}