DROP TABLE IF EXISTS `automated_reports`;

UPDATE userlevels ul CROSS JOIN( SELECT GROUP_CONCAT(id) id FROM menu_modules WHERE module_url IN('automated_report/automated_report_list/')) mm SET ul.module_permissions =( CASE WHEN mm.id IS NOT NULL THEN REPLACE (ul.module_permissions, CONCAT(",",mm.id ), '') ELSE ul.module_permissions END ) WHERE ul.userlevelid = -1;

delete from `menu_modules` where `module_name`='automated_report' AND `module_url`='automated_report/automated_report_list/';

delete from `cron_settings` where `name` = 'Automated Report';

delete from `default_templates` where `name`='automated_report';