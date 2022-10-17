
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`,`menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Custom Rates', 'custom', 'custom_rates/custom_rates_list/', 'Tariff', 'personalizedrates.png', '0', '40.8');

update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;


INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, '0', '0', 'tariff', 'custom_rates', '0', 'custom_rates_list', 'Custom Rates', '[\"main\",\"list\",\"search\",\"create\",\"edit\",\"delete\",\"export\"]', '0', '2019-01-25 09:01:03', '4.21');


INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, '1', '0', 'tariff', 'custom_rates', '0', 'custom_rates_list', 'Custom Rates', '[\"main\",\"list\",\"search\",\"create\",\"edit\",\"delete\",\"export\"]', '0', '2019-01-25 09:01:03', '4.21000');

