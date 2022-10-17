-- RingGroup Query
INSERT INTO `menu_modules` (`menu_label`, `module_name`, `module_url`, `menu_title`,`menu_image`, `menu_subtitle`, `priority`) VALUES
('RingGroup', 'ringgroup', 'ringgroup/ringgroup_list/', 'Inbound','RingGroup.png', '0', 40.4);

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(NULL, 1, 0, 'inbound','ringgroup',' ','ringgroup_list', 'RingGroup', '["main","list","create","edit","delete","search"]', 0, '2019-01-25 09:01:03', '6.60000') ; 

UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules WHERE module_url = 'ringgroup/ringgroup_list/' ) ) WHERE userlevelid = 1;


INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(NULL,0, 0, 'inbound','ringgroup',' ','ringgroup_list', 'RingGroup', '["main","list","create","edit","delete","search"]', 0, '2019-01-25 09:01:03', '6.60000'); 

UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules WHERE module_url = 'ringgroup/ringgroup_list/' ) ) WHERE userlevelid = 2; 

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(NULL,2,0, 'inbound','ringgroup',' ','ringgroup_list', 'RingGroup', '["main","list","create","edit","delete","search"]', 0, '2019-01-25 09:01:03', '2.26000');

INSERT INTO `did_call_types` (`id`, `call_type_code`, `call_type`) VALUES (NULL, '7', 'Ring Group'); 

update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 0;

CREATE TABLE `pbx_ringgroup` (
  `id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `strategy` varchar(50) NOT NULL,
  `destinations` longtext NOT NULL,
  `description` varchar(200) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `no_answer_call_type` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `no_answer_call_type_value` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Active,1:Inactive',
  `creation_date` datetime NOT NULL,
  `last_modified_date` datetime NOT NULL DEFAULT '2022-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `pbx_ringgroup`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pbx_ringgroup`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pbx_ringgroup` CHANGE `destinations` `destinations` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
