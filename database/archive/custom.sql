UPDATE `userlevels` SET `module_permissions` = '1,2,4,5,3,8,9,13,14,15,16,17,18,19,20,21,22,25,26,27,28,7,29,30,45,38,39,40,41,42,43,44,48,49,51,53,54,55,56,66,68,69,77,78,79,80,81,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,177,178,179,180,149,184,185,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,221,226,227,228,229,230,231,232,233,234,235,236,237,238,243,244,245,246,247,248,249,250,251,252,253,254,255,256,269,270,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342' WHERE `userlevels`.`userlevelid` = -1;



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'tax_type', 'Default Tax', '1,2', 'tax_type', 'Set Default taxes for tax_description', '0000-00-00 00:00:00', '0', '0', 'signup', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'notifications', 'Account Notification', '0', 'enable_disable_option', 'Set enable to account notification', '0000-00-00 00:00:00', '0', '0', 'signup', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'paypal_permission', 'Paypal Permission', '0', 'enable_disable_option', 'Set paypal permission', '0000-00-00 00:00:00', '0', '0', 'signup', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'generate_pin', 'Generate Pin', '0', 'set_prorate', 'Allow to Generate Pin', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'cps', 'CPS', '1', 'default_system_input', 'Allow to CPS', '0000-00-00 00:00:00', '0', '0', 'signup', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'maxchannels', 'Concurrent Calls', '1', 'default_system_input', 'Allow to Concurrent Calls', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'password_type', 'Password Strength', '0', 'default_password_input', 'Set Password Security Type For New Password Creation', '0000-00-00 00:00:00', '0', '0', 'global', 'General');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'charge_per_min', 'LC Charge / Min', '1', 'default_system_input', 'Allow to LC Charge / Min', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'loss_less_routing', 'Allow Loss Less Routing', '0', 'set_prorate', 'Allow Loss Less Routing', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'is_recording', 'Allow Recording', '0', 'set_prorate', 'Allow to Recording', '0000-00-00 00:00:00', '0', '0', 'signup', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'notify_credit_limit', 'Balance Below', '1', 'default_system_input', 'Balance Below', '0000-00-00 00:00:00', '0', '0', 'signup', '');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'allow_ip_management', 'Allow IP Management', '0', 'set_prorate', 'Allow to IP Management', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'local_call', 'Allow Local Calls', '0', 'set_prorate', 'Allow to Local Calls', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'notify_flag', 'Email Alerts ?', '0', 'set_prorate', 'Allow to Email Alerts', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'validfordays', 'Account Valid Days', '1', 'default_system_input', 'Allow to Valid Days', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'localization_id', 'Localization Type', '1', 'set_prorate', 'Allow Localization', '0000-00-00 00:00:00', '0', '0', 'signup', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'charge_per_min', 'LC Charge / Min', '1', 'default_system_input', 'Allow to LC Charge / Min', '0000-00-00 00:00:00', '0', '0', 'signup', '');


ALTER TABLE `accounts` ADD `loss_less_routing` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_recording`;



INSERT INTO `category` (`id`, `name`, `code`, `description`, `status`, `creation_date`) VALUES
(1, 'Package', 'PACKAGE', 'Package', 0, '0000-00-00 00:00:00'),
(2, 'Subscription', 'SUBSCRIPTION', 'Subscription', 0, '0000-00-00 00:00:00'),
(3, 'Refill', 'REFILL', 'Rfill', 0, '0000-00-00 00:00:00'),
(4, 'DID', 'DID', 'DIDs', 0, '0000-00-00 00:00:00');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`) VALUES (NULL, 'trunk_count', 'Trunk Count', '3', 'default_system_input', 'Priority trunk count', NULL, '0', '0', 'global');



 ALTER TABLE `userlevels` CHANGE `module_permissions` `module_permissions` VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

--
-- Stand-in structure for view `view_dids`
-- (See below for the actual view)
--
CREATE TABLE `view_dids` (
`id` int(11)
,`number` varchar(40)
,`reseller_product_id` int(11)
,`account_id` int(11)
,`reseller_id` int(11)
,`buyer_accountid` bigint(11)
,`country_id` int(3)
,`cost` double(20,5)
,`call_type` tinyint(1)
,`leg_timeout` int(4)
,`maxchannels` int(4)
,`extensions` varchar(180)
,`buy_cost` decimal(20,5)
,`setup_fee` decimal(20,5)
,`price` decimal(20,5)
,`billing_type` tinyint(2)
,`billing_days` int(11)
,`product_id` int(11)
,`modified_date` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_invoices`
-- (See below for the actual view)
--
CREATE TABLE `view_invoices` (
`id` int(11)
,`number` varchar(280)
,`accountid` int(11)
,`reseller_id` int(11)
,`from_date` datetime
,`to_date` datetime
,`due_date` datetime
,`status` tinyint(1)
,`generate_date` datetime
,`type` enum('I','R')
,`payment_id` int(11)
,`generate_type` int(10)
,`confirm` int(10)
,`notes` longtext
,`is_deleted` tinyint(1)
,`debit` decimal(52,10)
,`credit` decimal(52,10)
);

ALTER TABLE `invoices` ADD `payment_id` INT(11) NOT NULL DEFAULT '0' AFTER `reseller_id`;

-- --------------------------------------------------------

--
-- Structure for view `view_dids`
--
DROP TABLE IF EXISTS `view_dids`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_dids`  AS  (select `dids`.`id` AS `id`,`dids`.`number` AS `number`,`reseller_products`.`id` AS `reseller_product_id`,`reseller_products`.`account_id` AS `account_id`,`reseller_products`.`reseller_id` AS `reseller_id`,if((`dids`.`parent_id` <> `reseller_products`.`account_id`),(select `subrpro`.`account_id` from `reseller_products` `subrpro` where (`subrpro`.`id` > `reseller_products`.`id`) order by `subrpro`.`id` limit 1),`dids`.`accountid`) AS `buyer_accountid`,`dids`.`country_id` AS `country_id`,`dids`.`cost` AS `cost`,`dids`.`call_type` AS `call_type`,`dids`.`leg_timeout` AS `leg_timeout`,`dids`.`maxchannels` AS `maxchannels`,`dids`.`extensions` AS `extensions`,`reseller_products`.`buy_cost` AS `buy_cost`,`reseller_products`.`setup_fee` AS `setup_fee`,`reseller_products`.`price` AS `price`,`reseller_products`.`billing_type` AS `billing_type`,`reseller_products`.`billing_days` AS `billing_days`,`reseller_products`.`product_id` AS `product_id`,`reseller_products`.`modified_date` AS `modified_date` from (`reseller_products` join `dids` on((`dids`.`product_id` = `reseller_products`.`product_id`))) where (`reseller_products`.`is_optin` = 0) order by `reseller_products`.`account_id`) ;

-- --------------------------------------------------------

--
-- Structure for view `view_invoices`
--
DROP TABLE IF EXISTS `view_invoices`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_invoices`  AS  (select `invoices`.`id` AS `id`,concat(`invoices`.`prefix`,`invoices`.`number`) AS `number`,`invoices`.`accountid` AS `accountid`,`invoices`.`reseller_id` AS `reseller_id`,`invoices`.`from_date` AS `from_date`,`invoices`.`to_date` AS `to_date`,`invoices`.`due_date` AS `due_date`,`invoices`.`status` AS `status`,`invoices`.`generate_date` AS `generate_date`,`invoices`.`type` AS `type`,`invoices`.`payment_id` AS `payment_id`,`invoices`.`generate_type` AS `generate_type`,`invoices`.`confirm` AS `confirm`,`invoices`.`notes` AS `notes`,`invoices`.`is_deleted` AS `is_deleted`,sum((`invoice_details`.`debit` * `invoice_details`.`exchange_rate`)) AS `debit`,sum((`invoice_details`.`credit` * `invoice_details`.`exchange_rate`)) AS `credit` from (`invoices` join `invoice_details` on((`invoices`.`id` = `invoice_details`.`invoiceid`))) group by `invoice_details`.`invoiceid`) ;


DROP TABLE IF EXISTS `default_templates`;


CREATE TABLE `default_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL DEFAULT '',
  `subject` varchar(500) NOT NULL,
  `description` varchar(512) NOT NULL,
  `sms_template` varchar(500) NOT NULL,
  `alert_template` varchar(500) NOT NULL,
  `template` mediumtext NOT NULL,
  `last_modified_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `is_email_enable` tinyint(1) NOT NULL DEFAULT '0',
  `is_sms_enable` tinyint(1) NOT NULL,
  `is_alert_enable` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `default_templates`
--

INSERT INTO `default_templates` (`id`, `name`, `subject`, `description`, `sms_template`, `alert_template`, `template`, `last_modified_date`, `reseller_id`, `is_email_enable`, `is_sms_enable`, `is_alert_enable`, `status`) VALUES
(1, 'account_refilled', 'Your account recharged with #REFILLBALANCE#', 'Account recharge notification template', 'Dear #FIRST_NAME#, Your account has been recharged with #REFILLBALANCE#. Your new balance is #BALANCE#. Thanks, #COMPANY_NAME#', '<p>Your account has been recharged with #REFILLBALANCE#. Your new balance is #BALANCE#.</p>\n', '<p>Dear #NAME#,</p>\n\n<p>Your account has been recharged with #REFILLBALANCE#.</p>\n\n<p>Your account new balance is #BALANCE#.</p>\n\n<p>For more info, please visit on our website #COMPANY_WEBSITE# or contact to our support department at #COMPANY_EMAIL#.</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:53:45', 0, 0, 0, 0, 0),
(3, 'create_account', 'Welcome to #COMPANY_NAME#', 'New customer welcome notification template', 'Dear #FIRST_NAME#, Your new account has been created. Your account number is #NUMBER# and Password is #PASSWORD#. Thanks, #COMPANY_NAME#', '', '<p>Welcome #NAME#,</p>\r\n\r\n<p>Your new account has been created. You can log in into customer portal using below login credential,</p>\r\n\r\n<p>Account Number: #NUMBER#<br />\r\nPassword: #PASSWORD#<p/>\r\n\r\n<p>For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\r\n\r\n<p>Thanks,<br />\r\n#COMPANY_NAME#</p>\r\n', '2019-01-26 10:02:18', 0, 0, 1, 0, 0),
(4, 'create_sip_device', 'New SIP device added to your account', 'New SIP Device notification template', 'Dear #FIRST_NAME#, New SIP Device has been added to your account. Username is #USERNAME# and Password is #PASSWORD#. Thanks, #COMPANY_NAME#', '', '<p>Dear #NAME#,</p>\n\n<p>New SIP device has been added to your account.</p>\n\n<p><strong>Here is your SIP device information,</strong></p>\n\n<p>Username: #USERNAME#<br />\nPassword: #PASSWORD#</p>\n\n<p>&nbsp;</p>\n\n<p>For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:02:05', 0, 0, 0, 0, 0),
(10, 'new_invoice', 'Invoice created #INVOICE_NUMBER#', 'New invoice notification template', 'Dear #FIRST_NAME#, A new invoice #INVOICE_NUMBER# has been generated into your account of #AMOUNT#. You can log in into customer portal and pay the invoice. Thanks, #COMPANY_NAME#', '<p>A new invoice #INVOICE_NUMBER# has been generated into your account of #AMOUNT#.&nbsp;You can log in into customer portal and pay the invoice.</p>\n', '<p>Dear #NAME#,</p>\n\n<p>A new invoice has been generated into your account of #AMOUNT#.</p>\n\n<p><strong>Invoice Information: </strong></p>\n\n<p>Invoice Date: #INVOICE_DATE#<br />\nInvoice Number: #INVOICE_NUMBER#<br />\nDue Amount: #AMOUNT#<br />\nDue Date : #DUE_DATE#</p>\n\n<p>You can log in into customer portal and pay the invoice.</p>\n\n<p>For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:52:54', 0, 0, 0, 0, 0),
(11, 'low_balance', 'Low Balance notification #NUMBER#', 'Low balance notification template', 'Dear #FIRST_NAME#, You currently have #BALANCE# left in your account. Please make a deposit to avoid service interruptions. You can refill your account using our website. Thanks, #COMPANY_NAME#', '<p>Your current balance is at #BALANCE# which is below your set threshold. Please refill your account to ensure&nbsp;your services&nbsp;remain consistent.</p>\n', '<p>Dear #NAME#,</p>\n\n<p>Here is a quick reminder that your current balance is at #BALANCE# which is below your set threshold.</p>\n\n<p>You can refill your account from our website to ensure your services will remain consistent.</p>\n\n<p>For more info, please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n\n<p>Thanks,<br />\nCOMPANY_NAME#</p>\n', '2019-01-26 10:51:10', 0, 0, 0, 0, 0),
(12, 'signup_confirmation', 'Confirmation to activate account', 'Account activation email after signup process', 'Dear #FIRST_NAME#, Thanks for sign-up with us, Please use otp #OTP# to complete registration.\r\nThanks,\r\n#COMPANY_NAME#', '', 'Dear #NAME#,\r\n\r\nThanks for sign-up with us\r\n\r\nPlease use one time password #OTP# to activate your account and complete registration.\r\n\r\nFor more info Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.\r\n\r\nThanks,\r\n\r\n#COMPANY_NAME#\r\n', '2019-01-26 10:04:02', 0, 0, 0, 0, 0),
(13, 'new_password', 'Your account password changed', 'Forgot password notification template', 'Dear #FIRST_NAME#, Your account password has been changed. Your new password is #PASSWORD#. Thanks, #COMPANY_NAME# ', '', '<p>Dear #NAME#,</p>\n\n<p>Your account password has been changed.</p>\n\n<p>Please see your new password mentioned below: #PASSWORD#</p>\n\n<p>Henceforth, Please use the latest password.</p>\n\n<p>For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:04:18', 0, 0, 0, 0, 0),
(14, 'forgot_password_confirmation', 'Reset your password', 'Account confirmation notification template for forgot password', 'Dear #FIRST_NAME#, Please use otp #OTP# to reset your password. Thanks, #COMPANY_NAME#', '', '<p>Hi #NAME#,</p>\r\n\r\n<p>Please use one time password #OTP#  to reset your password.</p>\r\n\r\n<p>If you have not raised a request to reset password then please contact us immediately.</p>\r\n\r\n<p>For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#.</p>\r\n\r\n<p>Thanks,</p>\r\n\r\n<p>#COMPANY_NAME#</p>\r\n', '2019-01-26 10:05:29', 0, 0, 1, 0, 0),
(20, 'invoice_due_reminder', 'Invoice due reminder #INVOICE_NUMBER#', 'Invoice due reminder notification template', 'Dear #FIRST_NAME#, This is a reminder that your invoice #INVOICE_NUMBER# which was generated on #INVOICE_DATE# is due on #DUE_DATE# for #AMOUNT# amount.  You can log in into the customer portal and pay an invoice. Thanks, #COMPANY_NAME#', '<p>Your invoice #INVOICE_NUMBER# which was generated on #INVOICE_DATE# is due on #DUE_DATE# for #AMOUNT# amount.&nbsp;You can log in into the customer portal and pay&nbsp;an invoice.</p>\n', '<p>Dear #NAME#,</p>\n\n<p>This is a reminder that your invoice number #INVOICE_NUMBER# which was generated on #INVOICE_DATE# is due on #DUE_DATE#.</p>\n\n<p><strong>Invoice </strong><strong>Information:</strong></p>\n\n<p>Invoice Date: #INVOICE_DATE#<br />\nInvoice Number: #INVOICE_NUMBER#<br />\nDue Amount: #AMOUNT#</p>\n\n<p>You can log in into the customer portal and pay&nbsp;an invoice.</p>\n\n<p>For more info, Please visit on our website #COMPANY_WEBSITE# or contact to our support at #COMPANY_EMAIL#</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:46:49', 0, 0, 0, 0, 0),
(21, 'new_archive_table', 'New CDR archive table created #TABLE_NAME#', 'New CDR archive table creation notification template', 'Dear Admin, New CDR archive table has been created successfully to move old records. The table name is #TABLE_NAME#. Thanks, #COMPANY_NAME#', '', '<p>Dear Admin,</p>\n\n<p>New CDR archive table has been created successfully to move old records.</p>\n\n<p>The table name is #TABLE_NAME#.</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:45:29', 0, 1, 1, 1, 0),
(24, 'balance_tranfer', 'You have transferred #AMOUNT# to #RECEIVER_ACCOUNT_NUMBER# account', 'Balance transfer notification template', 'Dear #FIRST_NAME#, You have transferred #AMOUNT# from your account to #RECEIVER_ACCOUNT_NUMBER#. Thanks, #COMPANY_NAME#', '<p>You have transferred #AMOUNT# from your account to #RECEIVER_ACCOUNT_NUMBER#</p>\n', '<p>Dear #NAME#,</p>\n\n<p>You have transferred #AMOUNT# from your account to #RECEIVER_ACCOUNT_NUMBER#.</p>\n\n<p>If you have not raised a request then please contact us immediately.</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:44:35', 0, 1, 0, 0, 0),
(29, 'product_purchase', '#NAME#, New #PRODUCT_NAME# added to your account', 'New product allocation to account notification template', 'Dear #FIRST_NAME#, New #PRODUCT_NAME# added to your account. Thanks, #COMPANY_NAME#', '<p>New #PRODUCT_NAME# added to your account</p>\n', '<p>Dear #NAME#,</p>\n\n<p>The product #PRODUCT_NAME# has now been added to your account.</p>\n\n<p><strong>Product Information: </strong></p>\n\n<p>Product Name: #PRODUCT_NAME#<br />\nProduct Category: #PRODUCT_CATEGORY#<br />\nPayment Method: #PAYMENT_METHOD#<br />\nAmount: #PRODUCT_AMOUNT#<br />\nNext Bill Date: #NEXT_BILL_DATE#</p>\n\n<p>You can always let us know if you have any question at #COMPANY_EMAIL#. We will be happy to help!</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:44:20', 0, 0, 0, 0, 0),
(30, 'product_release', '#PRODUCT_NAME# released from your account #NUMBER#', 'Product release notification template', 'Dear #FIRST_NAME#, #PRODUCT_NAME# released from your account. Thanks, #COMPANY_NAME#', '<p>#PRODUCT_NAME# released from your account</p>\n', '<p>Dear #NAME#,</p>\n\n<p>The product #PRODUCT_NAME# is released from your account.</p>\n\n<p>You can always let us know if you have any question at #COMPANY_EMAIL#. We will be happy to help!</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:43:56', 0, 0, 0, 0, 0),
(31, 'product_renewal_notice', 'Renewal Notice for #PRODUCT_NAME#, #NUMBER#', 'Product renewal notice notification template', 'Dear #FIRST_NAME#, Your product #PRODUCT_NAME# is up for renewal on date #NEXT_BILL_DATE#. Please maintain your balance to ensure your services will remain consistent. Thanks, #COMPANY_NAME#', '<p>Your product #PRODUCT_NAME# is up for renewal on date #NEXT_BILL_DATE#. Please maintain your balance to ensure your services will remain consistent.&nbsp;</p>\n', '<p>Dear #NAME#,</p>\n\n<p>Your product #PRODUCT_NAME# is up for renewal, and it will automatically renew on the #NEXT_BILL_DATE#. Please maintain your balance to ensure your services will remain consistent.</p>\n\n<p><strong>Product Information: </strong></p>\n\n<p>Product Name: #PRODUCT_NAME#<br />\nAmount: #PRODUCT_AMOUNT#<br />\nNext Bill Date: #NEXT_BILL_DATE#</p>\n\n<p>You can always let us know if you have any question at #COMPANY_EMAIL#. We will be happy to help!</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:43:39', 0, 0, 0, 0, 0),
(32, 'product_renewed', '#PRODUCT_NAME# renewed for your account #NUMBER#', 'Product renewed notification template', 'Dear #FIRST_NAME#, Your product #PRODUCT_NAME# has been successfully renewed until #NEXT_BILL_DATE#. Thanks, #COMPANY_NAME#', '<p>Your product #PRODUCT_NAME# has been successfully renewed until #NEXT_BILL_DATE#</p>\n', '<p>Dear #NAME#,</p>\n\n<p>Your product #PRODUCT_NAME# has been successfully renewed until #NEXT_BILL_DATE#.</p>\n\n<p><strong>Product Information: </strong></p>\n\n<p>Product Name: #PRODUCT_NAME#<br />\nAmount: #PRODUCT_AMOUNT#<br />\nNext Bill Date: #NEXT_BILL_DATE#</p>\n\n<p>Remember, You can always let us know if you have any question at #COMPANY_EMAIL#. We will be happy to help!</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-01-26 10:43:13', 0, 0, 0, 1, 0),
(33, 'product_commission', 'Congratulations, Your have received #AMOUNT# for Product #PRODUCT_NAME# commission', 'Product commission notification template for re-seller', 'Dear #FIRST_NAME, #Congratulations, Your have received #AMOUNT# for Product #PRODUCT_NAME# commission. Thanks, #COMPANY_NAME#', '', '<p>Dear #NAME#,</p>\n\n<p>Your have received #AMOUNT# for Product #PRODUCT_NAME# commission. Your updated balance is #BALANCE#.</p>\n\n<p>You can always let us know if you have any question at #COMPANY_EMAIL#. We will be happy to help!</p>\n\n<p>Thanks,<br />\n#COMPANY_NAME#</p>\n', '2019-02-15 09:42:58', 0, 0, 0, 0, 0);

ALTER TABLE `default_templates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `default_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'balance', 'Initial Balance', '10', 'default_system_input', 'Set balance for newly created customer', '0000-00-00 00:00:00', '0', '0', 'signup', '');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'tax_type', 'Default Tax', NULL, 'tax_type', 'Set Default taxes for tax_description', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'ewallet_payment', 'Ewallet Payment Gateway', 'paypal', 'ewallet_payment_gateway', 'Set your ewallet payment gateway', '0000-00-00 00:00:00', '0', '0', 'global', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'verification_by', 'Account Verification By', '2', 'set_prorate_verification', 'Set verification mode', '0000-00-00 00:00:00', '0', '0', 'signup', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'telephone_as_account', 'Telephone as account number', '0', 'set_prorate', 'Set Yes to use Telephone number as account number', '0000-00-00 00:00:00', '0', '0', 'signup', '');



ALTER TABLE `dids` CHANGE `accountid` `accountid` INT(11) NOT NULL DEFAULT '0' COMMENT 'Accounts table id';


ALTER TABLE `system` CHANGE `group_title` `group_title` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


ALTER TABLE `account_unverified` ADD `reseller_id` INT NOT NULL AFTER `id`;


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'allow_retires', 'Allow Max Retries', '5', '0', 'Set max retries of signup with same number or email. If exceed then block account and inform user to contact adminstrator', '0000-00-00 00:00:00', '0', '0', 'signup', '');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'sms_secret_key', 'Nexmo Secret Key', 'GK8I02zNhudLOdgX', 'default_system_input', 'Set your API Secret Key ', '0000-00-00 00:00:00', '0', '0', 'notifications', 'SMS');












INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'sms_api_key', 'Nexmo API Key', '7b52affa', 'default_system_input', 'Set your API Key for SMS', '0000-00-00 00:00:00', '0', '0', 'notifications', 'SMS');


INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Translation', 'translation', 'systems/translation_list/', 'Configuration', 'ManageDIDs.png', 'Languages', '90.7');

update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Languages', 'languages', 'systems/languages_list/', 'Configuration', 'ManageDIDs.png', 'Languages', '90.6');


update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;


UPDATE `roles_and_permission` SET `permissions` = '[\"main\",\"list\",\"create\",\"edit\"]' WHERE `login_type` =1 AND  module_name='invoices' AND  module_url='invoice_conf_list';



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'debug', 'Debug', '0', 'enable_disable_option', 'To enable call debug, set it to Enable. Logs will appear at /var/log/astpp/astpp.log', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'max_free_length', 'Max Free Length', '00', 'default_system_input', 'Set maximum length (In minutes) for calls that are at no charge', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'call_max_length', 'Call Max Length', '440000', 'default_system_input', 'Set maximum length (In ms) for call.', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'playback_audio_notification', 'Playback Audio Notifications', '0', 'enable_disable_option', 'Set enable to play audio notification in call', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'balance_announce', 'Balance Announcement', '0', 'enable_disable_option', 'To enable balance playback in call', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'minutes_announce', 'Minutes Announcement', '0', 'enable_disable_option', 'To enable minutes playback in call', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'voicemail_number', 'Voicemail Number', '7777', 'default_system_input', 'Voicemail listen number', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'leg_timeout', 'Local Call Timeout (Sec.)', '0', 'default_system_input', 'Define Local SIP2SIP Call Leg Timeout. Default 30 seconds', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'realtime_billing', 'Realtime Billing <b>(<a href=\'http://astpp.readthedocs.io/en/v3.6/Integrations/realtime_billing.html\' target=\"_blank\">Experimental</a>)</b>', '0', 'enable_disable_option', 'Set enable to use realtime\r\nbilling.', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'card_retries', 'Card Retries', '0', 'default_system_input', 'Set number of retries to validate card number', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card'), (NULL, 'pin_length', 'Pin Length', '6', 'default_system_input', 'Set number of digits for pin numbers', '0000-00-00 00:00:00', '0', '0', 'signup', '');	


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'calling_cards_rate_announce', 'Rate Announcement', '0', 'enable_disable_option', 'Enable it to announce rate of the call', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'calling_cards_timelimit_announce', 'Timelimit Announcement', '0', 'enable_disable_option', 'Enable it to announce the time-limit on call', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'calling_cards_pin_input_timeout', 'Pin Input Timeout', '5000', 'default_system_input', 'How long do we wait when entering the pin number? Specified in MS', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'calling_cards_number_input_timeout', 'Card Input Timeout', '5000', 'default_system_input', 'How long do we wait when entering the calling card number?  Specified in MS', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'calling_cards_dial_input_timeout', 'Dial Input Timeout', '5000', 'default_system_input', 'How long do we wait when entering the destination number in calling cards? Specified in MS', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'calling_cards_general_input_timeout', 'General Input Timeout', '5000', 'default_system_input', 'How long do we wait for input in general menus?  Specified in MS', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'calling_cards_welcome_file', 'Welcome File', 'astpp-welcome.wav', 'default_system_input', 'Set your calling card welcome file', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'cc_ani_auth', 'Pinless Authentication', '0', 'enable_disable_option', 'Set enable to use ANI Authentication', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'ivr_count', 'IVR Count', '2', 'default_system_input', 'Number of time IVR should play in call', '0000-00-00 00:00:00', '0', '0', 'calls', 'Calling Card');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'balance_announce', 'Balance Announcement', '0', 'enable_disable_option', 'To enable balance playback in call', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'outbound_fax', 'Outbound Fax', '0', 'enable_disable_option', 'Set yes to allow outbound fax in call', '0000-00-00 00:00:00', '0', '0', 'calls', 'Fax');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'inbound_fax', 'Inbound Fax', '0', 'enable_disable_option', 'Set enable to allow inbound fax in call', '0000-00-00 00:00:00', '0', '0', 'calls', 'Fax');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'enable_database', 'Archive', '0', 'enable_disable_option', 'Set enable to activate CDR archive feature', '0000-00-00 00:00:00', '0', '0', 'database', '');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'default_database_type', 'Interval', '0', 'default_system_input', 'Select Interval of archive cdrs. Example: If 6 Months selected then older than 6 Months records will be archived automatically.', '0000-00-00 00:00:00', '0', '0', 'database', '');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'purge_recordings', 'Recording Files Older Than Days', '-', 'default_system_input', 'Remove recordings from directory older than defined days', '0000-00-00 00:00:00', '0', '0', 'purge', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'purge_audio_log', 'Audit Logs Older Than Days', '-', 'default_system_input', 'Remove audit log from table older than defined days', '0000-00-00 00:00:00', '0', '0', 'purge', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'purge_cdrs', 'CDRs Older Than Days', '-', 'default_system_input', 'Remove CDRs from table older than defined days', '0000-00-00 00:00:00', '0', '0', 'purge', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'purge_emails', 'Emails Older Than Days', '-', 'default_system_input', 'Remove emails from table older than defined days', '0000-00-00 00:00:00', '0', '0', 'purge', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'purge_invoices', 'Inovices Older Than Days', '-', 'default_system_input', 'Remove invoices from table older than defined days', '0000-00-00 00:00:00', '0', '0', 'purge', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'purge_accounts_expired', 'Expired Accounts After Days', '-', 'default_system_input', 'Removed expired accounts from table after defined days', '0000-00-00 00:00:00', '0', '0', 'purge', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'purge_accounts_deleted', 'Deleted Accounts After Days', '-', 'default_system_input', 'Remove deleted accounts from table after defined days', '0000-00-00 00:00:00', '0', '0', 'purge', '');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'email', 'Email Notifications', '0', 'enable_disable_option', 'Set enable to send email notifications', '0000-00-00 00:00:00', '0', '0', 'notifications', 'Email');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'smtp', 'SMTP', '0', 'enable_disable_option', 'Set yes to use SMTP connection to send email and no to use sendmail connection to send email', '0000-00-00 00:00:00', '0', '0', 'notifications', 'Email');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'smtp_host', 'SMTP Host', 'SMTP_HOST', 'default_system_input', 'Set SMTP hostname ', '0000-00-00 00:00:00', '0', '0', 'notifications', 'Email');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'smtp_port', 'SMTP Port', '45', 'default_system_input', 'Set SMTP port', '0000-00-00 00:00:00', '0', '0', 'notifications', 'Email');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'smtp_user', 'SMTP User', 'SMTP_USER_NAME', 'default_system_input', 'Set SMTP username', '0000-00-00 00:00:00', '0', '0', 'notifications', 'Email');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'smtp_user', 'SMTP User', 'SMTP_USER_NAME', 'default_system_input', 'Set SMTP username', '0000-00-00 00:00:00', '0', '0', 'notifications', 'Email');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'mail_log', 'Mail Log', '/backup/log/astpp_email.log', 'default_system_input', NULL, '0000-00-00 00:00:00', '0', '0', 'notifications', 'Email');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'sms_notications', 'SMS Notifications', '0', 'enable_disable_option', 'Set Enable To Use SMS Notification ', '0000-00-00 00:00:00', '0', '0', 'notifications', 'SMS');


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'sms_api_key', 'Nexmo API Key', '7b52affa', 'default_system_input', 'Set your API Key for SMS', '0000-00-00 00:00:00', '0', '0', 'notifications', 'SMS');



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'sms_secret_key', 'Nexmo Secret Key', 'GK8I02zNhudLOdgX', 'default_system_input', 'Set your API Secret Key ', '0000-00-00 00:00:00', '0', '0', 'notifications', 'SMS');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES (NULL, 'alert_notications', 'Push Notifications', '0', 'enable_disable_option', 'Set enable to send notifications to accounts', '0000-00-00 00:00:00', '0', '0', 'notifications', 'Push Notifications');


ALTER TABLE `system` CHANGE `group_title` `group_title` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


UPDATE `system` SET `field_type` = 'set_localization_verification' WHERE `system`.`name` = 'localization_id';



