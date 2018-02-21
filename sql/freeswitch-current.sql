SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `freeswitch`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl_lists`
--

CREATE TABLE IF NOT EXISTS `acl_lists` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `acl_name` varchar(128) NOT NULL,
  `default_policy` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `acl_lists`
--


-- --------------------------------------------------------

--
-- Table structure for table `acl_nodes`
--

CREATE TABLE IF NOT EXISTS `acl_nodes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cidr` varchar(45) NOT NULL,
  `type` varchar(16) NOT NULL,
  `list_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `acl_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `conference_advertise`
--

CREATE TABLE IF NOT EXISTS `conference_advertise` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `room` varchar(64) NOT NULL,
  `status` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_room` (`room`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `conference_advertise`
--


-- --------------------------------------------------------

--
-- Table structure for table `conference_controls`
--

CREATE TABLE IF NOT EXISTS `conference_controls` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `conf_group` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `digits` varchar(16) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_group_action` USING BTREE (`conf_group`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `conference_controls`
--


-- --------------------------------------------------------

--
-- Table structure for table `conference_profiles`
--

CREATE TABLE IF NOT EXISTS `conference_profiles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `profile_name` varchar(64) NOT NULL,
  `param_name` varchar(64) NOT NULL,
  `param_value` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `unique_profile_param` (`profile_name`,`param_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `conference_profiles`
--


-- --------------------------------------------------------

--
-- Table structure for table `dialplan`
--

CREATE TABLE IF NOT EXISTS `dialplan` (
  `dialplan_id` int(11) NOT NULL auto_increment,
  `domain` varchar(128) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  PRIMARY KEY  (`dialplan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dialplan`
--


-- --------------------------------------------------------

--
-- Table structure for table `dialplan_actions`
--

CREATE TABLE IF NOT EXISTS `dialplan_actions` (
  `action_id` int(11) NOT NULL auto_increment,
  `condition_id` int(11) NOT NULL,
  `application` varchar(256) NOT NULL,
  `data` varchar(256) NOT NULL,
  `type` varchar(32) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY  (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dialplan_actions`
--


-- --------------------------------------------------------

--
-- Table structure for table `dialplan_condition`
--

CREATE TABLE IF NOT EXISTS `dialplan_condition` (
  `condition_id` int(11) NOT NULL auto_increment,
  `extension_id` int(11) NOT NULL,
  `field` varchar(1238) NOT NULL,
  `expression` varchar(128) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY  (`condition_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dialplan_condition`
--


-- --------------------------------------------------------

--
-- Table structure for table `dialplan_context`
--

CREATE TABLE IF NOT EXISTS `dialplan_context` (
  `context_id` int(11) NOT NULL auto_increment,
  `dialplan_id` int(11) NOT NULL,
  `context` varchar(64) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY  (`context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dialplan_context`
--


-- --------------------------------------------------------

--
-- Table structure for table `dialplan_extension`
--

CREATE TABLE IF NOT EXISTS `dialplan_extension` (
  `extension_id` int(11) NOT NULL auto_increment,
  `context_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `continue` varchar(32) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY  (`extension_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dialplan_extension`
--


-- --------------------------------------------------------

--
-- Table structure for table `dialplan_special`
--

CREATE TABLE IF NOT EXISTS `dialplan_special` (
  `id` int(11) NOT NULL auto_increment,
  `context` varchar(255) NOT NULL,
  `class_file` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_context` (`context`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dialplan_special`
--


-- --------------------------------------------------------

--
-- Table structure for table `dingaling_profiles`
--

CREATE TABLE IF NOT EXISTS `dingaling_profiles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `profile_name` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_name` (`profile_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dingaling_profiles`
--


-- --------------------------------------------------------

--
-- Table structure for table `dingaling_profile_params`
--

CREATE TABLE IF NOT EXISTS `dingaling_profile_params` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `dingaling_id` int(10) unsigned NOT NULL,
  `param_name` varchar(64) NOT NULL,
  `param_value` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_type_name` (`dingaling_id`,`param_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dingaling_profile_params`
--


-- --------------------------------------------------------

--
-- Table structure for table `dingaling_settings`
--

CREATE TABLE IF NOT EXISTS `dingaling_settings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `param_name` varchar(64) NOT NULL,
  `param_value` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_param` (`param_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dingaling_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory`
--

CREATE TABLE IF NOT EXISTS `directory` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_domains`
--

CREATE TABLE IF NOT EXISTS `directory_domains` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `domain_name` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory_domains`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_gateways`
--

CREATE TABLE IF NOT EXISTS `directory_gateways` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `directory_id` int(10) unsigned NOT NULL,
  `gateway_name` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory_gateways`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_gateway_params`
--

CREATE TABLE IF NOT EXISTS `directory_gateway_params` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `d_gw_id` int(10) unsigned NOT NULL,
  `param_name` varchar(64) NOT NULL,
  `param_value` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_gw_param` (`d_gw_id`,`param_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory_gateway_params`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_global_params`
--

CREATE TABLE IF NOT EXISTS `directory_global_params` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `param_name` varchar(64) NOT NULL,
  `param_value` varchar(128) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory_global_params`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_global_vars`
--

CREATE TABLE IF NOT EXISTS `directory_global_vars` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `var_name` varchar(64) NOT NULL,
  `var_value` varchar(128) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory_global_vars`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_params`
--

CREATE TABLE IF NOT EXISTS `directory_params` (
  `id` int(11) NOT NULL auto_increment,
  `directory_id` int(11) default NULL,
  `param_name` varchar(255) default NULL,
  `param_value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory_params`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_vars`
--

CREATE TABLE IF NOT EXISTS `directory_vars` (
  `id` int(11) NOT NULL auto_increment,
  `directory_id` int(11) default NULL,
  `var_name` varchar(255) default NULL,
  `var_value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory_vars`
--


-- --------------------------------------------------------

--
-- Table structure for table `iax_conf`
--

CREATE TABLE IF NOT EXISTS `iax_conf` (
  `id` int(11) NOT NULL auto_increment,
  `profile_name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `iax_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `iax_settings`
--

CREATE TABLE IF NOT EXISTS `iax_settings` (
  `id` int(11) NOT NULL auto_increment,
  `iax_id` int(11) default NULL,
  `param_name` varchar(255) default NULL,
  `param_value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `iax_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `ivr_conf`
--

CREATE TABLE IF NOT EXISTS `ivr_conf` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `greet_long` varchar(255) NOT NULL,
  `greet_short` varchar(255) NOT NULL,
  `invalid_sound` varchar(255) NOT NULL,
  `exit_sound` varchar(255) NOT NULL,
  `max_failures` int(10) unsigned NOT NULL default '3',
  `timeout` int(11) NOT NULL default '5',
  `tts_engine` varchar(64) default NULL,
  `tts_voice` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ivr_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `ivr_entries`
--

CREATE TABLE IF NOT EXISTS `ivr_entries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ivr_id` int(10) unsigned NOT NULL,
  `action` varchar(64) NOT NULL,
  `digits` varchar(16) NOT NULL,
  `params` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_ivr_digits` USING BTREE (`ivr_id`,`digits`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ivr_entries`
--


-- --------------------------------------------------------

--
-- Table structure for table `limit_conf`
--

CREATE TABLE IF NOT EXISTS `limit_conf` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `limit_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `limit_data`
--

CREATE TABLE IF NOT EXISTS `limit_data` (
  `hostname` varchar(255) default NULL,
  `realm` varchar(255) default NULL,
  `id` varchar(255) default NULL,
  `uuid` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `limit_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `local_stream_conf`
--

CREATE TABLE IF NOT EXISTS `local_stream_conf` (
  `id` int(11) NOT NULL auto_increment,
  `directory_name` varchar(255) default NULL,
  `directory_path` text,
  `param_name` varchar(255) default NULL,
  `param_value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `local_stream_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `modless_conf`
--

CREATE TABLE IF NOT EXISTS `modless_conf` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `conf_name` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `modless_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_load_modules_conf`
--

CREATE TABLE IF NOT EXISTS `post_load_modules_conf` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `module_name` varchar(64) NOT NULL,
  `load_module` tinyint(1) NOT NULL default '1',
  `priority` int(10) unsigned NOT NULL default '1000',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_mod` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `post_load_modules_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `rss_conf`
--

CREATE TABLE IF NOT EXISTS `rss_conf` (
  `id` int(11) NOT NULL auto_increment,
  `directory_id` int(11) NOT NULL,
  `feed` text NOT NULL,
  `local_file` text NOT NULL,
  `description` text,
  `priority` int(11) NOT NULL default '1000',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `rss_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `sip_authentication`
--

CREATE TABLE IF NOT EXISTS `sip_authentication` (
  `nonce` varchar(255) default NULL,
  `expires` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sip_authentication`
--


-- --------------------------------------------------------

--
-- Table structure for table `sip_dialogs`
--

CREATE TABLE IF NOT EXISTS `sip_dialogs` (
  `call_id` varchar(255) default NULL,
  `uuid` varchar(255) default NULL,
  `sip_to_user` varchar(255) default NULL,
  `sip_to_host` varchar(255) default NULL,
  `sip_from_user` varchar(255) default NULL,
  `sip_from_host` varchar(255) default NULL,
  `contact_user` varchar(255) default NULL,
  `contact_host` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  `direction` varchar(255) default NULL,
  `user_agent` varchar(255) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sip_dialogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `sip_registrations`
--

CREATE TABLE IF NOT EXISTS `sip_registrations` (
  `call_id` varchar(255) default NULL,
  `sip_user` varchar(255) default NULL,
  `sip_host` varchar(255) default NULL,
  `contact` varchar(1024) default NULL,
  `status` varchar(255) default NULL,
  `rpid` varchar(255) default NULL,
  `expires` int(11) default NULL,
  `user_agent` varchar(255) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sip_registrations`
--


-- --------------------------------------------------------

--
-- Table structure for table `sip_subscriptions`
--

CREATE TABLE IF NOT EXISTS `sip_subscriptions` (
  `proto` varchar(255) default NULL,
  `sip_user` varchar(255) default NULL,
  `sip_host` varchar(255) default NULL,
  `sub_to_user` varchar(255) default NULL,
  `sub_to_host` varchar(255) default NULL,
  `event` varchar(255) default NULL,
  `contact` varchar(1024) default NULL,
  `call_id` varchar(255) default NULL,
  `full_from` varchar(255) default NULL,
  `full_via` varchar(255) default NULL,
  `expires` int(11) default NULL,
  `user_agent` varchar(255) default NULL,
  `accept` varchar(255) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sip_subscriptions`
--


-- --------------------------------------------------------

--
-- Table structure for table `sofia_aliases`
--

CREATE TABLE IF NOT EXISTS `sofia_aliases` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sofia_id` int(10) unsigned NOT NULL,
  `alias_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sofia_aliases`
--


-- --------------------------------------------------------

--
-- Table structure for table `sofia_conf`
--

CREATE TABLE IF NOT EXISTS `sofia_conf` (
  `id` int(11) NOT NULL auto_increment,
  `profile_name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sofia_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `sofia_domains`
--

CREATE TABLE IF NOT EXISTS `sofia_domains` (
  `id` int(11) NOT NULL auto_increment,
  `sofia_id` int(11) default NULL,
  `domain_name` varchar(255) default NULL,
  `parse` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sofia_domains`
--


-- --------------------------------------------------------

--
-- Table structure for table `sofia_gateways`
--

CREATE TABLE IF NOT EXISTS `sofia_gateways` (
  `id` int(11) NOT NULL auto_increment,
  `sofia_id` int(11) default NULL,
  `gateway_name` varchar(255) default NULL,
  `gateway_param` varchar(255) default NULL,
  `gateway_value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sofia_gateways`
--


-- --------------------------------------------------------

--
-- Table structure for table `sofia_settings`
--

CREATE TABLE IF NOT EXISTS `sofia_settings` (
  `id` int(11) NOT NULL auto_increment,
  `sofia_id` int(11) default NULL,
  `param_name` varchar(255) default NULL,
  `param_value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sofia_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `voicemail_conf`
--

CREATE TABLE IF NOT EXISTS `voicemail_conf` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vm_profile` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_profile` (`vm_profile`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `voicemail_conf`
--


-- --------------------------------------------------------

--
-- Table structure for table `voicemail_email`
--

CREATE TABLE IF NOT EXISTS `voicemail_email` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `voicemail_id` int(10) unsigned NOT NULL,
  `param_name` varchar(64) NOT NULL,
  `param_value` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_profile_param` (`param_name`,`voicemail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `voicemail_email`
--


-- --------------------------------------------------------

--
-- Table structure for table `voicemail_settings`
--

CREATE TABLE IF NOT EXISTS `voicemail_settings` (
  `id` int(11) NOT NULL auto_increment,
  `voicemail_id` int(11) default NULL,
  `param_name` varchar(255) default NULL,
  `param_value` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `voicemail_settings`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `dingaling_profile_params`
--
ALTER TABLE `dingaling_profile_params`
  ADD CONSTRAINT `dingaling_profile` FOREIGN KEY (`dingaling_id`) REFERENCES `dingaling_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
