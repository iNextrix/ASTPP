<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################
class dashboard extends CI_Controller {

    function dashboard() 
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('Auth_model');
        $this->load->library("astpp/form");
        $this->load->model('Astpp_common');
	$this->load->model('dashboard_model');
        $this->load->library('freeswitch_lib');  
    }

    function index() 
    {
        if ($this->session->userdata('user_login') == FALSE) 
            redirect(base_url() . 'login/login');
            $data['page_title'] = 'Dashboard';
        if ($this->session->userdata('logintype') == 0) 
	{
            $this->load->view('view_user_dashboard', $data);
        } else {
	    $gmtoffset=$this->common->get_timezone_offset();
            $data['date']=date('F Y', strtotime(date('Y-m-d'))+$gmtoffset);
            $this->load->view('view_dashboard', $data);
        }
    }
    function user_recent_payments(){
      $this->customerReport_recent_payments();
    }     
    function customerReport_recent_payments() 
    {
	 
        $json_data = array();
	$i=1;
	$result = $this->dashboard_model->get_recent_recharge();
	$gmtoffset=$this->common->get_timezone_offset();
        if($result->num_rows() > 0)
	{
		 $account_arr = $this->common->get_array('id,number,first_name,last_name', 'accounts','');
		 $json_data[0]['accountid']='Accounts';
		 $json_data[0]['credit']='Amount';
		 $json_data[0]['payment_date']='Date';
		 foreach($result->result_array() as $key=>$data){
		      $current_timestamp=strtotime($data['payment_date']);
		      $modified_date=$current_timestamp+$gmtoffset;
          	      $data['accountid'] = ($data['accountid'] != '' && isset($account_arr[$data['accountid']])) ? $account_arr[$data['accountid']] :"Anonymous";
		      $json_data[$i]['accountid']=$data['accountid'];
		      $json_data[$i]['credit']=$this->common_model->calculate_currency($data['credit'],'','',true);
		      $json_data[$i]['payment_date']=date('Y-m-d H:i:s',strtotime($data['payment_date'])+$gmtoffset);
		      $i++;
          	}
          }
         echo json_encode($json_data); 
    }
    function  user_call_statistics_with_profit(){
      $this->customerReport_call_statistics_with_profit();
     }
    function customerReport_call_statistics_with_profit() {
	$json_data = array();
	$start_date=date('Y-m-01');
	$gmtoffset=$this->common->get_timezone_offset();
	$end_date=date('Y-m-d H:i:s');
	$end_date=date('Y-m-d',strtotime($end_date)+$gmtoffset);
	$current_date=(int)date("d");
        $count=0;
        $i=0;
        $begin = new DateTime($start_date);
	$end = new DateTime($end_date);
	$end=$end->modify('+1 day');
	$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
	$records_date=array();
        $result = $this->dashboard_model->get_call_statistics();
        $acc_arr = array();
	foreach ($result->result_array() as $data) {
	  $acc_arr[$data['day']] = $data;
	}
	if($result->num_rows()>0){
	    foreach($daterange as $date){
		$json_data['date'][]=$date->format("d");
		$day = (int) $date->format("d");
		if(isset($acc_arr[$day])){
		  $json_data['total'][]=  array((string)$acc_arr[$day]['day'],(int) $acc_arr[$day]['sum']);
		  $json_data['answered'][]=  array((string)$acc_arr[$day]['day'],(int) $acc_arr[$day]['answered']);
		  $json_data['failed'][]=  array((string)$acc_arr[$day]['day'],(int) $acc_arr[$day]['failed']);
		  $json_data['profit'][]=  array((string)$acc_arr[$day]['day'],(float) $this->common_model->calculate_currency($acc_arr[$day]['profit']));
		}else{
		  $json_data['total'][]=  array($date->format("d"), 0);
		  $json_data['answered'][]=  array($date->format("d"), 0);
		  $json_data['failed'][]=  array($date->format("d"), 0);
		  $json_data['profit'][]=  array($date->format("d"), 0);
		}
		
	    }
	}
	else{
	foreach($daterange as $date){
		$json_data['date'][]=$date->format("d");
		$day = (int) $date->format("d");
		$json_data['total'][]=  array($date->format("d"), 0);
		$json_data['answered'][]=  array($date->format("d"), 0);
		$json_data['failed'][]=  array($date->format("d"), 0);
		$json_data['profit'][]=  array($date->format("d"), 0);
	}
	}
	echo json_encode($json_data);
}

     function user_maximum_callminutes(){
      $this->customerReport_maximum_callminutes();
    }
     function customerReport_maximum_callminutes()
     {
     	$json_data = array();
 	$result = $this->dashboard_model->get_customer_maximum_callminutes();
	$i=0;
	$accountinfo=$this->session->userdata('accountinfo');
	$reseller_id=$accountinfo['type']== -1 ? 0 : $accountinfo['id'];
	if($this->session->userdata('userlevel_logintype')!= 0 && $this->session->userdata('userlevel_logintype')!= 3){
	  $account_arr = $this->common->get_array('id,number,first_name,last_name', 'accounts',array('reseller_id'=>$reseller_id));
	} 
	else{
	 $account_arr = $this->common->get_array('id,number,first_name,last_name', 'accounts',array('id'=>$reseller_id));
	}
    	if($result->num_rows() > 0 )
	{
    		
    		foreach ($result->result_array() as $data) 
		{
			$data['accountid'] = ($data['accountid'] != '' && isset($account_arr[$data['accountid']])) ? $account_arr[$data['accountid']] :"Anonymous";
			$json_data[$i][]= $data['accountid'];
			$json_data[$i][]= (int)$data['billseconds'];
			$i++;
	    	}

    		
    	}else{
	  $json_data[] = array();
//     	       if(empty($account_arr)){
//     	       $json_data[0][]=$accountinfo['number']."(".$accountinfo['first_name']." ".$accountinfo['last_name'];
//     	       $json_data[0][]=0;
//     	       }
//     	       else{
//     	        foreach($account_arr as $account_id=>$acc_name){
// 		  $json_data[$i][]=$acc_name;
// 		  $json_data[$i][]=0;
// 		  $i++;
//     	        }
//     	       }
      	     }
     	     echo json_encode($json_data);
     }

     function user_maximum_callcount(){
       $this->customerReport_maximum_callcount();
     }
     function customerReport_maximum_callcount()
     {
     	$json_data = array();
	$result = $this->dashboard_model->get_customer_maximum_callcount();
	$accountinfo=$this->session->userdata('accountinfo');
	$reseller_id=$accountinfo['type']== -1 ? 0 : $accountinfo['id'];
	if($this->session->userdata('userlevel_logintype')!= 0 && $this->session->userdata('userlevel_logintype')!= 3){
	  $account_arr = $this->common->get_array('id,number,first_name,last_name', 'accounts',array('reseller_id'=>$reseller_id));
	} 
	else{
	 $account_arr = $this->common->get_array('id,number,first_name,last_name', 'accounts',array('id'=>$reseller_id));
	}
	$i=0;
    	if($result->num_rows() > 0 )
	{
	    foreach ($result->result_array() as $data) 
	    {
		        $data['accountid'] = ($data['accountid'] != '' && isset($account_arr[$data['accountid']])) ? $account_arr[$data['accountid']] :"Anonymous";
		        $json_data[$i][]= $data['accountid'];
		        $json_data[$i][]= (int)$data['call_count'];
		        $i++;
            }
    	    
        }else{
    	        $json_data[] = array();
     	     }
	echo json_encode($json_data);
     }
}

?>
