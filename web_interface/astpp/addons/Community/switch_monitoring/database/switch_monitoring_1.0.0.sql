INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`)
VALUES (NULL, 'Registered SIP Devices', 'sip', 'switch_monitoring/sip_devices/', 'Switch', 'Live-Report.png', '0', 100.6);
UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` ) ) WHERE `userlevelid` = -1;
UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` ) ) WHERE `userlevelid` = 1;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(NULL, 'Registered Gateways', 'gateways', 'switch_monitoring/gateways/', 'Switch', 'TrunkStats.png', '0', 100.7);
UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` ) ) WHERE `userlevelid` = -1;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(NULL, 'Switch CLI', 'fs', 'switch_monitoring/fs_cli/', 'Switch', 'Devices.png', '0', 100.8);
UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` ) ) WHERE `userlevelid` = -1;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(NULL, 'Live Call Graph', 'live', 'switch_monitoring/live_call_graph/', 'Switch', 'Devices.png', '0', 100.9);
UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` ) ) WHERE `userlevelid` = -1;

INSERT INTO `system` (`name`, `display_name` ,`value`, `comment`, `timestamp`, `reseller_id`, `group_title`,`sub_group`) VALUES ('refresh_second', 'Auto Refresh Page' , '60', 'Auto refresh page', '2015-06-15 00:00:00', 0, 'GLOBAL','Assorted');

