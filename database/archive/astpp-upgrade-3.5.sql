-- =================================================================================================
-- ------------------------------------- ASTPP Upgrade 3.5 -----------------------------------------
-- =================================================================================================

-- Accounts table update queries
update `accounts`  set balance = balance*(-1) where posttoexternal=1;

-- cdr table queries 
ALTER TABLE `cdrs` CHANGE `callerid` `callerid` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `reseller_cdrs` CHANGE `callerid` `callerid` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

-- invoice_conf table query
ALTER TABLE `invoice_conf` ADD `favicon` VARCHAR( 100 ) NOT NULL AFTER `logo` ;

-- New fields to add leg_timeout in calls
ALTER TABLE `dids` ADD `leg_timeout` INT(4) NOT NULL DEFAULT '30' AFTER `inuse`;
ALTER TABLE `trunks` ADD `leg_timeout` INT(4) NOT NULL DEFAULT '30' AFTER `codec`;

-- Call translation queries 
ALTER TABLE `accounts` ADD `std_cid_translation` VARCHAR(100) NOT NULL AFTER `allow_ip_management`, ADD `did_cid_translation` VARCHAR(100) NOT NULL AFTER`std_cid_translation`;
ALTER TABLE `trunks` ADD `cid_translation` VARCHAR(100) NOT NULL;

-- menu_modules table update queries
UPDATE `menu_modules` SET `menu_title` = 'Switch' WHERE `menu_modules`.`id` =29;
UPDATE `menu_modules` SET `menu_title` = 'Switch' WHERE `menu_modules`.`id` =30;
UPDATE `menu_modules` SET `menu_title` = 'Configuration' WHERE `menu_modules`.`id` =76;

-- userlevels table query
UPDATE `userlevels` SET `module_permissions` = '31,32,37,36,34,35,33,63,64,67,70,71,73,74,76,72' WHERE `userlevels`.`userlevelid` = 0; 

-- Change decimal points in all require tables 
ALTER TABLE `accounts` CHANGE `credit_limit` `credit_limit` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `accounts` CHANGE `local_call_cost` `local_call_cost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `dids` CHANGE `connectcost` `connectcost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `dids` CHANGE `monthlycost` `monthlycost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `cost` `cost` DOUBLE(20,5) NOT NULL DEFAULT '0.00000', CHANGE `setup` `setup` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `disconnectionfee` `disconnectionfee` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `invoice_details` CHANGE `debit` `debit` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `credit` `credit` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `outbound_routes` CHANGE `connectcost` `connectcost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `cost` `cost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `payments` CHANGE `credit` `credit` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `payment_transaction` CHANGE `amount` `amount` DECIMAL(20,5) NOT NULL, CHANGE `actual_amount` `actual_amount` DECIMAL(20,5) NOT NULL, CHANGE `paypal_fee` `paypal_fee` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `refill_coupon` CHANGE `amount` `amount` DECIMAL(20,5) NOT NULL;
ALTER TABLE `reseller_cdrs` CHANGE `rate_cost` `rate_cost` DECIMAL(20,6) NOT NULL DEFAULT '0.00000';
ALTER TABLE `reseller_pricing` CHANGE `monthlycost` `monthlycost` DOUBLE(20,5) NOT NULL DEFAULT '0.00000', CHANGE `setup` `setup` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `cost` `cost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `connectcost` `connectcost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `disconnectionfee` `disconnectionfee` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';
ALTER TABLE `routes` CHANGE `connectcost` `connectcost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000', CHANGE `cost` `cost` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';

-- Remove unused tables
DROP TABLE post_load_modules_conf;
DROP TABLE post_load_switch_conf;

-- system table queries
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES (NULL, 'mail_debug', 'Mail Debug', NULL, 'enable_disable_option', 'To enable mail debug, set it to Enable. Logs will appear at LOG_PATH/astpp_mail.log', NULL, '', '', 'email');
INSERT INTO `system` (`id` ,`name` ,`display_name` ,`value` ,`field_type` ,`comment` ,`timestamp` ,`reseller_id` ,`brand_id` ,`group_title`)
VALUES (NULL , 'enterprise', 'Enterprise', '1', 'enable_disable_option', '0:Enable 1:Disable', NULL , '0', '0', 'global');
INSERT INTO `system` (`id` ,`name` ,`display_name` ,`value` ,`field_type` ,`comment` ,`timestamp` ,`reseller_id` ,`brand_id` ,`group_title`)
VALUES (NULL , 'automatic_invoice', 'Automatic Invoice', '1', 'automatic_invoice', '0:Automatic 1:Manual', NULL , '0', '0', 'global');
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ('0', 'log_path', 'Log Path', '/var/log/astpp/', 'default_system_input', 'ASTPP log files path', NULL, '0', '0', 'global');
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ('0', 'leg_timeout', 'Local Call Timeout (Sec.)', '30', 'default_system_input', 'Define Local SIP2SIP Call Leg Timeout. Default 30 seconds', NULL, '0', '0', 'global');
UPDATE `system` SET `display_name` = 'Inbound Fax' WHERE `system`.`name` = 'inbound_fax';
UPDATE `system` SET `value` = '3.5' WHERE `system`.`id` = 191;

UPDATE `system` SET `value` = '1' WHERE `system`.`id` = 24;
UPDATE `system` SET `value` = '0' WHERE `system`.`id` = 22;
UPDATE `system` SET `value` = '1' WHERE `system`.`id` = 196;
