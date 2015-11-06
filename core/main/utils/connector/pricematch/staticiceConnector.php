<?php
class staticiceConnector extends pricematchConnectorAbstract {
	const BASE_URL = 'http://staticice.com.au/';
	const URL_PREFIX = 'cgi-bin/search.cgi';
	protected static $search_panel_indicators = array (
			// tr[valign="top"] could filter out all the exceptions
// 			'What are you shopping for',
// 			'advanced search',
// 			'Ads by Google',
// 			'advertisement',
// 			'Related searches:',
// 			'&nbsp;',
// 			'PriceDescription',
// 			'Download the staticICE Android or iPhone app',
// 			'Stores in bold are staticICE registered members',
// 			'staticICE. All Rights Reserved.',
// 			'Visit staticICE New Zealand',
// 			'Visit staticICE United Kingdom',
// 			'Visit staticICE United States',
// 			'top of page' 
	);
	protected static $dom_selectors= array (
			'row' => 'table tbody tr[valign="top"]',
			'price' => 'td[align="left"] a',
			'description' => 'td[valign="bottom"]',
			'subDescription' => 'font',
			'vendorLink' => 'a[href]',
			'vendorImage' => 'img',
			'vendorName' => 'b font',
			'subDescription' => 'font' 
	);
	
	public static function getPrices($productName, $debug = false) {
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
			$data = self::readUrl ( self::getUrlHeader (), self::CURL_TIMEOUT, $array, self::CURL_CUSTOM_REQUEST , array() , $debug);
			
			$rowCount = 0;
			foreach ( $data->find (self::$dom_selectors['row']) as $tr ) {
				if (($text = trim ( htmlspecialchars_decode ( $tr->plaintext ) )) === '' || self::isSearchPanel ( $text ) === true)
					continue;
				$price = 0;
				$description = $companyLink = $img = $companyName = $companyBaseUrl = $companyLocation = $updated = '';
				
				if($debug === true)
					print_r ( PHP_EOL . str_repeat ( '=', 100 ) . PHP_EOL . json_decode ( json_encode ( $tr->plaintext ), true ) . PHP_EOL . str_repeat ( '=', 100 ) . PHP_EOL );
				
				$priceEl = self::find($tr, self::$dom_selectors['price']);
				
				$price = StringUtilsAbstract::getValueFromCurrency($priceEl->plaintext);
				if(($price = doubleval($price)) <= doubleval(0))
					continue;
				
				$descriptionEl = self::find($tr, self::$dom_selectors['description']);
				
				if(get_class($descriptionEl) === self::HTML_DOM_NODE_OBJECT_NAME)
				{
					$description = trim($descriptionEl->plaintext);
					$subDescriptionEl = self::find($descriptionEl, self::$dom_selectors['subDescription']);
					if(is_object($subDescriptionEl) && get_class($subDescriptionEl) === self::HTML_DOM_NODE_OBJECT_NAME)
					{
						$subDescription = $subDescriptionEl->plaintext;
						$description = str_replace($subDescription, '', $description);
						$description = str_replace(PHP_EOL, '', $description);
						
						$linkEl = self::find($subDescriptionEl, self::$dom_selectors['vendorLink']);
						if(is_object($linkEl) && get_class($linkEl) === self::HTML_DOM_NODE_OBJECT_NAME)
						{
							$img = (count($imgEl = $linkEl->find('img')) > 0 ? ($imgEl[0]->src) : '');
							$img = ((is_object($imgEl = self::find($linkEl, self::$dom_selectors['vendorImage'])) && get_class($imgEl) === self::HTML_DOM_NODE_OBJECT_NAME) ? trim($imgEl->src) : '');
							$img = self::fillBaseUrl($img);
							
							$companyName = ((is_object($companyNameEl = self::find($linkEl, self::$dom_selectors['vendorName'])) && get_class($companyNameEl) === self::HTML_DOM_NODE_OBJECT_NAME) ? ($companyNameEl->plaintext) : '');
							$companyLink = self::fillBaseUrl($linkEl->href);
						}
						$subDescriptionArray = explode(' | ', $subDescription);
						if(count($subDescriptionArray) === 3)
						{
							foreach ($subDescriptionArray as $index => $string)
							{
								$string = trim($string);
								switch($index)
								{
									case 0:
										{
											$string = trim(str_replace($companyName, '', $string));
											preg_match('/\(([^\)]*)\)/', $string, $match);
											if(count($match) > 1)
												$companyLocation = $match[1];
											break;
										}
									case 1:
										{
											$string = 'http://' . $string;
											if (!filter_var($string, FILTER_VALIDATE_URL) === false)
												$companyBaseUrl = $string;
											break;
										}
									case 2:
										{
											if(strpos($string, 'updated:') !== false && ($tmp = trim(str_replace('updated:', '', $string))) && ($tmp = new UDate($tmp)) instanceof UDate)
												$updated = $tmp;
											break;
										}
								}
							}
						}
					}
				}
				
				$rowResult = array(
						'price' => $price,
						'description' => $description,
						'company' => $companyName,
						'company location' => $companyLocation,
						'company link' => $companyLink,
						'company base url' => $companyBaseUrl,
						'company image' => $img,
						'updated' => trim ( $updated ) 
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