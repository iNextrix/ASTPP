CREATE TABLE `local_number` (
  `id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `number` varchar(30) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0:active,1:inactive',
  `created_date` datetime NOT NULL,
  `last_modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `local_number`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `local_number`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `local_number_destination` (
  `id` int(11) NOT NULL,
  `local_number_id` int(11) NOT NULL DEFAULT '0',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `destination_name` varchar(50) DEFAULT NULL,
  `destination_number` varchar(50) DEFAULT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `local_number_destination`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `local_number_destination`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


INSERT INTO `menu_modules` (`id`,`menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(NULL,'Local Numbers', 'local_number', 'local_number/local_number_list/', 'Inbound', '', '0', 30.5);
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;

INSERT INTO `menu_modules` (`id`,`menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(NULL,'Local Numbers', 'local_number', 'local_number/local_number_list_customer/', 'Inbound', '', '0', 2.2);
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 0;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 3;

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(NULL, 0, 0, 'inbound','local_number','local_number','local_number_list', 'Local Number', '["main","list","search","create","export","import","delete"]', 0, '2019-01-25 09:01:03', '3.30000');

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(NULL,1, 0, 'inbound','local_number','','local_number_list', 'Local Number', '["main","list","search","create","export","import","delete"]', 0, '2019-01-25 09:01:03', '3.30000');
