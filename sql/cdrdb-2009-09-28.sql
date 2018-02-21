--
-- Table structure for table `cdr`
--

DROP TABLE IF EXISTS `cdr`;
CREATE TABLE `cdr` (
  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
  `clid` varchar(80) NOT NULL default '',
  `src` varchar(80) NOT NULL default '',
  `dst` varchar(80) NOT NULL default '',
  `dcontext` varchar(80) NOT NULL default '',
  `channel` varchar(80) NOT NULL default '',
  `dstchannel` varchar(80) NOT NULL default '',
  `lastapp` varchar(80) NOT NULL default '',
  `lastdata` varchar(80) NOT NULL default '',
  `duration` int(11) NOT NULL default '0',
  `billsec` int(11) NOT NULL default '0',
  `disposition` varchar(45) NOT NULL default '',
  `amaflags` int(11) NOT NULL default '0',
  `accountcode` varchar(20) NOT NULL default '',
  `uniqueid` varchar(32) NOT NULL default '',
  `userfield` varchar(255) NOT NULL default '',
  `cost` varchar(20) NOT NULL default 'none',
  `vendor` varchar(20) NOT NULL default 'none',
  KEY `calldate` (`calldate`),
  KEY `dst` (`dst`),
  KEY `accountcode` (`accountcode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `fscdr`
--

DROP TABLE IF EXISTS `fscdr`;
CREATE TABLE IF NOT EXISTS `fscdr` (
  `accountcode` varchar(20) NOT NULL default '',
  `src` varchar(80) NOT NULL default '',
  `dst` varchar(80) NOT NULL default '',
  `dcontext` varchar(80) NOT NULL default '',
  `clid` varchar(80) NOT NULL default '',
  `channel` varchar(80) NOT NULL default '',
  `dstchannel` varchar(80) NOT NULL default '',
  `lastapp` varchar(80) NOT NULL default '',
  `lastdata` varchar(80) NOT NULL default '',
  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
  `answerdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `enddate` datetime NOT NULL default '0000-00-00 00:00:00',
  `duration` int(11) NOT NULL default '0',
  `billsec` int(11) NOT NULL default '0',
  `disposition` varchar(45) NOT NULL default '',
  `amaflags` int(11) NOT NULL default '0',
  `uniqueid` varchar(60) NOT NULL,
  `userfield` varchar(255) NOT NULL default '',
  `read_codec` varchar(60) NOT NULL default '',
  `write_codec` varchar(60) NOT NULL default '',
  `cost` varchar(20) NOT NULL default 'none',
  `vendor` varchar(20) NOT NULL default 'none',
  `provider` varchar(60) NOT NULL,
  `trunk` varchar(60) NOT NULL,
  `outbound_route` varchar(60) NOT NULL,
  `progressmsec` varchar(20) NOT NULL,
  `answermsec` varchar(20) NOT NULL,
  `progress_mediamsec` varchar(20) NOT NULL,
  KEY `calldate` (`calldate`),
  KEY `dst` (`dst`),
  KEY `accountcode` (`accountcode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

