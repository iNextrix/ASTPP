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
class dashboard extends CI_Controller {
	function dashboard() {
		parent::__construct ();
		$this->load->helper ( 'form' );
		$this->load->model ( 'Auth_model' );
		$this->load->library ( "astpp/form" );
		$this->load->model ( 'Astpp_common' );
		$this->load->model ( 'dashboard_model' );
		$this->load->library ( 'freeswitch_lib' );
	}
	function index() {
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . 'login/login' );
		$data ['page_title'] = gettext ( 'Dashboard' );
		if ($this->session->userdata ( 'logintype' ) == 0) {
			$this->load->view ( 'view_user_dashboard', $data );
		} else {
			$gmtoffset = $this->common->get_timezone_offset ();
			$this->load->view ( 'view_dashboard', $data );
		}
	}
	function user_recent_payments() {
		$this->customerReport_recent_payments ();
	}
	function customerReport_recent_payments() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency = $this->common->get_field_name ( 'currency', 'currency', array (
				"id" => $accountinfo ['currency_id'] 
		) );
		$json_data = array ();
		$i = 1;
		$result = $this->dashboard_model->get_recent_recharge ();
		$gmtoffset = $this->common->get_timezone_offset ();
		if ($result->num_rows () > 0) {
			$account_arr = $this->common->get_array ( 'id,number,first_name,last_name', 'accounts', '' );
			$json_data [0] ['accountid'] = 'Accounts';
			$json_data [0] ['credit'] = 'Amount(' . $currency . ")";
			$json_data [0] ['payment_date'] = 'Date';
			foreach ( $result->result_array () as $key => $data ) {
				$current_timestamp = strtotime ( $data ['payment_date'] );
				$modified_date = $current_timestamp + $gmtoffset;
				$data ['accountid'] = ($data ['accountid'] != '' && isset ( $account_arr [$data ['accountid']] )) ? $account_arr [$data ['accountid']] : "Anonymous";
				$json_data [$i] ['accountid'] = $data ['accountid'];
				$json_data [$i] ['credit'] = $this->common_model->calculate_currency ( $data ['credit'], '', '', true, false );
				$json_data [$i] ['payment_date'] = date ( 'Y-m-d H:i:s', strtotime ( $data ['payment_date'] ) + $gmtoffset );
				$i ++;
			}
		}
		echo json_encode ( $json_data );
	}
	function user_call_statistics_with_profit() {
		$this->customerReport_call_statistics_with_profit ();
	}
	function customerReport_call_statistics_with_profit() {
		$post=$this->input->post();
		$year=isset($post['year']) && $post['year'] >0 ? $post['year']:date("Y");
		$month=isset($post['month'])&& $post['month'] >0 ? $post['month']:date("m");
		$json_data = array();
		$start_date=date($year.'-'.$month.'-01');
		$end_day= $year==date("Y") && $month ==date("m") ? date("d") :cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$gmtoffset=$this->common->get_timezone_offset();
		$end_date=date($year."-".$month."-".$end_day.' H:i:s');
		$end_date=date('Y-m-d',strtotime($end_date)+$gmtoffset);
		$current_date=(int)date("d");
		$count=0;
		$i=0;
		$begin = new DateTime($start_date);
		$end = new DateTime($end_date);
		$end=$end->modify('+1 day');
		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
		$records_date=array();
		$accountinfo=$this->session->userdata('accountinfo');
		$parent_id= ($accountinfo['type'] == 1) ? $accountinfo['id'] : 0;
		$customerresult = $this->dashboard_model->get_call_statistics('cdrs',$parent_id,$start_date,$end_date);
		$resellerresult = $this->dashboard_model->get_call_statistics('reseller_cdrs',$parent_id,$start_date,$end_date);
		$acc_arr = array();
/*Date: 08-Mar-2017
Reason: Improvement of Dashboard Performance*/
		$customer_total_result = array();
		$customer_total_result['sum'] = '';
		$customer_total_result['answered'] = '';
		$customer_total_result['mcd'] = '';
		$customer_total_result['duration'] = '';
		$customer_total_result['failed'] = '';
		$customer_total_result['profit'] = '';
		$customer_total_result['debit'] = '';
		$customer_total_result['cost'] = '';
		$customer_total_result['completed'] = '';
		$customer_total_result['billable'] = '';
		$reseller_total_result = array();
		$reseller_total_result['sum'] = '';
		$reseller_total_result['answered'] = '';
		$reseller_total_result['mcd'] = '';
		$reseller_total_result['duration'] = '';
		$reseller_total_result['failed'] = '';
		$reseller_total_result['profit'] = '';
		$reseller_total_result['debit'] = '';
		$reseller_total_result['cost'] = '';
		$reseller_total_result['completed'] = '';
		$reseller_total_result['billable'] = '';
		$mcd = 0;
		$res_mcd = 0;
		foreach ($customerresult->result_array() as $data) {
		  $acc_arr[$data['day']] = $data;
		  $customer_total_result['sum'] += $data['sum'];
		  $customer_total_result['answered'] += $data['answered'];
		  if($data['mcd'] > $mcd){
			  $mcd = $data['mcd'];
		  }
		  $customer_total_result['mcd'] = $mcd;
		  $customer_total_result['duration'] += $data['duration'];
		  $customer_total_result['billable'] += $data['billable'];
		  $customer_total_result['failed'] += $data['failed'];
		  $customer_total_result['profit'] += $data['profit'];
		  $customer_total_result['debit'] += $data['debit'];
		  $customer_total_result['cost'] += $data['cost'];
		  $customer_total_result['completed'] += $data['completed'];
		}
		foreach($resellerresult->result_array() as $data){
		  $reseller_arr[$data['day']]=$data;
		  if(isset($acc_arr[$data['day']])){
			$acc_arr[$data['day']]['sum']= $data['sum']+$acc_arr[$data['day']]['sum'];
			$acc_arr[$data['day']]['answered']= $data['answered']+$acc_arr[$data['day']]['answered'];
			$acc_arr[$data['day']]['failed']= $data['failed']+$acc_arr[$data['day']]['failed'];
			$acc_arr[$data['day']]['profit']= $data['profit']+$acc_arr[$data['day']]['profit'];
			$acc_arr[$data['day']]['completed']= $data['completed']+$acc_arr[$data['day']]['completed'];
			$acc_arr[$data['day']]['duration'] = $data['duration']+$acc_arr[$data['day']]['duration'];
			$acc_arr[$data['day']]['mcd'] =$data['mcd'] > $acc_arr[$data['day']]['mcd'] ? $data['mcd']: $acc_arr[$data['day']]['mcd'];
		  }else{
			$acc_arr[$data['day']]=$data;
		  }
		  $reseller_total_result['sum']+= $data['sum'];
		  $reseller_total_result['answered'] += $acc_arr[$data['day']]['answered'];
		  if($data['mcd'] > $res_mcd){
			  $res_mcd = $data['mcd'];
		  }
		  $reseller_total_result['mcd'] = $res_mcd;
		  $reseller_total_result['duration'] += $data['duration'];
		  $reseller_total_result['failed'] += $data['failed'];
  		  $reseller_total_result['debit'] += $data['debit'];
		  $reseller_total_result['cost'] += $data['cost'];
		  $reseller_total_result['profit'] += $data['profit'];
		  $reseller_total_result['completed'] += $data['completed'];
		  $reseller_total_result['billable'] += $data['billable'];
		}
		if(!empty($acc_arr)){
			foreach($daterange as $date){
				$json_data['date'][]=$date->format("d");
				$day = (int) $date->format("d");
				if(isset($acc_arr[$day])){
				  $asr= ($acc_arr[$day]['sum'] > 0 ) ? (round(($acc_arr[$day]['completed'] / $acc_arr[$day]['sum']) * 100,2)) : 0;
				  $acd= ($acc_arr[$day]['completed'] > 0 ) ? round($acc_arr[$day]['billable'] / $acc_arr[$day]['completed'],2) : 0;
				  $json_data['total'][]=  array((string)$acc_arr[$day]['day'],(int) $acc_arr[$day]['sum']);
				  $json_data['answered'][]=  array((string)$acc_arr[$day]['day'],(int) $acc_arr[$day]['answered']);
				  $json_data['failed'][]=  array((string)$acc_arr[$day]['day'],(int) $acc_arr[$day]['failed']);
				  $json_data['profit'][]=  array((string)$acc_arr[$day]['day'],(float)  str_replace(",", "", $this->common_model->calculate_currency($acc_arr[$day]['profit'])));
				  $json_data['acd'][]=array((string)$acc_arr[$day]['day'],(float)$acd);
				  $json_data['mcd'][]=array((string)$acc_arr[$day]['day'],(float)$acc_arr[$day]['mcd']);
				  $json_data['asr'][]=array((string)$acc_arr[$day]['day'],(float)$asr);
				}else{
				  $json_data['total'][]=  array($date->format("d"), 0);
				  $json_data['answered'][]=  array($date->format("d"), 0);
				  $json_data['failed'][]=  array($date->format("d"), 0);
				  $json_data['profit'][]=  array($date->format("d"), 0);
				  $json_data['acd'][]=array($date->format("d"), 0);
				  $json_data['mcd'][]=array($date->format("d"), 0);
				  $json_data['asr'][]=array($date->format("d"),0);
				}
			}
		} else{
			foreach($daterange as $date){
				$json_data['date'][]=$date->format("d");
				$day = (int) $date->format("d");
				$json_data['total'][]=  array($date->format("d"), 0);
				$json_data['answered'][]=  array($date->format("d"), 0);
				$json_data['failed'][]=  array($date->format("d"), 0);
				$json_data['profit'][]=  array($date->format("d"), 0);
				$json_data['acd'][]=array($date->format("d"), 0);
				$json_data['mcd'][]=array($date->format("d"), 0);
				$json_data['asr'][]=array($date->format("d"), 0);
			}
		}
		$json_data['total_count']['sum']=$reseller_total_result['sum']+$customer_total_result['sum'];
		$json_data['total_count']['debit']=$this->common_model->to_calculate_currency($reseller_total_result['debit']+$customer_total_result['debit'],'','',true,true);
		$json_data['total_count']['cost']=$this->common_model->to_calculate_currency($reseller_total_result['cost']+$customer_total_result['cost'],'','',true,true);
		$json_data['total_count']['profit']=$this->common_model->to_calculate_currency($reseller_total_result['profit']+$customer_total_result['profit'],'','',true,true);
		$json_data['total_count']['completed']=$reseller_total_result['completed']+$customer_total_result['completed'];
		$json_data['total_count']['duration']=$reseller_total_result['duration']+$customer_total_result['duration'];
		$json_data['total_count']['billable']=$reseller_total_result['billable']+$customer_total_result['billable'];
		//print_r($json_data);
		$json_data['total_count']['acd']=$json_data['total_count']['completed'] > 0 ? round($json_data['total_count']['billable']/$json_data['total_count']['completed'],2):0;
		$json_data['total_count']['mcd']=($customer_total_result['mcd'] > 0 || $reseller_total_result['mcd'] > 0 ) ? ($customer_total_result['mcd'] > $reseller_total_result['mcd'] ? $customer_total_result['mcd']:$reseller_total_result['mcd']) : 0;
		$json_data['total_count']['asr']=($json_data['total_count']['sum'] > 0 ) ? (round(($json_data['total_count']['completed'] / $json_data['total_count']['sum']) * 100,2)) : 0;
		$json_data['total_count']['asr']=$this->common_model->format_currency($json_data['total_count']['asr']);
		echo json_encode($json_data);
	}
	function user_maximum_callminutes() {
		$this->customerReport_maximum_callminutes ();
	}
	function customerReport_maximum_callminutes() {
		$post = $this->input->post ();
		$year = isset ( $post ['year'] ) && $post ['year'] > 0 ? $post ['year'] : date ( "Y" );
		$month = isset ( $post ['month'] ) && $post ['month'] > 0 ? $post ['month'] : date ( "m" );
		$start_date = date ( $year . '-' . $month . '-01' );
		$end_day = $year == date ( "Y" ) && $month == date ( "m" ) ? date ( "d" ) : cal_days_in_month ( CAL_GREGORIAN, $month, $year );
		$gmtoffset = $this->common->get_timezone_offset ();
		$end_date = date ( $year . "-" . $month . "-" . $end_day . ' H:i:s' );
		$end_date = date ( 'Y-m-d', strtotime ( $end_date ) + $gmtoffset );
		$json_data = array ();
		$result = $this->dashboard_model->get_customer_maximum_callminutes ( $start_date, $end_date );
		$i = 0;
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = ($accountinfo ['type'] == - 1 or $accountinfo ['type'] == 2) ? 0 : $accountinfo ['id'];
		if ($this->session->userdata ( 'userlevel_logintype' ) != 0 && $this->session->userdata ( 'userlevel_logintype' ) != 3) {
			$account_arr = $this->common->get_array ( 'id,number,first_name,last_name', 'accounts', array (
					'reseller_id' => $reseller_id 
			) );
		} else {
			$account_arr = $this->common->get_array ( 'id,number,first_name,last_name', 'accounts', array (
					'id' => $reseller_id 
			) );
		}
		if ($result->num_rows () > 0) {
			foreach ( $result->result_array () as $data ) {
				$data ['accountid'] = ($data ['accountid'] != '' && isset ( $account_arr [$data ['accountid']] )) ? $account_arr [$data ['accountid']] : "Anonymous";
				$json_data [$i] [] = $data ['accountid'];
				$json_data [$i] [] = round ( $data ['billseconds'] / 60, 0 );
				$i ++;
			}
		} else {
			$json_data [] = array ();
		}
		echo json_encode ( $json_data );
	}
	function user_maximum_callcount() {
		$this->customerReport_maximum_callcount ();
	}
	function customerReport_maximum_callcount() {
		$post = $this->input->post ();
		$year = isset ( $post ['year'] ) && $post ['year'] > 0 ? $post ['year'] : date ( "Y" );
		$month = isset ( $post ['month'] ) && $post ['month'] > 0 ? $post ['month'] : date ( "m" );
		$start_date = date ( $year . '-' . $month . '-01' );
		$end_day = $year == date ( "Y" ) && $month == date ( "m" ) ? date ( "d" ) : cal_days_in_month ( CAL_GREGORIAN, $month, $year );
		$gmtoffset = $this->common->get_timezone_offset ();
		$end_date = date ( $year . "-" . $month . "-" . $end_day . ' H:i:s' );
		$end_date = date ( 'Y-m-d', strtotime ( $end_date ) + $gmtoffset );
		$json_data = array ();
		$result = $this->dashboard_model->get_customer_maximum_callcount ( $start_date, $end_date );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = ($accountinfo ['type'] == - 1 or $accountinfo ['type'] == 2) ? 0 : $accountinfo ['id'];
		if ($this->session->userdata ( 'userlevel_logintype' ) != 0 && $this->session->userdata ( 'userlevel_logintype' ) != 3) {
			$account_arr = $this->common->get_array ( 'id,number,first_name,last_name', 'accounts', array (
					'reseller_id' => $reseller_id 
			) );
		} else {
			$account_arr = $this->common->get_array ( 'id,number,first_name,last_name', 'accounts', array (
					'id' => $reseller_id 
			) );
		}
		$i = 0;
		if ($result->num_rows () > 0) {
			foreach ( $result->result_array () as $data ) {
				$data ['accountid'] = ($data ['accountid'] != '' && isset ( $account_arr [$data ['accountid']] )) ? $account_arr [$data ['accountid']] : "Anonymous";
				$json_data [$i] [] = $data ['accountid'];
				$json_data [$i] [] = ( int ) $data ['call_count'];
				$i ++;
			}
		} else {
			$json_data [] = array ();
		}
		echo json_encode ( $json_data );
	}
}

?>
