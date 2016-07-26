SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `astpp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `accountid` int(11) NOT NULL AUTO_INCREMENT,
  `cc` char(20) NOT NULL DEFAULT '',
  `number` char(50) NOT NULL,
  `reseller` char(40) NOT NULL DEFAULT '',
  `pricelist` char(24) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sweep` int(11) NOT NULL DEFAULT '0',
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pin` int(11) NOT NULL DEFAULT '0',
  `credit_limit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `posttoexternal` int(11) NOT NULL DEFAULT '0',
  `balance` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `password` char(80) NOT NULL DEFAULT '',
  `first_name` char(40) NOT NULL DEFAULT '',
  `middle_name` char(40) NOT NULL DEFAULT '',
  `last_name` char(40) NOT NULL DEFAULT '',
  `company_name` char(40) NOT NULL DEFAULT '',
  `address_1` char(80) NOT NULL DEFAULT '',
  `address_2` char(80) NOT NULL DEFAULT '',
  `address_3` char(80) NOT NULL DEFAULT '',
  `postal_code` char(12) NOT NULL DEFAULT '',
  `province` char(40) NOT NULL DEFAULT '',
  `city` char(80) NOT NULL DEFAULT '',
  `country` char(40) NOT NULL DEFAULT '',
  `telephone_1` char(40) NOT NULL DEFAULT '',
  `telephone_2` char(40) NOT NULL DEFAULT '',
  `fascimile` char(40) NOT NULL DEFAULT '',
  `email` char(80) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT '',
  `currency` char(3) NOT NULL DEFAULT '',
  `maxchannels` int(11) NOT NULL DEFAULT '1',
  `routing_technique` int(4) NOT NULL DEFAULT '0',
  `dialed_modify` text NOT NULL,
  `type` int(11) DEFAULT '0',
  `tz` char(40) NOT NULL DEFAULT '',
  `inuse` int(11) NOT NULL,
  PRIMARY KEY (`accountid`),
  KEY `number` (`number`),
  KEY `pricelist` (`pricelist`),
  KEY `reseller` (`reseller`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `accounts_callerid`
--
DROP TABLE IF EXISTS `accounts_callerid`;
CREATE TABLE IF NOT EXISTS `accounts_callerid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `callerid_name` varchar(30) NOT NULL,
  `callerid_number` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 inactive 1 active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `user` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ani_map`
--
DROP TABLE IF EXISTS `ani_map`;
CREATE TABLE IF NOT EXISTS `ani_map` (
  `number` char(20) NOT NULL,
  `account` char(50) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `context` varchar(20) NOT NULL,
  PRIMARY KEY (`number`),
  KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `callingcardbrands`
--
DROP TABLE IF EXISTS `callingcardbrands`;
CREATE TABLE IF NOT EXISTS `callingcardbrands` (
  `name` char(40) NOT NULL,
  `reseller` char(40) NOT NULL DEFAULT '',
  `language` char(10) NOT NULL DEFAULT '',
  `pricelist` char(40) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `validfordays` char(4) NOT NULL DEFAULT '',
  `pin` int(11) NOT NULL DEFAULT '0',
  `maint_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `maint_fee_days` int(11) NOT NULL DEFAULT '0',
  `disconnect_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `minute_fee_minutes` int(11) NOT NULL DEFAULT '0',
  `minute_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `min_length_minutes` int(11) NOT NULL DEFAULT '0',
  `min_length_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`name`),
  KEY `reseller` (`reseller`),
  KEY `pricelist` (`pricelist`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `callingcardcdrs`
--
DROP TABLE IF EXISTS `callingcardcdrs`;
CREATE TABLE IF NOT EXISTS `callingcardcdrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cardnumber` char(50) NOT NULL DEFAULT '',
  `clid` char(80) NOT NULL DEFAULT '',
  `destination` char(40) NOT NULL DEFAULT '',
  `disposition` char(20) NOT NULL DEFAULT '',
  `callstart` char(40) NOT NULL DEFAULT '',
  `seconds` int(11) NOT NULL DEFAULT '0',
  `debit` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `credit` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `status` int(11) NOT NULL DEFAULT '0',
  `uniqueid` varchar(60) NOT NULL DEFAULT '',
  `notes` char(80) NOT NULL DEFAULT '',
  `pricelist` char(80) NOT NULL DEFAULT '',
  `pattern` char(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `cardnumber` (`cardnumber`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `callingcards`
--
DROP TABLE IF EXISTS `callingcards`;
CREATE TABLE IF NOT EXISTS `callingcards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cardnumber` char(20) NOT NULL DEFAULT '',
  `language` char(10) NOT NULL DEFAULT '',
  `value` double(10,5) NOT NULL DEFAULT '0.00000',
  `used` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `brand` varchar(20) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `firstused` datetime DEFAULT NULL,
  `expiry` datetime DEFAULT NULL,
  `validfordays` char(4) NOT NULL DEFAULT '',
  `inuse` int(11) NOT NULL DEFAULT '0',
  `pin` char(20) DEFAULT NULL,
  `account` varchar(50) NOT NULL DEFAULT '',
  `maint_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `maint_fee_days` int(11) NOT NULL DEFAULT '0',
  `maint_day` int(11) NOT NULL DEFAULT '0',
  `disconnect_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `minute_fee_minutes` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `minute_fee_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `min_length_minutes` int(11) NOT NULL DEFAULT '0',
  `min_length_pennies` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `timeused` int(11) NOT NULL DEFAULT '0',
  `invoice` char(20) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `brand` (`brand`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `callingcards_callerid`
--
DROP TABLE IF EXISTS `callingcards_callerid`;
CREATE TABLE IF NOT EXISTS `callingcards_callerid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cardnumber` char(20) NOT NULL,
  `callerid_name` varchar(30) NOT NULL,
  `callerid_number` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 inactive 1 active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `callingcard_stats`
--
DROP TABLE IF EXISTS `callingcard_stats`;
CREATE TABLE IF NOT EXISTS `callingcard_stats` (
  `uniqueid` varchar(60) NOT NULL,
  `total_time` varchar(48) NOT NULL,
  `billable_time` varchar(48) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`uniqueid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `callshops`
--
DROP TABLE IF EXISTS `callshops`;
CREATE TABLE IF NOT EXISTS `callshops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `osc_dbname` varchar(50) NOT NULL DEFAULT '',
  `osc_dbpass` varchar(50) NOT NULL DEFAULT '',
  `osc_dbuser` varchar(50) NOT NULL DEFAULT '',
  `osc_dbhost` varchar(50) NOT NULL DEFAULT '',
  `osc_site` varchar(50) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdrs`
--
DROP TABLE IF EXISTS `cdrs`;
CREATE TABLE IF NOT EXISTS `cdrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(60) NOT NULL DEFAULT '',
  `cardnum` char(50) DEFAULT NULL,
  `callerid` char(80) DEFAULT NULL,
  `callednum` varchar(80) NOT NULL DEFAULT '',
  `billseconds` int(11) NOT NULL DEFAULT '0',
  `trunk` varchar(30) DEFAULT NULL,
  `disposition` varchar(45) NOT NULL DEFAULT '',
  `callstart` varchar(80) NOT NULL DEFAULT '',
  `debit` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `credit` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `status` int(11) NOT NULL DEFAULT '0',
  `notes` char(80) DEFAULT NULL,
  `provider` char(50) DEFAULT NULL,
  `cost` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `pricelist` char(80) NOT NULL DEFAULT '',
  `pattern` char(80) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `invoiceid` int(11) NOT NULL DEFAULT '0',
  `calltype` enum('STANDARD','DID') NOT NULL,
  PRIMARY KEY (`id`),  
  KEY `cardnum` (`cardnum`),
  KEY `provider` (`provider`),
  KEY `trunk` (`trunk`),
  KEY `uniqueid` (`uniqueid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
SET FOREIGN_KEY_CHECKS=1;

-- --------------------------------------------------------

--
-- Table structure for table `charges`
--
DROP TABLE IF EXISTS `charges`;
CREATE TABLE IF NOT EXISTS `charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pricelist` char(40) NOT NULL DEFAULT '',
  `description` varchar(80) NOT NULL DEFAULT '',
  `charge` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sweep` int(11) NOT NULL DEFAULT '0',
  `reseller` char(40) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pricelist` (`pricelist`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `charge_to_account`
--
DROP TABLE IF EXISTS `charge_to_account`;
CREATE TABLE IF NOT EXISTS `charge_to_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charge_id` int(11) NOT NULL DEFAULT '0',
  `cardnum` char(50) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--
DROP TABLE IF EXISTS `counters`;
CREATE TABLE IF NOT EXISTS `counters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package` char(40) NOT NULL DEFAULT '',
  `account` varchar(50) NOT NULL,
  `seconds` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `countrycode`
--
DROP TABLE IF EXISTS `countrycode`;
CREATE TABLE IF NOT EXISTS `countrycode` (
  `country` varchar(255) NOT NULL,
  PRIMARY KEY (`country`),
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `countrycode` (`country`) VALUES
('Afghanistan'),
('Alaska'),
('Albania'),
('Algeria'),
('AmericanSamoa'),
('Andorra'),
('Angola'),
('Antarctica'),
('Argentina'),
('Armenia'),
('Aruba'),
('Ascension'),
('Australia'),
('Austria'),
('Azerbaijan'),
('Bahrain'),
('Bangladesh'),
('Belarus'),
('Belgium'),
('Belize'),
('Benin'),
('Bhutan'),
('Bolivia'),
('Bosnia & Herzegovina'),
('Botswana'),
('Brazil'),
('Brunei Darussalam'),
('Bulgaria'),
('Burkina Faso'),
('Burundi'),
('Cambodia'),
('Cameroon'),
('Canadda'),
('Cape Verde Islands'),
('Central African Republic'),
('Chad'),
('Chile'),
('China'),
('Colombia'),
('Comoros'),
('Congo'),
('Cook Islands'),
('Costa Rica'),
('Croatia'),
('Cuba'),
('Cuba Guantanamo Bay'),
('Cyprus'),
('Czech Republic'),
('Denmark'),
('Diego Garcia'),
('Djibouti'),
('Dominican Republic'),
('East Timor'),
('Ecuador'),
('Egypt'),
('El Salvador'),
('Equatorial Guinea'),
('Eritrea'),
('Estonia'),
('Ethiopia'),
('Faroe Islands'),
('Fiji Islands'),
('Finland'),
('France'),
('French Guiana'),
('French Polynesia'),
('Gabonese Republic'),
('Gambia'),
('Georgia'),
('Germany'),
('Ghana'),
('Gibraltar'),
('Greece'),
('Greenland'),
('Guadeloupe'),
('Guam'),
('Guatemala'),
('Guinea'),
('Guyana'),
('Haiti'),
('Honduras'),
('Hong Kong'),
('Hungary'),
('Iceland'),
('India'),
('Indonesia'),
('Iran'),
('Iraq'),
('Ireland'),
('Israel'),
('Italy'),
('Jamaica'),
('Japan'),
('Jordan'),
('Kazakstan'),
('Kenya'),
('Kiribati'),
('Kuwait'),
('Kyrgyz Republic'),
('Laos'),
('Latvia'),
('Lebanon'),
('Lesotho'),
('Liberia'),
('Libya'),
('Liechtenstein'),
('Lithuania'),
('Luxembourg'),
('Macao'),
('Madagascar'),
('Malawi'),
('Malaysia'),
('Maldives'),
('Mali Republic'),
('Malta'),
('Marshall Islands'),
('Martinique'),
('Mauritania'),
('Mauritius'),
('MayotteIsland'),
('Mexico'),
('Midway Islands'),
('Moldova'),
('Monaco'),
('Mongolia'),
('Morocco'),
('Mozambique'),
('Myanmar'),
('Namibia'),
('Nauru'),
('Nepal'),
('Netherlands'),
('Netherlands Antilles'),
('New Caledonia'),
('New Zealand'),
('Nicaragua'),
('Niger'),
('Nigeria'),
('Niue'),
('Norfolk Island'),
('North Korea'),
('Norway'),
('Oman'),
('Pakistan'),
('Palau'),
('Palestinian Settlements'),
('Panama'),
('PapuaNew Guinea'),
('Paraguay'),
('Peru'),
('Philippines'),
('Poland'),
('Portugal'),
('Puerto Rico'),
('Qatar'),
('RÃ©unionIsland'),
('Romania'),
('Russia'),
('Rwandese Republic'),
('San Marino'),
('Saudi Arabia'),
('SÃ£o TomÃ© and Principe'),
('Senegal '),
('Serbia and Montenegro'),
('Seychelles Republic'),
('Sierra Leone'),
('Singapore'),
('Slovak Republic'),
('Slovenia'),
('Solomon Islands'),
('Somali Democratic Republic'),
('South Africa'),
('South Korea'),
('Spain'),
('Sri Lanka'),
('St Kitts - Nevis'),
('St. Helena'),
('St. Lucia'),
('St. Pierre & Miquelon'),
('St. Vincent & Grenadines'),
('Sudan'),
('Suriname'),
('Swaziland'),
('Sweden'),
('Switzerland'),
('Syria'),
('Taiwan'),
('Tajikistan'),
('Tanzania'),
('Thailand'),
('Togolese Republic'),
('Tokelau'),
('Tonga Islands'),
('Trinidad & Tobago'),
('Tunisia'),
('Turkey'),
('Turkmenistan'),
('Tuvalu'),
('Uganda'),
('Ukraine'),
('United Arab Emirates'),
('United Kingdom'),
('United States of America'),
('Uruguay'),
('Uzbekistan'),
('Vanuatu'),
('Venezuela'),
('Vietnam'),
('Wake Island'),
('Wallisand Futuna Islands'),
('Western Samoa'),
('Yemen'),
('Zambia'),
('Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--
DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `Currency` varchar(3) NOT NULL DEFAULT '',
  `CurrencyName` varchar(40) NOT NULL DEFAULT '',
  `CurrencyRate` decimal(10,5) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Currency`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT IGNORE INTO `currency` (`Currency`, `CurrencyName`, `CurrencyRate`) VALUES
('ALL', 'Albanian Lek', 111.55000),
('DZD', 'Algerian Dinar', 75.96000),
('XAL', 'Aluminium Ounces', 0.00000),
('ARS', 'Argentine Peso', 4.46800),
('AWG', 'Aruba Florin', 1.78990),
('AUD', 'Australian Dollar', 1.02520),
('BSD', 'Bahamian Dollar', 1.00000),
('BHD', 'Bahraini Dinar', 0.37690),
('BDT', 'Bangladesh Taka', 81.95000),
('BBD', 'Barbados Dollar', 2.00000),
('BYR', 'Belarus Ruble', 8255.00000),
('BZD', 'Belize Dollar', 1.91350),
('BMD', 'Bermuda Dollar', 1.00000),
('BTN', 'Bhutan Ngultrum', 55.40500),
('BOB', 'Bolivian Boliviano', 6.91000),
('BRL', 'Brazilian Real', 1.98790),
('GBP', 'British Pound', 0.63840),
('BND', 'Brunei Dollar', 1.28160),
('BGN', 'Bulgarian Lev', 1.55000),
('BIF', 'Burundi Franc', 1409.00000),
('KHR', 'Cambodia Riel', 4050.00000),
('CAD', 'Canadian Dollar', 1.02940),
('KYD', 'Cayman Islands Dollar', 0.00000),
('XOF', 'CFA Franc (BCEAO)', 523.30000),
('XAF', 'CFA Franc (BEAC)', 524.09470),
('CLP', 'Chilean Peso', 510.75000),
('CNY', 'Chinese Yuan', 6.34540),
('COP', 'Colombian Peso', 1844.50000),
('KMF', 'Comoros Franc', 393.07110),
('XCP', 'Copper Ounces', 0.29000),
('CRC', 'Costa Rica Colon', 505.00000),
('HRK', 'Croatian Kuna', 6.06080),
('CUP', 'Cuban Peso', 1.00000),
('CYP', 'Cyprus Pound', 0.00000),
('CZK', 'Czech Koruna', 20.27000),
('DKK', 'Danish Krone', 5.93730),
('DJF', 'Dijibouti Franc', 180.95000),
('DOP', 'Dominican Peso', 39.00000),
('XCD', 'East Caribbean Dollar', 2.70000),
('ECS', 'Ecuador Sucre', 0.00000),
('EGP', 'Egyptian Pound', 6.03950),
('SVC', 'El Salvador Colon', 8.74750),
('ERN', 'Eritrea Nakfa', 0.00000),
('EEK', 'Estonian Kroon', 0.00000),
('ETB', 'Ethiopian Birr', 17.67370),
('EUR', 'Euro', 0.79900),
('FKP', 'Falkland Islands Pound', 0.63860),
('GMD', 'Gambian Dalasi', 30.69500),
('GHC', 'Ghanian Cedi', 0.00000),
('GIP', 'Gibraltar Pound', 0.63850),
('XAU', 'Gold Ounces', 0.00060),
('GTQ', 'Guatemala Quetzal', 7.78650),
('GNF', 'Guinea Franc', 7037.50000),
('HTG', 'Haiti Gourde', 41.97000),
('HNL', 'Honduras Lempira', 19.05500),
('HKD', 'Hong Kong Dollar', 7.76300),
('HUF', 'Hungarian ForINT', 239.30000),
('ISK', 'Iceland Krona', 129.23000),
('INR', 'Indian Rupee', 55.37000),
('IDR', 'Indonesian Rupiah', 9285.00000),
('IRR', 'Iran Rial', 12265.00000),
('ILS', 'Israeli Shekel', 3.84720),
('JMD', 'Jamaican Dollar', 87.25000),
('JPY', 'Japanese Yen', 79.68000),
('JOD', 'Jordanian Dinar', 0.71000),
('KZT', 'Kazakhstan Tenge', 147.90500),
('KES', 'Kenyan Shilling', 85.20000),
('KRW', 'Korean Won', 1185.50000),
('KWD', 'Kuwaiti Dinar', 0.28020),
('LAK', 'Lao Kip', 7992.50000),
('LVL', 'Latvian Lat', 0.55700),
('LBP', 'Lebanese Pound', 1503.50000),
('LSL', 'Lesotho Loti', 8.45500),
('LYD', 'Libyan Dinar', 1.27500),
('LTL', 'Lithuanian Lita', 2.75500),
('MOP', 'Macau Pataca', 7.99640),
('MKD', 'Macedonian Denar', 48.90000),
('MGF', 'Malagasy Franc', 0.00000),
('MWK', 'Malawi Kwacha', 251.70000),
('MYR', 'Malaysian Ringgit', 3.15300),
('MVR', 'Maldives Rufiyaa', 15.37000),
('MTL', 'Maltese Lira', 0.00000),
('MRO', 'Mauritania Ougulya', 292.25000),
('MUR', 'Mauritius Rupee', 29.90000),
('MXN', 'Mexican Peso', 14.02150),
('MDL', 'Moldovan Leu', 11.98500),
('MNT', 'Mongolian Tugrik', 1317.50000),
('MAD', 'Moroccan Dirham', 8.78250),
('MZM', 'Mozambique Metical', 0.00000),
('NAD', 'Namibian Dollar', 8.35900),
('NPR', 'Nepalese Rupee', 89.58000),
('ANG', 'Neth Antilles Guilder', 1.79000),
('TRY', 'New Turkish Lira', 1.85060),
('NZD', 'New Zealand Dollar', 1.32600),
('NIO', 'Nicaragua Cordoba', 23.33500),
('NGN', 'Nigerian Naira', 159.60000),
('NOK', 'Norwegian Krone', 6.01700),
('OMR', 'Omani Rial', 0.38500),
('XPF', 'Pacific Franc', 95.25000),
('PKR', 'Pakistani Rupee', 91.80000),
('XPD', 'Palladium Ounces', 0.00170),
('PAB', 'Panama Balboa', 1.00000),
('PGK', 'Papua New Guinea Kina', 2.04290),
('PYG', 'Paraguayan Guarani', 4385.00000),
('PEN', 'Peruvian Nuevo Sol', 2.70100),
('PHP', 'Philippine Peso', 43.79000),
('XPT', 'Platinum Ounces', 0.00070),
('PLN', 'Polish Zloty', 3.48600),
('QAR', 'Qatar Rial', 3.64120),
('ROL', 'Romanian Leu', 0.00000),
('RON', 'Romanian New Leu', 3.57350),
('RUB', 'Russian Rouble', 31.99000),
('RWF', 'Rwanda Franc', 609.18410),
('WST', 'Samoa Tala', 2.39840),
('STD', 'Sao Tome Dobra', 19505.00000),
('SAR', 'Saudi Arabian Riyal', 3.75030),
('SCR', 'Seychelles Rupee', 14.24070),
('SLL', 'Sierra Leone Leone', 4360.00000),
('XAG', 'Silver Ounces', 0.03520),
('SGD', 'Singapore Dollar', 1.28000),
('SKK', 'Slovak Koruna', 0.00000),
('SIT', 'Slovenian Tolar', 0.00000),
('SOS', 'Somali Shilling', 1625.00000),
('ZAR', 'South African Rand', 8.36500),
('LKR', 'Sri Lanka Rupee', 131.00000),
('SHP', 'St Helena Pound', 0.63850),
('SDD', 'Sudanese Dinar', 0.00000),
('SRG', 'Surinam Guilder', 0.00000),
('SZL', 'Swaziland Lilageni', 8.42000),
('SEK', 'Swedish Krona', 7.17820),
('CHF', 'Swiss Franc', 0.95950),
('SYP', 'Syrian Pound', 63.78000),
('TWD', 'Taiwan Dollar', 29.62300),
('TZS', 'Tanzanian Shilling', 1583.50000),
('THB', 'Thai Baht', 31.66000),
('TOP', 'Tonga Paanga', 1.79710),
('TTD', 'Trinidad&Tobago Dollar', 6.40340),
('TND', 'Tunisian Dinar', 1.60090),
('USD', 'U.S. Dollar', 1.00000),
('AED', 'UAE Dirham', 3.67320),
('UGX', 'Ugandan Shilling', 2485.00000),
('UAH', 'Ukraine Hryvnia', 8.07100),
('UYU', 'Uruguayan New Peso', 20.15000),
('VUV', 'Vanuatu Vatu', 94.83000),
('VEB', 'Venezuelan Bolivar', 0.00000),
('VND', 'Vietnam Dong', 20845.00000),
('YER', 'Yemen Riyal', 213.55000),
('ZMK', 'Zambian Kwacha', 5300.00000),
('ZWD', 'Zimbabwe Dollar', 0.00000),
('GYD', 'Guyana Dollar', 203.45000);

-- --------------------------------------------------------

--
-- Table structure for table `dids`
--
DROP TABLE IF EXISTS `dids`;
CREATE TABLE IF NOT EXISTS `dids` (
  `number` char(40) NOT NULL,
  `account` char(50) NOT NULL DEFAULT '',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `monthlycost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `cost` double(10,5) NOT NULL DEFAULT '0.00000',
  `inc` char(10) NOT NULL DEFAULT '',
  `extensions` char(180) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `provider` char(40) NOT NULL DEFAULT '',
  `country` char(80) NOT NULL DEFAULT '',
  `province` char(80) NOT NULL DEFAULT '',
  `city` char(80) NOT NULL DEFAULT '',
  `prorate` int(1) NOT NULL DEFAULT '0',
  `setup` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `limittime` int(1) NOT NULL DEFAULT '1',
  `disconnectionfee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `variables` text NOT NULL,
  `options` varchar(40) DEFAULT NULL,
  `maxchannels` int(4) NOT NULL DEFAULT '0',
  `chargeonallocation` int(1) NOT NULL DEFAULT '1',
  `allocation_bill_status` int(1) NOT NULL DEFAULT '0',
  `dial_as` char(40) NOT NULL DEFAULT '',
  `inuse` int(11) NOT NULL,
  PRIMARY KEY (`number`),
  KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `default_templates`
--

DROP TABLE IF EXISTS `default_templates`;
CREATE TABLE IF NOT EXISTS `default_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `subject` varchar(500) NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `default_templates`
--

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(1, 'voip_account_refilled', 'Account refilled succesfully', 'Attention: \$vars-&gt;{title} \$vars-&gt;{first} \$vars-&gt;{last}\nYour VOIP account with #YOUR COMPANY NAME# has been refilled.\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR EMAIL ADDRESS#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(2, 'voip_reactivate_account', 'Account reactivate', 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour VOIP account with #YOUR COMPANY NAME# has been reactivated.\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(3, 'email_add_user', 'user added successfully', 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour VOIP account with #YOUR COMPANY NAME# has been added.\nYour Username is -- $vars->{extension} --\nYour Password is -- $vars->{secret} --\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(4, 'add_sip_device', 'Sip Device add', 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nA new device has been enabled on your account. Here\nis the necessary configuration information.\n-------  #YOUR COMPANY NAME# Configuration Info --------\nIn sip.conf:\n[#YOUR COMPANY NAME#-in]\ntype=user\nusername=#YOUR COMPANY NAME#-in\nauth=rsa\ninkeys=$config->{key} ;This key may be downloaded from $config->{key_home}\nhost=$config->{asterisk_server}\ncontext=from-pstn\naccountcode=#YOUR COMPANY NAME#\n[#YOUR COMPANY NAME#]\ntype=peer\nusername=$vars->{extension}\nsecret=$vars->{secret}\nhost=$config->{asterisk_server}\ncallerid= <555-555-5555>\nqualify=yes\naccountcode=#YOUR COMPANY NAME#   ; for call tracking in the cdr\nIn the [globals] section add:\nregister => $vars->{user}:password@$config->{asterisk_server}');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(5, 'add_iax_device', 'Iax device added', 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nA new device has been enabled on your account. Here\nis the necessary configuration information.\n-------  #YOUR COMPANY NAME# Configuration Info --------\nIn iax.conf:\nAt the bottom of the file add:\n[#YOUR COMPANY NAME#-in]\n;trunk=yes   ;optional .. only works if you have a zaptel or ztdummy driver running\ntype=user\nusername=#YOUR COMPANY NAME#-in\nauth=rsa\ninkeys=$config->{key}  ;This key may be downloaded from $config->{key_home}\nhost=$config->{asterisk_server}\ncontext=incoming\naccountcode=#YOUR COMPANY NAME#        ;for call tracking in the cdr\n[#YOUR COMPANY NAME#]\n;to simplify and config outgoing calls\n;trunk=yes   ;optional .. only works if you have a zaptel driver running\ntype=peer\nusername=$vars->{extension}\nsecret=$vars->{secret}\nhost=$config->{asterisk_server}\ncallerid=<555-555-5555>   ;only the number will really be used\nqualify=yes\naccountcode=#YOUR COMPANY NAME#   ; for call tracking in the cdr\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(6, 'email_remove_user', 'remove user account', 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour VOIP Termination with #YOUR COMPANY NAME# has been removed\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(7, 'email_calling_card', 'New Calling Card', 'You have added a $vars->{pricelist} callingcard in the amount of $vars->{pennies} cents.\nCard Number $cc Pin: $pin\nThanks for your patronage.\nThe #YOUR COMPANY NAME# sales team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(8, 'email_add_did', 'did added to your account', 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour DID with #YOUR COMPANY NAME# has been added\nThe number is: $did\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team\nHere is a sample setup which would call a few sip phones with incoming calls:\n[incoming]\nexten => _1$did,1,Wait(2)\nexten => _1$did,2,Dial(SIP/2201&SIP/2202,15,Ttm)  ; dial a couple of phones for 15 secs\nexten => _1$did,3,Voicemail(u1000)   ; go to unavailable voicemail (vm box 1000)\nexten => _1$did,103,Voicemail(b1000) ; go to busy voicemail (vm box 1000)');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(9, 'email_remove_did', 'Remove dids', 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour DID with #YOUR COMPANY NAME# has been removed\nThe number was: $did\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(10, 'email_new_invoice', 'mail for new invoice', 'Invoice # $invoice in the amount of $$total has been added to your account.\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`) VALUES
(11, 'email_low_balance', 'Low balance', 'Your VOIP account with #YOUR COMPANY NAME# has a balance of $$balance.\nPlease visit our website to refill your account to ensure uninterrupted service.\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');


-- --------------------------------------------------------

--
-- Table structure for table `extensions_status`
--
DROP TABLE IF EXISTS `extensions_status`;
CREATE TABLE IF NOT EXISTS `extensions_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tech` varchar(6) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Privilege` varchar(255) DEFAULT NULL,
  `Channel` varchar(255) DEFAULT NULL,
  `Cause` varchar(255) DEFAULT NULL,
  `Causetxt` varchar(255) DEFAULT NULL,
  `PeerStatus` varchar(255) DEFAULT NULL,
  `Peer` varchar(255) DEFAULT NULL,
  `Context` varchar(255) DEFAULT NULL,
  `Application` varchar(255) DEFAULT NULL,
  `AppData` varchar(255) DEFAULT NULL,
  `Priority` varchar(255) DEFAULT NULL,
  `Uniqueid` varchar(255) DEFAULT NULL,
  `Event` varchar(255) DEFAULT NULL,
  `State` varchar(255) DEFAULT NULL,
  `CallerIDName` varchar(255) DEFAULT NULL,
  `CallerID` varchar(255) DEFAULT NULL,
  `AstExtension` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extension` (`extension`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `extension_list`
--
DROP TABLE IF EXISTS `extension_list`;
CREATE TABLE IF NOT EXISTS `extension_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) NOT NULL DEFAULT '0',
  `cardnum` char(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `invoiceid` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `external_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoiceid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoices_total`
--
DROP TABLE IF EXISTS `invoices_total`;
CREATE TABLE IF NOT EXISTS `invoices_total` (
  `invoices_total_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoices_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `value` decimal(15,4) NOT NULL,
  `class` varchar(32) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`invoices_total_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_conf`
--
DROP TABLE IF EXISTS `invoice_conf`;
CREATE TABLE IF NOT EXISTS `invoice_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `address` varchar(300) NOT NULL,
  `city` varchar(20) NOT NULL,
  `province` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `telephone` varchar(30) NOT NULL,
  `fax` varchar(30) NOT NULL,
  `emailaddress` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `invoice_conf` (`accountid`, `company_name`, `address`, `city`, `province`, `country`, `zipcode`, `telephone`, `fax`, `emailaddress`, `website`) VALUES
(-1, 'Company name', 'Address', 'City', 'Province', 'Country', 'Zipcode', 'Telephone', 'Fax', 'Email Address', 'Website');


-- --------------------------------------------------------

--
-- Table structure for table `ip_map`
--
DROP TABLE IF EXISTS `ip_map`;
CREATE TABLE IF NOT EXISTS `ip_map` (
  `ip` char(15) NOT NULL DEFAULT '',
  `account` char(20) NOT NULL DEFAULT '',
  `prefix` varchar(20) NOT NULL DEFAULT '',
  `context` varchar(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip`,`prefix`),
  KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `language` varchar(5) NOT NULL,
  `languagename` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `language` (`language`, `languagename`, `active`) VALUES
('en', 'English', 1),
('fr', 'French', 1),
('de', 'German', 1);

-- --------------------------------------------------------

--
-- Table structure for table `manager_action_variables`
--
DROP TABLE IF EXISTS `manager_action_variables`;
CREATE TABLE IF NOT EXISTS `manager_action_variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(60) NOT NULL DEFAULT '',
  `value` char(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `outbound_routes`
--
DROP TABLE IF EXISTS `outbound_routes`;
CREATE TABLE IF NOT EXISTS `outbound_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern` char(40) DEFAULT NULL,  
  `comment` char(80) NOT NULL DEFAULT '',
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `trunk` char(80) NOT NULL DEFAULT '',
  `inc` char(10) NOT NULL DEFAULT '',
  `strip` char(40) NOT NULL DEFAULT '',
  `prepend` char(40) NOT NULL DEFAULT '',
  `precedence` int(4) NOT NULL DEFAULT '0',
  `resellers` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `trunk` (`trunk`),
  KEY `pattern` (`pattern`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--
DROP TABLE IF EXISTS `packages`;
CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL DEFAULT '',
  `pricelist` char(40) NOT NULL DEFAULT '',
  `pattern` char(40) NOT NULL DEFAULT '',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `reseller` varchar(50) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pricelist` (`pricelist`),
  KEY `reseller` (`reseller`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--
DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pbx_list`
--
DROP TABLE IF EXISTS `pbx_list`;
CREATE TABLE IF NOT EXISTS `pbx_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pbx_id` int(11) NOT NULL DEFAULT '0',
  `cardnum` char(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pricelists`
--
DROP TABLE IF EXISTS `pricelists`;
CREATE TABLE IF NOT EXISTS `pricelists` (
  `name` char(40) NOT NULL,
  `markup` int(11) NOT NULL DEFAULT '0',
  `inc` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `reseller` char(50) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `queue_list`
--
DROP TABLE IF EXISTS `queue_list`;
CREATE TABLE IF NOT EXISTS `queue_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) NOT NULL DEFAULT '0',
  `cardnum` char(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `resellers`
--
DROP TABLE IF EXISTS `resellers`;
CREATE TABLE IF NOT EXISTS `resellers` (
  `name` varchar(40) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `posttoexternal` int(11) NOT NULL DEFAULT '0',
  `agile_site_id` int(11) NOT NULL DEFAULT '0',
  `config_file` char(80) NOT NULL DEFAULT 'reseller.conf',
  `companyname` varchar(255) DEFAULT NULL,
  `slogan` varchar(255) DEFAULT NULL,
  `footer` varchar(255) DEFAULT NULL,
  `pricelist` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `adminemail` varchar(255) DEFAULT NULL,
  `salesemail` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `defaultbrand` varchar(45) NOT NULL DEFAULT 'default',
  `defaultcurrency` varchar(45) NOT NULL DEFAULT 'USD',
  `defaultcredit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `externalbill` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reseller_pricing`
--
DROP TABLE IF EXISTS `reseller_pricing`;
CREATE TABLE IF NOT EXISTS `reseller_pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reseller` varchar(50) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `monthlycost` double(10,5) NOT NULL DEFAULT '0.00000',
  `prorate` int(11) NOT NULL DEFAULT '0',
  `setup` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `connectcost` decimal(10,0) NOT NULL DEFAULT '0',
  `includedseconds` int(11) NOT NULL DEFAULT '0',
  `note` varchar(50) NOT NULL DEFAULT '',
  `disconnectionfee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `status` int(11) NOT NULL DEFAULT '1',
  `inc` char(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `reseller` (`reseller`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--
DROP TABLE IF EXISTS `routes`;
CREATE TABLE IF NOT EXISTS `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern` char(40) DEFAULT NULL,
  `comment` char(80) DEFAULT NULL,
  `connectcost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `includedseconds` int(11) NOT NULL,
  `cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `pricelist` char(80) DEFAULT NULL,
  `inc` int(11) DEFAULT NULL,
  `reseller` char(50) DEFAULT NULL,
  `precedence` int(4) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pattern` (`pattern`),
  KEY `pricelist` (`pricelist`),
  KEY `reseller` (`reseller`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sweeplist`
--
DROP TABLE IF EXISTS `sweeplist`;
CREATE TABLE IF NOT EXISTS `sweeplist` (
  `Id` int(10) unsigned NOT NULL DEFAULT '0',
  `sweep` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT IGNORE INTO `sweeplist` (`Id`, `sweep`) VALUES
(0, 'daily'),
(1, 'weekly'),
(2, 'monthly'),
(3, 'quarterly'),
(4, 'semi-annually'),
(5, 'annually');

-- --------------------------------------------------------

--
-- Table structure for table `system`
--
DROP TABLE IF EXISTS `system`;
CREATE TABLE IF NOT EXISTS `system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `reseller` varchar(48) DEFAULT NULL,
  `brand` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `reseller` (`reseller`),
  KEY `brand` (`brand`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `system` (`id`, `name`, `value`, `comment`, `timestamp`, `reseller`, `brand`) VALUES
(1, 'log_file', '/var/log/astpp/astpp.log', 'Where do I log to?', NULL, NULL, NULL),
(2, 'callout_accountcode', 'admin', 'Call Files: What accountcode should we use?', NULL, NULL, NULL),
(3, 'lcrcontext', 'astpp-outgoing', 'This is the Local context we use to route our outgoing calls through esp for callbacks', NULL, NULL, NULL),
(4, 'maxretries', '3', 'Call Files: How many times do we retry?', NULL, NULL, NULL),
(5, 'retrytime', '30', 'Call Files: How long do we wait between retries?', NULL, NULL, NULL),
(6, 'waittime', '15', 'Call Files: How long do we wait before the initial call?', NULL, NULL, NULL),
(7, 'clidname', 'Private', 'Call Files: Outgoing CallerID company_emailName', NULL, NULL, NULL),
(8, 'clidnumber', '0000000000', 'Call Files: Outgoing CallerID Number', NULL, NULL, NULL),
(9, 'callingcards_callback_context', 'astpp-callingcards', 'Call Files: For callingcards what context do we end up in?', NULL, NULL, NULL),
(10, 'callingcards_callback_extension', 's', 'Call Files: For callingcards what extension do we use?', NULL, NULL, NULL),
(11, 'opensips_dbengine', 'MySQL', 'For now this must be MySQL', NULL, NULL, NULL),
(12, 'opensips', '0', 'Use Opensips?  1 for yes or 0 for no', NULL, NULL, NULL),
(13, 'opensips_dbname', 'opensips', 'Opensips Database Name', NULL, NULL, NULL),
(14, 'opensips_dbuser', 'OPENSIP DB USER', 'Opensips Database User', NULL, NULL, NULL),
(15, 'opensips_dbhost', 'localhost', 'Opensips Database Host', NULL, NULL, NULL),
(16, 'opensips_dbpass', 'OPENSIP DB PASSWORD', 'Opensips Database Password', NULL, NULL, NULL),
(17, 'opensips_domain', 'OPENSIP IP ADDRESS', 'Opensips Domain', NULL, NULL, NULL),
(18, 'company_email', 'info@astpp.org', 'Email address that email should appear to be from', NULL,NULL,NULL),
(19, 'asterisk_dir', '/etc/asterisk', 'Which directory are asterisk configuration files stored in?', NULL,NULL,NULL),
(20, 'company_website', 'http://www.astpp.org', 'Link to your company website', NULL,NULL,NULL),
(21, 'company_name', 'ASTPP.ORG', 'The name of your company.  Used in emails.', NULL,NULL,NULL),
(22, 'email', '1', 'Send out email? 0=no 1=yes', NULL,NULL,NULL),
(23, 'user_email', '1', 'Email user on account changes? 0=no 1=yes', NULL,NULL,NULL),
(24, 'debug', '0', 'Enable debugging output? 0=no 1=yes', NULL,NULL,NULL),
(25, 'emailadd', 'info@astpp.org', 'Administrator email address', NULL,NULL,NULL),
(26, 'startingdigit', '0', 'The digit that all calling cards must start with. 0=disabled', NULL,NULL,NULL),
(27, 'enablelcr', '1', 'Use least cost routing 0=no 1=yes', NULL,NULL,NULL),
(28, 'log_file', '/var/log/astpp/astpp.log', 'ASTPP Log file', NULL,NULL,NULL),
(29, 'key_home', 'http://www.astpp.org/astpp.pub', 'Asterisk RSA Key location (optional)', NULL,NULL,NULL),
(30, 'rate_engine_csv_file', '/var/log/astpp/astpp.csv', 'CSV File for call rating data', NULL,NULL,NULL),
(31, 'csv_dir', '/var/log/astpp/', 'CSV File Directory', NULL,NULL,NULL),
(32, 'default_brand', 'default', 'Default pricelist.  If a price is not found in the customers pricelist we check this one.', NULL,NULL,NULL),
(33, 'new_user_brand', 'default', 'What is the default pricelist for new customers?', NULL,NULL,NULL),
(34, 'default_context', 'default', 'What is the default context for new devices?', NULL,NULL,NULL),
(35, 'cardlength', '10', 'Number of digits in calling cards and cc codes.', NULL,NULL,NULL),
(36, 'asterisk_server', 'voip.astpp.org', 'Your default voip server.  Used in outgoing email.', NULL,NULL,NULL),
(37, 'currency', 'USD', 'Name of the currency you use', NULL,NULL,NULL),
(38, 'iax_port', '4569', 'Default IAX2 Port', NULL,NULL,NULL),
(39, 'sip_port', '5060', 'Default SIP Port', NULL,NULL,NULL),
(40, 'ipaddr', 'dynamic', 'Default IP Address for new devices', NULL,NULL,NULL),
(41, 'key', 'astpp.pub', 'Asterisk RSA Key Name (Optional)', NULL,NULL,NULL),
(42, 'pinlength', '6', 'For those calling cards that are using pins this is the number of digits it will have.', NULL,NULL,NULL),
(43, 'credit_limit', '0', 'Default credit limit in dollars.', NULL,NULL,NULL),
(44, 'decimalpoints', '4', 'How many decimal points do we bill to?', NULL,NULL,NULL),
(45, 'decimalpoints_tax', '2', 'How many decimal points do we calculate taxes to?', NULL,NULL,NULL),
(46, 'decimalpoints_total', '2', 'How many decimal points do we calculate totals to?', NULL,NULL,NULL),
(47, 'max_free_length', '100', 'What is the maximum length (in minutes) of calls that are at no charge?', NULL,NULL,NULL),
(48, 'trackvendorcharges', '1', 'Do we track the amount of money we spend with specific providers? 0=no 1=yes', NULL,NULL,NULL),
(52, 'default_language', 'en', 'Default ASTPP Language', NULL,NULL,NULL),
(53, 'card_retries', '3', 'How many retries do we allow for calling card numbers?', NULL,NULL,NULL),
(54, 'pin_retries', '3', 'How many retries do we allow for pins?', NULL,NULL,NULL),
(55, 'number_retries', '3', 'How many retries do we allow calling card users when dialing a number?', NULL,NULL,NULL),
(56, 'booth_context', 'callshop_booth', 'Please enter the default context for a callshop booth.', NULL,NULL,NULL),
(57, 'callingcards_max_length', '9000', 'What is the maximum length (in ms) of a callingcard call?', NULL,NULL,NULL),
(58, 'template_die_on_bad_params', '0', 'Should HTML::Template die on bad parameters?', NULL,NULL,NULL),
(60, 'astpp_dir', '/var/lib/astpp', 'Where do the astpp configs live?', NULL,NULL,NULL),
(61, 'auth', 'Passw0rd!', 'This is the override authorization code and will allow access to the system.', NULL,NULL,NULL),
(62, 'rt_dbengine', 'MySQL', 'Database type for Asterisk(tm) -Realtime', NULL,NULL,NULL),
(63, 'cdr_dbengine', 'MySQL', 'Database type for the cdr database', NULL,NULL,NULL),
(64, 'osc_dbengine', 'MySQL', 'Database type for OSCommerce', NULL,NULL,NULL),
(65, 'agile_dbengine', 'MySQL', 'Database type for AgileBill(tm)', NULL,NULL,NULL),
(66, 'freepbx_dbengine', 'MySQL', 'Database type for FreePBX', NULL,NULL,NULL),
(67, 'externalbill', 'internal', 'Please specify the external billing application to use.  If you are not using any then leave it blank.  Valid options are ', NULL,NULL,NULL),
(68, 'callingcards', '1', 'Do you wish to enable calling cards?  1 for yes and 2 for no.', NULL,NULL,NULL),
(69, 'astcdr', '1', 'Change this one at your own peril.  If you switch it off, calls will not be marked as billed in asterisk once they are billed.', NULL,NULL,NULL),
(70, 'posttoastpp', '1', 'Change this one at your own peril.  If you switch it off, calls will not be written to astpp when they are calculated.', NULL,NULL,NULL),
(71, 'sleep', '10', 'How long shall the rating engine sleep after it has been notified of a hangup? (in seconds)', NULL,NULL,NULL),
(72, 'users_dids_amp', '0', 'If this is enabled, ASTPP will create users and DIDs in the FreePBX (www.freepbx.org) database.', NULL,NULL,NULL),
(73, 'users_dids_rt', '0', 'If this is enabled, ASTPP will create users and DIDs in the Asterisk Realtime database.', NULL,NULL,NULL),
(74, 'users_dids_freeswitch', '1', 'If this is enabled, ASTPP will create SIP users in the freeswitch database.', NULL,NULL,NULL),
(75, 'softswitch', '1', 'What softswitch are we using?  0=asterisk, 1=freeswitch', NULL,NULL,NULL),
(76, 'service_prepend', '778', '', NULL, NULL, NULL),
(77, 'service_length,', '7', '', NULL, NULL, NULL),
(78, 'service_filler', '4110000', '', NULL, NULL, NULL),
(79, 'asterisk_cdr_table', 'cdr', 'Which table of the Asterisk(TM) database are the cdrs in?', NULL, NULL, NULL),
(80, 'agile_host', 'localhost', 'Agile Database Host', NULL,NULL,NULL),
(81, 'agile_db', 'agile', 'Agile Database Name', NULL,NULL,NULL),
(82, 'agile_user', 'AGILE DB USER', 'Agile Database Username', NULL,NULL,NULL),
(83, 'agile_pass', 'AGILE DB PASSWORD', 'Agile Database Password', NULL,NULL,NULL),
(84, 'agile_site_id', '1', 'Agile Site Id', NULL,NULL,NULL),
(85, 'agile_charge_status', '0', 'Agile Charge Status', NULL,NULL,NULL),
(86, 'agile_taxable', '1', 'Agile Charge Taxable?', NULL,NULL,NULL),
(87, 'agile_dbprefix', '_', 'Agile DB Prefix', NULL,NULL,NULL),
(88, 'agile_service_prepend', '778', 'Agile Service Prepend Code', NULL,NULL,NULL),
(89, 'osc_host', 'localhost', 'Oscommerce Database Host', NULL,NULL,NULL),
(90, 'osc_db', 'oscommerce', 'Oscommerce Database Name', NULL,NULL,NULL),
(91, 'osc_user', 'OSC DB USER', 'Oscommerce Database Username', NULL,NULL,NULL),
(92, 'osc_pass', 'OSC DB PASSWORD', 'Oscommerce Database Password', NULL,NULL,NULL),
(93, 'osc_product_id', '99999999', 'Oscommerce Default Product ID', NULL,NULL,NULL),
(94, 'osc_payment_method', '"Charge"', 'Oscommerce Default Payment method', NULL,NULL,NULL),
(95, 'osc_order_status', '1', 'Oscommerce Default Order Status', NULL,NULL,NULL),
(96, 'osc_post_nc', '1', 'Do we post ', NULL,NULL,NULL),
(97, 'freepbx_host', 'localhost', 'Freepbx Database Host', NULL,NULL,NULL),
(98, 'freepbx_db', 'asterisk', 'Freepbx Database name', NULL,NULL,NULL),
(99, 'freepbx_user', 'FREEPBX DB USER', 'FreePBX Database Username', NULL,NULL,NULL),
(100, 'freepbx_pass', 'FREEPBX DB PASSWORD', 'FreePBX Database Password', NULL,NULL,NULL),
(101, 'freepbx_iax_table', 'iax', 'FreePBX IAX Table Name', NULL,NULL,NULL),
(102, 'freepbx_table', 'sip', 'FreePBX SIP Table Name', NULL,NULL,NULL),
(103, 'freepbx_extensions_table', 'extensions', 'FreePBX Extensions Table Name', NULL,NULL,NULL),
(104, 'freepbx_codec_allow', 'g729,ulaw,alaw', 'FreePBX Default Asterisk Allowed Codec', NULL,NULL,NULL),
(105, 'freepbx_codec_disallow', 'all', 'FreePBX Default Asterisk Disallowed Codec', NULL,NULL,NULL),
(106, 'freepbx_mailbox_group', 'default', 'Freepbx Default Mailbox Group', NULL,NULL,NULL),
(107, 'freepbx_sip_nat', 'yes', '', NULL,NULL,NULL),
(108, 'freepbx_sip_canreinvite', 'no', '', NULL,NULL,NULL),
(109, 'freepbx_sip_dtmfmode', 'rfc2833', '', NULL,NULL,NULL),
(110, 'freepbx_sip_qualify', 'yes', '', NULL,NULL,NULL),
(111, 'freepbx_sip_type', 'friend', '', NULL,NULL,NULL),
(112, 'freepbx_sip_callgroup', '', '', NULL,NULL,NULL),
(113, 'freepbx_sip_pickupgroup', '', '', NULL,NULL,NULL),
(114, 'freepbx_iax_notransfer', 'yes', '', NULL,NULL,NULL),
(115, 'freepbx_iax_type', 'friend', '', NULL,NULL,NULL),
(116, 'freepbx_iax_qualify', 'yes', '', NULL,NULL,NULL),
(117, 'rt_host', 'localhost', 'Asterisk Realtime database host', NULL,NULL,NULL),
(118, 'rt_db', 'realtime', 'Asterisk Realtime database name', NULL,NULL,NULL),
(119, 'rt_user', 'ASTERISK REALTIME DB USER', 'Asterisk Realtime database username', NULL,NULL,NULL),
(120, 'rt_pass', 'ASTERISK REALTIME DB PASSWORD', 'Asterisk Realtime database password', NULL,NULL,NULL),
(121, 'rt_iax_table', 'iax', 'Asterisk Realtime IAX Table Name', NULL,NULL,NULL),
(122, 'rt_sip_table', 'sip', 'Asterisk Realtime SIP Table Name', NULL,NULL,NULL),
(123, 'rt_extensions_table', 'extensions', 'Asterisk Realtime Extensions Table Name', NULL,NULL,NULL),
(124, 'rt_sip_insecure', 'very', '', NULL,NULL,NULL),
(125, 'rt_sip_nat', 'yes', '', NULL,NULL,NULL),
(126, 'rt_sip_canreinvite', 'no', '', NULL,NULL,NULL),
(127, 'rt_codec_allow', 'g729,ulaw,alaw', '', NULL,NULL,NULL),
(128, 'rt_codec_disallow', 'all', '', NULL,NULL,NULL),
(129, 'rt_mailbox_group', 'default', '', NULL,NULL,NULL),
(130, 'rt_sip_qualify', 'yes', '', NULL,NULL,NULL),
(131, 'rt_sip_type', 'friend', '', NULL,NULL,NULL),
(132, 'rt_iax_qualify', 'yes', '', NULL,NULL,NULL),
(133, 'rt_iax_type', 'friend', '', NULL,NULL,NULL),
(134, 'rt_voicemail_table', 'voicemail_users', 'Asterisk Realtime Voicemail Table Name', NULL, NULL, NULL),
(135, 'calling_cards_rate_announce', '0', 'Do we want the calling cards script to announce the rate on calls?', NULL, NULL, NULL),
(136, 'calling_cards_timelimit_announce', '0', 'Do we want the calling cards script to announce the timelimit on calls?', NULL, NULL, NULL),
(137, 'calling_cards_cancelled_prompt', '1', 'Do we want the calling cards script to announce that the call was cancelled?', NULL, NULL, NULL),
(138, 'calling_cards_menu', '0', 'Do we want the calling cards script to present a menu before exiting?', NULL, NULL, NULL),
(139, 'calling_cards_connection_prompt', '0', 'Do we want the calling cards script to announce that it is connecting the call?', NULL, NULL, NULL),
(140, 'calling_cards_pin_input_timeout', '15000', 'How long do we wait when entering the calling card pin?  Specified in MS', NULL, NULL, NULL),
(141, 'calling_cards_number_input_timeout', '15000', 'How long do we wait when entering the calling card number?  Specified in MS', NULL, NULL, NULL),
(142, 'calling_cards_dial_input_timeout', '15000', 'How long do we wait when entering the destination number in calling cards?  Specified in MS', NULL, NULL, NULL),
(143, 'calling_cards_general_input_timeout', '15000', 'How long do we wait for input in general menus?  Specified in MS', NULL, NULL, NULL),
(144, 'calling_cards_welcome_file', 'astpp-welcome.wav', 'What do we play for a welcome file?', NULL, NULL, NULL),
(145, 'sip_ext_prepend', '10', 'What should every autoadded SIP extension begin with?', NULL, NULL, NULL),
(146, 'iax2_ext_prepend', '10', 'What should every autoadded IAX2 extension begin with?', NULL, NULL, NULL),
(147, 'cc_prepend', '', 'What should every autoadded callingcard begin with?', NULL, NULL, NULL),
(148, 'pin_cc_prepend', '', 'What should every autoadded callingcard pin begin with?', NULL, NULL, NULL),
(149, 'pin_act_prepend', '', 'What should every autoadded account pin begin with?', NULL, NULL, NULL),
(150, 'freeswitch_directory', '/usr/local/freeswitch', 'What is the Freeswitch root directory?', NULL, NULL, NULL),
(151, 'freeswitch_password', 'ClueCon', 'Freeswitch event socket password', NULL, NULL, NULL),
(152, 'freeswitch_host', 'localhost', 'Freeswitch event socket host', NULL, NULL, NULL),
(153, 'freeswitch_port', '8021', 'Freeswitch event socket port', NULL, NULL, NULL),
(154, 'freeswitch_timeout', '30', 'Freeswitch seconds to expect a heartbeat event or reconnect', NULL, NULL, NULL),
(155, 'freeswitch_dbengine', 'MySQL', 'For now this must be MySQL', NULL, NULL, NULL),
(156, 'freeswitch_dbhost', 'localhost', 'Freeswitch Database Host', NULL,NULL,NULL),
(157, 'freeswitch_dbname', 'freeswitch', 'Freeswitch Database Name', NULL,NULL,NULL),
(158, 'freeswitch_dbuser', 'FREESWITCH DB USER', 'Freeswitch Database User', NULL,NULL,NULL),
(159, 'freeswitch_dbpass', 'FREESWITCH DB PASSWORD', 'Freeswitch Database Password', NULL,NULL,NULL),
(160, 'freeswitch_cdr_table', 'fscdr', 'Which table of the cdr database are the Freeswitch cdrs in?', NULL, NULL, NULL),
(161, 'freeswitch_domain', '$${local_ip_v4}', 'This is entered as the Freeswitch domain.', NULL, NULL, NULL),
(162, 'freeswitch_context', 'default', 'This is entered as the Freeswitch user context.', NULL, NULL, NULL),
(163, 'freeswitch_sound_files', '/en/us/callie', 'Where are our sound files located?', NULL, NULL, NULL),
(164, 'cdr_dbhost', 'localhost', 'CDR Database Host', NULL,NULL,NULL),
(165, 'cdr_dbname', 'fscdr', 'CDR Database Name', NULL,NULL,NULL),
(166, 'cdr_dbuser', 'CDR DB USER', 'CDR Database User', NULL,NULL,NULL),
(167, 'cdr_dbpass', 'CDR DB PASSWORD', 'CDR Database Password', NULL,NULL,NULL),
(168, 'astman_user', 'admin', 'Asterisk(tm) Manager Interface User', NULL,NULL,NULL),
(169, 'astman_host', 'localhost', 'Asterisk(tm) Manager Interface Host', NULL,NULL,NULL),
(170, 'astman_secret', 'amp111', 'Asterisk(tm) Manager Interface Secret', NULL,NULL,NULL),
(171, 'call_max_length', '1440000', 'What is the maximum length (in ms) of a LCR call?', NULL,NULL,NULL),
(172, 'thirdlane_mods', '0', 'Provides a few different modifications across the rating code to work better with Thirdlane(tm) cdrs.', NULL,NULL,NULL),
(173, 'cc_ani_auth', '0', 'Calling card ANI authentiation. 0 for disable and 1 for enable', NULL, NULL, NULL),
(174, 'base_currency', 'USD', 'Base Currency of System', NULL, NULL, NULL),
(178, 'callingcard_leg_a_cdr', '0', 'Save leg A cdr of calling card? 0=no 1=yes', NULL, NULL, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--
DROP TABLE IF EXISTS `taxes`;
CREATE TABLE IF NOT EXISTS `taxes` (
  `taxes_id` int(11) NOT NULL AUTO_INCREMENT,
  `taxes_priority` int(5) DEFAULT '1',
  `taxes_amount` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `taxes_rate` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `taxes_description` varchar(255) NOT NULL,
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`taxes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `taxes_to_accounts`
--
DROP TABLE IF EXISTS `taxes_to_accounts`;
CREATE TABLE IF NOT EXISTS `taxes_to_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` varchar(11) NOT NULL,
  `taxes_id` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `taxes_to_accounts_view`
--
DROP TABLE IF EXISTS `taxes_to_accounts_view`;
CREATE TABLE IF NOT EXISTS `taxes_to_accounts_view` (
`id` int(11)
,`accountid` varchar(11)
,`taxes_id` int(11)
,`taxes_priority` int(5)
,`taxes_amount` decimal(10,5)
,`taxes_rate` decimal(10,5)
,`taxes_description` varchar(255)
);
-- --------------------------------------------------------

--
-- Table structure for table `templates`
--
DROP TABLE IF EXISTS `templates`;
CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `subject` varchar(10000) NOT NULL,
  `accountid` int(11) NOT NULL,
  `template` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `reseller` (`accountid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('voip_account_refilled', 'Account refilled succesfullydd', -1, '<p>\nAttention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last} Your VOIP account with $config-&gt;{company_name} has been refilled. For information please visit #YOUR COMPANY WEBSITE# or contact our support department at #YOUR COMPANY EMAIL# Thanks, The #YOUR COMPANY NAME# support team</p>');

INSERT INTO `templates` ( `name`, `subject`, `accountid`, `template`) VALUES
('voip_reactivate_account', 'Account reactivate', -1, 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour VOIP account with #YOUR COMPANY NAME# has been reactivated.\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('email_add_user', 'user added successfully', -1, 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour VOIP account with #YOUR COMPANY NAME# has been added.\nYour Username is -- $vars->{extension} --\nYour Password is -- $vars->{secret} --\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('add_sip_device', 'Sip Device add', -1, '<p>\n	Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last} A new device has been enabled on your account. Here is the necessary configuration information. ------- $config-&gt;{company_name} Configuration Info -------- In sip.conf: [$config-&gt;{company_name}-in] type=user username=$config-&gt;{company_name}-in auth=rsa inkeys=$config-&gt;{key} ;This key may be downloaded from $config-&gt;{key_home} host=$config-&gt;{asterisk_server} context=from-pstn accountcode=$config-&gt;{company_name} [$config-&gt;{company_name}] type=peer username=$vars-&gt;{extension} secret=$vars-&gt;{secret} host=$config-&gt;{asterisk_server} callerid= &lt;555-555-5555&gt; qualify=yes accountcode=$config-&gt;{company_name} ; for call tracking in the cdr In the [globals] section add: register =&gt; $vars-&gt;{user}:password@$config-&gt;{asterisk_server}</p>');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('add_iax_device', 'Iax device added', -1, 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nA new device has been enabled on your account. Here\nis the necessary configuration information.\n-------  #YOUR COMPANY NAME# Configuration Info --------\nIn iax.conf:\nAt the bottom of the file add:\n[#YOUR COMPANY NAME#-in]\n;trunk=yes   ;optional .. only works if you have a zaptel or ztdummy driver running\ntype=user\nusername=#YOUR COMPANY NAME#-in\nauth=rsa\ninkeys=$config->{key}  ;This key may be downloaded from $config->{key_home}\nhost=$config->{asterisk_server}\ncontext=incoming\naccountcode=#YOUR COMPANY NAME#        ;for call tracking in the cdr\n[#YOUR COMPANY NAME#]\n;to simplify and config outgoing calls\n;trunk=yes   ;optional .. only works if you have a zaptel driver running\ntype=peer\nusername=$vars->{extension}\nsecret=$vars->{secret}\nhost=$config->{asterisk_server}\ncallerid=<555-555-5555>   ;only the number will really be used\nqualify=yes\naccountcode=#YOUR COMPANY NAME#   ; for call tracking in the cdr\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('email_remove_user', 'remove user account', -1, 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour VOIP Termination with #YOUR COMPANY NAME# has been removed\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('email_calling_card', 'New Calling Card', -1, 'You have added a $vars->{pricelist} callingcard in the amount of $vars->{pennies} cents.\nCard Number $cc Pin: $pin\nThanks for your patronage.\nThe #YOUR COMPANY NAME# sales team');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('email_add_did', 'did added to your account', -1, 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour DID with #YOUR COMPANY NAME# has been added\nThe number is: $did\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team\nHere is a sample setup which would call a few sip phones with incoming calls:\n[incoming]\nexten => _1$did,1,Wait(2)\nexten => _1$did,2,Dial(SIP/2201&SIP/2202,15,Ttm)  ; dial a couple of phones for 15 secs\nexten => _1$did,3,Voicemail(u1000)   ; go to unavailable voicemail (vm box 1000)\nexten => _1$did,103,Voicemail(b1000) ; go to busy voicemail (vm box 1000)');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('email_remove_did', 'Remove dids', -1, 'Attention: $vars-&gt;{title} $vars-&gt;{first} $vars-&gt;{last}\nYour DID with #YOUR COMPANY NAME# has been removed\nThe number was: $did\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('email_new_invoice', 'mail for new invoice', -1, 'Invoice # $invoice in the amount of $$total has been added to your account.\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');

INSERT INTO `templates` (`name`, `subject`, `accountid`, `template`) VALUES
('email_low_balance', 'Low balance', -1, 'Your VOIP account with #YOUR COMPANY NAME# has a balance of $$balance.\nPlease visit our website to refill your account to ensure uninterrupted service.\nFor information please visit #YOUR COMPANY WEBSITE# or\ncontact our support department at #YOUR COMPANY EMAIL#\nThanks,\nThe #YOUR COMPANY NAME# support team');


-- --------------------------------------------------------

--
-- Table structure for table `trunks`
--
DROP TABLE IF EXISTS `trunks`;
CREATE TABLE IF NOT EXISTS `trunks` (
  `name` varchar(30) NOT NULL,
  `tech` char(10) NOT NULL DEFAULT '',
  `path` char(40) NOT NULL DEFAULT '',
  `provider` char(100) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `dialed_modify` text NOT NULL,
  `resellers` text NOT NULL,
  `precedence` int(4) NOT NULL DEFAULT '0',
  `maxchannels` int(11) NOT NULL DEFAULT '0',
  `inuse` int(11) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `provider` (`provider`),
  KEY `provider_2` (`provider`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userlevels`
--
DROP TABLE IF EXISTS `userlevels`;
CREATE TABLE IF NOT EXISTS `userlevels` (
  `userlevelid` int(11) NOT NULL,
  `userlevelname` varchar(50) NOT NULL,
  PRIMARY KEY (`userlevelid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `userlevels` (`userlevelid`, `userlevelname`) VALUES
(-1, 'Administrator'),
(0, 'Customer'),
(1, 'Reseller'),
(2, 'Admin'),
(3, 'Provider'),
(4, 'Sub Admin'),
(5, 'CallShop');

-- --------------------------------------------------------

--
-- Structure for view `invoice_list_view`
--
DROP TABLE IF EXISTS `invoice_list_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `invoice_list_view` AS select `invoices`.`invoiceid` AS `invoiceid`,`invoices`.`accountid` AS `accountid`,`invoices`.`date` AS `date`,`invoices`.`status` AS `status`,`invoices_total`.`value` AS `value`,`invoices_total`.`class` AS `class` from (`invoices` join `invoices_total`) where ((`invoices_total`.`class` = 9) and (`invoices`.`invoiceid` = `invoices_total`.`invoices_id`));

-- --------------------------------------------------------

--
-- Structure for view `taxes_to_accounts_view`
--
DROP TABLE IF EXISTS `taxes_to_accounts_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `taxes_to_accounts_view` AS select `taxes_to_accounts`.`id` AS `id`,`taxes_to_accounts`.`accountid` AS `accountid`,`taxes`.`taxes_id` AS `taxes_id`,`taxes`.`taxes_priority` AS `taxes_priority`,`taxes`.`taxes_amount` AS `taxes_amount`,`taxes`.`taxes_rate` AS `taxes_rate`,`taxes`.`taxes_description` AS `taxes_description` from (`taxes_to_accounts` join `taxes`) where (`taxes`.`taxes_id` = `taxes_to_accounts`.`taxes_id`);

-- --------------------------------------------------------

--
-- Structure for view `callingcard_cdrs`
--
DROP TABLE IF EXISTS `callingcard_cdrs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `callingcard_cdrs` AS select `callingcardcdrs`.`id` AS `id`,`callingcardcdrs`.`cardnumber` AS `cardnumber`,`callingcardcdrs`.`clid` AS `clid`,`callingcardcdrs`.`destination` AS `destination`,`callingcardcdrs`.`disposition` AS `disposition`,`callingcardcdrs`.`callstart` AS `callstart`,`callingcardcdrs`.`seconds` AS `seconds`,`callingcardcdrs`.`debit` AS `debit`,`callingcardcdrs`.`credit` AS `credit`,`callingcardcdrs`.`status` AS `status`,`callingcardcdrs`.`uniqueid` AS `uniqueid`,`callingcardcdrs`.`notes` AS `notes`,`callingcardcdrs`.`pricelist` AS `pricelist`,`callingcardcdrs`.`pattern` AS `pattern`,(select `callingcardbrands`.`reseller` from (`callingcards` join `callingcardbrands`) where ((`callingcardbrands`.`name` = `callingcards`.`brand`) and (`callingcards`.`cardnumber` = `callingcardcdrs`.`cardnumber`))) AS `reseller`,(select `callingcards`.`account` from `callingcards` where (`callingcards`.`cardnumber` = `callingcardcdrs`.`cardnumber`)) AS `account` from `callingcardcdrs`;

-- --------------------------------------------------------

--
-- Structure for view `customer_cdrs`
--
DROP TABLE IF EXISTS `customer_cdrs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customer_cdrs` AS select `a`.`accountid` AS `accountid`,`a`.`cc` AS `cc`,`a`.`number` AS `number`,`a`.`reseller` AS `reseller`,`b`.`id` AS `cdr_id`,`b`.`uniqueid` AS `uniqueid`,`b`.`callerid` AS `callerid`,`b`.`callednum` AS `callednum`,`b`.`billseconds` AS `billseconds`,`b`.`trunk` AS `trunk`,`b`.`disposition` AS `disposition`,`b`.`callstart` AS `callstart`,`b`.`debit` AS `debit`,`b`.`credit` AS `credit`,`b`.`status` AS `status`,replace(substr(substring_index(`b`.`notes`,_latin1'|',2),(length(substring_index(`b`.`notes`,_latin1'|',1)) + 1)),_latin1'|',_latin1'') AS `notes`,`b`.`provider` AS `provider`,`b`.`cost` AS `cost`,`b`.`pricelist` AS `pricelist`,(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`b`.`pattern`, '.', 1),'$',1),'^',-1)) AS `pattern`,`b`.`calltype` AS `calltype` from (`accounts` `a` join `cdrs` `b`) where ((`a`.`number` = `b`.`cardnum`) and (`a`.`type` = 0) and (`b`.`uniqueid` <> '')  and (`b`.`uniqueid` <> '0') and (`b`.`uniqueid` <> '1') and  (`b`.`uniqueid` <> 'N/A'));

-- --------------------------------------------------------

--
-- Structure for view `provider_cdrs`
--
DROP TABLE IF EXISTS `provider_cdrs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `provider_cdrs` AS select `a`.`accountid` AS `accountid`,`a`.`cc` AS `cc`,`a`.`number` AS `number`,`a`.`reseller` AS `reseller`,`b`.`id` AS `cdr_id`,`b`.`uniqueid` AS `uniqueid`,`b`.`callerid` AS `callerid`,`b`.`callednum` AS `callednum`,`b`.`billseconds` AS `billseconds`,`b`.`trunk` AS `trunk`,`b`.`disposition` AS `disposition`,`b`.`callstart` AS `callstart`,`b`.`debit` AS `debit`,`b`.`credit` AS `credit`,`b`.`status` AS `status`,replace(substr(substring_index(`b`.`notes`,_latin1'|',2),(length(substring_index(`b`.`notes`,_latin1'|',1)) + 1)),_latin1'|',_latin1'') AS `notes`,`b`.`provider` AS `provider`,`b`.`cost` AS `cost`,`b`.`pricelist` AS `pricelist`,(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`b`.`pattern`, '.', 1),'$',1),'^',-1)) AS `pattern`,`b`.`calltype` AS `calltype` from (`accounts` `a` join `cdrs` `b`) where ((`a`.`number` = `b`.`cardnum`) and (`a`.`type` = 3) and (`b`.`uniqueid` <> '0') and (`b`.`uniqueid` <> '1') and (`b`.`uniqueid` <> '') and  (`b`.`uniqueid` <> 'N/A'));

-- --------------------------------------------------------

--
-- Structure for view `reseller_cdrs`
--
DROP TABLE IF EXISTS `reseller_cdrs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reseller_cdrs` AS select `a`.`accountid` AS `accountid`,`a`.`cc` AS `cc`,`a`.`number` AS `number`,`b`.`id` AS `cdr_id`,`b`.`uniqueid` AS `uniqueid`,`b`.`callerid` AS `callerid`,`b`.`callednum` AS `callednum`,`b`.`billseconds` AS `billseconds`,`b`.`trunk` AS `trunk`,`b`.`disposition` AS `disposition`,`b`.`callstart` AS `callstart`,`b`.`debit` AS `debit`,`b`.`credit` AS `credit`,`b`.`status` AS `status`,`b`.`notes` AS `notes`,`b`.`provider` AS `provider`,`b`.`cost` AS `cost`,`b`.`pricelist` AS `pricelist`,(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`b`.`pattern`, '.', 1),'$',1),'^',-1)) AS `pattern`,`b`.`calltype` AS `calltype` from (`accounts` `a` join `cdrs` `b`) where ((`a`.`number` = `b`.`cardnum`) and (`a`.`type` = 1) and (`b`.`uniqueid` <> '') and (`b`.`uniqueid` <> '0') and (`b`.`uniqueid` <> '1') and  (`b`.`uniqueid` <> 'N/A'));
