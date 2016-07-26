INSERT INTO `menu_modules` (`menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES ('Ip MAP', 'ipmap', 'ipmap/ipmap_detail/', 'Switch', 'Gateway.png', '0', 60.5),('Caller Id', 'animap', 'animap/animap_detail/', 'Switch', 'Gateway.png', '0', 60.6),('Email History ', 'Email', 'email/email_history_list/', 'Call Reports', 'ListAccounts.png', '0', 72.1),('Email Mass', 'email', 'email/email_mass/', 'Accounts', 'email.jpg', '0', 10.4);

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Global', 'Configuration', 'systems/configuration/global', 'Configuration', '', 'Settings', '80.7');
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Opensips', 'Configuration', 'systems/configuration/opensips', 'Configuration', '', 'Settings', '80.8');
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Callingcard', 'Configuration', 'systems/configuration/callingcard', 'Configuration', '', 'Settings', '80.9');
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Freeswitch', 'Configuration', 'systems/configuration/freeswitch', 'Configuration', '', 'Settings', '80.10');
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Paypal', 'Configuration', 'systems/configuration/paypal', 'Configuration', '', 'Settings', '80.11');
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Email', 'Configuration', 'systems/configuration/email', 'Configuration', '', 'Settings', '80.12');

INSERT INTO `menu_modules` ( `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES ( 'Fund Transfer', 'customer', 'accounts/customer_transfer/', 'Payment', 'ListAccounts.png', '0', 1.9),( 'Recharge', 'user', 'user/user_payment/', 'Payment', 'ListAccounts.png', '0', 2);

CREATE TABLE IF NOT EXISTS `mail_details` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subject` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `from` varchar(100) NOT NULL,
  `to` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : Send 1: Not send',
  `template` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

UPDATE `userlevels` SET `module_permissions` = '1,2,7,8,13,16,17,18,19,21,25,28,38,40,44,45,46,52,27,9,29,53,54' WHERE `userlevels`.`userlevelid` =1;
UPDATE `userlevels` SET `module_permissions` = '1,2,4,5,3,8,9,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,7,29,30,45,38,39,40,41,42,43,44,48,49,51,53,54,55,56,57,58,59,61,62' WHERE `userlevels`.`userlevelid` = -1;

UPDATE `userlevels` SET `module_permissions` = '31,32,37,36,34,35,33,63,64' WHERE `userlevels`.`userlevelid` =0;

UPDATE `menu_modules` SET `menu_label` = 'IP MAP (ACL)' WHERE `menu_modules`.`id` =53;

ALTER TABLE `default_templates` ADD `reseller_id` INT( 11 ) NOT NULL DEFAULT '0';


INSERT INTO `system` (`name`, `value`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ('did_global_translation', '"27/1"', 'Global number translation for DID.', '2015-05-05 00:00:00', 0, 0, 'global');


ALTER TABLE `mail_details` ADD `attachment` VARCHAR( 100 ) NOT NULL AFTER `to` ;


INSERT INTO `system` (
`id` ,
`name` ,
`value` ,
`comment` ,
`timestamp` ,
`reseller_id` ,
`brand_id` ,
`group_title`
)
VALUES (
NULL , 'smtp', '0', 'Send out email using smtp? 0=no 1=yes', NULL , '0', '0', 'email'
);



INSERT INTO `system` (
`id` ,
`name` ,
`value` ,
`comment` ,
`timestamp` ,
`reseller_id` ,
`brand_id` ,
`group_title`
)
VALUES (
NULL , 'smtp_host', '', 'Host name for smtp connection', NULL , '0', '0', 'email'
);
INSERT INTO `system` (
`id` ,
`name` ,
`value` ,
`comment` ,
`timestamp` ,
`reseller_id` ,
`brand_id` ,
`group_title`
)
VALUES (
NULL , 'smtp_port', '465', 'Port name for smtp connection', NULL , '0', '0', 'email'
);
INSERT INTO `system` (
`id` ,
`name` ,
`value` ,
`comment` ,
`timestamp` ,
`reseller_id` ,
`brand_id` ,
`group_title`
)
VALUES (
NULL , 'smtp_user', '', 'User name for smtp connection', NULL , '0', '0', 'email'
);


INSERT INTO `system` (
`id` ,
`name` ,
`value` ,
`comment` ,
`timestamp` ,
`reseller_id` ,
`brand_id` ,
`group_title`
)
VALUES (
NULL , 'smtp_pass', '', 'Password name for smtp connection', NULL , '0', '0', 'email'
);

INSERT INTO `system` (`id`, `name`, `value`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES (NULL, 'playback_audio_notification', '1', 'Global audio notification', NULL, '0', '0', 'global');

DELETE FROM `system` WHERE name='emailadd' OR name='user_email' OR name='company_email';
update `system` set group_title='email' WHERE `name`='email';

UPDATE `system` SET `value` = '2.1' WHERE `system`.`name` ='version';
UPDATE `system` SET `value` = '172' WHERE `system`.`name` ='country';

UPDATE `menu_modules` SET `menu_label` = 'Caller Id',`menu_title` = 'Caller Id' WHERE `menu_modules`.`id` =33;
delete from system where name IN ('number_retries','callingcards_max_length','calling_cards_cancelled_prompt','calling_cards_menu','calling_cards_connection_prompt','freeswitch_sound_files','log_file','rate_engine_csv_file','csv_dir','auth','externalbill','sleep','timezone');