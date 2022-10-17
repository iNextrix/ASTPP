DROP TABLE IF EXISTS `dialer_device_info`;

DELETE FROM `system` where name IN('ios_push_notification_passphrase','api_auth_key','api_url','google_fcm_key','api_debug_log','ios_push_notification_mode','ios_push_notification_passphrase','static_domain','mobile_notification','apns_topic','voip_topic','apns_pem','voip_pem');

