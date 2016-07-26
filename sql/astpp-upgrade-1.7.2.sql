ALTER TABLE `accounts` ADD `deleted` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '1=deleted'; 

ALTER TABLE `fscdr`.`fscdr` ADD INDEX ( `uniqueid` );

DELETE FROM `system` WHERE `system`.`id` = 28;
DELETE FROM `system` WHERE `system`.`id` = 58;
DELETE FROM `system` WHERE `system`.`id` = 52;
DELETE FROM `system` WHERE `system`.`id` = 37;

UPDATE `system` SET `name` = 'service_length' WHERE `system`.`id` =77;

ALTER TABLE `system` ADD `group_title` VARCHAR( 15 ) NOT NULL;

UPDATE `system` SET group_title='global' WHERE id=1;
UPDATE `system` SET group_title='asterisk' WHERE id=2;
UPDATE `system` SET group_title='asterisk' WHERE id=3;
UPDATE `system` SET group_title='asterisk' WHERE id=4;
UPDATE `system` SET group_title='asterisk' WHERE id=5;
UPDATE `system` SET group_title='asterisk' WHERE id=6;
UPDATE `system` SET group_title='asterisk' WHERE id=7;
UPDATE `system` SET group_title='asterisk' WHERE id=8;
UPDATE `system` SET group_title='asterisk' WHERE id=9;
UPDATE `system` SET group_title='asterisk' WHERE id=10;
UPDATE `system` SET group_title='opensips' WHERE id=11;
UPDATE `system` SET group_title='opensips' WHERE id=12;
UPDATE `system` SET group_title='opensips' WHERE id=13;
UPDATE `system` SET group_title='opensips' WHERE id=14;
UPDATE `system` SET group_title='opensips' WHERE id=15;
UPDATE `system` SET group_title='opensips' WHERE id=16;
UPDATE `system` SET group_title='opensips' WHERE id=17;
UPDATE `system` SET group_title='global' WHERE id=18;
UPDATE `system` SET group_title='asterisk' WHERE id=19;
UPDATE `system` SET group_title='global' WHERE id=20;
UPDATE `system` SET group_title='global' WHERE id=21;
UPDATE `system` SET group_title='global' WHERE id=22;
UPDATE `system` SET group_title='global' WHERE id=23;
UPDATE `system` SET group_title='global' WHERE id=24;
UPDATE `system` SET group_title='global' WHERE id=25;
UPDATE `system` SET group_title='callingcard' WHERE id=26;
UPDATE `system` SET group_title='asterisk' WHERE id=27;
UPDATE `system` SET group_title='asterisk' WHERE id=29;
UPDATE `system` SET group_title='global' WHERE id=30;
UPDATE `system` SET group_title='global' WHERE id=31;
UPDATE `system` SET group_title='global' WHERE id=32;
UPDATE `system` SET group_title='global' WHERE id=33;
UPDATE `system` SET group_title='global' WHERE id=34;
UPDATE `system` SET group_title='callingcard' WHERE id=35;
UPDATE `system` SET group_title='asterisk' WHERE id=36;
UPDATE `system` SET group_title='asterisk' WHERE id=38;
UPDATE `system` SET group_title='asterisk' WHERE id=39;
UPDATE `system` SET group_title='global' WHERE id=40;
UPDATE `system` SET group_title='asterisk' WHERE id=41;
UPDATE `system` SET group_title='callingcard' WHERE id=42;
UPDATE `system` SET group_title='global' WHERE id=43;
UPDATE `system` SET group_title='global' WHERE id=44;
UPDATE `system` SET group_title='global' WHERE id=45;
UPDATE `system` SET group_title='global' WHERE id=46;
UPDATE `system` SET group_title='global' WHERE id=47;
UPDATE `system` SET group_title='global' WHERE id=48;
UPDATE `system` SET group_title='callingcard' WHERE id=53;
UPDATE `system` SET group_title='callingcard' WHERE id=54;
UPDATE `system` SET group_title='callingcard' WHERE id=55;
UPDATE `system` SET group_title='global' WHERE id=56;
UPDATE `system` SET group_title='callingcard' WHERE id=57;
UPDATE `system` SET group_title='global' WHERE id=60;
UPDATE `system` SET group_title='global' WHERE id=61;
UPDATE `system` SET group_title='asterisk' WHERE id=62;
UPDATE `system` SET group_title='cdr' WHERE id=63;
UPDATE `system` SET group_title='osc' WHERE id=64;
UPDATE `system` SET group_title='agile' WHERE id=65;
UPDATE `system` SET group_title='freepbx' WHERE id=66;
UPDATE `system` SET group_title='global' WHERE id=67;
UPDATE `system` SET group_title='callingcard' WHERE id=68;
UPDATE `system` SET group_title='cdr' WHERE id=69;
UPDATE `system` SET group_title='global' WHERE id=70;
UPDATE `system` SET group_title='global' WHERE id=71;
UPDATE `system` SET group_title='freepbx' WHERE id=72;
UPDATE `system` SET group_title='asterisk' WHERE id=73;
UPDATE `system` SET group_title='freeswitch' WHERE id=74;
UPDATE `system` SET group_title='global' WHERE id=75;
UPDATE `system` SET group_title='global' WHERE id=76;
UPDATE `system` SET group_title='global' WHERE id=77;
UPDATE `system` SET group_title='global' WHERE id=78;
UPDATE `system` SET group_title='asterisk' WHERE id=79;
UPDATE `system` SET group_title='agile' WHERE id=80;
UPDATE `system` SET group_title='agile' WHERE id=81;
UPDATE `system` SET group_title='agile' WHERE id=82;
UPDATE `system` SET group_title='agile' WHERE id=83;
UPDATE `system` SET group_title='agile' WHERE id=84;
UPDATE `system` SET group_title='agile' WHERE id=85;
UPDATE `system` SET group_title='agile' WHERE id=86;
UPDATE `system` SET group_title='agile' WHERE id=87;
UPDATE `system` SET group_title='agile' WHERE id=88;
UPDATE `system` SET group_title='osc' WHERE id=89;
UPDATE `system` SET group_title='osc' WHERE id=90;
UPDATE `system` SET group_title='osc' WHERE id=91;
UPDATE `system` SET group_title='osc' WHERE id=92;
UPDATE `system` SET group_title='osc' WHERE id=93;
UPDATE `system` SET group_title='osc' WHERE id=94;
UPDATE `system` SET group_title='osc' WHERE id=95;
UPDATE `system` SET group_title='osc' WHERE id=96;
UPDATE `system` SET group_title='freepbx' WHERE id=97;
UPDATE `system` SET group_title='freepbx' WHERE id=98;
UPDATE `system` SET group_title='freepbx' WHERE id=99;
UPDATE `system` SET group_title='freepbx' WHERE id=100;
UPDATE `system` SET group_title='freepbx' WHERE id=101;
UPDATE `system` SET group_title='freepbx' WHERE id=102;
UPDATE `system` SET group_title='freepbx' WHERE id=103;
UPDATE `system` SET group_title='freepbx' WHERE id=104;
UPDATE `system` SET group_title='freepbx' WHERE id=105;
UPDATE `system` SET group_title='freepbx' WHERE id=106;
UPDATE `system` SET group_title='freepbx' WHERE id=107;
UPDATE `system` SET group_title='freepbx' WHERE id=108;
UPDATE `system` SET group_title='freepbx' WHERE id=109;
UPDATE `system` SET group_title='freepbx' WHERE id=110;
UPDATE `system` SET group_title='freepbx' WHERE id=111;
UPDATE `system` SET group_title='freepbx' WHERE id=112;
UPDATE `system` SET group_title='freepbx' WHERE id=113;
UPDATE `system` SET group_title='freepbx' WHERE id=114;
UPDATE `system` SET group_title='freepbx' WHERE id=115;
UPDATE `system` SET group_title='freepbx' WHERE id=116;
UPDATE `system` SET group_title='asterisk' WHERE id=117;
UPDATE `system` SET group_title='asterisk' WHERE id=118;
UPDATE `system` SET group_title='asterisk' WHERE id=119;
UPDATE `system` SET group_title='asterisk' WHERE id=120;
UPDATE `system` SET group_title='asterisk' WHERE id=121;
UPDATE `system` SET group_title='asterisk' WHERE id=122;
UPDATE `system` SET group_title='asterisk' WHERE id=123;
UPDATE `system` SET group_title='asterisk' WHERE id=124;
UPDATE `system` SET group_title='asterisk' WHERE id=125;
UPDATE `system` SET group_title='asterisk' WHERE id=126;
UPDATE `system` SET group_title='asterisk' WHERE id=127;
UPDATE `system` SET group_title='asterisk' WHERE id=128;
UPDATE `system` SET group_title='asterisk' WHERE id=129;
UPDATE `system` SET group_title='asterisk' WHERE id=130;
UPDATE `system` SET group_title='asterisk' WHERE id=131;
UPDATE `system` SET group_title='asterisk' WHERE id=132;
UPDATE `system` SET group_title='asterisk' WHERE id=133;
UPDATE `system` SET group_title='asterisk' WHERE id=134;
UPDATE `system` SET group_title='callingcard' WHERE id=135;
UPDATE `system` SET group_title='callingcard' WHERE id=136;
UPDATE `system` SET group_title='callingcard' WHERE id=137;
UPDATE `system` SET group_title='callingcard' WHERE id=138;
UPDATE `system` SET group_title='callingcard' WHERE id=139;
UPDATE `system` SET group_title='callingcard' WHERE id=140;
UPDATE `system` SET group_title='callingcard' WHERE id=141;
UPDATE `system` SET group_title='callingcard' WHERE id=142;
UPDATE `system` SET group_title='callingcard' WHERE id=143;
UPDATE `system` SET group_title='callingcard' WHERE id=144;
UPDATE `system` SET group_title='asterisk' WHERE id=145;
UPDATE `system` SET group_title='asterisk' WHERE id=146;
UPDATE `system` SET group_title='callingcard' WHERE id=147;
UPDATE `system` SET group_title='callingcard' WHERE id=148;
UPDATE `system` SET group_title='global' WHERE id=149;
UPDATE `system` SET group_title='freeswitch' WHERE id=150;
UPDATE `system` SET group_title='freeswitch' WHERE id=151;
UPDATE `system` SET group_title='freeswitch' WHERE id=152;
UPDATE `system` SET group_title='freeswitch' WHERE id=153;
UPDATE `system` SET group_title='freeswitch' WHERE id=154;
UPDATE `system` SET group_title='freeswitch' WHERE id=155;
UPDATE `system` SET group_title='freeswitch' WHERE id=156;
UPDATE `system` SET group_title='freeswitch' WHERE id=157;
UPDATE `system` SET group_title='freeswitch' WHERE id=158;
UPDATE `system` SET group_title='freeswitch' WHERE id=159;
UPDATE `system` SET group_title='freeswitch' WHERE id=160;
UPDATE `system` SET group_title='freeswitch' WHERE id=161;
UPDATE `system` SET group_title='freeswitch' WHERE id=162;
UPDATE `system` SET group_title='freeswitch' WHERE id=163;
UPDATE `system` SET group_title='cdr' WHERE id=164;
UPDATE `system` SET group_title='cdr' WHERE id=165;
UPDATE `system` SET group_title='cdr' WHERE id=166;
UPDATE `system` SET group_title='cdr' WHERE id=167;
UPDATE `system` SET group_title='asterisk' WHERE id=168;
UPDATE `system` SET group_title='asterisk' WHERE id=169;
UPDATE `system` SET group_title='asterisk' WHERE id=170;
UPDATE `system` SET group_title='global' WHERE id=171;
UPDATE `system` SET group_title='global' WHERE id=172;
UPDATE `system` SET group_title='callingcard' WHERE id=173;
UPDATE `system` SET group_title='global' WHERE id=174;
UPDATE `system` SET group_title='callingcard' WHERE id=178;

DROP TABLE `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `payment_mode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 for system payment',
  `type` int(11) NOT NULL,
  `payment_by` int(11) NOT NULL COMMENT 'accountid by recharge done',
  `notes` text,
  `reference` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `block_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` varchar(15) NOT NULL,
  `blocked_patterns` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `package_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `patterns` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE `callingcard_stats`, `extensions_status`, `extension_list`, `manager_action_variables`, `pbx_list`, `queue_list`;

DROP VIEW `reseller_cdrs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reseller_cdrs` AS select `a`.`accountid` AS `accountid`,`a`.`cc` AS `cc`,`a`.`number` AS `number`,`a`.`reseller` AS `reseller`,`b`.`id` AS `cdr_id`,`b`.`uniqueid` AS `uniqueid`,`b`.`callerid` AS `callerid`,`b`.`callednum` AS `callednum`,`b`.`billseconds` AS `billseconds`,`b`.`trunk` AS `trunk`,`b`.`disposition` AS `disposition`,`b`.`callstart` AS `callstart`,`b`.`debit` AS `debit`,`b`.`credit` AS `credit`,`b`.`status` AS `status`,`b`.`notes` AS `notes`,`b`.`provider` AS `provider`,`b`.`cost` AS `cost`,`b`.`pricelist` AS `pricelist`,`b`.`pattern` AS `pattern`,`b`.`calltype` AS `calltype` from (`accounts` `a` join `cdrs` `b`) where ((`a`.`number` = `b`.`cardnum`) and (`a`.`type` = 1) and (`b`.`uniqueid` <> ''));