-- -------22-April-2021
UPDATE `default_templates` SET `template` = '<p>Email Ticket ID: #TICKET_ID# had a new status <strong>#REPLY_TYPE#</strong> posted by #NAME#</p>\r\n\r\n<p>#MESSAGE#</p>\r\n\r\n<p>Feel free to re write us in case if you have any concern regarding this ticket.</p>' WHERE `name`="email_sent_support_ticket";

-- -------24-August-2022
INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL,1, 0, 'services', 'department', 'support_ticket', 'department_list', 'Department', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.02000');

UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules WHERE module_url = 'department/department_list/' AND menu_subtitle = 'Support Ticket') ) WHERE userlevelid = 1;
