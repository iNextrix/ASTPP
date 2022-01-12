UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/sip_devices/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/sip_devices/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/sip_devices/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 2;
delete from `menu_modules` where `menu_label`='Registered SIP Devices' and `module_name`='sip';

UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/gateways/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/gateways/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 2;
delete from `menu_modules` where `menu_label`='Registered Gateways' and `module_name`='gateways';

UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/fs_cli/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/fs_cli/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 2;
delete from `menu_modules` where `menu_label`='Switch CLI' and `module_name`='fs';

UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/live_call_graph/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;
UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('switch_monitoring/live_call_graph/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 2;
delete from `menu_modules` where `menu_label`='Live Call Graph' and `module_name`='live';

delete from `system` where `name` = 'refresh_second';
DELETE FROM `translations` WHERE `module_name` = 'fm_addon';

delete from `roles_and_permission` where `module_name`='switch_monitoring' AND `module_url`='live_call_graph' AND `login_type`=0;
delete from `roles_and_permission` where `module_name`='switch_monitoring' AND `module_url`='fs_cli' AND `login_type`=0;
delete from `roles_and_permission` where `module_name`='switch_monitoring' AND `module_url`='gateways' AND `login_type`=0;
delete from `roles_and_permission` where `module_name`='switch_monitoring' AND `module_url`='sip_devices' AND `login_type`=0;
delete from `roles_and_permission` where `module_name`='switch_monitoring' AND `module_url`='sip_devices' AND `login_type`=1;
delete from `roles_and_permission` where `module_name`='user' AND `module_url`='user_registred_sip_devices' AND `login_type`=2;

UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('user/user_registred_sip_devices/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = 0;
delete from `menu_modules` where `menu_label`='Live Call Graph' and `module_name`='sip';
delete from `menu_modules` where `menu_label`='Registered SIP Devices' and `module_name`='user';