-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: astpp
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.14.04.1

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
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:active,1:inactive',
  `credit` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `sweep_id` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Sweep list table id',
  `creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `credit_limit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `posttoexternal` tinyint(1) NOT NULL DEFAULT '0',
  `balance` decimal(20,5) NOT NULL DEFAULT '0.00000',
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
  `notify_credit_limit` int(11) NOT NULL,
  `notify_flag` tinyint(1) NOT NULL,
  `notify_email` varchar(80) NOT NULL,
  `commission_rate` int(11) NOT NULL DEFAULT '0',
  `invoice_day` int(11) NOT NULL DEFAULT '0',
  `pin` varchar(20) NOT NULL,
  `first_used` datetime NOT NULL,
  `expiry` datetime NOT NULL,
  `validfordays` int(7) NOT NULL DEFAULT '3652',
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
INSERT INTO `accounts` VALUES (1,'admin',0,0,0,10000.00000,2,'2014-12-25 08:45:12',0.00000,0,0.00000,'admin','Administrator','','Your Company','ADDRESS','','','','',85,'','','your@email.com',1,139,1,'',-1,27,0,0,0,0,'0',0,0,'','0000-00-00 00:00:00','0000-00-00 00:00:00',60000),(2, '7335503421', 0, 1, 0, '0.00000', 2, '2015-01-23 13:22:50', '0.00000', 0, '1.00000', '7335503421', 'IPComms', 'SIPTrunk', 'IPComms', '1925 Vaughn Rd', '', '30144', 'Georgia', 'Kennesaw', 203, '+1.678.460.4302', '+1.678.460.1475', 'no-reply@ipcomms.net', 0, 139, 0, '', 3, 8, 0, 0, 0, 1, '', 0, 1, '7335503421','0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),(3,'2457848300',0,1,0,0.00000,2,'2015-01-23 13:25:15',0.00000,0,0.00000,'2457848300','customer','','ASTPP','','','','','',71,'','','customer@astpp.org',0,139,0,'',0,26,0,0,0,1,'',0,23,'2457848300','0000-00-00 00:00:00','2025-01-23 13:24:12',0);
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
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Active,1-inactive',
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
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `charge_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
INSERT INTO `ci_sessions` VALUES ('009bf0617596e04b2cbb2b035a64e316','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020632,'a:10:{s:9:\"user_data\";s:0:\"\";s:9:\"user_name\";s:5:\"admin\";s:10:\"user_login\";b:1;s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('01c15c1aed6da540c19ec7edfbfd18fc','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020662,'a:10:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('0dea0ffd06830e846db652f8c4c4b96b','192.168.1.203','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36',1422018622,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('12823c589647517e4d3443b697693512','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020647,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('1e233fa15744836e9d0952834629475a','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020272,'a:11:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:15:\"currency_search\";i:0;}'),('288ccc82ab0f4d628b8fc904a2e4289c','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020632,'a:10:{s:9:\"user_data\";s:0:\"\";s:9:\"user_name\";s:5:\"admin\";s:10:\"user_login\";b:1;s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('29189640ab947f180ee62dfefef7f99a','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020650,'a:10:{s:9:\"user_data\";s:0:\"\";s:9:\"user_name\";s:5:\"admin\";s:10:\"user_login\";b:1;s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('29c302af73fce1bf3abfddd236e3ae7c','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020650,'a:10:{s:9:\"user_data\";s:0:\"\";s:9:\"user_name\";s:5:\"admin\";s:10:\"user_login\";b:1;s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('2db13fc1f40fa1850771eb92a5285bec','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020635,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('33293588925b01cc02428f146c198799','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020651,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('437996c26c66774d01442097410d9426','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422019056,'a:11:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:6:\"Master\";s:9:\"last_name\";s:5:\"Admin\";s:12:\"company_name\";s:31:\"Inextrix Technologies Pvt. Ltd.\";s:9:\"address_1\";s:80:\" iNextrix Technologies Pvt. Ltd. 21, 22, 202, Abhushan Complex, Stadium Road, Na\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:6:\"380014\";s:8:\"province\";s:7:\"Gujarat\";s:4:\"city\";s:9:\"Ahmedabad\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:13:\"+917940307595\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:20:\"contact@inextrix.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"49\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:14:\"advance_search\";i:0;}'),('4fd6c1631e540d1465d23d3f81c3e735','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020635,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('55819fac1a9ef934a1f77fee95a4e8ce','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020651,'a:10:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('5f0f20f8a7d8c4305bc8ea6357b3e826','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020205,'a:11:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:14:\"advance_search\";i:0;}'),('6161a63a4790e9a9145fb38714f67430','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020633,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('7853f52ae7d7f2301811aa8aa07a3029','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020634,'a:10:{s:9:\"user_data\";s:0:\"\";s:9:\"user_name\";s:5:\"admin\";s:10:\"user_login\";b:1;s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('7c2fcaafb4841faf39d4dbe26a4f89c3','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020648,'a:10:{s:9:\"user_data\";s:0:\"\";s:9:\"user_name\";s:5:\"admin\";s:10:\"user_login\";b:1;s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('82e83f4f1d111e2f10c1d6f490495729','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020632,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('9651aa9f20769205381018199e98592d','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020625,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('9b93384f8d0285c74f5aec80061d11a9','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020640,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('9eb014b134e7ef0a6295371929d469b9','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020646,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('a7567ccae20024aa5107686637139bad','192.168.1.35','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0',1422020377,'a:11:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:15:\"currency_search\";i:0;}'),('c60a7946e4e9e1ff020a0ba747e6d1d0','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422021928,'a:13:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:15:\"currency_search\";i:0;s:14:\"advance_search\";i:0;s:17:\"trunk_stat_search\";i:0;}'),('c9357266464f49364dbfdbbd049eaa13','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020652,''),('cd93487f3e4bf33304e15e325a43f32c','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422019627,'a:12:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:14:\"advance_search\";i:0;s:10:\"did_search\";i:0;}'),('d39ab5f70494f0abbb9f775f31ac8975','0.0.0.0','0',1422019801,''),('da2bb0547757a1a06b7cdbf68027b34e','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020639,'a:10:{s:9:\"user_data\";s:0:\"\";s:9:\"user_name\";s:5:\"admin\";s:10:\"user_login\";b:1;s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";}'),('db19cbd40edad942fd833a228a98431c','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020630,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}'),('dca2e8ed49e0fb1a18fdb66e2d252489','192.168.1.30','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',1422020348,'a:11:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:1;s:9:\"user_name\";s:5:\"admin\";s:9:\"logintype\";i:2;s:19:\"userlevel_logintype\";s:2:\"-1\";s:8:\"username\";s:5:\"admin\";s:11:\"accountinfo\";a:41:{s:2:\"id\";s:1:\"1\";s:6:\"number\";s:5:\"admin\";s:11:\"reseller_id\";s:1:\"0\";s:12:\"pricelist_id\";s:1:\"0\";s:6:\"status\";s:1:\"0\";s:6:\"credit\";s:11:\"10000.00000\";s:8:\"sweep_id\";s:1:\"2\";s:8:\"creation\";s:19:\"2014-12-25 08:45:12\";s:12:\"credit_limit\";s:7:\"0.00000\";s:14:\"posttoexternal\";s:1:\"0\";s:7:\"balance\";s:7:\"0.00000\";s:8:\"password\";s:5:\"admin\";s:10:\"first_name\";s:13:\"Administrator\";s:9:\"last_name\";s:0:\"\";s:12:\"company_name\";s:12:\"Your Company\";s:9:\"address_1\";s:7:\"ADDRESS\";s:9:\"address_2\";s:0:\"\";s:11:\"postal_code\";s:0:\"\";s:8:\"province\";s:0:\"\";s:4:\"city\";s:0:\"\";s:10:\"country_id\";s:2:\"85\";s:11:\"telephone_1\";s:0:\"\";s:11:\"telephone_2\";s:0:\"\";s:5:\"email\";s:14:\"your@email.com\";s:11:\"language_id\";s:1:\"1\";s:11:\"currency_id\";s:3:\"139\";s:11:\"maxchannels\";s:1:\"1\";s:13:\"dialed_modify\";s:0:\"\";s:4:\"type\";s:2:\"-1\";s:11:\"timezone_id\";s:2:\"27\";s:5:\"inuse\";s:1:\"0\";s:7:\"deleted\";s:1:\"0\";s:19:\"notify_credit_limit\";s:1:\"0\";s:11:\"notify_flag\";s:1:\"0\";s:12:\"notify_email\";s:1:\"0\";s:15:\"commission_rate\";s:1:\"0\";s:11:\"invoice_day\";s:1:\"0\";s:3:\"pin\";s:0:\"\";s:10:\"first_used\";s:19:\"0000-00-00 00:00:00\";s:6:\"expiry\";s:19:\"0000-00-00 00:00:00\";s:12:\"validfordays\";s:5:\"60000\";}s:16:\"permited_modules\";s:790:\"a:36:{i:0;s:8:\"provider\";i:1;s:8:\"subadmin\";i:2;s:8:\"customer\";i:3;s:8:\"reseller\";i:4;s:5:\"admin\";i:5;s:7:\"invoice\";i:6;s:15:\"periodiccharges\";i:7;s:13:\"paymentreport\";i:8;s:3:\"did\";i:9;s:5:\"price\";i:10;s:11:\"origination\";i:11;s:7:\"package\";i:12;s:7:\"package\";i:13;s:5:\"trunk\";i:14;s:16:\"terminationrates\";i:15;s:12:\"fssipdevices\";i:16;s:9:\"fsgateway\";i:17;s:12:\"fssipprofile\";i:18;s:8:\"fsserver\";i:19;s:14:\"customerReport\";i:20;s:14:\"resellerReport\";i:21;s:14:\"providerReport\";i:22;s:15:\"customersummary\";i:23;s:15:\"resellersummary\";i:24;s:15:\"providersummary\";i:25;s:10:\"trunkstats\";i:26;s:8:\"livecall\";i:27;s:7:\"invoice\";i:28;s:5:\"taxes\";i:29;s:8:\"template\";i:30;s:7:\"country\";i:31;s:8:\"currency\";i:32;s:8:\"database\";i:33;s:13:\"configuration\";i:34;s:8:\"opensips\";i:35;s:10:\"dispatcher\";}\";s:8:\"menuinfo\";s:5775:\"a:9:{s:8:\"Accounts\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:9:\"Customers\";s:10:\"module_url\";s:23:\"accounts/customer_list/\";s:6:\"module\";s:8:\"provider\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:1;a:4:{s:10:\"menu_label\";s:6:\"Admins\";s:10:\"module_url\";s:20:\"accounts/admin_list/\";s:6:\"module\";s:8:\"subadmin\";s:10:\"menu_image\";s:16:\"ListAccounts.png\";}i:2;a:4:{s:10:\"menu_label\";s:9:\"Resellers\";s:10:\"module_url\";s:23:\"accounts/reseller_list/\";s:6:\"module\";s:8:\"reseller\";s:10:\"menu_image\";s:12:\"reseller.png\";}}}s:10:\"Accounting\";a:1:{i:0;a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Invoices\";s:10:\"module_url\";s:22:\"invoices/invoice_list/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceList.png\";}i:1;a:4:{s:10:\"menu_label\";s:13:\"Subscriptions\";s:10:\"module_url\";s:24:\"charges/periodiccharges/\";s:6:\"module\";s:15:\"periodiccharges\";s:10:\"menu_image\";s:19:\"PeriodicCharges.png\";}i:2;a:4:{s:10:\"menu_label\";s:14:\"Payment Report\";s:10:\"module_url\";s:22:\"reports/paymentreport/\";s:6:\"module\";s:13:\"paymentreport\";s:10:\"menu_image\";s:17:\"PaymentReport.png\";}}}s:4:\"DIDs\";a:1:{i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:4:\"DIDs\";s:10:\"module_url\";s:13:\"did/did_list/\";s:6:\"module\";s:3:\"did\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}}}s:6:\"Tariff\";a:2:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:11:\"Rate Groups\";s:10:\"module_url\";s:19:\"pricing/price_list/\";s:6:\"module\";s:5:\"price\";s:10:\"menu_image\";s:13:\"pricelist.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Origination Rates\";s:10:\"module_url\";s:23:\"rates/origination_list/\";s:6:\"module\";s:11:\"origination\";s:10:\"menu_image\";s:10:\"Routes.png\";}}s:8:\"Packages\";a:2:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Packages\";s:10:\"module_url\";s:21:\"package/package_list/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"packages.png\";}i:1;a:4:{s:10:\"menu_label\";s:12:\"Usage Report\";s:10:\"module_url\";s:24:\"package/package_counter/\";s:6:\"module\";s:7:\"package\";s:10:\"menu_image\";s:12:\"Counters.png\";}}}s:8:\"Carriers\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:6:\"Trunks\";s:10:\"module_url\";s:17:\"trunk/trunk_list/\";s:6:\"module\";s:5:\"trunk\";s:10:\"menu_image\";s:10:\"Trunks.png\";}i:1;a:4:{s:10:\"menu_label\";s:17:\"Termination Rates\";s:10:\"module_url\";s:28:\"rates/terminationrates_list/\";s:6:\"module\";s:16:\"terminationrates\";s:10:\"menu_image\";s:18:\"OutboundRoutes.png\";}}}s:6:\"Switch\";a:1:{i:0;a:4:{i:0;a:4:{s:10:\"menu_label\";s:11:\"SIP Devices\";s:10:\"module_url\";s:24:\"freeswitch/fssipdevices/\";s:6:\"module\";s:12:\"fssipdevices\";s:10:\"menu_image\";s:11:\"Devices.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Gateways\";s:10:\"module_url\";s:21:\"freeswitch/fsgateway/\";s:6:\"module\";s:9:\"fsgateway\";s:10:\"menu_image\";s:11:\"Gateway.png\";}i:2;a:4:{s:10:\"menu_label\";s:12:\"Sip Profiles\";s:10:\"module_url\";s:24:\"freeswitch/fssipprofile/\";s:6:\"module\";s:12:\"fssipprofile\";s:10:\"menu_image\";s:15:\"SipProfiles.png\";}i:3;a:4:{s:10:\"menu_label\";s:17:\"Freeswitch Server\";s:10:\"module_url\";s:25:\"freeswitch/fsserver_list/\";s:6:\"module\";s:8:\"fsserver\";s:10:\"menu_image\";s:14:\"freeswitch.png\";}}}s:12:\"Call Reports\";a:3:{s:14:\"Detail Reports\";a:3:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:23:\"reports/customerReport/\";s:6:\"module\";s:14:\"customerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:23:\"reports/resellerReport/\";s:6:\"module\";s:14:\"resellerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:17:\"Provider Outbound\";s:10:\"module_url\";s:23:\"reports/providerReport/\";s:6:\"module\";s:14:\"providerReport\";s:10:\"menu_image\";s:7:\"cdr.png\";}}s:15:\"Summary Reports\";a:4:{i:0;a:4:{s:10:\"menu_label\";s:8:\"Customer\";s:10:\"module_url\";s:24:\"reports/customersummary/\";s:6:\"module\";s:15:\"customersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:1;a:4:{s:10:\"menu_label\";s:8:\"Reseller\";s:10:\"module_url\";s:24:\"reports/resellersummary/\";s:6:\"module\";s:15:\"resellersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:2;a:4:{s:10:\"menu_label\";s:8:\"Provider\";s:10:\"module_url\";s:24:\"reports/providersummary/\";s:6:\"module\";s:15:\"providersummary\";s:10:\"menu_image\";s:7:\"cdr.png\";}i:3;a:4:{s:10:\"menu_label\";s:11:\"Trunk Stats\";s:10:\"module_url\";s:22:\"statistics/trunkstats/\";s:6:\"module\";s:10:\"trunkstats\";s:10:\"menu_image\";s:14:\"TrunkStats.png\";}}i:0;a:1:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Live Call Report\";s:10:\"module_url\";s:27:\"freeswitch/livecall_report/\";s:6:\"module\";s:8:\"livecall\";s:10:\"menu_image\";s:7:\"cdr.png\";}}}s:13:\"Configuration\";a:1:{i:0;a:6:{i:0;a:4:{s:10:\"menu_label\";s:14:\"Invoice Config\";s:10:\"module_url\";s:22:\"invoices/invoice_conf/\";s:6:\"module\";s:7:\"invoice\";s:10:\"menu_image\";s:15:\"InvoiceConf.png\";}i:1;a:4:{s:10:\"menu_label\";s:5:\"Taxes\";s:10:\"module_url\";s:17:\"taxes/taxes_list/\";s:6:\"module\";s:5:\"taxes\";s:10:\"menu_image\";s:16:\"AccountTaxes.png\";}i:2;a:4:{s:10:\"menu_label\";s:15:\"Email Templates\";s:10:\"module_url\";s:17:\"systems/template/\";s:6:\"module\";s:8:\"template\";s:10:\"menu_image\";s:22:\"TemplateManagement.png\";}i:3;a:4:{s:10:\"menu_label\";s:9:\"Countries\";s:10:\"module_url\";s:21:\"systems/country_list/\";s:6:\"module\";s:7:\"country\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:4;a:4:{s:10:\"menu_label\";s:10:\"Currencies\";s:10:\"module_url\";s:22:\"systems/currency_list/\";s:6:\"module\";s:8:\"currency\";s:10:\"menu_image\";s:14:\"ManageDIDs.png\";}i:5;a:4:{s:10:\"menu_label\";s:16:\"Database Restore\";s:10:\"module_url\";s:25:\"systems/database_restore/\";s:6:\"module\";s:8:\"database\";s:10:\"menu_image\";s:18:\"Configurations.png\";}}}s:8:\"Opensips\";a:1:{i:0;a:2:{i:0;a:4:{s:10:\"menu_label\";s:16:\"Opensips devices\";s:10:\"module_url\";s:23:\"opensips/opensips_list/\";s:6:\"module\";s:8:\"opensips\";s:10:\"menu_image\";s:18:\"OpensipDevices.png\";}i:1;a:4:{s:10:\"menu_label\";s:15:\"Dispatcher list\";s:10:\"module_url\";s:25:\"opensips/dispatcher_list/\";s:6:\"module\";s:10:\"dispatcher\";s:10:\"menu_image\";s:14:\"Dispatcher.png\";}}}}\";s:8:\"mode_cur\";s:5:\"admin\";s:14:\"advance_search\";i:0;}'),('f958ef46e68945deaa36bf63961847bb','192.168.1.32','Mozilla/5.0 (X11; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',1422020649,'a:2:{s:9:\"user_data\";s:0:\"\";s:10:\"user_login\";b:0;}');
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
  `currencyrate` decimal(10,5) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `currency` (`currency`),
  KEY `currencyrate` (`currencyrate`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency`
--

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
INSERT INTO `currency` VALUES (1,'ALL','Albanian Lek',71.95000,'2015-01-23 12:55:11'),(2,'DZD','Algerian Dinar',0.00000,'2014-05-14 06:41:29'),(3,'XAL','Aluminium Ounces',0.00000,'2013-03-23 09:03:23'),(4,'ARS','Argentine Peso',0.00000,'2014-05-14 06:37:57'),(5,'AWG','Aruba Florin',0.00000,'2014-05-14 06:37:57'),(6,'AUD','Australian Dollar',91.72000,'2014-10-28 05:30:45'),(7,'BSD','Bahamian Dollar',14.05000,'2015-01-23 12:55:11'),(8,'BHD','Bahraini Dinar',0.00000,'2014-05-14 06:37:57'),(9,'BDT','Bangladesh Taka',0.00000,'2014-05-14 06:37:57'),(10,'BBD','Barbados Dollar',13.97000,'2015-01-23 12:55:11'),(11,'BYR','Belarus Ruble',0.00000,'2014-05-14 06:37:57'),(12,'BZD','Belize Dollar',0.00000,'2014-05-14 06:37:57'),(13,'BMD','Bermuda Dollar',0.00000,'2014-05-14 06:37:57'),(14,'BTN','Bhutan Ngultrum',4.27000,'2015-01-23 12:55:11'),(15,'BOB','Bolivian Boliviano',0.00000,'2014-05-14 06:37:58'),(16,'BRL','Brazilian Real',0.00000,'2014-05-14 06:37:58'),(17,'GBP','British Pound',0.00000,'2014-05-14 06:37:58'),(18,'BND','Brunei Dollar',83.46000,'2015-01-23 12:55:11'),(19,'BGN','Bulgarian Lev',0.00000,'2014-05-14 06:37:58'),(20,'BIF','Burundi Franc',8.87000,'2015-01-23 12:55:11'),(21,'KHR','Cambodia Riel',0.00000,'2014-05-14 06:37:58'),(22,'CAD','Canadian Dollar',95.87900,'2014-10-28 05:30:46'),(23,'KYD','Cayman Islands Dollar',0.00000,'2013-03-23 09:03:23'),(24,'XOF','CFA Franc (BCEAO)',0.00000,'2014-05-14 06:37:58'),(25,'XAF','CFA Franc (BEAC)',0.00000,'2014-05-14 06:37:58'),(26,'CLP','Chilean Peso',0.00000,'2014-05-14 06:37:58'),(27,'CNY','Chinese Yuan',43.25000,'2015-01-23 12:55:11'),(28,'COP','Colombian Peso',65.30000,'2015-01-23 12:55:11'),(29,'KMF','Comoros Franc',31.30000,'2015-01-23 12:55:11'),(30,'XCP','Copper Ounces',0.00000,'2014-05-14 06:37:58'),(31,'CRC','Costa Rica Colon',3.93000,'2015-01-23 12:55:11'),(32,'HRK','Croatian Kuna',0.00000,'2014-05-14 06:37:58'),(33,'CUP','Cuban Peso',0.00000,'2014-05-14 06:37:58'),(34,'CYP','Cyprus Pound',0.00000,'2013-03-23 09:03:23'),(35,'CZK','Czech Koruna',0.00000,'2014-05-14 06:37:58'),(36,'DKK','Danish Krone',0.00000,'2014-05-14 06:37:58'),(37,'DJF','Dijibouti Franc',0.00000,'2014-05-14 06:37:58'),(38,'DOP','Dominican Peso',0.00000,'2014-05-14 06:37:59'),(39,'XCD','East Caribbean Dollar',0.00000,'2014-05-14 06:37:59'),(40,'ECS','Ecuador Sucre',0.00000,'2013-03-23 09:03:23'),(41,'EGP','Egyptian Pound',66.99000,'2015-01-23 12:55:11'),(42,'SVC','El Salvador Colon',0.00000,'2014-05-14 06:37:59'),(43,'ERN','Eritrea Nakfa',0.00000,'2013-03-23 09:03:23'),(44,'EEK','Estonian Kroon',0.00000,'2013-03-23 09:03:23'),(45,'ETB','Ethiopian Birr',16.04000,'2015-01-23 12:55:11'),(46,'EUR','Euro',0.00000,'2015-01-23 12:55:11'),(47,'FKP','Falkland Islands Pound',0.00000,'2014-05-14 06:37:59'),(48,'GMD','Gambian Dalasi',0.00000,'2014-05-14 06:37:59'),(49,'GHC','Ghanian Cedi',897.33000,'2015-01-23 12:55:12'),(50,'GIP','Gibraltar Pound',0.00000,'2014-05-14 06:37:59'),(51,'XAU','Gold Ounces',0.00000,'2014-05-14 06:37:59'),(52,'GTQ','Guatemala Quetzal',0.00000,'2014-05-14 06:37:59'),(53,'GNF','Guinea Franc',0.00000,'2014-05-14 06:37:59'),(54,'HTG','Haiti Gourde',0.00000,'2014-05-14 06:37:59'),(55,'HNL','Honduras Lempira',0.00000,'2014-05-14 06:37:59'),(56,'HKD','Hong Kong Dollar',0.00000,'2014-05-14 06:37:59'),(57,'HUF','Hungarian ForINT',0.00000,'2014-05-14 06:37:59'),(58,'ISK','Iceland Krona',0.00000,'2014-05-14 06:37:59'),(59,'INR','Indian Rupee',37.93000,'2015-01-23 12:55:12'),(60,'IDR','Indonesian Rupiah',0.00000,'2014-05-14 06:37:59'),(61,'IRR','Iran Rial',8.80000,'2015-01-23 12:55:12'),(62,'ILS','Israeli Shekel',0.00000,'2014-05-14 06:37:59'),(63,'JMD','Jamaican Dollar',0.00000,'2014-05-14 06:37:59'),(64,'JPY','Japanese Yen',0.00000,'2014-05-14 06:38:00'),(65,'JOD','Jordanian Dinar',0.00000,'2014-05-14 06:38:00'),(66,'KZT','Kazakhstan Tenge',0.00000,'2014-05-14 06:38:00'),(67,'KES','Kenyan Shilling',0.00000,'2014-05-14 06:38:00'),(68,'KRW','Korean Won',0.00000,'2014-05-14 06:38:00'),(69,'KWD','Kuwaiti Dinar',0.00000,'2014-05-14 06:38:00'),(70,'LAK','Lao Kip',0.00000,'2014-05-14 06:38:00'),(71,'LVL','Latvian Lat',11.50000,'2015-01-23 12:55:12'),(72,'LBP','Lebanese Pound',0.00000,'2014-05-14 06:38:00'),(73,'LSL','Lesotho Loti',0.00000,'2014-05-14 06:38:00'),(74,'LYD','Libyan Dinar',0.00000,'2014-05-14 06:38:00'),(75,'LTL','Lithuanian Lita',89.97000,'2015-01-23 12:55:12'),(76,'MOP','Macau Pataca',0.00000,'2014-05-14 06:38:00'),(77,'MKD','Macedonian Denar',0.00000,'2014-05-14 06:38:00'),(78,'MGF','Malagasy Franc',5.83000,'2015-01-23 12:55:12'),(79,'MWK','Malawi Kwacha',0.00000,'2014-05-14 06:38:00'),(80,'MYR','Malaysian Ringgit',0.00000,'2014-05-14 06:38:00'),(81,'MVR','Maldives Rufiyaa',0.00000,'2014-05-14 06:38:00'),(82,'MTL','Maltese Lira',0.84000,'2015-01-23 12:55:12'),(83,'MRO','Mauritania Ougulya',26.83000,'2015-01-23 12:55:12'),(84,'MUR','Mauritius Rupee',48.11000,'2015-01-23 12:55:12'),(85,'MXN','Mexican Peso',14.54000,'2014-10-28 05:30:46'),(86,'MDL','Moldovan Leu',0.00000,'2014-05-14 06:38:01'),(87,'MNT','Mongolian Tugrik',0.00000,'2014-05-14 06:38:01'),(88,'MAD','Moroccan Dirham',0.00000,'2014-05-14 06:38:01'),(89,'MZM','Mozambique Metical',0.00000,'2013-03-23 09:03:23'),(90,'NAD','Namibian Dollar',14.44000,'2015-01-23 12:55:12'),(91,'NPR','Nepalese Rupee',0.00000,'2014-05-14 06:38:01'),(92,'ANG','Neth Antilles Guilder',0.00000,'2014-05-14 06:38:01'),(93,'TRY','New Turkish Lira',0.00000,'2014-05-14 06:38:01'),(94,'NZD','New Zealand Dollar',0.00000,'2014-05-14 06:38:01'),(95,'NIO','Nicaragua Cordoba',14.72000,'2015-01-23 12:55:12'),(96,'NGN','Nigerian Naira',0.00000,'2014-05-14 06:38:01'),(97,'NOK','Norwegian Krone',7.87000,'2015-01-23 12:55:12'),(98,'OMR','Omani Rial',0.00000,'2014-05-14 06:38:01'),(99,'XPF','Pacific Franc',0.00000,'2014-05-14 06:38:01'),(100,'PKR','Pakistani Rupee',0.00000,'2014-05-14 06:38:01'),(101,'XPD','Palladium Ounces',0.00000,'2014-05-14 06:38:02'),(102,'PAB','Panama Balboa',0.00000,'2014-05-14 06:38:02'),(103,'PGK','Papua New Guinea Kina',0.00000,'2014-05-14 06:38:02'),(104,'PYG','Paraguayan Guarani',0.00000,'2014-05-14 06:38:02'),(105,'PEN','Peruvian Nuevo Sol',0.00000,'2014-05-14 06:38:02'),(106,'PHP','Philippine Peso',0.00000,'2014-05-14 06:38:02'),(107,'XPT','Platinum Ounces',0.00000,'2014-05-14 06:38:02'),(108,'PLN','Polish Zloty',0.00000,'2014-05-14 06:38:02'),(109,'QAR','Qatar Rial',0.00000,'2014-05-14 06:38:02'),(110,'ROL','Romanian Leu',33.34000,'2015-01-09 12:36:25'),(111,'RON','Romanian New Leu',0.00000,'2014-05-14 06:38:02'),(112,'RUB','Russian Rouble',0.00000,'2014-05-14 06:38:02'),(113,'RWF','Rwanda Franc',0.00000,'2014-05-14 06:38:02'),(114,'WST','Samoa Tala',51.00000,'2015-01-23 12:55:12'),(115,'STD','Sao Tome Dobra',0.00000,'2014-05-14 06:38:02'),(116,'SAR','Saudi Arabian Riyal',15.55000,'2015-01-23 12:55:12'),(117,'SCR','Seychelles Rupee',0.00000,'2014-05-14 06:38:02'),(118,'SLL','Sierra Leone Leone',0.00000,'2014-05-14 06:38:02'),(119,'XAG','Silver Ounces',0.00000,'2014-05-14 06:38:02'),(120,'SGD','Singapore Dollar',0.00000,'2014-05-14 06:38:02'),(121,'SKK','Slovak Koruna',33.55350,'2015-01-09 12:36:25'),(122,'SIT','Slovenian Tolar',0.00000,'2013-03-23 09:03:23'),(123,'SOS','Somali Shilling',0.00000,'2014-05-14 06:38:02'),(124,'ZAR','South African Rand',0.00000,'2014-05-14 06:38:03'),(125,'LKR','Sri Lanka Rupee',0.00000,'2014-05-14 06:38:03'),(126,'SHP','St Helena Pound',0.00000,'2014-05-14 06:38:03'),(127,'SDD','Sudanese Dinar',40.36600,'2015-01-23 12:55:12'),(128,'SRG','Surinam Guilder',0.00000,'2013-03-23 09:03:23'),(129,'SZL','Swaziland Lilageni',0.00000,'2014-05-14 06:38:03'),(130,'SEK','Swedish Krona',0.00000,'2014-05-14 06:38:03'),(131,'CHF','Swiss Franc',0.00000,'2014-05-14 06:38:03'),(132,'SYP','Syrian Pound',0.00000,'2014-05-14 06:38:03'),(133,'TWD','Taiwan Dollar',0.00000,'2014-05-14 06:38:03'),(134,'TZS','Tanzanian Shilling',0.00000,'2014-05-14 06:38:03'),(135,'THB','Thai Baht',0.00000,'2014-05-14 06:38:03'),(136,'TOP','Tonga Paanga',0.00000,'2014-05-14 06:38:03'),(137,'TTD','Trinidad&Tobago Dollar',0.00000,'2014-05-14 06:38:03'),(138,'TND','Tunisian Dinar',0.00000,'2014-05-14 06:38:03'),(139,'USD','U.S. Dollar',1.00000,'2013-03-23 09:03:23'),(140,'AED','UAE Dirham',25.78010,'2015-01-23 12:55:12'),(141,'UGX','Ugandan Shilling',0.00000,'2014-05-14 06:38:03'),(142,'UAH','Ukraine Hryvnia',0.00000,'2014-05-14 06:38:03'),(143,'UYU','Uruguayan New Peso',0.00000,'2014-05-14 06:38:03'),(144,'VUV','Vanuatu Vatu',0.00000,'2014-05-14 06:38:03'),(145,'VEB','Venezuelan Bolivar',0.00000,'2013-03-23 09:03:23'),(146,'VND','Vietnam Dong',0.00000,'2014-05-14 06:38:03'),(147,'YER','Yemen Riyal',0.00000,'2014-05-14 06:38:03'),(148,'ZMK','Zambian Kwacha',0.00000,'2014-05-14 06:38:03'),(149,'ZWD','Zimbabwe Dollar',0.00000,'2013-03-23 09:03:23'),(150,'GYD','Guyana Dollar',0.00000,'2014-05-14 06:38:03'),(151,'458','uioui',0.00000,'2015-01-23 12:55:12');
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;

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
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `default_templates`
--

LOCK TABLES `default_templates` WRITE;
/*!40000 ALTER TABLE `default_templates` DISABLE KEYS */;
INSERT INTO `default_templates` VALUES (1,'voip_account_refilled','#NAME#, Your account refilled','HI #NAME#,\n\nYour account with #COMPANY_NAME# has been refilled.\n\nFor information please visit #COMPANY_WEBSITE# or contact our support department at #COMPANY_EMAIL#.\n\nThanks,\n#COMPANY_NAME#','2015-01-17 10:45:26'),(3,'email_add_user','#NAME#, welcome to #COMPANY_NAME#','Welcome #NAME#,\n\nYour account has been added in #COMPANY_NAME#.\n\nHere is your account information,\n\nAccount Number : #NUMBER#\nPassword : #PASSWORD#\n          \nFor information please visit #COMPANY_WEBSITE# or contact our support department at #COMPANY_EMAIL#.\n\nThanks, \n#COMPANY_NAME#\n','2015-01-01 06:23:30'),(4,'add_sip_device','#NAME#, New SIP device created','Hi #NAME#,\n\nA new device has been created on your account.\n\nHere is the necessary configuration information,\n\nUsername : #USERNAME#\nPassword : #PASSWORD#\n\nFor information please visit #COMPANY_WEBSITE# or contact our support department at #COMPANY_EMAIL#.\n\n\nThanks, \n#COMPANY_NAME#','2015-01-05 10:48:21'),(8,'email_add_did','#NAME#, #NUMBER# assigned to your account','Hi #NAME#,\n\n#NUMBER# DID has been assigned to your account. \n\nFor information please visit #COMPANY_WEBSITE# or contact our support department at #COMPANY_EMAIL#.\n\n\nThanks,\n#COMPANY_NAME#','2015-01-02 13:42:06'),(9,'email_remove_did','#NAME#, #NUMBER# removed from your account','Hi #NAME#,\n\n#NUMBER# DID has been removed from your account.\n\nFor information please visit #COMPANY_WEBSITE# or contact our support department at #COMPANY_EMAIL#.\n\nThanks,\n#COMPANY_NAME#\n\n','2015-01-02 13:42:13'),(10,'email_new_invoice','#NAME#, Invoice # #INVOICE_NUMBER# generated','Hi #NAME#,\n\nNew invoice # #INVOICE_NUMBER# for amount of #AMOUNT# has been generated to your account. \n\nFor information please visit #COMPANY_WEBSITE# or contact our support department at #COMPANY_EMAIL#.\n\nThanks,\n#COMPANY_NAME#','2015-01-01 06:42:54'),(11,'email_low_balance','#NAME#, Low balance notification','Hi #NAME#,\n\nYour account with #COMPANY_NAME# has a balance of #BALANCE#.\n\nPlease visit our website to refill your account to ensure uninterrupted service. \n\nFor information please visit #COMPANY_WEBSITE# or contact our support department at #COMPANY_EMAIL#.\n\nThanks,\n#COMPANY_NAME#','2015-01-01 06:43:52');
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
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `monthlycost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `cost` double(10,5) NOT NULL DEFAULT '0.00000',
  `inc` int(4) NOT NULL,
  `extensions` char(180) NOT NULL DEFAULT '',
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
  `dial_as` char(40) NOT NULL DEFAULT '',
  `call_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'call type',
  `inuse` int(4) NOT NULL DEFAULT '0',
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `charge_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Active , 1= inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freeswich_servers`
--

LOCK TABLES `freeswich_servers` WRITE;
/*!40000 ALTER TABLE `freeswich_servers` DISABLE KEYS */;
INSERT INTO `freeswich_servers` VALUES (1,'127.0.0.1','ClueCon','8021',0);
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
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for Active 1 for Inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateways`
--

LOCK TABLES `gateways` WRITE;
/*!40000 ALTER TABLE `gateways` DISABLE KEYS */;
INSERT INTO `gateways` VALUES (1,1,'IPComms','{\"username\":\"USERNAME\",\"password\":\"PASSWORD\",\"proxy\":\"siptrunk.ipcomms.net\",\"register\":\"false\",\"caller-id-in-from\":\"true\"}','2015-01-23 13:20:54','0000-00-00 00:00:00',0,0);
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
INSERT INTO `invoice_conf` VALUES (1,1,'iNextrix Technologies. Pvt. Ltd.','Abhushan Complex','Ahmedabad','Gujarat','India','380014','+1-855-580-1802','','sales@inextrix.com','www.inextrix.com');
/*!40000 ALTER TABLE `invoice_conf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_item`
--

DROP TABLE IF EXISTS `invoice_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL COMMENT 'account table id',
  `pricelist_id` int(11) NOT NULL DEFAULT '0' COMMENT 'pricelist table id',
  `description` varchar(255) NOT NULL COMMENT 'charges description',
  `charge_id` int(11) NOT NULL COMMENT 'charge table id',
  `package_id` int(11) NOT NULL COMMENT 'Package table id',
  `debit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `invoiceid` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `charge_type` enum('did_charge','post_charge','monthly_charge','periodic_charge','account_refill') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_item`
--

LOCK TABLES `invoice_item` WRITE;
/*!40000 ALTER TABLE `invoice_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `from_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `to_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:inactive,1:active',
  `external_id` int(11) NOT NULL DEFAULT '0',
  `invoice_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` enum('I','R') NOT NULL DEFAULT 'I' COMMENT 'I => Invoice R=> Receipt',
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
  `invoiceid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `text` varchar(150) NOT NULL DEFAULT '',
  `value` decimal(10,5) NOT NULL,
  `class` varchar(32) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_id` (`invoiceid`)
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
  `priority` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_modules`
--

LOCK TABLES `menu_modules` WRITE;
/*!40000 ALTER TABLE `menu_modules` DISABLE KEYS */;
INSERT INTO `menu_modules` VALUES (1,'Customers','customer','accounts/customer_list/','Accounts','ListAccounts.png','0',10.1),(2,'Resellers','reseller','accounts/reseller_list/','Accounts','reseller.png','0',10.2),(3,'Customers','provider','accounts/customer_list/','Accounts','ListAccounts.png','0',0),(4,'Admins','admin','accounts/admin_list/','Accounts','ListAccounts.png','0',10.3),(5,'Admins','subadmin','accounts/admin_list/','Accounts','ListAccounts.png','0',0),(7,'Subscriptions ','periodiccharges','charges/periodiccharges/','Accounting','PeriodicCharges.png','0',20.2),(8,'Invoices','invoice','invoices/invoice_list/','Accounting','InvoiceList.png','0',20.1),(9,'Invoice Config ','invoice','invoices/invoice_conf/','Configuration','InvoiceConf.png','0',80.1),(10,'Calling Cards','callingcards','callingcards/callingcards_list/','Calling Cards','ListCards.png','0',30.1),(11,'Card Brands','brands','callingcards/brands/','Calling Cards','CCBand.png','0',30.2),(12,'Call Report','callingcards','callingcards/callingcards_cdrs/','Calling Cards','CallingCardCDR\'s.png','0',30.3),(13,'DIDs','did','did/did_list/','DIDs','ManageDIDs.png','0',40),(14,'Trunks','trunk','trunk/trunk_list/','Carriers','Trunks.png','0',55),(15,'Termination Rates','terminationrates','rates/terminationrates_list/','Carriers','OutboundRoutes.png','0',56),(16,'Rate Groups','price','pricing/price_list/','Tariff','pricelist.png','0',51),(17,'Origination Rates','origination','rates/origination_list/','Tariff','Routes.png','0',52),(18,'Packages','package','package/package_list/','Tariff','packages.png','Packages',53),(19,'Customer','customerReport','reports/customerReport/','Call Reports','cdr.png','Detail Reports',70.1),(20,'Live Call Report','livecall','freeswitch/livecall_report/','Call Reports','cdr.png','0',72),(21,'Reseller','resellerReport','reports/resellerReport/','Call Reports','cdr.png','Detail Reports',70.2),(22,'Provider Outbound','providerReport','reports/providerReport/','Call Reports','cdr.png','Detail Reports',70.3),(24,'Trunk Stats','trunkstats','statistics/trunkstats/','Call Reports','TrunkStats.png','Summary Reports',71.5),(25,'SIP Devices','fssipdevices','freeswitch/fssipdevices/','Switch','Devices.png','0',60.1),(26,'Configuration','configuration','systems/configuration/','System Configuration','Configurations.png','System',90.1),(27,'Taxes','taxes','taxes/taxes_list/','Configuration','AccountTaxes.png','0',80.2),(28,'Email Templates','template','systems/template/','Configuration','TemplateManagement.png','0',80.3),(29,'Opensips devices','opensips','opensips/opensips_list/','Opensips','OpensipDevices.png','0',90.2),(30,'Dispatcher list','dispatcher','opensips/dispatcher_list/','Opensips','Dispatcher.png','0',90.3),(31,'Invoices','user','user/user_invoice_list/','Manage Invoice','ListAccounts.png','0',1.6),(32,'DIDs','user','user/user_didlist/','Manage DIDs','ManageDIDs.png','0',1.3),(33,'ANI MAP','user','user/user_animap_list/','Manage ANI','Providers.png','0',1.5),(34,'CDRs Reports','user','user/user_cdrs_report/','Reports','cdr.png','0',1.7),(35,'Payment Reports','user','user/user_payment_report/','Reports','PaymentReport.png','0',1.8),(36,'SIP Devices','user','user/user_sipdevices/','SIP Devices','freeswitch.png','0',1.4),(37,'My Rates','user','user/user_rates_list/','Rates','Routes.png','0',1.2),(38,'Reseller ','resellersummary','reports/resellersummary/','Call Reports','cdr.png','Summary Reports',71.3),(39,'Provider','providersummary','reports/providersummary/','Call Reports','cdr.png','Summary Reports',71.4),(40,'Payment Report','paymentreport','reports/paymentreport/','Accounting','PaymentReport.png','0',20.3),(41,'Gateways','fsgateway','freeswitch/fsgateway/','Switch','Gateway.png','0',60.2),(42,'Sip Profiles ','fssipprofile','freeswitch/fssipprofile/','Switch','SipProfiles.png','0',60.3),(43,'Freeswitch Server','fsserver','freeswitch/fsserver_list/','Switch','freeswitch.png','0',60.4),(44,'Usage Report','package','package/package_counter/','Tariff','Counters.png','Packages',54),(45,'Customer ','customersummary','reports/customersummary/','Call Reports','cdr.png','Summary Reports',71.2),(48,'Countries','country','systems/country_list/','Configuration','ManageDIDs.png','0',80.4),(49,'Currencies ','currency','systems/currency_list/','Configuration','ManageDIDs.png','0',80.5),(51,'Database Restore','database','systems/database_restore/','Configuration','Configurations.png','0',80.6),(52,'My Rates','resellersrates','rates/resellersrates_list/','Tariff','OutboundRoutes.png','0',52.1);
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
  `comment` char(80) DEFAULT '',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(4) NOT NULL DEFAULT '0',
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `trunk_id` int(4) NOT NULL DEFAULT '0',
  `inc` int(4) NOT NULL,
  `strip` char(40) NOT NULL DEFAULT '',
  `prepend` char(40) NOT NULL DEFAULT '',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  PRIMARY KEY (`id`),
  KEY `trunk` (`trunk_id`),
  KEY `pattern` (`pattern`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


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
  PRIMARY KEY (`id`),
  KEY `accountid` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

ALTER TABLE `payments` ADD `txn_id` VARCHAR( 25 ) NOT NULL AFTER `paypalid` ;

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
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
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
INSERT INTO `pricelists` VALUES (1,'default',0,60,0,0);
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
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `extensions` varchar(20) NOT NULL,
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
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `charge_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  `trunk_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Trunk id for force routing',
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
INSERT INTO `routes` VALUES (1,'^1.*','USA',0.00000,0,1.00000,1,1,0,0,0,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routing`
--

LOCK TABLES `routing` WRITE;
/*!40000 ALTER TABLE `routing` DISABLE KEYS */;
INSERT INTO `routing` VALUES (1,1,1);
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
  `accountid` int(11) NOT NULL DEFAULT '0',
  `dir_params` mediumtext NOT NULL,
  `dir_vars` mediumtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:active,1:inactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sip_devices`
--

LOCK TABLES `sip_devices` WRITE;
/*!40000 ALTER TABLE `sip_devices` DISABLE KEYS */;
INSERT INTO `sip_devices` VALUES (1,'2457848300',1,3,'{\"password\":\"2457848300\"}','{\"effective_caller_id_name\":\"\",\"effective_caller_id_number\":\"\"}',0);
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
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
INSERT INTO `sip_profiles` (`id`, `name`, `sip_ip`, `sip_port`, `profile_data`, `created_date`, `modified_date`, `accountid`, `status`) VALUES
(1, 'default', '$${local_ip_v4}', '5060', '{"rtp-ip":"$${local_ip_v4}","dialplan":"XML","user-agent-string":"ASTPP","debug":"0","sip-trace":"no","tls":"false","inbound-reg-force-matching-username":"true","disable-transcoding":"true","all-reg-options-ping":"false","unregister-on-options-fail":"true","log-auth-failures":"true","status":"0","inbound-bypass-media":"false","inbound-proxy-media":"false","disable-transfer":"true","enable-100rel":"false","rtp-timeout-sec":"300","dtmf-duration":"2000","manual-redirect":"false","aggressive-nat-detection":"false","enable-timer":"false","minimum-session-expires":"120","session-timeout-pt":"1800","auth-calls":"true","apply-inbound-acl":"default","inbound-codec-prefs":"$${global_codec_prefs}","outbound-codec-prefs":"$${global_codec_prefs}","inbound-late-negotiation":"false","sip-capture":"no","forward-unsolicited-mwi-notify":"false","context":"default","rfc2833-pt":"101","rtp-timer-name":"soft","hold-music":"$${hold_music}","manage-presence":"true","presence-hosts":"$${domain},$${local_ip_v4}","presence-privacy":"$${presence_privacy}","inbound-codec-negotiation":"generous","auth-all-packets":"false","ext-rtp-ip":"auto-nat","ext-sip-ip":"auto-nat","rtp-hold-timeout-sec":"1800","force-register-domain":"$${domain}","force-subscription-domain":"$${domain}","force-register-db-domain":"$${domain}","challenge-realm":"auto_from","nonce-ttl":"60"}', '2015-01-21 17:25:01', '0000-00-00 00:00:00', 0, 0);
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
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system`
--

LOCK TABLES `system` WRITE;
/*!40000 ALTER TABLE `system` DISABLE KEYS */;
INSERT INTO `system` VALUES (1,'log_file','/var/log/astpp/astpp.log','Where do I log to?',NULL,0,0,'global'),(11,'opensips_dbengine','MySQL','For now this must be MySQL',NULL,0,0,'opensips'),(12,'opensips','0','Use Opensips?  1 for yes or 0 for no',NULL,0,0,'opensips'),(13,'opensips_dbname','opensips dbname','Opensips Database Name',NULL,0,0,'opensips'),(14,'opensips_dbuser','opensips dbuser','Opensips Database User',NULL,0,0,'opensips'),(15,'opensips_dbhost','opensips dbhost','Opensips Database Host',NULL,0,0,'opensips'),(16,'opensips_dbpass','opensips dbpass','Opensips Database Password',NULL,0,0,'opensips'),(17,'opensips_domain','opensips domain','Opensips Domain',NULL,0,0,'opensips'),(18,'company_email','contact@inextrix.com','Email address that email should appear to be from',NULL,0,0,'global'),(20,'company_website','http://www.inextrix.com','Link to your company website',NULL,0,0,'global'),(21,'company_name','iNextrix Technologies. Pvt. Ltd.','The name of your company.  Used in emails.',NULL,0,0,'global'),(22,'email','1','Send out email? 0=no 1=yes',NULL,0,0,'global'),(23,'user_email','1','Email user on account changes? 0=no 1=yes',NULL,0,0,'global'),(24,'debug','0','Enable debugging output? 0=no 1=yes',NULL,0,0,'global'),(25,'emailadd','contact@inextrix.com','Administrator email address',NULL,0,0,'global'),(26,'startingdigit','0','The digit that all calling cards must start with. 0=disabled',NULL,0,0,'callingcard'),(30,'rate_engine_csv_file','/var/log/astpp/astpp.csv','CSV File for call rating data',NULL,0,0,'global'),(31,'csv_dir','/var/log/astpp/','CSV File Directory',NULL,0,0,'global'),(35,'cardlength','10','Number of digits in calling cards and cc codes.',NULL,0,0,'callingcard'),(42,'pinlength','6','For those calling cards that are using pins this is the number of digits it will have.',NULL,0,0,'callingcard'),(44,'decimalpoints','4','How many decimal points do we bill to?',NULL,0,0,'global'),(45,'decimalpoints_tax','2','How many decimal points do we calculate taxes to?',NULL,0,0,'global'),(46,'decimalpoints_total','2','How many decimal points do we calculate totals to?',NULL,0,0,'global'),(47,'max_free_length','100','What is the maximum length (in minutes) of calls that are at no charge?',NULL,0,0,'global'),(53,'card_retries','3','How many retries do we allow for calling card numbers?',NULL,0,0,'callingcard'),(54,'pin_retries','3','How many retries do we allow for pins?',NULL,0,0,'callingcard'),(55,'number_retries','3','How many retries do we allow calling card users when dialing a number?',NULL,0,0,'callingcard'),(57,'callingcards_max_length','9000','What is the maximum length (in ms) of a callingcard call?',NULL,0,0,'callingcard'),(61,'auth','Passw0rd!','This is the override authorization code and will allow access to the system.',NULL,0,0,'global'),(67,'externalbill','internal','Please specify the external billing application to use.  If you are not using any then leave it blank.  Valid options are ',NULL,0,0,'global'),(71,'sleep','10','How long shall the rating engine sleep after it has been notified of a hangup? (in seconds)',NULL,0,0,'global'),(135,'calling_cards_rate_announce','0','Do we want the calling cards script to announce the rate on calls?',NULL,0,0,'callingcard'),(136,'calling_cards_timelimit_announce','0','Do we want the calling cards script to announce the timelimit on calls?',NULL,0,0,'callingcard'),(137,'calling_cards_cancelled_prompt','1','Do we want the calling cards script to announce that the call was cancelled?',NULL,0,0,'callingcard'),(138,'calling_cards_menu','0','Do we want the calling cards script to present a menu before exiting?',NULL,0,0,'callingcard'),(139,'calling_cards_connection_prompt','0','Do we want the calling cards script to announce that it is connecting the call?',NULL,0,0,'callingcard'),(140,'calling_cards_pin_input_timeout','15000','How long do we wait when entering the calling card pin?  Specified in MS',NULL,0,0,'callingcard'),(141,'calling_cards_number_input_timeout','15000','How long do we wait when entering the calling card number?  Specified in MS',NULL,0,0,'callingcard'),(142,'calling_cards_dial_input_timeout','15000','How long do we wait when entering the destination number in calling cards?  Specified in MS',NULL,0,0,'callingcard'),(143,'calling_cards_general_input_timeout','15000','How long do we wait for input in general menus?  Specified in MS',NULL,0,0,'callingcard'),(144,'calling_cards_welcome_file','astpp-welcome.wav','What do we play for a welcome file?',NULL,0,0,'callingcard'),(163,'freeswitch_sound_files','/en/us/callie','Where are our sound files located?',NULL,0,0,'freeswitch'),(171,'call_max_length','1440000','What is the maximum length (in ms) of a LCR call?',NULL,0,0,'global'),(173,'cc_ani_auth','1','Calling card ANI authentiation. 0 for disable and 1 for enable',NULL,0,0,'callingcard'),(174,'base_currency','USD','Base Currency of System',NULL,0,0,'global'),(179,'default_timezone','49','system timezone','2013-05-06 19:34:39',0,0,'global'),(181,'country','India','default country',NULL,0,0,'global'),(182,'timezone','GMT+00:00','default timezone',NULL,0,0,'global'),(183,'paypal_status','0','0=enable paypal module 1=disable paypal module',NULL,0,0,'paypal'),(184,'paypal_url','https://www.paypal.com/cgi-bin/webscr','paypal live url',NULL,0,0,'paypal'),(185,'paypal_sandbox_url','https://www.sandbox.paypal.com/cgi-bin/webscr','Paypal Sandbox url for testing',NULL,0,0,'paypal'),(186,'paypal_id','your@paypal.com','Paypal Live account id',NULL,0,0,'paypal'),(187,'paypal_sandbox_id','your@paypal.com','Paypal sandbox accountid for testing',NULL,0,0,'paypal'),(188,'paypal_mode','0','0=paypal Live mode 1= paypal Sandbox mode',NULL,0,0,'paypal'),(189,'paypal_fee','0','0=paypal mc fee paid by admin 1= paypal mc fee paid by customer',NULL,0,0,'paypal'),(190,'paypal_tax','0','Paypal tax rate (in percentage) apply to recharge amount',NULL,0,0,'paypal'),(191,'version','2.0','ASTPP Version',NULL,0,0,'global'),(192,'ivr_count','2','IVR playback loop count.',NULL,0,0,'callingcard'),(193,'calling_cards_balance_announce','1','Do we want the calling cards script to announce the balance of account?',NULL,0,0,'callingcard'),(194,'cc_access_numbers','2222,3333,6666','Add calling card access numbers with comma separation. Ex : 12345678,3581629',NULL,0,0,'callingcard');
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
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active 1 for inactive',
  PRIMARY KEY (`id`),
  KEY `taxes_priority` (`taxes_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `taxes` CHANGE `last_modified` `last_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `taxes` CHANGE `date_added` `date_added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';

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
  `tech` char(10) NOT NULL DEFAULT '',
  `gateway_id` int(4) NOT NULL DEFAULT '0',
  `failover_gateway_id` int(4) NOT NULL DEFAULT '0' COMMENT 'Fail over Gateway id',
  `provider_id` int(11) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `dialed_modify` mediumtext NOT NULL,
  `resellers_id` varchar(11) NOT NULL DEFAULT '0',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `maxchannels` int(4) NOT NULL DEFAULT '0',
  `inuse` int(4) NOT NULL DEFAULT '0',
  `codec` varchar(100) NOT NULL,
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
INSERT INTO `trunks` VALUES (1,'IPComms','',1,0,2,0,'','0',0,0,0,'PCMU,PCMA');
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
INSERT INTO `userlevels` VALUES (-1,'Administrator','1,2,4,5,3,8,9,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,7,29,30,45,38,39,40,41,42,43,44,48,49,51'),(0,'Customer','31,32,37,36,34,35,33'),(1,'Reseller','1,2,7,8,13,16,17,18,19,21,25,38,40,44,45,46,52,27,9,29'),(2,'Admin','1,2,3,4,5,7,8,9,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,29,30,38,40,41,42,43,44,45'),(3,'Provider','31,32,37,36,34,35,33'),(4,'Sub Admin','8,19,20,21,22,38,24,45'),(5,'CallShop','17');
/*!40000 ALTER TABLE `userlevels` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-23 19:42:19
