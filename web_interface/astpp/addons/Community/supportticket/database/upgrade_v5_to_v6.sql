UPDATE `system` SET `sub_group` = 'Assorted', `group_title` = 'GLOBAL' where name = 'ticket_digits';  

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL,1, 0, 'services', 'department', 'support_ticket', 'department_list', 'Department', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.02000');

UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules WHERE module_url = 'department/department_list/' AND menu_subtitle = 'Support Ticket') ) WHERE userlevelid = 1;
