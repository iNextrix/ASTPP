--
-- Table structure for table `q850code`
--
DROP TABLE IF EXISTS `q850code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `q850code` (
  `cause` varchar(70) DEFAULT NULL,
  `code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `q850code`
--

LOCK TABLES `q850code` WRITE;
/*!40000 ALTER TABLE `q850code` DISABLE KEYS */;
INSERT INTO `q850code` VALUES ('UNSPECIFIED',0),('UNALLOCATED_NUMBER',1),('NO_ROUTE_TRANSIT_NET',2),('NO_ROUTE_DESTINATION',3),('CHANNEL_UNACCEPTABLE',6),('CALL_AWARDED_DELIVERED',7),('NORMAL_CLEARING',16),('USER_BUSY',17),('NO_USER_RESPONSE',18),('NO_ANSWER',19),('SUBSCRIBER_ABSENT',20),('CALL_REJECTED',21),('NUMBER_CHANGED',22),('REDIRECTION_TO_NEW_DESTINATION',23),('EXCHANGE_ROUTING_ERROR',25),('DESTINATION_OUT_OF_ORDER',27),('INVALID_NUMBER_FORMAT',28),('FACILITY_REJECTED',29),('RESPONSE_TO_STATUS_ENQUIRY',30),('NORMAL_UNSPECIFIED',31),('NORMAL_CIRCUIT_CONGESTION',34),('NETWORK_OUT_OF_ORDER',38),('NORMAL_TEMPORARY_FAILURE',41),('SWITCH_CONGESTION',42),('ACCESS_INFO_DISCARDED',43),('REQUESTED_CHAN_UNAVAIL',44),('PRE_EMPTED',45),('FACILITY_NOT_SUBSCRIBED',50),('OUTGOING_CALL_BARRED',52),('INCOMING_CALL_BARRED',54),('BEARERCAPABILITY_NOTAUTH',57),('BEARERCAPABILITY_NOTAVAIL',58),('SERVICE_UNAVAILABLE',63),('BEARERCAPABILITY_NOTIMPL',65),('CHAN_NOT_IMPLEMENTED',66),('FACILITY_NOT_IMPLEMENTED',69),('SERVICE_NOT_IMPLEMENTED',79),('INVALID_CALL_REFERENCE',81),('INCOMPATIBLE_DESTINATION',88),('INVALID_MSG_UNSPECIFIED',95),('MANDATORY_IE_MISSING',96),('MESSAGE_TYPE_NONEXIST',97),('WRONG_MESSAGE',98),('IE_NONEXIST',99),('INVALID_IE_CONTENTS',100),('WRONG_CALL_STATE',101),('RECOVERY_ON_TIMER_EXPIRE',102),('MANDATORY_IE_LENGTH_ERROR',103),('PROTOCOL_ERROR',111),('INTERWORKING',127),('ORIGINATOR_CANCEL',487),('CRASH',500),('SYSTEM_SHUTDOWN',501),('LOSE_RACE',502),('MANAGER_REQUEST',503),('BLIND_TRANSFER',600),('ATTENDED_TRANSFER',601),('ALLOTTED_TIMEOUT',602),('USER_CHALLENGE',603),('MEDIA_TIMEOUT',604),('PICKED_OFF',605),('USER_NOT_REGISTERED',606),('PROGRESS_TIMEOUT',607),('GATEWAY_DOWN',609);
/*!40000 ALTER TABLE `q850code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Account table query 
--

ALTER TABLE `accounts` CHANGE `interval` `cps` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `accounts` ADD `tax_number` VARCHAR(100) NULL DEFAULT NULL AFTER `did_cid_translation`;

--
-- Trunk table query 
--
ALTER TABLE `trunks` CHANGE `inuse` `cps` INT(4) NOT NULL DEFAULT '0';

--
-- Invoice detail table query 
--
ALTER TABLE `invoice_details` ADD `quantity` INT(11) NOT NULL DEFAULT '1' COMMENT 'Default will be 1' AFTER `after_balance`;

--
-- menu table queries
--
UPDATE `menu_modules` SET `menu_label` = 'Settings' WHERE `menu_modules`.`id` = 69;

--
-- Email template table query 
--
INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`, `last_modified_date`, `reseller_id`) VALUES ('0', 'invoice_due_reminder', 'Invoice due reminder #INVOICE_NUMBER#', 'Hi #NAME#, This is a reminder that your invoice number #INVOICE_NUMBER# which was generated on #INVOICE_DATE# is due on #DUE_DATE# Invoice Information : Invoice Date : #INVOICE_DATE# Invoice Number : #INVOICE_NUMBER# Due Amount : #AMOUNT# You can login into customer portal and pay the invoice. For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL# Thanks, #COMPANY_NAME#', CURRENT_DATE(), '0');

--
-- system table queries
--
UPDATE `system` SET `group_title` = 'signup' WHERE `system`.`id` = 179;
UPDATE `system` SET `display_name` = 'Timezone' WHERE `system`.`id` = 179;
UPDATE `system` SET `group_title` = 'signup' WHERE `system`.`id` = 181;
UPDATE `system` SET `display_name` = 'Country' WHERE `system`.`id` = 181;
UPDATE `system` SET `display_name` = 'Rategroup' WHERE `system`.`id` = 204;
UPDATE `system` SET `display_name` = 'Initial Balance' WHERE `system`.`id` = 207;
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`)
VALUES(219,'realtime_billing','Realtime Billing <b>(<a href=\'http://astpp.readthedocs.io/en/v3.6/Integrations/realtime_billing.html\' target="_blank">Experimental</a>)</b>',1,'enable_disable_option','Set enable to use realtime
billing.',NULL,0,0,'global'); 
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ('0', 'homer_capture_server', 'Capture Server', '', 'default_system_input', 'Set enable to capture logs in homer. Format : udp:192.168.1.200:9060', NULL, '0', '0', 'homer');
UPDATE `system` SET `display_name` = 'Default Invoice Mode', `comment` = 'Draft will give possibility to admin and reseller to modify invoice after generation whereas Confirmed invoices will be readonly.' WHERE `system`.`id` = 216;

--
-- add call request field in CDRs
--
ALTER TABLE `cdrs` ADD `call_request` TINYINT( 3 ) NOT NULL DEFAULT '0';
ALTER TABLE `reseller_cdrs` ADD `call_request` TINYINT( 3 ) NOT NULL DEFAULT '0';

--
-- sip profile table query
--
ALTER TABLE `sip_profiles` CHANGE `sip_ip` `sip_ip` VARCHAR(39) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

--
-- Update to latest version 
--
UPDATE `system` SET `value` ='3.6' WHERE `system`.`id` = 191;
