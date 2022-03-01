-- sandip add roles and permission in admin 2-4-20

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, '1', '0', 'switch', 'switch_monitoring', '', 'sip_devices', 'Registered SIP Devices', '[\"main\",\"list\"]', '0', '2019-01-25 09:01:05', '6');

-- sandip add roles and permission in admin 2-4-20
-- INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`)
-- VALUES (NULL, 'Registered SIP Devices', 'sip', 'user/user_registred_sip_devices/', 'Live Calls', '', '0', 6.3);
-- UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` where `module_url` = "user/user_registred_sip_devices/" ) ) WHERE `userlevelid` = 0;

-- INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
-- (NULL,2,0,'live_calls','user',' ','user_registred_sip_devices', 'Registered SIP Devices', '["main","list"]', 0, '2019-01-25 09:01:03', '9.00000');

-- -------------------06-Feb-2021
UPDATE `system` SET `display_name` = 'Auto Refresh Page' WHERE `name` = 'refresh_second';

UPDATE `system` SET `sub_group` = 'Assorted' WHERE `name` = 'refresh_second';