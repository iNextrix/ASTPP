INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Support Ticket', 'supportticket', 'supportticket/supportticket_list/', 'Services', 'ListAccounts.png', 'Support Ticket', 59.1); 
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;


update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 0;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 3;


INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `reseller_id`, `is_display`, `group_title`) VALUES
(NULL, 'ticket_digits', 'Ticket Digits', '6', 'default_system_input', 'Add Ticket digits', 0, 0, 'global');
 
DROP TABLE IF EXISTS support_ticket_details;
CREATE TABLE `support_ticket_details` (
  `id` int(11) NOT NULL,
  `support_ticket_id` int(11) NOT NULL,
  `generate_account_id` int(11) NOT NULL,
  `message` longtext DEFAULT NULL,
  `attachment` varchar(150) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `last_modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


ALTER TABLE `support_ticket_details`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `support_ticket_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;
  
DROP TABLE IF EXISTS support_ticket;  
CREATE TABLE `support_ticket` (
  `id` int(11) NOT NULL,
  `support_ticket_number` varchar(20) NOT NULL DEFAULT '0',
  `ticket_type` varchar(150) NOT NULL DEFAULT '0',
  `priority` varchar(150) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `subject` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `close_ticket_display_flag` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


ALTER TABLE `support_ticket`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `support_ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  
 INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Department', 'department', 'department/department_list/', 'Services', 'pricelist.png', 'Support Ticket', 59.3);
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
 
 DROP TABLE IF EXISTS department;  
 CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `admin_id_list` varchar(100) NOT NULL,
  `sub_admin_id_list` varchar(100) NOT NULL,
  `additional_email_address` varchar(200) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` varchar(100) NOT NULL,
  `smtp_user` varchar(100) NOT NULL,
  `smtp_password` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `reseller_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
  
 
 
ALTER TABLE `mail_details` ADD `cc` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `reseller_id`;   

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, 0, 0, 'services', 'department', 'department', 'department_list', 'Department', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.02000'),(NULL, 0, 0,'services', 'supportticket', 'supportticket', 'supportticket_list', 'Support Tickets', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\",\"close\"]', 0, '2019-01-25 09:01:05', '5.01000');


  
  


 
       
  
  


