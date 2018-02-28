<?php

set_time_limit(0);
require_once __DIR__.'/config.php';
libxml_use_internal_errors(true);


try 
{
	// connect to mysql
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS, array(
	    PDO::ATTR_EMULATE_PREPARES=>false,
	    PDO::MYSQL_ATTR_DIRECT_QUERY=>false,
	    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
	));

	// parse cryptocurrency tickers via API
	$url = 'https://api.coinmarketcap.com/v1/ticker/?limit=20000';
	$json = file_get_contents($url);
	$aJson = json_decode($json, true);

	foreach ($aJson as $cryptocurrency) 
	{
		// delete previous parsing data 
		$sql = 'DELETE 
			FROM `cryptocurrency_markets` 
        	WHERE `cryptocurrency_id` = :cryptocurrency_id
        ';
		
		$sth = $pdo->prepare($sql);
		$sth->bindValue(':cryptocurrency_id', $cryptocurrency['id'], PDO::PARAM_STR);
        $sth->execute();

        // load markets page
		$url = 'https://coinmarketcap.com/currencies/'.$cryptocurrency['id'].'/#markets';
		$response = file_get_contents($url);

		// parse markets
		$dom = new DomDocument;
		$dom->loadHTML($response);
		$xpath = new DomXPath($dom);
		$domNodeListItems = $xpath->query("//table[@id='markets-table']/tbody/tr");

		for($i = 0; $i < $domNodeListItems->length; $i++) 
		{
			$xpath = new DomXPath($dom);
			$domNodeListItemTds = $xpath->query('./td', $domNodeListItems->item($i));
			
			$source = trim($domNodeListItemTds->item(1)->nodeValue);
			$pair = trim($domNodeListItemTds->item(2)->nodeValue);
			$volumeUsd = (float)($domNodeListItemTds->item(3)->childNodes->item(1)->attributes->getNamedItem("data-usd")->nodeValue);
			$volumeBtc = (float)($domNodeListItemTds->item(3)->childNodes->item(1)->attributes->getNamedItem("data-btc")->nodeValue);
			$volumeNative = (float)($domNodeListItemTds->item(3)->childNodes->item(1)->attributes->getNamedItem("data-native")->nodeValue);	// what is it ?
			$priceUsd = (float)($domNodeListItemTds->item(4)->childNodes->item(1)->attributes->getNamedItem("data-usd")->nodeValue);
			$priceBtc = (float)($domNodeListItemTds->item(4)->childNodes->item(1)->attributes->getNamedItem("data-btc")->nodeValue);
			$priceNative = (float)($domNodeListItemTds->item(4)->childNodes->item(1)->attributes->getNamedItem("data-native")->nodeValue);	
			$volumePercent = (float)($domNodeListItemTds->item(5)->childNodes->item(1)->attributes->getNamedItem("data-format-value")->nodeValue);	
			$updated = trim($domNodeListItemTds->item(6)->nodeValue);	

			/*print $source.PHP_EOL;
			print $pair.PHP_EOL;
			print $volumeUsd.PHP_EOL;
			print $volumeBtc.PHP_EOL;
			print $volumeNative.PHP_EOL;
			print $priceUsd.PHP_EOL;
			print $priceBtc.PHP_EOL;
			print $priceNative.PHP_EOL;
			print $volumePercent.PHP_EOL;
			print $updated.PHP_EOL;*/

        	// new cryptocurrency historical data
        	// insert it
			$sql = '
				INSERT INTO `cryptocurrency_markets`
				SET `cryptocurrency_id` = :cryptocurrency_id,
					`symbol` = :symbol,
					`source` = :source,
					`pair` = :pair,
					`volume_usd` = :volume_usd,
					`volume_btc` = :volume_btc,
					`volume_native` = :volume_native,
					`price_usd` = :price_usd,
					`price_btc` = :price_btc,
					`price_native` = :price_native,
					`volume_percent` = :volume_percent,
					`updated` = :updated
			';

			$sth = $pdo->prepare($sql);

			$sth->bindValue(':cryptocurrency_id', $cryptocurrency['id']);
			$sth->bindValue(':symbol', $cryptocurrency['symbol']);
			$sth->bindValue(':source', $source);
			$sth->bindValue(':pair', $pair);
			$sth->bindValue(':volume_usd', $volumeUsd);
			$sth->bindValue(':volume_btc', $volumeBtc);
			$sth->bindValue(':volume_native', $volumeNative);
			$sth->bindValue(':price_usd', $priceUsd);
			$sth->bindValue(':price_btc', $priceBtc);
			$sth->bindValue(':price_native', $priceNative);
			$sth->bindValue(':volume_percent', $volumePercent);
			$sth->bindValue(':updated', $updated);
			
			$sth->execute();	        
			
		}

		
	}

    $pdo = null;
} 
catch (PDOException $e) 
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
catch (Exception $e) 
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
