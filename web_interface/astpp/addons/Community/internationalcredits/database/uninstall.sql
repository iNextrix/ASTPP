UPDATE userlevels AS ul  CROSS JOIN (SELECT CONCAT(",",GROUP_CONCAT(id)) AS id FROM menu_modules WHERE module_url IN ('internationalcredits/internationalcredits_list/')) AS mm SET ul.module_permissions = REPLACE(ul.module_permissions, mm.id, '')  WHERE ul.userlevelid = -1;
UPDATE userlevels AS ul  CROSS JOIN (SELECT CONCAT(",",GROUP_CONCAT(id)) AS id FROM menu_modules WHERE module_url IN ('internationalcredits/internationalcredits_list/')) AS mm SET ul.module_permissions = REPLACE(ul.module_permissions, mm.id, '')  WHERE ul.userlevelid = 1;
delete from `roles_and_permission` where `module_name`='internationalcredits' AND `module_url`='internationalcredits_list' AND `login_type`=1;
delete from `menu_modules` where `menu_label`='International Credits' and `module_name`='internationalcredits';
delete from `system` where group_title='InternationalPrefixes';
alter table accounts drop column int_balance;
alter table accounts drop column int_credit_limit;
delete from `cron_settings` where name='Low International Balance Alert';
