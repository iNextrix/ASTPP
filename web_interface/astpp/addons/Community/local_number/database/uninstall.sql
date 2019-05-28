DROP TABLE IF EXISTS local_number;
DROP TABLE IF EXISTS local_number_destination;
delete from `menu_modules` where `module_url`='local_number/local_number_list/' and `module_name`='local_number';
delete from `menu_modules` where `module_url`='local_number/local_number_list_customer/' and `module_name`='local_number';


delete from `roles_and_permission` where `module_name`='local_number' AND `module_url`='local_number_list' AND `login_type`=0;
delete from `roles_and_permission` where `module_name`='local_number' AND `module_url`='local_number_list' AND `login_type`=1;
