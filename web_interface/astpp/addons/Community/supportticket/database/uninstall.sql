DELETE FROM `menu_modules` WHERE module_url='supportticket/supportticket_list/';
DROP TABLE IF EXISTS support_ticket_details;
DROP TABLE IF EXISTS support_ticket; 
DELETE FROM `menu_modules` WHERE module_url='department/department_list/';
DROP TABLE IF EXISTS department;
DELETE FROM `system` WHERE name='ticket_digits';
DELETE FROM `roles_and_permission` WHERE `module_name`='supportticket' AND `module_url`='supportticket_list' AND `login_type`=0;
DELETE FROM `roles_and_permission` WHERE `module_name`='department' AND `module_url`='department_list' AND `login_type`=0;
DELETE FROM `roles_and_permission` WHERE `module_name`='supportticket' AND `module_url`='supportticket_list' AND `login_type`=1;
DELETE FROM `translations` WHERE `module_name` = 'supportticket';
DELETE FROM `cron_settings` WHERE `name`='Support Ticket';
DELETE FROM `default_templates` WHERE `name` = 'auto_reply_mail_support';
DELETE FROM `default_templates` WHERE `name` = 'email_sent_support_ticket';

-- -------24-August-2022
DELETE FROM `roles_and_permission` WHERE `module_name`='department' AND `module_url`='department_list' AND `login_type`=1;
