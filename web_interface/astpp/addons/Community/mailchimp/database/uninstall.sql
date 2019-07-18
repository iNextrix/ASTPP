DELETE FROM `system`  WHERE `name` = 'mailchimp_api_key';
DELETE FROM `system`  WHERE `name` = 'mailchimp_audience_id';

DELETE FROM `cron_settings` WHERE `name`='Mailchimp';
DELETE FROM `translations` WHERE `module_name` = 'mailchimp';
