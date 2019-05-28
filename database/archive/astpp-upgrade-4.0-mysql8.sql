

--
-- Table structure for table `accessnumber`
--

CREATE TABLE `accessnumber` (
  `id` int(4) NOT NULL,
  `access_number` varchar(25) DEFAULT NULL,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `description` varchar(1000) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active and 1 for inactive',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessnumber`
--
ALTER TABLE `accessnumber` ADD PRIMARY KEY (`id`);
ALTER TABLE `accessnumber` MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

ALTER TABLE `accounts` CHANGE `number` `number` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `accounts` ADD `non_cli_pricelist_id` INT NOT NULL DEFAULT '0' AFTER `pricelist_id`, ADD `reference` VARCHAR(100) NOT NULL AFTER `pricelist_id`, ADD `paypal_permission` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0:enable,1:disable' AFTER `pricelist_id`, ADD `cli_pool` INT(11) NOT NULL DEFAULT '0' AFTER `pricelist_id`;

ALTER TABLE `accounts` CHANGE `creation` `creation` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

/*ALTER TABLE `accounts` CHANGE `interval` `cps` INT(11) NOT NULL DEFAULT '0';*/

ALTER TABLE `accounts` ADD `invoice_interval` INT NOT NULL AFTER `invoice_day`, ADD `invoice_note` TEXT NOT NULL AFTER `invoice_interval`;

ALTER TABLE `accounts` ADD `permission_id` INT NOT NULL DEFAULT '0' AFTER `allow_ip_management`, ADD `deleted_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' AFTER `permission_id`, ADD `localization_id` INT NOT NULL DEFAULT '0' AFTER `deleted_date`, ADD `notifications` TINYINT NOT NULL DEFAULT '0' COMMENT '0:enable,1:disable' AFTER `localization_id`, ADD `is_distributor` TINYINT NOT NULL DEFAULT '1' COMMENT '0 for yes and 1 for No ' AFTER `notifications`;

--
-- Table structure for table `accounts_cdr_summary`
--

CREATE TABLE `accounts_cdr_summary` (
  `date_hour` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `country_id` int(11) NOT NULL,
  `account_entity_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `total_calls` int(11) NOT NULL,
  `answered_calls` smallint(6) NOT NULL,
  `minutes` smallint(6) NOT NULL,
  `debit` decimal(20,5) NOT NULL,
  `cost` decimal(20,5) NOT NULL,
  `acd` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts_cdr_summary`
--
ALTER TABLE `accounts_cdr_summary` ADD PRIMARY KEY (`date_hour`,`country_id`,`account_id`,`reseller_id`);

--
-- Table structure for table `account_unverified`
--

CREATE TABLE `account_unverified` (
  `id` int(11) NOT NULL,
  `number` varchar(20) CHARACTER SET utf8 NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `country_id` int(3) NOT NULL,
  `currency_id` int(3) NOT NULL,
  `timezone_id` int(3) NOT NULL,
  `otp` int(20) NOT NULL,
  `retries` int(3) NOT NULL,
  `client_ip` varchar(50) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for table `account_unverified`
--
ALTER TABLE `account_unverified` ADD PRIMARY KEY (`id`);
ALTER TABLE `account_unverified` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Table structure for table `addons`
--
DROP TABLE IF EXISTS `addons`;

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `package_name` varchar(30) NOT NULL,
  `version` varchar(10) NOT NULL,
  `installed_date` timestamp NULL DEFAULT NULL,
  `last_updated_date` timestamp NULL DEFAULT NULL,
  `files` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons` ADD PRIMARY KEY (`id`);
ALTER TABLE `addons` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ani_map` CHANGE `creation_date` `creation_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `ani_map` CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `ani_map` ADD `reseller_id` INT(11) NOT NULL DEFAULT '0' AFTER `accountid`;

--
-- Table structure for table `calltype`
--

CREATE TABLE `calltype` (
  `id` int(11) NOT NULL,
  `call_type` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calltype`
--
ALTER TABLE `calltype` ADD PRIMARY KEY (`id`);
ALTER TABLE `calltype` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Table structure for table `call_barring`
--

CREATE TABLE `call_barring` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `number` varchar(100) NOT NULL,
  `number_type` tinyint(1) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '0 Black List 1 White List',
  `destination` tinyint(1) DEFAULT '0',
  `action_type` tinyint(1) DEFAULT '0' COMMENT '0 Allow 1 Reject',
  `status` tinyint(1) DEFAULT '0' COMMENT '0 active 1 inactive',
  `creation_date` datetime DEFAULT '1000-01-01 00:00:00',
  `modified_date` datetime DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `call_barring`
--
ALTER TABLE `call_barring` ADD PRIMARY KEY (`id`);
ALTER TABLE `call_barring` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0 active 1 inactive',
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category` ADD PRIMARY KEY (`id`);
ALTER TABLE `category` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cdrs`  ADD `country_id` INT NOT NULL DEFAULT '0' AFTER `call_request`;

ALTER TABLE `cdrs` CHANGE `callstart` `callstart` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

DROP TABLE charges;
DROP TABLE charge_to_account;

--
-- Table structure for table `cli_group`
--

CREATE TABLE `cli_group` (
  `id` int(11) NOT NULL,
  `name` char(20) NOT NULL DEFAULT '0',
  `description` varchar(100) NOT NULL,
  `reseller_id` int(11) DEFAULT '0' COMMENT 'Accoun',
  `mapping_expired_by` char(5) NOT NULL,
  `mapping_expired_after` char(5) NOT NULL,
  `assignment_method` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_access_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cli_group`
--
ALTER TABLE `cli_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reseller` (`reseller_id`);

ALTER TABLE `cli_group` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `commission`
--

CREATE TABLE `commission` (
  `id` int(10) NOT NULL,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `accountid` int(10) NOT NULL,
  `reseller_id` int(10) NOT NULL DEFAULT '0',
  `payment_id` int(11) NOT NULL DEFAULT '0',
  `commission` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `commission_rate` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `commission_status` varchar(10) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commission`
--
ALTER TABLE `commission` ADD PRIMARY KEY (`id`);
ALTER TABLE `commission` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

/*--- ALTER TABLE `countrycode` ADD `currency_id` INT(11) NOT NULL AFTER `country`, ADD `iso` VARCHAR(5) NOT NULL AFTER `currency_id`, ADD `iso3` VARCHAR(5) NOT NULL AFTER `iso`, ADD `nicename` VARCHAR(80) NOT NULL AFTER `iso3`, ADD `countrycode` INT(5) NOT NULL AFTER `nicename`, ADD `capital` VARCHAR(20) NOT NULL AFTER `countrycode`, ADD `vat` DECIMAL(10,5) NOT NULL DEFAULT '0.00' AFTER `capital`, ADD `latitude` VARCHAR(20) NOT NULL AFTER `vat`, ADD `longitude` VARCHAR(20) NOT NULL AFTER `latitude`;

--- ALTER TABLE `countrycode` ADD UNIQUE(`iso`);
*/
--
-- Table structure for table `countrycode`
--

DROP TABLE IF EXISTS `countrycode`;

CREATE TABLE `countrycode` (
  `id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `iso` char(2) NOT NULL,
  `country` varchar(80) NOT NULL,
  `nicename` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `countrycode` int(5) NOT NULL,
  `vat` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `latitude` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `capital` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countrycode`
--

INSERT INTO `countrycode` (`id`, `currency_id`, `iso`, `country`, `nicename`, `iso3`, `countrycode`, `vat`, `latitude`, `longitude`, `capital`) VALUES
(1, 5, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 93, '0.00000', '33.9391', '67.7100', 'Kabul'),
(2, 0, 'AL', 'ALBANIA', 'Albania', 'ALB', 355, '0.00000', '41.153332', ' 	20.168331', 'Tirana'),
(3, 0, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 213, '0.00000', '28.0339', '1.6596', 'Algiers'),
(4, 0, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 1684, '0.00000', '-14.270972', '-170.132217', 'Pago Pago'),
(5, 0, 'AD', 'ANDORRA', 'Andorra', 'AND', 376, '0.00000', ' 	42.546245', '1.601554', 'Andorra la Vella'),
(6, 0, 'AO', 'ANGOLA', 'Angola', 'AGO', 244, '0.00000', '-11.202692', ' 	17.873887', 'Luanda'),
(7, 0, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 1264, '0.00000', '18.220554', ' 	-63.068615', ''),
(8, 0, 'AG', 'ANTIGUA & BARBUDA', 'Antigua_&_Barbuda', 'ATG', 1268, '0.00000', '17.060816', ' 	-61.796428', 'Saint John\'s'),
(9, 0, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 54, '0.00000', '-38.416097', '-63.616672', 'Buenos Aires'),
(10, 0, 'AM', 'ARMENIA', 'Armenia', 'ARM', 374, '0.00000', ' 	40.069099', '45.038189', 'Yerevan'),
(11, 0, 'AW', 'ARUBA', 'Aruba', 'ABW', 297, '0.00000', '12.52111', ' 	-69.968338', ''),
(12, 0, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 61, '0.00000', '-25.274398', ' 	133.775136', 'Canberra'),
(13, 0, 'AT', 'AUSTRIA', 'Austria', 'AUT', 43, '20.00000', '47.516231', ' 	14.550072', 'Vienna'),
(14, 0, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 994, '0.00000', '40.143105', '47.576927', 'Baku'),
(15, 0, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 1242, '0.00000', '25.03343', '77.3963', 'Nassau'),
(16, 0, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 973, '0.00000', '25.930414', '50.637772', 'Manama'),
(17, 0, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 880, '0.00000', '23.684994', '90.356331', 'Dhaka'),
(18, 0, 'BB', 'BARBADOS', 'Barbados', 'BRB', 1246, '0.00000', '13.193887', '-59.543198', 'Bridgetown'),
(19, 0, 'BY', 'BELARUS', 'Belarus', 'BLR', 375, '0.00000', '53.709807', '27.953389', 'Minsk'),
(20, 0, 'BE', 'BELGIUM', 'Belgium', 'BEL', 32, '21.00000', '50.503887', '4.469936', 'Brussels'),
(21, 0, 'BZ', 'BELIZE', 'Belize', 'BLZ', 501, '0.00000', '17.189877', '-88.49765', 'Belmopan'),
(22, 0, 'BJ', 'BENIN', 'Benin', 'BEN', 229, '0.00000', '9.30769', '2.315834', 'Porto-Novo'),
(23, 0, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 1441, '0.00000', '32.3078', '64.7505', ''),
(24, 0, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 975, '0.00000', '27.514162', '90.433601', 'Thimphu'),
(25, 0, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 591, '0.00000', '-16.290154', '-63.588653', 'La Paz'),
(26, 0, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia_and_Herzegovina', 'BIH', 387, '0.00000', '43.915886', '17.679076', 'Sarajevo'),
(27, 0, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 267, '0.00000', '-22.328474', '24.684866', 'Gaborone'),
(28, 0, 'BR', 'BRAZIL', 'Brazil', 'BRA', 55, '0.00000', '-14.235004', '-51.92528', 'Brasilia'),
(29, 0, 'VG', 'BRITISH VIRGIN ISLANDS', 'British Virgin Islands', 'VGB', 1284, '0.00000', '18.4207', '64.6400', ''),
(30, 0, 'BN', 'BRUNEI', 'Brunei', 'BRN', 673, '0.00000', '4.535277', '114.727669', 'Bandar Seri Begawan'),
(31, 0, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 359, '20.00000', '42.733883', '25.48583', 'Sofia'),
(32, 0, 'BF', 'BURKINA FASO', 'Burkina_Faso', 'BFA', 226, '0.00000', '12.238333', '-1.561593', 'Ouagadougou'),
(33, 0, 'BI', 'BURUNDI', 'Burundi', 'BDI', 257, '0.00000', '-3.373056', '29.918886', 'Bujumbura'),
(34, 0, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 855, '0.00000', '12.5657', '104.9910', 'Phnom Penh'),
(35, 0, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 237, '0.00000', '7.369722', '12.354722', 'Yaounde'),
(36, 0, 'CA', 'CANADA', 'Canada', 'CAN', 1, '0.00000', '56.130366', '-106.346771', 'Ottawa'),
(37, 0, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 238, '0.00000', '16.002082', '-24.013197', 'Praia'),
(38, 0, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 1345, '0.00000', '19.513469', '-80.566956', ''),
(39, 0, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 236, '0.00000', '6.611111', '20.939444', 'Bangui'),
(40, 0, 'TD', 'CHAD', 'Chad', 'TCD', 235, '0.00000', '15.4542', ' 18.7322', 'N\'Djamena'),
(41, 0, 'CL', 'CHILE', 'Chile', 'CHL', 56, '0.00000', '-35.675147', '-71.542969', 'Santiago'),
(42, 0, 'CN', 'CHINA', 'China', 'CHN', 86, '0.00000', '35.86166', '104.195397', 'Beijing'),
(43, 0, 'CO', 'COLOMBIA', 'Colombia', 'COL', 57, '0.00000', '4.570868', '-74.297333', 'Bogota'),
(44, 0, 'KM', 'COMOROS', 'Comoros', 'COM', 269, '0.00000', '-11.875001', '43.872219', 'Moroni'),
(45, 0, 'CG', 'CONGO', 'Congo', 'COG', 242, '0.00000', '-0.228021', '15.827659', ''),
(46, 0, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 506, '0.00000', '9.748917', '-83.753428', 'San Jose'),
(47, 0, 'HR', 'CROATIA', 'Croatia', 'HRV', 385, '25.00000', '45.1', '15.2', 'Zagreb'),
(48, 0, 'CU', 'CUBA', 'Cuba', 'CUB', 53, '0.00000', '21.521757', '-77.781167', 'Havana'),
(49, 0, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 357, '19.00000', '35.126413', '33.429859', 'Nicosia'),
(50, 0, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 420, '21.00000', '49.817492', '15.472962', 'Prague'),
(51, 0, 'CD', 'DEMOCRATIC REPUBLIC', 'Democratic Republic', 'COD', 243, '0.00000', '4.0383', '21.7587', ''),
(52, 0, 'DK', 'DENMARK', 'Denmark', 'DNK', 45, '25.00000', '56.26392', '9.501785', 'Copenhagen'),
(53, 0, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 253, '0.00000', '11.825138', '42.590275', 'Djibouti'),
(54, 0, 'DM', 'DOMINICA', 'Dominica', 'DMA', 1767, '0.00000', '15.414999', '-61.370976', 'Roseau'),
(55, 0, 'DO', 'DOMINICAN REPUBLIC', 'Dominican republic', 'DOM', 1809, '0.00000', '18.735693', '-70.162651', 'Santo Domingo'),
(56, 0, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 593, '0.00000', '-1.831239', '-78.183406', 'Quito'),
(57, 0, 'EG', 'EGYPT', 'Egypt', 'EGY', 20, '0.00000', '26.820553', '30.802498', 'Cairo'),
(58, 0, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 503, '0.00000', '13.794185', '-88.89653', 'San Salvador'),
(59, 0, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 240, '0.00000', '1.650801', '10.267895', 'Malabo'),
(60, 0, 'ER', 'ERITREA', 'Eritrea', 'ERI', 291, '0.00000', '15.179384', '39.782334', 'Asmara'),
(61, 0, 'EE', 'ESTONIA', 'Estonia', 'EST', 372, '20.00000', '58.595272', '25.013607', 'Tallinn'),
(62, 0, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 251, '0.00000', '9.145', '40.489673', 'Addis Ababa'),
(63, 0, 'FO', 'FAEROE ISLANDS', 'Faeroe Islands', 'FRO', 298, '0.00000', '61.892635', '-6.911806', ''),
(64, 0, 'FJ', 'FIJI ISLANDS', 'Fiji Islands', 'FJI', 67970, '0.00000', '-16.578193', '179.414413', ''),
(65, 0, 'FI', 'FINLAND', 'Finland', 'FIN', 358, '24.00000', '61.92411', '25.748151', 'Helsinki'),
(66, 0, 'FR', 'FRANCE', 'France', 'FRA', 33, '20.00000', '46.227638', '2.213749', 'Paris'),
(67, 0, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 594, '0.00000', '3.933889', '-53.125782', ''),
(68, 0, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 689, '0.00000', '-17.679742', '-149.406843', ''),
(69, 0, 'GA', 'GABON', 'Gabon', 'GAB', 241, '0.00000', '-0.803689', '11.609444', 'Libreville'),
(70, 0, 'GM', 'GAMBIA', 'Gambia', 'GMB', 220, '0.00000', '13.443182', '-15.310139', ''),
(71, 0, 'GE', 'GEORGIA', 'Georgia', 'GEO', 995, '0.00000', '42.315407', '43.356892', 'Tbilisi'),
(72, 0, 'DE', 'GERMANY', 'Germany', 'DEU', 49, '19.00000', '51.165691', '10.451526', 'Berlin'),
(73, 0, 'GH', 'GHANA', 'Ghana', 'GHA', 233, '0.00000', '7.946527', '7.946527', 'Accra'),
(74, 0, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 350, '0.00000', '36.137741', '-5.345374', ''),
(75, 0, 'GR', 'GREECE', 'Greece', 'GRC', 30, '23.00000', '39.074208', '21.824312', 'Athens'),
(76, 0, 'GD', 'GRENADA', 'Grenada', 'GRD', 1473, '0.00000', '12.262776', '-61.604171', 'Saint George\'s'),
(77, 0, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 590, '0.00000', '16.995971', '-62.067641', ''),
(78, 0, 'GU', 'GUAM', 'Guam', 'GUM', 1671, '0.00000', '13.444304', '144.793731', ''),
(79, 0, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 502, '0.00000', '15.783471', '-90.230759', 'Guatemala City'),
(80, 0, 'GN', 'GUINEA', 'Guinea', 'GIN', 224, '0.00000', '9.945587', '-9.696645', 'Conakry'),
(81, 0, 'GW', 'GUINEA BISSAU', 'Guinea Bissau', 'GNB', 245, '0.00000', '11.803749', '-15.180413', ''),
(82, 0, 'GY', 'GUYANA', 'Guyana', 'GUY', 592, '0.00000', '4.860416', '-58.93018', 'Georgetown'),
(83, 0, 'HT', 'HAITI', 'Haiti', 'HTI', 509, '0.00000', '18.971187', '-72.285215', 'Port-au-Prince'),
(84, 0, 'HN', 'HONDURAS', 'Honduras', 'HND', 504, '0.00000', '15.199999', '-86.241905', 'Tegucigalpa'),
(85, 0, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 852, '0.00000', '22.396428', '114.109497', ''),
(86, 0, 'HU', 'HUNGARY', 'Hungary', 'HUN', 36, '27.00000', '47.162494', '19.503304', 'Budapest'),
(87, 0, 'IS', 'ICELAND', 'Iceland', 'ISL', 354, '0.00000', '64.963051', '-19.020835', 'Reykjavik'),
(88, 0, 'IN', 'INDIA', 'India', 'IND', 91, '0.00000', '20.5937', '78.9629', 'New Delhi'),
(89, 0, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 62, '0.00000', '-0.789275', '113.921327', 'Jakarta'),
(90, 0, 'IR', 'IRAN', 'Iran', 'IRN', 98, '0.00000', '32.427908', '53.688046', 'Tehran'),
(91, 0, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 964, '0.00000', '33.223191', '43.679291', 'Baghdad'),
(92, 0, 'IE', 'IRELAND', 'Ireland', 'IRL', 353, '23.00000', '53.41291', '-8.24389', 'Dublin'),
(93, 0, 'IL', 'ISRAEL', 'Israel', 'ISR', 972, '0.00000', '31.046051', '34.851612', 'Jerusalem'),
(94, 0, 'IT', 'ITALY', 'Italy', 'ITA', 39, '22.00000', '41.87194', '12.56738', 'Rome'),
(95, 0, 'CI', 'IVORY COAST', 'Ivory Coast', 'CIV', 225, '0.00000', '7.5400', '5.5471', ''),
(96, 0, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 1876, '0.00000', '18.109581', '-77.297508', 'Kingston'),
(97, 0, 'JP', 'JAPAN', 'Japan', 'JPN', 81, '0.00000', '36.204824', '138.252924', 'Tokyo'),
(98, 0, 'JO', 'JORDAN', 'Jordan', 'JOR', 962, '0.00000', '30.585164', '36.238414', 'Amman'),
(99, 0, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 7, '0.00000', '48.019573', '66.923684', 'Astana'),
(100, 0, 'KE', 'KENYA', 'Kenya', 'KEN', 254, '0.00000', '-0.023559', '37.906193', 'Nairobi'),
(101, 0, 'KS', 'KOSOVO', 'Kosovo', 'KSV', 38128, '0.00000', '42.602636', '20.902977', 'Pristina'),
(102, 0, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 965, '0.00000', '29.31166', '47.481766', 'Kuwait City'),
(103, 0, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 996, '0.00000', '41.20438', '74.766098', 'Bishkek'),
(104, 0, 'LA', 'LAOS', 'Laos', 'LAO', 856, '0.00000', '19.85627', '102.495496', 'Vientiane'),
(105, 0, 'LV', 'LATVIA', 'Latvia', 'LVA', 371, '21.00000', '56.879635', '24.603189', 'Riga'),
(106, 0, 'LB', 'LEBANON', 'Lebanon', 'LBN', 961, '0.00000', '33.854721', '33.854721', 'Beirut'),
(107, 0, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 266, '0.00000', '-29.609988', '28.233608', 'Maseru'),
(108, 0, 'LR', 'LIBERIA', 'Liberia', 'LBR', 231, '0.00000', '6.428055', '-9.429499', 'Monrovia'),
(109, 0, 'LY', 'LIBYA', 'Libya', 'LBY', 218, '0.00000', '26.3351', '17.228331', 'Tripoli'),
(110, 0, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 423, '0.00000', '47.166', '9.555373', 'Vaduz'),
(111, 0, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 370, '21.00000', '55.169438', '23.881275', 'Vilnius'),
(112, 0, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 352, '17.00000', '49.815273', '6.129583', 'Luxembourg'),
(113, 0, 'MO', 'MACAU', 'Macau', 'MAC', 853, '0.00000', '22.198745', '113.543873', ''),
(114, 0, 'MK', 'MACEDONIA', 'Macedonia', 'MKD', 389, '0.00000', '41.608635', '21.745275', 'Skopje'),
(115, 0, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 261, '0.00000', '-18.766947', '46.869107', 'Antananarivo'),
(116, 0, 'MW', 'MALAWI', 'Malawi', 'MWI', 265, '0.00000', '-13.254308', '34.301525', 'Lilongwe'),
(117, 0, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 60, '0.00000', '4.210484', '101.975766', 'Kuala Lumpur'),
(118, 0, 'ML', 'MALI', 'Mali', 'MLI', 223, '0.00000', '17.570692', '-3.996166', 'Bamako'),
(119, 0, 'MT', 'MALTA', 'Malta', 'MLT', 356, '18.00000', '35.937496', '14.375416', 'Valletta'),
(120, 0, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 692, '0.00000', '7.131474', '171.184478', 'Majuro'),
(121, 0, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 596, '0.00000', '14.641528', '-61.024174', ''),
(122, 0, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 222, '0.00000', '21.00789', '-10.940835', 'Nouakchott'),
(123, 0, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 230, '0.00000', '-20.348404', '57.552152', 'Port Louis'),
(124, 0, 'MX', 'MEXICO', 'Mexico', 'MEX', 52, '0.00000', '23.634501', '-102.552784', 'Mexico City'),
(125, 0, 'FM', 'MICRONESIA', 'Micronesia', 'FSM', 691, '0.00000', '7.4256', '150.5508', ''),
(126, 0, 'MD', 'MOLDOVA', 'Moldova', 'MDA', 373, '0.00000', '47.411631', '28.369885', 'Chisinau'),
(127, 0, 'MC', 'MONACO', 'Monaco', 'MCO', 377, '0.00000', '43.750298', '7.412841', 'Monaco'),
(128, 0, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 976, '0.00000', '46.862496', '103.846656', 'Ulaanbaatar'),
(129, 0, 'ME', 'MONTENEGRO', 'Montenegro', 'MNE', 382, '0.00000', '42.708678', '19.37439', 'Podgorica'),
(130, 0, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 1664, '0.00000', '16.742498', '-62.187366', ''),
(131, 0, 'MA', 'MOROCCO', 'Morocco', 'MAR', 212, '0.00000', '31.791702', '-7.09262', 'Rabat'),
(132, 0, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 258, '0.00000', '-18.665695', '35.529562', 'Maputo'),
(133, 0, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 95, '0.00000', '21.913965', '95.956223', 'Rangoon'),
(134, 0, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 264, '0.00000', '-22.95764', '18.49041', 'Windhoek'),
(135, 0, 'NP', 'NEPAL', 'Nepal', 'NPL', 977, '0.00000', '28.394857', '84.124008', 'Kathmandu'),
(136, 0, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 31, '21.00000', '52.132633', '5.291266', 'Amsterdam'),
(137, 0, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 687, '0.00000', '-20.904305', '165.618042', ''),
(138, 0, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 64, '0.00000', '-40.900557', '174.885971', 'Wellington'),
(139, 0, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 505, '0.00000', '12.865416', '-85.207229', 'Managua'),
(140, 0, 'NE', 'NIGER', 'Niger', 'NER', 227, '0.00000', '17.607789', '8.081666', 'Niamey'),
(141, 0, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 234, '0.00000', '9.081999', '8.675277', 'Abuja'),
(142, 0, 'MP', 'NO. MARIANA ISLANDS', 'No. Mariana Islands', 'MNP', 1670, '0.00000', '17.33083', '145.38469', ''),
(143, 0, 'KP', 'NORTH KOREA', 'North Korea', 'PRK', 850, '0.00000', '40.339852', '127.510093', ''),
(144, 0, 'NO', 'NORWAY', 'Norway', 'NOR', 47, '0.00000', '60.472024', '8.468946', 'Oslo'),
(145, 0, 'OM', 'OMAN', 'Oman', 'OMN', 968, '0.00000', '21.512583', '55.923255', 'Muscat'),
(146, 0, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 92, '0.00000', '30.375321', '69.345116', 'Islamabad'),
(147, 0, 'PW', 'PALAU', 'Palau', 'PLW', 680, '0.00000', '7.51498', '134.58252', 'Melekeok'),
(148, 0, 'PS', 'PALESTINIAN AUTHORITY', 'Palestinian Authority', 'PSE', 970, '0.00000', '31.952162', '35.233154', ''),
(149, 0, 'PA', 'PANAMA', 'Panama', 'PAN', 507, '0.00000', '8.537981', '-80.782127', 'Panama City'),
(150, 0, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 595, '0.00000', '-23.442503', '-58.443832', 'Asuncion'),
(151, 0, 'PE', 'PERU', 'Peru', 'PER', 51, '0.00000', '-9.189967', '-75.015152', 'Lima'),
(152, 0, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 63, '0.00000', '12.879721', '121.774017', 'Manila'),
(153, 0, 'PL', 'POLAND', 'Poland', 'POL', 48, '23.00000', '51.919438', '19.145136', 'Warsaw'),
(154, 0, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 351, '23.00000', '39.399872', '-8.224454', 'Lisbon'),
(155, 0, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 1787, '0.00000', '18.220833', '-66.590149', ''),
(156, 0, 'QA', 'QATAR', 'Qatar', 'QAT', 974, '0.00000', '25.354826', '51.183884', 'Doha'),
(157, 0, 'RE', 'REUNION ISLAND', 'Reunion Island', 'REU', 262, '0.00000', '-21.115141', '55.536384', ''),
(158, 0, 'RO', 'ROMANIA', 'Romania', 'ROM', 40, '20.00000', '45.943161', '24.96676', 'Bucharest'),
(159, 0, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 70, '0.00000', '61.52401', '105.318756', ''),
(160, 0, 'RW', 'RWANDA', 'Rwanda', 'RWA', 250, '0.00000', '-1.940278', '29.873888', 'Kigali'),
(161, 0, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 378, '0.00000', '43.94236', '12.457777', 'San Marino'),
(162, 0, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 966, '0.00000', '23.885942', '45.079162', 'Riyadh'),
(163, 0, 'SN', 'SENEGAL', 'Senegal', 'SEN', 221, '0.00000', '14.497401', '-14.452362', 'Dakar'),
(164, 0, 'RS', 'SERBIA', 'Serbia', 'SRB', 381, '0.00000', '44.016521', '21.005859', 'Belgrade'),
(165, 0, 'SC', 'SEYCHELLES ISLANDS', 'Seychelles Islands', 'SYC', 248, '0.00000', '-4.679574', '55.491977', ''),
(166, 0, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 232, '0.00000', '8.460555', '-11.779889', 'Freetown'),
(167, 0, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 65, '0.00000', '1.352083', '103.819836', 'Singapore'),
(168, 0, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 421, '20.00000', '48.669026', '19.699024', 'Bratislava'),
(169, 0, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 386, '22.00000', '46.151241', '14.995463', 'Ljubljana'),
(170, 0, 'SO', 'SOMALIA', 'Somalia', 'SOM', 252, '0.00000', '5.152149', '46.199616', 'Mogadishu'),
(171, 0, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 27, '0.00000', '-30.559482', '22.937506', 'Cape Town'),
(172, 0, 'KR', 'SOUTH KOREA', 'South Korea', 'KOR', 82, '0.00000', '35.907757', '127.766922', 'Seoul'),
(173, 0, 'SS', 'SOUTH SUDAN', 'South Sudan', 'SSD', 211, '0.00000', '6.8770', '31.3070', 'Juba'),
(174, 0, 'ES', 'SPAIN', 'Spain', 'ESP', 34, '21.00000', '40.463667', '-3.74922', 'Madrid'),
(175, 0, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 94, '0.00000', '7.873054', '80.771797', 'Colombo'),
(176, 0, 'KN', 'ST. KITTS', 'St. Kitts', 'KNA', 1869, '0.00000', '17.3578', '62.7830', 'Basseterre'),
(177, 0, 'LC', 'ST. LUCIA', 'St. Lucia', 'LCA', 1758, '0.00000', '13.9094', '60.9789', 'Castries'),
(178, 0, 'MF', 'ST. MARTIN', 'St. Martin', 'MAF', 1721, '0.00000', '18.0708', '63.0501', ''),
(179, 0, 'PM', 'ST. PIERRE & MIQUELON', 'St. Pierre & Miquelon', 'SPM', 508, '0.00000', '46.8852', '56.3159', ''),
(180, 0, 'VC', 'ST. VINCENT', 'St. Vincent', 'VCT', 1784, '0.00000', '12.9843', '61.2872', 'Kingstown'),
(181, 0, 'SD', 'SUDAN', 'Sudan', 'SDN', 249, '0.00000', '12.862807', '30.217636', 'Khartoum'),
(182, 0, 'SR', 'SURINAME', 'Suriname', 'SUR', 597, '0.00000', '3.919305', '-56.027783', 'Paramaribo'),
(183, 0, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 268, '0.00000', '-26.522503', '31.465866', 'Mbabane'),
(184, 0, 'SE', 'SWEDEN', 'Sweden', 'SWE', 46, '25.00000', '60.128161', '18.643501', 'Stockholm'),
(185, 0, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 41, '0.00000', '46.818188', '8.227512', 'Bern'),
(186, 0, 'SY', 'SYRIA', 'Syria', 'SYR', 963, '0.00000', '34.802075', '38.996815', 'Damascus'),
(187, 0, 'TW', 'TAIWAN', 'Taiwan', 'TWN', 886, '0.00000', '23.69781', '120.960515', 'Taipei'),
(188, 0, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 992, '0.00000', '38.861034', '71.276093', 'Dushanbe'),
(189, 0, 'TZ', 'TANZANIA', 'Tanzania', 'TZA', 255, '0.00000', '-6.369028', '34.888822', 'Dar es Salaam'),
(190, 0, 'TH', 'THAILAND', 'Thailand', 'THA', 66, '0.00000', '15.870032', '100.992541', 'Bangkok'),
(191, 0, 'TG', 'TOGO', 'Togo', 'TGO', 228, '0.00000', '8.619543', '0.824782', 'Lome'),
(192, 0, 'TT', 'TRINIDAD & TOBAGO', 'Trinidad & Tobago', 'TTO', 1868, '0.00000', '10.691803', '-61.222503', 'Port-of-Spain'),
(193, 0, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 216, '0.00000', '33.886917', '9.537499', 'Tunis'),
(194, 0, 'TR', 'TURKEY', 'Turkey', 'TUR', 90, '0.00000', '38.963745', '35.243322', 'Ankara'),
(195, 0, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 7370, '0.00000', '38.969719', '59.556278', 'Ashgabat'),
(196, 0, 'TC', 'TURKS & CAICOS ISLANDS', 'Turks & Caicos Islands', 'TCA', 1649, '0.00000', '21.694025', '-71.797928', ''),
(197, 0, 'UG', 'UGANDA', 'Uganda', 'UGA', 256, '0.00000', '1.373333', '32.290275', 'Kampala'),
(198, 0, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 380, '0.00000', '48.379433', '31.16558', 'Kyiv'),
(199, 0, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 971, '0.00000', '23.424076', '53.847818', 'Abu Dhabi'),
(200, 0, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 44, '20.00000', '55.378051', '-3.435973', 'London'),
(201, 0, 'UY', 'URUGUAY', 'Uruguay', 'URY', 598, '0.00000', '-32.522779', '-55.765835', 'Montevideo'),
(202, 0, 'VI', 'US VIRGIN ISLANDS', 'Us Virgin Islands', 'VIR', 1340, '0.00000', '18.335765', '-64.896335', ''),
(203, 0, 'US', 'USA', 'Usa', 'USA', 1, '0.00000', '37.09024', '-95.712891', ''),
(204, 0, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 998, '0.00000', '41.377491', '64.585262', 'Tashkent'),
(205, 0, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 58, '0.00000', '6.42375', '-66.58973', 'Caracas'),
(206, 0, 'VN', 'VIETNAM', 'Vietnam', 'VNM', 84, '0.00000', '14.058324', '108.277199', 'Hanoi'),
(207, 0, 'YE', 'YEMEN', 'Yemen', 'YEM', 967, '0.00000', '15.552727', '48.516388', 'Sanaa'),
(208, 0, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 260, '0.00000', '-13.133897', '27.849332', 'Lusaka'),
(209, 0, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 263, '0.00000', '-19.015438', '29.154857', 'Harare'),
(210, 0, 'AQ', 'ANTARCTICA', 'Antarctica', 'ATA', 672, '0.00000', '', '', ''),
(211, 0, 'A', 'ASCENSION', 'Ascension', 'ASC', 247, '0.00000', '', '', ''),
(212, 0, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 682, '0.00000', '', '', ''),
(213, 0, 'TL', 'EAST TIMOR', 'EastTimor', 'TLS', 670, '0.00000', '', '', ''),
(214, 0, 'FK', 'FALKLAND ISLANDS', 'FalklandIslands', 'FLK', 500, '0.00000', '', '', ''),
(215, 0, 'GL', 'GREENLAND', 'GreenLand', 'GRL', 299, '0.00000', '', '', ''),
(216, 0, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 686, '0.00000', '', '', ''),
(217, 0, 'MV', 'MALDIVES', 'Maldives', 'MDV', 960, '0.00000', '', '', ''),
(218, 0, 'NR', 'NAURU', 'Nauru', 'NRU', 674, '0.00000', '', '', ''),
(219, 0, 'NU', 'NIUE', 'Niue', 'NIU', 683, '0.00000', '', '', ''),
(220, 0, '44', 'hgfh', 'fdg', 'f44', 44, '114.00000', '141', '141', 'ffd'),
(221, 1, '5', 'oi', 'iiiiiio', '54', 45, '11.22000', '44.22', '44.77', 'piop');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countrycode`
--
ALTER TABLE `countrycode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_key` (`iso`);

ALTER TABLE `countrycode` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Table structure for table `cron_settings`
--

CREATE TABLE `cron_settings` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `command` varchar(50) NOT NULL,
  `exec_interval` int(11) NOT NULL DEFAULT '1',
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_execution_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `next_execution_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `file_path` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cron_settings`
--
ALTER TABLE `cron_settings` ADD PRIMARY KEY (`id`);
ALTER TABLE `cron_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `default_templates` ADD `sms_template` VARCHAR(500) NOT NULL AFTER `template`, ADD `alert_template` VARCHAR(500) NOT NULL AFTER `sms_template`, ADD `description` VARCHAR(200) NOT NULL AFTER `alert_template`, ADD `is_email_enable` TINYINT NOT NULL DEFAULT '0' AFTER `description`, ADD `is_sms_enable` TINYINT NOT NULL DEFAULT '0' AFTER `is_email_enable`, ADD `is_alert_enable` TINYINT NOT NULL DEFAULT '0' AFTER `is_sms_enable`, ADD `status` TINYINT NOT NULL DEFAULT '0' AFTER `is_alert_enable`;

ALTER TABLE `default_templates` CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `dids` DROP `prorate`;

ALTER TABLE `dids` DROP `limittime`, DROP `disconnectionfee`, DROP `variables`, DROP `options`;

ALTER TABLE `dids` DROP `chargeonallocation`, DROP `allocation_bill_status`, DROP `dial_as`, DROP `inuse`, DROP `assign_date`, DROP `charge_upto`;

ALTER TABLE `dids` ADD `product_id` INT NOT NULL  AFTER `leg_timeout`,  ADD `always` INT(10) NOT NULL  AFTER `product_id`,  ADD `always_destination` VARCHAR(50) NOT NULL  AFTER `always`,  ADD `user_busy` INT(10) NOT NULL  AFTER `always_destination`,  ADD `user_busy_destination` VARCHAR(50) NOT NULL  AFTER `user_busy`,  ADD `user_not_registered` INT(10) NOT NULL  AFTER `user_busy_destination`,  ADD `user_not_registered_destination` VARCHAR(50) NOT NULL  AFTER `user_not_registered`,  ADD `no_answer` INT(10) NOT NULL  AFTER `user_not_registered_destination`,  ADD `no_answer_destination` VARCHAR(50) NOT NULL  AFTER `no_answer`,  ADD `failover_extensions` VARCHAR(180) NOT NULL  AFTER `no_answer_destination`,  ADD `failover_call_type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 enable 1 for disable'  AFTER `failover_extensions`,  ADD `always_vm_flag` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 enable 1 for disable'  AFTER `failover_call_type`,  ADD `user_busy_vm_flag` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 enable 1 for disable'  AFTER `always_vm_flag`,  ADD `user_not_registered_vm_flag` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 enable 1 for disable'  AFTER `user_busy_vm_flag`,  ADD `no_answer_vm_flag` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 enable 1 for disable'  AFTER `user_not_registered_vm_flag`;

ALTER TABLE `freeswich_servers` CHANGE `creation_date` `creation_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';
ALTER TABLE `freeswich_servers` CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `gateways` CHANGE `created_date` `created_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00', CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `invoices` CHANGE `invoice_prefix` `prefix` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `invoiceid` `number` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `invoice_date` `generate_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00 ', CHANGE `deleted` `is_deleted` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0:Not delete 1:delete';

ALTER TABLE `invoices` DROP `amount`, DROP `balance`, DROP `saving_report`, DROP `invoice_note`;

ALTER TABLE `invoice_conf` ADD `reseller_id` INT NOT NULL DEFAULT '0' AFTER `website_footer`, ADD `invoice_note` TEXT NOT NULL AFTER `favicon`;

ALTER TABLE `invoice_details` CHANGE `item_id` `order_item_id` INT(11) NOT NULL DEFAULT '0', CHANGE `item_type` `charge_type` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `invoice_details` ADD `payment_id` INT(11) NOT NULL DEFAULT '0' AFTER `charge_type`, ADD `product_category` INT(11) NOT NULL DEFAULT '0' AFTER `payment_id`, ADD `is_tax` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0 FOR NO AND 1 FOR YES' AFTER `product_category`, ADD `base_currency` VARCHAR(5) NOT NULL AFTER `is_tax`, ADD `exchange_rate` DECIMAL(20,6) NOT NULL DEFAULT '0.0000' AFTER `base_currency`, ADD `account_currency` VARCHAR(5) NOT NULL AFTER `exchange_rate`;

ALTER TABLE `ip_map` ADD `reseller_id` INT(11) NOT NULL DEFAULT '0' AFTER `accountid`;

ALTER TABLE `ip_map` CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `locale` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0->yes/active,1->no/inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `code`, `name`, `locale`, `status`) VALUES
(1, 'eng', 'English', 'en_En', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locale` (`locale`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


--
-- Table structure for table `localization`
--

CREATE TABLE `localization` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `account_id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `country_id` int(11) NOT NULL,
  `in_caller_id_originate` varchar(200) NOT NULL,
  `out_caller_id_originate` varchar(200) NOT NULL,
  `number_originate` varchar(200) NOT NULL,
  `in_caller_id_terminate` varchar(200) NOT NULL,
  `out_caller_id_terminate` varchar(200) NOT NULL,
  `number_terminate` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime DEFAULT '1000-01-01 00:00:00',
  `modified_date` datetime DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `localization`
--
ALTER TABLE `localization` ADD PRIMARY KEY (`id`);
ALTER TABLE `localization` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `mail_details` ADD `to_number` INT(11) NOT NULL AFTER `reseller_id`, ADD `sms_body` VARCHAR(500) NOT NULL AFTER `to_number`;

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `parent_order_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `order_generated_by` int(11) NOT NULL,
  `payment_gateway` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders` ADD PRIMARY KEY (`id`);
ALTER TABLE `orders` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_category` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `price` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `setup_fee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `billing_type` int(5) NOT NULL,
  `billing_days` int(11) NOT NULL DEFAULT '0',
  `free_minutes` int(11) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `billing_date` datetime NOT NULL,
  `next_billing_date` datetime NOT NULL,
  `is_terminated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 FOR NO AND 1 FOR YES',
  `termination_date` datetime NOT NULL,
  `termination_note` varchar(255) NOT NULL,
  `from_currency` varchar(3) NOT NULL,
  `exchange_rate` decimal(10,5) NOT NULL DEFAULT '1.00000',
  `to_currency` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items` ADD PRIMARY KEY (`id`);
ALTER TABLE `order_items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `outbound_routes` CHANGE `creation_date` `creation_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00', CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

DROP TABLE packages;

ALTER TABLE `package_patterns` CHANGE `package_id` `product_id` INT(4) NOT NULL;

ALTER TABLE `package_patterns` ADD `country_id` INT(11) NOT NULL DEFAULT '0' AFTER `product_id`;

DROP TABLE payments;

ALTER TABLE `payment_transaction` CHANGE `paypal_fee` `payment_fee` DECIMAL(20,5) NOT NULL DEFAULT '0.00000';

ALTER TABLE `payment_transaction` ADD `reseller_id` INT(11) NOT NULL DEFAULT '0' AFTER `payment_fee`, ADD `transaction_id` VARCHAR(50) NOT NULL AFTER `reseller_id`, ADD `customer_ip` VARCHAR(100) NOT NULL AFTER `transaction_id`;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `login_type` tinyint(1) NOT NULL DEFAULT '0',
  `permissions` text NOT NULL,
  `edit_permissions` longtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modification_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `reseller_id`, `description`, `login_type`, `permissions`, `edit_permissions`, `creation_date`, `modification_date`) VALUES
(1, 'Admin_permission', 0, 'Permissions', 0, '{\"accounts\":{\"customer_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"mass_create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"callerid\":\"0\",\"payment\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"reseller_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"payment\":\"0\"},\"admin_list\":{\"main\":\"0\",\"list\":\"0\",\"create_admin\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"freeswitch\":{\"fssipdevices\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"search\":\"0\"},\"fssipprofile\":{\"main\":\"0\"},\"livecall_report\":{\"main\":\"0\"}},\"ipmap\":{\"ipmap_detail\":{\"main\":\"0\"}},\"animap\":{\"animap_detail\":{\"main\":\"0\"}},\"permissions\":{\"permissions_list\":{\"main\":\"0\",\"list\":\"0\",\"edit\":\"0\"}},\"invoices\":{\"invoice_list\":{\"main\":\"0\",\"list\":\"0\",\"download\":\"0\",\"edit\":\"0\",\"generate\":\"0\",\"search\":\"0\",\"delete\":\"0\"},\"invoice_conf_list\":{\"main\":\"0\"}},\"reports\":{\"commission_report_list\":{\"main\":\"0\"},\"customerReport\":{\"main\":\"0\"},\"resellerReport\":{\"main\":\"0\"},\"providerReport\":{\"main\":\"0\"}},\"did\":{\"did_list\":{\"main\":\"0\",\"list\":\"0\",\"import\":\"0\",\"purchase\":\"0\"}},\"accessnumber\":{\"accessnumber_list\":{\"main\":\"0\"}},\"pricing\":{\"price_list\":{\"main\":\"0\"}},\"rates\":{\"origination_rates_list\":{\"main\":\"0\"}},\"ratedeck\":{\"ratedeck_list\":{\"main\":\"0\"}},\"calltype\":{\"calltype_list\":{\"main\":\"0\"}},\"products\":{\"products_list\":{\"main\":\"0\"}},\"orders\":{\"orders_list\":{\"main\":\"0\"}},\"refill_coupon\":{\"refill_coupon_list\":{\"main\":\"0\"}},\"email\":{\"email_mass\":{\"main\":\"0\"},\"email_history_list\":{\"main\":\"0\"}},\"localization\":{\"localization_list\":{\"main\":\"0\"}},\"callbarring\":{\"callbarring_list\":{\"main\":\"0\"}},\"summary\":{\"customer\":{\"main\":\"0\"},\"reseller\":{\"main\":\"0\"},\"provider\":{\"main\":\"0\"}},\"audit\":{\"audit_list\":{\"main\":\"0\"}},\"countryreports\":{\"accounts_list\":{\"main\":\"0\"},\"provider_list_outbound\":{\"main\":\"0\"}},\"taxes\":{\"taxes_list\":{\"main\":\"0\"}},\"systems\":{\"template\":{\"main\":\"0\"},\"languages_list\":{\"main\":\"0\"}}}', 'null', '2019-01-26 07:41:03', '2019-02-09 11:02:49'),
(2, 'Reseller Permission', 0, 'Test Reseller Permission', 1, '{\"accounts\":{\"customer_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"mass_create\":\"0\",\"export\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"reseller_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"}},\"freeswitch\":{\"fssipdevices\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"search\":\"0\"}},\"ipmap\":{\"ipmap_detail\":{\"main\":\"0\",\"list\":\"0\",\"Add\":\"0\",\"search\":\"0\"}},\"animap\":{\"animap_detail\":{\"main\":\"0\",\"list\":\"0\",\"Add\":\"0\",\"search\":\"0\"}},\"products\":{\"products_topuplist\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"search\":\"0\"},\"products_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"orders\":{\"orders_list\":{\"main\":\"0\",\"list\":\"0\",\"new\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"did\":{\"did_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"purchase\":\"0\"}},\"accessnumber\":{\"accessnumber_list\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"}},\"pricing\":{\"price_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"rates\":{\"origination_rates_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"}},\"ratedeck\":{\"ratedeck_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"search\":\"0\",\"export\":\"0\",\"import\":\"0\"}},\"reports\":{\"customerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"resellerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"summary\":{\"customer\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"reseller\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"invoices\":{\"invoice_conf_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\"}},\"systems\":{\"template\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"}}}', 'null', '2019-01-26 07:46:49', '2019-02-06 11:57:23'),
(3, 'QA', 0, 'all', 0, '{\"accounts\":{\"customer_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"mass_create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"callerid\":\"0\",\"payment\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"reseller_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"callerid\":\"0\",\"payment\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"admin_list\":{\"main\":\"0\",\"list\":\"0\",\"create_admin\":\"0\",\"create_ subadmin\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"}},\"freeswitch\":{\"fssipdevices\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"fsgateway\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"fssipprofile\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"fsserver_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"livecall_report\":{\"main\":\"0\",\"list\":\"0\"}},\"ipmap\":{\"ipmap_detail\":{\"main\":\"0\",\"list\":\"0\",\"Add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"animap\":{\"animap_detail\":{\"main\":\"0\",\"list\":\"0\",\"Add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"permissions\":{\"permissions_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"invoices\":{\"invoice_list\":{\"main\":\"0\",\"list\":\"0\",\"download\":\"0\",\"edit\":\"0\",\"generate\":\"0\",\"search\":\"0\",\"delete\":\"0\"},\"invoice_conf_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\"}},\"reports\":{\"refillreport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"charges_history\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"},\"commission_report_list\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"customerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"resellerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"providerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"did\":{\"did_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"FORWARD\":\"0\",\"search\":\"0\",\"purchase\":\"0\"}},\"accessnumber\":{\"accessnumber_list\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"}},\"pricing\":{\"price_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"duplicate\":\"0\"}},\"rates\":{\"origination_rates_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"termination_rates_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\",\"field_import\":\"0\"}},\"ratedeck\":{\"ratedeck_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"export\":\"0\",\"import\":\"0\"}},\"calltype\":{\"calltype_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"trunk\":{\"trunk_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"products\":{\"products_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"EDIT\":\"0\",\"search\":\"0\"}},\"orders\":{\"orders_list\":{\"main\":\"0\",\"list\":\"0\",\"new\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"refill_coupon\":{\"refill_coupon_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"search\":\"0\",\"DELETE\":\"0\"}},\"email\":{\"email_mass\":{\"main\":\"0\",\"list\":\"0\"},\"email_history_list\":{\"main\":\"0\",\"list\":\"0\",\"RESEND\":\"0\",\"VIEW\":\"0\",\"search\":\"0\"}},\"localization\":{\"localization_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"callbarring\":{\"callbarring_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"summary\":{\"customer\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"reseller\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"provider\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"countryreports\":{\"accounts_list\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"},\"provider_list_outbound\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"}},\"audit\":{\"audit_list\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"}},\"taxes\":{\"taxes_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"systems\":{\"template\":{\"main\":\"0\",\"list\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"country_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"currency_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"currencies_update\":\"0\"},\"database_restore\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"configuration\":{\"main\":\"0\",\"list\":\"0\"},\"languages_list\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"search\":\"0\"},\"translation_list\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"search\":\"0\"}},\"schedule_report\":{\"schedule_report_list\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"search\":\"0\"}},\"cronsettings\":{\"cronsettings_list\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"search\":\"0\"}}}', '', '2019-02-05 06:33:27', '0000-00-00 00:00:00'),
(4, 'sonal_permission', 0, 'test', 0, '{\"accounts\":{\"customer_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"mass_create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"callerid\":\"0\",\"payment\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"reseller_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"admin_list\":{\"main\":\"0\",\"list\":\"0\",\"create_admin\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"freeswitch\":{\"fssipdevices\":{\"main\":\"0\",\"list\":\"0\"},\"fssipprofile\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"livecall_report\":{\"main\":\"0\",\"list\":\"0\"}},\"ipmap\":{\"ipmap_detail\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"animap\":{\"animap_detail\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"permissions\":{\"permissions_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"invoices\":{\"invoice_list\":{\"main\":\"0\",\"list\":\"0\",\"download\":\"0\",\"edit\":\"0\",\"generate\":\"0\",\"search\":\"0\",\"delete\":\"0\"},\"invoice_conf_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\"}},\"reports\":{\"refillreport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"charges_history\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"},\"commission_report_list\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"customerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"resellerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"providerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"did\":{\"did_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"forward\":\"0\",\"search\":\"0\",\"purchase\":\"0\"}},\"accessnumber\":{\"accessnumber_list\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"pricing\":{\"price_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"duplicate\":\"0\"}},\"rates\":{\"origination_rates_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"}},\"ratedeck\":{\"ratedeck_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"export\":\"0\",\"import\":\"0\"}},\"calltype\":{\"calltype_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"products\":{\"products_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"assign\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"orders\":{\"orders_list\":{\"main\":\"0\",\"list\":\"0\",\"new\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"refill_coupon\":{\"refill_coupon_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"search\":\"0\",\"delete\":\"0\"}},\"email\":{\"email_mass\":{\"main\":\"0\",\"list\":\"0\"},\"email_history_list\":{\"main\":\"0\",\"list\":\"0\",\"resend\":\"0\",\"view\":\"0\",\"search\":\"0\"}},\"localization\":{\"localization_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"callbarring\":{\"callbarring_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"summary\":{\"customer\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"reseller\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"provider\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"product\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\",\"export\":\"0\"}},\"audit\":{\"audit_list\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"}},\"taxes\":{\"taxes_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"systems\":{\"template\":{\"main\":\"0\",\"list\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"languages_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"search\":\"0\"}}}', '', '2019-02-06 05:34:40', '2019-02-08 05:03:21'),
(5, 'sonal_reseller', 0, 'sonal_reseller', 1, '{\"accounts\":{\"customer_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"mass_create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"callerid\":\"0\",\"payment\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"reseller_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"callerid\":\"0\",\"payment\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"}},\"freeswitch\":{\"fssipdevices\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"ipmap\":{\"ipmap_detail\":{\"main\":\"0\",\"list\":\"0\",\"Add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"animap\":{\"animap_detail\":{\"main\":\"0\",\"list\":\"0\",\"Add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"invoices\":{\"invoice_list\":{\"main\":\"0\",\"list\":\"0\",\"download\":\"0\",\"edit\":\"0\",\"generate\":\"0\",\"search\":\"0\",\"delete\":\"0\"},\"invoice_conf_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\"}},\"reports\":{\"refillreport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"charges_history\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"},\"commission_report_list\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"customerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"resellerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"did\":{\"did_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"forward\":\"0\",\"search\":\"0\",\"purchase\":\"0\",\"buy_did\":\"0\",\"available_did\":\"0\"}},\"accessnumber\":{\"accessnumber_list\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"}},\"pricing\":{\"price_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"duplicate\":\"0\"}},\"rates\":{\"origination_rates_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"resellersrates_list\":{\"main\":\"0\",\"list\":\"0\"}},\"products\":{\"products_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"products_topuplist\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"payment\":\"0\"}},\"orders\":{\"orders_list\":{\"main\":\"0\",\"list\":\"0\",\"new\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"summary\":{\"customer\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"reseller\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"systems\":{\"template\":{\"main\":\"0\",\"list\":\"0\",\"edit\":\"0\",\"search\":\"0\"}}}', '', '2019-02-06 06:05:27', '2019-02-11 10:50:14'),
(6, 'test', 0, 'test', 0, '{\"accounts\":{\"customer_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"mass_create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"callerid\":\"0\",\"payment\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"},\"reseller_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"admin_list\":{\"main\":\"0\",\"list\":\"0\",\"create_admin\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"freeswitch\":{\"fssipdevices\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"fssipprofile\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"},\"livecall_report\":{\"main\":\"0\",\"list\":\"0\"}},\"ipmap\":{\"ipmap_detail\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"animap\":{\"animap_detail\":{\"main\":\"0\",\"list\":\"0\",\"add\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"permissions\":{\"permissions_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"invoices\":{\"invoice_list\":{\"main\":\"0\",\"list\":\"0\",\"download\":\"0\",\"edit\":\"0\",\"generate\":\"0\",\"search\":\"0\",\"delete\":\"0\"}},\"reports\":{\"refillreport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"charges_history\":{\"main\":\"0\",\"list\":\"0\",\"search\":\"0\"},\"commission_report_list\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"customerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"resellerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"},\"providerReport\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"search\":\"0\"}},\"did\":{\"did_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"forward\":\"0\",\"search\":\"0\",\"purchase\":\"0\"}},\"accessnumber\":{\"accessnumber_list\":{\"main\":\"0\",\"list\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"pricing\":{\"price_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"duplicate\":\"0\"}},\"rates\":{\"origination_rates_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"import\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"batch_update\":\"0\"}},\"ratedeck\":{\"ratedeck_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\",\"export\":\"0\",\"import\":\"0\"}},\"calltype\":{\"calltype_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"products\":{\"products_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"assign\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"orders\":{\"orders_list\":{\"main\":\"0\",\"list\":\"0\",\"new\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"refill_coupon\":{\"refill_coupon_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"export\":\"0\",\"search\":\"0\",\"delete\":\"0\"}},\"email\":{\"email_mass\":{\"main\":\"0\",\"list\":\"0\"}},\"localization\":{\"localization_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}},\"callbarring\":{\"callbarring_list\":{\"main\":\"0\",\"list\":\"0\",\"create\":\"0\",\"delete\":\"0\",\"edit\":\"0\",\"search\":\"0\"}}}', '', '2019-02-07 10:54:37', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions` ADD PRIMARY KEY (`id`);
ALTER TABLE `permissions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS post_load_modules_conf;

DROP TABLE IF EXISTS post_load_switch_conf;

ALTER TABLE `pricelists` ADD `pricelist_id_admin` INT(11) NOT NULL DEFAULT '0' AFTER `reseller_id`, ADD `routing_prefix` VARCHAR(100) NOT NULL AFTER `pricelist_id_admin`, ADD `call_count` INT(11) NOT NULL DEFAULT '0' AFTER `routing_prefix`;

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `product_category` int(11) NOT NULL,
  `buy_cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `price` decimal(10,5) DEFAULT '0.00000',
  `setup_fee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `can_resell` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 for no,0 for yes',
  `commission` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `billing_type` tinyint(1) NOT NULL,
  `billing_days` int(11) NOT NULL DEFAULT '0',
  `free_minutes` int(11) NOT NULL DEFAULT '0',
  `applicable_for` int(11) NOT NULL,
  `apply_on_existing_account` tinyint(1) NOT NULL,
  `apply_on_rategroups` varchar(50) NOT NULL,
  `destination_rategroups` varchar(50) NOT NULL,
  `destination_countries` varchar(256) NOT NULL,
  `destination_calltypes` varchar(50) NOT NULL,
  `release_no_balance` tinyint(1) NOT NULL,
  `can_purchase` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for yes, 1 for no',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active,1 for inactive',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0 for no,1 for yes',
  `created_by` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products` ADD PRIMARY KEY (`id`);
ALTER TABLE `products` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `provider_cdr_summary`
--

CREATE TABLE `provider_cdr_summary` (
  `date_hour` varchar(25) NOT NULL,
  `country_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `trunk_id` int(11) NOT NULL,
  `total_calls` int(11) NOT NULL,
  `answered_calls` int(11) NOT NULL,
  `minutes` varchar(50) NOT NULL,
  `cost` decimal(15,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `provider_cdr_summary`
--
ALTER TABLE `provider_cdr_summary`
  ADD PRIMARY KEY (`date_hour`,`country_id`,`provider_id`,`trunk_id`);

--
-- Table structure for table `q850code`
--
DROP TABLE IF EXISTS `q850code`;

CREATE TABLE `q850code` (
  `cause` varchar(70) NOT NULL,
  `code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `q850code`
--

INSERT INTO `q850code` (`cause`, `code`) VALUES
('UNSPECIFIED', 0),
('UNALLOCATED_NUMBER', 1),
('NO_ROUTE_TRANSIT_NET', 2),
('NO_ROUTE_DESTINATION', 3),
('CHANNEL_UNACCEPTABLE', 6),
('CALL_AWARDED_DELIVERED', 7),
('NORMAL_CLEARING', 16),
('USER_BUSY', 17),
('NO_USER_RESPONSE', 18),
('NO_ANSWER', 19),
('SUBSCRIBER_ABSENT', 20),
('CALL_REJECTED', 21),
('NUMBER_CHANGED', 22),
('REDIRECTION_TO_NEW_DESTINATION', 23),
('EXCHANGE_ROUTING_ERROR', 25),
('DESTINATION_OUT_OF_ORDER', 27),
('INVALID_NUMBER_FORMAT', 28),
('FACILITY_REJECTED', 29),
('RESPONSE_TO_STATUS_ENQUIRY', 30),
('NORMAL_UNSPECIFIED', 31),
('NORMAL_CIRCUIT_CONGESTION', 34),
('NETWORK_OUT_OF_ORDER', 38),
('NORMAL_TEMPORARY_FAILURE', 41),
('SWITCH_CONGESTION', 42),
('ACCESS_INFO_DISCARDED', 43),
('REQUESTED_CHAN_UNAVAIL', 44),
('PRE_EMPTED', 45),
('FACILITY_NOT_SUBSCRIBED', 50),
('OUTGOING_CALL_BARRED', 52),
('INCOMING_CALL_BARRED', 54),
('BEARERCAPABILITY_NOTAUTH', 57),
('BEARERCAPABILITY_NOTAVAIL', 58),
('SERVICE_UNAVAILABLE', 63),
('BEARERCAPABILITY_NOTIMPL', 65),
('CHAN_NOT_IMPLEMENTED', 66),
('FACILITY_NOT_IMPLEMENTED', 69),
('SERVICE_NOT_IMPLEMENTED', 79),
('INVALID_CALL_REFERENCE', 81),
('INCOMPATIBLE_DESTINATION', 88),
('INVALID_MSG_UNSPECIFIED', 95),
('MANDATORY_IE_MISSING', 96),
('MESSAGE_TYPE_NONEXIST', 97),
('WRONG_MESSAGE', 98),
('IE_NONEXIST', 99),
('INVALID_IE_CONTENTS', 100),
('WRONG_CALL_STATE', 101),
('RECOVERY_ON_TIMER_EXPIRE', 102),
('MANDATORY_IE_LENGTH_ERROR', 103),
('PROTOCOL_ERROR', 111),
('INTERWORKING', 127),
('ORIGINATOR_CANCEL', 487),
('CRASH', 500),
('SYSTEM_SHUTDOWN', 501),
('LOSE_RACE', 502),
('MANAGER_REQUEST', 503),
('BLIND_TRANSFER', 600),
('ATTENDED_TRANSFER', 601),
('ALLOTTED_TIMEOUT', 602),
('USER_CHALLENGE', 603),
('MEDIA_TIMEOUT', 604),
('PICKED_OFF', 605),
('USER_NOT_REGISTERED', 606),
('PROGRESS_TIMEOUT', 607),
('GATEWAY_DOWN', 609);

--
-- Table structure for table `ratedeck`
--
DROP TABLE IF EXISTS `ratedeck`;
CREATE TABLE `ratedeck` (
  `id` int(6) NOT NULL,
  `destination` varchar(80) NOT NULL,
  `country_id` int(11) NOT NULL,
  `pattern` varchar(40) NOT NULL,
  `call_type` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1 = Disabled / Inactive / False / No , 0 = Enable / Active / True / Yes,2->Deleted',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ratedeck`
--
ALTER TABLE `ratedeck` ADD PRIMARY KEY (`id`);
ALTER TABLE `ratedeck` MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

ALTER TABLE `refill_coupon` CHANGE `creation_date` `creation_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `reseller_cdrs` ADD `country_id` INT(11) NOT NULL AFTER `call_request`;

DROP TABLE IF EXISTS reseller_pricing;

--
-- Table structure for table `reseller_products`
--

CREATE TABLE `reseller_products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `buy_cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `price` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `free_minutes` int(11) NOT NULL DEFAULT '0',
  `commission` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `setup_fee` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `billing_days` int(11) NOT NULL,
  `billing_type` tinyint(2) NOT NULL COMMENT '0 for onetime,1 for recurring',
  `is_owner` tinyint(2) NOT NULL COMMENT '0 for yes, 1 for no',
  `is_optin` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0 for yes, 1 for no',
  `optin_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `modified_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reseller_products`
--
ALTER TABLE `reseller_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`account_id`,`reseller_id`) USING BTREE;
ALTER TABLE `reseller_products` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `roles_and_permission`
--

CREATE TABLE `roles_and_permission` (
  `id` int(11) NOT NULL,
  `login_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Admin,1:Reseller',
  `permission_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Main,1:Edit',
  `menu_name` varchar(50) NOT NULL,
  `module_name` varchar(50) NOT NULL,
  `sub_module_name` varchar(50) NOT NULL,
  `module_url` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `permissions` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Active,1:Inactive',
  `creation_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `priority` decimal(10,5) NOT NULL DEFAULT '0.00000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles_and_permission`
--

INSERT INTO `roles_and_permission` (`id`, `login_type`, `permission_type`, `menu_name`, `module_name`, `sub_module_name`, `module_url`, `display_name`, `permissions`, `status`, `creation_date`, `priority`) VALUES
(1, 0, 0, 'accounts', 'accounts', 'customers', 'customer_list', 'Customers', '[\"main\",\"list\",\"create\",\"mass_create\",\"export\",\"import\",\"delete\",\"edit\",\"callerid\",\"payment\",\"search\",\"batch_update\"]', 0, '2019-01-25 09:01:03', '1.10000'),
(2, 0, 0, 'accounts', 'freeswitch', 'customers', 'fssipdevices', 'Sip Devices', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '1.20000'),
(3, 0, 0, 'accounts', 'ipmap', 'customers', 'ipmap_detail', 'IP Settings', '[\"main\",\"list\",\"add\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '1.30000'),
(4, 0, 0, 'accounts', 'animap', 'customers', 'animap_detail', 'Caller IDs', '[\"main\",\"list\",\"add\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '1.40000'),
(5, 0, 0, 'accounts', 'accounts', '', 'reseller_list', 'Resellers', '[\"main\",\"list\",\"create\",\"export\",\"delete\",\"edit\",\"search\",\"payment\"]', 0, '2019-01-25 09:01:03', '1.50000'),
(6, 0, 0, 'accounts', 'accounts', '', 'admin_list', 'Admins', '[\"main\",\"list\",\"create_admin\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:03', '1.60000'),
(7, 0, 0, 'accounts', 'permissions', '', 'permissions_list', 'Roles & Permission', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:03', '1.70000'),
(8, 0, 0, 'billing', 'invoices', '', 'invoice_list', 'Invoices', '[\"main\",\"list\",\"download\",\"edit\",\"generate\",\"search\",\"delete\"]', 0, '2019-01-25 09:01:03', '2.10000'),
(9, 0, 0, 'billing', 'reports', '', 'refillreport', 'Refill Report', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:03', '2.20000'),
(10, 0, 0, 'billing', 'reports', '', 'charges_history', 'Charges History', '[\"main\",\"list\",\"search\"]', 0, '2019-01-25 09:01:03', '2.30000'),
(11, 0, 0, 'billing', 'reports', 'commission_report', 'commission_report_list', 'Commission Reports', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '2.40000'),
(12, 0, 0, 'inbound', 'did', '', 'did_list', 'DIDs', '[\"main\",\"list\",\"create\",\"export\",\"import\",\"delete\",\"edit\",\"forward\",\"search\",\"purchase\"]', 0, '2019-01-25 09:01:05', '3.10000'),
(13, 0, 0, 'inbound', 'accessnumber', '', 'accessnumber_list', 'Access Numbers', '[\"main\",\"list\",\"export\",\"import\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '3.20000'),
(14, 0, 0, 'tariff', 'pricing', '', 'price_list', 'Rate Groups', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\",\"duplicate\"]', 0, '2019-01-25 09:01:05', '4.10000'),
(15, 0, 0, 'tariff', 'rates', '', 'origination_rates_list', 'Origination Rates', '[\"main\",\"list\",\"create\",\"export\",\"import\",\"delete\",\"edit\",\"search\",\"batch_update\"]', 0, '2019-01-25 09:01:05', '4.20000'),
(16, 0, 0, 'tariff', 'ratedeck', '', 'ratedeck_list', 'Ratedeck', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\",\"export\",\"import\"]', 0, '2019-01-25 09:01:05', '4.30000'),
(17, 0, 0, 'tariff', 'calltype', '', 'calltype_list', 'Call Types', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '4.40000'),
(18, 0, 0, 'services', 'products', '', 'products_list', 'Products', '[\"main\",\"list\",\"create\",\"assign\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.10000'),
(19, 0, 0, 'services', 'orders', '', 'orders_list', 'Orders', '[\"main\",\"list\",\"new\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.20000'),
(20, 0, 0, 'services', 'refill_coupon', '', 'refill_coupon_list', 'Refill Coupons', '[\"main\",\"list\",\"create\",\"export\",\"search\",\"delete\"]', 0, '2019-01-25 09:01:05', '5.30000'),
(21, 0, 0, 'services', 'email', '', 'email_mass', 'Mass Email', '[\"main\",\"list\"]', 0, '2019-01-25 09:01:05', '5.40000'),
(22, 0, 0, 'switch', 'freeswitch', '', 'fssipprofile', 'SIP Profiles', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '6.10000'),
(23, 0, 0, 'switch', 'freeswitch', '', 'livecall_report', 'Live Calls', '[\"main\",\"list\"]', 0, '2019-01-25 09:01:05', '6.20000'),
(24, 0, 0, 'switch', 'localization', '', 'localization_list', 'Localizations', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '6.30000'),
(25, 0, 0, 'switch', 'callbarring', '', 'callbarring_list', 'Call Barring', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '6.40000'),
(26, 0, 0, 'reports', 'reports', 'call_detail_reports', 'customerReport', 'Customer', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '7.10000'),
(27, 0, 0, 'reports', 'reports', 'call_detail_reports', 'resellerReport', 'Reseller', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '7.20000'),
(28, 0, 0, 'reports', 'reports', 'call_detail_reports', 'providerReport', 'Provider Outbound', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '7.30000'),
(29, 0, 0, 'reports', 'summary', 'call_summary_reports', 'customer', 'Customer Summary', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '7.40000'),
(30, 0, 0, 'reports', 'summary', 'call_summary_reports', 'reseller', 'Reseller Summary', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '7.50000'),
(31, 0, 0, 'reports', 'summary', 'call_summary_reports', 'provider', 'Provider Summary', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '7.60000'),
(32, 0, 0, 'reports', 'email', '', 'email_history_list', 'Email History', '[\"main\",\"list\",\"resend\",\"view\",\"search\"]', 0, '2019-01-25 09:01:06', '7.70000'),
(33, 0, 0, 'reports', 'audit', '', 'audit_list', 'Audit Log', '[\"main\",\"list\",\"search\"]', 0, '2019-01-25 09:01:06', '7.80000'),
(34, 0, 0, 'reports', 'summary', 'product', 'product', 'Product Summary Report', '[\"main\",\"list\",\"search\",\"export\"]', 0, '2019-01-25 09:01:03', '7.90000'),
(35, 0, 0, 'configuration', 'invoices', '', 'invoice_conf_list', 'Profiles', '[\"main\",\"list\",\"create\"]', 0, '2019-01-25 09:01:06', '8.10000'),
(36, 0, 0, 'configuration', 'taxes', '', 'taxes_list', 'Taxes', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:06', '8.20000'),
(37, 0, 0, 'configuration', 'systems', '', 'template', 'Templates', '[\"main\",\"list\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:06', '8.30000'),
(38, 0, 0, 'configuration', 'systems', 'languages', 'languages_list', 'Languages', '[\"main\",\"list\",\"create\",\"delete\",\"search\"]', 0, '2019-01-25 09:01:06', '8.40000'),
(39, 1, 0, 'accounts', 'accounts', 'customers', 'customer_list', 'Customers', '[\"main\",\"list\",\"create\",\"mass_create\",\"export\",\"delete\",\"edit\",\"callerid\",\"payment\",\"search\",\"batch_update\"]', 0, '2019-01-25 09:01:03', '1.10000'),
(40, 1, 0, 'accounts', 'freeswitch', 'customers', 'fssipdevices', 'Sip Devices', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '1.20000'),
(41, 1, 0, 'accounts', 'ipmap', 'customers', 'ipmap_detail', 'IP Settings', '[\"main\",\"list\",\"Add\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '1.30000'),
(42, 1, 0, 'accounts', 'animap', 'customers', 'animap_detail', 'Caller IDs', '[\"main\",\"list\",\"Add\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '1.40000'),
(43, 1, 0, 'accounts', 'accounts', '', 'reseller_list', 'Resellers', '[\"main\",\"list\",\"create\",\"export\",\"delete\",\"edit\",\"callerid\",\"payment\",\"search\",\"batch_update\"]', 0, '2019-01-25 09:01:03', '1.50000'),
(44, 1, 0, 'billing', 'invoices', '', 'invoice_list', 'Invoices', '[\"main\",\"list\",\"download\",\"edit\",\"generate\",\"search\",\"delete\"]', 0, '2019-01-25 09:01:03', '2.10000'),
(45, 1, 0, 'billing', 'reports', '', 'refillreport', 'Refill Report', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:03', '2.20000'),
(46, 1, 0, 'billing', 'reports', '', 'charges_history', 'Charges History', '[\"main\",\"list\",\"search\"]', 0, '2019-01-25 09:01:03', '2.30000'),
(47, 1, 0, 'billing', 'reports', 'commission_report', 'commission_report_list', 'Commission Reports', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '2.40000'),
(48, 1, 0, 'inbound', 'did', '', 'did_list', 'DIDs', '[\"main\",\"list\",\"create\",\"export\",\"import\",\"delete\",\"edit\",\"forward\",\"search\",\"purchase\",\"buy_did\",\"available_did\"]', 0, '2019-01-25 09:01:05', '3.10000'),
(49, 1, 0, 'inbound', 'accessnumber', '', 'accessnumber_list', 'Access Numbers', '[\"main\",\"list\",\"search\"]', 0, '2019-01-25 09:01:05', '3.20000'),
(50, 1, 0, 'tariff', 'pricing', '', 'price_list', 'Rate Groups', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\",\"duplicate\"]', 0, '2019-01-25 09:01:05', '4.10000'),
(51, 1, 0, 'tariff', 'rates', '', 'origination_rates_list', 'Origination Rates', '[\"main\",\"list\",\"create\",\"export\",\"import\",\"delete\",\"edit\",\"search\",\"batch_update\"]', 0, '2019-01-25 09:01:05', '4.20000'),
(52, 1, 0, 'tariff', 'rates', 'resellersrates', 'resellersrates_list', 'My Rates', '[\"main\",\"list\"]', 0, '2019-01-25 09:01:03', '4.30000'),
(53, 1, 0, 'services', 'products', '', 'products_list', 'Products', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.10000'),
(54, 1, 0, 'services', 'orders', '', 'orders_list', 'Orders', '[\"main\",\"list\",\"new\",\"delete\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:05', '5.20000'),
(55, 1, 0, 'services', 'products', '', 'products_topuplist', 'TopUp', '[\"main\",\"list\",\"create\",\"delete\",\"edit\",\"search\",\"payment\"]', 0, '2019-02-04 11:49:27', '5.30000'),
(56, 1, 0, 'reports', 'reports', 'call_detail_reports', 'customerReport', 'Customer', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '6.10000'),
(57, 1, 0, 'reports', 'reports', 'call_detail_reports', 'resellerReport', 'Reseller', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '6.20000'),
(58, 1, 0, 'reports', 'summary', 'call_summary_reports', 'customer', 'Customer Summary', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '6.30000'),
(59, 1, 0, 'reports', 'summary', 'call_summary_reports', 'reseller', 'Reseller Summary', '[\"main\",\"list\",\"export\",\"search\"]', 0, '2019-01-25 09:01:06', '6.40000'),
(60, 1, 0, 'configuration', 'invoices', '', 'invoice_conf_list', 'Profile', '[\"main\",\"list\",\"create\"]', 0, '2019-01-25 09:01:06', '7.10000'),
(61, 1, 0, 'configuration', 'systems', '', 'template', 'Templates', '[\"main\",\"list\",\"edit\",\"search\"]', 0, '2019-01-25 09:01:06', '7.20000'),
(89, 0, 0, 'configuration', 'schedule_report', 'schedule_report', 'schedule_report_list', 'Schedule Report', '[\"main\",\"list\",\"search\",\"create\",\"delete\"]', 0, '2019-01-25 09:01:03', '9.50000'),
(101, 0, 0, 'services', 'ipboss', 'servers', 'servers_list', 'Servers', '[\"main\",\"list\",\"search\",\"add_server\",\"edit\",\"delete\"]', 0, '2019-01-25 09:01:03', '5.70000'),
(102, 0, 0, 'services', 'ipboss', 'chains', 'chains_list', 'Chain', '[\"main\",\"list\",\"search\",\"add_chain\",\"edit\",\"delete\"]', 0, '2019-01-25 09:01:03', '5.80000'),
(105, 0, 0, 'configuration', 'schedule_report', 'schedule_report', 'schedule_report_list', 'Schedule Report', '[\"main\",\"list\",\"search\",\"create\",\"delete\"]', 0, '2019-01-25 09:01:03', '9.50000'),
(108, 0, 0, 'services', 'ipboss', 'servers', 'servers_list', 'Servers', '[\"main\",\"list\",\"search\",\"add_server\",\"edit\",\"delete\"]', 0, '2019-01-25 09:01:03', '5.70000'),
(109, 0, 0, 'services', 'ipboss', 'chains', 'chains_list', 'Chain', '[\"main\",\"list\",\"search\",\"add_chain\",\"edit\",\"delete\"]', 0, '2019-01-25 09:01:03', '5.80000'),
(110, 0, 0, 'configuration', 'schedule_report', 'schedule_report', 'schedule_report_list', 'Schedule Report', '[\"main\",\"list\",\"search\",\"create\",\"delete\"]', 0, '2019-01-25 09:01:03', '9.50000'),
(111, 0, 0, 'configuration', 'schedule_report', 'schedule_report', 'schedule_report_list', 'Schedule Report', '[\"main\",\"list\",\"search\",\"create\",\"delete\"]', 0, '2019-01-25 09:01:03', '9.50000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `roles_and_permission`
--
ALTER TABLE `roles_and_permission` ADD PRIMARY KEY (`id`);
ALTER TABLE `roles_and_permission` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `routes` ADD `country_id` INT NOT NULL DEFAULT '0' AFTER `inc`, ADD `call_type` VARCHAR(20) NOT NULL AFTER `country_id`, ADD `routing_type` VARCHAR(50) NOT NULL AFTER `call_type`, ADD `percentage` VARCHAR(50) NOT NULL AFTER `routing_type`, ADD `call_count` INT(11) NOT NULL AFTER `percentage`;

ALTER TABLE `routing` ADD `routes_id` INT NOT NULL DEFAULT '0' AFTER `trunk_id`, ADD `percentage` VARCHAR(20) NOT NULL AFTER `routes_id`, ADD `call_count` INT NOT NULL DEFAULT '0' AFTER `percentage`;

ALTER TABLE `sip_devices` CHANGE `creation_date` `creation_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00', CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `sip_profiles` CHANGE `created_date` `created_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00', CHANGE `last_modified_date` `last_modified_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';

ALTER TABLE `system` CHANGE `brand_id` `is_display` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `system` ADD `sub_group` VARCHAR(100) NOT NULL AFTER `group_title`;

ALTER TABLE `taxes` ADD `tax_type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0:Default,1:Other' AFTER `taxes_amount`;

ALTER TABLE `taxes_to_accounts` ADD `assign_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' AFTER `taxes_priority`;

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `en_En` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `module_name`, `en_En`) VALUES
(1, 'Accounts', 'Accounts'),
(2, 'Customers', 'Customers'),
(3, 'Billing', 'Billing'),
(4, 'First Name', 'First Name'),
(5, 'Invoices', 'Invoices'),
(6, 'Refill Report', 'Refill Report'),
(7, 'Begins With', 'Begins With'),
(8, 'Charges History', 'Charges History'),
(9, 'Contains', 'Contains'),
(10, 'Commission Reports', 'Commission Reports'),
(11, 'Doesnt Contain', 'Doesnt Contain'),
(12, 'Tariff', 'Tariff'),
(13, 'Is Equal To', 'Is Equal To'),
(14, 'Rate Groups', 'Rate Groups'),
(15, 'Is Not Equal To', 'Is Not Equal To'),
(16, 'Ends With', 'Ends With'),
(17, 'Origination Rates', 'Origination Rates'),
(18, 'Ratedeck', 'Ratedeck'),
(19, 'Last Name', 'Last Name'),
(20, 'Call Types', 'Call Types'),
(21, 'Company', 'Company'),
(22, 'CC', 'CC'),
(23, 'Services', 'Services'),
(24, 'Balance', 'Balance'),
(25, 'Products', 'Products'),
(26, 'Credit Limit', 'Credit Limit'),
(27, 'Orders', 'Orders'),
(28, 'Email', 'Email'),
(29, 'First Used', 'First Used'),
(30, 'Refill Coupons', 'Refill Coupons'),
(31, 'Expiry Date', 'Expiry Date'),
(32, 'Mass Email', 'Mass Email'),
(33, 'Rate Group', 'Rate Group'),
(34, 'Reports', 'Reports'),
(35, 'Status', 'Status'),
(36, 'Created Date', 'Created Date'),
(37, 'Call Detail Reports', 'Call Detail Reports'),
(38, 'Entity Type', 'Entity Type'),
(39, 'Call Summary Reports', 'Call Summary Reports'),
(40, 'Account Type', 'Account Type'),
(41, 'Email History', 'Email History'),
(42, 'Billing Cycle', 'Billing Cycle'),
(43, 'Audit Log', 'Audit Log'),
(44, 'Country Reports', 'Country Reports'),
(45, 'Reseller', 'Reseller'),
(46, 'Customer', 'Customer'),
(47, 'Search', 'Search'),
(49, 'Clear', 'Clear'),
(50, 'Provider Outbound', 'Provider Outbound'),
(51, 'Customer Summary', 'Customer Summary'),
(52, 'Create Customer', 'Create Customer'),
(53, 'Mass Create', 'Mass Create'),
(54, 'Import Customers', 'Import Customers'),
(55, 'Create Provider', 'Create Provider'),
(56, 'Export', 'Export'),
(57, 'Delete', 'Delete'),
(58, 'Account', 'Account'),
(60, 'Localization', 'Localization'),
(61, 'Page', 'Page'),
(62, 'Records', 'Records'),
(63, 'of', 'of'),
(65, 'SIP Devices', 'SIP Devices'),
(66, 'Username', 'Username'),
(67, 'SIP Profile', 'SIP Profile'),
(69, 'Password', 'Password'),
(70, 'Caller Number', 'Caller Number'),
(72, 'Caller Name', 'Caller Name'),
(73, 'Voicemail', 'Voicemail'),
(75, 'Modified Date', 'Modified Date'),
(76, 'Create', 'Create'),
(77, 'IP Settings', 'IP Settings'),
(78, 'Name', 'Name'),
(79, 'IP', 'IP'),
(80, 'Prefix', 'Prefix'),
(81, 'Add', 'Add'),
(83, 'Caller ID', 'Caller ID'),
(84, 'Reseller Summary', 'Reseller Summary'),
(86, 'Provider', 'Provider'),
(88, 'Caller IDs', 'Caller IDs'),
(89, 'Accounts Report', 'Accounts Report'),
(90, 'Resellers', 'Resellers'),
(91, 'Provider Outbound Report', 'Provider Outbound Report'),
(92, ' Batch Update', ' Batch Update'),
(94, 'Preserve', 'Preserve'),
(95, 'Balance Below', 'Balance Below'),
(97, 'LC Charge/Min', 'LC Charge/Min'),
(98, 'CPS', 'CPS'),
(99, 'Allow Loss Less Routing', 'Allow Loss Less Routing'),
(100, 'Allow Recording', 'Allow Recording'),
(101, 'Allow IP Management', 'Allow IP Management'),
(102, 'Allow Local Call', 'Allow Local Call'),
(103, 'Number', 'Number'),
(104, 'Type', 'Type'),
(105, 'Generate Invoice', 'Generate Invoice'),
(106, ' Powered by ASTPP ', ' Powered by ASTPP '),
(107, 'Low balance Alert?', 'Low balance Alert?'),
(110, 'Update', 'Update'),
(112, 'Invoice Date', 'Invoice Date'),
(113, 'From Date', 'From Date'),
(115, 'Due Date', 'Due Date'),
(116, 'Last Paid Date', 'Last Paid Date'),
(117, 'Roles & Permission', 'Roles & Permission'),
(118, 'Amount INR', 'Amount INR'),
(119, 'Outstanding Amount INR', 'Outstanding Amount INR'),
(120, 'Action', 'Action'),
(121, 'Roles & Permissions', 'Roles & Permissions'),
(122, 'Automatically', 'Automatically'),
(123, 'Description', 'Description'),
(124, 'Unpaid', 'Unpaid'),
(125, 'Admins', 'Admins'),
(126, 'Manually', 'Manually'),
(127, 'Create Admin', 'Create Admin'),
(131, 'Phone', 'Phone'),
(132, 'Notes', 'Notes'),
(133, 'Country', 'Country'),
(134, 'Version 4.0 Beta', 'Version 4.0 Beta'),
(135, 'Role', 'Role'),
(136, 'Date', 'Date'),
(139, 'Payment Method', 'Payment Method'),
(140, 'Refill By', 'Refill By'),
(141, 'Transaction ID', 'Transaction ID'),
(142, 'Receiver Email', 'Receiver Email'),
(143, 'Client IP', 'Client IP'),
(147, 'Amount', 'Amount'),
(151, 'Invoice Number', 'Invoice Number'),
(152, 'Charge Type', 'Charge Type'),
(154, 'Before Balance INR', 'Before Balance INR'),
(155, 'Debit', 'Debit'),
(156, 'Credit', 'Credit'),
(157, 'After Balance', 'After Balance'),
(158, 'Product Name', 'Product Name'),
(159, 'Order', 'Order'),
(161, 'Commission', 'Commission'),
(163, 'Order ID', 'Order ID'),
(164, 'Commission Rate', 'Commission Rate'),
(166, 'Routing Prefix', 'Routing Prefix'),
(167, 'Routing Type', 'Routing Type'),
(168, 'Initial Increment', 'Initial Increment'),
(169, 'Increment', 'Increment'),
(170, 'Markup', 'Markup'),
(171, 'Rates Count', 'Rates Count'),
(173, 'Duplicate', 'Duplicate'),
(175, 'Default Increment ', 'Default Increment '),
(176, 'Code', 'Code'),
(178, 'Connection Cost', 'Connection Cost'),
(179, 'Grace Time', 'Grace Time'),
(180, 'Cost/Min', 'Cost/Min'),
(183, 'Inbound', 'Inbound'),
(184, 'DIDs', 'DIDs'),
(185, 'Call Type', 'Call Type'),
(187, 'Add Ratedeck', 'Add Ratedeck'),
(188, 'Ratedeck Information', 'Ratedeck Information'),
(189, 'Create Rate Group', 'Create Rate Group'),
(190, 'Basic', 'Basic'),
(192, 'Create Origination Rate', 'Create Origination Rate'),
(193, 'DID', 'DID'),
(194, 'Rate Information', 'Rate Information'),
(196, 'Billing Information', 'Billing Information'),
(197, 'Cost', 'Cost'),
(198, 'Setup Fee', 'Setup Fee'),
(199, 'Price', 'Price'),
(201, 'Destination', 'Destination'),
(202, 'Import', 'Import'),
(203, 'Included Seconds', 'Included Seconds'),
(204, 'Per Minute Cost', 'Per Minute Cost'),
(210, 'Is Purchased?', 'Is Purchased?'),
(211, 'Create calltype', 'Create calltype'),
(212, 'Calltype Information', 'Calltype Information'),
(213, 'Billing Days', 'Billing Days'),
(214, 'Billing Type', 'Billing Type'),
(215, 'Call Timeout', 'Call Timeout'),
(217, 'Category', 'Category'),
(218, 'Buy Cost', 'Buy Cost'),
(220, 'Local Numbers', 'Local Numbers'),
(221, 'Local Number', 'Local Number'),
(225, 'Province/State', 'Province/State'),
(227, 'Free Minutes', 'Free Minutes'),
(228, 'Product Category', 'Product Category'),
(229, 'Create Product ', 'Create Product '),
(230, 'City', 'City'),
(231, 'Basic Information', 'Basic Information'),
(232, 'Product Details', 'Product Details'),
(233, 'Access Numbers', 'Access Numbers'),
(234, 'Access Number', 'Access Number'),
(235, 'Can be purchased?', 'Can be purchased?'),
(236, 'Reseller can resell', 'Reseller can resell'),
(237, 'Release if no balance', 'Release if no balance'),
(238, 'Apply on existing accounts', 'Apply on existing accounts'),
(239, 'Applicable For', 'Applicable For'),
(241, 'Email Notification', 'Email Notification'),
(242, 'Add Destination', 'Add Destination'),
(245, 'Place Order ', 'Place Order '),
(247, 'Order Now', 'Order Now'),
(248, 'Subscription', 'Subscription'),
(249, 'Used', 'Used'),
(250, 'Used Date', 'Used Date'),
(251, 'Create Refill Coupon', 'Create Refill Coupon'),
(252, 'Coupon Information', 'Coupon Information'),
(253, 'Start prefix', 'Start prefix'),
(254, 'Quantity', 'Quantity'),
(256, 'Coupon Number', 'Coupon Number'),
(257, 'Filter', 'Filter'),
(258, 'Email Template', 'Email Template'),
(261, 'Called Number', 'Called Number'),
(263, 'Duration', 'Duration'),
(264, 'Disposition', 'Disposition'),
(265, 'Trunk', 'Trunk'),
(268, 'Select Year', 'Select Year'),
(269, 'Display records in', 'Display records in'),
(270, 'Resellers CDRs Report ', 'Resellers CDRs Report '),
(271, 'Customer CDRs Report ', 'Customer CDRs Report '),
(272, 'Provider CDRs Report ', 'Provider CDRs Report '),
(273, 'Customer Summary Report ', 'Customer Summary Report '),
(274, 'Attempted Calls', 'Attempted Calls'),
(275, 'Completed Calls', 'Completed Calls'),
(276, 'ASR', 'ASR'),
(277, 'ACD', 'ACD'),
(278, 'MCD', 'MCD'),
(279, 'Billable', 'Billable'),
(280, 'Profit', 'Profit'),
(281, 'Group By', 'Group By'),
(282, 'Group By #Time', 'Group By #Time'),
(283, 'Group By #1', 'Group By #1'),
(284, 'Group By #2', 'Group By #2'),
(285, 'Group By #3', 'Group By #3'),
(287, 'Reseller Summary Report ', 'Reseller Summary Report '),
(288, 'Provider Summary Report ', 'Provider Summary Report '),
(289, 'Email History List ', 'Email History List '),
(290, 'From', 'From'),
(291, 'To', 'To'),
(292, 'Subject', 'Subject'),
(293, 'Body', 'Body'),
(294, 'To Number', 'To Number'),
(295, 'SMS Body', 'SMS Body'),
(296, 'Attachement', 'Attachement'),
(297, 'Resend Mail', 'Resend Mail'),
(298, 'View Details', 'View Details'),
(300, 'Audit', 'Audit'),
(302, 'Request URI', 'Request URI'),
(303, 'Timestamp', 'Timestamp'),
(305, 'Client User Agent', 'Client User Agent'),
(306, 'Referer Page', 'Referer Page'),
(307, 'From Timestamp', 'From Timestamp'),
(308, 'To Timestamp', 'To Timestamp'),
(313, 'Answered Calls', 'Answered Calls'),
(314, 'Minutes', 'Minutes'),
(315, 'Calls', 'Calls'),
(317, 'Grand Total', 'Grand Total'),
(318, 'Total Calls ', 'Total Calls '),
(319, 'Total Minutes', 'Total Minutes'),
(320, 'Total Charges', 'Total Charges'),
(321, 'Calls Breakdown', 'Calls Breakdown'),
(322, 'Minutes Breakdown', 'Minutes Breakdown'),
(323, 'Charges Breakdown', 'Charges Breakdown'),
(325, ' Provider Outbound Report', ' Provider Outbound Report'),
(326, 'Company Profiles', 'Company Profiles'),
(327, 'Telephone', 'Telephone'),
(329, 'Domain', 'Domain'),
(330, 'Create Company Profile ', 'Create Company Profile '),
(331, 'Configuration ', 'Configuration '),
(332, 'Invoice Configuration ', 'Invoice Configuration '),
(333, 'Portal personalization', 'Portal personalization'),
(334, 'Address1', 'Address1'),
(335, 'Province', 'Province'),
(336, 'Zip Code', 'Zip Code'),
(338, 'Fax', 'Fax'),
(340, 'Website', 'Website'),
(341, 'Company Tax number', 'Company Tax number'),
(344, '898989', '8989'),
(345, 'Invoice Notification', 'Invoice Notification'),
(346, 'Invoice Due Notification', 'Invoice Due Notification'),
(347, 'Invoice Due Days', 'Invoice Due Days'),
(348, 'Notify before due days', 'Notify before due days'),
(349, 'Invoice Prefix', 'Invoice Prefix'),
(350, 'Invoice Start Form', 'Invoice Start Form'),
(352, 'Header', 'Header'),
(353, 'Footer', 'Footer'),
(354, 'Logo', 'Logo'),
(355, 'Favicon', 'Favicon'),
(356, 'Select File', 'Select File'),
(359, 'Priority', 'Priority'),
(360, 'Rate', 'Rate'),
(361, 'Create Tax', 'Create Tax'),
(362, 'Tax Information', 'Tax Information'),
(364, 'Email Status', 'Email Status'),
(365, 'SMS Status', 'SMS Status'),
(366, 'Alert Status', 'Alert Status'),
(367, 'Templates', 'Templates'),
(368, 'Countries', 'Countries'),
(369, 'Nickname', 'Nickname'),
(370, 'Capital', 'Capital'),
(371, 'Iso', 'Iso'),
(372, 'Iso3', 'Iso3'),
(373, 'Country Code', 'Country Code'),
(374, 'Currency', 'Currency'),
(376, 'Add Country', 'Add Country'),
(377, 'Country List', 'Country List'),
(379, 'Add Currency', 'Add Currency'),
(380, 'Currency List', 'Currency List'),
(381, 'Carriers', 'Carriers'),
(382, 'Gateways', 'Gateways'),
(385, 'Proxy', 'Proxy'),
(386, 'Register', 'Register'),
(387, 'Caller-Id-In-Form', 'Caller-Id-In-Form'),
(388, 'Trunks', 'Trunks'),
(390, 'Gateway Name', 'Gateway Name'),
(391, 'Failover  GW Name #1', 'Failover  GW Name #1'),
(392, 'Failover GW Name #2', 'Failover GW Name #2'),
(394, 'Codecs', 'Codecs'),
(395, 'Rate Count', 'Rate Count'),
(396, 'Termination Rates', 'Termination Rates'),
(397, 'Import with field map', 'Import with field map'),
(406, 'Strip', 'Strip'),
(407, 'Prepend', 'Prepend'),
(408, 'Switch', 'Switch'),
(409, 'FreeSwitch Servers', 'FreeSwitch Servers'),
(410, 'SIP Profiles', 'SIP Profiles'),
(411, '  SIP IP', '  SIP IP'),
(412, 'SIP Port', 'SIP Port'),
(413, 'Profile Action', 'Profile Action'),
(414, 'Host', 'Host'),
(415, 'Port', 'Port'),
(418, 'Live Call Report', 'Live Call Report'),
(419, 'Call Date', 'Call Date'),
(421, 'CID', 'CID'),
(422, 'Caller IP', 'Caller IP'),
(424, 'Org. Pefix', 'Org. Pefix'),
(425, 'Org. Destination', 'Org. Destination'),
(426, 'Org. Cost', 'Org. Cost'),
(427, 'Term. Trunk', 'Term. Trunk'),
(428, 'Term. Prefix', 'Term. Prefix'),
(429, 'Term. Destination', 'Term. Destination'),
(430, 'Term. Cost', 'Term. Cost'),
(433, 'Localizations', 'Localizations'),
(434, 'Call Barring', 'Call Barring'),
(435, 'Number Type', 'Number Type'),
(436, 'Direction', 'Direction'),
(437, 'Action Type', 'Action Type'),
(438, 'Crons', 'Crons'),
(439, 'Interval Type', 'Interval Type'),
(440, 'Interval', 'Interval'),
(441, 'Last Execution Date', 'Last Execution Date'),
(442, 'File Path', 'File Path'),
(443, 'Next Execution Date', 'Next Execution Date'),
(444, 'Creation Date', 'Creation Date'),
(445, 'Last Modified Date', 'Last Modified Date'),
(446, 'Settings', 'Settings'),
(447, 'Global', 'Global'),
(448, 'Decimal Points', 'Decimal Points'),
(449, 'Base Currency', 'Base Currency'),
(450, 'Refill Coupon Length', 'Refill Coupon Length'),
(451, 'Minimum Fund Transfer', 'Minimum Fund Transfer'),
(452, 'Default Invoice Mode', 'Default Invoice Mode'),
(453, 'Log Path', 'Log Path'),
(454, 'Fixer Key', 'Fixer Key'),
(455, 'Currency Conversion Loss Percentage', 'Currency Conversion Loss Percentage'),
(456, 'Password Strength', 'Password Strength'),
(457, 'Ewallet Payment Gateway', 'Ewallet Payment Gateway'),
(458, 'Save', 'Save'),
(459, 'General', 'General'),
(461, 'Debug', 'Debug'),
(462, 'Max Free Length', 'Max Free Length'),
(463, 'Call Max Length', 'Call Max Length'),
(464, 'Playback Audio Notifications', 'Playback Audio Notifications'),
(465, 'Balance Announcement', 'Balance Announcement'),
(466, 'Minutes Announcement', 'Minutes Announcement'),
(467, 'Enable', 'Enable'),
(468, 'Disable', 'Disable'),
(469, 'Voicemail Number', 'Voicemail Number'),
(470, 'Local Call Timeout', 'Local Call Timeout'),
(471, 'Realtime Billing', 'Realtime Billing'),
(472, 'Calling Card', 'Calling Card'),
(473, 'Card Retries', 'Card Retries'),
(474, 'Pin retries', 'Pin retries'),
(475, 'Rate Announcement', 'Rate Announcement'),
(476, 'Timelimit Announcement', 'Timelimit Announcement'),
(477, 'Pin Input Timeout', 'Pin Input Timeout'),
(478, 'Card Input Timeout', 'Card Input Timeout'),
(479, 'Dial Input Timeout', 'Dial Input Timeout'),
(480, 'General Input Timeout', 'General Input Timeout'),
(481, 'Welcome File', 'Welcome File'),
(482, 'Pinless Authentication', 'Pinless Authentication'),
(483, 'IVR Count', 'IVR Count'),
(486, 'Outbound Fax', 'Outbound Fax'),
(487, 'Inbound Fax', 'Inbound Fax'),
(488, 'Homer', 'Homer'),
(489, 'Capture Server', 'Capture Server'),
(490, 'Opensips', 'Opensips'),
(491, 'Opensips DB Engine', 'Opensips DB Engine'),
(493, 'Opensips DB Name', 'Opensips DB Name'),
(494, 'Opensips DB User', 'Opensips DB User'),
(495, 'Opensips DB Host', 'Opensips DB Host'),
(496, 'Opensips DB Pass', 'Opensips DB Pass'),
(497, 'Opensips Domain', 'Opensips Domain'),
(500, 'Payment Methods', 'Payment Methods'),
(501, 'Paypal', 'Paypal'),
(503, 'Live Url', 'Live Url'),
(504, 'Sandbox Url', 'Sandbox Url'),
(505, 'Live Id', 'Live Id'),
(506, 'Sandbox Id', 'Sandbox Id'),
(507, 'Environment', 'Environment'),
(508, 'Fee', 'Fee'),
(509, 'Tax', 'Tax'),
(510, 'Authorize.net', 'Authorize.net'),
(511, 'Set Maximum Add card limit', 'Set Maximum Add card limit'),
(512, 'Sandbox Name', 'Sandbox Name'),
(513, 'Sandbox Key', 'Sandbox Key'),
(516, 'Live Key', 'Live Key'),
(517, 'Purge', 'Purge'),
(518, 'Deleted Accounts After Days', 'Deleted Accounts After Days'),
(519, 'Expired Accounts After Days', 'Expired Accounts After Days'),
(520, 'Inovices Older Than Days', 'Inovices Older Than Days'),
(521, 'Emails Older Than Days', 'Emails Older Than Days'),
(522, 'CDRs Older Than Days', 'CDRs Older Than Days'),
(523, 'Audit Logs Older Than Days', 'Audit Logs Older Than Days'),
(524, 'Recording Files Older Than Days', 'Recording Files Older Than Days'),
(525, 'Signup', 'Signup'),
(526, 'Starting Digit', 'Starting Digit'),
(527, 'Card Length', 'Card Length'),
(528, 'Pin Length', 'Pin Length'),
(529, 'Timezone', 'Timezone'),
(531, 'Enable Signup', 'Enable Signup'),
(532, 'Create SIP Device', 'Create SIP Device'),
(533, 'Initial Balance', 'Initial Balance'),
(534, 'Default Tax', 'Default Tax'),
(535, 'Telephone as account number', 'Telephone as account number'),
(536, 'Account Verification By', 'Account Verification By'),
(537, 'Allow Max Retries', 'Allow Max Retries'),
(538, 'Generate Pin', 'Generate Pin'),
(539, 'Allow Local Calls', 'Allow Local Calls'),
(540, 'LC Charge / Min', 'LC Charge / Min'),
(541, 'Concurrent Calls', 'Concurrent Calls'),
(546, 'Localization Type', 'Localization Type'),
(547, 'Account Valid Days', 'Account Valid Days'),
(548, 'Email Alerts ?', 'Email Alerts ?'),
(550, 'Database', 'Database'),
(551, 'Archive', 'Archive'),
(552, '6 months', '6 months'),
(553, '1 year', '1 year'),
(554, 'Notifications', 'Notifications'),
(556, 'Email Notifications', 'Email Notifications'),
(557, 'SMTP', 'SMTP'),
(558, 'SMTP Host', 'SMTP Host'),
(559, 'SMTP Port', 'SMTP Port'),
(560, 'SMTP User', 'SMTP User'),
(561, 'SMTP Pass', 'SMTP Pass'),
(562, 'Mail Log', 'Mail Log'),
(563, 'SMS', 'SMS'),
(564, 'SMS Notifications', 'SMS Notifications'),
(565, 'Nexmo API Key', 'Nexmo API Key'),
(566, 'Nexmo Secret Key', 'Nexmo Secret Key'),
(567, 'Push Notifications', 'Push Notifications'),
(568, 'Languages', 'Languages'),
(569, 'Locale code', 'Locale code'),
(570, 'Translations Language', 'Translations Language'),
(571, 'Module', 'Module'),
(572, 'En', 'En'),
(573, 'Version', 'Version'),
(574, 'Beta', 'Beta'),
(575, 'Dashboard', 'Dashboard'),
(576, 'Report a Bug', 'Report a Bug'),
(577, 'Documentation', 'Documentation'),
(578, 'Translations', 'Translations'),
(579, 'Get App', 'Get App'),
(580, 'Get Addons', 'Get Addons'),
(581, 'Log out', 'Log out'),
(582, 'Administrator', 'Administrator'),
(583, 'Call State', 'Call State'),
(585, 'Today', 'Today'),
(586, 'This Month', 'This Month'),
(587, 'This Week', 'This Week'),
(594, 'Top 10 Destinations', 'Top 10 Destinations'),
(596, 'New Accounts', 'New Accounts'),
(598, 'Refills', 'Refills'),
(600, 'Top 10 Accounts', 'Top 10 Accounts'),
(601, 'No Records Found', 'No Records Found'),
(602, 'Latest Orders', 'Latest Orders'),
(605, 'Order Date', 'Order Date'),
(606, 'Order Amount', 'Order Amount'),
(608, 'Payment Status', 'Payment Status'),
(609, 'View All', 'View All'),
(610, 'Alarm', 'Alarm'),
(611, 'Generates Alarm From System', 'Generates Alarm From System'),
(612, 'Generates Various Country Reports From System', 'Generates Various Country Reports From System'),
(614, 'FMAddon', 'FMAddon'),
(615, 'Generates Various Alarms From System', 'Generates Various Alarms From System'),
(619, 'Yes', 'Yes'),
(621, 'No', 'No'),
(622, 'Allow integration with Fraud detection', 'Allow integration with Fraud detection'),
(623, 'Generates Local Number system', 'Generates Local Number system'),
(624, 'Generates various reports from system', 'Generates various reports from system'),
(626, 'Generates ticket of issues from system', 'Generates ticket of issues from system'),
(627, 'Install', 'Install'),
(629, 'Sandbox', 'Sandbox'),
(631, 'Live', 'Live'),
(634, 'Fraud Detection', 'Fraud Detection'),
(638, 'Schedule Reports', 'Schedule Reports'),
(639, 'Supportticket', 'Supportticket'),
(640, 'Strong', 'Strong'),
(641, 'Moderate', 'Moderate'),
(642, 'Confirmed', 'Confirmed'),
(643, 'Draft', 'Draft'),
(644, 'Installed', 'Installed'),
(647, 'Opensource', 'Opensource'),
(648, 'Enterprise', 'Enterprise'),
(649, 'Third Party', 'Third Party'),
(651, 'Administrator Admin', 'Administrator Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `en_En` (`en_En`);

ALTER TABLE `translations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

/*ALTER TABLE `trunks` ADD `cps` INT(4) NOT NULL DEFAULT '0';

ALTER TABLE `trunks` ADD `leg_timeout` INT(11) NOT NULL DEFAULT '30' AFTER `cps`;*/

--
-- Table structure for table `usertracking`
--

DROP TABLE IF EXISTS `usertracking`;

CREATE TABLE `usertracking` (
  `id` int(6) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `user_identifier` varchar(255) NOT NULL,
  `request_uri` text NOT NULL,
  `timestamp` datetime NOT NULL,
  `client_ip` varchar(50) NOT NULL,
  `client_user_agent` text NOT NULL,
  `referer_page` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `usertracking`
--
ALTER TABLE `usertracking` ADD PRIMARY KEY (`id`);
ALTER TABLE `usertracking` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Userlevel table opeations 
-- 

TRUNCATE TABLE `userlevels`;
ALTER TABLE `userlevels` CHANGE `module_permissions` `module_permissions` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

INSERT INTO `userlevels` (`userlevelid`, `userlevelname`, `module_permissions`) VALUES
(-1, 'Administrator', '1,2,4,5,3,8,9,13,14,15,16,17,18,19,20,21,22,25,26,27,28,7,29,30,45,38,39,40,41,42,43,44,48,49,51,53,54,55,56,66,68,69,77,78,79,80,81,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,177,178,179,180,149,184,185,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,221,226,227,228,229,230,231,232,233,234,235,236,237,238,243,244,245,246,247,248,249,250,251,252,253,254,255,256,269,270,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,406,407,408,409,410,411,413,415,416,417,418,420,422,424,426,428,430,432,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,435,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476,477,478,479,480,481,482,483,484,485,488,490,491,492,493,494,495,496,497,498,499,500,501,502,503,504,505,506,507,509,512,515,517,518,519,520,521,522,523,524,525,526,527,528,529,530,531,532,533,534,535,536,537,538,539,540,541,542,543,544,545,549,551,553,554,555,556,557,558,559,560,561,562,563,564,565,566,567,568,569,570,571,572,573,574,575,576,577,578,579,580,581,582,583,584,585,586,587,588,589,590,591,592,593,594,595,596,597,598,599,600,601,602,603,604,605,606,607,608,609,610,611,610,611,613,615,617,619,620,622,623,625,626,627,628,629,630,631,632,633,634,635,637,638,640,641,643,644,646,647,649,650,652,653,655,656,658,659,661,662,663,664,665,666,667,669,671,672,673,686,687,549,551'),
(0, 'Customer', '31,32,37,36,34,35,33,63,64,67,70,71,73,74,76,72,82,181,182,396,397,398,399,400,401,414,419,421,423,425,427,429,431,433,486,487,489,508,510,511,513,514,516,550,552,612,614,616,618,619,621,622,624,625,636,637,639,640,642,643,645,646,648,649,651,652,654,655,657,658,660,661,668,670,550'),
(1, 'Reseller', '1,2,7,8,13,16,17,18,19,21,25,28,38,40,44,45,46,52,9,29,53,54,66,55,68,79,81,93,94,95,96,97,100,101,102,103,104,107,108,109,110,111,114,115,116,117,118,123,124,126,127,131,132,133,134,135,140,141,143,144,148,149,150,151,152,157,158,160,161,165,166,168,169,91,149,92,183,277,281,285,289,293,297,302,355,357,359,361,363,365,369,371,372,374,378,392,89,403,406,410,413,415,418,420,422,424,426,428,430,432,418,420,422,424,426,428,430,432,435,437,439,441,443,445,447,463,471,476,478,480,483,486,487,488,493,502,508,509,511,512,514,515,517,524,548,549,551,570,574,584,586,588,594,598,602,606,611,613,615,617,619,620,622,623,625,635,637,638,640,641,643,644,646,647,649,650,652,653,655,656,658,659,661,668,669,679'),
(2, 'Admin', '1,2,3,4,5,7,8,9,13,14,15,16,17,18,19,20,21,22,25,26,27,28,29,30,38,40,41,42,43,44,45,65,93,94,97,100,101,104,107,108,111,114,115,118,123,124,125,131,132,135,140,141,142,148,149,152,157,158,159,165,166,167,149,229,230,231,232,233,234,235,236,237,238,275,276,306,307,376,377,390,391,506,507,578,579,580,581,582,583,592,593,626,627'),
(3, 'Provider', '31,32,37,36,34,35,33,63,64,67,70,71,73,74,75,356,358,360,362,364,366,370,373,375,398,33,399,400,401,82,411,419,421,423,425,427,429,431,433,436,438,440,442,444,446,448,464,477,479,481,484,486,487,489,508,510,511,513,514,516,585,587,589,668,670,680');

--
-- menu_modules tables operations 
-- 
TRUNCATE TABLE `menu_modules`;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(8, 'Invoices', 'invoice', 'invoices/invoice_list/', 'Billing', 'InvoiceList.png', '0', 20.1),
(9, 'Company Profiles', 'invoice', 'invoices/invoice_conf_list/', 'Configuration', 'InvoiceConf.png', '0', 90.1),
(10, 'Calling Cards', 'callingcards', 'callingcards/callingcards_list/', 'Calling Cards', 'ListCards.png', '0', 30.1),
(11, 'Card Brands', 'brands', 'callingcards/brands/', 'Calling Cards', 'CCBand.png', '0', 30.2),
(12, 'Call Report', 'callingcards', 'callingcards/callingcards_cdrs/', 'Calling Cards', 'CallingCardCDR\'s.png', '0', 30.3),
(13, 'DIDs', 'did', 'did/did_list/', 'Inbound', 'ManageDIDs.png', '0', 30.1),
(14, 'Trunks', 'trunk', 'trunk/trunk_list/', 'Carriers', 'Trunks.png', '0', 50.2),
(15, 'Termination Rates', 'termination', 'rates/termination_rates_list/', 'Carriers', 'OutboundRoutes.png', '0', 50.3),
(16, 'Rate Groups', 'price', 'pricing/price_list/', 'Tariff', 'pricelist.png', '0', 40.1),
(17, 'Origination Rates', 'origination', 'rates/origination_rates_list/', 'Tariff', 'Routes.png', '0', 40.2),
(20, 'Live Calls', 'livecall', 'freeswitch/livecall_report/', 'Switch', 'cdr.png', '0', 70.3),
(26, 'Configuration', 'configuration', 'systems/configuration/', 'System Configuration', 'Configurations.png', 'System', 90.1),
(27, 'Taxes', 'taxes', 'taxes/taxes_list/', 'Configuration', 'AccountTaxes.png', '0', 90.2),
(28, 'Templates', 'template', 'systems/template/', 'Configuration', 'TemplateManagement.png', '0', 90.3),
(29, 'Opensips devices', 'opensips', 'opensips/opensips_list/', 'Switch', 'OpensipDevices.png', '0', 90.2),
(30, 'Dispatcher list', 'dispatcher', 'opensips/dispatcher_list/', 'Switch', 'Dispatcher.png', '0', 90.3),
(31, 'Invoices', 'user', 'user/user_invoices_list/', 'Billing', 'ListAccounts.png', '0', 1.1),
(32, 'DIDs', 'user', 'user/user_didlist/', 'Inbound', 'ManageDIDs.png', '0', 2.1),
(33, 'Pinless CLI', 'user', 'user/user_animap_list/', 'Calling Card', 'Providers.png', '0', 4.2),
(34, 'CDRs', 'user', 'user/user_cdrs_report/', 'CDRs', 'cdr.png', '0', 5.1),
(35, 'Refill Report', 'user', 'user/user_refill_report/', 'Billing', 'PaymentReport.png', '0', 1.3),
(36, 'SIP Devices', 'user', 'user/user_sipdevices/', 'My Account', 'freeswitch.png', '0', 7.2),
(37, 'Rates', 'user', 'user/user_rates_list/', 'Rates', 'Routes.png', '0', 6.1),
(40, 'Refill Report', 'refillreport', 'reports/refillreport/', 'Billing', 'PaymentReport.png', '0', 20.2),
(41, 'Gateways', 'fsgateway', 'freeswitch/fsgateway/', 'Carriers', 'Gateway.png', '0', 50.1),
(42, 'SIP Profiles', 'fssipprofile', 'freeswitch/fssipprofile/', 'Switch', 'SipProfiles.png', '0', 70.1),
(43, 'FreeSwitch Servers', 'fsserver', 'freeswitch/fsserver_list/', 'Switch', 'freeswitch.png', '0', 70.2),
(48, 'Countries', 'country', 'systems/country_list/', 'Configuration', 'ManageDIDs.png', '0', 90.4),
(49, 'Currencies ', 'currency', 'systems/currency_list/', 'Configuration', 'ManageDIDs.png', '0', 90.5),
(52, 'My Rates', 'resellersrates', 'rates/resellersrates_list/', 'Tariff', 'OutboundRoutes.png', '0', 52.1),
(56, 'Mass Email', 'email', 'email/email_mass/', 'Services', 'email.jpg', '0', 60.4),
(58, 'Opensips', 'Configuration', 'systems/configuration/opensips', 'Configuration', '', 'Settings', 80.8),
(59, 'Callingcard', 'Configuration', 'systems/configuration/callingcard', 'Configuration', '', 'Settings', 80.9),
(60, 'Freeswitch', 'Configuration', 'systems/configuration/freeswitch', 'Configuration', '', 'Settings', 80.1),
(61, 'Paypal', 'configuration', 'systems/configuration/paypal', 'Configuration', '', 'Settings', 80.11),
(62, 'Mass Email1', 'Configuration', 'systems/configuration/email', 'Configuration', '', 'Settings', 80.12),
(63, 'Fund Transfer', 'customer', 'user/user_fund_transfer/', 'Services', 'ListAccounts.png', '0', 3.3),
(65, 'Signup', 'Configuration', 'systems/configuration/signup/', 'Configuration', '', 'Settings', 81),
(66, 'Refill Coupons', 'refill', 'refill_coupon/refill_coupon_list/', 'Services', 'cdr.png', '0', 60.3),
(68, 'Charges History', 'charges', 'reports/charges_history/', 'Billing', 'PaymentReport.png', '0', 20.3),
(70, 'Charges History', 'user', 'user/user_charges_history/', 'Billing', 'PaymentReport.png', '0', 1.2),
(72, 'IP Settings', 'user', 'user/user_ipmap/', 'My Account', 'PaymentReport.png', '0', 7.3),
(73, 'Alert Threshold', 'user', 'user/user_alert_threshold/', 'My Account', 'PaymentReport.png', '0', 7.5),
(74, 'Speed Dial', 'user', 'user/user_speeddial/', 'My Account', 'freeswitch.png', '0', 7.4),
(75, 'Provider Outbound', 'user', 'user/user_provider_cdrs_report/', 'Reports', 'cdr.png', '0', 4.3),
(76, 'Opensips', 'user', 'user/user_opensips/', 'Configuration', 'OpensipDevices.png', '0', 90.2),
(77, '', 'addons', 'addons/addons_list/', '', '', '0', 74),
(79, 'Ratedeck', 'ratedeck', 'ratedeck/ratedeck_list/', 'Tariff', 'Routes.png', '0', 40.3),
(84, 'Localizations', 'localization', 'localization/localization_list/', 'Configuration', '', '0', 80.6),
(86, 'Call Barring', 'callbarring', 'callbarring/callbarring_list/', 'Configuration', '', '0', 80.6),
(88, 'Call Types', 'calltype', 'calltype/calltype_list/', 'Tariff', 'packages.png', '0', 55),
(89, 'Access Numbers', 'accessnumber', 'accessnumber/accessnumber_list/', 'Inbound', '', '0', 30.3),
(91, 'Products', 'products', 'products/products_list/', 'Services', 'Routes.png', '0', 60.1),
(92, 'Orders', 'orders', 'orders/orders_list/', 'Services', 'Routes.png', '0', 60.2),
(93, 'Customers', 'accounts', 'accounts/customer_list/', 'Accounts', 'ListAccounts.png', 'Customers', 10.1),
(100, 'Customers', 'accounts', 'accounts/customer_list/', 'Accounts', 'ListAccounts.png', 'Customers', 10.1),
(107, 'Customers', 'accounts', 'accounts/customer_list/', 'Accounts', 'ListAccounts.png', 'Customers', 10.1),
(114, 'Customers', 'accounts', 'accounts/customer_list/', 'Accounts', 'ListAccounts.png', 'Customers', 10.1),
(121, 'Localizations', 'localization', 'localization/localization_list/', 'Switch', '', '0', 70.4),
(122, 'Call Barring', 'callbarring', 'callbarring/callbarring_list/', 'Switch', '', '0', 70.5),
(131, 'Customers', 'accounts', 'accounts/customer_list/', 'Accounts', 'ListAccounts.png', 'Customers', 10.1),
(138, 'Localizations', 'localization', 'localization/localization_list/', 'Switch', '', '0', 70.4),
(139, 'Call Barring', 'callbarring', 'callbarring/callbarring_list/', 'Switch', '', '0', 70.5),
(148, 'Customers', 'accounts', 'accounts/customer_list/', 'Accounts', 'ListAccounts.png', 'Customers', 10.1),
(149, 'SIP Devices', 'fssipdevices', 'freeswitch/fssipdevices/', 'Accounts', 'Devices.png', 'Customers', 10.2),
(150, 'IP Settings', 'ipmap', 'ipmap/ipmap_detail/', 'Accounts', 'Gateway.png', 'Customers', 10.3),
(151, 'Caller IDs', 'animap', 'animap/animap_detail/', 'Accounts', 'Gateway.png', 'Customers', 10.4),
(152, 'Resellers', 'reseller', 'accounts/reseller_list/', 'Accounts', 'reseller.png', '0', 10.4),
(153, 'Admins', 'admin', 'accounts/admin_list/', 'Accounts', 'ListAccounts.png', '0', 10.5),
(154, 'Roles & Permission', 'permissions', 'permissions/permissions_list/', 'Accounts', '', '0', 10.6),
(155, 'Localizations', 'localization', 'localization/localization_list/', 'Switch', '', '0', 70.4),
(156, 'Call Barring', 'callbarring', 'callbarring/callbarring_list/', 'Switch', '', '0', 70.5),
(165, 'Customer', 'customerReport', 'reports/customerReport/', 'Reports', 'cdr.png', 'Call Detail Reports', 80.1),
(166, 'Reseller', 'resellerReport', 'reports/resellerReport/', 'Reports', 'cdr.png', 'Call Detail Reports', 80.2),
(167, 'Provider Outbound', 'providerReport', 'reports/providerReport/', 'Reports', 'cdr.png', 'Call Detail Reports', 80.3),
(168, 'Customer Summary', 'customer', 'summary/customer/', 'Reports', 'cdr.png', 'Call Summary Reports', 81.1),
(169, 'Reseller Summary', 'reseller', 'summary/reseller/', 'Reports', 'cdr.png', 'Call Summary Reports', 81.2),
(170, 'Provider', 'provider', 'summary/provider/', 'Reports', 'cdr.png', 'Call Summary Reports', 81.3),
(171, 'Email History', 'email', 'email/email_history_list/', 'Reports', 'ListAccounts.png', '0', 82.1),
(172, 'Audit Log', 'audit', 'audit/audit_list/', 'Reports', '', '0', 82.2),
(177, 'Database Backup', 'database', 'systems/database_restore/', 'Configuration', 'Configurations.png', '0', 90.6),
(179, 'Crons', 'cronsettings', 'cronsettings/cronsettings_list/', 'Configuration', 'Configurations.png', '0', 90.8),
(180, 'Settings', 'configuration', 'systems/configuration/global', 'Configuration', 'Configurations.png', '0', 90.9),
(182, 'Products', 'user', 'user/user_products_list/', 'Services', 'ListAccounts.png', '0', 3.1),
(183, 'Settings', 'configuration', 'systems/configuration/payment_methods', 'Configuration', 'Configurations.png', '0', 90.12),
(396, 'Order New', 'user', 'user/user_available_products/', 'Services', 'Routes.png', '0', 3.2),
(397, 'Pin', 'user', 'user/user_pin_add/', 'Calling Card', '', '0', 4.3),
(398, 'Access Numbers', 'accessnumber', 'accessnumber/accessnumber_list/', 'Calling Card', '', '0', 4.1),
(399, 'Profile', 'user', 'user/user_myprofile/', 'My Account', '0', '0', 7.1),
(400, 'Dashboard', 'user', 'user/user/', 'Home', '', '0', 0),
(401, 'Change Password', 'user', 'user/user_change_password', 'My Account', '0', '0', 7.6),
(403, 'Commission Repots', 'commission', 'reports/commission_report_list/', 'Billing', '0', '0', 83.1),
(404, 'Languages ', 'languages ', 'systems/languages_list/', 'Configuration', 'ManageDIDs.png', 'Languages', 90.6),
(405, 'Translation', 'translation', 'systems/translation_list/', 'Configuration', 'ManageDIDs.png', 'Languages', 90.7),
(415, 'Commission Repots', 'commission', 'reports/commission_report_list/', 'Billing', '0', '0', 83.1),
(416, 'Languages ', 'languages ', 'systems/languages_list/', 'Configuration', 'ManageDIDs.png', 'Languages', 90.6),
(417, 'Translation', 'translation', 'systems/translation_list/', 'Configuration', 'ManageDIDs.png', 'Languages', 90.7),
(543, 'Product Summary Reports', 'product', 'summary/product/', 'Reports', '', '0', 82.4),
(544, 'Product Summary Reports', 'product', 'summary/product/', 'Reports', '', '0', 82.4),
(546, '', '', 'products/products_list/', '', '', '', 0),
(547, '', '', 'products/products_list/', '', '', '', 0),
(548, 'TopUp', 'products', 'products/products_topuplist/', 'Services', 'Routes.png', '0', 20.6),
(550, 'Refill Coupon', 'refill', 'user/user_refill_coupon_list/', 'Services', 'ListAccounts.png', '0', 3.4);

--
-- Queries for call scripts by Samir 
--

-- For DID Localization - System table 
-- @TODO: Need to udpate field type
INSERT INTO `system` (`name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES ('did_global_translation', 'DID Localization', '0', 'default_system_input', 'If you wish to translate DID number with some defined number then use this feature. This will be applicable to all DIDs.', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');

INSERT INTO `system` (`name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `is_display`, `group_title`, `sub_group`) VALUES ('free_inbound', 'Rate check for DID', '', 'default_system_input', '', '0000-00-00 00:00:00', '0', '0', 'calls', 'General');

-- Create view for packages 
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `packages_view`  AS  select `P`.`id` AS `id`,`P`.`name` AS `package_name`,`O`.`free_minutes` AS `free_minutes`,`P`.`applicable_for` AS `applicable_for`,`O`.`accountid` AS `accountid` from (`products` `P` join `order_items` `O`) where ((`P`.`id` = `O`.`product_id`) and (`P`.`product_category` = 1) and (`P`.`status` = 0) and ((`O`.`is_terminated` = 0) or (`O`.`termination_date` <= utc_timestamp()))) ;

-- For Origination rates 
ALTER TABLE `routes` ADD `accountid` INT NULL DEFAULT '0' AFTER `call_count`;
ALTER TABLE routes DROP INDEX pattern_2;
ALTER TABLE routes DROP INDEX status;
ALTER TABLE `routes` ADD UNIQUE `code_rg_accid_key` (`pattern`, `pricelist_id`, `accountid`);

-- Accounts table. Set localization_id default 0 
ALTER TABLE `accounts` CHANGE `localization_id` `localization_id` INT(3) NULL DEFAULT '0';

-- Counter table fields changes
ALTER TABLE `counters` CHANGE `package_id` `product_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `counters` CHANGE `seconds` `used_seconds` INT(11) NOT NULL DEFAULT '0';

-- CDR table update
ALTER TABLE `cdrs` CHANGE `calltype` `calltype` ENUM('STANDARD','DID','FREE','CALLINGCARD','FAX','LOCAL') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'STANDARD';
ALTER TABLE `cdrs` ADD `sip_user` VARCHAR(20) NOT NULL DEFAULT '' AFTER `type`;
ALTER TABLE `reseller_cdrs` CHANGE `calltype` `calltype` ENUM('STANDARD','DID','FREE','CALLINGCARD','FAX','LOCAL') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'STANDARD';
