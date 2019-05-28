-- MySQL dump 10.13  Distrib 5.5.50, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: astpp
-- ------------------------------------------------------
-- Server version	5.5.50-0+deb8u1

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
  `number` varchar(20) NOT NULL,
  `reseller_id` int(4) DEFAULT NULL COMMENT 'Resellers account id',
  `pricelist_id` int(4) NOT NULL COMMENT 'pricelist table id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:active,1:inactive',
  `sweep_id` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Sweep list table id',
  `creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `credit_limit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `posttoexternal` tinyint(1) NOT NULL DEFAULT '0',
  `balance` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `password` varchar(100) NOT NULL DEFAULT '',
  `first_name` varchar(40) NOT NULL DEFAULT '',
  `last_name` varchar(40) NOT NULL DEFAULT '',
  `company_name` varchar(40) NOT NULL DEFAULT '',
  `address_1` varchar(80) NOT NULL DEFAULT '',
  `address_2` varchar(80) NOT NULL DEFAULT '',
  `postal_code` varchar(12) NOT NULL DEFAULT '',
  `province` varchar(20) NOT NULL DEFAULT '',
  `city` varchar(20) NOT NULL DEFAULT '',
  `country_id` int(3) NOT NULL DEFAULT '0' COMMENT 'Country table id',
  `telephone_1` varchar(20) NOT NULL DEFAULT '',
  `telephone_2` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `language_id` int(3) NOT NULL DEFAULT '0' COMMENT 'language table id',
  `currency_id` int(3) NOT NULL DEFAULT '0' COMMENT 'Currency table id',
  `maxchannels` int(4) NOT NULL DEFAULT '1',
  `interval` int(11) NOT NULL DEFAULT '0',
  `dialed_modify` mediumtext NOT NULL,
  `type` tinyint(1) DEFAULT '0',
  `timezone_id` int(3) NOT NULL DEFAULT '0' COMMENT 'timezone table id',
  `inuse` int(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=deleted',
  `notify_credit_limit` int(11) NOT NULL,
  `notify_flag` tinyint(1) NOT NULL,
  `notify_email` varchar(80) NOT NULL,
  `commission_rate` int(11) NOT NULL DEFAULT '0',
  `invoice_day` int(11) NOT NULL DEFAULT '0',
  `pin` varchar(20) NOT NULL,
  `first_used` datetime NOT NULL,
  `expiry` datetime NOT NULL,
  `validfordays` int(7) NOT NULL DEFAULT '3652',
  `local_call_cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `pass_link_status` tinyint(1) NOT NULL DEFAULT '0',
  `local_call` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:enable,0:disable',
  `charge_per_min` varchar(100) NOT NULL,
  `is_recording` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 for On,1 for Off',
  `allow_ip_management` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:enable,0:disable',
  PRIMARY KEY (`id`),
  KEY `number` (`number`),
  KEY `pricelist` (`pricelist_id`),
  KEY `reseller` (`reseller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'admin',0,0,0,2,'2016-07-25 00:00:01',0.00000,0,1.00000,'drwcmIaIlzzUaQ9PwgOGRn2KcKmSq44tWvKGgHfkpl0','Administrator','Admin','Your Company','ADDRESS','','','','',85,'','','your@email.com',1,139,1,0,'',-1,27,0,0,0,0,'0',0,0,'','2016-07-26 11:26:24','2046-07-25 11:26:24',60000,0.00000,0,0,'1',0,0),(2,'2457848300',0,1,0,2,'2016-07-25 11:26:24',0.00000,0,1.00000,'GpMl9v2b32xNILRXMxHxrStFNd4I26bTNDAEG2eYQDM','default','customer','ASTPP','adress','','','','',85,'','','yourcustomer@test.com',0,59,1,0,'',0,49,0,0,0,1,'',0,1,'2457848300','2016-07-26 11:26:24','2046-07-25 11:26:24',3652,0.00000,0,0,'1',0,0),(3, '7335503421', 0, 1, 0, 2, '2016-07-26 15:15:20', 0.00000, 0, 0.00000, 'gaiaRg3lAZI$nTbTjVVe4Z0-hFxXrxCzQTOIug0SHow', 'default', 'provider', 'ASTPP', 'adress', '', '', '', '', 85, '', '', 'yourprovider@test.com', 0, 59, 0, 0, '', 3, 49, 0, 0, 0, 0, '', 0, 1, '320736', '0000-00-00 00:00:00', '2026-07-26 15:12:18', 0, 0.00000, 0, 0, '', 0, 0);
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
-- Table structure for table `ani_map`
--

DROP TABLE IF EXISTS `ani_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ani_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(20) NOT NULL DEFAULT '',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Active,1-inactive',
  `context` varchar(20) NOT NULL DEFAULT '',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
-- Table structure for table `backup_database`
--

DROP TABLE IF EXISTS `backup_database`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_database` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `backup_name` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `path` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_database`
--

LOCK TABLES `backup_database` WRITE;
/*!40000 ALTER TABLE `backup_database` DISABLE KEYS */;
/*!40000 ALTER TABLE `backup_database` ENABLE KEYS */;
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
  `destination` varchar(100) NOT NULL,
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
-- Table structure for table `cdrs`
--

DROP TABLE IF EXISTS `cdrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cdrs` (
  `uniqueid` varchar(60) NOT NULL DEFAULT '',
  `accountid` int(11) DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `callerid` varchar(30) NOT NULL,
  `callednum` varchar(30) NOT NULL DEFAULT '',
  `billseconds` smallint(6) NOT NULL DEFAULT '0',
  `trunk_id` smallint(6) NOT NULL DEFAULT '0',
  `trunkip` varchar(15) NOT NULL DEFAULT '',
  `callerip` varchar(15) NOT NULL DEFAULT '',
  `disposition` varchar(45) NOT NULL DEFAULT '',
  `callstart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `debit` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `cost` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `provider_id` int(11) NOT NULL DEFAULT '0',
  `pricelist_id` smallint(6) NOT NULL DEFAULT '0',
  `package_id` int(11) NOT NULL DEFAULT '0',
  `pattern` varchar(20) NOT NULL,
  `notes` varchar(80) NOT NULL,
  `invoiceid` int(11) NOT NULL DEFAULT '0',
  `rate_cost` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `reseller_code` varchar(20) NOT NULL,
  `reseller_code_destination` varchar(80) DEFAULT NULL,
  `reseller_cost` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `provider_code` varchar(20) NOT NULL,
  `provider_code_destination` varchar(80) NOT NULL,
  `provider_cost` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `provider_call_cost` decimal(20,6) NOT NULL,
  `call_direction` enum('outbound','inbound') NOT NULL,
  `calltype` enum('STANDARD','DID','FREE','CALLINGCARD') NOT NULL DEFAULT 'STANDARD',
  `profile_start_stamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `answer_stamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bridge_stamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `progress_stamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `progress_media_stamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_stamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `billmsec` int(11) NOT NULL DEFAULT '0',
  `answermsec` int(11) NOT NULL DEFAULT '0',
  `waitmsec` int(11) NOT NULL DEFAULT '0',
  `progress_mediamsec` int(11) NOT NULL DEFAULT '0',
  `flow_billmsec` int(11) NOT NULL DEFAULT '0',
  `is_recording` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 for On,1 for Off',
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `user_id` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cdrs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cdrs`
--

LOCK TABLES `cdrs` WRITE;
/*!40000 ALTER TABLE `cdrs` DISABLE KEYS */;
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
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `charge_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `charge` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `pro_rate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for yes 1 for no',
  `sweep_id` int(1) NOT NULL DEFAULT '0' COMMENT 'sweeplist table id',
  `reseller_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Accounts table id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:active,1:Inactive',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
INSERT INTO `ci_sessions` VALUES ('c33135e4ba5266e6ba7b0fda696f9222','192.168.1.38','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0',1469548489,'a:20:{s:9:\"user_data\";s:0:\"\";s:9:\"user_logo\";s:8:\"logo.png\";s:11:\"user_header\";s:41:\"ASTPP - Open Source Voip Billing Solution\";s:11:\"user_footer\";s:51:\"Inextrix Technologies Pvt. Ltd All Rights Reserved.\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:48:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2016-07-25 00:00:01\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:43:\"drwcmIaIlzzUaQ9PwgOGRn2KcKmSq44tWvKGgHfkpl0\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:5:\"Admin\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:8:\"interval\";s:1:\"0\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"2016-07-26 11:26:24\";s:6:\"expiry\";s:19:\"2046-07-25 11:26:24\";s:12:\"validfordays\";s:5:\"60000\";s:15:\"local_call_cost\";s:7:\"0.00000\";s:16:\"pass_link_status\";s:1:\"0\";s:10:\"local_call\";s:1:\"0\";s:14:\"charge_per_min\";s:1:\"1\";s:12:\"is_recording\";s:1:\"0\";s:19:\"allow_ip_management\";s:1:\"0\";}s:16:\"permited_modules\";s:869:\"a:42:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:5:\"email\";i:6;s:7:\"invoice\";i:7;s:15:\"periodiccharges\";i:8;s:12:\"refillreport\";i:9;s:7:\"charges\";i:10;s:3:\"did\";i:11;s:6:\"refill\";i:12;s:5:\"price\";i:13;s:11:\"origination\";i:14;s:7:\"package\";i:15;s:7:\"package\";i:16;s:5:\"trunk\";i:17;s:11:\"termination\";i:18;s:12:\"fssipdevices\";i:19;s:9:\"fsgateway\";i:20;s:12:\"fssipprofile\";i:21;s:8:\"fsserver\";i:22;s:5:\"ipmap\";i:23;s:6:\"animap\";i:24;s:14:\"customerReport\";i:25;s:14:\"resellerReport\";i:26;s:14:\"providerReport\";i:27;s:8:\"customer\";i:28;s:8:\"reseller\";i:29;s:8:\"provider\";i:30;s:8:\"livecall\";i:31;s:5:\"email\";i:32;s:7:\"invoice\";i:33;s:5:\"taxes\";i:34;s:8:\"template\";i:35;s:7:\"country\";i:36;s:8:\"currency\";i:37;s:8:\"database\";i:38;s:13:\"Configuration\";i:39;s:13:\"configuration\";i:40;s:8:\"opensips\";i:41;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:6698:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}i:3;a:4:{s:10:\"menu_label\";s:10:\"Email Mass\";s:10:\"module_url\";s:17:\"email/email_mass/\";s:6:\"module\";s:5:\"email\";s:10:\"menu_image\";s:9:\"email.jpg\";}}}s:10:\"Accounting\";a:1:{i:0;a:5:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:13:\"Refill Report\";s:10:\"module_url\";s:21:\"reports/refillreport/\";s:6:\"module\";s:12:\"refillreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}i:3;a:4:{s:10:\"menu_label\";s:15:\"Charges History\";s:10:\"module_url\";s:24:\"reports/charges_history/\";s:6:\"module\";s:7:\"charges\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}i:4;a:4:{s:10:\"menu_label\";s:13:\"Refill Coupon\";s:10:\"module_url\";s:33:\"refill_coupon/refill_coupon_list/\";s:6:\"module\";s:6:\"refill\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:29:\"rates/origination_rates_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:29:\"rates/termination_rates_list/\";s:6:\"module\";s:11:\"termination\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"SIP Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}i:4;a:4:{s:10:\"menu_label\";s:11:\"IP Settings\";s:10:\"module_url\";s:19:\"ipmap/ipmap_detail/\";s:6:\"module\";s:5:\"ipmap\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:5;a:4:{s:10:\"menu_label\";s:9:\"Caller ID\";s:10:\"module_url\";s:21:\"animap/animap_detail/\";s:6:\"module\";s:6:\"animap\";s:10:\"menu_image\";s:11:\"Gateway.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:17:\"summary/customer/\";s:6:\"module\";s:8:\"customer\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:17:\"summary/reseller/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:17:\"summary/provider/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:7:\"cdr.png\";}}i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Email History\";s:10:\"module_url\";s:25:\"email/email_history_list/\";s:6:\"module\";s:5:\"email\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:7:{i:0;a:4:{s:10:\"menu_label\";s:15:\"Company Profile\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}i:6;a:4:{s:10:\"menu_label\";s:7:\"Setting\";s:10:\"module_url\";s:28:\"systems/configuration/global\";s:6:\"module\";s:13:\"Configuration\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:14:\"advance_search\";i:0;s:25:\"refill_coupon_list_search\";i:0;s:10:\"did_search\";i:0;s:27:\"import_termination_rate_csv\";s:0:\"\";s:33:\"import_termination_rate_csv_error\";s:0:\"\";s:14:\"country_search\";i:0;s:15:\"currency_search\";i:0;}');
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
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countrycode`
--

LOCK TABLES `countrycode` WRITE;
/*!40000 ALTER TABLE `countrycode` DISABLE KEYS */;
INSERT INTO `countrycode` VALUES (2,'Alaska'),(3,'Albania'),(4,'Algeria'),(5,'AmericanSamoa'),(6,'Andorra'),(7,'Angola'),(8,'Antarctica'),(9,'Argentina'),(10,'Armenia'),(11,'Aruba'),(12,'Ascension'),(13,'Australia'),(14,'Austria'),(15,'Azerbaijan'),(16,'Bahrain'),(17,'Bangladesh'),(18,'Belarus'),(19,'Belgium'),(20,'Belize'),(21,'Benin'),(22,'Bhutan'),(23,'Bolivia'),(24,'Bosnia & Herzegovina'),(25,'Botswana'),(26,'Brazil'),(27,'Brunei Darussalam'),(28,'Bulgaria'),(29,'Burkina Faso'),(30,'Burundi'),(31,'Cambodia'),(32,'Cameroon'),(33,'Canada'),(34,'Cape Verde Islands'),(35,'Central African Republic'),(36,'Chad'),(37,'Chile'),(38,'China'),(39,'Colombia'),(40,'Comoros'),(41,'Congo'),(42,'Cook Islands'),(43,'Costa Rica'),(44,'Croatia'),(45,'Cuba'),(46,'Cuba Guantanamo Bay'),(47,'Cyprus'),(48,'Czech Republic'),(49,'Denmark'),(50,'Diego Garcia'),(51,'Djibouti'),(52,'Dominican Republic'),(53,'East Timor'),(54,'Ecuador'),(55,'Egypt'),(56,'El Salvador'),(57,'Equatorial Guinea'),(58,'Eritrea'),(59,'Estonia'),(60,'Ethiopia'),(61,'Faroe Islands'),(62,'Fiji Islands'),(63,'Finland'),(64,'France'),(65,'French Guiana'),(66,'French Polynesia'),(67,'Gabonese Republic'),(68,'Gambia'),(69,'Georgia'),(70,'Germany'),(71,'Ghana'),(72,'Gibraltar'),(73,'Greece'),(74,'Greenland'),(75,'Guadeloupe'),(76,'Guam'),(77,'Guatemala'),(78,'Guinea'),(79,'Guyana'),(80,'Haiti'),(81,'Honduras'),(82,'Hong Kong'),(83,'Hungary'),(84,'Iceland'),(85,'India'),(86,'Indonesia'),(87,'Iran'),(88,'Iraq'),(89,'Ireland'),(90,'Israel'),(91,'Italy'),(92,'Jamaica'),(93,'Japan'),(94,'Jordan'),(95,'Kazakstan'),(96,'Kenya'),(97,'Kiribati'),(98,'Kuwait'),(99,'Kyrgyz Republic'),(100,'Laos'),(101,'Latvia'),(102,'Lebanon'),(103,'Lesotho'),(104,'Liberia'),(105,'Libya'),(106,'Liechtenstein'),(107,'Lithuania'),(108,'Luxembourg'),(109,'Macao'),(110,'Madagascar'),(111,'Malawi'),(112,'Malaysia'),(113,'Maldives'),(114,'Mali Republic'),(115,'Malta'),(116,'Marshall Islands'),(117,'Martinique'),(118,'Mauritania'),(119,'Mauritius'),(120,'MayotteIsland'),(121,'Mexico'),(122,'Midway Islands'),(123,'Moldova'),(124,'Monaco'),(125,'Mongolia'),(126,'Morocco'),(127,'Mozambique'),(128,'Myanmar'),(129,'Namibia'),(130,'Nauru'),(131,'Nepal'),(132,'Netherlands'),(133,'Netherlands Antilles'),(134,'New Caledonia'),(135,'New Zealand'),(136,'Nicaragua'),(137,'Niger'),(138,'Nigeria'),(139,'Niue'),(140,'Norfolk Island'),(141,'North Korea'),(142,'Norway'),(143,'Oman'),(144,'Pakistan'),(145,'Palau'),(146,'Palestinian Settlements'),(147,'Panama'),(148,'PapuaNew Guinea'),(149,'Paraguay'),(150,'Peru'),(151,'Philippines'),(152,'Poland'),(153,'Portugal'),(154,'Puerto Rico'),(155,'Qatar'),(156,'RÃ©unionIsland'),(157,'Romania'),(158,'Russia'),(159,'Rwandese Republic'),(160,'San Marino'),(161,'Saudi Arabia'),(162,'SÃ£o TomÃ© and Principe'),(163,'Senegal '),(164,'Serbia and Montenegro'),(165,'Seychelles Republic'),(166,'Sierra Leone'),(167,'Singapore'),(168,'Slovak Republic'),(169,'Slovenia'),(170,'Solomon Islands'),(171,'Somali Democratic Republic'),(172,'South Africa'),(173,'South Korea'),(174,'Spain'),(175,'Sri Lanka'),(176,'St Kitts - Nevis'),(177,'St. Helena'),(178,'St. Lucia'),(179,'St. Pierre & Miquelon'),(180,'St. Vincent & Grenadines'),(181,'Sudan'),(182,'Suriname'),(183,'Swaziland'),(184,'Sweden'),(185,'Switzerland'),(186,'Syria'),(187,'Taiwan'),(188,'Tajikistan'),(189,'Tanzania'),(190,'Thailand'),(191,'Togolese Republic'),(192,'Tokelau'),(193,'Tonga Islands'),(194,'Trinidad & Tobago'),(195,'Tunisia'),(196,'Turkey'),(197,'Turkmenistan'),(198,'Tuvalu'),(199,'Uganda'),(200,'Ukraine'),(201,'United Arab Emirates'),(202,'United Kingdom'),(203,'United States of America'),(204,'Uruguay'),(205,'Uzbekistan'),(206,'Vanuatu'),(207,'Venezuela'),(208,'Vietnam'),(209,'Wake Island'),(210,'Wallisand Futuna Islands'),(211,'Western Samoa'),(212,'Yemen'),(213,'Zambia'),(214,'Zimbabwe'),(215,'po[');
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
  `currencyrate` decimal(10,3) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `currency` (`currency`),
  KEY `currencyrate` (`currencyrate`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency`
--

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
INSERT INTO `currency` VALUES (1,'ALL','Albanian Lek',1.840,'2016-07-26 15:29:52'),(2,'DZD','Algerian Dinar',1.643,'2016-07-26 15:29:52'),(3,'XAL','Aluminium Ounces',0.000,'2016-07-25 05:04:43'),(4,'ARS','Argentine Peso',0.222,'2016-07-26 15:29:52'),(5,'AWG','Aruba Florin',0.027,'2016-07-25 05:04:43'),(6,'AUD','Australian Dollar',0.020,'2016-07-25 05:04:43'),(7,'BSD','Bahamian Dollar',0.015,'2016-07-25 05:04:43'),(8,'BHD','Bahraini Dinar',0.006,'2016-07-25 05:04:43'),(9,'BDT','Bangladesh Taka',1.164,'2016-07-26 06:03:04'),(10,'BBD','Barbados Dollar',0.030,'2016-07-25 05:04:43'),(11,'BYR','Belarus Ruble',297.474,'2016-07-26 15:29:52'),(12,'BZD','Belize Dollar',0.029,'2016-07-26 06:03:04'),(13,'BMD','Bermuda Dollar',0.015,'2016-07-25 05:04:43'),(14,'BTN','Bhutan Ngultrum',0.999,'2016-07-26 06:03:04'),(15,'BOB','Bolivian Boliviano',0.102,'2016-07-25 05:04:43'),(16,'BRL','Brazilian Real',0.049,'2016-07-26 06:03:04'),(17,'GBP','British Pound',0.011,'2016-07-25 05:04:43'),(18,'BND','Brunei Dollar',0.020,'2016-07-25 05:04:43'),(19,'BGN','Bulgarian Lev',0.026,'2016-07-25 05:04:43'),(20,'BIF','Burundi Franc',24.443,'2016-07-26 15:29:52'),(21,'KHR','Cambodia Riel',60.505,'2016-07-26 15:29:52'),(22,'CAD','Canadian Dollar',0.020,'2016-07-25 05:04:44'),(23,'KYD','Cayman Islands Dollar',0.012,'2016-07-25 05:04:44'),(24,'XOF','CFA Franc (BCEAO)',8.868,'2016-07-26 15:29:52'),(25,'XAF','CFA Franc (BEAC)',8.867,'2016-07-26 15:29:52'),(26,'CLP','Chilean Peso',9.823,'2016-07-26 15:29:52'),(27,'CNY','Chinese Yuan',0.099,'2016-07-25 05:04:44'),(28,'COP','Colombian Peso',45.614,'2016-07-26 15:29:52'),(29,'KMF','Comoros Franc',6.639,'2016-07-26 15:29:52'),(30,'XCP','Copper Ounces',0.007,'2016-07-25 05:04:44'),(31,'CRC','Costa Rica Colon',8.044,'2016-07-26 15:29:52'),(32,'HRK','Croatian Kuna',0.101,'2016-07-25 05:04:44'),(33,'CUP','Cuban Peso',0.015,'2016-07-25 05:04:44'),(34,'CYP','Cyprus Pound',0.008,'2016-07-25 05:04:44'),(35,'CZK','Czech Koruna',0.366,'2016-07-26 15:29:52'),(36,'DKK','Danish Krone',0.101,'2016-07-26 15:29:52'),(37,'DJF','Dijibouti Franc',2.627,'2016-07-26 15:29:52'),(38,'DOP','Dominican Peso',0.680,'2016-07-26 06:03:04'),(39,'XCD','East Caribbean Dollar',0.040,'2016-07-25 05:04:44'),(40,'ECS','Ecuador Sucre',371.471,'2016-07-26 15:29:52'),(41,'EGP','Egyptian Pound',0.132,'2016-07-25 05:04:44'),(42,'SVC','El Salvador Colon',0.130,'2016-07-25 05:04:44'),(43,'ERN','Eritrea Nakfa',0.229,'2016-07-26 06:03:04'),(44,'EEK','Estonian Kroon',0.000,'2013-03-23 09:03:23'),(45,'ETB','Ethiopian Birr',0.324,'2016-07-25 05:04:44'),(46,'EUR','Euro',0.014,'2016-07-25 05:04:45'),(47,'FKP','Falkland Islands Pound',0.011,'2016-07-25 05:04:45'),(48,'GMD','Gambian Dalasi',0.621,'2016-07-26 15:29:52'),(49,'GHC','Ghanian Cedi',897.330,'2015-01-23 12:55:12'),(50,'GIP','Gibraltar Pound',0.011,'2016-07-25 05:04:45'),(51,'XAU','Gold Ounces',0.000,'2016-07-25 05:04:45'),(52,'GTQ','Guatemala Quetzal',0.113,'2016-07-26 06:03:04'),(53,'GNF','Guinea Franc',132.095,'2016-07-26 15:29:52'),(54,'HTG','Haiti Gourde',0.936,'2016-07-26 15:29:52'),(55,'HNL','Honduras Lempira',0.337,'2016-07-26 06:03:05'),(56,'HKD','Hong Kong Dollar',0.115,'2016-07-25 05:04:45'),(57,'HUF','Hungarian ForINT',4.232,'2016-07-26 15:29:52'),(58,'ISK','Iceland Krona',1.801,'2016-07-26 15:29:52'),(59,'INR','Indian Rupee',1.000,'2016-07-25 05:04:42'),(60,'IDR','Indonesian Rupiah',195.100,'2016-07-26 15:29:52'),(61,'IRR','Iran Rial',446.731,'2016-07-26 15:29:52'),(62,'ILS','Israeli Shekel',0.057,'2016-07-25 05:04:45'),(63,'JMD','Jamaican Dollar',1.876,'2016-07-26 15:29:52'),(64,'JPY','Japanese Yen',1.552,'2016-07-26 15:29:52'),(65,'JOD','Jordanian Dinar',0.011,'2016-07-25 05:04:45'),(66,'KZT','Kazakhstan Tenge',5.261,'2016-07-26 15:29:52'),(67,'KES','Kenyan Shilling',1.504,'2016-07-26 06:03:05'),(68,'KRW','Korean Won',16.876,'2016-07-26 15:29:52'),(69,'KWD','Kuwaiti Dinar',0.005,'2016-07-25 05:04:45'),(70,'LAK','Lao Kip',120.059,'2016-07-26 15:29:52'),(71,'LVL','Latvian Lat',0.009,'2016-07-25 05:04:45'),(72,'LBP','Lebanese Pound',22.354,'2016-07-26 15:29:52'),(73,'LSL','Lesotho Loti',0.213,'2016-07-25 05:04:45'),(74,'LYD','Libyan Dinar',0.020,'2016-07-26 15:29:52'),(75,'LTL','Lithuanian Lita',0.045,'2016-07-25 05:04:45'),(76,'MOP','Macau Pataca',0.119,'2016-07-25 05:04:45'),(77,'MKD','Macedonian Denar',0.828,'2016-07-26 15:29:52'),(78,'MGF','Malagasy Franc',5.830,'2015-01-23 12:55:12'),(79,'MWK','Malawi Kwacha',10.541,'2016-07-26 15:29:52'),(80,'MYR','Malaysian Ringgit',0.060,'2016-07-25 05:04:45'),(81,'MVR','Maldives Rufiyaa',0.222,'2016-07-26 06:03:05'),(82,'MTL','Maltese Lira',0.840,'2015-01-23 12:55:12'),(83,'MRO','Mauritania Ougulya',5.245,'2016-07-26 15:29:52'),(84,'MUR','Mauritius Rupee',0.524,'2016-07-26 15:29:52'),(85,'MXN','Mexican Peso',0.280,'2016-07-26 15:29:52'),(86,'MDL','Moldovan Leu',0.293,'2016-07-26 06:03:05'),(87,'MNT','Mongolian Tugrik',30.327,'2016-07-26 15:29:52'),(88,'MAD','Moroccan Dirham',0.146,'2016-07-26 06:03:06'),(89,'MZM','Mozambique Metical',0.000,'2013-03-23 09:03:23'),(90,'NAD','Namibian Dollar',0.214,'2016-07-26 15:29:52'),(91,'NPR','Nepalese Rupee',1.594,'2016-07-26 15:29:52'),(92,'ANG','Neth Antilles Guilder',0.026,'2016-07-25 05:04:46'),(93,'TRY','New Turkish Lira',0.045,'2016-07-26 06:03:06'),(94,'NZD','New Zealand Dollar',0.021,'2016-07-25 05:04:46'),(95,'NIO','Nicaragua Cordoba',0.421,'2016-07-26 15:29:52'),(96,'NGN','Nigerian Naira',4.636,'2016-07-26 15:29:52'),(97,'NOK','Norwegian Krone',0.128,'2016-07-26 15:29:52'),(98,'OMR','Omani Rial',0.006,'2016-07-25 05:04:46'),(99,'XPF','Pacific Franc',1.606,'2016-07-26 06:03:06'),(100,'PKR','Pakistani Rupee',1.553,'2016-07-26 15:29:52'),(101,'XPD','Palladium Ounces',0.000,'2016-07-25 05:04:46'),(102,'PAB','Panama Balboa',0.015,'2016-07-25 05:04:46'),(103,'PGK','Papua New Guinea Kina',0.047,'2016-07-25 05:04:46'),(104,'PYG','Paraguayan Guarani',83.091,'2016-07-26 15:29:52'),(105,'PEN','Peruvian Nuevo Sol',0.050,'2016-07-26 15:29:52'),(106,'PHP','Philippine Peso',0.700,'2016-07-26 06:03:06'),(107,'XPT','Platinum Ounces',0.000,'2016-07-25 05:04:46'),(108,'PLN','Polish Zloty',0.059,'2016-07-25 05:04:46'),(109,'QAR','Qatar Rial',0.054,'2016-07-25 05:04:46'),(110,'ROL','Romanian Leu',33.340,'2015-01-09 12:36:25'),(111,'RON','Romanian New Leu',0.060,'2016-07-25 05:04:46'),(112,'RUB','Russian Rouble',0.983,'2016-07-26 15:29:52'),(113,'RWF','Rwanda Franc',11.040,'2016-07-26 15:29:52'),(114,'WST','Samoa Tala',0.038,'2016-07-25 05:04:46'),(115,'STD','Sao Tome Dobra',331.085,'2016-07-26 15:29:52'),(116,'SAR','Saudi Arabian Riyal',0.056,'2016-07-25 05:04:46'),(117,'SCR','Seychelles Rupee',0.190,'2016-07-26 15:29:52'),(118,'SLL','Sierra Leone Leone',81.724,'2016-07-26 15:29:52'),(119,'XAG','Silver Ounces',0.001,'2016-07-25 05:04:47'),(120,'SGD','Singapore Dollar',0.020,'2016-07-25 05:04:47'),(121,'SKK','Slovak Koruna',33.554,'2015-01-09 12:36:25'),(122,'SIT','Slovenian Tolar',3.217,'2016-07-26 15:29:52'),(123,'SOS','Somali Shilling',8.232,'2016-07-26 15:29:52'),(124,'ZAR','South African Rand',0.214,'2016-07-26 15:29:52'),(125,'LKR','Sri Lanka Rupee',2.169,'2016-07-26 15:29:52'),(126,'SHP','St Helena Pound',0.011,'2016-07-25 05:04:47'),(127,'SDD','Sudanese Dinar',40.366,'2015-01-23 12:55:12'),(128,'SRG','Surinam Guilder',0.000,'2013-03-23 09:03:23'),(129,'SZL','Swaziland Lilageni',0.214,'2016-07-26 15:29:52'),(130,'SEK','Swedish Krona',0.129,'2016-07-26 15:29:52'),(131,'CHF','Swiss Franc',0.015,'2016-07-25 05:04:47'),(132,'SYP','Syrian Pound',3.203,'2016-07-26 15:29:52'),(133,'TWD','Taiwan Dollar',0.477,'2016-07-26 15:29:52'),(134,'TZS','Tanzanian Shilling',32.422,'2016-07-26 15:29:52'),(135,'THB','Thai Baht',0.519,'2016-07-26 15:29:52'),(136,'TOP','Tonga Paanga',0.034,'2016-07-25 05:04:47'),(137,'TTD','Trinidad&Tobago Dollar',0.099,'2016-07-25 05:04:47'),(138,'TND','Tunisian Dinar',0.033,'2016-07-25 05:04:47'),(139,'USD','U.S. Dollar',0.015,'2016-07-25 05:04:47'),(140,'AED','UAE Dirham',0.055,'2016-07-26 15:29:52'),(141,'UGX','Ugandan Shilling',50.193,'2016-07-26 15:29:52'),(142,'UAH','Ukraine Hryvnia',0.368,'2016-07-25 05:04:47'),(143,'UYU','Uruguayan New Peso',0.444,'2016-07-26 06:03:06'),(144,'VUV','Vanuatu Vatu',1.583,'2016-07-26 06:03:06'),(145,'VEB','Venezuelan Bolivar',0.000,'2013-03-23 09:03:23'),(146,'VND','Vietnam Dong',331.352,'2016-07-26 15:29:52'),(147,'YER','Yemen Riyal',3.711,'2016-07-26 15:29:52'),(148,'ZMK','Zambian Kwacha',76.442,'2016-06-25 09:51:32'),(149,'ZWD','Zimbabwe Dollar',0.000,'2013-03-23 09:03:23'),(150,'GYD','Guyana Dollar',3.045,'2016-07-26 15:29:52'),(151,'458','uioui',0.000,'2015-01-23 12:55:12'),(152,'h45','g34y35hg 4h45h45',100000.000,'2016-06-15 06:15:57'),(153,'453','45435',3445.659,'2016-06-15 11:51:12'),(154,'526','test',100000.000,'2016-06-15 11:54:06'),(155,'34g','test2',0.000,'2016-06-15 11:58:08'),(156,'fhd','fdg',0.000,'2016-06-15 12:00:32'),(157,'dfh','fdsdg',0.000,'2016-06-15 12:00:47'),(159,'tes','test',9999999.000,'2016-06-24 03:57:09');
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
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
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `default_templates`
--

LOCK TABLES `default_templates` WRITE;
/*!40000 ALTER TABLE `default_templates` DISABLE KEYS */;
INSERT INTO `default_templates` VALUES (1,'voip_account_refilled','Account credited Successfully ','<p>HI #NAME#,</p>\n<p>Your account has been successfully credited with #REFILLBALANCE#.</p>\n<p>Your account available balance is #BALANCE#.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support department at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 06:45:51',0),(3,'email_add_user','Welcome to ASTPP','<p>Welcome #NAME#,</p>\n<p>Your new account has been created.</p>\n<p>You can login into customer portal using below login credential,</p>\n<p>Account Number : #NUMBER#</p>\n<p>Password : #PASSWORD#</p>\n<p>Url of Portal : #LINK#</p>\n<p>You can also use our mobile application, Add Google, iOS and Windows mobile application icons to download.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:25:30',0),(4,'add_sip_device','Sip device added successfully','<p>Hi #NAME#,</p>\n<p>New Sip device has been added in your account successfully.</p>\n<p>Here is your sip device information,</p>\n<p>Username : #USERNAME#</p>\n<p>Password : #PASSWORD# Additional information :</p>\n<p>SIP Server :192.168.1.2&nbsp; SIP Port : 5060 Preferable codecs : PCMU, PCMA, G729</p>\n<p>You can also use our mobile application, Add Google, iOS and Windows mobile application icons to download.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:26:34',0),(8,'email_add_did','DID #DIDNUMBER# assigned to your account #NUMBER#','<p>Hi #NAME#,</p>\n<p>A DID number #DIDNUMBER# has been assigned to your account.</p>\n<p>You can now login into your account and set destination.</p>\n<p>More Information about DID : DID Country : #COUNTRYNAME# Setup fee : #SETUPFEE# Monthly Fee : #MONTHLYFEE# Concurrent calls supported : #MAXCHANNEL#</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:27:19',0),(9,'email_remove_did','DID #DIDNUMBER# unassigned from your account #NUMBER#','<p>Hi #NAME#,</p>\n<p>A DID number #DIDNUMBER# has been unassigned from your account.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:27:36',0),(10,'email_new_invoice','Invoice created #INVOICE_NUMBER#','<p>Hi #NAME#,</p>\n<p>A new invoice has been generated into your account of #AMOUNT#.</p>\n<p>Invoice Information :</p>\n<p>Invoice Date : #INVOICE_DATE#</p>\n<p>Invoice Number : #INVOICE_NUMBER#</p>\n<p>Due Amount : #AMOUNT#</p>\n<p>Due Date : #DUE_DATE#</p>\n<p>You can login into customer portal and pay the invoice.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:28:15',0),(11,'email_low_balance','Low Balance notification #NUMBER#','<p>Hi #NAME#,</p>\n<p>This is a quick notification about the low balance of #AMOUNT# in your account.</p>\n<p>Please refill you account from our website to ensure your services will remain consistent.</p>\n<p>You can login into customer portal and refill your account.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:28:55',0),(12,'email_signup_confirmation','Confirmation to activate account','<p>Hi #NAME#,</p>\n<p>Thanks for sign-up with us,</p>\n<p>Please click on below link to active your account and complete registration.</p>\n<p>#LINK#</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:29:42',0),(13,'email_forgot_user','Your account password changed','<p>Hi #NAME#,</p>\n<p>Your account password has been changed.</p>\n<p>Please see your new password mentioned below: #PASSWORD#</p>\n<p>Henceforth,Please use the latest password.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:30:22',0),(14,'email_forgot_confirmation','Reset your password','<p>Hi #NAME#,</p>\n<p>Please click on below link to reset your password.</p>\n<p>#LINK#</p>\n<p>If you have not raised request to reset password then please contact us immediately.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-14 07:31:08',0),(15,'add_subscription','#NAME#, New service added to your account','<p>HI #NAME#</p>\n<p>A new service has been addd to your account.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-18 09:07:17',0),(16,'remove_subscription','#NAME#, Service has been removed from your account','<p>HI #NAME#</p>\n<p>A service has been removed from your account.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-18 09:07:45',0),(17,'add_package','#NAME#, New call package added to your account','<p>HI #NAME#</p>\n<p>A new call package has been added to your account.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-18 09:08:41',0),(18,'remove_package','#NAME#, Call package has been rmeoved from your account','<p>HI #NAME#</p>\n<p>A call package has been removed from your account.</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-18 09:08:16',0),(19,'voip_child_account_refilled','Account credited Successfully ','<p>HI #NAME#,</p>\n<p>Your account has been successfully credited with #REFILLBALANCE# due to recharge of the #ENTITY# account.(#ACCOUNTNUMBER#)</p>\n<p>For more info,</p>\n<p>Please visit on our website #COMPANY_WEBSITE# or contact to our support department at #COMPANY_EMAIL#.</p>\n<p>Thanks, #COMPANY_NAME#</p>','2016-10-18 09:06:36',0);
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
  `number` varchar(40) NOT NULL DEFAULT '',
  `accountid` int(11) DEFAULT '0' COMMENT 'Accounts table id',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `monthlycost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `cost` double(10,5) NOT NULL DEFAULT '0.00000',
  `init_inc` int(11) NOT NULL DEFAULT '0',
  `inc` int(4) NOT NULL,
  `extensions` varchar(180) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  `provider_id` int(11) NOT NULL DEFAULT '0',
  `country_id` int(3) NOT NULL DEFAULT '0',
  `province` varchar(20) NOT NULL DEFAULT '',
  `city` varchar(20) NOT NULL DEFAULT '',
  `prorate` int(1) NOT NULL DEFAULT '0',
  `setup` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `limittime` int(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  `disconnectionfee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `variables` mediumtext NOT NULL,
  `options` varchar(40) DEFAULT NULL,
  `maxchannels` int(4) NOT NULL DEFAULT '0',
  `chargeonallocation` int(1) NOT NULL DEFAULT '1',
  `allocation_bill_status` int(1) NOT NULL DEFAULT '0',
  `dial_as` varchar(40) NOT NULL DEFAULT '',
  `call_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'call type',
  `inuse` int(4) NOT NULL DEFAULT '0',
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `charge_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `freeswitch_port` varchar(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Active , 1= inactive',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freeswich_servers`
--

LOCK TABLES `freeswich_servers` WRITE;
/*!40000 ALTER TABLE `freeswich_servers` DISABLE KEYS */;
INSERT INTO `freeswich_servers` VALUES (1,'127.0.0.1','ClueCon','8021',0,'2016-07-26 15:25:07','2016-07-26 15:25:07');
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
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for Active 1 for Inactive',
  `dialplan_variable` varchar(500) NOT NULL,
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateways`
--

LOCK TABLES `gateways` WRITE;
/*!40000 ALTER TABLE `gateways` DISABLE KEYS */;
INSERT INTO `gateways` VALUES (1,1,'YourProvider','{\"username\":\"USERNAME\",\"password\":\"PASSWORD\",\"proxy\":\"sip.provider.com\",\"register\":\"false\",\"caller-id-in-from\":\"true\"}','2016-07-25 10:59:26',0,0,'','2016-07-26 15:21:09');
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
  `invoice_prefix` varchar(11) NOT NULL DEFAULT 'INV_',
  `invoice_start_from` int(11) NOT NULL DEFAULT '1',
  `logo` varchar(100) NOT NULL,
  `invoice_due_notification` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:enable,1:disable',
  `invoice_notification` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:enable,1:disable',
  `interval` varchar(11) NOT NULL,
  `notify_before_day` int(11) NOT NULL DEFAULT '1',
  `invoice_taxes_number` varchar(100) NOT NULL DEFAULT 'ABN 12 345 678 901',
  `domain` varchar(100) NOT NULL,
  `website_title` varchar(100) NOT NULL,
  `website_footer` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_conf`
--

LOCK TABLES `invoice_conf` WRITE;
/*!40000 ALTER TABLE `invoice_conf` DISABLE KEYS */;
INSERT INTO `invoice_conf` VALUES (1,1,'iNextrix Technologies Pvt. Ltd.','Lilamani Corporate Heights, Nava Vadaj','Ahmedabad','Gujarat','India','380014','+1-855-580-1802','','sales@inextrix.com','www.inextrix.com','INV_',1,'',1,1,'7',1,'ABC 435 1XX 8XX 3XX','www.inextrix.com','iNextrix Technologies Pvt. Ltd.','iNextrix Technologies Pvt. Ltd.');
/*!40000 ALTER TABLE `invoice_conf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_details`
--

DROP TABLE IF EXISTS `invoice_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `invoiceid` int(11) NOT NULL DEFAULT '0',
  `item_id` varchar(25) NOT NULL DEFAULT '0',
  `item_type` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `debit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `created_date` datetime NOT NULL,
  `generate_type` int(10) NOT NULL DEFAULT '0' COMMENT '0:Auto 1:manually',
  `before_balance` varchar(100) NOT NULL DEFAULT '0',
  `after_balance` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_details`
--

LOCK TABLES `invoice_details` WRITE;
/*!40000 ALTER TABLE `invoice_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_prefix` varchar(25) NOT NULL,
  `invoiceid` varchar(255) NOT NULL,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `from_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `to_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `due_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:paid,1:unpaid,2:partial_payment',
  `invoice_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` enum('I','R') NOT NULL DEFAULT 'I' COMMENT 'I => Invoice R=> Receipt',
  `amount` varchar(10) NOT NULL DEFAULT '0.00000',
  `balance` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `generate_type` int(10) NOT NULL DEFAULT '0' COMMENT '0:Auto 1:manually',
  `confirm` int(10) DEFAULT '0' COMMENT '0:not conform 1:conform',
  `notes` longtext NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Not delete 1:delete',
  `saving_report` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:not send 1:send',
  `invoice_note` longtext NOT NULL,
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
-- Table structure for table `ip_map`
--

DROP TABLE IF EXISTS `ip_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `ip` varchar(30) NOT NULL DEFAULT '',
  `accountid` int(11) NOT NULL DEFAULT '0' COMMENT 'Accounts table id',
  `pricelist_id` int(4) NOT NULL DEFAULT '0',
  `prefix` varchar(20) NOT NULL DEFAULT '',
  `context` varchar(20) NOT NULL DEFAULT 'default',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Active,1-inactive',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
-- Table structure for table `mail_details`
--

DROP TABLE IF EXISTS `mail_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_details` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subject` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `from` varchar(100) NOT NULL,
  `to` varchar(100) NOT NULL,
  `attachment` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : Send 1: Not send',
  `template` varchar(100) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_details`
--

LOCK TABLES `mail_details` WRITE;
/*!40000 ALTER TABLE `mail_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_details` ENABLE KEYS */;
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
  `priority` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_modules`
--

LOCK TABLES `menu_modules` WRITE;
/*!40000 ALTER TABLE `menu_modules` DISABLE KEYS */;
INSERT INTO `menu_modules` VALUES (1,'Customers','customer','accounts/customer_list/','Accounts','ListAccounts.png','0',10.1),(2,'Resellers','reseller','accounts/reseller_list/','Accounts','reseller.png','0',10.2),(3,'Customers','provider','accounts/customer_list/','Accounts','ListAccounts.png','0',0),(4,'Admins','admin','accounts/admin_list/','Accounts','ListAccounts.png','0',10.3),(5,'Admins','subadmin','accounts/admin_list/','Accounts','ListAccounts.png','0',0),(7,'Subscriptions ','periodiccharges','charges/periodiccharges/','Accounting','PeriodicCharges.png','0',20.2),(8,'Invoices','invoice','invoices/invoice_list/','Accounting','InvoiceList.png','0',20.1),(9,'Company Profile','invoice','invoices/invoice_conf/','Configuration','InvoiceConf.png','0',80.1),(10,'Calling Cards','callingcards','callingcards/callingcards_list/','Calling Cards','ListCards.png','0',30.1),(11,'Card Brands','brands','callingcards/brands/','Calling Cards','CCBand.png','0',30.2),(12,'Call Report','callingcards','callingcards/callingcards_cdrs/','Calling Cards','CallingCardCDR\'s.png','0',30.3),(13,'DIDs','did','did/did_list/','DIDs','ManageDIDs.png','0',40),(14,'Trunks','trunk','trunk/trunk_list/','Carriers','Trunks.png','0',55),(15,'Termination Rates','termination','rates/termination_rates_list/','Carriers','OutboundRoutes.png','0',56),(16,'Rate Groups','price','pricing/price_list/','Tariff','pricelist.png','0',51),(17,'Origination Rates','origination','rates/origination_rates_list/','Tariff','Routes.png','0',52),(18,'Packages','package','package/package_list/','Tariff','packages.png','Packages',53),(19,'Customer','customerReport','reports/customerReport/','Call Reports','cdr.png','Detail Reports',70.1),(20,'Live Call Report','livecall','freeswitch/livecall_report/','Call Reports','cdr.png','0',72),(21,'Reseller','resellerReport','reports/resellerReport/','Call Reports','cdr.png','Detail Reports',70.2),(22,'Provider Outbound','providerReport','reports/providerReport/','Call Reports','cdr.png','Detail Reports',70.3),(25,'SIP Devices','fssipdevices','freeswitch/fssipdevices/','Switch','Devices.png','0',60.1),(26,'Configuration','configuration','systems/configuration/','System Configuration','Configurations.png','System',90.1),(27,'Taxes','taxes','taxes/taxes_list/','Configuration','AccountTaxes.png','0',80.2),(28,'Email Templates','template','systems/template/','Configuration','TemplateManagement.png','0',80.3),(29,'Opensips devices','opensips','opensips/opensips_list/','Opensips','OpensipDevices.png','0',90.2),(30,'Dispatcher list','dispatcher','opensips/dispatcher_list/','Opensips','Dispatcher.png','0',90.3),(31,'Invoices','user','user/user_invoices_list/','Billing','ListAccounts.png','0',1.1),(32,'DIDs','user','user/user_didlist/','DIDs','ManageDIDs.png','0',2.1),(33,'Caller ID','user','user/user_animap_list/','Configuration','Providers.png','0',3.3),(34,'CDRs Reports','user','user/user_cdrs_report/','Reports','cdr.png','0',4.1),(35,'Refill Reports','user','user/user_refill_report/','Reports','PaymentReport.png','0',4.2),(36,'SIP Devices','user','user/user_sipdevices/','Configuration','freeswitch.png','0',3.2),(37,'My Rates','user','user/user_rates_list/','Rates','Routes.png','0',6.1),(38,'Reseller ','reseller','summary/reseller/','Call Reports','cdr.png','Summary Reports',71.3),(39,'Provider','provider','summary/provider/','Call Reports','cdr.png','Summary Reports',71.4),(40,'Refill Report','refillreport','reports/refillreport/','Accounting','PaymentReport.png','0',20.3),(41,'Gateways','fsgateway','freeswitch/fsgateway/','Switch','Gateway.png','0',60.2),(42,'SIP Profiles ','fssipprofile','freeswitch/fssipprofile/','Switch','SipProfiles.png','0',60.3),(43,'Freeswitch Server','fsserver','freeswitch/fsserver_list/','Switch','freeswitch.png','0',60.4),(44,'Usage Report','package','package/package_counter/','Tariff','Counters.png','Packages',54),(45,'Customer ','customer','summary/customer/','Call Reports','cdr.png','Summary Reports',71.2),(48,'Countries','country','systems/country_list/','Configuration','ManageDIDs.png','0',80.4),(49,'Currencies ','currency','systems/currency_list/','Configuration','ManageDIDs.png','0',80.5),(51,'Database Restore','database','systems/database_restore/','Configuration','Configurations.png','0',80.6),(52,'My Rates','resellersrates','rates/resellersrates_list/','Tariff','OutboundRoutes.png','0',52.1),(53,'IP Settings','ipmap','ipmap/ipmap_detail/','Switch','Gateway.png','0',60.5),(54,'Caller ID','animap','animap/animap_detail/','Switch','Gateway.png','0',60.6),(55,'Email History ','email','email/email_history_list/','Call Reports','ListAccounts.png','0',72.1),(56,'Email Mass','email','email/email_mass/','Accounts','email.jpg','0',10.4),(57,'Global','Configuration','systems/configuration/global','Configuration','','Settings',80.7),(58,'Opensips','Configuration','systems/configuration/opensips','Configuration','','Settings',80.8),(59,'Callingcard','Configuration','systems/configuration/callingcard','Configuration','','Settings',80.9),(60,'Freeswitch','Configuration','systems/configuration/freeswitch','Configuration','','Settings',80.1),(61,'Paypal','Configuration','systems/configuration/paypal','Configuration','','Settings',80.11),(62,'Email','Configuration','systems/configuration/email','Configuration','','Settings',80.12),(63,'Fund Transfer','customer','user/user_fund_transfer/','Payment','ListAccounts.png','0',5.1),(64,'Recharge','user','user/user_payment/','Payment','ListAccounts.png','0',5.2),(65,'Signup','Configuration','systems/configuration/signup/','Configuration','','Settings',81),(66,'Refill Coupon','refill','refill_coupon/refill_coupon_list/','Accounting','cdr.png','0',50),(67,'Refill Coupon','refill','user/user_refill_coupon_list/','Refill Coupon','cdr.png','0',8),(68,'Charges History','charges','reports/charges_history/','Accounting','PaymentReport.png','0',20.4),(69,'Setting','Configuration','systems/configuration/global','Configuration','Configurations.png','0',80.7),(70,'Charges History','user','user/user_charges_history/','Billing','PaymentReport.png','0',1.2),(71,'Subscriptions','user','user/user_subscriptions/','Billing','PaymentReport.png','0',1.3),(72,'IP Settings','user','user/user_ipmap/','Configuration','PaymentReport.png','0',3.1),(73,'Alert Threshold','user','user/user_alert_threshold/','Configuration','PaymentReport.png','0',3.4),(74,'Speed Dial','user','user/user_speeddial/','Configuration','freeswitch.png','0',3.5),(75,'Provider Outbound','user','user/user_provider_cdrs_report/','Reports','cdr.png','0',4.3);
/*!40000 ALTER TABLE `menu_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outbound_routes`
--

DROP TABLE IF EXISTS `outbound_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `outbound_routes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `pattern` varchar(15) DEFAULT NULL,
 `comment` varchar(80) DEFAULT '',
 `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
 `includedseconds` int(4) NOT NULL DEFAULT '0',
 `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
 `trunk_id` int(4) NOT NULL DEFAULT '0',
 `inc` int(4) NOT NULL,
 `strip` varchar(40) NOT NULL DEFAULT '',
 `prepend` varchar(40) NOT NULL DEFAULT '',
 `precedence` int(4) NOT NULL DEFAULT '0',
 `reseller_id` int(11) NOT NULL DEFAULT '0',
 `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
 `init_inc` int(11) NOT NULL DEFAULT '0',
 `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`id`),
 UNIQUE KEY `pattern2` (`pattern`,`trunk_id`),
 KEY `trunk` (`trunk_id`),
 KEY `pattern` (`pattern`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT  INTO `outbound_routes` (`id`, `pattern`, `comment`, `connectcost`,  `includedseconds`, `cost`, `trunk_id`, `inc`, `strip`, `prepend`,  `precedence`, `reseller_id`, `status`, `init_inc`, `creation_date`,  `last_modified_date`) VALUES
(1, '^1.*', 'USA', 0.00000, 0, 0.10000, 1, 60, '', '', 0, 0, 0, 30, '2016-07-26 20:12:02', '2016-07-26 20:12:49');

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outbound_routes`
--

LOCK TABLES `outbound_routes` WRITE;
/*!40000 ALTER TABLE `outbound_routes` DISABLE KEYS */;
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
  `destination` varchar(100) NOT NULL,
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
  `package_name` varchar(20) NOT NULL DEFAULT '0',
  `pricelist_id` int(4) NOT NULL DEFAULT '0' COMMENT 'pricelist table id',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `reseller_id` int(11) DEFAULT '0' COMMENT 'Accoun',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `applicable_for` varchar(10) NOT NULL DEFAULT '0' COMMENT '0 for outbound 1 for inbound adn 2 for both',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
-- Table structure for table `payment_transaction`
--

DROP TABLE IF EXISTS `payment_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `amount` decimal(10,5) NOT NULL,
  `tax` varchar(10) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `actual_amount` decimal(10,5) NOT NULL,
  `paypal_fee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `user_currency` varchar(50) NOT NULL,
  `currency_rate` decimal(10,5) NOT NULL COMMENT 'user currency rate against base currency rate',
  `transaction_details` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transaction`
--

LOCK TABLES `payment_transaction` WRITE;
/*!40000 ALTER TABLE `payment_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_transaction` ENABLE KEYS */;
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
  `payment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `paypalid` int(11) NOT NULL,
  `txn_id` varchar(25) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `refill_coupon_number` varchar(20) NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
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
  `name` varchar(30) NOT NULL DEFAULT '',
  `markup` int(3) NOT NULL DEFAULT '0',
  `routing_type` tinyint(1) NOT NULL DEFAULT '0',
  `initially_increment` int(4) NOT NULL,
  `inc` int(4) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active,1 for inactive,2 for delete',
  `reseller_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Accounts table id',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
INSERT INTO `pricelists` VALUES (1,'default',0,0,0,60,0,0,'2016-07-25 00:00:00','2016-07-26 00:00:00');
/*!40000 ALTER TABLE `pricelists` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `updateRates` AFTER UPDATE ON `pricelists`
 FOR EACH ROW BEGIN
   if new.status = '2'
   then
       Delete from routes where pricelist_id = new.id;
   end if;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `refill_coupon`
--

DROP TABLE IF EXISTS `refill_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refill_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` bigint(20) NOT NULL,
  `amount` decimal(10,5) NOT NULL,
  `description` varchar(55) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0=Active,1=Inactive,2-Inuse',
  `firstused` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `account_id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refill_coupon`
--

LOCK TABLES `refill_coupon` WRITE;
/*!40000 ALTER TABLE `refill_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `refill_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reseller_cdrs`
--

DROP TABLE IF EXISTS `reseller_cdrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reseller_cdrs` (
  `uniqueid` varchar(60) NOT NULL DEFAULT '',
  `accountid` int(11) DEFAULT '0',
  `callerid` varchar(30) NOT NULL DEFAULT '',
  `callednum` varchar(30) NOT NULL DEFAULT '',
  `billseconds` smallint(6) NOT NULL DEFAULT '0',
  `disposition` varchar(45) NOT NULL DEFAULT '',
  `callstart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `debit` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `cost` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `pricelist_id` smallint(6) NOT NULL DEFAULT '0',
  `package_id` smallint(6) NOT NULL DEFAULT '0',
  `pattern` varchar(20) NOT NULL,
  `notes` varchar(80) NOT NULL,
  `calltype` enum('STANDARD','DID','FREE','CALLINGCARD') NOT NULL DEFAULT 'STANDARD',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `rate_cost` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `reseller_code` varchar(20) NOT NULL DEFAULT '',
  `reseller_code_destination` varchar(80) NOT NULL DEFAULT '',
  `reseller_cost` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `call_direction` enum('outbound','inbound') NOT NULL,
  UNIQUE KEY `uk_uniquekey` (`uniqueid`,`reseller_id`),
  KEY `reseller_id` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cdrs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reseller_cdrs`
--

LOCK TABLES `reseller_cdrs` WRITE;
/*!40000 ALTER TABLE `reseller_cdrs` DISABLE KEYS */;
/*!40000 ALTER TABLE `reseller_cdrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reseller_pricing`
--

DROP TABLE IF EXISTS `reseller_pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reseller_pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `extensions` varchar(180) NOT NULL,
  `call_type` tinyint(1) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `monthlycost` double(10,5) NOT NULL DEFAULT '0.00000',
  `prorate` tinyint(10) NOT NULL DEFAULT '0',
  `setup` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(4) NOT NULL DEFAULT '0',
  `note` varchar(50) NOT NULL DEFAULT '',
  `disconnectionfee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `inc` int(4) NOT NULL,
  `init_inc` int(11) NOT NULL,
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `charge_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern` varchar(40) DEFAULT '',
  `comment` varchar(80) DEFAULT '',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(4) NOT NULL,
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `pricelist_id` int(4) DEFAULT '0',
  `inc` int(4) DEFAULT NULL,
  `reseller_id` int(11) DEFAULT '0',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  `trunk_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Trunk id for force routing',
  `init_inc` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pattern_2` (`pattern`,`pricelist_id`),
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
INSERT INTO `routes` VALUES (1,'^1.*','USA',0.00000,0,0.20000,1,60,0,0,0,0,30,'2016-07-26 15:11:50','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routing`
--

DROP TABLE IF EXISTS `routing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pricelist_id` int(11) NOT NULL,
  `trunk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routing`
--

LOCK TABLES `routing` WRITE;
INSERT INTO `routing` VALUES (1,1,1);
/*!40000 ALTER TABLE `routing` DISABLE KEYS */;
/*!40000 ALTER TABLE `routing` ENABLE KEYS */;
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
  `reseller_id` int(4) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `dir_params` mediumtext NOT NULL,
  `dir_vars` mediumtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:active,1:inactive',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `call_waiting` int(11) NOT NULL DEFAULT '0' COMMENT '0:Enable 1:Disable',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sip_devices`
--

LOCK TABLES `sip_devices` WRITE;
/*!40000 ALTER TABLE `sip_devices` DISABLE KEYS */;
INSERT INTO `sip_devices` VALUES (1,'4810338297',1,0,2,'{\"password\":\"aa0b9a\",\"vm-enabled\":0,\"vm-password\":\"aa0b9a\",\"vm-mailto\":\"\",\"vm-attach-file\":0,\"vm-keep-local-after-email\":0,\"vm-email-all-messages\":0}','{\"effective_caller_id_name\":\"ASTPP\",\"effective_caller_id_number\":\"4810338297\",\"user_context\":\"default\"}',0,'2016-07-26 15:19:43','0000-00-00 00:00:00',0);
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
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sip_profiles`
--

LOCK TABLES `sip_profiles` WRITE;
/*!40000 ALTER TABLE `sip_profiles` DISABLE KEYS */;
INSERT INTO `sip_profiles` VALUES (1,'default','$${local_ip_v4}','5060','{\"rtp-ip\":\"$${local_ip_v4}\",\"dialplan\":\"XML\",\"user-agent-string\":\"ASTPP\",\"debug\":\"0\",\"sip-trace\":\"no\",\"tls\":\"false\",\"inbound-reg-force-matching-username\":\"true\",\"disable-transcoding\":\"true\",\"all-reg-options-ping\":\"false\",\"unregister-on-options-fail\":\"true\",\"log-auth-failures\":\"true\",\"status\":\"0\",\"inbound-bypass-media\":\"false\",\"inbound-proxy-media\":\"false\",\"disable-transfer\":\"true\",\"enable-100rel\":\"false\",\"rtp-timeout-sec\":\"300\",\"dtmf-duration\":\"2000\",\"manual-redirect\":\"true\",\"aggressive-nat-detection\":\"true\",\"enable-timer\":\"false\",\"minimum-session-expires\":\"120\",\"session-timeout-pt\":\"1800\",\"auth-calls\":\"true\",\"apply-inbound-acl\":\"default\",\"inbound-codec-prefs\":\"PCMA,PCMU\",\"outbound-codec-prefs\":\"PCMA,PCMU\",\"inbound-late-negotiation\":\"false\",\"sip-capture\":\"no\",\"forward-unsolicited-mwi-notify\":\"false\",\"context\":\"default\",\"rfc2833-pt\":\"101\",\"rtp-timer-name\":\"soft\",\"hold-music\":\"$${hold_music}\",\"manage-presence\":\"true\",\"presence-hosts\":\"$${domain},$${local_ip_v4}\",\"presence-privacy\":\"$${presence_privacy}\",\"inbound-codec-negotiation\":\"generous\",\"auth-all-packets\":\"false\",\"ext-rtp-ip\":\"$${local_ip_v4}\",\"ext-sip-ip\":\"$${local_ip_v4}\",\"rtp-hold-timeout-sec\":\"1800\",\"force-register-domain\":\"$${domain}\",\"force-subscription-domain\":\"$${domain}\",\"force-register-db-domain\":\"$${domain}\",\"challenge-realm\":\"auto_from\",\"nonce-ttl\":\"60\",\"pass-callee-id\":\"false\"}','2015-01-21 17:25:01','0000-00-00 00:00:00',0,0);
/*!40000 ALTER TABLE `sip_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `speed_dial`
--

DROP TABLE IF EXISTS `speed_dial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `speed_dial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `speed_num` int(11) NOT NULL,
  `number` varchar(15) NOT NULL,
  `accountid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `speed_dial`
--

LOCK TABLES `speed_dial` WRITE;
/*!40000 ALTER TABLE `speed_dial` DISABLE KEYS */;
/*!40000 ALTER TABLE `speed_dial` ENABLE KEYS */;
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
INSERT INTO `sweeplist` VALUES (0,'Daily'),(2,'Monthly');
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
  `display_name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `field_type` varchar(250) NOT NULL DEFAULT 'default_system_input',
  `comment` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `reseller_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `group_title` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `reseller` (`reseller_id`),
  KEY `brand` (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system`
--

LOCK TABLES `system` WRITE;
/*!40000 ALTER TABLE `system` DISABLE KEYS */;
INSERT INTO `system` VALUES (11,'opensips_dbengine','Opensips DB Engine','MySQL','default_system_input','For now this must be MySQL',NULL,0,0,'opensips'),(12,'opensips','Opensips','1','enable_disable_option','Set enable to add opensips support',NULL,0,0,'opensips'),(13,'opensips_dbname','Opensips DB Name','opensips','default_system_input','Set opensips database name',NULL,0,0,'opensips'),(14,'opensips_dbuser','Opensips DB User','DB_USER','default_system_input','Set opensips database user',NULL,0,0,'opensips'),(15,'opensips_dbhost','Opensips DB Host','DB_HOST','default_system_input','Set opensips database host',NULL,0,0,'opensips'),(16,'opensips_dbpass','Opensips DB Pass','DB_PASSWORD','default_system_input','Set opensips database password',NULL,0,0,'opensips'),(17,'opensips_domain','Opensips Domain','127.0.0.1','default_system_input','Set opensips domain',NULL,0,0,'opensips'),(20,'company_website','Company Website','www.inextrix.com','default_system_input','Set your company website url.',NULL,0,0,'global'),(21,'company_name','Company Name','iNextrix Technologies. Pvt. Ltd.','default_system_input','Set your company name.',NULL,0,0,'global'),(22,'email','Email Notifications','1','enable_disable_option','Set enable to send email notifications',NULL,0,0,'email'),(24,'debug','Call Debug','0','enable_disable_option','To enable call debug, set it to Enable. Logs will appear at /var/log/astpp/astpp.log',NULL,0,0,'global'),(26,'starting_digit','Starting Digit','0','default_system_input','Set the digits that all calling cards must start with. 0=disabled. Example : 1234',NULL,0,0,'callingcard'),(35,'card_length','Card Length','10','default_system_input','Set number of digits for calling cards',NULL,0,0,'callingcard'),(42,'pin_length','Pin Length','6','default_system_input','Set number of digits for pin numbers',NULL,0,0,'callingcard'),(44,'decimal_points','Decimal Points','4','default_system_input','Set decimal points to use through out system',NULL,0,0,'global'),(47,'max_free_length','Max Free Length','100','default_system_input','Set maximum length (In minutes) for calls that are at no charge',NULL,0,0,'global'),(53,'card_retries','Card Retries','3','default_system_input','Set number of retries to validate card number',NULL,0,0,'callingcard'),(54,'pin_retries','Pin retries','3','default_system_input','Set number of retries to validate pin number',NULL,0,0,'callingcard'),(135,'calling_cards_rate_announce','Rate Announcement','0','enable_disable_option','Enable it to announce rate of the call',NULL,0,0,'callingcard'),(136,'calling_cards_timelimit_announce','Timelimit Announce','0','enable_disable_option','Enable it to announce the time-limit on call',NULL,0,0,'callingcard'),(140,'calling_cards_pin_input_timeout','Pin Input Timeout','15000','default_system_input','How long do we wait when entering the pin number? Specified in MS',NULL,0,0,'callingcard'),(141,'calling_cards_number_input_timeout','Card Input Timeout','15000','default_system_input','How long do we wait when entering the calling card number?  Specified in MS',NULL,0,0,'callingcard'),(142,'calling_cards_dial_input_timeout','Dial Input Timeout','15000','default_system_input','How long do we wait when entering the destination number in calling cards? Specified in MS',NULL,0,0,'callingcard'),(143,'calling_cards_general_input_timeout','General Input Timeout','15000','default_system_input','How long do we wait for input in general menus?  Specified in MS',NULL,0,0,'callingcard'),(144,'calling_cards_welcome_file','Welcome File','astpp-welcome.wav','default_system_input','Set your calling card welcome file',NULL,0,0,'callingcard'),(171,'call_max_length','Call Max Length','1440000','default_system_input','Set maximum length (In ms) for call.',NULL,0,0,'global'),(173,'cc_ani_auth','ANI Authentication','1','enable_disable_option','Set enable to use ANI Authentication',NULL,0,0,'callingcard'),(174,'base_currency','Base Currency','USD','base_currency','Set base currency of system.',NULL,0,0,'global'),(179,'default_timezone','Default Timezone','49','default_timezone','Set default timezone for accounts','2013-05-06 19:34:39',0,0,'global'),(181,'country','Default Country','203','country','Set default country for accounts',NULL,0,0,'global'),(183,'paypal_status','Paypal','0','enable_disable_option','Set enable to add paypal as payment gateway option',NULL,0,0,'paypal'),(184,'paypal_url','Paypal Url','https://www.paypal.com/cgi-bin/webscr','default_system_input','Set paypal live url',NULL,0,0,'paypal'),(185,'paypal_sandbox_url','Paypal Sandbox Url','https://www.sandbox.paypal.com/cgi-bin/webscr','default_system_input','Set paypal sandbox url for testing',NULL,0,0,'paypal'),(186,'paypal_id','Paypal Id','your@paypal.com','default_system_input','Set paypal live account id',NULL,0,0,'paypal'),(187,'paypal_sandbox_id','Paypal Sandbox Id','paypal@test.com','default_system_input','Set paypal sandbox account id for testing',NULL,0,0,'paypal'),(188,'paypal_mode','Paypal Mode','0','paypal_mode','Set paypal mode. Sandbox for testing',NULL,0,0,'paypal'),(189,'paypal_fee','Paypal Fee','0','enable_disable_option','Set who should pay paypal fee',NULL,0,0,'paypal'),(190,'paypal_tax','Paypal Tax','0','default_system_input','Set paypal tax rate (in percentage) apply to recharge amount',NULL,0,0,'paypal'),(191,'version','Version','3.0','default_system_input','Current version of ASTPP',NULL,0,0,'global'),(192,'ivr_count','IVR Count','2','default_system_input','Number of time IVR should play in call',NULL,0,0,'callingcard'),(193,'calling_cards_balance_announce','Balance Announcement','0','enable_disable_option','To enable balance playback in call',NULL,0,0,'callingcard'),(194,'cc_access_numbers','CC Access Numbers','2222,3333,6666','default_system_input','Add calling card access numbers with comma separation. Ex : 12345678,3581629',NULL,0,0,'callingcard'),(195,'did_global_translation','DID Global Translation','','default_system_input','If you wish to translate DID number with some defined number then use this feature. This will be applicable to all DIDs.\nEx: “011/2222” (You can define multiple translations like \"011/2222\",\"02/33\")\nThat means from called number 011 is replaced by 2222.','2015-05-05 00:00:00',0,0,'global'),(196,'smtp','SMTP','0','enable_disable_option','Set yes to use SMTP connection to send email and no to use sendmail connection to send email',NULL,0,0,'email'),(197,'smtp_host','SMTP Host','SMTP_HOST','default_system_input','Set SMTP hostname ',NULL,0,0,'email'),(198,'smtp_port','SMTP Port','465','default_system_input','Set SMTP port',NULL,0,0,'email'),(199,'smtp_user','SMTP User','SMTP_USER_NAME','default_system_input','Set SMTP username',NULL,0,0,'email'),(200,'smtp_pass','SMTP Pass','SMTP_USER_PASSWORD','default_system_input','Set SMTP user password',NULL,0,0,'email'),(201,'playback_audio_notification','Playback Audio Notification','1','enable_disable_option','Set enable to play audio notification in call',NULL,0,0,'global'),(202,'outbound_fax','Outbound Fax','1','enable_disable_option','Set yes to allow outbound fax in call',NULL,0,0,'global'),(203,'inbound_fax','Inboud Fax','1','enable_disable_option','Set enable to allow inbound fax in call',NULL,0,0,'global'),(204,'default_signup_rategroup','Default Rategroup','default','default_signup_rategroup','Set default rategroup for singup customers',NULL,0,0,'signup'),(205,'enable_signup','Enable Signup','0','enable_disable_option','Set enable to add signup module',NULL,0,0,'signup'),(206,'create_sipdevice','Create SIP Device','0','enable_disable_option','Set yes to create sip device when customer will do singup in system',NULL,0,0,'signup'),(207,'balance','Default Balance','0','default_system_input','Set balance for newly created customer',NULL,0,0,'signup'),(208,'refill_coupon_length','Refill Coupon Length','8','default_system_input','Set refill coupon generation length',NULL,0,0,'global'),(209,'minimum_fund_transfer','Minimum Fund Transfer','1','default_system_input','Set minimum amount for fund transfer',NULL,0,0,'global'),(210,'balance_announce','Balance Announcement','1','enable_disable_option','To enable balance playback in call',NULL,0,0,'global'),(211,'minutes_announce','Minutes Announcement','1','enable_disable_option','To enable minutes playback in call',NULL,0,0,'global'),(212,'voicemail_number','Voicemail Number','7777','default_system_input','Voicemail listen number',NULL,0,0,'global');
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
  `taxes_amount` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `taxes_rate` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `taxes_description` varchar(255) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  PRIMARY KEY (`id`),
  KEY `taxes_priority` (`taxes_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;
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
  `taxes_priority` tinyint(4) NOT NULL DEFAULT '0',
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
INSERT INTO `timezone` VALUES (1,'(GMT-12:00) International Date Line West','GMT-12:00',-43200),(2,'(GMT-11:00) Midway Island, Samoa','GMT-11:00',-39600),(3,'(GMT-10:00) Hawaii','GMT-10:00',-36000),(4,'(GMT-09:00) Alaska','GMT-09:00',-32400),(5,'(GMT-08:00) Pacific Time (US & Canada) Tijuana','GMT-08:00',-28800),(6,'(GMT-07:00) Arizona','GMT-07:00',-25200),(7,'(GMT-07:00) Chihuahua, La Paz, Mazatlan','GMT-07:00',-25200),(8,'(GMT-07:00) Mountain Time(US & Canada)','GMT-07:00',-25200),(9,'(GMT-06:00) Central America','GMT-06:00',-21600),(10,'(GMT-06:00) Central Time (US & Canada)','GMT-06:00',-21600),(11,'(GMT-06:00) Guadalajara, Mexico City, Monterrey','GMT-06:00',-21600),(12,'(GMT-06:00) Saskatchewan','GMT-06:00',-21600),(13,'(GMT-05:00) Bogota, Lima, Quito','GMT-05:00',-18000),(14,'(GMT-05:00) Eastern Time (US & Canada)','GMT-05:00',-18000),(15,'(GMT-05:00) Indiana (East)','GMT-05:00',-18000),(16,'(GMT-04:00) Atlantic Time (Canada)','GMT-04:00',-14400),(17,'(GMT-04:00) Caracas, La Paz','GMT-04:00',-14400),(18,'(GMT-04:00) Santiago','GMT-04:00',-14400),(19,'(GMT-03:30) NewFoundland','GMT-03:30',-12600),(20,'(GMT-03:00) Brasillia','GMT-03:00',-10800),(21,'(GMT-03:00) Buenos Aires, Georgetown','GMT-03:00',-10800),(22,'(GMT-03:00) Greenland','GMT-03:00',-10800),(23,'(GMT-03:00) Mid-Atlantic','GMT-03:00',-10800),(24,'(GMT-01:00) Azores','GMT-01:00',-3600),(25,'(GMT-01:00) Cape Verd Is.','GMT-01:00',-3600),(26,'(GMT) Casablanca, Monrovia','GMT+00:00',0),(27,'(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon,  London','GMT',0),(28,'(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna','GMT+01:00',3600),(29,'(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague','GMT+01:00',3600),(30,'(GMT+01:00) Brussels, Copenhagen, Madrid, Paris','GMT+01:00',3600),(31,'(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb','GMT+01:00',3600),(32,'(GMT+01:00) West Central Africa','GMT+01:00',3600),(33,'(GMT+02:00) Athens, Istanbul, Minsk','GMT+02:00',7200),(34,'(GMT+02:00) Bucharest','GMT+02:00',7200),(35,'(GMT+02:00) Cairo','GMT+02:00',7200),(36,'(GMT+02:00) Harare, Pretoria','GMT+02:00',7200),(37,'(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius','GMT+02:00',7200),(38,'(GMT+02:00) Jeruasalem','GMT+02:00',7200),(39,'(GMT+03:00) Baghdad','GMT+03:00',10800),(40,'(GMT+03:00) Kuwait, Riyadh','GMT+03:00',10800),(41,'(GMT+03:00) Moscow, St.Petersburg, Volgograd','GMT+03:00',10800),(42,'(GMT+03:00) Nairobi','GMT+03:00',10800),(43,'(GMT+03:30) Tehran','GMT+03:30',12600),(44,'(GMT+04:00) Abu Dhabi, Muscat','GMT+04:00',14400),(45,'(GMT+04:00) Baku, Tbillisi, Yerevan','GMT+04:00',14400),(46,'(GMT+04:30) Kabul','GMT+04:30',16200),(47,'(GMT+05:00) Ekaterinburg','GMT+05:00',18000),(48,'(GMT+05:00) Islamabad, Karachi, Tashkent','GMT+05:00',18000),(49,'(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi','GMT+05:30',19800),(50,'(GMT+05:45) Kathmandu','GMT+05:45',20700),(51,'(GMT+06:00) Almaty, Novosibirsk','GMT+06:00',21600),(52,'(GMT+06:00) Astana, Dhaka','GMT+06:00',21600),(53,'(GMT+06:00) Sri Jayawardenepura','GMT+06:00',21600),(54,'(GMT+06:30) Rangoon','GMT+06:30',23400),(55,'(GMT+07:00) Bangkok, Hanoi, Jakarta','GMT+07:00',25200),(56,'(GMT+07:00) Krasnoyarsk','GMT+07:00',25200),(57,'(GMT+08:00) Beijiing, Chongging, Hong Kong, Urumqi','GMT+08:00',28800),(58,'(GMT+08:00) Irkutsk, Ulaan Bataar','GMT+08:00',28800),(59,'(GMT+08:00) Kuala Lumpur, Singapore','GMT+08:00',28800),(60,'(GMT+08:00) Perth','GMT+08:00',28800),(61,'(GMT+08:00) Taipei','GMT+08:00',28800),(62,'(GMT+09:00) Osaka, Sapporo, Tokyo','GMT+09:00',32400),(63,'(GMT+09:00) Seoul','GMT+09:00',32400),(64,'(GMT+09:00) Yakutsk','GMT+09:00',32400),(65,'(GMT+09:00) Adelaide','GMT+09:00',32400),(66,'(GMT+09:30) Darwin','GMT+09:30',34200),(67,'(GMT+10:00) Brisbane','GMT+10:00',36000),(68,'(GMT+10:00) Canberra, Melbourne, Sydney','GMT+10:00',36000),(69,'(GMT+10:00) Guam, Port Moresby','GMT+10:00',36000),(70,'(GMT+10:00) Hobart','GMT+10:00',36000),(71,'(GMT+10:00) Vladivostok','GMT+10:00',36000),(72,'(GMT+11:00) Magadan, Solomon Is., New Caledonia','GMT+11:00',39600),(73,'(GMT+12:00) Auckland, Wellington','GMT+1200',43200),(74,'(GMT+12:00) Fiji, Kamchatka, Marshall Is.','GMT+12:00',43200),(75,'(GMT+13:00) Nuku alofa','GMT+13:00',46800);
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
  `tech` varchar(10) NOT NULL DEFAULT '',
  `gateway_id` int(4) NOT NULL DEFAULT '0',
  `failover_gateway_id` int(4) NOT NULL DEFAULT '0' COMMENT 'Fail over Gateway id',
  `failover_gateway_id1` int(4) NOT NULL DEFAULT '0',
  `provider_id` int(11) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `dialed_modify` mediumtext NOT NULL,
  `resellers_id` varchar(11) NOT NULL DEFAULT '0',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `maxchannels` int(4) NOT NULL DEFAULT '0',
  `inuse` int(4) NOT NULL DEFAULT '0',
  `codec` varchar(100) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `provider` (`provider_id`),
  KEY `resellers_id` (`resellers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trunks`
--

LOCK TABLES `trunks` WRITE;
/*!40000 ALTER TABLE `trunks` DISABLE KEYS */;
INSERT INTO `trunks` VALUES (1,'YourTrunk','',1,0,0,3,0,'','0',0,0,0,'PCMA,G729,PCMA','0000-00-00 00:00:00','2016-07-26 15:16:00');
/*!40000 ALTER TABLE `trunks` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `updateTerminationRates` AFTER UPDATE ON `trunks`
 FOR EACH ROW BEGIN
   if new.status = '2'
   then
        Delete from outbound_routes where trunk_id = new.id;
   end if;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
INSERT INTO `userlevels` VALUES (-1,'Administrator','1,2,4,5,3,8,9,13,14,15,16,17,18,19,20,21,22,25,26,27,28,7,29,30,45,38,39,40,41,42,43,44,48,49,51,53,54,55,56,66,68,69'),(0,'Customer','31,32,37,36,34,35,33,63,64,67,70,71,73,74'),(1,'Reseller','1,2,7,8,13,16,17,18,19,21,25,28,38,40,44,45,46,52,27,9,29,53,54,66,55,68'),(2,'Admin','1,2,3,4,5,7,8,9,13,14,15,16,17,18,19,20,21,22,25,26,27,28,29,30,38,40,41,42,43,44,45,65'),(3,'Provider','31,32,37,36,34,35,33,63,64,67,70,71,73,74,75'),(4,'Sub Admin','8,19,20,21,22,38,45'),(5,'CallShop','17');
/*!40000 ALTER TABLE `userlevels` ENABLE KEYS */;
UNLOCK TABLES;



UPDATE `default_templates` SET `template` = 'Hi #NAME#,

A DID number #DIDNUMBER# has been assigned to your account. You can now login into your account and set destination.

More Information about DID : 
DID Country : #COUNTRYNAME#
Setup fee : #SETUPFEE#
Monthly Fee : #MONTHLYFEE#
Concurrent calls supported : #MAXCHANNEL#

For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#

Thanks, 
#COMPANY_NAME#' WHERE `default_templates`.`id` =8;

UPDATE `default_templates` SET `template` = 'Hi #NAME#,

Your account password has been changed. Please see your new password mentioned below:

#PASSWORD#

Henceforth, Please use the latest password.

For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#

Thanks, 
#COMPANY_NAME#' WHERE `default_templates`.`id` =13;


UPDATE `default_templates` SET `subject` = 'Invoice created #INVOICE_NUMBER#' WHERE `default_templates`.`id` =10;


UPDATE `default_templates` SET `template` = 'Hi #NAME#,

A new invoice has been generated into your account of #AMOUNT#.

Invoice Information : 
Invoice Date : #INVOICE_DATE#
Invoice Number : #INVOICE_NUMBER#
Due Amount : #AMOUNT#
Due Date : #DUE_DATE#

You can login into customer portal and pay the invoice. 

For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#

Thanks, 
#COMPANY_NAME#' WHERE `default_templates`.`id` =10;



UPDATE `default_templates` SET `template` = 'Welcome #NAME#,

Your new account has been created. You can login into customer portal using below login credential, 

Account Number : #NUMBER#
Password : #PASSWORD#

Url of Portal : #LINK#

You can also use our mobile application, 
Add Google, iOS and Windows mobile application icons to download. 

For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.

Thanks, 
#COMPANY_NAME#

' WHERE `default_templates`.`id` =3;

INSERT  INTO  `menu_modules` ( `id` , `menu_label` , `module_name` , `module_url` , `menu_title` , `menu_image` , `menu_subtitle` , `priority` ) VALUES ( NULL ,  'Opensips',  'user',  'user/user_opensips/',  'Opensips',  'OpensipDevices.png',  '0',  '90.2');

UPDATE `userlevels` SET `module_permissions` = '31,32,37,36,34,35,33,63,64,67,70,71,73,74,76' WHERE `userlevels`.`userlevelid` =0;

ALTER TABLE `charge_to_account` ADD `status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `accountid`;

--
-- Added below query to remove default indexes of cdrs table and create new for better performance
--

ALTER TABLE cdrs
    DROP INDEX uniqueid,
    DROP INDEX user_id
;

CREATE INDEX cdr_index ON cdrs (callstart,reseller_id,type);

ALTER TABLE reseller_cdrs
    DROP INDEX uk_uniquekey,
    DROP INDEX reseller_id
;

CREATE INDEX rs_cdr_index ON reseller_cdrs (callstart,reseller_id);

--
-- ---------------------------------------------------------
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-26 12:53:03
