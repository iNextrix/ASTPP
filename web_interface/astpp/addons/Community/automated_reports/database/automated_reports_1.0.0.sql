
DROP TABLE IF EXISTS `automated_reports`;

CREATE TABLE `automated_reports` ( 
  `id` int(11) AUTO_INCREMENT primary key NOT NULL, 
  `report_name` varchar(40) NOT NULL , 
  `account_email` varchar(80) NOT NULL , 
  `report_interval_days` tinyint(1) NOT NULL,
  `report_interval_recurring` tinyint(1) NOT NULL,
  `interval_frequency_on` tinyint(1) NOT NULL,
  `filters_where` text NOT NULL,
  `select_names` text NOT NULL,
  `module` varchar(20) NOT NULL,
  `select_values` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL,
  `week_day` text NOT NULL,
  `automated_report_value` tinyint(1) NOT NULL,
  `next_execution_date` date NOT NULL DEFAULT '0000-00-00',
  `update_flag` tinyint(1) NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`,`menu_image`, `menu_subtitle`, `priority`) VALUES(NULL, 'Automated Reports', 'automated_report', 'automated_report/automated_report_list/', 'Reports','', '0', 89.7);

UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules WHERE module_url = 'automated_report/automated_report_list/' ) ) WHERE userlevelid = -1;

INSERT INTO `cron_settings` (`name`, `command`, `exec_interval`, `creation_date`, `last_modified_date`, `last_execution_date`, `next_execution_date`, `status`, `file_path`) VALUES ( 'Automated Report', 'days', '1', UTC_TIMESTAMP(), UTC_TIMESTAMP(),'0000-00-00 00:00:00','0000-00-00 00:00:00', '0', 'wget --no-check-certificate -q -O- {BASE_URL}automatedReport/automated_Report');

INSERT INTO `default_templates` ( `name`, `subject`, `description`, `sms_template`, `alert_template`, `template`, `last_modified_date`, `reseller_id`, `is_email_enable`, `is_sms_enable`, `is_alert_enable`, `status`) VALUES ( 'automated_report', 'Automated Report: #Report Name# - #Interval Freq. of Email#', '', '', '', '<p>Hi, </p>\n\n<p>Here is the report information: </p>\n\n<p> Report Interval : Last #Integer value# #Interval#</p>\n\n<p>Interval Filter On: #Interval Filter On#</p>\n\n<p>Please find an attached report.</p>\n\n', '1000-01-01 00:00:00.000000', '0', '0', '0', '0', '0');
