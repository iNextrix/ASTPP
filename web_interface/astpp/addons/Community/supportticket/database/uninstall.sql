
delete from `menu_modules` where module_url='supportticket/supportticket_list/';
DROP TABLE IF EXISTS support_ticket_details;
DROP TABLE IF EXISTS support_ticket; 

delete from `menu_modules` where module_url='department/department_list/';
DROP TABLE IF EXISTS department;

alter table `mail_details` drop column cc;

delete from `system` where name='ticket_digits';

delete from `roles_and_permission` where `module_name`='supportticket' AND `module_url`='supportticket_list' AND `login_type`=0;
delete from `roles_and_permission` where `module_name`='department' AND `module_url`='department_list' AND `login_type`=0;
