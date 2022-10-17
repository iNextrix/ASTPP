UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('custom_rates/custom_rates_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('custom_rates/custom_rates_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 2;
delete from `menu_modules` where `module_name`='custom' AND `module_url`='custom_rates/custom_rates_list/';

delete from `roles_and_permission` where `module_name`='custom_rates' AND `module_url`='custom_rates_list' AND `login_type`=0;

delete from `roles_and_permission` where `module_name`='custom_rates' AND `module_url`='custom_rates_list' AND `login_type`=1;

-- ------2021-05-27
-- ------Nirali issue 3898 start
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Personalized Rates', 'custom_rates', 'personalized_rates/personalized_rates_list/', 'Tariff' ,'personalizedrates.png', '0', '40.8');
-- ------Nirali issue 3898 END

update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;


INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, '0', '0', 'tariff', 'personalized_rates', '0', 'personalized_rates_list', 'Personalized Rates', '[\"main\",\"list\",\"search\",\"create\",\"edit\",\"delete\",\"export\"]', '0', '2019-01-25 09:01:03', '4.21');


INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, '1', '0', 'tariff', 'personalized_rates', '0', 'personalized_rates_list', 'Personalized Rates', '[\"main\",\"list\",\"search\",\"create\",\"edit\",\"delete\",\"export\"]', '0', '2019-01-25 09:01:03', '4.21000');

-- --sandip change for roles and permission
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules where module_url ='personalized_rates/personalized_rates_list/' ) ) WHERE userlevelid = 2;

