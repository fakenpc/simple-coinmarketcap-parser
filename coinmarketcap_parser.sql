-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 01 2018 г., 00:00
-- Версия сервера: 5.7.21-0ubuntu0.16.04.1
-- Версия PHP: 5.6.33-3+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `coinmarketcap_parser`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cryptocurrency_historical_data`
--

CREATE TABLE IF NOT EXISTS `cryptocurrency_historical_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cryptocurrency_id` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `open` double DEFAULT NULL,
  `high` double DEFAULT NULL,
  `low` double DEFAULT NULL,
  `close` double DEFAULT NULL,
  `volume` double DEFAULT NULL,
  `market_cap` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43899 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cryptocurrency_markets`
--

CREATE TABLE IF NOT EXISTS `cryptocurrency_markets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cryptocurrency_id` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `pair` varchar(255) DEFAULT NULL,
  `volume_usd` double DEFAULT NULL,
  `volume_btc` double DEFAULT NULL,
  `volume_native` double DEFAULT NULL,
  `price_usd` double DEFAULT NULL,
  `price_btc` double DEFAULT NULL,
  `price_native` double DEFAULT NULL,
  `volume_percent` double DEFAULT NULL,
  `updated` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16023 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cryptocurrency_tickers`
--

CREATE TABLE IF NOT EXISTS `cryptocurrency_tickers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cryptocurrency_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `price_usd` double DEFAULT NULL,
  `price_btc` double DEFAULT NULL,
  `24h_volume_usd` double DEFAULT NULL,
  `market_cap_usd` double DEFAULT NULL,
  `available_supply` double DEFAULT NULL,
  `total_supply` double DEFAULT NULL,
  `max_supply` double DEFAULT NULL,
  `percent_change_1h` double DEFAULT NULL,
  `percent_change_24h` double DEFAULT NULL,
  `percent_change_7d` double DEFAULT NULL,
  `last_updated` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1725 DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
