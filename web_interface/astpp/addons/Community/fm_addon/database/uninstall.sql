UPDATE userlevels AS ul  CROSS JOIN (SELECT CONCAT(",",GROUP_CONCAT(id)) AS id FROM menu_modules WHERE module_url IN ('fsmonitor/sip_devices/')) AS mm SET ul.module_permissions = REPLACE(ul.module_permissions, mm.id, '')  WHERE ul.userlevelid = -1;
UPDATE userlevels AS ul  CROSS JOIN (SELECT CONCAT(",",GROUP_CONCAT(id)) AS id FROM menu_modules WHERE module_url IN ('fsmonitor/sip_devices/')) AS mm SET ul.module_permissions = REPLACE(ul.module_permissions, mm.id, '')  WHERE ul.userlevelid = 1;
delete from `menu_modules` where `menu_label`='Registered SIP Devices' and `module_name`='sip';

UPDATE userlevels AS ul  CROSS JOIN (SELECT CONCAT(",",GROUP_CONCAT(id)) AS id FROM menu_modules WHERE module_url IN ('fsmonitor/gateways/')) AS mm SET ul.module_permissions = REPLACE(ul.module_permissions, mm.id, '')  WHERE ul.userlevelid = -1;
delete from `menu_modules` where `menu_label`='Registered Gateways' and `module_name`='gateways';

UPDATE userlevels AS ul  CROSS JOIN (SELECT CONCAT(",",GROUP_CONCAT(id)) AS id FROM menu_modules WHERE module_url IN ('fsmonitor/fs_cli/')) AS mm SET ul.module_permissions = REPLACE(ul.module_permissions, mm.id, '')  WHERE ul.userlevelid = -1;
delete from `menu_modules` where `menu_label`='Switch CLI' and `module_name`='fs';

UPDATE userlevels AS ul  CROSS JOIN (SELECT CONCAT(",",GROUP_CONCAT(id)) AS id FROM menu_modules WHERE module_url IN ('fsmonitor/live_call_graph/')) AS mm SET ul.module_permissions = REPLACE(ul.module_permissions, mm.id, '')  WHERE ul.userlevelid = -1;
delete from `menu_modules` where `menu_label`='Live Call Graph' and `module_name`='live';

delete from `system` where `name` = 'refresh_second';
