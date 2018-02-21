<?php

class User extends MX_Controller {

    function User() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->helper('form');
        $this->load->library("astpp/form");
        $this->load->library("user_form");
        $this->load->model('Auth_model');
        $this->load->model('Astpp_common');
    }

    function index() {
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . 'login/login');

        $data['username'] = $this->session->userdata('user_name');
        $account_data = $this->session->userdata("accountinfo");
        $where = array('id' => $account_data["id"]);
        $account = $this->db_model->getSelect("*", "accounts", $where);
        $account = $account->result_array();
        $account[0]['country_id'] = $this->common->get_field_name('country', 'countrycode', $account[0]['country_id']);
        $account[0]['timezone_id'] = $this->common->get_field_name('gmtzone', 'timezone', $account[0]['timezone_id']);
        $data["account"] = $account[0];

        $data["account"]["balance"] = $this->common_model->calculate_currency($data["account"]["balance"]*-1);
        $data["account"]["credit_limit"] = $this->common_model->calculate_currency($data["account"]["credit_limit"]);
	if ($this->session->userdata('logintype') != 0) {
	    redirect(base_url().'dashboard');
	}else{
	  $this->load->view('view_user_dashboard', $data);
	}
    }
    function validate_customer_data($data){
        $this->load->module('accounts/accounts');
        $id = "";
        if(isset($data["id"]) && $data["id"] != ""){
            $id = $data["id"];
        }
        $where = array("first_name"=>$data["first_name"],"last_name"=>$data["last_name"]);
        $name_flag = $this->accounts_model->account_authentication($where,$id);
        if($name_flag == 0){
            $where = array("email"=>$data["email"]);
            $email_flag = $this->accounts_model->account_authentication($where,$id);
            if($email_flag == 0){
                return "1";
            }else{
                return "Duplicate email Found Email must be unique.";
            }
        }else{
            return "Duplicate Name Found First Name and Last Name must be unique.";
        }
        return "0";
    }

    function user_edit_account() {
        $this->load->module('accounts/accounts');
        $account_data = $this->session->userdata("accountinfo");

        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->accounts->accounts_form->get_user_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
                    $this->accounts->accounts_model->edit_account($add_array, $add_array['id']);
                    $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
                    redirect(base_url() . 'user/user/');
                } else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
            $this->load->view('view_user_details', $data);
        } else {

            $data['page_title'] = 'Edit User Account';
            $where = array('id' => $account_data["id"]);
            $account = $this->db_model->getSelect("*", "accounts", $where);
            $data["account_data"] = $account->result_array();

            foreach ($account->result_array() as $key => $value) {
                $editable_data = $value;
            }
            $data['form'] = $this->form->build_form($this->accounts->accounts_form->get_user_form_fields(), $editable_data);
            $this->load->view('view_user_details', $data);
        }
    }

    function user_didlist() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'DIDs List';

        $this->load->module('did/did');
        $data['grid_fields'] = $this->did->did_form->build_did_list_for_user();
        $data["grid_buttons"] = array();

        $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');
        $this->load->view('view_did_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function user_didlist_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('did/did');
        $this->did->user_did($account_data["id"]);
    }

    function user_invoice_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Invoices List';
        $this->session->set_userdata('advance_search', 0);

        $this->load->module('invoices/invoices');
        $data['form_search'] =$this->form->build_serach_form($this->invoices->invoices_form->get_invoice_search_form());
        
        $data['grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

        $this->load->view('view_invoices_list', $data);
    }

    function user_invoice_list_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('invoices/invoices');
        $this->invoices->customer_invoices($account_data["id"]);
    }

    function user_animap_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'ANI MAP List';
        $this->session->set_userdata('advance_search', 0);

        $this->load->module('accounts/accounts');
        $data['grid_fields'] = $this->accounts->accounts_form->build_animap_list_for_user();

        $this->load->view('view_animap_list', $data);
    }

    function user_animap_list_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('accounts/accounts');
        $this->accounts->user_animap_json($account_data["id"]);
    }
    function user_did_edit($edit_id = '') {
        $data['page_title'] = 'Edit Dids';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "dids", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $edit_data['setup'] = $this->common_model->to_calculate_currency($edit_data['setup'], '', '', false, false);
        $edit_data['disconnectionfee'] = $this->common_model->to_calculate_currency($edit_data['disconnectionfee'], '', '', false, false);
        $edit_data['monthlycost'] = $this->common_model->to_calculate_currency($edit_data['monthlycost'], '', '', false, false);
        $edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data['connectcost'], '', '', false, false);
        $edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data['cost'], '', '', false, false);

        $data['call_type'] = form_dropdown('call_type', $this->common->set_call_type(), $edit_data["call_type"]);        
        $data["didinfo"] = $edit_data;
        $this->load->view('view_user_did_edit', $data);
    }
    

    function user_dids_action($action, $did_id = "") {
        if ($action == "add") {
            $account_data = $this->session->userdata("accountinfo");
            $did_id = $this->input->post("free_did_list", true);
            if ($did_id != "") {
                if ($account_data["reseller_id"] != 0){
                    $did_query = $this->db_model->getSelect("*", "reseller_pricing", array("id" => $did_id));
                }else{
                    $did_query = $this->db_model->getSelect("*", "dids", array("id" => $did_id));
                }
                $did_arr = $did_query->result_array();
                $available_bal = $this->db_model->get_available_bal($account_data);
                if($available_bal >= $did_arr[0]["setup"]){
                    if($did_arr[0]["allocation_bill_status"] == 1){
                        $available_bal = $this->db_model->update_balance($did_arr[0]["setup"],$account_data["id"],"debit");
                    }
                    $this->db_model->update("dids", array("accountid" => $account_data["id"],"call_type"=>$this->input->post("call_type", true)), array("id" => $did_id));
                    redirect(base_url() . "user/user_didlist/");
                }else{
                    $this->session->set_flashdata('astpp_notification', 'Insuffiecient fund to purchase this did');
                    redirect(base_url() . "user/user_didlist/");
                }
            } else {
                redirect(base_url() . "user/user_didlist/");
            }
        }
        if($action == "edit"){
            $update_arr = array("call_type"=>$this->input->post("call_type", true),
                                "extensions"=>$this->input->post("extension", true)
                                );
            $this->db_model->update("dids",$update_arr, array("id" =>$this->input->post("didid", true)));
            redirect(base_url() . "user/user_didlist/");
        }
        if ($action == "delete") {
            $this->db_model->update("dids", array("accountid" => "0"), array("id" => $did_id));
            redirect(base_url() . "user/user_didlist/");
        }
    }

    function user_animap_action($action, $aniid = "") {
        if ($action == "add") {
            $account_data = $this->session->userdata("accountinfo");
            $ani = $this->input->post("ANI", true);
            if ($ani != "") {
                $insert_arr = array("number" => $this->input->post("ANI", true), "accountid" => $account_data["id"],
                    "context" => $this->input->post("context", true));
                $this->db->insert("ani_map", $insert_arr);
                redirect(base_url() . "user/user_animap_list/");
            } else {
                redirect(base_url() . "user/user_animap_list/");
            }
        }
        if ($action == "delete") {
            $this->db_model->delete("ani_map", array("id" => $aniid));
            redirect(base_url() . "user/user_animap_list/");
        }
    }

    function user_cdrs_report() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'CDRs Report';
        $this->load->module('reports/reports');
        $data['grid_fields'] = $this->reports->reports_form->build_report_list_for_user();
        $data['form_search'] = $this->form->build_serach_form($this->reports->reports_form->get_user_cdr_form());
        $data['grid_title'] = "CDRs Report";
        $this->load->view('view_report_list', $data);
    }

    function user_cdrs_report_json() {
        $this->load->module('reports/reports');
        $this->reports->user_cdrreport();
    }

    function user_cdrs_report_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('user_cdrs_report_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_cdrs_report/');
        }
    }

    function user_cdrs_report_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function user_payment_report() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Payment Report';

        $this->load->module('reports/reports');
        $data['grid_fields'] = $this->reports->reports_form->build_payment_report_for_user();
        $data['grid_title'] = "Payment Report";

        $this->load->module('reports/reports');
        $data['form_search'] = $this->form->build_serach_form($this->reports->reports_form->get_user_cdr_payment_form());

        $this->load->view('view_report_list_payment', $data);
    }

    function user_payment_report_json() {
        $this->load->module('reports/reports');
        $this->reports->user_paymentreport();
    }

    function user_sipdevices() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'SIP Devices List';
        
        $this->load->module('freeswitch/freeswitch');
        $data["fs_grid_buttons"] = $this->freeswitch->freeswitch_form->build_grid_buttons_for_user();
        $data['grid_fields'] = $this->freeswitch->freeswitch_form->build_devices_list_for_customer();
        $data['form_search'] =$this->form->build_serach_form($this->freeswitch->freeswitch_form->get_freeswith_search_form_user());
        $data['grid_title'] = "SIP Devices";
        $this->load->view('view_sipdevices_list', $data);
    }
    function user_sipdevices_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('freeswitch/freeswitch');
        $this->freeswitch->customer_fssipdevices_json($account_data["id"]);
    }
    function user_fssipdevices_action($action, $id="", $accountid="") {
        $this->load->module('freeswitch/freeswitch');
        if ($action == "delete") {
            $this->freeswitch->freeswitch_model->delete_freeswith_devices($id);
            redirect(base_url() . "user/user_sipdevices/");
        }
        if ($action == "edit") {
            $this->freeswitch->customer_fssipdevices_edit($id, $accountid);
        }
        if ($action == "add") {
            $account_data = $this->session->userdata("accountinfo");
            $this->freeswitch->customer_fssipdevices_add($account_data["id"]);
        }
    }
    
    function user_rates_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Rates List';
        $this->session->set_userdata('advance_search', 0);

        $this->load->module('rates/rates');
        $data['grid_fields'] = $this->rates->rates_form->build_inbound_list_for_user();
        $data['form_search'] = $this->form->build_serach_form($this->rates->rates_form->get_user_rates_search_form());
        $this->load->view('view_rates_list', $data);
    }

    function user_rates_list_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('rates/rates');
        $this->rates->user_inboundrates_list_json($account_data["id"]);
    }
    function user_payment($action=""){
        $this->load->module("user/payment");
        if($action=="GET_AMT"){
            $amount = $this->input->post("value",true);
            $this->payment->convert_amount($amount);
        }else{
            $this->payment->index();
        }
    }
    
    function user_change_password()
    {
        $data['username'] = $this->session->userdata('user_name');	
        $data['page_title'] = "Change Password";
        if(isset($_POST) && !empty($_POST)){
            $accountinfo = $this->session->userdata['accountinfo'];
            $pass_count = $this->usermodel->validate_password($_POST["old_pass"],$accountinfo['accountid']);
            if($pass_count>0){
                $this->load->library('email');
                $pass_count = $this->usermodel->update_password($_POST["new_pass"],$accountinfo['accountid']);
                $this->redirect_notification("your account password updated successfully",'/user/change_password/');						
            }else{
                $this->redirect_notification("your have enter wrong password ",'/user/change_password/');						
            }
        }
        $this->load->view('view_user_change_password',$data);	    
    }	
    function user_ipmap_list(){
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = "IP List";
        $data["account_data"] = $this->session->userdata("accountinfo");      
        $this->load->module('accounts/accounts');
        $data['ipmap_grid_field'] = $this->accounts->accounts_form->build_ipmap_for_user();
        $this->load->view('view_ip_map',$data);
        
    }
    function user_ipmap_json(){
        
        $json_data = array();
        $this->load->module('accounts/accounts');
        $accountdata=$this->session->userdata['accountinfo'];
        $where = array("accountid" => $accountdata['id']);
        $count_all = $this->db_model->countQuery("*", "ip_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ip_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);

        $grid_fields = json_decode($this->accounts_form->build_ipmap_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);                
    }
    function user_ipmap_action($action, $id=""){
        
        if($action == 'add'){
            $ip = $this->input->post("ip", true);
            if($ip!=""){
            $flag=0;
            $where = array("ip" => $this->input->post('ip'),"prefix" => $this->input->post('prefix'));
            $getdata= $this->db_model->countQuery("*", "ip_map", $where);
            if($getdata==0){
            $insert_arr=  array();
            $insert_arr = array(
                "name"=>$this->input->post("name"),
                "ip"=>$this->input->post("ip"),
                "accountid"=>$this->session->userdata['accountinfo']['id'],
                "pricelist_id"=>$this->session->userdata['accountinfo']['pricelist_id'],
                "prefix"=>$this->input->post('prefix'),
                "context"=>'default'
            );
            $this->db->insert("ip_map", $insert_arr);
            $this->session->set_flashdata('astpp_notification', 'IP Map Added Successfully');
            redirect(base_url() . "user/user_ipmap_list/");
            exit;
            }
            if($getdata>=1){
//                 echo json_encode(array("SUCCESS"=>" Duplication Found!! IP Address and Prefix Combination are Not Unique"));
             $this->session->set_userdata('astpp_notification', 'Duplication Found!! IP Address and Prefix Combination are Not Unique');
             redirect(base_url() . "user/user_ipmap_list/");     
             exit;
            }
        }
        else {
                redirect(base_url() . "user/user_ipmap_list/");  
                exit;
        }
        }
        if ($action == "delete") {
            $this->db_model->delete("ip_map", array("id" => $id));
            $this->session->set_flashdata('astpp_notification', 'IP Map Deleted Successfully');
            redirect(base_url() . "user/user_ipmap_list/");
            exit;
        }
        }
}

?>
