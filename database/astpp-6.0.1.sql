DROP TABLE IF EXISTS `login_activity_report`;

CREATE TABLE `login_activity_report` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `country_name` varchar(200) NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `login_activity_report` VALUES (1,1,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36','','::1','2021-05-12 07:46:07');

DROP TABLE IF EXISTS `activity_reports`;

CREATE TABLE `activity_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `accountid` int NOT NULL,
  `reseller_id` int NOT NULL DEFAULT '1',
  `last_did_call_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_outbound_call_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `balance` varchar(40) NOT NULL,
  `credit_limit` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accountid` (`accountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES(NULL, 'Activity Report', 'activity_report', 'activity_report/activityReport/', 'Reports', "Activity-Reports.png", '0', 82.1);

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(NULL, 0, 0, 'reports','activity_report','0','activityReport', 'Call Activity Report', '["main","list","search","export"]', 0, '2019-01-25 09:01:03', '9.96000'); 

UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules WHERE module_url = 'activity_report/activityReport/' ) ) WHERE userlevelid = -1;

DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`astppuser`@`localhost`*/ /*!50003 TRIGGER `activity_reports` AFTER INSERT ON `cdrs` FOR EACH ROW BEGIN
IF (NEW.calltype = 'DID' AND NEW.call_direction = 'outbound') THEN
  INSERT INTO `activity_reports` (accountid,reseller_id,last_did_call_time,balance,credit_limit) VALUES (NEW.accountid, NEW.reseller_id, NEW.callstart,(SELECT balance from accounts where id=NEW.accountid),(SELECT credit_limit from accounts where id=NEW.accountid)) ON DUPLICATE KEY UPDATE `last_did_call_time`=NEW.callstart,`balance`=VALUES(balance),`credit_limit`=VALUES(credit_limit);
ELSEIF (NEW.calltype = 'STANDARD') THEN
    INSERT INTO `activity_reports` (accountid, reseller_id,last_outbound_call_time,balance,credit_limit) VALUES (NEW.accountid, NEW.reseller_id, NEW.callstart,(SELECT balance from accounts where id=NEW.accountid),(SELECT credit_limit from accounts where id=NEW.accountid)) ON DUPLICATE KEY UPDATE `last_outbound_call_time`=NEW.callstart,`balance`=VALUES(balance),`credit_limit`=VALUES(credit_limit);
END IF;
END */;;
DELIMITER ;

update invoice_conf set company_name='Inextrix Technologies Pvt. Ltd.', website_title='Inextrix Technologies Pvt. Ltd.', website_footer='Inextrix Technologies Pvt. Ltd.' where id=1;

update `system` set comment='Set Mail Log Path Here' where display_name='Mail Log';
-- ASTPPCOM-944 Jaimin Start
ALTER TABLE `trunks` ADD COLUMN `sip_cid_type` VARCHAR(50) NOT NULL COMMENT 'none:- None, rpid :- Remote-Party-ID, pid :- P-Asserted-Identity';
-- END

-- ASTPPCOM-945 Jaimin Start
  ALTER TABLE `sip_devices` ADD COLUMN `codec` varchar(100) NOT NULL AFTER `dir_vars`;
-- End

-- ASTPPCOM-942 Sanket Start
update `system` set value='6.0' where name='version';
-- End

-- ASTPPCOM-975 Kinjal Start
ALTER TABLE `system` CHANGE `sub_group` `sub_group` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
UPDATE `system` SET `sub_group`='Alert Notifications <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>' WHERE `name` = 'alert_notications';
-- ASTPPCOM-975 Kinjal END

-- ASTPPCOM-975 Kinjal Start

-- ASTPPCOM-978 Kinjal Start
CREATE TABLE `automated_report_log` ( 
  `id` int(11) AUTO_INCREMENT primary key NOT NULL, 
  `filename` varchar(100) NULL , 
  `usercode` varchar(50)  NULL , 
  `creation_date` datetime NOT NULL,
  `purge_date` date NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `system` (`id`,`name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES(NULL,'automated_report_attachment_deleted','Delete Automated Report Attachment After Days','1','default_system_input','Here -1 means disable and any positive value means that much of days.All attachment will delete from folder after selected time here.','0000-00-00 00:00:00',0,0,'purge','','');
-- ASTPPCOM-978 Kinjal END

-- ASTPPCOM-1039 Sanket Start
update menu_modules set menu_label='My Order' where module_url='user/user_products_list/';
-- End

-- ASTPPCOM-982 Ashish Start
ALTER TABLE cdrs MODIFY COLUMN calltype  enum('STANDARD','DID','FREE','CALLINGCARD','FAX','LOCAL','BROADCAST') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'STANDARD';
-- ASTPPCOM-982 Ashish End

-- ASTPPCOM-1180 Kinjal Start
ALTER TABLE automated_report_log MODIFY purge_date DATE NULL;
-- ASTPPCOM-1180 Kinjal Start

-- ASTPPCOM-1028 Kinjal Start
UPDATE default_templates SET template='<p>Dear #NAME#,</p>\n\n<p>The product #PRODUCT_NAME# has now been added to your account.</p>\n\n<p><strong>Product Information: </strong></p>\n\n<p>Product Name: #PRODUCT_NAME#<br />\nProduct Category: #PRODUCT_CATEGORY#<br />\nPayment Method: #PAYMENT_METHOD#<br />\nProduct Amount: #PRODUCT_AMOUNT#<br />\nNext Bill Date: #NEXT_BILL_DATE#<br />\nQuantity:#QUANTITY#<br />\nTotal Amount:#TOTAL_PRICE# </p>\n\n<p>You can always let us know if you have any question at #COMPANY_EMAIL#. We will be happy to help!</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n' WHERE name='product_purchase';
-- ASTPPCOM-1028 Kinjal END

-- ASTPPCOM-1366 Kinjal Start
Delete from `system` where `name` = 'paypal_id' and `reseller_id` != 0;
-- ASTPPCOM-1366 Kinjal END

-- ASTPPCOM-1307 Gautam Start
INSERT INTO `translations` (`id`, `module_name`, `en_En`) VALUES (NULL, 'Whats New', 'Whats New'),(NULL, 'Up Time', 'Up Time'),(NULL, 'CPU Cores', 'CPU Cores'),(NULL, 'Hard Disk Usage', 'Hard Disk Usage'),(NULL, 'Operating System', 'Operating System'),(NULL, 'Available', 'Available'),(NULL, 'CPU Usage', 'CPU Usage'),(NULL, 'Kernal Version', 'Kernal Version'),(NULL, 'Memory Usage', 'Memory Usage'),(NULL, 'Shared', 'Shared'),(NULL, 'Cache', 'Cache'),(NULL, 'Donate Now', 'Donate Now'),(NULL, 'Professional Services', 'Professional Services'),(NULL, 'Report an Issue', 'Report an Issue'),(NULL, 'Forum', 'Forum'),(NULL, 'OS', 'OS'),(NULL, 'Architecture', 'Architecture'),(NULL, 'Static Hostname', 'Static Hostname'),(NULL, 'Memory', 'Memory'),(NULL, 'Swap', 'Swap');
-- ASTPPCOM-1307 Gautam end