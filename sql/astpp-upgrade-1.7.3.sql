CREATE TABLE IF NOT EXISTS `payment_transaction` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `amount` decimal(10,5) NOT NULL,
  `description` varchar(255) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `commission_percent` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


ALTER TABLE `system` ADD UNIQUE `unq` ( `name` , `reseller_id` , `brand_id` , `group_title` );

INSERT INTO `system` ( `name` , `value` , `comment` , `timestamp` , `reseller_id` , `brand_id` , `group_title` )
VALUES (
'paypal_status', '0', '0=enable paypal module 1=disable paypal module', NULL , 0, 0, 'paypal'
), (
'paypal_url', 'https://www.paypal.com/cgi-bin/webscr', 'paypal live url', NULL , 0, 0, 'paypal'
), (
'paypal_sandbox_url', 'https://www.sandbox.paypal.com/cgi-bin/webscr', 'Paypal Sandbox url for testing', NULL , 0, 0, 'paypal'
), (
'paypal_id', 'your@paypal.com', 'Paypal Live account id', NULL , 0, 0, 'paypal'
), (
'paypal_sandbox_id', 'your@paypal.com', 'Paypal sandbox accountid for testing', NULL , 0, 0, 'paypal'
), (
'paypal_mode', '0', '0=paypal Live mode 1= paypal Sandbox mode', NULL , 0, 0, 'paypal'
), (
'paypal_fee', '1', '0=paypal mc fee paid by admin 1= paypal mc fee paid by customer', NULL , 0, 0, 'paypal'
), (
'paypal_tax', '1', 'Paypal tax rate (in percentage) apply to recharge amount', NULL , 0, 0, 'paypal'
), (
'opensips_ip', 'Opensips IP', 'Opensips IP for ACL', NULL , 0, 0, 'opensips'
), (
'version', '1.7.3', 'ASTPP Version', NULL , 0, 0, 'global'
);

ALTER TABLE payments ADD `paypalid` INT NOT NULL;
ALTER TABLE accounts ADD `notify_credit_limit` INT NOT NULL; 
ALTER TABLE `accounts` ADD `notify_flag` TINYINT( 1 ) NOT NULL; 
ALTER TABLE `accounts` ADD `notify_email` VARCHAR( 80 ) NOT NULL; 
ALTER TABLE `accounts` ADD `commission_rate` INT default 0 NOT NULL;
ALTER TABLE `accounts` ADD `invoice_day` INT NOT NULL DEFAULT '0' COMMENT 'invoice day if 0 then generate daily base else on difine day' ;
ALTER TABLE `invoices` CHANGE `date` `invoice_date` TIMESTAMP NOT NULL; 

ALTER TABLE `invoices` ADD `from_date` TIMESTAMP NOT NULL AFTER `invoice_date` ,
ADD `to_date` TIMESTAMP NOT NULL AFTER `from_date` ,
ADD `due_date` DATE NOT NULL AFTER `to_date` ,
ADD `paid_date` TIMESTAMP NOT NULL AFTER `due_date` ; 

ALTER TABLE `invoices` CHANGE `status` `paid_status` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '0:unpaid,1:paid';

ALTER TABLE `invoices_total` ADD `tax` VARCHAR( 100 ) NOT NULL AFTER `text` ;

RENAME TABLE `customer_log` TO `invoice_item` ; 
ALTER TABLE `invoice_item` ADD `invoiceid` INT NOT NULL DEFAULT '0' AFTER `credit` ; 
ALTER TABLE `invoices_total` CHANGE `invoices_id` `invoiceid` INT( 11 ) NOT NULL DEFAULT '0' ;

DROP view if exists taxes_to_accounts_view;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `taxes_to_accounts_view` AS select `taxes_to_accounts`.`id` AS `id`,`taxes_to_accounts`.`accountid` AS `accountid`,`taxes`.`id` AS `taxes_id`,`taxes`.`taxes_priority` AS `taxes_priority`,`taxes`.`taxes_amount` AS `taxes_amount`,`taxes`.`taxes_rate` AS `taxes_rate`,`taxes`.`taxes_description` AS `taxes_description` from (`taxes_to_accounts` join `taxes`) where (`taxes`.`id` = `taxes_to_accounts`.`taxes_id`);

ALTER  TABLE  `trunks`  CHANGE  `resellers_id`  `reseller_id` VARCHAR( 11  )  CHARACTER  SET utf8 COLLATE utf8_general_ci NOT  NULL DEFAULT  '0';

DROP view if exists invoice_list_view;
CREATE VIEW `invoice_list_view` AS select `invoices`.`id` AS `invoiceid`,`invoices`.`accountid` AS `accountid`,`invoices`.`invoice_date` AS `date`,`invoices`.`paid_status` AS `paid_status`,`invoices_total`.`value` AS `value`,`invoices_total`.`class` AS `class` from (`invoices` join `invoices_total`) where ((`invoices_total`.`class` = 9) and (`invoices`.`id` = `invoices_total`.`invoiceid`));


/*Modification in tables */
UPDATE userlevels set module_permissions='1,2,4,5,3,6,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,7,29,30,38,39,40,41,42,43,44,45,46' where userlevelid=-1;

UPDATE userlevels set module_permissions=' 1,2,7,8,9,10,11,12,13,16,17,18,19,21,28,38,40,46' where userlevelid=1;
UPDATE `userlevels` SET `module_permissions` = '36,47,32,37,31,35,34' WHERE `userlevels`.`userlevelid` =0;
UPDATE `userlevels` SET `module_permissions` = '15,22,24,31,50' WHERE `userlevels`.`userlevelid` =3;
UPDATE `userlevels` SET `module_permissions` = '12,19,20,21,22,24,51,50,48,44,45,46' WHERE `userlevels`.`userlevelid` =4;

ALTER TABLE `menu_modules` ADD UNIQUE `unq` ( `module_name` , `module_url` );

INSERT INTO `menu_modules` (`menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`) VALUES('Commission Reports', 'commission', 'reports/reseller_commissionreport/', 'Reports', 'PaymentReport.png', 'Payment Reports'); 

INSERT INTO `menu_modules` (`menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`) VALUES
('IP Map', 'user', 'user/user_ipmap_list/', 'IP Settings', 'Providers.png', 'IP Setting'),
('Reseller Report', 'reseller', 'reports/reseller_summery_Report/', 'Reports', 'cdr.png', 'Summary Reports'),
('Provider Report', 'provider', 'reports/provider_summery_Report/', 'Reports', 'cdr.png', 'Summary Reports'),
('Payment Report', 'customer', 'reports/customer_paymentreport/', 'Reports', 'PaymentReport.png', 'Payment Reports');

UPDATE `menu_modules` SET `menu_label` = 'My Rates' WHERE `menu_modules`.`id` =37;
