-- MySQL dump 10.13  Distrib 5.5.30, for Linux (x86_64)
--
-- Host: localhost    Database: astpp
-- ------------------------------------------------------
-- Server version	5.5.30-cll

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` char(20) NOT NULL,
  `reseller_id` int(4) DEFAULT NULL COMMENT 'Resellers account id',
  `pricelist_id` int(4) NOT NULL COMMENT 'pricelist table id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:inactive,1:active',
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sweep_id` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Sweep list table id',
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `credit_limit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `posttoexternal` tinyint(1) NOT NULL DEFAULT '0',
  `balance` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `password` char(20) NOT NULL DEFAULT '',
  `first_name` char(40) NOT NULL DEFAULT '',
  `last_name` char(40) NOT NULL DEFAULT '',
  `company_name` char(40) NOT NULL DEFAULT '',
  `address_1` char(80) NOT NULL DEFAULT '',
  `address_2` char(80) NOT NULL DEFAULT '',
  `postal_code` char(12) NOT NULL DEFAULT '',
  `province` char(20) NOT NULL DEFAULT '',
  `city` char(20) NOT NULL DEFAULT '',
  `country_id` int(3) NOT NULL DEFAULT '0' COMMENT 'Country table id',
  `telephone_1` char(20) NOT NULL DEFAULT '',
  `telephone_2` char(20) NOT NULL DEFAULT '',
  `email` char(80) NOT NULL DEFAULT '',
  `language_id` int(3) NOT NULL DEFAULT '0' COMMENT 'language table id',
  `currency_id` int(3) NOT NULL DEFAULT '0' COMMENT 'Currency table id',
  `maxchannels` int(4) NOT NULL DEFAULT '1',
  `dialed_modify` mediumtext NOT NULL,
  `type` tinyint(1) DEFAULT '0',
  `timezone_id` int(3) NOT NULL DEFAULT '0' COMMENT 'timezone table id',
  `inuse` int(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=deleted',
  PRIMARY KEY (`id`),
  KEY `number` (`number`),
  KEY `pricelist` (`pricelist_id`),
  KEY `reseller` (`reseller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'5163162651',0,0,1,0.00000,2,'2013-05-24 12:33:31',0.00000,0,0.00000,'5163162651','First name','Last name','Company','','','','','',203,'','','info@astpp.org',1,139,1,'',3,9,0,0);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounts_callerid`
--

DROP TABLE IF EXISTS `accounts_callerid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts_callerid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `callerid_name` varchar(30) NOT NULL DEFAULT '',
  `callerid_number` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 inactive 1 active',
  PRIMARY KEY (`id`),
  KEY `accountid` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts_callerid`
--

LOCK TABLES `accounts_callerid` WRITE;
/*!40000 ALTER TABLE `accounts_callerid` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts_callerid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` mediumtext NOT NULL,
  `user` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ani_map`
--

DROP TABLE IF EXISTS `ani_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ani_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` char(20) NOT NULL DEFAULT '',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `context` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `account` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ani_map`
--

LOCK TABLES `ani_map` WRITE;
/*!40000 ALTER TABLE `ani_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `ani_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `block_patterns`
--

DROP TABLE IF EXISTS `block_patterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `block_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `blocked_patterns` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `accountid` (`accountid`),
  KEY `blocked_patterns` (`blocked_patterns`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `block_patterns`
--

LOCK TABLES `block_patterns` WRITE;
/*!40000 ALTER TABLE `block_patterns` DISABLE KEYS */;
/*!40000 ALTER TABLE `block_patterns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callingcardbrands`
--

DROP TABLE IF EXISTS `callingcardbrands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callingcardbrands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL DEFAULT '',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(3) NOT NULL DEFAULT '0',
  `pricelist_id` int(4) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `validfordays` char(4) NOT NULL DEFAULT '',
  `pin` char(15) NOT NULL DEFAULT '',
  `maint_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `maint_fee_days` int(4) NOT NULL DEFAULT '0',
  `disconnect_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `minute_fee_minutes` int(4) NOT NULL DEFAULT '0',
  `minute_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `min_length_minutes` int(4) NOT NULL DEFAULT '0',
  `min_length_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `reseller` (`reseller_id`),
  KEY `pricelist` (`pricelist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callingcardbrands`
--

LOCK TABLES `callingcardbrands` WRITE;
/*!40000 ALTER TABLE `callingcardbrands` DISABLE KEYS */;
/*!40000 ALTER TABLE `callingcardbrands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callingcardcdrs`
--

DROP TABLE IF EXISTS `callingcardcdrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callingcardcdrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `callingcard_id` int(11) NOT NULL DEFAULT '0',
  `clid` char(20) NOT NULL DEFAULT '',
  `destination` char(20) NOT NULL DEFAULT '',
  `disposition` char(20) NOT NULL DEFAULT '',
  `callstart` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seconds` int(11) NOT NULL DEFAULT '0',
  `debit` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `credit` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `uniqueid` varchar(60) NOT NULL DEFAULT '',
  `notes` char(80) NOT NULL DEFAULT '',
  `pricelist_id` int(4) NOT NULL DEFAULT '0',
  `pattern` char(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `cardnumber` (`callingcard_id`),
  KEY `pricelist_id` (`pricelist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callingcardcdrs`
--

LOCK TABLES `callingcardcdrs` WRITE;
/*!40000 ALTER TABLE `callingcardcdrs` DISABLE KEYS */;
/*!40000 ALTER TABLE `callingcardcdrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callingcards`
--

DROP TABLE IF EXISTS `callingcards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callingcards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cardnumber` char(20) NOT NULL DEFAULT '',
  `language_id` int(3) NOT NULL DEFAULT '0',
  `value` double(10,5) NOT NULL DEFAULT '0.00000',
  `used` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `firstused` datetime DEFAULT NULL,
  `expiry` datetime DEFAULT NULL,
  `validfordays` char(4) NOT NULL DEFAULT '',
  `inuse` int(4) NOT NULL DEFAULT '0',
  `pin` char(20) NOT NULL DEFAULT '',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `maint_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `maint_fee_days` int(4) NOT NULL DEFAULT '0',
  `maint_day` int(4) NOT NULL DEFAULT '0',
  `disconnect_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `minute_fee_minutes` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `minute_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `min_length_minutes` int(4) NOT NULL DEFAULT '0',
  `min_length_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `timeused` int(11) NOT NULL DEFAULT '0',
  `invoice` char(20) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `brand` (`brand_id`),
  KEY `cardnumber` (`cardnumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callingcards`
--

LOCK TABLES `callingcards` WRITE;
/*!40000 ALTER TABLE `callingcards` DISABLE KEYS */;
/*!40000 ALTER TABLE `callingcards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callingcards_callerid`
--

DROP TABLE IF EXISTS `callingcards_callerid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callingcards_callerid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `callingcard_id` int(11) NOT NULL DEFAULT '0',
  `callerid_name` varchar(30) NOT NULL DEFAULT '',
  `callerid_number` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 inactive 1 active',
  PRIMARY KEY (`id`),
  KEY `callingcard_id` (`callingcard_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callingcards_callerid`
--

LOCK TABLES `callingcards_callerid` WRITE;
/*!40000 ALTER TABLE `callingcards_callerid` DISABLE KEYS */;
/*!40000 ALTER TABLE `callingcards_callerid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callshops`
--

DROP TABLE IF EXISTS `callshops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callshops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `osc_dbname` varchar(50) NOT NULL DEFAULT '',
  `osc_dbpass` varchar(50) NOT NULL DEFAULT '',
  `osc_dbuser` varchar(50) NOT NULL DEFAULT '',
  `osc_dbhost` varchar(50) NOT NULL DEFAULT '',
  `osc_site` varchar(50) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callshops`
--

LOCK TABLES `callshops` WRITE;
/*!40000 ALTER TABLE `callshops` DISABLE KEYS */;
/*!40000 ALTER TABLE `callshops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cdrs`
--

DROP TABLE IF EXISTS `cdrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cdrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(60) NOT NULL DEFAULT '',
  `accountid` int(11) DEFAULT '0',
  `callerid` char(20) DEFAULT '',
  `callednum` varchar(20) NOT NULL DEFAULT '',
  `billseconds` int(6) NOT NULL DEFAULT '0',
  `trunk_id` int(4) NOT NULL DEFAULT '0',
  `trunkip` varchar(15) NOT NULL DEFAULT '',
  `callerip` varchar(15) NOT NULL DEFAULT '',
  `disposition` varchar(45) NOT NULL DEFAULT '',
  `callstart` timestamp NULL DEFAULT NULL,
  `debit` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `credit` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `notes` char(80) DEFAULT '',
  `provider_id` int(11) DEFAULT '0',
  `cost` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `pricelist_id` int(4) NOT NULL DEFAULT '0',
  `pattern` char(15) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `invoiceid` int(11) NOT NULL DEFAULT '0',
  `calltype` enum('STANDARD','DID') NOT NULL,
  `accountname` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `cardnum` (`accountid`),
  KEY `provider` (`provider_id`),
  KEY `trunk` (`trunk_id`),
  KEY `uniqueid` (`uniqueid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cdrs`
--

LOCK TABLES `cdrs` WRITE;
/*!40000 ALTER TABLE `cdrs` DISABLE KEYS */;
INSERT INTO `cdrs` VALUES (1,'',2147483647,'','Account Setup',0,0,'','','','2013-05-24 12:33:31',0.000000,0.000000,0,'',0,0.000000,0,'',1,0,'STANDARD','');
/*!40000 ALTER TABLE `cdrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `charge_to_account`
--

DROP TABLE IF EXISTS `charge_to_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `charge_to_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charge_id` int(4) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `charge_id` (`charge_id`),
  KEY `accountid` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `charge_to_account`
--

LOCK TABLES `charge_to_account` WRITE;
/*!40000 ALTER TABLE `charge_to_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `charge_to_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `charges`
--

DROP TABLE IF EXISTS `charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pricelist_id` int(4) NOT NULL DEFAULT '0' COMMENT 'pricelist table id',
  `description` varchar(80) NOT NULL DEFAULT '',
  `charge` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sweep_id` int(1) NOT NULL DEFAULT '0' COMMENT 'sweeplist table id',
  `reseller_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Accounts table id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:inactive,1:active',
  PRIMARY KEY (`id`),
  KEY `pricelist` (`pricelist_id`),
  KEY `sweep_id` (`sweep_id`),
  KEY `reseller_id` (`reseller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `charges`
--

LOCK TABLES `charges` WRITE;
/*!40000 ALTER TABLE `charges` DISABLE KEYS */;
/*!40000 ALTER TABLE `charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(150) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ci_sessions`
--

LOCK TABLES `ci_sessions` WRITE;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `counters`
--

DROP TABLE IF EXISTS `counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `counters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(4) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `seconds` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`),
  KEY `accountid` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `counters`
--

LOCK TABLES `counters` WRITE;
/*!40000 ALTER TABLE `counters` DISABLE KEYS */;
/*!40000 ALTER TABLE `counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countrycode`
--

DROP TABLE IF EXISTS `countrycode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countrycode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countrycode`
--

LOCK TABLES `countrycode` WRITE;
/*!40000 ALTER TABLE `countrycode` DISABLE KEYS */;
INSERT INTO `countrycode` VALUES (1,'Afghanistan'),(2,'Alaska'),(3,'Albania'),(4,'Algeria'),(5,'AmericanSamoa'),(6,'Andorra'),(7,'Angola'),(8,'Antarctica'),(9,'Argentina'),(10,'Armenia'),(11,'Aruba'),(12,'Ascension'),(13,'Australia'),(14,'Austria'),(15,'Azerbaijan'),(16,'Bahrain'),(17,'Bangladesh'),(18,'Belarus'),(19,'Belgium'),(20,'Belize'),(21,'Benin'),(22,'Bhutan'),(23,'Bolivia'),(24,'Bosnia & Herzegovina'),(25,'Botswana'),(26,'Brazil'),(27,'Brunei Darussalam'),(28,'Bulgaria'),(29,'Burkina Faso'),(30,'Burundi'),(31,'Cambodia'),(32,'Cameroon'),(33,'Canada'),(34,'Cape Verde Islands'),(35,'Central African Republic'),(36,'Chad'),(37,'Chile'),(38,'China'),(39,'Colombia'),(40,'Comoros'),(41,'Congo'),(42,'Cook Islands'),(43,'Costa Rica'),(44,'Croatia'),(45,'Cuba'),(46,'Cuba Guantanamo Bay'),(47,'Cyprus'),(48,'Czech Republic'),(49,'Denmark'),(50,'Diego Garcia'),(51,'Djibouti'),(52,'Dominican Republic'),(53,'East Timor'),(54,'Ecuador'),(55,'Egypt'),(56,'El Salvador'),(57,'Equatorial Guinea'),(58,'Eritrea'),(59,'Estonia'),(60,'Ethiopia'),(61,'Faroe Islands'),(62,'Fiji Islands'),(63,'Finland'),(64,'France'),(65,'French Guiana'),(66,'French Polynesia'),(67,'Gabonese Republic'),(68,'Gambia'),(69,'Georgia'),(70,'Germany'),(71,'Ghana'),(72,'Gibraltar'),(73,'Greece'),(74,'Greenland'),(75,'Guadeloupe'),(76,'Guam'),(77,'Guatemala'),(78,'Guinea'),(79,'Guyana'),(80,'Haiti'),(81,'Honduras'),(82,'Hong Kong'),(83,'Hungary'),(84,'Iceland'),(85,'India'),(86,'Indonesia'),(87,'Iran'),(88,'Iraq'),(89,'Ireland'),(90,'Israel'),(91,'Italy'),(92,'Jamaica'),(93,'Japan'),(94,'Jordan'),(95,'Kazakstan'),(96,'Kenya'),(97,'Kiribati'),(98,'Kuwait'),(99,'Kyrgyz Republic'),(100,'Laos'),(101,'Latvia'),(102,'Lebanon'),(103,'Lesotho'),(104,'Liberia'),(105,'Libya'),(106,'Liechtenstein'),(107,'Lithuania'),(108,'Luxembourg'),(109,'Macao'),(110,'Madagascar'),(111,'Malawi'),(112,'Malaysia'),(113,'Maldives'),(114,'Mali Republic'),(115,'Malta'),(116,'Marshall Islands'),(117,'Martinique'),(118,'Mauritania'),(119,'Mauritius'),(120,'MayotteIsland'),(121,'Mexico'),(122,'Midway Islands'),(123,'Moldova'),(124,'Monaco'),(125,'Mongolia'),(126,'Morocco'),(127,'Mozambique'),(128,'Myanmar'),(129,'Namibia'),(130,'Nauru'),(131,'Nepal'),(132,'Netherlands'),(133,'Netherlands Antilles'),(134,'New Caledonia'),(135,'New Zealand'),(136,'Nicaragua'),(137,'Niger'),(138,'Nigeria'),(139,'Niue'),(140,'Norfolk Island'),(141,'North Korea'),(142,'Norway'),(143,'Oman'),(144,'Pakistan'),(145,'Palau'),(146,'Palestinian Settlements'),(147,'Panama'),(148,'PapuaNew Guinea'),(149,'Paraguay'),(150,'Peru'),(151,'Philippines'),(152,'Poland'),(153,'Portugal'),(154,'Puerto Rico'),(155,'Qatar'),(156,'RÃ©unionIsland'),(157,'Romania'),(158,'Russia'),(159,'Rwandese Republic'),(160,'San Marino'),(161,'Saudi Arabia'),(162,'SÃ£o TomÃ© and Principe'),(163,'Senegal '),(164,'Serbia and Montenegro'),(165,'Seychelles Republic'),(166,'Sierra Leone'),(167,'Singapore'),(168,'Slovak Republic'),(169,'Slovenia'),(170,'Solomon Islands'),(171,'Somali Democratic Republic'),(172,'South Africa'),(173,'South Korea'),(174,'Spain'),(175,'Sri Lanka'),(176,'St Kitts - Nevis'),(177,'St. Helena'),(178,'St. Lucia'),(179,'St. Pierre & Miquelon'),(180,'St. Vincent & Grenadines'),(181,'Sudan'),(182,'Suriname'),(183,'Swaziland'),(184,'Sweden'),(185,'Switzerland'),(186,'Syria'),(187,'Taiwan'),(188,'Tajikistan'),(189,'Tanzania'),(190,'Thailand'),(191,'Togolese Republic'),(192,'Tokelau'),(193,'Tonga Islands'),(194,'Trinidad & Tobago'),(195,'Tunisia'),(196,'Turkey'),(197,'Turkmenistan'),(198,'Tuvalu'),(199,'Uganda'),(200,'Ukraine'),(201,'United Arab Emirates'),(202,'United Kingdom'),(203,'United States of America'),(204,'Uruguay'),(205,'Uzbekistan'),(206,'Vanuatu'),(207,'Venezuela'),(208,'Vietnam'),(209,'Wake Island'),(210,'Wallisand Futuna Islands'),(211,'Western Samoa'),(212,'Yemen'),(213,'Zambia'),(214,'Zimbabwe');
/*!40000 ALTER TABLE `countrycode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` varchar(3) NOT NULL DEFAULT '',
  `currencyname` varchar(40) NOT NULL DEFAULT '',
  `currencyrate` decimal(10,5) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `currency` (`currency`),
  KEY `currencyrate` (`currencyrate`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency`
--

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
INSERT INTO `currency` VALUES (1,'ALL','Albanian Lek',109.42500,'2013-05-16 20:09:18'),(2,'DZD','Algerian Dinar',79.36500,'2013-05-16 20:09:18'),(3,'XAL','Aluminium Ounces',0.00000,'2013-03-23 09:03:23'),(4,'ARS','Argentine Peso',5.23400,'2013-05-16 20:09:18'),(5,'AWG','Aruba Florin',1.79030,'2013-05-16 20:09:18'),(6,'AUD','Australian Dollar',1.01890,'2013-05-16 20:10:33'),(7,'BSD','Bahamian Dollar',1.00000,'2013-03-23 09:03:23'),(8,'BHD','Bahraini Dinar',0.37700,'2013-05-16 20:09:18'),(9,'BDT','Bangladesh Taka',77.81000,'2013-05-16 20:09:18'),(10,'BBD','Barbados Dollar',2.00000,'2013-03-23 09:03:23'),(11,'BYR','Belarus Ruble',8685.00000,'2013-05-16 20:09:18'),(12,'BZD','Belize Dollar',2.02000,'2013-05-16 20:09:18'),(13,'BMD','Bermuda Dollar',1.00000,'2013-03-23 09:03:23'),(14,'BTN','Bhutan Ngultrum',54.80500,'2013-05-16 20:09:18'),(15,'BOB','Bolivian Boliviano',6.91000,'2013-03-23 09:03:23'),(16,'BRL','Brazilian Real',2.02780,'2013-05-16 20:10:33'),(17,'GBP','British Pound',0.65480,'2013-05-16 20:09:18'),(18,'BND','Brunei Dollar',1.25240,'2013-05-16 20:09:18'),(19,'BGN','Bulgarian Lev',1.52230,'2013-05-16 20:09:18'),(20,'BIF','Burundi Franc',1576.00000,'2013-05-16 20:09:19'),(21,'KHR','Cambodia Riel',3998.50000,'2013-05-16 20:09:19'),(22,'CAD','Canadian Dollar',1.01910,'2013-05-16 20:10:33'),(23,'KYD','Cayman Islands Dollar',0.00000,'2013-03-23 09:03:23'),(24,'XOF','CFA Franc (BCEAO)',508.95000,'2013-05-16 20:09:19'),(25,'XAF','CFA Franc (BEAC)',509.38230,'2013-05-16 20:09:19'),(26,'CLP','Chilean Peso',480.03000,'2013-05-16 20:09:19'),(27,'CNY','Chinese Yuan',6.15390,'2013-05-16 20:09:19'),(28,'COP','Colombian Peso',1839.60000,'2013-05-16 20:09:19'),(29,'KMF','Comoros Franc',382.03670,'2013-05-16 20:09:19'),(30,'XCP','Copper Ounces',0.30380,'2013-05-16 20:09:19'),(31,'CRC','Costa Rica Colon',502.20000,'2013-05-16 20:09:19'),(32,'HRK','Croatian Kuna',5.87630,'2013-05-16 20:10:33'),(33,'CUP','Cuban Peso',1.00000,'2013-03-23 09:03:23'),(34,'CYP','Cyprus Pound',0.00000,'2013-03-23 09:03:23'),(35,'CZK','Czech Koruna',20.17950,'2013-05-16 20:10:33'),(36,'DKK','Danish Krone',5.78580,'2013-05-16 20:10:33'),(37,'DJF','Dijibouti Franc',178.76000,'2013-05-16 20:09:19'),(38,'DOP','Dominican Peso',41.12500,'2013-05-16 20:09:19'),(39,'XCD','East Caribbean Dollar',2.70000,'2013-03-23 09:03:23'),(40,'ECS','Ecuador Sucre',0.00000,'2013-03-23 09:03:23'),(41,'EGP','Egyptian Pound',6.98090,'2013-05-16 20:10:33'),(42,'SVC','El Salvador Colon',8.74750,'2013-03-23 09:03:23'),(43,'ERN','Eritrea Nakfa',0.00000,'2013-03-23 09:03:23'),(44,'EEK','Estonian Kroon',0.00000,'2013-03-23 09:03:23'),(45,'ETB','Ethiopian Birr',18.62550,'2013-05-16 20:09:19'),(46,'EUR','Euro',0.77630,'2013-05-16 20:09:19'),(47,'FKP','Falkland Islands Pound',0.65500,'2013-05-16 20:09:19'),(48,'GMD','Gambian Dalasi',33.00000,'2013-05-16 20:09:19'),(49,'GHC','Ghanian Cedi',0.00000,'2013-03-23 09:03:23'),(50,'GIP','Gibraltar Pound',0.65500,'2013-05-16 20:09:19'),(51,'XAU','Gold Ounces',0.00070,'2013-05-16 20:09:19'),(52,'GTQ','Guatemala Quetzal',7.79750,'2013-05-16 20:09:19'),(53,'GNF','Guinea Franc',7061.00000,'2013-05-16 20:09:19'),(54,'HTG','Haiti Gourde',42.50000,'2013-05-16 20:09:20'),(55,'HNL','Honduras Lempira',19.03000,'2013-05-16 20:09:20'),(56,'HKD','Hong Kong Dollar',7.76370,'2013-05-16 20:10:33'),(57,'HUF','Hungarian ForINT',226.41500,'2013-05-16 20:10:33'),(58,'ISK','Iceland Krona',123.33000,'2013-05-16 20:09:20'),(59,'INR','Indian Rupee',54.78550,'2013-05-16 20:09:20'),(60,'IDR','Indonesian Rupiah',9751.00000,'2013-05-16 20:09:20'),(61,'IRR','Iran Rial',12283.00000,'2013-05-16 20:09:20'),(62,'ILS','Israeli Shekel',3.64060,'2013-05-16 20:10:33'),(63,'JMD','Jamaican Dollar',98.45000,'2013-05-16 20:09:20'),(64,'JPY','Japanese Yen',102.25500,'2013-05-16 20:10:33'),(65,'JOD','Jordanian Dinar',0.70830,'2013-05-16 20:09:20'),(66,'KZT','Kazakhstan Tenge',151.12500,'2013-05-16 20:09:20'),(67,'KES','Kenyan Shilling',83.68000,'2013-05-16 20:09:20'),(68,'KRW','Korean Won',1115.20000,'2013-05-16 20:10:33'),(69,'KWD','Kuwaiti Dinar',0.28630,'2013-05-16 20:09:20'),(70,'LAK','Lao Kip',7670.29980,'2013-05-16 20:09:20'),(71,'LVL','Latvian Lat',0.54280,'2013-05-16 20:10:34'),(72,'LBP','Lebanese Pound',1504.00000,'2013-05-16 20:09:20'),(73,'LSL','Lesotho Loti',9.33500,'2013-05-16 20:09:20'),(74,'LYD','Libyan Dinar',1.26600,'2013-05-16 20:09:20'),(75,'LTL','Lithuanian Lita',2.67980,'2013-05-16 20:10:34'),(76,'MOP','Macau Pataca',7.99660,'2013-05-16 20:09:20'),(77,'MKD','Macedonian Denar',47.37500,'2013-05-16 20:09:20'),(78,'MGF','Malagasy Franc',0.00000,'2013-03-23 09:03:23'),(79,'MWK','Malawi Kwacha',354.54000,'2013-05-16 20:09:20'),(80,'MYR','Malaysian Ringgit',3.01150,'2013-05-16 20:09:21'),(81,'MVR','Maldives Rufiyaa',15.38000,'2013-05-16 20:09:21'),(82,'MTL','Maltese Lira',0.00000,'2013-03-23 09:03:23'),(83,'MRO','Mauritania Ougulya',298.25000,'2013-05-16 20:09:21'),(84,'MUR','Mauritius Rupee',31.30000,'2013-05-16 20:09:21'),(85,'MXN','Mexican Peso',12.28800,'2013-05-16 20:09:21'),(86,'MDL','Moldovan Leu',12.33000,'2013-05-16 20:09:21'),(87,'MNT','Mongolian Tugrik',1418.50000,'2013-05-16 20:09:21'),(88,'MAD','Moroccan Dirham',8.60810,'2013-05-16 20:10:34'),(89,'MZM','Mozambique Metical',0.00000,'2013-03-23 09:03:23'),(90,'NAD','Namibian Dollar',9.31500,'2013-05-16 20:09:21'),(91,'NPR','Nepalese Rupee',87.23000,'2013-05-16 20:09:21'),(92,'ANG','Neth Antilles Guilder',1.79000,'2013-03-23 09:03:23'),(93,'TRY','New Turkish Lira',1.82500,'2013-05-16 20:10:34'),(94,'NZD','New Zealand Dollar',1.22680,'2013-05-16 20:10:34'),(95,'NIO','Nicaragua Cordoba',24.78500,'2013-05-16 20:09:21'),(96,'NGN','Nigerian Naira',158.13000,'2013-05-16 20:09:21'),(97,'NOK','Norwegian Krone',5.83380,'2013-05-16 20:10:34'),(98,'OMR','Omani Rial',0.38500,'2013-03-23 09:03:23'),(99,'XPF','Pacific Franc',92.56000,'2013-05-16 20:09:21'),(100,'PKR','Pakistani Rupee',98.55000,'2013-05-16 20:09:21'),(101,'XPD','Palladium Ounces',0.00150,'2013-05-16 20:09:21'),(102,'PAB','Panama Balboa',1.00000,'2013-03-23 09:03:23'),(103,'PGK','Papua New Guinea Kina',2.20670,'2013-05-16 20:09:21'),(104,'PYG','Paraguayan Guarani',4172.50000,'2013-05-16 20:09:21'),(105,'PEN','Peruvian Nuevo Sol',2.63200,'2013-05-16 20:09:21'),(106,'PHP','Philippine Peso',41.23000,'2013-05-16 20:09:21'),(107,'XPT','Platinum Ounces',0.00060,'2013-05-16 20:09:21'),(108,'PLN','Polish Zloty',3.24920,'2013-05-16 20:10:34'),(109,'QAR','Qatar Rial',3.64080,'2013-05-16 20:09:21'),(110,'ROL','Romanian Leu',0.00000,'2013-03-23 09:03:23'),(111,'RON','Romanian New Leu',3.36770,'2013-05-16 20:09:22'),(112,'RUB','Russian Rouble',31.36750,'2013-05-16 20:09:22'),(113,'RWF','Rwanda Franc',639.89250,'2013-05-16 20:09:22'),(114,'WST','Samoa Tala',2.29130,'2013-05-16 20:09:22'),(115,'STD','Sao Tome Dobra',19015.00000,'2013-05-16 20:09:22'),(116,'SAR','Saudi Arabian Riyal',3.75030,'2013-03-23 09:03:23'),(117,'SCR','Seychelles Rupee',11.73000,'2013-05-16 20:09:22'),(118,'SLL','Sierra Leone Leone',4327.50000,'2013-05-16 20:09:22'),(119,'XAG','Silver Ounces',0.04420,'2013-05-16 20:09:22'),(120,'SGD','Singapore Dollar',1.25200,'2013-05-16 20:10:34'),(121,'SKK','Slovak Koruna',0.00000,'2013-03-23 09:03:23'),(122,'SIT','Slovenian Tolar',0.00000,'2013-03-23 09:03:23'),(123,'SOS','Somali Shilling',1494.50000,'2013-05-16 20:09:22'),(124,'ZAR','South African Rand',9.32720,'2013-05-16 20:10:34'),(125,'LKR','Sri Lanka Rupee',125.70000,'2013-05-16 20:09:22'),(126,'SHP','St Helena Pound',0.65500,'2013-05-16 20:09:22'),(127,'SDD','Sudanese Dinar',0.00000,'2013-03-23 09:03:23'),(128,'SRG','Surinam Guilder',0.00000,'2013-03-23 09:03:23'),(129,'SZL','Swaziland Lilageni',9.32300,'2013-05-16 20:10:34'),(130,'SEK','Swedish Krona',6.67200,'2013-05-16 20:09:22'),(131,'CHF','Swiss Franc',0.96460,'2013-05-16 20:10:34'),(132,'SYP','Syrian Pound',97.55000,'2013-05-16 20:09:22'),(133,'TWD','Taiwan Dollar',30.02900,'2013-05-16 20:09:22'),(134,'TZS','Tanzanian Shilling',1626.50000,'2013-05-16 20:09:22'),(135,'THB','Thai Baht',29.71000,'2013-05-16 20:10:34'),(136,'TOP','Tonga Paanga',1.74110,'2013-05-16 20:09:22'),(137,'TTD','Trinidad&Tobago Dollar',6.42000,'2013-05-16 20:09:22'),(138,'TND','Tunisian Dinar',1.65350,'2013-05-16 20:09:22'),(139,'USD','U.S. Dollar',1.00000,'2013-03-23 09:03:23'),(140,'AED','UAE Dirham',3.67310,'2013-05-16 20:09:22'),(141,'UGX','Ugandan Shilling',2570.00000,'2013-05-16 20:09:22'),(142,'UAH','Ukraine Hryvnia',8.13950,'2013-05-16 20:09:22'),(143,'UYU','Uruguayan New Peso',18.85000,'2013-05-16 20:09:22'),(144,'VUV','Vanuatu Vatu',93.00000,'2013-05-16 20:09:23'),(145,'VEB','Venezuelan Bolivar',0.00000,'2013-03-23 09:03:23'),(146,'VND','Vietnam Dong',20925.00000,'2013-05-16 20:09:23'),(147,'YER','Yemen Riyal',214.85500,'2013-05-16 20:09:23'),(148,'ZMK','Zambian Kwacha',5195.00000,'2013-05-16 20:09:23'),(149,'ZWD','Zimbabwe Dollar',0.00000,'2013-03-23 09:03:23'),(150,'GYD','Guyana Dollar',204.40000,'2013-05-16 20:09:23');
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `customer_cdrs`
--

DROP TABLE IF EXISTS `customer_cdrs`;
/*!50001 DROP VIEW IF EXISTS `customer_cdrs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `customer_cdrs` (
  `accountid` tinyint NOT NULL,
  `number` tinyint NOT NULL,
  `reseller_id` tinyint NOT NULL,
  `cdr_id` tinyint NOT NULL,
  `uniqueid` tinyint NOT NULL,
  `callerid` tinyint NOT NULL,
  `callednum` tinyint NOT NULL,
  `billseconds` tinyint NOT NULL,
  `trunk_id` tinyint NOT NULL,
  `disposition` tinyint NOT NULL,
  `callstart` tinyint NOT NULL,
  `debit` tinyint NOT NULL,
  `credit` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `notes` tinyint NOT NULL,
  `provider_id` tinyint NOT NULL,
  `cost` tinyint NOT NULL,
  `pricelist_id` tinyint NOT NULL,
  `pattern` tinyint NOT NULL,
  `calltype` tinyint NOT NULL,
  `trunkip` tinyint NOT NULL,
  `callerip` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `customer_log`
--

DROP TABLE IF EXISTS `customer_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL COMMENT 'account table id',
  `pricelist_id` int(11) NOT NULL DEFAULT '0' COMMENT 'pricelist table id',
  `description` varchar(255) NOT NULL COMMENT 'charges description',
  `charge_id` int(11) NOT NULL COMMENT 'charge table id',
  `package_id` int(11) NOT NULL COMMENT 'Package table id',
  `debit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `charge_type` enum('did_charge','post_charge','monthly_charge','periodic_charge','account_refill') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_log`
--

LOCK TABLES `customer_log` WRITE;
/*!40000 ALTER TABLE `customer_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `default_templates`
--

DROP TABLE IF EXISTS `default_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `default_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `subject` varchar(500) NOT NULL,
  `template` mediumtext NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `default_templates`
--

LOCK TABLES `default_templates` WRITE;
/*!40000 ALTER TABLE `default_templates` DISABLE KEYS */;
INSERT INTO `default_templates` VALUES (1,'voip_account_refilled','Account refilled succesfully','Hi <NAME>,\r\n\r\nYour VOIP account with #YOUR COMPANY NAME# has been refilled.\r\n\r\nFor information please visit #YOUR COMPANY WEBSITE# or contact our support department at #YOUR EMAIL ADDRESS#\r\n\r\nThanks,\r\n\r\n#YOUR COMPANY NAME#\r\n\r\nsupport team\r\n','2013-05-25 06:28:54'),(2,'voip_reactivate_account','Account reactivate','\r\n	HI <NAME>,\r\n\r\n	Your VOIP account with #YOUR COMPANY NAME# has been reactivated.</p>\r\n\r\n	For information please visit #YOUR COMPANY WEBSITE# or contact our support department at #YOUR COMPANY EMAIL#\r\n\r\n	Thanks,</p>\r\n\r\n	The #YOUR COMPANY NAME#\r\n\r\n	support team\r\n','2013-05-25 06:29:51'),(3,'email_add_user','user added successfully','Hi <NAME>\r\nYour VOIP account with DEMO has been added.\r\n\r\nYour Account Number is :><NUMBER> \r\nYour Password is : <password> \r\n\r\nFor information please visit Test or contact our support department at Test@gmail.com \r\n	\r\nThanks, \r\nThe Test \r\nsupport team','2013-05-25 06:31:27'),(4,'add_sip_device','Sip Device add','Hi <NAME>\r\n\r\n\r\nA new device has been enabled on your account. Here is the necessary configuration information. #YOUR COMPANY NAME# Configuration Info -------- In sip.conf: [#YOUR COMPANY NAME#-in] type=user username=#YOUR COMPANY NAME#<br />\r\n\r\n\r\nThanks,\r\n\r\n#YOUR COMPANY NAME#\r\n\r\nsupport team\r\n','2013-05-25 06:32:40'),(6,'email_remove_user','remove user account','Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour VOIP Termination with #YOUR COMPANY NAME# has been removed\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team','0000-00-00 00:00:00'),(7,'email_calling_card','New Calling Card','Hi <NAME>\r\n\r\n\r\nYou have added a callingcard in the amount of <BALANCE>  cents. \r\n\r\nCard Number <CARDNUMBER>\r\nPin: <PIN>\r\n\r\nThanks,\r\n\r\nThe #YOUR COMPANY NAME# \r\n\r\nsales team\r\n','2013-05-25 06:36:07'),(8,'email_add_did','did added to your account','Hi <NAME>,\r\n\r\n\r\nYour DID with #YOUR COMPANY NAME# has been added\r\n\r\nThe number is: <NUMBER>\r\n\r\nFor information please visit #YOUR COMPANY WEBSITE# or contact our support department at #YOUR COMPANY EMAIL#\r\n\r\n\r\nThanks,\r\n\r\nThe #YOUR COMPANY NAME#\r\n\r\nsupport team\r\n','2013-05-25 06:37:21'),(9,'email_remove_did','Remove dids','\r\nHi <NAME>,\r\n\r\nYour DID with #YOUR COMPANY NAME# has been removed ,\r\n\r\nThe number was: <NUMBER>\r\n\r\nFor information please visit #YOUR COMPANY WEBSITE# or contact our support department at #YOUR COMPANY EMAIL#</p>\r\n\r\nThanks,\r\n\r\nThe #YOUR COMPANY NAME#\r\n\r\nsupport team\r\n','2013-05-25 06:38:30'),(10,'email_new_invoice','mail for new invoice','Hi <NAME>,\r\n\r\n\r\nInvoice amount of <AMOUNT> has been added to your account.\r\n\r\nFor information please visit #YOUR COMPANY WEBSITE# or contact our support department at #YOUR COMPANY EMAIL#</p>\r\n\r\nThanks,\r\n\r\nThe #YOUR COMPANY NAME#\r\n\r\nsupport team\r\n','2013-05-25 06:39:57'),(11,'email_low_balance','Low balance','Hi <NAME>\r\n\r\n\r\nYour VOIP account with #YOUR COMPANY NAME# has a balance of &lt;BALANCE&gt;.\r\n\r\nPlease visit our website to refill your account to ensure uninterrupted service. For information please visit #YOUR COMPANY WEBSITE# or contact our support department at #YOUR COMPANY EMAIL#</p>\r\n\r\n\r\nThanks,\r\n\r\nThe #YOUR COMPANY NAME#</p>\r\n\r\nsupport team</p>\r\n','2013-05-25 06:40:50');
/*!40000 ALTER TABLE `default_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dids`
--

DROP TABLE IF EXISTS `dids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` char(40) NOT NULL DEFAULT '',
  `accountid` int(11) DEFAULT '0' COMMENT 'Accounts table id',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `monthlycost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `cost` double(10,5) NOT NULL DEFAULT '0.00000',
  `inc` int(4) NOT NULL,
  `extensions` char(180) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `provider_id` int(11) NOT NULL DEFAULT '0',
  `country_id` int(3) NOT NULL DEFAULT '0',
  `province` varchar(20) NOT NULL DEFAULT '',
  `city` varchar(20) NOT NULL DEFAULT '',
  `prorate` int(1) NOT NULL DEFAULT '0',
  `setup` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `limittime` int(1) NOT NULL DEFAULT '1',
  `disconnectionfee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `variables` mediumtext NOT NULL,
  `options` varchar(40) DEFAULT NULL,
  `maxchannels` int(4) NOT NULL DEFAULT '0',
  `chargeonallocation` int(1) NOT NULL DEFAULT '1',
  `allocation_bill_status` int(1) NOT NULL DEFAULT '0',
  `dial_as` char(40) NOT NULL DEFAULT '',
  `call_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'call type',
  `inuse` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `account` (`accountid`),
  KEY `number` (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dids`
--

LOCK TABLES `dids` WRITE;
/*!40000 ALTER TABLE `dids` DISABLE KEYS */;
/*!40000 ALTER TABLE `dids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `freeswich_servers`
--

DROP TABLE IF EXISTS `freeswich_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `freeswich_servers` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `freeswitch_host` varchar(100) NOT NULL,
  `freeswitch_password` varchar(50) NOT NULL,
  `freeswitch_port` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freeswich_servers`
--

LOCK TABLES `freeswich_servers` WRITE;
/*!40000 ALTER TABLE `freeswich_servers` DISABLE KEYS */;
INSERT INTO `freeswich_servers` VALUES (1,'127.0.0.1','ClueCon','8021');
/*!40000 ALTER TABLE `freeswich_servers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateways`
--

DROP TABLE IF EXISTS `gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gateways` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `sip_profile_id` int(4) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL DEFAULT '',
  `gateway_data` text NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accountid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateways`
--

LOCK TABLES `gateways` WRITE;
/*!40000 ALTER TABLE `gateways` DISABLE KEYS */;
INSERT INTO `gateways` VALUES (1,1,'Yourgateway','{\"username\":\"username\",\"password\":\"password\",\"proxy\":\"192.168.1.10\",\"register\":\"false\",\"caller-id-in-from\":\"true\"}','2013-05-24 12:38:28','0000-00-00 00:00:00',0);
/*!40000 ALTER TABLE `gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_conf`
--

DROP TABLE IF EXISTS `invoice_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `address` varchar(300) NOT NULL,
  `city` varchar(20) NOT NULL,
  `province` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `fax` varchar(20) NOT NULL,
  `emailaddress` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_conf`
--

LOCK TABLES `invoice_conf` WRITE;
/*!40000 ALTER TABLE `invoice_conf` DISABLE KEYS */;
INSERT INTO `invoice_conf` VALUES (1,-1,'Company name','Address','City','Province','Country','Zipcode','Telephone','Fax','Email address','Website');
/*!40000 ALTER TABLE `invoice_conf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `invoice_list_view`
--

DROP TABLE IF EXISTS `invoice_list_view`;
/*!50001 DROP VIEW IF EXISTS `invoice_list_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `invoice_list_view` (
  `invoiceid` tinyint NOT NULL,
  `accountid` tinyint NOT NULL,
  `date` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `value` tinyint NOT NULL,
  `class` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:inactive,1:active',
  `external_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `accountid` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices_total`
--

DROP TABLE IF EXISTS `invoices_total`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices_total` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoices_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `text` varchar(150) NOT NULL DEFAULT '',
  `value` decimal(10,5) NOT NULL,
  `class` varchar(32) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_id` (`invoices_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices_total`
--

LOCK TABLES `invoices_total` WRITE;
/*!40000 ALTER TABLE `invoices_total` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices_total` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_map`
--

DROP TABLE IF EXISTS `ip_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL DEFAULT '',
  `accountid` int(11) NOT NULL DEFAULT '0' COMMENT 'Accounts table id',
  `pricelist_id` int(4) NOT NULL DEFAULT '0',
  `prefix` varchar(20) NOT NULL DEFAULT '',
  `context` varchar(20) NOT NULL DEFAULT 'default',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `account` (`accountid`),
  KEY `ip` (`ip`),
  KEY `prefix` (`prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_map`
--

LOCK TABLES `ip_map` WRITE;
/*!40000 ALTER TABLE `ip_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) NOT NULL,
  `languagename` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language`
--

LOCK TABLES `language` WRITE;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` VALUES (1,'en','English',1),(2,'fr','French',1),(3,'de','German',1);
/*!40000 ALTER TABLE `language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_modules`
--

DROP TABLE IF EXISTS `menu_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_modules` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `menu_label` varchar(25) NOT NULL,
  `module_name` varchar(25) NOT NULL,
  `module_url` varchar(100) NOT NULL,
  `menu_title` varchar(20) NOT NULL,
  `menu_image` varchar(25) NOT NULL,
  `menu_subtitle` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_modules`
--

LOCK TABLES `menu_modules` WRITE;
/*!40000 ALTER TABLE `menu_modules` DISABLE KEYS */;
INSERT INTO `menu_modules` VALUES (1,'Customer List','customer','accounts/customer_list/','Global Accounts','ListAccounts.png','User Accounts'),(2,'Reseller List','reseller','accounts/reseller_list/','Global Accounts','reseller.png','User Accounts'),(3,'Provider List','provider','accounts/provider_list/','Global Accounts','ListAccounts.png','Provider Accounts'),(4,'Admin List','admin','accounts/admin_list/','Global Accounts','ListAccounts.png','System Accounts'),(5,'Subadmin List','subadmin','accounts/subadmin_list/','Global Accounts','ListAccounts.png','System Accounts'),(6,'Callshop List','callshop','accounts/callshop_list/','Global Accounts','ListCallshop.png','User Accounts'),(7,'Periodic Charges','periodiccharges','charges/periodiccharges/','Accounting','PeriodicCharges.png','Subscriptions'),(8,'Invoice List','invoice','invoices/invoice_list/','Accounting','InvoiceList.png','Manage Invoice'),(9,'Invoice Configuration','invoice','invoices/invoice_conf/','Accounting','InvoiceConf.png','Manage Invoice'),(10,'List Cards','callingcards','callingcards/callingcards_list/','Services','ListCards.png','Calling Cards'),(11,'CC Brands','brands','callingcards/brands/','Services','CCBand.png','Calling Cards'),(12,'Calling Card CDRs','callingcards','callingcards/callingcards_cdrs/','Services','CallingCardCDR\'s.png','Calling Cards'),(13,'Manage DIDs','did','did/did_list/','DIDs','ManageDIDs.png','Manage DID'),(14,'Trunks','trunk','trunk/trunk_list/','Routing','Trunks.png','Providers'),(15,'Termination Rates','terminationrates','rates/terminationrates_list/','Routing','OutboundRoutes.png','Providers'),(16,'Rate Group','price','pricing/price_list/','Routing','pricelist.png','Clients'),(17,'Origination Rates','origination','rates/origination_list/','Routing','Routes.png','Clients'),(18,'Packages','package','package/package_list/','Routing','packages.png','Clients'),(19,'Customer Reports','customerReport','reports/customerReport/','Reports','cdr.png','Call Detail Reports'),(20,'Live Call Report','livecall','freeswitch/livecall_report/','Reports','cdr.png','Switch Reports'),(21,'Reseller Report','resellerReport','reports/resellerReport/','Reports','cdr.png','Call Detail Reports'),(22,'Provider Report','providerReport','reports/providerReport/','Reports','cdr.png','Call Detail Reports'),(24,'Trunk Stats','trunkstats','statistics/trunkstats/','Reports','TrunkStats.png','Switch Reports'),(25,'Freeswitch SIP Devices','fssipdevices','freeswitch/fssipdevices/','System Configuration','Devices.png','Switch Config'),(26,'Configuration','configuration','systems/configuration/','System Configuration','Configurations.png','System'),(27,'Taxes','taxes','taxes/taxes_list/','Accounting','AccountTaxes.png','Manage Taxes'),(28,'Email Template','template','systems/template/','System Configuration','TemplateManagement.png','System'),(29,'Opensips devices','opensips','opensips/opensips_list/','System Configuration','OpensipDevices.png','Opensips'),(30,'Dispatcher list','dispatcher','opensips/dispatcher_list/','System Configuration','Dispatcher.png','Opensips'),(31,'Invoices','user','user/user_invoice_list/','Manage Invoice','ListAccounts.png','Manage Invoice'),(32,'DIDs','user','user/user_didlist/','Manage DIDs','ManageDIDs.png','Manage DIDs'),(33,'ANI MAP','user','user/user_animap_list/','Manage ANI','Providers.png','ANI MAP'),(34,'CDRs Reports','user','user/user_cdrs_report/','Reports','cdr.png','Reports'),(35,'Payment Reports','user','user/user_payment_report/','Reports','PaymentReport.png','Reports'),(36,'SIP Devices','user','user/user_sipdevices/','SIP Devices','freeswitch.png','Freeswitch Devices'),(37,'Rates','user','user/user_rates_list/','Rates','OutboundRoutes','Rates'),(38,'Reseller Report','resellerReport','reports/reseller_summery_Report/','Reports','cdr.png','Summary Reports'),(39,'Provider Report','resellerReport','reports/provider_summery_Report/','Reports','cdr.png','Summary Reports'),(40,'Payment Report','resellerReport','reports/customer_paymentreport/','Reports','PaymentReport.png','Payment Reports'),(41,'Gateways','fsgateway','freeswitch/fsgateway/','Routing','Gateway.png','Providers'),(42,'Sip Profile','fssipprofile','freeswitch/fssipprofile/','Routing','SipProfiles.png','Providers'),(43,'Freeswitch Server','fsserver','freeswitch/fsserver_list/','System Configuration','freeswitch.png','Switch Config'),(44,'Package Usage Report','package','package/package_counter/','Routing','Counters.png','Clients'),(45,'User Report','userReport','reports/userReport/','Reports','cdr.png','Summary Reports');
/*!40000 ALTER TABLE `menu_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outbound_routes`
--

DROP TABLE IF EXISTS `outbound_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outbound_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern` char(15) DEFAULT NULL,
  `comment` char(80) NOT NULL DEFAULT '',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(4) NOT NULL DEFAULT '0',
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `trunk_id` int(4) NOT NULL DEFAULT '0',
  `inc` int(4) NOT NULL,
  `strip` char(40) NOT NULL DEFAULT '',
  `prepend` char(40) NOT NULL DEFAULT '',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `trunk` (`trunk_id`),
  KEY `pattern` (`pattern`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outbound_routes`
--

LOCK TABLES `outbound_routes` WRITE;
/*!40000 ALTER TABLE `outbound_routes` DISABLE KEYS */;
INSERT INTO `outbound_routes` VALUES (1,'^1.*','USA',0.00000,0,0.10000,1,60,'','',0,0,1);
/*!40000 ALTER TABLE `outbound_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `package_patterns`
--

DROP TABLE IF EXISTS `package_patterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(4) NOT NULL,
  `patterns` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`),
  KEY `patterns` (`patterns`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `package_patterns`
--

LOCK TABLES `package_patterns` WRITE;
/*!40000 ALTER TABLE `package_patterns` DISABLE KEYS */;
/*!40000 ALTER TABLE `package_patterns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` char(20) NOT NULL DEFAULT '0',
  `pricelist_id` int(4) NOT NULL DEFAULT '0' COMMENT 'pricelist table id',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `reseller_id` int(11) DEFAULT '0' COMMENT 'Accoun',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pricelist` (`pricelist_id`),
  KEY `reseller` (`reseller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `payment_mode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 for system payment',
  `type` varchar(11) NOT NULL DEFAULT 'SYSTEM',
  `payment_by` int(11) NOT NULL DEFAULT '0' COMMENT 'accountid by recharge done',
  `notes` mediumtext,
  `reference` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accountid` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_load_modules_conf`
--

DROP TABLE IF EXISTS `post_load_modules_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_load_modules_conf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(64) NOT NULL,
  `load_module` tinyint(1) NOT NULL DEFAULT '1',
  `priority` int(10) unsigned NOT NULL DEFAULT '1000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mod` (`module_name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_load_modules_conf`
--

LOCK TABLES `post_load_modules_conf` WRITE;
/*!40000 ALTER TABLE `post_load_modules_conf` DISABLE KEYS */;
INSERT INTO `post_load_modules_conf` VALUES (1,'mod_sofia',1,2000),(2,'mod_xml_cdr',1,1000),(3,'mod_commands',1,1000),(4,'mod_dialplan_xml',1,150),(5,'mod_g723_1',1,500),(6,'mod_g729',1,500),(7,'mod_g722',1,500),(8,'mod_amr',1,500),(9,'mod_event_socket',1,100),(10,'mod_dptools',1,1500),(11,'mod_perl',0,1600),(12,'mod_db',1,1000),(13,'mod_hash',1,1000),(14,'mod_console',1,1000);
/*!40000 ALTER TABLE `post_load_modules_conf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_load_switch_conf`
--

DROP TABLE IF EXISTS `post_load_switch_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_load_switch_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(255) NOT NULL,
  `param_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_load_switch_conf`
--

LOCK TABLES `post_load_switch_conf` WRITE;
/*!40000 ALTER TABLE `post_load_switch_conf` DISABLE KEYS */;
INSERT INTO `post_load_switch_conf` VALUES (1,'max-sessions','2000'),(2,'sessions-per-second','30'),(3,'switchname','ASTPP');
/*!40000 ALTER TABLE `post_load_switch_conf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pricelists`
--

DROP TABLE IF EXISTS `pricelists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pricelists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  `markup` int(3) NOT NULL DEFAULT '0',
  `inc` int(4) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `reseller_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Accounts table id',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `reseller_id` (`reseller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pricelists`
--

LOCK TABLES `pricelists` WRITE;
/*!40000 ALTER TABLE `pricelists` DISABLE KEYS */;
INSERT INTO `pricelists` VALUES (1,'default',0,60,1,0);
/*!40000 ALTER TABLE `pricelists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `provider_cdrs`
--

DROP TABLE IF EXISTS `provider_cdrs`;
/*!50001 DROP VIEW IF EXISTS `provider_cdrs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `provider_cdrs` (
  `accountid` tinyint NOT NULL,
  `number` tinyint NOT NULL,
  `reseller_id` tinyint NOT NULL,
  `cdr_id` tinyint NOT NULL,
  `uniqueid` tinyint NOT NULL,
  `callerid` tinyint NOT NULL,
  `callednum` tinyint NOT NULL,
  `billseconds` tinyint NOT NULL,
  `trunk_id` tinyint NOT NULL,
  `disposition` tinyint NOT NULL,
  `callstart` tinyint NOT NULL,
  `debit` tinyint NOT NULL,
  `credit` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `notes` tinyint NOT NULL,
  `provider_id` tinyint NOT NULL,
  `cost` tinyint NOT NULL,
  `pricelist_id` tinyint NOT NULL,
  `pattern` tinyint NOT NULL,
  `calltype` tinyint NOT NULL,
  `trunkip` tinyint NOT NULL,
  `callerip` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `reseller_cdrs`
--

DROP TABLE IF EXISTS `reseller_cdrs`;
/*!50001 DROP VIEW IF EXISTS `reseller_cdrs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `reseller_cdrs` (
  `accountid` tinyint NOT NULL,
  `number` tinyint NOT NULL,
  `reseller_id` tinyint NOT NULL,
  `cdr_id` tinyint NOT NULL,
  `uniqueid` tinyint NOT NULL,
  `callerid` tinyint NOT NULL,
  `callednum` tinyint NOT NULL,
  `billseconds` tinyint NOT NULL,
  `trunk_id` tinyint NOT NULL,
  `disposition` tinyint NOT NULL,
  `callstart` tinyint NOT NULL,
  `debit` tinyint NOT NULL,
  `credit` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `notes` tinyint NOT NULL,
  `provider_id` tinyint NOT NULL,
  `cost` tinyint NOT NULL,
  `pricelist_id` tinyint NOT NULL,
  `pattern` tinyint NOT NULL,
  `calltype` tinyint NOT NULL,
  `trunkip` tinyint NOT NULL,
  `callerip` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `reseller_pricing`
--

DROP TABLE IF EXISTS `reseller_pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reseller_pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `monthlycost` double(10,5) NOT NULL DEFAULT '0.00000',
  `prorate` tinyint(10) NOT NULL DEFAULT '0',
  `setup` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `connectcost` decimal(10,0) NOT NULL DEFAULT '0',
  `includedseconds` int(4) NOT NULL DEFAULT '0',
  `note` varchar(50) NOT NULL DEFAULT '',
  `disconnectionfee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `inc` int(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reseller` (`reseller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reseller_pricing`
--

LOCK TABLES `reseller_pricing` WRITE;
/*!40000 ALTER TABLE `reseller_pricing` DISABLE KEYS */;
/*!40000 ALTER TABLE `reseller_pricing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resellers`
--

DROP TABLE IF EXISTS `resellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resellers` (
  `name` varchar(40) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `posttoexternal` int(11) NOT NULL DEFAULT '0',
  `agile_site_id` int(11) NOT NULL DEFAULT '0',
  `config_file` char(80) NOT NULL DEFAULT 'reseller.conf',
  `companyname` varchar(255) DEFAULT NULL,
  `slogan` varchar(255) DEFAULT NULL,
  `footer` varchar(255) DEFAULT NULL,
  `pricelist` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `adminemail` varchar(255) DEFAULT NULL,
  `salesemail` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `defaultbrand` varchar(45) NOT NULL DEFAULT 'default',
  `defaultcurrency` varchar(45) NOT NULL DEFAULT 'USD',
  `defaultcredit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `externalbill` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resellers`
--

LOCK TABLES `resellers` WRITE;
/*!40000 ALTER TABLE `resellers` DISABLE KEYS */;
/*!40000 ALTER TABLE `resellers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern` char(40) DEFAULT '',
  `comment` char(80) DEFAULT '',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(4) NOT NULL,
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `pricelist_id` int(4) DEFAULT '0',
  `inc` int(4) DEFAULT NULL,
  `reseller_id` int(11) DEFAULT '0',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pattern` (`pattern`),
  KEY `pricelist` (`pricelist_id`),
  KEY `reseller` (`reseller_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
INSERT INTO `routes` VALUES (1,'^1.*','USA',0.00000,0,0.20000,1,60,0,0,1);
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sip_devices`
--

DROP TABLE IF EXISTS `sip_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sip_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL DEFAULT '',
  `sip_profile_id` int(4) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `pricelist_id` int(4) NOT NULL DEFAULT '0' COMMENT 'pricelist table id',
  `dir_params` mediumtext NOT NULL,
  `dir_vars` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sip_devices`
--

LOCK TABLES `sip_devices` WRITE;
/*!40000 ALTER TABLE `sip_devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `sip_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sip_profiles`
--

DROP TABLE IF EXISTS `sip_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sip_profiles` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `sip_ip` varchar(15) NOT NULL DEFAULT '',
  `sip_port` varchar(6) NOT NULL DEFAULT '',
  `profile_data` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accountid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sip_profiles`
--

LOCK TABLES `sip_profiles` WRITE;
/*!40000 ALTER TABLE `sip_profiles` DISABLE KEYS */;
INSERT INTO `sip_profiles` VALUES (1,'default','$${local_ip_v4}','5060','{\"rtp_ip\":\"$${local_ip_v4}\",\"dialplan\":\"XML\",\"user-agent-string\":\"ASTPP\",\"debug\":\"0\",\"sip-trace\":\"no\",\"tls\":\"false\",\"inbound-reg-force-matching-username\":\"true\",\"disable-transcoding\":\"true\",\"all-reg-options-ping\":\"false\",\"unregister-on-options-fail\":\"true\",\"inbound-bypass-media\":\"false\",\"inbound-proxy-media\":\"false\",\"disable-transfer\":\"true\",\"enable-100rel\":\"false\",\"rtp-timeout-sec\":\"60\",\"dtmf-duration\":\"2000\",\"aggressive-nat-detection\":\"false\",\"enable-timer\":\"false\",\"minimum-session-expires\":\"120\",\"session-timeout-pt\":\"1800\",\"auth-calls\":\"true\",\"apply-inbound-acl\":\"default\"}','2013-05-17 08:07:23','0000-00-00 00:00:00',0);
/*!40000 ALTER TABLE `sip_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sweeplist`
--

DROP TABLE IF EXISTS `sweeplist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sweeplist` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `sweep` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sweeplist`
--

LOCK TABLES `sweeplist` WRITE;
/*!40000 ALTER TABLE `sweeplist` DISABLE KEYS */;
INSERT INTO `sweeplist` VALUES (0,'Daily'),(1,'Weekly'),(2,'Monthly'),(3,'Quarterly'),(4,'Semi-annually'),(5,'Annually');
/*!40000 ALTER TABLE `sweeplist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system`
--

DROP TABLE IF EXISTS `system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `reseller_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `group_title` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `reseller` (`reseller_id`),
  KEY `brand` (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system`
--

LOCK TABLES `system` WRITE;
/*!40000 ALTER TABLE `system` DISABLE KEYS */;
INSERT INTO `system` VALUES (1,'log_file','/var/log/astpp/astpp.log','Where do I log to?',NULL,0,0,'global'),(2,'callout_accountcode','admin','Call Files: What accountcode should we use?',NULL,0,0,'asterisk'),(3,'lcrcontext','astpp-outgoing','This is the Local context we use to route our outgoing calls through esp for callbacks',NULL,0,0,'asterisk'),(4,'maxretries','3','Call Files: How many times do we retry?',NULL,0,0,'asterisk'),(5,'retrytime','30','Call Files: How long do we wait between retries?',NULL,0,0,'asterisk'),(6,'waittime','15','Call Files: How long do we wait before the initial call?',NULL,0,0,'asterisk'),(7,'clidname','Private','Call Files: Outgoing CallerID company_emailName',NULL,0,0,'asterisk'),(8,'clidnumber','0000000000','Call Files: Outgoing CallerID Number',NULL,0,0,'asterisk'),(9,'callingcards_callback_context','astpp-callingcards','Call Files: For callingcards what context do we end up in?',NULL,0,0,'asterisk'),(10,'callingcards_callback_extension','s','Call Files: For callingcards what extension do we use?',NULL,0,0,'asterisk'),(11,'opensips_dbengine','MySQL','For now this must be MySQL',NULL,0,0,'opensips'),(12,'opensips','0','Use Opensips?  1 for yes or 0 for no',NULL,0,0,'opensips'),(13,'opensips_dbname','opensips dbname','Opensips Database Name',NULL,0,0,'opensips'),(14,'opensips_dbuser','opensips dbuser','Opensips Database User',NULL,0,0,'opensips'),(15,'opensips_dbhost','opensips dbhost','Opensips Database Host',NULL,0,0,'opensips'),(16,'opensips_dbpass','opensips dbpass','Opensips Database Password',NULL,0,0,'opensips'),(17,'opensips_domain','opensips domain','Opensips Domain',NULL,0,0,'opensips'),(18,'company_email','info@astpp.org','Email address that email should appear to be from',NULL,0,0,'global'),(19,'asterisk_dir','/etc/asterisk','Which directory are asterisk configuration files stored in?',NULL,0,0,'asterisk'),(20,'company_website','http://www.astpp.org','Link to your company website',NULL,0,0,'global'),(21,'company_name','ASTPP.ORG','The name of your company.  Used in emails.',NULL,0,0,'global'),(22,'email','1','Send out email? 0=no 1=yes',NULL,0,0,'global'),(23,'user_email','1','Email user on account changes? 0=no 1=yes',NULL,0,0,'global'),(24,'debug','0','Enable debugging output? 0=no 1=yes',NULL,0,0,'global'),(25,'emailadd','info@astpp.org','Administrator email address',NULL,0,0,'global'),(26,'startingdigit','0','The digit that all calling cards must start with. 0=disabled',NULL,0,0,'callingcard'),(27,'enablelcr','1','Use least cost routing 0=no 1=yes',NULL,0,0,'asterisk'),(29,'key_home','http://www.astpp.org/astpp.pub','Asterisk RSA Key location (optional)',NULL,0,0,'asterisk'),(30,'rate_engine_csv_file','/var/log/astpp/astpp.csv','CSV File for call rating data',NULL,0,0,'global'),(31,'csv_dir','/var/log/astpp/','CSV File Directory',NULL,0,0,'global'),(32,'default_brand','default','Default pricelist.  If a price is not found in the customers pricelist we check this one.',NULL,0,0,'global'),(33,'new_user_brand','default','What is the default pricelist for new customers?',NULL,0,0,'global'),(34,'default_context','default','What is the default context for new devices?',NULL,0,0,'global'),(35,'cardlength','10','Number of digits in calling cards and cc codes.',NULL,0,0,'callingcard'),(36,'asterisk_server','voip.astpp.org','Your default voip server.  Used in outgoing email.',NULL,0,0,'asterisk'),(38,'iax_port','4569','Default IAX2 Port',NULL,0,0,'asterisk'),(39,'sip_port','5060','Default SIP Port',NULL,0,0,'asterisk'),(40,'ipaddr','dynamic','Default IP Address for new devices',NULL,0,0,'asterisk'),(41,'key','astpp.pub','Asterisk RSA Key Name (Optional)',NULL,0,0,'asterisk'),(42,'pinlength','6','For those calling cards that are using pins this is the number of digits it will have.',NULL,0,0,'callingcard'),(43,'credit_limit','0','Default credit limit in dollars.',NULL,0,0,'global'),(44,'decimalpoints','4','How many decimal points do we bill to?',NULL,0,0,'global'),(45,'decimalpoints_tax','2','How many decimal points do we calculate taxes to?',NULL,0,0,'global'),(46,'decimalpoints_total','2','How many decimal points do we calculate totals to?',NULL,0,0,'global'),(47,'max_free_length','100','What is the maximum length (in minutes) of calls that are at no charge?',NULL,0,0,'global'),(48,'trackvendorcharges','1','Do we track the amount of money we spend with specific providers? 0=no 1=yes',NULL,0,0,'global'),(53,'card_retries','3','How many retries do we allow for calling card numbers?',NULL,0,0,'callingcard'),(54,'pin_retries','3','How many retries do we allow for pins?',NULL,0,0,'callingcard'),(55,'number_retries','3','How many retries do we allow calling card users when dialing a number?',NULL,0,0,'callingcard'),(56,'booth_context','callshop_booth','Please enter the default context for a callshop booth.',NULL,0,0,'global'),(57,'callingcards_max_length','9000','What is the maximum length (in ms) of a callingcard call?',NULL,0,0,'callingcard'),(60,'astpp_dir','/var/lib/astpp','Where do the astpp configs live?',NULL,0,0,'global'),(61,'auth','Passw0rd!','This is the override authorization code and will allow access to the system.',NULL,0,0,'global'),(62,'rt_dbengine','MySQL','Database type for Asterisk(tm) -Realtime',NULL,0,0,'asterisk'),(64,'osc_dbengine','MySQL','Database type for OSCommerce',NULL,0,0,'osc'),(66,'freepbx_dbengine','MySQL','Database type for FreePBX',NULL,0,0,'freepbx'),(67,'externalbill','internal','Please specify the external billing application to use.  If you are not using any then leave it blank.  Valid options are ',NULL,0,0,'global'),(68,'callingcards','1','Do you wish to enable calling cards?  1 for yes and 2 for no.',NULL,0,0,'callingcard'),(70,'posttoastpp','1','Change this one at your own peril.  If you switch it off, calls will not be written to astpp when they are calculated.',NULL,0,0,'global'),(71,'sleep','10','How long shall the rating engine sleep after it has been notified of a hangup? (in seconds)',NULL,0,0,'global'),(72,'users_dids_amp','0','If this is enabled, ASTPP will create users and DIDs in the FreePBX (www.freepbx.org) database.',NULL,0,0,'freepbx'),(73,'users_dids_rt','0','If this is enabled, ASTPP will create users and DIDs in the Asterisk Realtime database.',NULL,0,0,'asterisk'),(74,'users_dids_freeswitch','1','If this is enabled, ASTPP will create SIP users in the freeswitch database.',NULL,0,0,'freeswitch'),(75,'softswitch','1','What softswitch are we using?  0=asterisk, 1=freeswitch',NULL,0,0,'global'),(76,'service_prepend','778','',NULL,0,0,'global'),(77,'service_length','7','',NULL,0,0,'global'),(78,'service_filler','4110000','',NULL,0,0,'global'),(79,'asterisk_cdr_table','cdr','Which table of the Asterisk(TM) database are the cdrs in?',NULL,0,0,'asterisk'),(89,'osc_host','localhost','Oscommerce Database Host',NULL,0,0,'osc'),(90,'osc_db','oscommerce','Oscommerce Database Name',NULL,0,0,'osc'),(91,'osc_user','OSC DB USER','Oscommerce Database Username',NULL,0,0,'osc'),(92,'osc_pass','OSC DB PASSWORD','Oscommerce Database Password',NULL,0,0,'osc'),(93,'osc_product_id','99999999','Oscommerce Default Product ID',NULL,0,0,'osc'),(94,'osc_payment_method','\"Charge\"','Oscommerce Default Payment method',NULL,0,0,'osc'),(95,'osc_order_status','1','Oscommerce Default Order Status',NULL,0,0,'osc'),(96,'osc_post_nc','1','Do we post ',NULL,0,0,'osc'),(97,'freepbx_host','localhost','Freepbx Database Host',NULL,0,0,'freepbx'),(98,'freepbx_db','asterisk','Freepbx Database name',NULL,0,0,'freepbx'),(99,'freepbx_user','FREEPBX DB USER','FreePBX Database Username',NULL,0,0,'freepbx'),(100,'freepbx_pass','FREEPBX DB PASSWORD','FreePBX Database Password',NULL,0,0,'freepbx'),(101,'freepbx_iax_table','iax','FreePBX IAX Table Name',NULL,0,0,'freepbx'),(102,'freepbx_table','sip','FreePBX SIP Table Name',NULL,0,0,'freepbx'),(103,'freepbx_extensions_table','extensions','FreePBX Extensions Table Name',NULL,0,0,'freepbx'),(104,'freepbx_codec_allow','g729,ulaw,alaw','FreePBX Default Asterisk Allowed Codec',NULL,0,0,'freepbx'),(105,'freepbx_codec_disallow','all','FreePBX Default Asterisk Disallowed Codec',NULL,0,0,'freepbx'),(106,'freepbx_mailbox_group','default','Freepbx Default Mailbox Group',NULL,0,0,'freepbx'),(107,'freepbx_sip_nat','yes','',NULL,0,0,'freepbx'),(108,'freepbx_sip_canreinvite','no','',NULL,0,0,'freepbx'),(109,'freepbx_sip_dtmfmode','rfc2833','',NULL,0,0,'freepbx'),(110,'freepbx_sip_qualify','yes','',NULL,0,0,'freepbx'),(111,'freepbx_sip_type','friend','',NULL,0,0,'freepbx'),(112,'freepbx_sip_callgroup','','',NULL,0,0,'freepbx'),(113,'freepbx_sip_pickupgroup','','',NULL,0,0,'freepbx'),(114,'freepbx_iax_notransfer','yes','',NULL,0,0,'freepbx'),(115,'freepbx_iax_type','friend','',NULL,0,0,'freepbx'),(116,'freepbx_iax_qualify','yes','',NULL,0,0,'freepbx'),(117,'rt_host','localhost','Asterisk Realtime database host',NULL,0,0,'asterisk'),(118,'rt_db','realtime','Asterisk Realtime database name',NULL,0,0,'asterisk'),(119,'rt_user','ASTERISK REALTIME DB USER','Asterisk Realtime database username',NULL,0,0,'asterisk'),(120,'rt_pass','ASTERISK REALTIME DB PASSWORD','Asterisk Realtime database password',NULL,0,0,'asterisk'),(121,'rt_iax_table','iax','Asterisk Realtime IAX Table Name',NULL,0,0,'asterisk'),(122,'rt_sip_table','sip','Asterisk Realtime SIP Table Name',NULL,0,0,'asterisk'),(123,'rt_extensions_table','extensions','Asterisk Realtime Extensions Table Name',NULL,0,0,'asterisk'),(124,'rt_sip_insecure','very','',NULL,0,0,'asterisk'),(125,'rt_sip_nat','yes','',NULL,0,0,'asterisk'),(126,'rt_sip_canreinvite','no','',NULL,0,0,'asterisk'),(127,'rt_codec_allow','g729,ulaw,alaw','',NULL,0,0,'asterisk'),(128,'rt_codec_disallow','all','',NULL,0,0,'asterisk'),(129,'rt_mailbox_group','default','',NULL,0,0,'asterisk'),(130,'rt_sip_qualify','yes','',NULL,0,0,'asterisk'),(131,'rt_sip_type','friend','',NULL,0,0,'asterisk'),(132,'rt_iax_qualify','yes','',NULL,0,0,'asterisk'),(133,'rt_iax_type','friend','',NULL,0,0,'asterisk'),(134,'rt_voicemail_table','voicemail_users','Asterisk Realtime Voicemail Table Name',NULL,0,0,'asterisk'),(135,'calling_cards_rate_announce','0','Do we want the calling cards script to announce the rate on calls?',NULL,0,0,'callingcard'),(136,'calling_cards_timelimit_announce','0','Do we want the calling cards script to announce the timelimit on calls?',NULL,0,0,'callingcard'),(137,'calling_cards_cancelled_prompt','1','Do we want the calling cards script to announce that the call was cancelled?',NULL,0,0,'callingcard'),(138,'calling_cards_menu','0','Do we want the calling cards script to present a menu before exiting?',NULL,0,0,'callingcard'),(139,'calling_cards_connection_prompt','0','Do we want the calling cards script to announce that it is connecting the call?',NULL,0,0,'callingcard'),(140,'calling_cards_pin_input_timeout','15000','How long do we wait when entering the calling card pin?  Specified in MS',NULL,0,0,'callingcard'),(141,'calling_cards_number_input_timeout','15000','How long do we wait when entering the calling card number?  Specified in MS',NULL,0,0,'callingcard'),(142,'calling_cards_dial_input_timeout','15000','How long do we wait when entering the destination number in calling cards?  Specified in MS',NULL,0,0,'callingcard'),(143,'calling_cards_general_input_timeout','15000','How long do we wait for input in general menus?  Specified in MS',NULL,0,0,'callingcard'),(144,'calling_cards_welcome_file','astpp-welcome.wav','What do we play for a welcome file?',NULL,0,0,'callingcard'),(145,'sip_ext_prepend','10','What should every autoadded SIP extension begin with?',NULL,0,0,'asterisk'),(146,'iax2_ext_prepend','10','What should every autoadded IAX2 extension begin with?',NULL,0,0,'asterisk'),(147,'cc_prepend','','What should every autoadded callingcard begin with?',NULL,0,0,'callingcard'),(148,'pin_cc_prepend','','What should every autoadded callingcard pin begin with?',NULL,0,0,'callingcard'),(149,'pin_act_prepend','','What should every autoadded account pin begin with?',NULL,0,0,'global'),(162,'freeswitch_context','default','This is entered as the Freeswitch user context.',NULL,0,0,'freeswitch'),(163,'freeswitch_sound_files','/en/us/callie','Where are our sound files located?',NULL,0,0,'freeswitch'),(168,'astman_user','admin','Asterisk(tm) Manager Interface User',NULL,0,0,'asterisk'),(169,'astman_host','localhost','Asterisk(tm) Manager Interface Host',NULL,0,0,'asterisk'),(170,'astman_secret','amp111','Asterisk(tm) Manager Interface Secret',NULL,0,0,'asterisk'),(171,'call_max_length','1440000','What is the maximum length (in ms) of a LCR call?',NULL,0,0,'global'),(173,'cc_ani_auth','0','Calling card ANI authentiation. 0 for disable and 1 for enable',NULL,0,0,'callingcard'),(174,'base_currency','USD','Base Currency of System',NULL,0,0,'global'),(178,'callingcard_leg_a_cdr','0','Save leg A cdr of calling card? 0=no 1=yes',NULL,0,0,'callingcard'),(179,'default_timezone','26','system timezone','2013-05-06 19:34:39',0,0,'global'),(180,'min_channel_balance','0.0','Per channel balance',NULL,0,0,'global');
/*!40000 ALTER TABLE `system` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taxes_priority` int(5) DEFAULT '1',
  `taxes_amount` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `taxes_rate` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `taxes_description` varchar(255) NOT NULL,
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `taxes_priority` (`taxes_priority`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;
INSERT INTO `taxes` VALUES (1,1,0.00000,5.00000,'VAT',NULL,'2013-05-24 18:07:19');
/*!40000 ALTER TABLE `taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxes_to_accounts`
--

DROP TABLE IF EXISTS `taxes_to_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxes_to_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `taxes_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `accountid` (`accountid`),
  KEY `taxes_id` (`taxes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes_to_accounts`
--

LOCK TABLES `taxes_to_accounts` WRITE;
/*!40000 ALTER TABLE `taxes_to_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxes_to_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `subject` varchar(10000) NOT NULL,
  `accountid` int(11) NOT NULL,
  `template` mediumtext NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `reseller` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timezone`
--

DROP TABLE IF EXISTS `timezone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timezone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gmtzone` varchar(255) DEFAULT NULL,
  `gmttime` varchar(255) DEFAULT NULL,
  `gmtoffset` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timezone`
--

LOCK TABLES `timezone` WRITE;
/*!40000 ALTER TABLE `timezone` DISABLE KEYS */;
INSERT INTO `timezone` VALUES (1,'(GMT-12:00) International Date Line West','GMT-12:00',-43200),(2,'(GMT-11:00) Midway Island, Samoa','GMT-11:00',-39600),(3,'(GMT-10:00) Hawaii','GMT-10:00',-36000),(4,'(GMT-09:00) Alaska','GMT-09:00',-32400),(5,'(GMT-08:00) Pacific Time (US & Canada) Tijuana','GMT-08:00',-28800),(6,'(GMT-07:00) Arizona','GMT-07:00',-25200),(7,'(GMT-07:00) Chihuahua, La Paz, Mazatlan','GMT-07:00',-25200),(8,'(GMT-07:00) Mountain Time(US & Canada)','GMT-07:00',-25200),(9,'(GMT-06:00) Central America','GMT-06:00',-21600),(10,'(GMT-06:00) Central Time (US & Canada)','GMT-06:00',-21600),(11,'(GMT-06:00) Guadalajara, Mexico City, Monterrey','GMT-06:00',-21600),(12,'(GMT-06:00) Saskatchewan','GMT-06:00',-21600),(13,'(GMT-05:00) Bogota, Lima, Quito','GMT-05:00',-18000),(14,'(GMT-05:00) Eastern Time (US & Canada)','GMT-05:00',-18000),(15,'(GMT-05:00) Indiana (East)','GMT-05:00',-18000),(16,'(GMT-04:00) Atlantic Time (Canada)','GMT-04:00',-14400),(17,'(GMT-04:00) Caracas, La Paz','GMT-04:00',-14400),(18,'(GMT-04:00) Santiago','GMT-04:00',-14400),(19,'(GMT-03:30) NewFoundland','GMT-03:30',-12600),(20,'(GMT-03:00) Brasillia','GMT-03:00',-10800),(21,'(GMT-03:00) Buenos Aires, Georgetown','GMT-03:00',-10800),(22,'(GMT-03:00) Greenland','GMT-03:00',-10800),(23,'(GMT-03:00) Mid-Atlantic','GMT-03:00',-10800),(24,'(GMT-01:00) Azores','GMT-01:00',-3600),(25,'(GMT-01:00) Cape Verd Is.','GMT-01:00',-3600),(26,'(GMT) Casablanca, Monrovia','GMT+00:00',0),(27,'(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon,  London','GMT',0),(28,'(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna','GMT+01:00',3600),(29,'(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague','GMT+01:00',3600),(30,'(GMT+01:00) Brussels, Copenhagen, Madrid, Paris','GMT+01:00',3600),(31,'(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb','GMT+01:00',3600),(32,'(GMT+01:00) West Central Africa','GMT+01:00',3600),(33,'(GMT+02:00) Athens, Istanbul, Minsk','GMT+02:00',7200),(34,'(GMT+02:00) Bucharest','GMT+02:00',7200),(35,'(GMT+02:00) Cairo','GMT+02:00',7200),(36,'(GMT+02:00) Harere, Pretoria','GMT+02:00',7200),(37,'(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius','GMT+02:00',7200),(38,'(GMT+02:00) Jeruasalem','GMT+02:00',7200),(39,'(GMT+03:00) Baghdad','GMT+03:00',10800),(40,'(GMT+03:00) Kuwait, Riyadh','GMT+03:00',10800),(41,'(GMT+03:00) Moscow, St.Petersburg, Volgograd','GMT+03:00',10800),(42,'(GMT+03:00) Nairobi','GMT+03:00',10800),(43,'(GMT+03:30) Tehran','GMT+03:30',12600),(44,'(GMT+04:00) Abu Dhabi, Muscat','GMT+04:00',14400),(45,'(GMT+04:00) Baku, Tbillisi, Yerevan','GMT+04:00',14400),(46,'(GMT+04:30) Kabul','GMT+04:30',16200),(47,'(GMT+05:00) Ekaterinburg','GMT+05:00',18000),(48,'(GMT+05:00) Islamabad, Karachi, Tashkent','GMT+05:00',18000),(49,'(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi','GMT+05:30',19800),(50,'(GMT+05:45) Kathmandu','GMT+05:45',20700),(51,'(GMT+06:00) Almaty, Novosibirsk','GMT+06:00',21600),(52,'(GMT+06:00) Astana, Dhaka','GMT+06:00',21600),(53,'(GMT+06:00) Sri Jayawardenepura','GMT+06:00',21600),(54,'(GMT+06:30) Rangoon','GMT+06:30',23400),(55,'(GMT+07:00) Bangkok, Hanoi, Jakarta','GMT+07:00',25200),(56,'(GMT+07:00) Krasnoyarsk','GMT+07:00',25200),(57,'(GMT+08:00) Beijiing, Chongging, Hong Kong, Urumqi','GMT+08:00',28800),(58,'(GMT+08:00) Irkutsk, Ulaan Bataar','GMT+08:00',28800),(59,'(GMT+08:00) Kuala Lumpur, Singapore','GMT+08:00',28800),(60,'(GMT+08:00) Perth','GMT+08:00',28800),(61,'(GMT+08:00) Taipei','GMT+08:00',28800),(62,'(GMT+09:00) Osaka, Sapporo, Tokyo','GMT+09:00',32400),(63,'(GMT+09:00) Seoul','GMT+09:00',32400),(64,'(GMT+09:00) Yakutsk','GMT+09:00',32400),(65,'(GMT+09:00) Adelaide','GMT+09:00',32400),(66,'(GMT+09:30) Darwin','GMT+09:30',34200),(67,'(GMT+10:00) Brisbane','GMT+10:00',36000),(68,'(GMT+10:00) Canberra, Melbourne, Sydney','GMT+10:00',36000),(69,'(GMT+10:00) Guam, Port Moresby','GMT+10:00',36000),(70,'(GMT+10:00) Hobart','GMT+10:00',36000),(71,'(GMT+10:00) Vladivostok','GMT+10:00',36000),(72,'(GMT+11:00) Magadan, Solomon Is., New Caledonia','GMT+11:00',39600),(73,'(GMT+12:00) Auckland, Wellington','GMT+1200',43200),(74,'(GMT+12:00) Fiji, Kamchatka, Marshall Is.','GMT+12:00',43200),(75,'(GMT+13:00) Nuku alofa','GMT+13:00',46800);
/*!40000 ALTER TABLE `timezone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trunks`
--

DROP TABLE IF EXISTS `trunks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trunks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `tech` char(10) NOT NULL DEFAULT '',
  `gateway_id` int(4) NOT NULL DEFAULT '0',
  `provider_id` int(11) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `dialed_modify` mediumtext NOT NULL,
  `resellers_id` varchar(11) NOT NULL DEFAULT '0',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `maxchannels` int(4) NOT NULL DEFAULT '0',
  `inuse` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `provider` (`provider_id`),
  KEY `resellers_id` (`resellers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trunks`
--

LOCK TABLES `trunks` WRITE;
/*!40000 ALTER TABLE `trunks` DISABLE KEYS */;
INSERT INTO `trunks` VALUES (1,'Yourtrunk','SIP',1,1,1,'','',1,0,0);
/*!40000 ALTER TABLE `trunks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userlevels`
--

DROP TABLE IF EXISTS `userlevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userlevels` (
  `userlevelid` int(11) NOT NULL,
  `userlevelname` varchar(15) NOT NULL,
  `module_permissions` varchar(255) NOT NULL,
  PRIMARY KEY (`userlevelid`),
  KEY `userlevelname` (`userlevelname`),
  KEY `module_permissions` (`module_permissions`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userlevels`
--

LOCK TABLES `userlevels` WRITE;
/*!40000 ALTER TABLE `userlevels` DISABLE KEYS */;
INSERT INTO `userlevels` VALUES (-1,'Administrator','1,2,4,5,3,6,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,7,29,30,38,39,40,41,42,43,44,45'),(0,'Customer','31,32,34,35,36,37'),(1,'Reseller','1,2,7,8,9,10,11,12,13,16,17,18,19,21,28,38,40'),(2,'Admin','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,29,30,38,39,40,41,42,43,44,45'),(3,'Provider','15,24'),(4,'Sub Admin','1,2,3,5,6,10,11,13,16,24,25,26,28'),(5,'CallShop','10,11,17');
/*!40000 ALTER TABLE `userlevels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `customer_cdrs`
--

/*!50001 DROP TABLE IF EXISTS `customer_cdrs`*/;
/*!50001 DROP VIEW IF EXISTS `customer_cdrs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `customer_cdrs` AS select `a`.`id` AS `accountid`,`a`.`number` AS `number`,`a`.`reseller_id` AS `reseller_id`,`b`.`id` AS `cdr_id`,`b`.`uniqueid` AS `uniqueid`,`b`.`callerid` AS `callerid`,`b`.`callednum` AS `callednum`,`b`.`billseconds` AS `billseconds`,`b`.`trunk_id` AS `trunk_id`,`b`.`disposition` AS `disposition`,`b`.`callstart` AS `callstart`,`b`.`debit` AS `debit`,`b`.`credit` AS `credit`,`b`.`status` AS `status`,`b`.`notes` AS `notes`,`b`.`provider_id` AS `provider_id`,`b`.`cost` AS `cost`,`b`.`pricelist_id` AS `pricelist_id`,`b`.`pattern` AS `pattern`,`b`.`calltype` AS `calltype`,`b`.`trunkip` AS `trunkip`,`b`.`callerip` AS `callerip` from (`accounts` `a` join `cdrs` `b`) where ((`a`.`id` = `b`.`accountid`) and (`a`.`type` = 0) and (`b`.`uniqueid` <> _utf8'') and (`b`.`uniqueid` <> _utf8'0') and (`b`.`uniqueid` <> _utf8'1') and (`b`.`uniqueid` <> _utf8'N/A')) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `invoice_list_view`
--

/*!50001 DROP TABLE IF EXISTS `invoice_list_view`*/;
/*!50001 DROP VIEW IF EXISTS `invoice_list_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `invoice_list_view` AS select `invoices`.`id` AS `invoiceid`,`invoices`.`accountid` AS `accountid`,`invoices`.`date` AS `date`,`invoices`.`status` AS `status`,`invoices_total`.`value` AS `value`,`invoices_total`.`class` AS `class` from (`invoices` join `invoices_total`) where ((`invoices_total`.`class` = 9) and (`invoices`.`id` = `invoices_total`.`invoices_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `provider_cdrs`
--

/*!50001 DROP TABLE IF EXISTS `provider_cdrs`*/;
/*!50001 DROP VIEW IF EXISTS `provider_cdrs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `provider_cdrs` AS select `a`.`id` AS `accountid`,`a`.`number` AS `number`,`a`.`reseller_id` AS `reseller_id`,`b`.`id` AS `cdr_id`,`b`.`uniqueid` AS `uniqueid`,`b`.`callerid` AS `callerid`,`b`.`callednum` AS `callednum`,`b`.`billseconds` AS `billseconds`,`b`.`trunk_id` AS `trunk_id`,`b`.`disposition` AS `disposition`,`b`.`callstart` AS `callstart`,`b`.`debit` AS `debit`,`b`.`credit` AS `credit`,`b`.`status` AS `status`,`b`.`notes` AS `notes`,`b`.`provider_id` AS `provider_id`,`b`.`cost` AS `cost`,`b`.`pricelist_id` AS `pricelist_id`,`b`.`pattern` AS `pattern`,`b`.`calltype` AS `calltype`,`b`.`trunkip` AS `trunkip`,`b`.`callerip` AS `callerip` from (`accounts` `a` join `cdrs` `b`) where ((`a`.`id` = `b`.`accountid`) and (`a`.`type` = 3) and (`b`.`uniqueid` <> _utf8'') and (`b`.`uniqueid` <> _utf8'0') and (`b`.`uniqueid` <> _utf8'1') and (`b`.`uniqueid` <> _utf8'N/A')) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `reseller_cdrs`
--

/*!50001 DROP TABLE IF EXISTS `reseller_cdrs`*/;
/*!50001 DROP VIEW IF EXISTS `reseller_cdrs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `reseller_cdrs` AS select `a`.`id` AS `accountid`,`a`.`number` AS `number`,`a`.`reseller_id` AS `reseller_id`,`b`.`id` AS `cdr_id`,`b`.`uniqueid` AS `uniqueid`,`b`.`callerid` AS `callerid`,`b`.`callednum` AS `callednum`,`b`.`billseconds` AS `billseconds`,`b`.`trunk_id` AS `trunk_id`,`b`.`disposition` AS `disposition`,`b`.`callstart` AS `callstart`,`b`.`debit` AS `debit`,`b`.`credit` AS `credit`,`b`.`status` AS `status`,`b`.`notes` AS `notes`,`b`.`provider_id` AS `provider_id`,`b`.`cost` AS `cost`,`b`.`pricelist_id` AS `pricelist_id`,`b`.`pattern` AS `pattern`,`b`.`calltype` AS `calltype`,`b`.`trunkip` AS `trunkip`,`b`.`callerip` AS `callerip` from (`accounts` `a` join `cdrs` `b`) where ((`a`.`id` = `b`.`accountid`) and (`a`.`type` = 1) and (`b`.`uniqueid` <> _utf8'')) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-05-25 21:19:02
