<?php

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
	/*	print $cryptocurrency['id'].PHP_EOL;
		print $cryptocurrency['name'].PHP_EOL;
		print $cryptocurrency['symbol'].PHP_EOL;
		print $cryptocurrency['rank'].PHP_EOL;
		print $cryptocurrency['price_usd'].PHP_EOL;
		print $cryptocurrency['price_btc'].PHP_EOL;
		print $cryptocurrency['24h_volume_usd'].PHP_EOL;
		print $cryptocurrency['market_cap_usd'].PHP_EOL;
		print $cryptocurrency['available_supply'].PHP_EOL;
		print $cryptocurrency['total_supply'].PHP_EOL;
		print $cryptocurrency['max_supply'].PHP_EOL;
		print $cryptocurrency['percent_change_1h'].PHP_EOL;
		print $cryptocurrency['percent_change_24h'].PHP_EOL;
		print $cryptocurrency['percent_change_7d'].PHP_EOL;
		print $cryptocurrency['last_updated'].PHP_EOL;
		print PHP_EOL;
		*/

		$sql = 'SELECT `last_updated`
          FROM `cryptocurrency_tickers` 
          WHERE `cryptocurrency_id` = :cryptocurrency_id
          ORDER BY `last_updated` DESC
          LIMIT 1
        ';

        $sth = $pdo->prepare($sql);
		$sth->bindValue(':cryptocurrency_id', $cryptocurrency['id'], PDO::PARAM_STR);
        $sth->execute();
		$aCryptocurrencyTickers = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(count($aCryptocurrencyTickers)) 
        {
        	// already exists
        	$cryptocurrencyTicker = $aCryptocurrencyTickers[0];

        	if($cryptocurrency['last_updated'] != $cryptocurrencyTicker['last_updated'])
        	{
        		// cryptocurrency on coimarketcap is updated
        		// insert new ticker
        		insertCryptocurrencyTicker($pdo, $cryptocurrency);
        	}
        	else
        	{
        		// cryptocurrency on coimarketcap is not changed
        		// do nothing
        	}
        }
        else
        {
        	// new cryptocurrency
        	// insert new ticker
        	insertCryptocurrencyTicker($pdo, $cryptocurrency);
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

function insertCryptocurrencyTicker($pdo, $cryptocurrency)
{
	$sql = '
	INSERT INTO `cryptocurrency_tickers`
	SET `cryptocurrency_id` = :cryptocurrency_id,
		`name` = :name,
		`symbol` = :symbol,
		`rank` = :rank,
		`price_usd` = :price_usd,
		`price_btc` = :price_btc,
		`24h_volume_usd` = :24h_volume_usd,
		`market_cap_usd` = :market_cap_usd,
		`available_supply` = :available_supply,
		`total_supply` = :total_supply,
		`max_supply` = :max_supply,
		`percent_change_1h` = :percent_change_1h,
		`percent_change_24h` = :percent_change_24h,
		`percent_change_7d` = :percent_change_7d,
		`last_updated` = :last_updated
	';

	$sth = $pdo->prepare($sql);

	$sth->bindValue(':cryptocurrency_id', $cryptocurrency['id']);
	$sth->bindValue(':name', $cryptocurrency['name']);
	$sth->bindValue(':symbol', $cryptocurrency['symbol']);
	$sth->bindValue(':rank', $cryptocurrency['rank']);
	$sth->bindValue(':price_usd', $cryptocurrency['price_usd']);
	$sth->bindValue(':price_btc', $cryptocurrency['price_btc']);
	$sth->bindValue(':24h_volume_usd', $cryptocurrency['24h_volume_usd']);
	$sth->bindValue(':market_cap_usd', $cryptocurrency['market_cap_usd']);
	$sth->bindValue(':available_supply', $cryptocurrency['available_supply']);
	$sth->bindValue(':total_supply', $cryptocurrency['total_supply']);
	$sth->bindValue(':max_supply', $cryptocurrency['max_supply']);
	$sth->bindValue(':percent_change_1h', $cryptocurrency['percent_change_1h']);
	$sth->bindValue(':percent_change_24h', $cryptocurrency['percent_change_24h']);
	$sth->bindValue(':percent_change_7d', $cryptocurrency['percent_change_7d']);
	$sth->bindValue(':last_updated', $cryptocurrency['last_updated']);

	return $sth->execute();
}