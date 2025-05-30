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
class DailyBalanceReport extends CI_Controller {
	public function __construct() {
        	parent::__construct();
        	$this->load->library('email');
	}
	public function send_daily_balance_email(){
		$this->db->select('id,email,first_name,last_name,balance,posttoexternal,credit_limit,reseller_id');
		$customers =$this->db->get_where("accounts",array("deleted"=>0,"type"=>0))->result_array();
		if(!empty($customers)){
			$this->db->select('name,value');
			$this->db->where_in('name',array("base_currency",'decimal_points'));
			$system_info  = $this->db->get('system')->result_array();
			$base_currency= "";
			$decimal_points =0;
			foreach($system_info as $key=>$value){
				if($value['name'] == "base_currency"){
					$base_currency = $value['value'];
				}elseif($value['name'] == "decimal_points"){
					$decimal_points = $value['value'];
				}
			}
			$startGmt = new DateTime('yesterday 18:30:00', new DateTimeZone('UTC'));
			$nowGmt = new DateTime('now', new DateTimeZone('UTC'));
			$email_current_date = new DateTime("now", new DateTimeZone("Asia/Kolkata"));
			$date = $email_current_date->format("Y-m-d");
			$start_date = $startGmt->format('Y-m-d H:i:s');
			$end_date = $nowGmt->format("Y-m-d H:i:s");	
			$customer_array = array();
			$reseller_array = array();
			$invoice_conf_array = array();
			foreach ($customers as $key => $customer) {
				$customer_array[] = $customer['id'];
				$reseller_array[$customer['reseller_id']] =$customer['reseller_id'];
			}
			$reseller_array[1]=1;
			$this->db->select("accountid,sum(debit) as debit");
			$this->db->where('callstart >=',$start_date);
			$this->db->where('callstart <=',$end_date);
			$this->db->where_in("accountid",$customer_array);
			$this->db->group_by("accountid");
			$cdrs_result = $this->db->get('cdrs')->result_array();
			$cdrs_array = array();
			if(!empty($cdrs_result)){
				foreach($cdrs_result as $key=>$cdrs_value){
					$cdrs_array[$cdrs_value['accountid']] =$cdrs_value['debit'];
				}
			}
			foreach ($customers as $key => $customer) {
				$utilized_balance = !empty($cdrs_array[$customer['id']]) && isset($cdrs_array[$customer['id']]) ? $cdrs_array[$customer['id']] : "0";
				$customer['utilized_balance'] = $base_currency." ".number_format($utilized_balance,$decimal_points); // returns string "123.4568"
				$from = $customer['reseller_id'] > 0  && isset($invoice_conf_array[$customer['reseller_id']]) ? $invoice_conf_array[$customer['reseller_id']] : $invoice_conf_array[1];
				$current_balance = $customer['posttoexternal'] == 1 ? $customer['credit_limit'] - $customer['balance'] : $customer['balance'];
				$customer['current_balance'] = $base_currency." ".number_format($current_balance, 4);
				$customer['date'] = $date;
				$customer['email'] = "ankit.doshi@inextrix.com";
				$this->common->mail_to_users('daily_career_performance_report',$customer);
			}
			die;
		}
	}
}
?>
