INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'International Credits', 'internationalcredits', 'internationalcredits/internationalcredits_list/', 'Accounts', '', '0', '10.7');

update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(NULL, 1, 0, 'accounts', 'internationalcredits', '', 'internationalcredits_list', 'International Recharge', '[\"main\",\"list\",\"recharge\",\"delete\",\"search\"]', 0, '2019-01-25 09:01:05', '1.60000');

ALTER TABLE `accounts` ADD `int_balance` DECIMAL(20,5) NOT NULL  DEFAULT '0.00000',
		       ADD `int_credit_limit`  DECIMAL(20,5) NOT NULL DEFAULT '0.0000';

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`) VALUES (NULL, 'international_prefixes', 'International Prefixes', 'NULL', 'default_system_input', 'Define international prefixes with comma  separated. System will use international balance and credit limit for  calls. ', '0000-00-00 00:00:00', '0', '0', 'InternationalPrefixes');

INSERT INTO `cron_settings` (`id`, `name`, `command`, `exec_interval`, `creation_date`, `last_modified_date`, `last_execution_date`, `next_execution_date`, `status`, `file_path`) VALUES 
(NULL, 'Low International Balance Alert', 'hours', '1', UTC_TIMESTAMP(), UTC_TIMESTAMP(),'0000-00-00 00:00:00','0000-00-00 00:00:00', '0', 'wget -O - -q {BASE_URL}lowintbalance/low_balance');


