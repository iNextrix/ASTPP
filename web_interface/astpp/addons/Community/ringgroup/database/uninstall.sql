delete from `did_call_types` where call_type_code >= 7 and call_type_code <= 13;

update `dids` set extensions ='',call_type=0 where call_type >= 7 and call_type <= 13;

delete from `system` where name = 'recording_number';

UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('ringgroup/ringgroup_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('ringgroup/ringgroup_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 0;

UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('ringgroup/ringgroup_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('ringgroup/ringgroup_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 2;

delete from `menu_modules` where `menu_label`='RingGroup' and `module_name`='ringgroup';
delete from `roles_and_permission` where `module_name`='ringgroup' AND `module_url`='ringgroup_list' AND `login_type`=0;
delete from `roles_and_permission` where `module_name`='ringgroup' AND `module_url`='ringgroup_list' AND `login_type`=1;
delete from `roles_and_permission` where `module_name`='ringgroup' AND `module_url`='ringgroup_list' AND `login_type`=2;
	
DROP TABLE IF EXISTS `pbx_ringgroup`;