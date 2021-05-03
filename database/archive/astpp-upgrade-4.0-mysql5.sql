--
-- Add new calltype in cdrs table
--
ALTER TABLE `cdrs` CHANGE `calltype` `calltype` ENUM( 'STANDARD', 'DID', 'FREE', 'CALLINGCARD', 'FAX' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'STANDARD';
ALTER TABLE `reseller_cdrs` CHANGE `calltype` `calltype` ENUM( 'STANDARD', 'DID', 'FREE', 'CALLINGCARD', 'FAX' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'STANDARD';

-- 
-- Queries for userlevels table
-- 
ALTER TABLE `userlevels` CHANGE `module_permissions` `module_permissions` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,  
  `package_name` varchar(30) NOT NULL,
  `version` varchar(10) NOT NULL,
  `license_key` varchar(30) NOT NULL,
  `installed_date` timestamp NULL DEFAULT NULL,
  `last_updated_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Table structure for table `custom_did_call_types`
--

CREATE TABLE `custom_did_call_types` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Queries for menu modules
-- 
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, '', 'addons', 'addons/addons_list/', '', '', '0', '74');
UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` ) ) WHERE `userlevelid` = -1;
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Audit Log', 'audit', 'audit/audit_list/', 'Call Reports', '', '0', '73.1');
UPDATE `userlevels` SET `module_permissions` = concat( `module_permissions`, ',', (  SELECT max( `id` ) FROM `menu_modules` ) ) WHERE `userlevelid` = -1;

--
-- Table structure for table `usertracking`
--

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

ALTER TABLE `usertracking`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usertracking`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Remove old country table  
--
DROP table `countrycode`;
--
-- Create new country table  
--
CREATE TABLE `countrycode` (
  `id` int(11) NOT NULL,
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

INSERT INTO `countrycode` (`id`, `iso`, `country`, `nicename`, `iso3`, `countrycode`, `vat`, `latitude`, `longitude`, `capital`) VALUES
(1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 93, '0.00000', '33.9391', '67.7100', 'Kabul'),
(2, 'AL', 'ALBANIA', 'Albania', 'ALB', 355, '0.00000', '41.153332', ' 	20.168331', 'Tirana'),
(3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 213, '0.00000', '28.0339', '1.6596', 'Algiers'),
(4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 1684, '0.00000', '-14.270972', '-170.132217', 'Pago Pago'),
(5, 'AD', 'ANDORRA', 'Andorra', 'AND', 376, '0.00000', ' 	42.546245', '1.601554', 'Andorra la Vella'),
(6, 'AO', 'ANGOLA', 'Angola', 'AGO', 244, '0.00000', '-11.202692', ' 	17.873887', 'Luanda'),
(7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 1264, '0.00000', '18.220554', ' 	-63.068615', ''),
(8, 'AG', 'ANTIGUA & BARBUDA', 'Antigua_&_Barbuda', 'ATG', 1268, '0.00000', '17.060816', ' 	-61.796428', 'Saint John\'s'),
(9, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 54, '0.00000', '-38.416097', '-63.616672', 'Buenos Aires'),
(10, 'AM', 'ARMENIA', 'Armenia', 'ARM', 374, '0.00000', ' 	40.069099', '45.038189', 'Yerevan'),
(11, 'AW', 'ARUBA', 'Aruba', 'ABW', 297, '0.00000', '12.52111', ' 	-69.968338', ''),
(12, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 61, '0.00000', '-25.274398', ' 	133.775136', 'Canberra'),
(13, 'AT', 'AUSTRIA', 'Austria', 'AUT', 43, '20.00000', '47.516231', ' 	14.550072', 'Vienna'),
(14, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 994, '0.00000', '40.143105', '47.576927', 'Baku'),
(15, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 1242, '0.00000', '25.03343', '77.3963', 'Nassau'),
(16, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 973, '0.00000', '25.930414', '50.637772', 'Manama'),
(17, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 880, '0.00000', '23.684994', '90.356331', 'Dhaka'),
(18, 'BB', 'BARBADOS', 'Barbados', 'BRB', 1246, '0.00000', '13.193887', '-59.543198', 'Bridgetown'),
(19, 'BY', 'BELARUS', 'Belarus', 'BLR', 375, '0.00000', '53.709807', '27.953389', 'Minsk'),
(20, 'BE', 'BELGIUM', 'Belgium', 'BEL', 32, '21.00000', '50.503887', '4.469936', 'Brussels'),
(21, 'BZ', 'BELIZE', 'Belize', 'BLZ', 501, '0.00000', '17.189877', '-88.49765', 'Belmopan'),
(22, 'BJ', 'BENIN', 'Benin', 'BEN', 229, '0.00000', '9.30769', '2.315834', 'Porto-Novo'),
(23, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 1441, '0.00000', '32.3078', '64.7505', ''),
(24, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 975, '0.00000', '27.514162', '90.433601', 'Thimphu'),
(25, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 591, '0.00000', '-16.290154', '-63.588653', 'La Paz'),
(26, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia_and_Herzegovina', 'BIH', 387, '0.00000', '43.915886', '17.679076', 'Sarajevo'),
(27, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 267, '0.00000', '-22.328474', '24.684866', 'Gaborone'),
(28, 'BR', 'BRAZIL', 'Brazil', 'BRA', 55, '0.00000', '-14.235004', '-51.92528', 'Brasilia'),
(29, 'VG', 'BRITISH VIRGIN ISLANDS', 'British Virgin Islands', 'VGB', 1284, '0.00000', '18.4207', '64.6400', ''),
(30, 'BN', 'BRUNEI', 'Brunei', 'BRN', 673, '0.00000', '4.535277', '114.727669', 'Bandar Seri Begawan'),
(31, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 359, '20.00000', '42.733883', '25.48583', 'Sofia'),
(32, 'BF', 'BURKINA FASO', 'Burkina_Faso', 'BFA', 226, '0.00000', '12.238333', '-1.561593', 'Ouagadougou'),
(33, 'BI', 'BURUNDI', 'Burundi', 'BDI', 257, '0.00000', '-3.373056', '29.918886', 'Bujumbura'),
(34, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 855, '0.00000', '12.5657', '104.9910', 'Phnom Penh'),
(35, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 237, '0.00000', '7.369722', '12.354722', 'Yaounde'),
(36, 'CA', 'CANADA', 'Canada', 'CAN', 1, '0.00000', '56.130366', '-106.346771', 'Ottawa'),
(37, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 238, '0.00000', '16.002082', '-24.013197', 'Praia'),
(38, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 1345, '0.00000', '19.513469', '-80.566956', ''),
(39, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 236, '0.00000', '6.611111', '20.939444', 'Bangui'),
(40, 'TD', 'CHAD', 'Chad', 'TCD', 235, '0.00000', '15.4542', ' 18.7322', 'N\'Djamena'),
(41, 'CL', 'CHILE', 'Chile', 'CHL', 56, '0.00000', '-35.675147', '-71.542969', 'Santiago'),
(42, 'CN', 'CHINA', 'China', 'CHN', 86, '0.00000', '35.86166', '104.195397', 'Beijing'),
(43, 'CO', 'COLOMBIA', 'Colombia', 'COL', 57, '0.00000', '4.570868', '-74.297333', 'Bogota'),
(44, 'KM', 'COMOROS', 'Comoros', 'COM', 269, '0.00000', '-11.875001', '43.872219', 'Moroni'),
(45, 'CG', 'CONGO', 'Congo', 'COG', 242, '0.00000', '-0.228021', '15.827659', ''),
(46, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 506, '0.00000', '9.748917', '-83.753428', 'San Jose'),
(47, 'HR', 'CROATIA', 'Croatia', 'HRV', 385, '25.00000', '45.1', '15.2', 'Zagreb'),
(48, 'CU', 'CUBA', 'Cuba', 'CUB', 53, '0.00000', '21.521757', '-77.781167', 'Havana'),
(49, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 357, '19.00000', '35.126413', '33.429859', 'Nicosia'),
(50, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 420, '21.00000', '49.817492', '15.472962', 'Prague'),
(51, 'CD', 'DEMOCRATIC REPUBLIC', 'Democratic Republic', 'COD', 243, '0.00000', '4.0383', '21.7587', ''),
(52, 'DK', 'DENMARK', 'Denmark', 'DNK', 45, '25.00000', '56.26392', '9.501785', 'Copenhagen'),
(53, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 253, '0.00000', '11.825138', '42.590275', 'Djibouti'),
(54, 'DM', 'DOMINICA', 'Dominica', 'DMA', 1767, '0.00000', '15.414999', '-61.370976', 'Roseau'),
(55, 'DO', 'DOMINICAN REPUBLIC', 'Dominican republic', 'DOM', 1809, '0.00000', '18.735693', '-70.162651', 'Santo Domingo'),
(56, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 593, '0.00000', '-1.831239', '-78.183406', 'Quito'),
(57, 'EG', 'EGYPT', 'Egypt', 'EGY', 20, '0.00000', '26.820553', '30.802498', 'Cairo'),
(58, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 503, '0.00000', '13.794185', '-88.89653', 'San Salvador'),
(59, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 240, '0.00000', '1.650801', '10.267895', 'Malabo'),
(60, 'ER', 'ERITREA', 'Eritrea', 'ERI', 291, '0.00000', '15.179384', '39.782334', 'Asmara'),
(61, 'EE', 'ESTONIA', 'Estonia', 'EST', 372, '20.00000', '58.595272', '25.013607', 'Tallinn'),
(62, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 251, '0.00000', '9.145', '40.489673', 'Addis Ababa'),
(63, 'FO', 'FAEROE ISLANDS', 'Faeroe Islands', 'FRO', 298, '0.00000', '61.892635', '-6.911806', ''),
(64, 'FJ', 'FIJI ISLANDS', 'Fiji Islands', 'FJI', 67970, '0.00000', '-16.578193', '179.414413', ''),
(65, 'FI', 'FINLAND', 'Finland', 'FIN', 358, '24.00000', '61.92411', '25.748151', 'Helsinki'),
(66, 'FR', 'FRANCE', 'France', 'FRA', 33, '20.00000', '46.227638', '2.213749', 'Paris'),
(67, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 594, '0.00000', '3.933889', '-53.125782', ''),
(68, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 689, '0.00000', '-17.679742', '-149.406843', ''),
(69, 'GA', 'GABON', 'Gabon', 'GAB', 241, '0.00000', '-0.803689', '11.609444', 'Libreville'),
(70, 'GM', 'GAMBIA', 'Gambia', 'GMB', 220, '0.00000', '13.443182', '-15.310139', ''),
(71, 'GE', 'GEORGIA', 'Georgia', 'GEO', 995, '0.00000', '42.315407', '43.356892', 'Tbilisi'),
(72, 'DE', 'GERMANY', 'Germany', 'DEU', 49, '19.00000', '51.165691', '10.451526', 'Berlin'),
(73, 'GH', 'GHANA', 'Ghana', 'GHA', 233, '0.00000', '7.946527', '7.946527', 'Accra'),
(74, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 350, '0.00000', '36.137741', '-5.345374', ''),
(75, 'GR', 'GREECE', 'Greece', 'GRC', 30, '23.00000', '39.074208', '21.824312', 'Athens'),
(76, 'GD', 'GRENADA', 'Grenada', 'GRD', 1473, '0.00000', '12.262776', '-61.604171', 'Saint George\'s'),
(77, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 590, '0.00000', '16.995971', '-62.067641', ''),
(78, 'GU', 'GUAM', 'Guam', 'GUM', 1671, '0.00000', '13.444304', '144.793731', ''),
(79, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 502, '0.00000', '15.783471', '-90.230759', 'Guatemala City'),
(80, 'GN', 'GUINEA', 'Guinea', 'GIN', 224, '0.00000', '9.945587', '-9.696645', 'Conakry'),
(81, 'GW', 'GUINEA BISSAU', 'Guinea Bissau', 'GNB', 245, '0.00000', '11.803749', '-15.180413', ''),
(82, 'GY', 'GUYANA', 'Guyana', 'GUY', 592, '0.00000', '4.860416', '-58.93018', 'Georgetown'),
(83, 'HT', 'HAITI', 'Haiti', 'HTI', 509, '0.00000', '18.971187', '-72.285215', 'Port-au-Prince'),
(84, 'HN', 'HONDURAS', 'Honduras', 'HND', 504, '0.00000', '15.199999', '-86.241905', 'Tegucigalpa'),
(85, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 852, '0.00000', '22.396428', '114.109497', ''),
(86, 'HU', 'HUNGARY', 'Hungary', 'HUN', 36, '27.00000', '47.162494', '19.503304', 'Budapest'),
(87, 'IS', 'ICELAND', 'Iceland', 'ISL', 354, '0.00000', '64.963051', '-19.020835', 'Reykjavik'),
(88, 'IN', 'INDIA', 'India', 'IND', 91, '0.00000', '20.5937', '78.9629', 'New Delhi'),
(89, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 62, '0.00000', '-0.789275', '113.921327', 'Jakarta'),
(90, 'IR', 'IRAN', 'Iran', 'IRN', 98, '0.00000', '32.427908', '53.688046', 'Tehran'),
(91, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 964, '0.00000', '33.223191', '43.679291', 'Baghdad'),
(92, 'IE', 'IRELAND', 'Ireland', 'IRL', 353, '23.00000', '53.41291', '-8.24389', 'Dublin'),
(93, 'IL', 'ISRAEL', 'Israel', 'ISR', 972, '0.00000', '31.046051', '34.851612', 'Jerusalem'),
(94, 'IT', 'ITALY', 'Italy', 'ITA', 39, '22.00000', '41.87194', '12.56738', 'Rome'),
(95, 'CI', 'IVORY COAST', 'Ivory Coast', 'CIV', 225, '0.00000', '7.5400', '5.5471', ''),
(96, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 1876, '0.00000', '18.109581', '-77.297508', 'Kingston'),
(97, 'JP', 'JAPAN', 'Japan', 'JPN', 81, '0.00000', '36.204824', '138.252924', 'Tokyo'),
(98, 'JO', 'JORDAN', 'Jordan', 'JOR', 962, '0.00000', '30.585164', '36.238414', 'Amman'),
(99, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 7, '0.00000', '48.019573', '66.923684', 'Astana'),
(100, 'KE', 'KENYA', 'Kenya', 'KEN', 254, '0.00000', '-0.023559', '37.906193', 'Nairobi'),
(101, 'KS', 'KOSOVO', 'Kosovo', 'KSV', 38128, '0.00000', '42.602636', '20.902977', 'Pristina'),
(102, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 965, '0.00000', '29.31166', '47.481766', 'Kuwait City'),
(103, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 996, '0.00000', '41.20438', '74.766098', 'Bishkek'),
(104, 'LA', 'LAOS', 'Laos', 'LAO', 856, '0.00000', '19.85627', '102.495496', 'Vientiane'),
(105, 'LV', 'LATVIA', 'Latvia', 'LVA', 371, '21.00000', '56.879635', '24.603189', 'Riga'),
(106, 'LB', 'LEBANON', 'Lebanon', 'LBN', 961, '0.00000', '33.854721', '33.854721', 'Beirut'),
(107, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 266, '0.00000', '-29.609988', '28.233608', 'Maseru'),
(108, 'LR', 'LIBERIA', 'Liberia', 'LBR', 231, '0.00000', '6.428055', '-9.429499', 'Monrovia'),
(109, 'LY', 'LIBYA', 'Libya', 'LBY', 218, '0.00000', '26.3351', '17.228331', 'Tripoli'),
(110, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 423, '0.00000', '47.166', '9.555373', 'Vaduz'),
(111, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 370, '21.00000', '55.169438', '23.881275', 'Vilnius'),
(112, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 352, '17.00000', '49.815273', '6.129583', 'Luxembourg'),
(113, 'MO', 'MACAU', 'Macau', 'MAC', 853, '0.00000', '22.198745', '113.543873', ''),
(114, 'MK', 'MACEDONIA', 'Macedonia', 'MKD', 389, '0.00000', '41.608635', '21.745275', 'Skopje'),
(115, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 261, '0.00000', '-18.766947', '46.869107', 'Antananarivo'),
(116, 'MW', 'MALAWI', 'Malawi', 'MWI', 265, '0.00000', '-13.254308', '34.301525', 'Lilongwe'),
(117, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 60, '0.00000', '4.210484', '101.975766', 'Kuala Lumpur'),
(118, 'ML', 'MALI', 'Mali', 'MLI', 223, '0.00000', '17.570692', '-3.996166', 'Bamako'),
(119, 'MT', 'MALTA', 'Malta', 'MLT', 356, '18.00000', '35.937496', '14.375416', 'Valletta'),
(120, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 692, '0.00000', '7.131474', '171.184478', 'Majuro'),
(121, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 596, '0.00000', '14.641528', '-61.024174', ''),
(122, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 222, '0.00000', '21.00789', '-10.940835', 'Nouakchott'),
(123, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 230, '0.00000', '-20.348404', '57.552152', 'Port Louis'),
(124, 'MX', 'MEXICO', 'Mexico', 'MEX', 52, '0.00000', '23.634501', '-102.552784', 'Mexico City'),
(125, 'FM', 'MICRONESIA', 'Micronesia', 'FSM', 691, '0.00000', '7.4256', '150.5508', ''),
(126, 'MD', 'MOLDOVA', 'Moldova', 'MDA', 373, '0.00000', '47.411631', '28.369885', 'Chisinau'),
(127, 'MC', 'MONACO', 'Monaco', 'MCO', 377, '0.00000', '43.750298', '7.412841', 'Monaco'),
(128, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 976, '0.00000', '46.862496', '103.846656', 'Ulaanbaatar'),
(129, 'ME', 'MONTENEGRO', 'Montenegro', 'MNE', 382, '0.00000', '42.708678', '19.37439', 'Podgorica'),
(130, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 1664, '0.00000', '16.742498', '-62.187366', ''),
(131, 'MA', 'MOROCCO', 'Morocco', 'MAR', 212, '0.00000', '31.791702', '-7.09262', 'Rabat'),
(132, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 258, '0.00000', '-18.665695', '35.529562', 'Maputo'),
(133, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 95, '0.00000', '21.913965', '95.956223', 'Rangoon'),
(134, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 264, '0.00000', '-22.95764', '18.49041', 'Windhoek'),
(135, 'NP', 'NEPAL', 'Nepal', 'NPL', 977, '0.00000', '28.394857', '84.124008', 'Kathmandu'),
(136, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 31, '21.00000', '52.132633', '5.291266', 'Amsterdam'),
(137, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 687, '0.00000', '-20.904305', '165.618042', ''),
(138, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 64, '0.00000', '-40.900557', '174.885971', 'Wellington'),
(139, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 505, '0.00000', '12.865416', '-85.207229', 'Managua'),
(140, 'NE', 'NIGER', 'Niger', 'NER', 227, '0.00000', '17.607789', '8.081666', 'Niamey'),
(141, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 234, '0.00000', '9.081999', '8.675277', 'Abuja'),
(142, 'MP', 'NO. MARIANA ISLANDS', 'No. Mariana Islands', 'MNP', 1670, '0.00000', '17.33083', '145.38469', ''),
(143, 'KP', 'NORTH KOREA', 'North Korea', 'PRK', 850, '0.00000', '40.339852', '127.510093', ''),
(144, 'NO', 'NORWAY', 'Norway', 'NOR', 47, '0.00000', '60.472024', '8.468946', 'Oslo'),
(145, 'OM', 'OMAN', 'Oman', 'OMN', 968, '0.00000', '21.512583', '55.923255', 'Muscat'),
(146, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 92, '0.00000', '30.375321', '69.345116', 'Islamabad'),
(147, 'PW', 'PALAU', 'Palau', 'PLW', 680, '0.00000', '7.51498', '134.58252', 'Melekeok'),
(148, 'PS', 'PALESTINIAN AUTHORITY', 'Palestinian Authority', 'PSE', 970, '0.00000', '31.952162', '35.233154', ''),
(149, 'PA', 'PANAMA', 'Panama', 'PAN', 507, '0.00000', '8.537981', '-80.782127', 'Panama City'),
(150, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 595, '0.00000', '-23.442503', '-58.443832', 'Asuncion'),
(151, 'PE', 'PERU', 'Peru', 'PER', 51, '0.00000', '-9.189967', '-75.015152', 'Lima'),
(152, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 63, '0.00000', '12.879721', '121.774017', 'Manila'),
(153, 'PL', 'POLAND', 'Poland', 'POL', 48, '23.00000', '51.919438', '19.145136', 'Warsaw'),
(154, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 351, '23.00000', '39.399872', '-8.224454', 'Lisbon'),
(155, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 1787, '0.00000', '18.220833', '-66.590149', ''),
(156, 'QA', 'QATAR', 'Qatar', 'QAT', 974, '0.00000', '25.354826', '51.183884', 'Doha'),
(157, 'RE', 'REUNION ISLAND', 'Reunion Island', 'REU', 262, '0.00000', '-21.115141', '55.536384', ''),
(158, 'RO', 'ROMANIA', 'Romania', 'ROM', 40, '20.00000', '45.943161', '24.96676', 'Bucharest'),
(159, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 70, '0.00000', '61.52401', '105.318756', ''),
(160, 'RW', 'RWANDA', 'Rwanda', 'RWA', 250, '0.00000', '-1.940278', '29.873888', 'Kigali'),
(161, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 378, '0.00000', '43.94236', '12.457777', 'San Marino'),
(162, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 966, '0.00000', '23.885942', '45.079162', 'Riyadh'),
(163, 'SN', 'SENEGAL', 'Senegal', 'SEN', 221, '0.00000', '14.497401', '-14.452362', 'Dakar'),
(164, 'RS', 'SERBIA', 'Serbia', 'SRB', 381, '0.00000', '44.016521', '21.005859', 'Belgrade'),
(165, 'SC', 'SEYCHELLES ISLANDS', 'Seychelles Islands', 'SYC', 248, '0.00000', '-4.679574', '55.491977', ''),
(166, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 232, '0.00000', '8.460555', '-11.779889', 'Freetown'),
(167, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 65, '0.00000', '1.352083', '103.819836', 'Singapore'),
(168, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 421, '20.00000', '48.669026', '19.699024', 'Bratislava'),
(169, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 386, '22.00000', '46.151241', '14.995463', 'Ljubljana'),
(170, 'SO', 'SOMALIA', 'Somalia', 'SOM', 252, '0.00000', '5.152149', '46.199616', 'Mogadishu'),
(171, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 27, '0.00000', '-30.559482', '22.937506', 'Cape Town'),
(172, 'KR', 'SOUTH KOREA', 'South Korea', 'KOR', 82, '0.00000', '35.907757', '127.766922', 'Seoul'),
(173, 'SS', 'SOUTH SUDAN', 'South Sudan', 'SSD', 211, '0.00000', '6.8770', '31.3070', 'Juba'),
(174, 'ES', 'SPAIN', 'Spain', 'ESP', 34, '21.00000', '40.463667', '-3.74922', 'Madrid'),
(175, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 94, '0.00000', '7.873054', '80.771797', 'Colombo'),
(176, 'KN', 'ST. KITTS', 'St. Kitts', 'KNA', 1869, '0.00000', '17.3578', '62.7830', 'Basseterre'),
(177, 'LC', 'ST. LUCIA', 'St. Lucia', 'LCA', 1758, '0.00000', '13.9094', '60.9789', 'Castries'),
(178, 'MF', 'ST. MARTIN', 'St. Martin', 'MAF', 1721, '0.00000', '18.0708', '63.0501', ''),
(179, 'PM', 'ST. PIERRE & MIQUELON', 'St. Pierre & Miquelon', 'SPM', 508, '0.00000', '46.8852', '56.3159', ''),
(180, 'VC', 'ST. VINCENT', 'St. Vincent', 'VCT', 1784, '0.00000', '12.9843', '61.2872', 'Kingstown'),
(181, 'SD', 'SUDAN', 'Sudan', 'SDN', 249, '0.00000', '12.862807', '30.217636', 'Khartoum'),
(182, 'SR', 'SURINAME', 'Suriname', 'SUR', 597, '0.00000', '3.919305', '-56.027783', 'Paramaribo'),
(183, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 268, '0.00000', '-26.522503', '31.465866', 'Mbabane'),
(184, 'SE', 'SWEDEN', 'Sweden', 'SWE', 46, '25.00000', '60.128161', '18.643501', 'Stockholm'),
(185, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 41, '0.00000', '46.818188', '8.227512', 'Bern'),
(186, 'SY', 'SYRIA', 'Syria', 'SYR', 963, '0.00000', '34.802075', '38.996815', 'Damascus'),
(187, 'TW', 'TAIWAN', 'Taiwan', 'TWN', 886, '0.00000', '23.69781', '120.960515', 'Taipei'),
(188, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 992, '0.00000', '38.861034', '71.276093', 'Dushanbe'),
(189, 'TZ', 'TANZANIA', 'Tanzania', 'TZA', 255, '0.00000', '-6.369028', '34.888822', 'Dar es Salaam'),
(190, 'TH', 'THAILAND', 'Thailand', 'THA', 66, '0.00000', '15.870032', '100.992541', 'Bangkok'),
(191, 'TG', 'TOGO', 'Togo', 'TGO', 228, '0.00000', '8.619543', '0.824782', 'Lome'),
(192, 'TT', 'TRINIDAD & TOBAGO', 'Trinidad & Tobago', 'TTO', 1868, '0.00000', '10.691803', '-61.222503', 'Port-of-Spain'),
(193, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 216, '0.00000', '33.886917', '9.537499', 'Tunis'),
(194, 'TR', 'TURKEY', 'Turkey', 'TUR', 90, '0.00000', '38.963745', '35.243322', 'Ankara'),
(195, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 7370, '0.00000', '38.969719', '59.556278', 'Ashgabat'),
(196, 'TC', 'TURKS & CAICOS ISLANDS', 'Turks & Caicos Islands', 'TCA', 1649, '0.00000', '21.694025', '-71.797928', ''),
(197, 'UG', 'UGANDA', 'Uganda', 'UGA', 256, '0.00000', '1.373333', '32.290275', 'Kampala'),
(198, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 380, '0.00000', '48.379433', '31.16558', 'Kyiv'),
(199, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 971, '0.00000', '23.424076', '53.847818', 'Abu Dhabi'),
(200, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 44, '20.00000', '55.378051', '-3.435973', 'London'),
(201, 'UY', 'URUGUAY', 'Uruguay', 'URY', 598, '0.00000', '-32.522779', '-55.765835', 'Montevideo'),
(202, 'VI', 'US VIRGIN ISLANDS', 'Us Virgin Islands', 'VIR', 1340, '0.00000', '18.335765', '-64.896335', ''),
(203, 'US', 'USA', 'Usa', 'USA', 1, '0.00000', '37.09024', '-95.712891', ''),
(204, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 998, '0.00000', '41.377491', '64.585262', 'Tashkent'),
(205, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 58, '0.00000', '6.42375', '-66.58973', 'Caracas'),
(206, 'VN', 'VIETNAM', 'Vietnam', 'VNM', 84, '0.00000', '14.058324', '108.277199', 'Hanoi'),
(207, 'YE', 'YEMEN', 'Yemen', 'YEM', 967, '0.00000', '15.552727', '48.516388', 'Sanaa'),
(208, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 260, '0.00000', '-13.133897', '27.849332', 'Lusaka'),
(209, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 263, '0.00000', '-19.015438', '29.154857', 'Harare'),
(210, 'AQ', 'ANTARCTICA', 'Antarctica', 'ATA', 672, '0.00000', '', '', ''),
(211, 'A', 'ASCENSION', 'Ascension', 'ASC', 247, '0.00000', '', '', ''),
(212, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 682, '0.00000', '', '', ''),
(213, 'TL', 'EAST TIMOR', 'EastTimor', 'TLS', 670, '0.00000', '', '', ''),
(214, 'FK', 'FALKLAND ISLANDS', 'FalklandIslands', 'FLK', 500, '0.00000', '', '', ''),
(215, 'GL', 'GREENLAND', 'GreenLand', 'GRL', 299, '0.00000', '', '', ''),
(216, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 686, '0.00000', '', '', ''),
(217, 'MV', 'MALDIVES', 'Maldives', 'MDV', 960, '0.00000', '', '', ''),
(218, 'NR', 'NAURU', 'Nauru', 'NRU', 674, '0.00000', '', '', ''),
(219, 'NU', 'NIUE', 'Niue', 'NIU', 683, '0.00000', '', '', ''),
(220, '44', 'hgfh', 'fdg', 'f44', 44, '114.00000', '141', '141', 'ffd'),
(221, '5', 'oi', 'iiiiiio', '54', 45, '11.22000', '44.22', '44.77', 'piop');

ALTER TABLE `countrycode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_key` (`iso`);

ALTER TABLE `countrycode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Create ratedeck table
--
CREATE TABLE `ratedeck` (
  `id` int(11) NOT NULL,
  `destination` varchar(80) NOT NULL,
  `country_id` int(11) NOT NULL,
  `pattern` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1 = Disabled / Inactive / False / No , 0 = Enable / Active / True / Yes,2->Deleted',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `ratedeck`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ratedeck`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Create menu entry for ratedeck 
--  
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Ratedeck', 'ratedeck', 'ratedeck/ratedeck_list/', 'Tariff', 'Routes.png', '0', '92'); 

--
-- Set permission for ratedeck
--  
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;  

--
-- CDRS Archive feature queries
--
INSERT INTO `system` (`id`, `name`, `value`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`,`field_type`,`display_name`) VALUES (NULL, 'enable_database', '1', 'Set enable to enable this feature and disable to disable it', NULL, '', '', 'database','enable_disable_option','Archive flag');
INSERT INTO `system` (`id`, `name`, `value`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`,`field_type`,`display_name`) VALUES (NULL, 'default_database_type', '0', 'Select period of archive cdrs.', NULL, '', '', 'database','default_system_type','Archive Type');
INSERT INTO `default_templates` (`id`, `name`, `subject`, `template`, `last_modified_date`, `reseller_id`) VALUES (NULL, 'new_archive_table_create', 'New archive table create #TABLE_NAME#', 'Hello,

New archive table create successfully to move old archive record.

Table name: #TABLE_NAME#', '0000-00-00 00:00:00.000000', '0');

--
-- System table queries
--

INSERT INTO system (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `reseller_id`, `brand_id`, `group_title`) VALUES  (NULL, "currency_conv_api_key", "API key for currency rate import from 1forge.com", "YourKeyGoesHere", "default_system_input", "", 0, 0, "global");

INSERT INTO system (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `reseller_id`, `brand_id`, `group_title`) VALUES  (NULL, "currency_conv_loss_pct", "Currency Conversion Loss Percentage", 20, "default_system_input", "What percentage to allow for currency conversion losses", 0, 0, "global");


--
-- Purge Module query
--

INSERT INTO `system` ( `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ( 'purge_accounts_deleted', 'Deleted Accounts After Days', '-1', 'default_system_input', 'Here -1 means disable and any positive value means that much of days', NULL, '', '', 'purge'); 


INSERT INTO `system` ( `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ( 'purge_accounts_expired', 'Expired Accounts After Days', '-1', 'default_system_input', 'Here -1 means disable and any positive value means that much of days', NULL, '', '', 'purge');

INSERT INTO `system` ( `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ( 'purge_invoices', 'Invoices/Receipts Older Than Days', '-1', 'default_system_input', 'Here -1 means disable and any positive value means that much of days', NULL, '0', '0', 'purge');


INSERT INTO `system` ( `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ( 'purge_emails', 'Emails Older Than Days', '-1', 'default_system_input', 'Here -1 means disable and any positive value means that much of days', NULL, '0', '0', 'purge');


INSERT INTO `system` ( `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ('purge_cdrs', 'CDRs Older Than Days', '-1', 'default_system_input', 'Here -1 means disable and any positive value means that much of days', NULL, '0', '0', 'purge');



INSERT INTO `system` ( `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ( 'purge_audio_log', 'Audit Logs Older Than Days', '-1', 'default_system_input', 'Here -1 means disable and any positive value means that much of days', NULL, '0', '0', 'purge');


INSERT INTO `system` ( `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES ( 'purge_recordings', 'Recording Files Older Than Days', '-1', 'default_system_input', 'Here -1 means disable and any positive value means that much of days', NULL, '0', '0', 'purge');

ALTER TABLE `accounts` ADD `deleted_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'; 

--
-- Permission module changes
--
INSERT INTO `menu_modules` ( `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES ('Roles & Permissions ', 'permissions', '/permissions/permissions_list/', 'Accounts', '', '0', '10.5'); 
update userlevels set module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `permissions` text NOT NULL,
  `edit_permissions` longtext NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modification_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

ALTER TABLE `accounts` ADD `permission_id` INT(11) NOT NULL DEFAULT '0' AFTER `did_cid_translation`; 

--
-- Change default admin password
--
UPDATE `accounts` SET `password` ='8xbJv9wZmjA' WHERE `password`='drwcmIaIlzzUaQ9PwgOGRn2KcKmSq44tWvKGgHfkpl0';

--
-- Update to latest version 
--
UPDATE `system` SET `value` ='4.0 Beta' WHERE `system`.`id` = 191;


--
-- Routing strategy
--
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES (NULL, 'trunk_count', 'Trunk Count', '3', 'default_system_input', 'Priority trunk count', NULL, '0', '0', 'global');

ALTER TABLE `routes` CHANGE `trunk_id` `trunk_id` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT 'Trunk id for force routing';

ALTER TABLE `pricelists` ADD `call_count` INT(11) NOT NULL DEFAULT '0' AFTER `last_modified_date`;

ALTER TABLE `routing` ADD `routes_id` INT(11) NOT NULL DEFAULT '0' AFTER `trunk_id`, ADD `percentage` INT(3) NOT NULL AFTER `routes_id`, ADD `call_count` INT(11) NOT NULL DEFAULT '0' AFTER `percentage`;

ALTER TABLE `routes` ADD `routing_type` INT(11) DEFAULT NULL AFTER `last_modified_date`, ADD `percentage` VARCHAR(50) NOT NULL AFTER `routing_type`, ADD `call_count` INT(11) NOT NULL DEFAULT '0' AFTER `percentage`;


ALTER TABLE `routing` CHANGE `percentage` `percentage` VARCHAR(50) NOT NULL;
-- ALTER TABLE `routes` CHANGE `routing_type` `routing_type` INT(11) NULL DEFAULT NULL;
ALTER TABLE `routes` CHANGE `routing_type` `routing_type` VARCHAR(50) NULL DEFAULT NULL;

ALTER TABLE `pricelists` ADD `routing_prefix` VARCHAR(8) NOT NULL AFTER `markup`;

ALTER TABLE `accounts` ADD `loss_less_routing` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_recording`;

ALTER TABLE `routes` CHANGE `trunk_id` `trunk_id` VARCHAR(50) DEFAULT NULL COMMENT 'Trunk id for force routing';

update routes set trunk_id="" where trunk_id=0;

ALTER TABLE `pricelists` ADD `pricelist_id_admin` INT(11) NOT NULL DEFAULT '0' AFTER `id`;


-- NON CLI Rate group queries:
ALTER TABLE `accounts` ADD `non_cli_pricelist_id` INT(11) NOT NULL DEFAULT '0' AFTER `pricelist_id`;
ALTER TABLE `accounts` ADD `cid_pool` INT(11) NULL DEFAULT NULL AFTER `non_cli_pricelist_id`;

-- Create trigger for CLI NONCLI Rategroup
DELIMITER $$
CREATE TRIGGER `customer_reseller_insert` BEFORE INSERT ON `accounts`
 FOR EACH ROW BEGIN
   DECLARE callerid_pool INT(11);
   if NEW.reseller_id > 0
   then
	select cid_pool INTO callerid_pool from accounts where id=NEW.reseller_id;
        SET NEW.cid_pool=callerid_pool;
   end if;
END$$
DELIMITER ;

/* Password changes by harsh s begin */

INSERT INTO `system` (`name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`)
VALUES ('password_type', 'Set Password Security Type', '0', 'default_password_input', 'Set Password Security Type For New Password Creation', NULL, 0, 0, 'global');

/* Password changes by harsh s complete */
--
--Paypal Changes
--
UPDATE `menu_modules` SET `module_name` = 'configuration' WHERE `menu_modules`.`id` =61;

UPDATE `userlevels` SET `module_permissions` = CONCAT( `module_permissions` , '', ',61' ) WHERE `userlevelid` = '1';
--
--Local Number Changes
--


CREATE TABLE `local_number` (
  `id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `number` varchar(30) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0:active,1:inactive',
  `created_date` datetime NOT NULL,
  `last_modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

	ALTER TABLE `local_number`
	  ADD PRIMARY KEY (`id`);


	ALTER TABLE `local_number`
	  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


	CREATE TABLE `local_number_destination` (
	  `id` int(11) NOT NULL,
	  `local_number_id` int(11) NOT NULL DEFAULT '0',
	  `account_id` int(11) NOT NULL DEFAULT '0',
	  `destination_name` varchar(50) DEFAULT NULL,
	  `destination_number` varchar(50) DEFAULT NULL,
	  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `last_modified_date` datetime NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;


	ALTER TABLE `local_number_destination`
	  ADD PRIMARY KEY (`id`);


	ALTER TABLE `local_number_destination`
	  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

	INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES ('', 'Local Number', 'local', 'local_number/local_number_list/', 'DIDs', '', '0', 40.7);

	UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;
	UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 1;

	INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES ('', 'Local Number', 'local', 'local_number/user_local_number_list/', 'DIDs', '', '0', 2.2);

	UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = 0;

ALTER TABLE `speed_dial` CHANGE `speed_num` `speed_num` VARCHAR(30) NOT NULL; 


INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Cron Settings', 'cronsettings', 'cronsettings/cronsettings_list/', 'Configuration', '', '0', '80.6');
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

CREATE TABLE `cron_settings` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `interval_type` int(11) NOT NULL,
  `interval` varchar(50) DEFAULT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_execution_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `next_execution_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `file_path` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `cron_settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cron_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


/*Update the query for tooltip for system table*/
UPDATE `system` SET `comment` = 'Set your API Key for currency rate' WHERE name='currency_conv_api_key' AND group_title='global';
/*Changes By sonal mali*/


/*add by pratik */

-- System Table Make dropdown in Signup for Default Tax
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES
('', 'tax_type', 'Default Tax', '', 'tax_type', 'Set Default taxes for tax_description', NULL, 0, 0, 'Signup');

-- Tax table For tax type entry
ALTER TABLE `taxes` ADD `tax_type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0:Default,1:Other' AFTER `taxes_amount`;
/*change by pratik*/

-- Add currency_id in Countrycode table for display currency in grid field 

ALTER TABLE `countrycode` ADD `currency_id` INT NOT NULL AFTER `id`;



/*localization module*/
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Localization', 'localization', 'localization/localization_list/', 'Configuration', '', '0', '80.6');
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

ALTER TABLE `accounts` ADD `localization_id` INT(11) NULL AFTER `deleted_date`; 

CREATE TABLE `localization` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `account_id` int(11) NOT NULL,
  `reseller_id` int(11) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `in_caller_id_originate` varchar(200) DEFAULT NULL,
  `out_caller_id_originate` varchar(200) DEFAULT NULL,
  `number_originate` varchar(200) DEFAULT NULL,
  `in_caller_id_terminate` varchar(200) DEFAULT NULL,
  `out_caller_id_terminate` varchar(200) DEFAULT NULL,
  `number_terminate` varchar(200) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `localization`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `localization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
/*change by ekta for reseller list in admin*/

UPDATE `menu_modules` SET `module_url` = 'invoices/invoice_conf_list/' WHERE `menu_modules`.`id` = 9;


ALTER TABLE `accounts` ADD `vat_no` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `non_cli_pricelist_id`;
ALTER TABLE `accounts` ADD `reference` INT(100) NOT NULL AFTER `vat_no`;
ALTER TABLE `accounts` ADD `paypal_permission` TINYINT(2) NOT NULL COMMENT '0:enable,1:disable' AFTER `reference`;
ALTER TABLE `accounts` ADD `invoice_note` TEXT NOT NULL AFTER `invoice_day`;
ALTER TABLE `accounts` ADD `invoice_interval` INT(11) NOT NULL AFTER `invoice_day`;


/* ekta end*/
/* ekta chANGE for plan module*/

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Plans', 'plans', 'plans/plans_list/', 'Tariff', 'Routes.png', '0', '93');

UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `category_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci,
  `price` decimal(11,2) UNSIGNED DEFAULT NULL,
  `included_seconds` int(11) DEFAULT NULL,
  `time_created` datetime DEFAULT '0000-00-00 00:00:00',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `country_id` int(11) NOT NULL DEFAULT '0',
  `duration` tinyint(2) NOT NULL COMMENT '1 for daily, 7 for weekly,15 for half weekly ,30 for monthly',
  `renewal_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:One time, 1:Recurring',
  `applicable_for` tinyint(2) NOT NULL,
  `apply_tax` tinyint(4) NOT NULL COMMENT '1 for Yes, 0 for No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `plans_to_account` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `plan_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `plan_release` tinyint(1) NOT NULL DEFAULT '0',
  `refill_coupon_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `plans_to_account`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `plans_to_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `plan_orders` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL DEFAULT '0',
  `plan_to_account_id` int(11) NOT NULL DEFAULT '0',
  `seconds` varchar(20) NOT NULL DEFAULT '0',
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `included_seconds` int(11) NOT NULL DEFAULT '0',
  `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `label` varchar(100) NOT NULL,
  `renewal_type` varchar(100) NOT NULL,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `account_number` varchar(20) NOT NULL DEFAULT '0',
  `assign_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `plan_upto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `renew_count` int(11) NOT NULL DEFAULT '0',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `order_status` int(11) NOT NULL DEFAULT '0',
  `expired_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `price_with_tax` decimal(20,5) NOT NULL,
  `price_without_tax` decimal(20,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `plan_orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `plan_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `plans` CHANGE `label` `label` TINYINT(0) NULL DEFAULT NULL COMMENT '0 for subscribtio,1 for package,2 for topup';
/*ekta end*/



/*Add the following query for call barrings module*/
/*Start of call barring module*/

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Call Barring', 'callbarring', 'callbarring/callbarring_list/', 'Configuration', '', '0', '80.6');
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

CREATE TABLE `call_barring` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `number` varchar(100) DEFAULT NULL,
  `number_type` tinyint(1) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `destination` tinyint(1) DEFAULT '0',
  `action_type` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT NULL,
  `creation_date` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `call_barring`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `call_barring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
/*End of call barring module*/




ALTER TABLE `ratedeck` ADD `call_type` VARCHAR(50) NOT NULL AFTER `pattern`;


INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Plan Usage Report', 'plans', 'plans/plans_counter/', 'Tariff', 'Routes.png', '0', '94');
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

ALTER TABLE `counters` ADD `plan_id` INT(4) NOT NULL DEFAULT '0' AFTER `id`;


/*Starting of SMS and alert*/
INSERT INTO `system` (`id`, `name`, `display_name`, `value`, `field_type`, `comment`, `timestamp`, `reseller_id`, `brand_id`, `group_title`) VALUES 
(NULL, 'sms_notications', 'SMS Notifications', '0', 'enable_disable_option', 'Set Enable To Use SMS Notification ', NULL, 0, 0, 'sms'),
(NULL, 'alert_notications', 'Alert Notifications', '0', 'enable_disable_option', 'Set Enable To Use Alert Notification ', NULL, 0, 0, 'alert'),
(NULL, 'sms_api_key', 'SMS API Key', '', 'default_system_input', 'Set your API Key for SMS', NULL, 0, 0, 'sms'),
(NULL, 'sms_secret_key', 'SMS Secret Key', '', 'default_system_input', 'Set your API Secret Key ', NULL, 0, 0, 'sms'),
(NULL, 'sms_content', 'SMS Body', '', 'default_system_input', 'Set your SMS Body', NULL, 0, 0, 'sms');
/*Ending of SMS and alert*/

/*Starting of default templates*/
ALTER TABLE `default_templates` ADD `email_template` VARCHAR(500) NOT NULL AFTER `subject`;
ALTER TABLE `default_templates` ADD `sms_template` VARCHAR(500) NOT NULL AFTER `email_template`, ADD `alert_template` VARCHAR(500) NOT NULL AFTER `sms_template`;
ALTER TABLE `default_templates` ADD `is_email_enable` TINYINT(1) NOT NULL DEFAULT '0' AFTER `reseller_id`, ADD `is_sms_enable` TINYINT(1) NOT NULL AFTER `is_email_enable`, ADD `is_alert_enable` TINYINT(1) NOT NULL AFTER `is_sms_enable`, ADD `status` TINYINT(1) NOT NULL AFTER `is_alert_enable`; 

UPDATE `default_templates` SET `sms_template` = 'Dear [first_name], You have successfully paid $[amount] into your Lugertel account. Here is your receipt. [Payment Details] Thank You. LUGERTEL INC.' WHERE `default_templates`.`name` = 'voip_account_refilled';
UPDATE `default_templates` SET `sms_template` = 'Dear [First_name], Thank you for signing up with LugerTel, a convenience you can trust. Your username is your phone number or email address associated to this account. We offer Bill pay Services and Mobile Top Ups. Thank You. LUGERTEL INC.' WHERE `default_templates`.`name` = 'email_add_user';
UPDATE `default_templates` SET `sms_template` = 'Dear [first name]: You currently have $[amount] left in your account. Please make a deposit to avoid service interruptions. You can recharge your account using the app or on the website or via phone using our IVR systems. Thank You. LUGERTEL INC.' WHERE `default_templates`.`name` = 'email_low_balance'; 
UPDATE `default_templates` SET `sms_template` = 'Dear [first_name]: As per your request, here is your password information at [url] : username:[user_name] password:[password] Thank You. LUGERTEL INC.' WHERE `default_templates`.`name` ='email_forgot_user';


INSERT INTO `default_templates` (`id`, `name`, `subject`, `email_template`, `sms_template`, `alert_template`, `template`, `last_modified_date`, `reseller_id`, `is_email_enable`, `is_sms_enable`, `is_alert_enable`, `status`) VALUES 
(NULL, 'bill_pay', 'Bill Pay', '', 'Dear [first_name], \nYou have successfully paid $[amount] for Bill Pay Services. Here is your receipt.\n[Payment Details] \n\nThank You.\nLUGERTEL INC.\n', '', '', '0000-00-00 00:00:00', 0, 0, 0, 0, 0),
(NULL, 'otp_notification', 'OTP Notification ', '', 'As a part of our security measures, we have enabled the use of an OTP (One-Time Password) for your current session. \r\n\r\nThe OTP to access your account is [userpassword] \r\n\r\nPlease note that this password will be expired in 5 minutes from the time that is sent.\r\n\r\nThank You\r\nLUGERTEL System Administrator', '', '', '0000-00-00 00:00:00', 0, 0, 0, 0, 0),
(NULL, 'balance_tranfer', 'Balance Transfer', '', 'Dear [first_name]:\r\n\r\nYou have transferred $[amount] from your account to [account#]\r\n\r\nThank You.\r\nLUGERTEL INC.', '', '', '0000-00-00 00:00:00', 0, 0, 0, 0, 0),
(NULL, 'contact_us', 'Contact Us', '', 'Link the contact us page on website please \r\nSee http://www.lugertel.com/estore/ContactUs.aspx', '', '', '0000-00-00 00:00:00', 0, 0, 0, 0, 0),
(NULL, 'verification_code', 'Verification Code', '', 'Dear [first_name],\n\nThank you for Signing up with us. Below is your verification code. \n\n[code]\n\nPlease enter your verification code to complete the registration. \n\nThank You.\nLUGERTEL INC.', '', '', '0000-00-00 00:00:00', 0, 0, 0, 0, 0); 

/*Ending of default templates*/


/*Starting of Call type module*/
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Call Type', 'calltype', 'calltype/calltype_list/', 'Tariff', 'packages.png', '0', '55');
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `calltype` (
  `id` int(11) NOT NULL,
  `call_type` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `calltype`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `calltype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
  
/*Ending of Call type module*/



/*Starting of Access Number*/
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Access Number', 'accessnumber', 'accessnumber/accessnumber_list/', 'DIDs', '', '0', '76.2');
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

CREATE TABLE `accessnumber` (
  `id` int(4) NOT NULL,
  `access_number` varchar(25) DEFAULT NULL,
  `country_id` varchar(3) NOT NULL DEFAULT '0',
  `description` varchar(1000) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `accessnumber`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `accessnumber` CHANGE `id` `id` INT(4) NOT NULL AUTO_INCREMENT; 

/*Ending of Access Number*/

/*Starting of Activity Report*/
INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(NULL, 'Activity Report', 'activity', 'activity_report/activity_report_list/', 'Call Reports', '', '0', 75.2);
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

/*Ending of Activity Report*/


INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Products', 'products', 'products/products_list/', 'Tariff', 'Routes.png', '0', 93);
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES (NULL, 'Orders', 'orders', 'orders/orders_list/', 'Tariff', 'Routes.png', '0', 94);
UPDATE userlevels SET module_permissions = concat( module_permissions, ',', (  SELECT max( id ) FROM menu_modules ) ) WHERE userlevelid = -1;

ALTER TABLE `routes` ADD `country_id` VARCHAR(20) NOT NULL AFTER `pricelist_id`;
ALTER TABLE `routes` ADD `call_type` VARCHAR(20) NOT NULL AFTER `country_id`;


CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `order_date` datetime NOT NULL,
  `order_generated_by` int(11) NOT NULL,
  `payment_gateway` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
  
  
  
 DROP TABLE invoices;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `prefix` varchar(25) NOT NULL,
  `number` varchar(255) NOT NULL,
  `accountid` int(11) NOT NULL DEFAULT '0',
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `from_date` datetime NOT NULL,
  `to_date` datetime NOT NULL,
  `due_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:paid,1:unpaid,2:partial_payment',
  `generate_date` datetime NOT NULL,
  `type` enum('I','R') NOT NULL DEFAULT 'I' COMMENT 'I => Invoice R=> Receipt',
  `amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `payment_id` int(11) NOT NULL,
  `generate_type` int(10) NOT NULL DEFAULT '0' COMMENT '0:Auto 1:manually',
  `confirm` int(10) DEFAULT '0' COMMENT '0:not conform 1:conform',
  `notes` longtext NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Not delete 1:delete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accountid` (`accountid`);


ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  
  
 DROP TABLE invoice_details;

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL DEFAULT '0',
  `invoiceid` int(11) NOT NULL DEFAULT '0',
  `order_item_id` varchar(25) NOT NULL DEFAULT '0',
  `product_category` int(11) NOT NULL DEFAULT '0',
  `is_tax` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 FOR NO AND 1 FOR YES',
  `description` varchar(255) NOT NULL,
  `debit` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `credit` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `base_currency` varchar(3) NOT NULL,
  `exchange_rate` decimal(10,5) NOT NULL DEFAULT '1.00000',
  `account_currency` varchar(3) NOT NULL,
  `created_date` datetime NOT NULL,
  `before_balance` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `after_balance` decimal(20,6) NOT NULL DEFAULT '0.000000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
  
  
 DROP TABLE payment_transaction;

CREATE TABLE `payment_transaction` (
  `id` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `amount` decimal(20,5) NOT NULL,
  `tax` varchar(10) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `actual_amount` decimal(20,5) NOT NULL,
  `paypal_fee` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `user_currency` varchar(50) NOT NULL,
  `currency_rate` decimal(10,5) NOT NULL COMMENT 'user currency rate against base currency rate',
  `transaction_details` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `payment_transaction`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payment_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
  
  
  CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `product_category` int(11) NOT NULL,
  `buy_cost` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `price` decimal(10,5) DEFAULT '0.00000',
  `can_resell` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for no,1 for yes',
  `commission` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `billing_type` tinyint(1) NOT NULL,
  `billing_days` int(11) NOT NULL DEFAULT '0',
  `free_minutes` int(11) NOT NULL DEFAULT '0',
  `apply_on_existing_account` tinyint(1) NOT NULL,
  `apply_on_rategroups` varchar(50) NOT NULL,
  `destination_rategroups` varchar(50) NOT NULL,
  `destination_countries` varchar(256) NOT NULL,
  `destination_calltypes` varchar(50) NOT NULL,
  `release_no_balance` tinyint(1) NOT NULL,
  `can_purchase` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for yes, 1 for no',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active,1 for inactive',
  `created_by` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `last_modified_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
  
  CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_category` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `price` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `billing_type` int(5) NOT NULL,
  `billing_days` int(11) NOT NULL DEFAULT '0',
  `free_minutes` int(11) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `billing_date` datetime NOT NULL,
  `next_billing_date` datetime NOT NULL,
  `is_terminated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 FOR NO AND 1 FOR YES',
  `termination_date` datetime NOT NULL,
  `from_currency` varchar(3) NOT NULL,
  `exchange_rate` decimal(10,5) NOT NULL DEFAULT '1.00000',
  `to_currency` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6; 
  
ALTER TABLE `package_patterns` CHANGE `package_id` `product_id` INT(4) NOT NULL;


CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `creation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `category` (`id`, `name`, `code`, `description`, `status`, `creation_date`) VALUES
(1, 'Package', 'PKG', 'Package', 0, '0000-00-00'),
(2, 'Subscription', 'SUB', 'Subscription', 0, '0000-00-00'),
(3, 'Refill', 'RFL', 'Rfill', 0, '0000-00-00');

ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
  
 CREATE TABLE `otp_number` (
  `id` int(11) NOT NULL,
  `otp_number` varchar(20) NOT NULL,
  `user_number` varchar(50) NOT NULL,
  `account_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:active 1:Inactive',
  `type` varchar(20) NOT NULL,
  `country_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `sms_status` varchar(255) NOT NULL,
  `sms_template` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `otp_number`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `otp_number`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88; 
  
ALTER TABLE `addons` ADD `files` BLOB NOT NULL AFTER `last_updated_date`;
ALTER TABLE `addons` DROP `license_key`;  
  

 
  
   


