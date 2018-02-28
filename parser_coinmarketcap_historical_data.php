<?php

set_time_limit(0);
libxml_use_internal_errors(true);
require_once __DIR__.'/config.php';

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
		$url = 'https://coinmarketcap.com/currencies/'.$cryptocurrency['id'].'/historical-data/?start=20000101&end='.date('Ymd');
		$response = file_get_contents($url);

		$dom = new DomDocument;
		$dom->loadHTML($response);
		$xpath = new DomXPath($dom);
		$domNodeListItems = $xpath->query("//div[@id='historical-data']//table/tbody/tr");

		for($i = 0; $i < $domNodeListItems->length; $i++) 
		{
			$xpath = new DomXPath($dom);
			$domNodeListItemTds = $xpath->query('./td', $domNodeListItems->item($i));
			
			$date = $domNodeListItemTds->item(0)->nodeValue;
			$date = date("Y-m-d", strtotime($date));
			$open = (float)($domNodeListItemTds->item(1)->attributes->getNamedItem("data-format-value")->nodeValue);
			$high = (float)($domNodeListItemTds->item(2)->attributes->getNamedItem("data-format-value")->nodeValue);
			$low = (float)($domNodeListItemTds->item(3)->attributes->getNamedItem("data-format-value")->nodeValue);
			$close = (float)($domNodeListItemTds->item(4)->attributes->getNamedItem("data-format-value")->nodeValue);
			$volume = (float)($domNodeListItemTds->item(5)->attributes->getNamedItem("data-format-value")->nodeValue);
			$marketCap = (float)($domNodeListItemTds->item(6)->attributes->getNamedItem("data-format-value")->nodeValue);

			/*print $date.PHP_EOL;
			print $open.PHP_EOL;*/

			$sql = 'SELECT `date`
	          FROM `cryptocurrency_historical_data` 
	          WHERE `cryptocurrency_id` = :cryptocurrency_id
	          	AND `date` = :date 
	          LIMIT 1
	        ';
			
			$sth = $pdo->prepare($sql);
			$sth->bindValue(':cryptocurrency_id', $cryptocurrency['id'], PDO::PARAM_STR);
			$sth->bindValue(':date', $date, PDO::PARAM_STR);
	        $sth->execute();
			$aCryptocurrencyHistoricalDates = $sth->fetchAll(PDO::FETCH_ASSOC);

	        if(count($aCryptocurrencyHistoricalDates) == 0) 
	        {
	        	// new cryptocurrency historical data
	        	// insert it
				$sql = '
					INSERT INTO `cryptocurrency_historical_data`
					SET `cryptocurrency_id` = :cryptocurrency_id,
						`symbol` = :symbol,
						`date` = :tdate,
						`open` = :open,
						`high` = :high,
						`low` = :low,
						`close` = :close,
						`volume` = :volume,
						`market_cap` = :market_cap
				';

				$sth = $pdo->prepare($sql);

				$sth->bindValue(':cryptocurrency_id', $cryptocurrency['id']);
				$sth->bindValue(':symbol', $cryptocurrency['symbol']);
				$sth->bindValue(':tdate', $date);
				$sth->bindValue(':open', $open);
				$sth->bindValue(':high', $high);
				$sth->bindValue(':low', $low);
				$sth->bindValue(':close', $close);
				$sth->bindValue(':volume', $volume);
				$sth->bindValue(':market_cap', $marketCap);
				
				$sth->execute();	        
			}
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
