#
# Table structure for table `sip_buddies`
#

CREATE TABLE `sip_buddies` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(80) NOT NULL default '',
 `accountcode` varchar(20) default NULL,
 `amaflags` varchar(13) default NULL,
 `callgroup` varchar(10) default NULL,
 `callerid` varchar(80) default NULL,
 `canreinvite` char(3) default 'yes',
 `context` varchar(80) default NULL,
 `defaultip` varchar(15) default NULL,
 `dtmfmode` varchar(7) default NULL,
 `fromuser` varchar(80) default NULL,
 `fromdomain` varchar(80) default NULL,
 `fullcontact` varchar(80) default NULL,
 `host` varchar(31) NOT NULL default '',
 `insecure` varchar(4) default NULL,
 `language` char(2) default NULL,
 `mailbox` varchar(50) default NULL,
 `md5secret` varchar(80) default NULL,
 `nat` varchar(5) NOT NULL default 'no',
 `deny` varchar(95) default NULL,
 `permit` varchar(95) default NULL,
 `mask` varchar(95) default NULL,
 `pickupgroup` varchar(10) default NULL,
 `port` varchar(5) NOT NULL default '',
 `qualify` char(3) default NULL,
 `restrictcid` char(1) default NULL,
 `rtptimeout` char(3) default NULL,
 `rtpholdtimeout` char(3) default NULL,
 `secret` varchar(80) default NULL,
 `type` varchar(6) NOT NULL default 'friend',
 `username` varchar(80) NOT NULL default '',
 `disallow` varchar(100) default 'all',
 `allow` varchar(100) default 'g729;ilbc;gsm;ulaw;alaw',
 `musiconhold` varchar(100) default NULL,
 `regseconds` int(11) NOT NULL default '0',
 `ipaddr` varchar(15) NOT NULL default '',
 `regexten` varchar(80) NOT NULL default '',
 `cancallforward` char(3) default 'yes',
 `setvar` varchar(100) NOT NULL default '',
 PRIMARY KEY  (`id`),
 UNIQUE KEY `name` (`name`),
 KEY `name_2` (`name`)
) TYPE=MyISAM ROW_FORMAT=DYNAMIC; 

#
# Table structure for table `iax_buddies`
#

CREATE TABLE iax_buddies (
       name varchar(30) primary key NOT NULL,
       username varchar(30), 
       type varchar(6) NOT NULL, 
       secret varchar(50), 
       md5secret varchar(32), 
       dbsecret varchar(100), 
       notransfer varchar(10), 
       inkeys varchar(100),
       outkey varchar(100),
       auth varchar(100), 
       accountcode varchar(100), 
       amaflags varchar(100), 
       callerid varchar(100), 
       context varchar(100), 
       defaultip varchar(15), 
       host varchar(31) NOT NULL default 'dynamic', 
       language char(5), 
       mailbox varchar(50), 
       deny varchar(95),
       permit varchar(95),  
       qualify varchar(4), 
       disallow varchar(100), 
       allow varchar(100), 
       ipaddr varchar(15), 
       port integer default 0,
       regseconds integer default 0
);
CREATE UNIQUE INDEX iax_buddies_username_idx ON iax_buddies(username); 

#
# Table structure for table `voicemail_users`
#

CREATE TABLE `voicemail_users` (
 `uniqueid` int(11) NOT NULL auto_increment,
 `customer_id` varchar(11) NOT NULL default '0',
 `context` varchar(50) NOT NULL default '',
 `mailbox` varchar(11) NOT NULL default '0',
 `password` varchar(5) NOT NULL default '0',
 `fullname` varchar(150) NOT NULL default '',
 `email` varchar(50) NOT NULL default '',
 `pager` varchar(50) NOT NULL default '',
 `tz` varchar(10) NOT NULL default 'central',
 `attach` varchar(4) NOT NULL default 'yes',
 `saycid` varchar(4) NOT NULL default 'yes',
 `dialout` varchar(10) NOT NULL default '',
 `callback` varchar(10) NOT NULL default '',
 `review` varchar(4) NOT NULL default 'no',
 `operator` varchar(4) NOT NULL default 'no',
 `envelope` varchar(4) NOT NULL default 'no',
 `sayduration` varchar(4) NOT NULL default 'no',
 `saydurationm` tinyint(4) NOT NULL default '1',
 `sendvoicemail` varchar(4) NOT NULL default 'no',
 `delete` varchar(4) NOT NULL default 'no',
 `nextaftercmd` varchar(4) NOT NULL default 'yes',
 `forcename` varchar(4) NOT NULL default 'no',
 `forcegreetings` varchar(4) NOT NULL default 'no',
 `hidefromdir` varchar(4) NOT NULL default 'yes',
 `stamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 PRIMARY KEY  (`uniqueid`),
 KEY `mailbox_context` (`mailbox`,`context`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

CREATE TABLE queue_table (
 name VARCHAR(128) PRIMARY KEY,
 musiconhold VARCHAR(128),
 announce VARCHAR(128),
 context VARCHAR(128),
 timeout INT(11),
 monitor_join BOOL,
 monitor_format VARCHAR(128),
 queue_youarenext VARCHAR(128),
 queue_thereare VARCHAR(128),
 queue_callswaiting VARCHAR(128),
 queue_holdtime VARCHAR(128),
 queue_minutes VARCHAR(128),
 queue_seconds VARCHAR(128),
 queue_lessthan VARCHAR(128),
 queue_thankyou VARCHAR(128),
 queue_reporthold VARCHAR(128),
 announce_frequency INT(11),
 announce_round_seconds INT(11),
 announce_holdtime VARCHAR(128),
 retry INT(11),
 wrapuptime INT(11),
 maxlen INT(11),
 servicelevel INT(11),
 strategy VARCHAR(128),
 joinempty VARCHAR(128),
 leavewhenempty VARCHAR(128),
 eventmemberstatus BOOL,
 eventwhencalled BOOL,
 reportholdtime BOOL,
 memberdelay INT(11),
 weight INT(11),
 timeoutrestart BOOL
);

#
# Table structure for table `extensions_table`
#

CREATE TABLE `extensions_table` (
 `id` int(11) NOT NULL auto_increment,
 `context` varchar(20) NOT NULL default '',
 `exten` varchar(20) NOT NULL default '',
 `priority` tinyint(4) NOT NULL default '0',
 `app` varchar(20) NOT NULL default '',
 `appdata` varchar(128) NOT NULL default '',
 PRIMARY KEY  (`context`,`exten`,`priority`),
 KEY `id` (`id`)
) TYPE=MyISAM; 
