INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Support Ticket', 'supportticket', 'supportticket/supportticket_list/', 'Services', 'ListAccounts.png', 'Support Ticket', 59.1); 
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 2;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 0;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 3;



INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `reseller_id`, `is_display`, `group_title`,`sub_group`) VALUES
(NULL, 'ticket_digits', 'Ticket Digits', '6', 'default_system_input', 'Add Ticket digits', 0, 0, 'GLOBAL','Assorted');

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
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 2;

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
  

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, 0, 0, 'services', 'department', 'support_ticket', 'department_list', 'Department', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.02000'),(NULL, 0, 0,'services', 'supportticket', 'support_ticket', 'supportticket_list', 'Support Tickets', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\",\"close\"]', 0, '2019-01-25 09:01:05', '5.01000');

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, '1', '0', 'services', 'supportticket', 'support_ticket', 'supportticket_list', 'Support Tickets', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\",\"close\"]', '0', '2019-01-25 09:01:05', '5.01000');

INSERT INTO `cron_settings` (`id`, `name`, `command`, `exec_interval`, `creation_date`, `last_modified_date`, `last_execution_date`, `next_execution_date`, `status`, `file_path`) VALUES (NULL, 'Support Ticket', 'minutes', '1', UTC_TIMESTAMP(), UTC_TIMESTAMP(), '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'wget --no-check-certificate -q -O- {BASE_URL}departmentmail/index');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `description`, `sms_template`, `alert_template`, `template`, `last_modified_date`, `reseller_id`, `is_email_enable`, `is_sms_enable`, `is_alert_enable`, `status`) VALUES (NULL, 'auto_reply_mail_support', '[Ticket ID: #TICKET_ID#] #TICKET_SUBJECT#', '', '', '', '<p>Hello #NAME#,</p>\n\n<p>This is an automated response confirming the receipt of your ticket.</p>\n\n<p>A support ticket has now been opened for your request. Our team will get back to you as soon as possible. When replying, please make sure that the ticket ID is kept on the subject so that we can track your replies. You will be notified when a response is made by email. The details of your ticket are shown below.</p>\n\n<p><strong>Ticket ID: </strong>#TICKET_ID#<br />\n<strong>Department: </strong>#DEPARTMENT#<br />\n<strong>Subject: </strong>#TICKET_SUBJECT#<br />\n<strong>Priority: </strong>#PRIORITY#<br />\n<strong>Status: </strong>#REPLY_TYPE#</p>\n\n<p><strong>Message:</strong></p>\n<p>#MESSAGE#</p>\n\n<p>Feel free to re write us in case if you have any concern regarding this ticket.</p>\n\n<p>Sincerely,</p>\n<p>#COMPANY_NAME#</p>\n<p>Support Team</p>', '2019-07-11 14:00:42', 0, 0, 0, 0, 0),(NULL, 'email_sent_support_ticket', '[Ticket ID: #TICKET_ID#] #TICKET_SUBJECT#', '', '', '', '<p>Email Ticket ID: #TICKET_ID# had a new status <strong>#REPLY_TYPE#</strong> posted by #NAME#</p>\n\n<p>#MESSAGE#</p>\n\n<p>Feel free to re write us in case if you have any concern regarding this ticket.</p>', '2019-08-14 12:04:17', 0, 0, 0, 0, 0);

-- -- -------22-April-2021
-- UPDATE `default_templates` SET `template` = '<p>Email Ticket ID: #TICKET_ID# had a new status <strong>#REPLY_TYPE#</strong> posted by #NAME#</p>\r\n\r\n<p>#MESSAGE#</p>\r\n\r\n<p>Feel free to re write us in case if you have any concern regarding this ticket.</p>' WHERE `name`="email_sent_support_ticket";

