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

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Dynamically build forms for display
 */
class common {

    protected $CI; // codeigniter

    function __construct($library_name = '') {

        $this->CI = & get_instance();
        $this->CI->load->library("timezone");
        $this->CI->load->model('db_model');
        $this->CI->load->library('email');
        $this->CI->load->library('session');
    }

// __construct
    /**
     * adds raw html to the field array
     * @param type $label
     * @param type $field add
     */
  
    function generate_password(){
      $pass=substr(md5(rand(0, 1000000000)), 0, common_model::$global_config['system_config']['pinlength']);
      return $pass;
    }
function find_uniq_rendno($size = '', $field = '', $tablename = '') {

        if ($tablename != '') {
            $accounttype_array = array();
            $uname = rand(pow(10, $size - 1), pow(10, $size) - 1);
            $where = array($field => $uname);
            $acc_result = $this->CI->db_model->getSelect('Count(*) as count', $tablename, $where);
            $acc_result = $acc_result->result();
            while ($acc_result[0]->count != 0) {
                $uname = rand(pow(10, $size - 1), pow(10, $size) - 1);
                $acc_result = $this->CI->db_model->getSelect('Count(*) as count', $tablename, $where);
            }
        } else {
            $uname = rand(pow(10, $size - 1), pow(10, $size) - 1);
        }
        return $uname;
    }
    function find_uniq_rendno_customer($size = '', $field = '', $tablename = '') {
        if ($tablename != '') {
            $accounttype_array = array();
            $uname = rand(pow(10, $size - 1), pow(10, $size) - 1);
            $where = array($field => $uname);
            $acc_result = $this->CI->db_model->getSelect('Count(*) as count', $tablename, $where);
            $acc_result = $acc_result->result();
            while ($acc_result[0]->count != 0) {
                $uname = rand(pow(10, $size - 1), pow(10, $size) - 1);
                $acc_result = $this->CI->db_model->getSelect('Count(*) as count', $tablename, $where);
            }
        } else {
            $uname = rand(pow(10, $size - 1), pow(10, $size) - 1);
        }
        $start_prifix_value = common_model::$global_config['system_config']['startingdigit'];
       if($tablename == 'accounts' && $start_prifix_value != 0){
          $length=strlen($start_prifix_value);
          $uname = substr($uname,$length);
          $uname = $start_prifix_value . $uname;
       }
       return $uname;
    }

function random_string($length)
{
    $chars ="1234567890";//length:36
    $final_rand='';
    for($i=0;$i<$length; $i++)
    {
        $final_rand .= $chars[ rand(0,strlen($chars)-1)];
 
    }
    return $final_rand;
}
        function find_uniq_rendno_accno($length = '', $field = '', $tablename = '',$default,$creation_count) {
        $number=array();
        $j=0;
        
        $total_count=pow(10,$length);
        for($i=1;$i<=$total_count;$i++){
           
            $flag =false;
             $uname=$this->random_string($length);
             $uname=  strtolower($uname);
             if(isset($default))
             $uname =$default.$uname;
             if(!in_array($uname,$number)){
                $where = array($field => $uname);
                $acc_result = $this->CI->db_model->getSelect('Count(id) as count', $tablename, $where);
                $acc_result=$acc_result->result_array();
                if($acc_result[0]['count'] == 0 && !in_array($uname,$number)){
                    $number[]=$uname;
                    $j++;
                }
                if($j == $creation_count){
                    break;
                }
             }
             else{
                $total_count++;
             }
             
        }
        return $number;
        }
    function get_field_count($select, $table, $where) {
//        echo $select."=====".$table."===".$where;
        if (is_array($where)) {
            $where = $where;
        } else {
            $where = array($select => $where);
        }
        $field_name = $this->CI->db_model->countQuery($select, $table, $where);
        if (isset($field_name) && !empty($field_name)) {
            return $field_name;
        } else {
            return "0";
        }
    }

    function get_field_name($select, $table, $where) {
        if (is_array($where)) {
            $where = $where;
        } else {
            $where = array("id" => $where);
        }
        $field_name = $this->CI->db_model->getSelect($select, $table, $where);
        $field_name = $field_name->result();
        if (isset($field_name) && !empty($field_name)) {
            return $field_name[0]->$select;
        } else {
            return "";
        }
    }
    
    function get_field_name_coma_new($select, $table, $where) {
        $value = '';
        if (is_array($where)) {
            $where = $where;
        } else {
            $where = explode(',', $where);
        }
        $select1 = explode(',', $select);
        for ($i = 0; $i < count($where); $i++) {
            $where_in = array("id" => $where[$i]);

            $field_name = $this->CI->db_model->getSelect($select, $table, $where_in);
            $field_name = $field_name->result();
            if (isset($field_name) && !empty($field_name)) {
	      foreach($select1 as $sel)
	      {
		if($sel=='number')
		{
		   $value.="(". $field_name[0]->$sel . ")";
		}
		else
		{
                $value.= $field_name[0]->$sel . " ";
                }
              }
            } else {
                $value = "";
            }
        }
        return rtrim($value, ',');
    }
    function check_did_avl($select, $table, $where)
    {
        $accountinfo=$this->CI->session->userdata('accountinfo');
        $flag_status = "";
        $where = array("number" => $where);
        $field_name = $this->CI->db_model->getSelect("id,accountid,parent_id", 'dids', $where);
        $field_name = $field_name->result();
        if (isset($field_name) && !empty($field_name)) {
            if(isset($field_name[0]) && $accountinfo['type'] != 1 )
            {
                if($field_name[0]->accountid != 0){
                  $flag_status="<a href='../did_list_reliase/".$field_name[0]->id."' title='Reliase' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='reliase'>Release(C)<span></a>";
                }
                else if($field_name[0]->parent_id != 0){
                  $flag_status="<a href='../did_list_reliase/".$field_name[0]->id."' title='Reliase' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='reliase'>Release(R)</span></a>";
                }else{
                    $flag_status="<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
                }
            }else{
                $reseller_id= $accountinfo['type'] != 1 ? 0 : $accountinfo['id'];
                $where = array("note" => $field_name[0]->number,'parent_id'=>$reseller_id);
                $field_name_re = $this->CI->db_model->getSelect("reseller_id", 'reseller_pricing', $where);
                $field_name_re = $field_name_re->result();
        
                if (isset($field_name_re) && !empty($field_name_re)) {
                      $flag_status="<a href='../did_list_reliase/".$field_name[0]->id."' title='Reliase' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='reliase'>Release(R)</span></a>";
                }else{
                  $flag_status="<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
                }
            }
        } else {
            $flag_status="<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
        } 
        return $flag_status;           
    }
    
    function check_did_avl_reseller($select, $table, $where)
    {
        $accountinfo=$this->CI->session->userdata('accountinfo');
        $flag_status = "";
        $where = array("number" => $where);
        $field_name = $this->CI->db_model->getSelect("id,accountid,parent_id,number", 'dids', $where);
        $field_name = $field_name->result();
        if (isset($field_name) && !empty($field_name)) {
            if(isset($field_name[0]) && $accountinfo['type'] != 1 )
            {
                if($field_name[0]->accountid != 0){
                  $flag_status="<a href='../did_list_reliase/".$field_name[0]->id."' title='Reliase' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='reliase'>Release(C)<span></a>";
                }
                else if($field_name[0]->parent_id != 0){
                  $flag_status="<a href='../did_list_reliase/".$field_name[0]->id."' title='Reliase' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='reliase'>Release(R)</span></a>";
                }else{
                    $flag_status="<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
                }
            }else{
                $reseller_id= $accountinfo['type'] != 1 ? 0 : $accountinfo['id'];
                $where = array("note" => $field_name[0]->number,'parent_id'=>$reseller_id);
                $field_name_re = $this->CI->db_model->getSelect("reseller_id,id", 'reseller_pricing', $where);
                $field_name_re = $field_name_re->result();
        
                if (isset($field_name_re) && !empty($field_name_re)) {
                      $flag_status="<a href='../did/did_reseller_edit/delete/".$field_name_re[0]->id."' title='Reliase' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='reliase'>Release(R)</span></a>";
                }else{
                  $flag_status="<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
                }
            }
        } else {
            $flag_status="<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
        } 
        return $flag_status;           
    }
//    get data for Comma seprated
    function get_field_name_coma($select, $table, $where) {
        $value = '';
        if (is_array($where)) {
            $where = $where;
        } else {
            $where = explode(',', $where);
        }
        for ($i = 0; $i < count($where); $i++) {
            $where_in = array("id" => $where[$i]);

            $field_name = $this->CI->db_model->getSelect($select, $table, $where_in);
            $field_name = $field_name->result();
            if (isset($field_name) && !empty($field_name)) {
                $value.= $field_name[0]->$select . ",";
            } else {
                $value = "";
            }
        }
        return rtrim($value, ',');
    }
    function set_invoice_option($select = "", $table = "", $call_type="",$edit_value=''){

        $invoice_date=false;
        $uri_segment = $this->CI->uri->segments;
        if(isset($uri_segment[3]) && empty($edit_value)){
            $field_name = $this->CI->db_model->getSelect("sweep_id,invoice_day","accounts",array("id"=>$uri_segment[3]));
            $field_name= $field_name->result_array();
            $select = $field_name[0]["sweep_id"];
            $invoice_date= $field_name[0]["invoice_day"];
        }
        else{
            $invoice_date=$edit_value;
        }
        if($select == "" || $select == "0"){
            $daily_arr = array("0"=>"0");
            return $daily_arr;
        }
        if($select == 1){
            $week_arr = array("1"=>"Monday","2"=>"Tuesday","3"=>"Wednesday","4"=>"Thursday","5"=>"Friday",
                    "6"=>"Saturday","7"=>"Sunday");
            $rawDate = date("Y-m-d");
            $day = date('N', strtotime($rawDate));
            if(isset($uri_segment[3])){
                return $week_arr;
            }else{
            $week_drp = form_dropdown(array("name"=>'invoice_day',"class"=>"invoice_day"),$week_arr, $day);
            return $week_drp;
            }
        }
        if($select != 0 && $select != 1){
            for($i=1; $i<29; $i++){
                $mon_arr[$i]= $i;
            }
            if(isset($uri_segment[3]) && empty($edit_value)){
                return $mon_arr;
            }else{
		  $day = $invoice_date > 0 ? $invoice_date : date('d');
                $month_drp = form_dropdown(array("name"=>'invoice_day',"class"=>"invoice_day"),$mon_arr,$day );
                return $month_drp;
            }
        }
    }
    function set_status($status = '') {
        $status_array = array( '0' => 'Active','1' => 'Inactive',);
        return $status_array;
    }
    function set_prorate($status = '') {
        $status_array = array( '0' => 'Yes','1' => 'No',);
        return $status_array;
    }

    function set_package_status($status = '') {
        $status_array = array( '1' => 'Active','0' => 'Inactive',);
        return $status_array;
    }
    function get_package_status($select = "", $table = "", $status) {
        return ($status == 0) ? "Inactive" : "Active";
    }
    function set_allow($status = '') {
        $status_array = array('1' => 'Enable', '0' => 'Disable');
        return $status_array;
    } 
    function get_allow($select = "", $table = "", $status) {
        return ($status == 1) ? "Yes" : "No";
    }

    function set_call_type($call_type = "") {
        $call_type_array = array("" => "--Select--",'0' => 'PSTN', '1' => 'Local', '2' => 'Other');
        return $call_type_array;
    }

    function get_call_type($select = "", $table = "", $call_type) {
        $call_type_array = array('0' => 'PSTN', '1' => 'Local', '2' => 'Other');
        return $call_type_array[$call_type];
    }
    function get_custom_call_type($call_type){
        $call_type_array = array('PSTN'=>'0','LOCAL'=>'1','OTHER'=>'2');
        return $call_type_array[$call_type];
    }
    function set_sip_config_option($option = "") {
        $config_option = array("true" => "True", "false" => "False");
        return $config_option;
    }
    function get_entity_type($select = "", $table = "", $entity_type){
	 $entity_array = array('-1'=>"Administratior",'0' => 'Customer', '1' => 'Reseller', '2' => 'Admin','3'=>"Provider","4"=>"Sub Admin","5"=>"Callshop");
	 return($entity_array[$entity_type]);
	 
    }
    function set_entity_type_customer($entity_type = ""){
    $entity_array = array(''=>"--Select--",'0' => 'Customer','3'=>"Provider");
        return $entity_array;
    }
    function set_entity_type_admin($entity_type = ""){
	$entity_array = array(''=>"--Select--",'2' => 'Admin',"4"=>"Sub Admin");
        return $entity_array;
    }
    function set_sip_config_options($option = "") {
        $config_option = array("false" => "False", "true" => "True");
        return $config_option;
    }

    function set_sip_config_default($option = "") {
        $config_option = array("" => "--SELECT--", "false" => "False", "true" => "True");
        return $config_option;
    }

    function set_sip_bind_params($option = "") {
        $config_option = array("" => "--SELECT--", "udp" => "UDP", "tcp" => "TCP");
        return $config_option;
    }

    function set_sip_vad_option() {
        $config_option = array("in" => "In", "out" => "Out", "both" => "Both");
        return $config_option;
    }

    function set_sip_drp_option($option = "") {
        $status_array = array('no' => 'No', 'yes' => 'Yes');
        return $status_array;
    }

    function set_status_callingcard($status = '') {
        $status_array = array('1' => 'Active', '0' => 'Inactive', '2' => 'Deleted');
        return $status_array;
    }

    function get_status($select = "", $table = "", $status) {
        return ($status == 0) ? "Active" : "Inactive";
    }
     function get_invoice_date($select='',$table='',$invoice_date){

      $invoice_date = date('Y-m-d', strtotime($invoice_date)); 
      return $invoice_date;  
    }
    function get_from_date($select='',$table='',$from_date){

      $from_date = date('Y-m-d', strtotime($from_date)); 
      return $from_date;  
    }
    function get_account_balance($select = "", $table = "", $amount) {
        $this->CI->load->model('common_model');
        if ($amount == 0) {
            return $amount;
        } else {
	    $balance = $this->CI->common_model->add_calculate_currency(($amount), "", '', true, true);

            return $balance;
        }
    }

    function convert_to_currency($select = "", $table = "", $amount) {
        $this->CI->load->model('common_model');
        return $this->CI->common_model->calculate_currency($amount);
    }

    function get_paid_status($select = "", $table = "", $status) {
        return ($status == 1) ? "Paid" : "Unpaid";
    }

    function set_account_type($status = '') {
        $status_array = array('0' => 'Prepaid', '1' => 'Postpaid');
        return $status_array;
    }
    function set_account_type_search($status = '') {
        $status_array = array(''=>"--Select--",'0' => 'Prepaid', '1' => 'Postpaid');
        return $status_array;
    }
    function get_account_type($select = "", $table = "", $PTE) {
        return ($PTE == 1) ? "Postpaid" : "Prepaid";
    }

    function get_payment_by($select = "", $table = "", $type) {
        if ($type == '-1') {
            $type = "Admin";
        }else{
	    $type = $this->get_field_name("number","accounts", array("id"=>$type));
	}
        return $type;
    }

    function set_payment_type($payment_type= ''){
 	$status_array = array( '0' => 'Recharge','1' => 'Postcharge',);
         return $status_array;
    }

    function search_int_type($status = '') {
        $status_array = array('1' => 'is equal to', '2' => 'is not equal to', '3' => 'greater than', '4' => 'less than', '5' => 'greater or equal than', '6' => 'less or equal than');
        return $status_array;
    }

    function update_int_type($status = '') {
        $status_array = array('1' => 'Preserve', '2' => 'set to', '3' => 'Increase by', '4' => 'decrease by');
        return $status_array;
    }

    function update_drp_type($status = '') {
        $status_array = array('1' => 'Preserve', '2' => 'set to');
        return $status_array;
    }

    function search_string_type($status = '') {
        $status_array = array('1' => 'contains', '2' => 'doesnt contain', '3' => 'is equal to', '4' => 'is not equal to');
        return $status_array;
    }

    function set_protocal($protpcal = '') {
        $status_array = array('SIP' => 'SIP', 'IAX2' => 'IAX2', 'Zap' => 'Zap', 'Local' => 'Local', 'OH323' => 'OH323', 'OOH323C' => 'OOH323C');
        return $status_array;
    }

/*
*
* Purpose : Add Profit Margin report
* Version 2.1
*/
    function set_notify_by($status = '') {
        $status_array = array(''=>'Select Notify By','0' => 'CSV', '1' => 'Email');
        return $status_array;
    }

    function convert_to_percentage($select = "", $table = "", $amount) {
        return round($amount,2)." %";
    }

    function convert_to_minutes($select = "", $table = "", $amount) {
        return str_replace('.',':',round($amount/60,2));
    }

    function set_filter_type_search($status = '') {
        $status_array = array('pricelist_id' => 'Rate Group', 'accountid' => 'Customer','reseller_id' => 'Reseller');
        return $status_array;
    }
    
    //attachment download in email module...
     function attachment_icons($select = "", $table = "", $attachement="") {
	if($attachement!="")
	{
		$array=explode(",", $attachement);
		$str='';
	//echo '<pre>'; print_r($array); exit;	
		foreach($array as $key =>$val){
			$link = base_url() . "email/email_history_list_attachment/".$val;
			$str.="<a href='".$link."' title='".$val."' class='btn btn-royelblue btn-sm'><i class='fa fa-paperclip fa-fw'></i></a>&nbsp;&nbsp;";
		}
		return $str;
	}
	else{
		return "";
	}
    }
/****************************************************************/

    function set_despostion($dis = '') {
        $status_array = array("" => "--Select Disposition--",
            "UNSPECIFIED" => "UNSPECIFIED",
            "UNALLOCATED_NUMBER" => "UNALLOCATED_NUMBER",
            "NO_ROUTE_DESTINATION" => "NO_ROUTE_DESTINATION",
            "CHANNEL_UNACCEPTABLE" => "CHANNEL_UNACCEPTABLE",
            "NORMAL_CLEARING" => "NORMAL_CLEARING",
            "SUCCESS" => "SUCCESS",
            "USER_BUSY" => "USER_BUSY",
            "NO_USER_RESPONSE" => "NO_USER_RESPONSE",
            "NO_ANSWER" => "NO_ANSWER",
            "CALL_REJECTED" => "CALL_REJECTED",
            "NUMBER_CHANGED" => "NUMBER_CHANGED",
            "DESTINATION_OUT_OF_ORDER" => "DESTINATION_OUT_OF_ORDER",
            "INVALID_NUMBER_FORMAT" => "INVALID_NUMBER_FORMAT",
            "FACILITY_REJECTED" => "FACILITY_REJECTED",
            "NORMAL_UNSPECIFIED" => "NORMAL_UNSPECIFIED",
            "NORMAL_CIRCUIT_CONGESTION" => "NORMAL_CIRCUIT_CONGESTION",
            "NETWORK_OUT_OF_ORDER" => "NETWORK_OUT_OF_ORDER",
            "NORMAL_TEMPORARY_FAILURE" => "NORMAL_TEMPORARY_FAILURE",
            "SWITCH_CONGESTION" => "SWITCH_CONGESTION",
            "FACILITY_NOT_SUBSCRIBED" => "FACILITY_NOT_SUBSCRIBED",
            "OUTGOING_CALL_BARRED" => "OUTGOING_CALL_BARRED",
            "BEARERCAPABILITY_NOTAUTH" => "BEARERCAPABILITY_NOTAUTH",
            "BEARERCAPABILITY_NOTAVAIL" => "BEARERCAPABILITY_NOTAVAIL",
            "SERVICE_UNAVAILABLE" => "SERVICE_UNAVAILABLE",
            "BEARERCAPABILITY_NOTIMPL" => "BEARERCAPABILITY_NOTIMPL",
            "CHAN_NOT_IMPLEMENTED" => "CHAN_NOT_IMPLEMENTED",
            "FACILITY_NOT_IMPLEMENTED" => "FACILITY_NOT_IMPLEMENTED",
            "SERVICE_NOT_IMPLEMENTED" => "SERVICE_NOT_IMPLEMENTED",
            "INCOMPATIBLE_DESTINATION" => "INCOMPATIBLE_DESTINATION",
            "RECOVERY_ON_TIMER_EXPIRE" => "RECOVERY_ON_TIMER_EXPIRE",
            "ORIGINATOR_CANCEL" => "ORIGINATOR_CANCEL",
            "ALLOTTED_TIMEOUT" => "ALLOTTED_TIMEOUT",
            "MEDIA_TIMEOUT" => "MEDIA_TIMEOUT",
            "PROGRESS_TIMEOUT" => "PROGRESS_TIMEOUT"
        );

//        $status_array = array('' => '--Select Desposition--', 'NORMAL_CLEARING' => 'NORMAL_CLEARING', 'INVALID_GATEWAY' => 'INVALID_GATEWAY', 'NO_ROUTE_DESTINATION' => 'NO_ROUTE_DESTINATION',
//            'CALL_REJECTED' => 'CALL_REJECTED', 'DESTINATION_OUT_OF_ORDER' => 'DESTINATION_OUT_OF_ORDER', 'NORMAL_TEMPORARY_FAILURE' => 'NORMAL_TEMPORARY_FAILURE',
//            'ORIGINATOR_CANCEL' => 'ORIGINATOR_CANCEL', 'SYSTEM_SHUTDOWN' => 'SYSTEM_SHUTDOWN');
        return $status_array;
    }

    function set_calltype($type = '') {
        $status_array = array("" => "--Select Type--",
            "STANDARD" => "STANDARD",
            "DID" => "DID",
            "CALLINGCARD"=>"CALLINGCARD"
        );
        return $status_array;
    }
  function set_search_status($select= ''){
        $status_array = array("" => "--Select--",
            "0" => "Active",
            "1" => "Inactive"
        );
        return $status_array;
}
    function get_action_buttons($buttons_arr, $linkid) {
        $ret_url = '';
        if (!empty($buttons_arr) && $buttons_arr != '') {
            foreach ($buttons_arr as $button_key => $buttons_params) {
                if (strtoupper($button_key) == "EDIT") {
                    $ret_url .= $this->build_edit_button($buttons_params, $linkid);
                }
/*
*
* Purpose : Add resend link
* Version 2.1
*/
		if (strtoupper($button_key) == "RESEND") {
                    $ret_url .= $this->build_edit_button_resend($buttons_params, $linkid);
                }
/****************************************/
                if (strtoupper($button_key) == "EDIT_RESTORE") {
                    $ret_url .= $this->build_edit_button_restore($buttons_params, $linkid);
                }
                if (strtoupper($button_key) == "DELETE") {
                    $ret_url .= $this->build_delete_button($buttons_params->url, $linkid);
                }
                if (strtoupper($button_key) == "VIEW") {
                    $ret_url .= $this->build_view_button($buttons_params, $linkid);
                }
                if (strtoupper($button_key) == "TAXES") {
                    $ret_url .= $this->build_add_taxes_button($buttons_params, $linkid);
                }
                if (strtoupper($button_key) == "BLUEBOX_LOGIN") {
                    $ret_url .= $this->build_bluebox_login($buttons_params->url, $linkid);
                }
                if (strtoupper($button_key) == "CALLERID") {
                    $ret_url .= $this->build_add_callerid_button($buttons_params, $linkid);
                }
                if (strtoupper($button_key) == "PAYMENT") {
                    $ret_url .= $this->build_add_payment_button($buttons_params->url, $linkid);
                }
                if (strtoupper($button_key) == "DOWNLOAD") {
                    $ret_url .= $this->build_add_download_button($buttons_params->url, $linkid);
                }
                if (strtoupper($button_key) == "START") {
                    $ret_url .= $this->build_start_button($buttons_params->url, $linkid);
                }
                if (strtoupper($button_key) == "STOP") {
                    $ret_url .= $this->build_stop_button($buttons_params->url, $linkid);
                }
                if (strtoupper($button_key) == "RELOAD") {
                    $ret_url .= $this->build_reload_button($buttons_params->url, $linkid);
                }
                if (strtoupper($button_key) == "RESCAN") {
                    $ret_url .= $this->build_rescan_button($buttons_params->url, $linkid);
                }
		
        	 if (strtoupper($button_key) == "DOWNLOAD_DATABASE") {
                    $ret_url .= $this->build_add_download_database_button($buttons_params->url, $linkid);
                }       
                if(strtoupper($button_key) == "DELETE_ANIMAP"){
                $ret_url .= $this->build_delete_button_animap($buttons_params->url,$linkid);
                }
                if(strtoupper($button_key) == "EDIT_ANIMAP"){
                    $ret_url .= $this->build_edit_button_animap($buttons_params,$linkid);
                }
                if(strtoupper($button_key) == "ANIMAP"){
                    $ret_url .= $this->build_animap_button($buttons_params,$linkid);
                }
            }
        }
        return $ret_url;
    }
 function build_delete_button_animap($url,$linkid){
        $link = base_url().$url."".$linkid;
        return '<a href="javascript:void(0)" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg_destination('.$linkid.');"><i class="fa fa-trash fa-fw"></i></a>';
    }
    function build_edit_button_animap($button_params,$linkid){
        $link = base_url().$button_params->url."".$linkid;
            return '<a href="javascript:void(0);" id="destination_new" class="btn btn-royelblue btn-sm" onclick="return get_destination('.$linkid.');" title="Update"><i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
    }
    function build_animap_button($button_params,$linkid){
        $link = base_url().$button_params->url."".$linkid;
              return '<a href="'.$link.'" class="btn btn-royelblue btn-sm animap_image" rel="facebox" title="ANI Map"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
    }
    function build_edit_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Update"><i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Edit"><i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
        }
    }
    function build_edit_button_restore($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Restore" onClick="return get_alert_msg();"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Restore" onClick="return get_alert_msg_restore();"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
        }
    }

    function build_delete_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();"><i class="fa fa-trash fa-fw"></i></a>';
    }

    function build_view_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="View Details"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="View Details"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
        }
    }

    function build_add_taxes_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Add Account Taxes"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Add Account Taxes"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
        }
    }
	function build_add_download_database_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class="btn btn-royelblue btn-sm "  title="Download Database" ><i class="fa-fw fa fa-file-archive-o"></i></a>&nbsp;';
    }

    function build_add_callerid_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Caller id"><i class="fa fa-mobile-phone fa-fw"></i></a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="CallerID"><i class="fa fa-mobile-phone fa-fw"></i></a>&nbsp;';
        }
    }
    function build_start_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        
        return '<a href="' . $link . '" class=""  title="Start" style="text-decoration:none;color: #428BCA;"><b>Start |</b></a>&nbsp;';
    }
    
    function build_stop_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class=""  title="Stop" style="text-decoration:none;color: #428BCA;" ><b>Stop |</b></a>&nbsp;';
    }
    function build_reload_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class=""  title="reload" style="text-decoration:none;color: #428BCA;"><b>Reload |</b></a>&nbsp;';
    }
    function build_rescan_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class=""  title="rescan" style="text-decoration:none;color: #428BCA;"><b>Rescan</b></a>&nbsp;';
    }
    function build_add_payment_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Recharge" ><i class="fa fa-usd fa-fw"></i></a>&nbsp;';
    }
    
    function build_add_download_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class="btn btn-royelblue btn-sm"  title="Download Invoice" ><i class="fa fa-cloud-download fa-fw"></i></a>&nbsp;';
    }
/*
* Purpose : Add following for resent icon
* Version 2.1
*/
    function build_edit_button_resend($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Resend Mail"><i class="fa fa-repeat"></i></a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Resend Mail"><i class="fa fa-repeat"></i></a>&nbsp;';
        }
    }
/*
*----------------------------------------------------------------------------
*/
    function get_only_numeric_val($select = "", $table = "", $string) {
        return filter_var($string, FILTER_SANITIZE_NUMBER_INT);
    }

function mail_to_users($type, $accountinfo,$attachment="",$amount="") {
        
	$settings_reply_email = 'astpp@astpp.com';
	$where = array('accountid' =>'1');
	if($accountinfo['reseller_id'] > 0){
        	$where = array('accountid' =>$accountinfo['reseller_id']);
	}
	$query = $this->CI->db_model->getSelect("emailaddress", "invoice_conf", $where);
	$query = $query->result();
	$settings_reply_email = $query[0]->emailaddress;

	$where = array('name' =>'company_name');
        $query = $this->CI->db_model->getSelect("*", "system", $where);
        $query = $query->result();
        $company_name = $query[0]->value;
	
	$where = array('name' =>'company_website');
        $query = $this->CI->db_model->getSelect("*", "system", $where);
        $query = $query->result();
        $company_website = $query[0]->value;

        $where = array('name' => $type);
        $query = $this->CI->db_model->getSelect("*", "default_templates", $where);
        $query = $query->result();
        $message = $query[0]->template;
        $useremail = $accountinfo['email'];
		
	$message = html_entity_decode($message);
	$message = str_replace("#COMPANY_EMAIL#", $settings_reply_email, $message);
	$message = str_replace("#COMPANY_NAME#", $company_name, $message);
	$message = str_replace("#COMPANY_WEBSITE#", $company_website, $message);
	$message = str_replace("</p>", "", $message);
	
        switch ($type) {
            case 'email_add_user':
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('#NUMBER#', $accountinfo['number'], $message);
                $message = str_replace('#PASSWORD#', $accountinfo['password'], $message);
                $subject = $query[0]->subject;
                break;
	    case 'add_sip_device':
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('#USERNAME#', $accountinfo['number'], $message);
                $message = str_replace('#PASSWORD#', $accountinfo['password'], $message);
                $subject = $query[0]->subject;
                break;
            case 'voip_account_refilled':
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $subject = $query[0]->subject;
                break;
            case 'email_calling_card':
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('#CARDNUMBER#', $accountinfo['cardnumber'], $message);
                $message = str_replace('#PIN#', $accountinfo['pin'], $message);
                $message = str_replace('#BALANCE#', $accountinfo['balance'], $message);
                $subject = $query[0]->subject;
                break;
            case 'email_low_balance';
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $to_currency = $this->CI->common->get_field_name('currency', 'currency', $accountinfo['currency_id']);
                $balance = $this->CI->common_model->calculate_currency($accountinfo['balance'], "", $to_currency, true, true);                
                $message = str_replace('#BALANCE#', $accountinfo['balance'], $message);
                $subject = $query[0]->subject;
		break;
	  case 'email_new_invoice';
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('#AMOUNT#', $amount, $message);
                $message = str_replace('#INVOICE_NUMBER#', $amount, $message);
                $subject = $query[0]->subject;
                $subject = str_replace("#INVOICE_NUMBER#", $amount, $subject);
		break;	
	  case 'email_add_did';
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('#NUNBER#', $amount, $message);
                $subject = $query[0]->subject;
                $subject = str_replace("#NUNBER#", $amount, $subject);
		break;	
	  case 'email_remove_did';
                $message = str_replace('#NAME#', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('#NUNBER#', $amount, $message);
                $subject = $query[0]->subject;
                $subject = str_replace("#NUNBER#", $amount, $subject);
		break;	
	  
	  
	  
        }  
        $subject = str_replace("#NAME#", $accountinfo['first_name']." ".$accountinfo['last_name'], $subject);
        $subject = str_replace("#COMPANY_NAME#", $company_name, $subject);
        $this->emailFunction($settings_reply_email, $useremail, $subject, $message,$company_name,$attachment);
        return true;
    }
    function emailFunction($from, $to, $subject, $message,$company_name="",$attachment="") {
        $this->CI->email->from($from, $company_name);
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        eval("\$message = \"$message\";");
        $this->CI->email->message($message);
        if($attachment!="")
	  $this->CI->email->attach($attachment);
        $this->CI->email->send();
        return true;
    }

    function convert_GMT_to($select = "", $table = "", $date) {
//        return $date;
        return $this->CI->timezone->display_GMT($date);
    }

    function convert_GMT($date) {
        return $this->CI->timezone->convert_to_GMT($select = "", $table = "", $date);
    }

    function convert_to_ucfirst($select = "", $table = "", $str_value) {
        return ucfirst($str_value);
    }
    function set_charge_type($status =''){
        $status_array = array('1' => 'Accounts', '2' => 'Rate Group');
        return $status_array;
    }
    function build_concat_string($select, $table, $id_where = '') {
        $select_params = explode(',', $select);
        $where = array("1");
        if ($id_where != '') {
            $where = array("id" => $id_where);
        }
        $select_params = explode(',', $select);
        $cnt_str = " $select_params[0],' ',$select_params[1],' ','(',$select_params[2],')' ";
        $select = "concat($cnt_str) as $select_params[2] ";
        $drp_array = $this->CI->db_model->getSelect($select, $table, $where);
        $drp_array = $drp_array->result();
        if (isset($drp_array[0]))
            return $drp_array[0]->$select_params[2];
    }
	  function get_invoice_total($select='',$table='',$id){
        $where_arr=array('invoiceid'=>$id,'text'=>"Total");
	$this->CI->db->where($where_arr);
	$this->CI->db->select('value');
	$result=$this->CI->db->get('invoices_total');
	if($result->num_rows() > 0 ){
	  $result=$result->result_array();
	  $result1 = $this->convert_to_currency('','',$result[0]['value']);
	  return $result1;
	}
	else{
	  return null;
	}
    }
        function get_array($select, $table_name,$where=false){
	$new_array = array();
        $select_params = array();
        $select_params = explode(",", $select);
        if (isset($select_params[3])) {
	    $cnt_str = " $select_params[1],'(',$select_params[2],' ',$select_params[3],')' ";
	    $select = "concat($cnt_str) as $select_params[3] ";
            $field_name = $select_params[3];
        }elseif(isset($select_params[2])){
	    $cnt_str = " $select_params[1],' ','(',$select_params[2],')' ";
	    $select = "concat($cnt_str) as $select_params[2] ";
            $field_name = $select_params[2];
        }
        else{
            $select=$select_params[1];
            $field_name=$select_params[1];
        }
        if($where){
         $this->CI->db->where($where);
        }
        $this->CI->db->select("$select_params[0],$select",false);
        $result = $this->CI->db->get($table_name);
        foreach ($result->result_array() as $key => $value) {
             $new_array[$value[$select_params[0]]] = $value[$field_name];
        }
        ksort($new_array);
        return $new_array;
     }
 function get_timezone_offset(){
	  $gmtoffset=0;
	  $accountinfo=$this->CI->session->userdata('accountinfo');
	  $account_result=$this->CI->db->get_where('accounts',array('id'=>$accountinfo['id']));
	  $account_result=$account_result->result_array();
	  $accountinfo=$account_result[0];
	  
// 	  $timezone_id=$this->get_field_name("id",'timezone',array("gmttime"=>Common_model::$global_config['system_config']['timezone']));
// 	  if($accountinfo['type']== -1){
// 	  $timezone_result=$this->CI->db->get_where('timezone',array('id'=>$timezone_id));
// 	      if($timezone_result->num_rows() > 0){
// 		$timezone_result=$timezone_result->result_array();
// 		$gmtoffset=$timezone_result[0]['gmtoffset'];
// 	      }
// 	  }
	  $timezone_id_arr=array($accountinfo['timezone_id']);
	  $this->CI->db->where_in('id',$timezone_id_arr);
	  $this->CI->db->select('gmtoffset');		  
	  $this->CI->db->from('timezone');
	  $timezone_result=$this->CI->db->get();
	  if($timezone_result->num_rows() > 0){
	   
	      $timezone_result=$timezone_result->result_array();
	      foreach($timezone_result as $data){
	      $gmtoffset+=$data['gmtoffset'];
	      }
	  }
// 	  echo $gmtoffset;exit;
	  return $gmtoffset;
     }
     function subreseller_list($parent_id=''){
      $customer_id = $parent_id;
      $query='select id from accounts where reseller_id = '.$parent_id .' AND deleted = 0 AND type in (1)';
      $reseller_get=$parent_id;
      $result=$this->CI->db->query($query);
      if($result->num_rows() > 0 ){
	$result=$result->result_array();
	foreach($result as $data){
	  if(isset($data['id']) && $data['id'] != ''){
	    $reseller_get.=",".$this->subreseller_list($data['id'])."";
	  }
	}
      }
      return $reseller_get;
     }
    /** Version 2.1
     * Purpose : Set default data for new created profile
     **/
    function sip_profile_date(){
    $defualt_profile_data ='{"rtp_ip":"$${local_ip_v4}","dialplan":"XML","user-agent-string":"ASTPP","debug":"0","sip-trace":"no","tls":"false","inbound-reg-force-matching-username":"true","disable-transcoding":"true","all-reg-options-ping":"false","unregister-on-options-fail":"true","log-auth-failures":"true","status":"0","inbound-bypass-media":"false","inbound-proxy-media":"false","disable-transfer":"true","enable-100rel":"false","rtp-timeout-sec":"60","dtmf-duration":"2000","manual-redirect":"false","aggressive-nat-detection":"false","enable-timer":"false","minimum-session-expires":"120","session-timeout-pt":"1800","auth-calls":"true","apply-inbound-acl":"default","inbound-codec-prefs":"PCMU,PCMA,G729","outbound-codec-prefs":"PCMU,PCMA,G729","inbound-late-negotiation":"false"}';
    return $defualt_profile_data;
    
    } 
    /*=====================================================================*/
/*
* Purpose : Add following for mass mail and mail history
* Version 2.1
*/
  function set_search_temp($select= ''){
        $status_array = array("0" => "--Select--",
            "1" => "Voip account refilled",
            "3" => "Email add user",
            "4" => "Add sip device",
            "8" => "Email add did",
            "9" => "Email remove did",
            "10" => "Email new invoice",
            "11" => "Email low balance",

        );
        return $status_array;
}
    function email_status($select = "", $table = "", $status) {
        return ($status == 0) ? "Sent" : "Not Sent";
    }
    function email_search_status($select= ''){
        $status_array = array("" => "--Select--",
            "0" => "Sent",
            "1" => "Not Sent"
        );
        return $status_array;
    }
    /*=====================================================================*/


/*
* Purpose : Add following for setting page
* Version 2.1
*/
    function paypal_status($status = '') {
        $status_array = array( '0' => 'Enable','1' => 'Disable',);
        return $status_array;
    }
    function paypal_mode($status = '') {
        $status_array = array( '0' => 'Live','1' => 'Sandbox',);
        return $status_array;
    }
    function paypal_fee($status = '') {
        $status_array = array( '0' => 'Paid By Admin','1' => 'Paid By Customer',);
        return $status_array;
    }
    function email(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }

    function smtp(){
	return $this->set_allow();
    }
    
    function debug(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
    function opensips(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
    function cc_ani_auth(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
     function calling_cards_balance_announce(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
     function calling_cards_timelimit_announce(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
    function calling_cards_rate_announce(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
    function startingdigit(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
    function SMPT(){
	$status_array = array( '1' => 'Enable','0' => 'Disable',);
        return $status_array;
    }
    function country(){
	return $this->CI->common_model->get_country_list();
    }
    function default_timezone(){
	return $this->CI->db_model->build_dropdown('id,gmtzone', 'timezone');
    }
    function timezone(){
	return $this->CI->db_model->build_dropdown('gmttime,gmttime', 'timezone');
    }
    function base_currency(){
	return $this->CI->db_model->build_dropdown('currency,currencyname', 'currency');
    }
/*************************************************************************************/
}

