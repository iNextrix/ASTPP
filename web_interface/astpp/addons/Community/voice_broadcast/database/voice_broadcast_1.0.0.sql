CREATE TABLE `voice_broadcast` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `sip_device_id` int(11) NOT NULL DEFAULT '0',
  `destination_number` longtext NOT NULL,
  `broadcast` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0:active,1:inactive',
  `created_date` datetime NOT NULL,
  `last_modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `voice_broadcast`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `voice_broadcast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



INSERT INTO `menu_modules` (`id`,`menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(NULL,'Voice Broadcast', 'voice_broadcast', 'voice_broadcast/voice_broadcast_list/', 'Services', '', '0', 30.5);
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 2;

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES (NULL, 0, 0, 'services','voice_broadcast','voice_broadcast','voice_broadcast_list', 'Voice Broadcast', '["main","list","search","create","delete","download_sample_file"]', 0, UTC_TIMESTAMP(), '3.30000');

INSERT INTO `cron_settings` (`id`, `name`, `command`, `exec_interval`, `creation_date`, `last_modified_date`, `last_execution_date`,`next_execution_date`, `status`, `file_path`) VALUES (NULL, 'Voice Broadcast', 'minutes', '5', UTC_TIMESTAMP(), UTC_TIMESTAMP(),'0000-00-00 00:00:00','0000-00-00 00:00:00', '0', 'wget --no-check-certificate -O - -q {BASE_URL}Voice_broadcast/voice_broadcast/');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`,`sub_group`,`field_rules`) VALUES (NULL, 'voice_broadcast_host', 'Voice Broadcast Host', '127.0.0.1', 'default_system_input', 'Set your IP here.', 'NOW()', '0', '0', 'voice_broadcast', 'Voice Broadcast', '');

INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`,`sub_group`,`field_rules`) VALUES (NULL, 'voice_broadcast_port', 'Voice Broadcast Port', '5060', 'default_system_input', 'Set your Port here.', 'NOW()', '0', '0', 'voice_broadcast', 'Voice Broadcast', '');