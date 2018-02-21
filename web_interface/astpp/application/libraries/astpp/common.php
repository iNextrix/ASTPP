<?php

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
    function set_invoice_option($select = "", $table = "", $call_type=""){

        $uri_segment = $this->CI->uri->segments;
        if(isset($uri_segment[3])){
            $field_name = $this->CI->db_model->getSelect("sweep_id,invoice_day","accounts",array("id"=>$uri_segment[3]));
            $field_name= $field_name->result_array();
            $select = $field_name[0]["sweep_id"];
            $invoice_daya= $field_name[0]["invoice_day"];
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
            if(isset($uri_segment[3])){
                return $mon_arr;
            }else{
                $day = date('d');
                $month_drp = form_dropdown(array("name"=>'invoice_day',"class"=>"invoice_day"),$mon_arr,$day );
                return $month_drp;
            }
        }
    }
    function set_status($status = '') {
        $status_array = array('1' => 'Active', '0' => 'Inactive');
        return $status_array;
    }

    function set_allow($status = '') {
        $status_array = array('1' => 'Yes', '0' => 'No');
        return $status_array;
    }
    function get_allow($select = "", $table = "", $status) {
        return ($status == 1) ? "Yes" : "No";
    }

    function set_call_type($call_type = "") {
        $call_type_array = array('0' => 'PSTN', '1' => 'Local', '2' => 'Other');
        return $call_type_array;
    }

    function get_call_type($select = "", $table = "", $call_type) {
        $call_type_array = array('0' => 'PSTN', '1' => 'Local', '2' => 'Other');
        return $call_type_array[$call_type];
    }

    function set_sip_config_option($option = "") {
        $config_option = array("true" => "True", "false" => "False");
        return $config_option;
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
        return ($status == 1) ? "Active" : "Inactive";
    }

    function get_account_balance($select = "", $table = "", $amount) {
        $this->CI->load->model('common_model');
        if ($amount == 0) {
            return $amount;
        } else {
	    $balance = $this->CI->common_model->add_calculate_currency(($amount*-1), "", '', true, true);
//             $balance = $this->CI->common_model->calculate_currency($amount* -1);
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

    function get_account_type($select = "", $table = "", $PTE) {
        return ($PTE == 1) ? "Post Paid" : "Pre Paid";
    }

    function get_payment_by($select = "", $table = "", $type) {
        if ($type == '-1') {
            $type = "Admin";
        }else{
	    $type = $this->get_field_name("number","accounts", array("id"=>$type));
	}
        return $type;
    }

    function set_payment_type($status = '') {
        $status_array = array('0' => 'Cash', '1' => 'Cheque', '2' => 'Transfer');
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
            "DID" => "DID"
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
            }
        }
        return $ret_url;
    }

    function build_edit_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="icon edit_image" rel="facebox" title="Update">&nbsp;</a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="icon edit_image" title="Update">&nbsp;</a>&nbsp;';
        }
    }

    function build_bluebox_login($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="javascript:void(0);" class="icon bluebox_image" onClick="return get_login_bluebox(' . $linkid . ');" title="Bluebox Login">&nbsp;</a>&nbsp;';
    }

    function build_delete_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class="icon delete_image" title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
    }

    function build_view_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="icon details_image" rel="facebox" title="View Details">&nbsp;</a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="icon details_image" title="View Details">&nbsp;</a>&nbsp;';
        }
    }

    function build_add_taxes_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="icon tax_image" rel="facebox" title="Add Account Taxes">&nbsp;</a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="icon tax_image" title="Add Account Taxes">&nbsp;</a>&nbsp;';
        }
    }

    function build_add_callerid_button($button_params, $linkid) {
        $link = base_url() . $button_params->url . "" . $linkid;
        if ($button_params->mode == 'popup') {
            return '<a href="' . $link . '" class="icon callerid_image" rel="facebox" title="Add CallerID">&nbsp;</a>&nbsp;';
        } else {
            return '<a href="' . $link . '" class="icon callerid_image" title="Add CallerID">&nbsp;</a>&nbsp;';
        }
    }

    function build_add_payment_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class="icon payment_image" rel="facebox" title="Make Payment" >&nbsp;</a>';
    }
    
    function build_add_download_button($url, $linkid) {
        $link = base_url() . $url . "" . $linkid;
        return '<a href="' . $link . '" class="icon pdf_image"  title="Download Invoice" >&nbsp;</a>';
    }


    function get_only_numeric_val($select = "", $table = "", $string) {
        return filter_var($string, FILTER_SANITIZE_NUMBER_INT);
    }

    function mail_to_users($type, $accountinfo,$attachment="",$amount="") {
        //$settings_reply_email = 'astpp@astpp.com';
	
	$where = array('name' =>'company_email');
        $query = $this->CI->db_model->getSelect("*", "system", $where);
        $query = $query->result();
	$settings_reply_email = $query[0]->value;

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
	$message = str_replace("#YOUR COMPANY EMAIL#", $settings_reply_email, $message);
	$message = str_replace("#YOUR COMPANY NAME#", $company_name, $message);
	$message = str_replace("#YOUR COMPANY WEBSITE#", $company_website, $message);
	$message = str_replace("</p>", "", $message);
	
        switch ($type) {
            case 'email_add_user':
                $message = str_replace('<NAME>', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('<NUMBER>', $accountinfo['number'], $message);
                $message = str_replace('<PASSWORD>', $accountinfo['password'], $message);
                $subject = $query[0]->subject;
                break;
            case 'voip_account_refilled':
                $message = str_replace('<NAME>', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $subject = $query[0]->subject;
                break;
            case 'email_calling_card':
                $message = str_replace('<NAME>', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('<CARDNUMBER>', $accountinfo['cardnumber'], $message);
                $message = str_replace('<PIN>', $accountinfo['pin'], $message);
                $message = str_replace('<BALANCE>', $accountinfo['balance'], $message);
                $subject = $query[0]->subject;
                break;
            case 'email_low_balance';
                $message = str_replace('<NAME>', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $to_currency = $this->CI->common->get_field_name('currency', 'currency', $accountinfo['currency_id']);
                $balance = $this->CI->common_model->calculate_currency($accountinfo['balance'], "", $to_currency, true, true);                
                $message = str_replace('<BALANCE>', $accountinfo['balance'], $message);
                $subject = $query[0]->subject;
		break;
	  case 'email_new_invoice';
                $message = str_replace('<NAME>', $accountinfo['first_name']." ".$accountinfo['last_name'], $message);
                $message = str_replace('<AMOUNT>', $amount, $message);
                $subject = $query[0]->subject;
		break;	
        }        
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

}

