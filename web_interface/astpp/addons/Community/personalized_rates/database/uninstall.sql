
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('personalized_rates/personalized_rates_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('custom_rates/personalized_rates_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 2;
delete from `menu_modules` where `module_name`='custom_rates' AND `module_url`='personalized_rates/personalized_rates_list/';

delete from `roles_and_permission` where `module_name`='personalized_rates' AND `module_url`='personalized_rates_list' AND `login_type`=0;

delete from `roles_and_permission` where `module_name`='personalized_rates' AND `module_url`='personalized_rates_list' AND `login_type`=1;

delete from routes where pricelist_id = '0';
DELETE FROM `translations` WHERE `module_name` = 'custom_rates';
