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
if (! defined ( 'BASEPATH' )) {
	exit ( 'No direct script access allowed' );
}
class Timezone {
	function __construct() {
		$this->CI = & get_instance ();
		$this->CI->load->library ( 'session' );
		$this->CI->load->database ();
	}
	function uset_timezone() {
		$account_data = $this->CI->session->userdata ( 'accountinfo' );
		return $account_data ['timezone_id'];
	}
	function display_GMT($currDate, $fulldate = 1, $timezone_id = "") {
		$number = ($timezone_id == "") ? $this->uset_timezone () : $timezone_id;
		$SERVER_GMT = '0';
		
		$result = $this->CI->db->query ( "select gmtoffset from timezone where id =" . $number );
		$timezone_offset = $result->result ();
		
		$USER_GMT = $timezone_offset ['0']->gmtoffset;
		
		$date_time_array = getdate ( strtotime ( $currDate ) );
		
		$hours = $date_time_array ['hours'];
		$minutes = $date_time_array ['minutes'];
		$seconds = $date_time_array ['seconds'];
		$month = $date_time_array ['mon'];
		$day = $date_time_array ['mday'];
		$year = $date_time_array ['year'];
		$timestamp = mktime ( $hours, $minutes, $seconds, $month, $day, $year );
		
		$timestamp = $timestamp + ($USER_GMT - $SERVER_GMT);
		if ($fulldate == 1) {
			$date = date ( "Y-m-d H:i:s", $timestamp );
		} else {
			$date = date ( "Y-m-d", $timestamp );
		}
		
		return $date;
	}
	function convert_to_GMT($currDate, $fulldate = 1, $timezone_id = '') {
		$number = ($timezone_id == "") ? $this->uset_timezone () : $timezone_id;
		$SERVER_GMT = '0';
		$result = $this->CI->db->query ( "select gmtoffset from timezone where id =" . $number );
		$timezone_offset = $result->result ();
		$USER_GMT = $timezone_offset ['0']->gmtoffset;
		
		$date_time_array = getdate ( strtotime ( $currDate ) );
		$hours = $date_time_array ['hours'];
		$minutes = $date_time_array ['minutes'];
		$seconds = $date_time_array ['seconds'];
		$month = $date_time_array ['mon'];
		$day = $date_time_array ['mday'];
		$year = $date_time_array ['year'];
		$timestamp = mktime ( $hours, $minutes, $seconds, $month, $day, $year );
		$timestamp = $timestamp - ($SERVER_GMT - $USER_GMT);

		if ($fulldate == 1) {
			$date = date ( "Y-m-d H:i:s", $timestamp );
		} else {
			$date = date ( "Y-m-d", $timestamp );
		}
		return $date;
	}
}
?>
