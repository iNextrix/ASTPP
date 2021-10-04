<?php

// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
// ASTPP Version 3.0 and above
// License https://www.gnu.org/licenses/agpl-3.0.html
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ##############################################################################
class Upgrade_v4_to_v5_community extends MX_Controller {
    function __construct() {
        parent::__construct ();
        $this->load->model ( "db_model" );
        $this->load->model ( "common_model" );
        $this->load->library ( "astpp/common" );
        $this->currentdate = gmdate ( 'Y-m-d H:i:s' );
    }
    function update_timezone() {
        if (!($this->db->table_exists('mytimezone'))){
            $create_query =  "CREATE TABLE `mytimezone` (
                `id` int NOT NULL,
                `gmtzone` varchar(255) DEFAULT NULL,
                `gmttime` varchar(255) DEFAULT NULL,
                `gmtoffset` bigint NOT NULL DEFAULT '0',
                `status` tinyint NOT NULL COMMENT '0-pending update, 1- updated'
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;";
            $query = $this->db->query($create_query);
            $create_query = "ALTER TABLE `mytimezone` ADD PRIMARY KEY (`id`);";
            $query = $this->db->query($create_query);
            $create_query = "ALTER TABLE `mytimezone`MODIFY `id` int NOT NULL AUTO_INCREMENT;";
            $query = $this->db->query($create_query);
        

            $query = "INSERT INTO `mytimezone` (`id`, `gmtzone`, `gmttime`, `gmtoffset`, `status`) VALUES
            (1, '(GMT-12:00) International Date Line West', 'GMT-12:00', -43200, 0),
            (2, '(GMT-11:00) Midway Island, Samoa', 'GMT-11:00', -39600, 0),
            (3, '(GMT-10:00) Hawaii', 'GMT-10:00', -36000, 0),
            (4, '(GMT-09:00) Alaska', 'GMT-09:00', -32400, 0),
            (5, '(GMT-08:00) Pacific Time (US & Canada) Tijuana', 'GMT-08:00', -28800, 0),
            (6, '(GMT-07:00) Arizona', 'GMT-07:00', -25200, 0),
            (7, '(GMT-07:00) Chihuahua, La Paz, Mazatlan', 'GMT-07:00', -25200, 0),
            (8, '(GMT-07:00) Mountain Time(US & Canada)', 'GMT-07:00', -25200, 0),
            (9, '(GMT-06:00) Central America', 'GMT-06:00', -21600, 0),
            (10, '(GMT-06:00) Central Time (US & Canada)', 'GMT-06:00', -21600, 0),
            (11, '(GMT-06:00) Guadalajara, Mexico City, Monterrey', 'GMT-06:00', -21600, 0),
            (12, '(GMT-06:00) Saskatchewan', 'GMT-06:00', -21600, 0),
            (13, '(GMT-05:00) Bogota, Lima, Quito', 'GMT-05:00', -18000, 0),
            (14, '(GMT-05:00) Eastern Time (US & Canada)', 'GMT-05:00', -18000, 0),
            (15, '(GMT-05:00) Indiana (East)', 'GMT-05:00', -18000, 0),
            (16, '(GMT-04:00) Atlantic Time (Canada)', 'GMT-04:00', -14400, 0),
            (17, '(GMT-04:00) Caracas, La Paz', 'GMT-04:00', -14400, 0),
            (18, '(GMT-04:00) Santiago', 'GMT-04:00', -14400, 0),
            (19, '(GMT-03:30) NewFoundland', 'GMT-03:30', -12600, 0),
            (20, '(GMT-03:00) Brasillia', 'GMT-03:00', -10800, 0),
            (21, '(GMT-03:00) Buenos Aires, Georgetown', 'GMT-03:00', -10800, 0),
            (22, '(GMT-03:00) Greenland', 'GMT-03:00', -10800, 0),
            (23, '(GMT-03:00) Mid-Atlantic', 'GMT-03:00', -10800, 0),
            (24, '(GMT-01:00) Azores', 'GMT-01:00', -3600, 0),
            (25, '(GMT-01:00) Cape Verd Is.', 'GMT-01:00', -3600, 0),
            (26, '(GMT+00:00) Casablanca, Monrovia', 'GMT+00:00', 0, 0),
            (27, '(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon,  London', 'GMT+00:00', 0, 0),
            (28, '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', 'GMT+01:00', 3600, 0),
            (29, '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague', 'GMT+01:00', 3600, 0),
            (30, '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris', 'GMT+01:00', 3600, 0),
            (31, '(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb', 'GMT+01:00', 3600, 0),
            (32, '(GMT+01:00) West Central Africa', 'GMT+01:00', 3600, 0),
            (33, '(GMT+02:00) Athens, Istanbul, Minsk', 'GMT+02:00', 7200, 0),
            (34, '(GMT+02:00) Bucharest', 'GMT+02:00', 7200, 0),
            (35, '(GMT+02:00) Cairo', 'GMT+02:00', 7200, 0),
            (36, '(GMT+02:00) Harare, Pretoria', 'GMT+02:00', 7200, 0),
            (37, '(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius', 'GMT+02:00', 7200, 0),
            (38, '(GMT+02:00) Jeruasalem', 'GMT+02:00', 7200, 0),
            (39, '(GMT+03:00) Baghdad', 'GMT+03:00', 10800, 0),
            (40, '(GMT+03:00) Kuwait, Riyadh', 'GMT+03:00', 10800, 0),
            (41, '(GMT+03:00) Moscow, St.Petersburg, Volgograd', 'GMT+03:00', 10800, 0),
            (42, '(GMT+03:00) Nairobi', 'GMT+03:00', 10800, 0),
            (43, '(GMT+03:30) Tehran', 'GMT+03:30', 12600, 0),
            (44, '(GMT+04:00) Abu Dhabi, Muscat', 'GMT+04:00', 14400, 0),
            (45, '(GMT+04:00) Baku, Tbillisi, Yerevan', 'GMT+04:00', 14400, 0),
            (46, '(GMT+04:30) Kabul', 'GMT+04:30', 16200, 0),
            (47, '(GMT+05:00) Ekaterinburg', 'GMT+05:00', 18000, 0),
            (48, '(GMT+05:00) Islamabad, Karachi, Tashkent', 'GMT+05:00', 18000, 0),
            (49, '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi', 'GMT+05:30', 19800, 0),
            (50, '(GMT+05:45) Kathmandu', 'GMT+05:45', 20700, 0),
            (51, '(GMT+06:00) Almaty, Novosibirsk', 'GMT+06:00', 21600, 0),
            (52, '(GMT+06:00) Astana, Dhaka', 'GMT+06:00', 21600, 0),
            (53, '(GMT+06:00) Sri Jayawardenepura', 'GMT+06:00', 21600, 0),
            (54, '(GMT+06:30) Rangoon', 'GMT+06:30', 23400, 0),
            (55, '(GMT+07:00) Bangkok, Hanoi, Jakarta', 'GMT+07:00', 25200, 0),
            (56, '(GMT+07:00) Krasnoyarsk', 'GMT+07:00', 25200, 0),
            (57, '(GMT+08:00) Beijiing, Chongging, Hong Kong, Urumqi', 'GMT+08:00', 28800, 0),
            (58, '(GMT+08:00) Irkutsk, Ulaan Bataar', 'GMT+08:00', 28800, 0),
            (59, '(GMT+08:00) Kuala Lumpur, Singapore', 'GMT+08:00', 28800, 0),
            (60, '(GMT+08:00) Perth', 'GMT+08:00', 28800, 0),
            (61, '(GMT+08:00) Taipei', 'GMT+08:00', 28800, 0),
            (62, '(GMT+09:00) Osaka, Sapporo, Tokyo', 'GMT+09:00', 32400, 0),
            (63, '(GMT+09:00) Seoul', 'GMT+09:00', 32400, 0),
            (64, '(GMT+09:00) Yakutsk', 'GMT+09:00', 32400, 0),
            (65, '(GMT+09:00) Adelaide', 'GMT+09:00', 32400, 0),
            (66, '(GMT+09:30) Darwin', 'GMT+09:30', 34200, 0),
            (67, '(GMT+10:00) Brisbane', 'GMT+10:00', 36000, 0),
            (68, '(GMT+10:00) Canberra, Melbourne, Sydney', 'GMT+10:00', 36000, 0),
            (69, '(GMT+10:00) Guam, Port Moresby', 'GMT+10:00', 36000, 0),
            (70, '(GMT+10:00) Hobart', 'GMT+10:00', 36000, 0),
            (71, '(GMT+10:00) Vladivostok', 'GMT+10:00', 36000, 0),
            (72, '(GMT+11:00) Magadan, Solomon Is., New Caledonia', 'GMT+11:00', 39600, 0),
            (73, '(GMT+12:00) Auckland, Wellington', 'GMT+1200', 43200, 0),
            (74, '(GMT+12:00) Fiji, Kamchatka, Marshall Is.', 'GMT+12:00', 43200, 0),
            (75, '(GMT+13:00) Nuku alofa', 'GMT+13:00', 46800, 0);";
            $query = $this->db->query($query);
           // print_r($this->db->last_query());die;
         
        }
        $accounts_data = $this->db_model->getSelect ('*','accounts',array())->result_array();
        foreach($accounts_data as $key=> $accounts_value){
            $old_timezone = $this->db_model->getSelect ('gmttime','mytimezone',array('id' =>$accounts_value['timezone_id']))->row_array()['gmttime'];
            if(isset($old_timezone) && $old_timezone != ""){
            $new_timezone = $this->db_model->getSelect ('id','timezone',array('gmttime' =>$old_timezone))->row_array();
             if(!empty($new_timezone)){
                $this->db->update ( "accounts", array (
                    "timezone_id" => $new_timezone['id'] ),array('id'=> $accounts_value ["id"]));
                }
            }
        }
    }
}
?> 
