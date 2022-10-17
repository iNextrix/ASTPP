  CREATE TABLE `dialer_device_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `accountid` int(11) NOT NULL,
    `username` varchar(20) NOT NULL,
    `last_login_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES
  (NULL, 'ios_push_notification_passphrase', 'IOS Push Notification Passphrase', '0', 'default_system_input', 'Set IOS Push notification passphrase', '1000-01-01 00:00:00', 0, 0, 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', ''),
  (NULL, 'api_auth_key', 'API Auth Key (For IOS Push)', '0', 'default_system_input', 'Define API Auth Key', '1000-01-01 00:00:00', 0, 0, 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', ''),
  (NULL, 'api_url', 'API URL (For IOS Push)', '0', 'default_system_input', 'Define API URL', '1000-01-01 00:00:00', 0, 0, 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', ''),
  (NULL, 'api_debug_log', 'API Debug Mode', '0', 'enable_disable_option', 'To enable api log', '1000-01-01 00:00:00', 0, 0, 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', '');

  INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES
  (NULL, 'google_fcm_key', 'Google API Key', '0', 'default_system_input', 'Set API key from the google firebase console', '2019-04-01 00:00:00.000000', '0', '0', 'notifications', 'Alert Notifications <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', ''),
  (NULL, 'ios_push_notification_mode', 'IOS Push Notification Mode', '0', 'ios_notification_mode', 'Set IOS Push notification mode for incoming calls. Enable for Live and Disable for Sandbox', '1000-01-01 00:00:00', 0, 0, 'notifications', 'Alert Notifications <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', ''),
  (NULL, 'ios_push_notification_passphrase', 'IOS Push Notification Passphrase', '0', 'default_system_input', 'Set IOS Push notification passphrase', '1000-01-01 00:00:00', 0, 0, 'notifications', 'Alert Notifications <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', '');

  INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES (NULL, 'static_domain', 'Sip Domain', '0', 'default_system_input', 'Define SIP domain with port. EG. sip.yourdomain.com:5060', '1000-01-01 00:00:00', '0', '0', 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', '') ;

  INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES(NULL, 'mobile_notification','Mobile App', '1', 'notification_mode', 'Set Notification', '0000-00-00 00:00:00', 0, 0, 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', '');

  INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES (NULL, 'apns_topic', 'APNS Topic', '0', 'default_system_input', 'Define APNS topic name(Define value if you have personalised dialer and want to send notification from our central notification server.). EG. com.inextrix.astpp.iap.voip', '2021-09-03 00:00:00', '0', '0', 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', '');

  INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES (NULL, 'voip_topic', 'VOIP Topic', '0', 'default_system_input', 'Define VOIP topic name(Define value if you have personalised dialer and want to send notification from our central notification server.). EG. com.inextrix.astpp.iap.voip', '2021-09-03 00:00:00', '0', '0', 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', '') ;

  INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`, `field_rules`) VALUES (NULL, 'apns_pem', 'APNS Pem File', '0', 'default_system_input','Set your APNS pem file', '2022-01-21 00:00:00', '0', '0', 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', ''),
  (NULL, 'voip_pem', 'VOIP Pem File', '0', 'default_system_input','Set your VOIP pem file', '2022-01-21 00:00:00', '0', '0', 'global', 'Dialer Configuration <span id = "Enterprise" class="badge badge-warning Enterprise"> Enterprise</span>', '');
