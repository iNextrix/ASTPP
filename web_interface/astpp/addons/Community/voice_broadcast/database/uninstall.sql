DROP TABLE IF EXISTS `voice_broadcast`;

delete from `menu_modules` where `module_url`='voice_broadcast/voice_broadcast_list/' and `module_name`='voice_broadcast';

delete from `roles_and_permission` where `module_name`='voice_broadcast' AND `module_url`='voice_broadcast_list' AND `login_type`=0;

delete from `cron_settings` where name='Voice Broadcast';

delete from `system` where `name`= 'voice_broadcast_host';

delete from `system` where `name`= 'voice_broadcast_port';