<?php

class Accounts extends MX_Controller {

    function Accounts() {
        parent::__construct();
        $this->load->helper('template_inheritance');

        $this->load->library('accounts_form');
        $this->load->library('astpp/form');

        $this->load->model('common_model');
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->model('accounts_model');
        $this->load->model('Astpp_common');
        $this->load->config('accounts_config');
        $this->protected_pages = array('account_list');
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/login/login');
    }

    function customer_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Customer Account';
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_form_fields(), '');
        
        $this->load->view('view_accounts_create', $data);
    }
    function customer_invoice_option(){
        $sweepid = $this->input->post("sweepid",true);
        $invoice_dropdown = $this->common->set_invoice_option($sweepid,"","");
        echo $invoice_dropdown;
    }
    function validate_customer_data($data){
        $id = "";
        if(isset($data["id"]) && $data["id"] != ""){
            $id = $data["id"];
        }
	$where = array("email"=>$data["email"]);
	$email_flag = $this->accounts_model->account_authentication($where,$id);
	if($email_flag == 0){
	    return "1";
	}else{
            return "Duplicate Email Address Found Email must be unique.";
        }
        return "0";
    }
    function customer_edit($edit_id = '') {
        $data['page_title'] = 'Edit Customer Account';
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
        } else {
            $reseller = "0";
        }

        $where = array('id' => $edit_id, "reseller_id" => $reseller);
        $account = $this->db_model->getSelect("*", "accounts", $where);

        if ($account->num_rows > 0) {
            $data["account_data"] = $account->result_array();

            $data["ipmap_grid_field"] = json_decode($this->accounts_form->build_ip_list_for_customer($edit_id, "customer"));
            $data["animap_grid_field"] = json_decode($this->accounts_form->build_animap_list_for_customer($edit_id));
            $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges(), '');

            $this->load->module('package/package');
            $data['charges_grid_field'] = $this->package->package_form->build_charges_list_for_customer($edit_id, "customer");

            $this->load->module('rates/rates');
            $data['pattern_grid_fields'] = $this->rates->rates_form->build_pattern_list_for_customer($edit_id);
            $data['pattern_grid_buttons'] = $this->rates->rates_form->set_pattern_grid_buttons($edit_id);

            $this->load->module('freeswitch/freeswitch');
            $data["fs_grid_buttons"] = $this->freeswitch->freeswitch_form->fsdevices_build_grid_buttons($edit_id);
            $data['sipiax_grid_field'] = $this->freeswitch->freeswitch_form->build_devices_list_for_customer();

            $this->load->module('opensips/opensips');
            $data["opensips_grid_buttons"] = $this->opensips->opensips_form->opensips_customer_build_grid_buttons($edit_id);
            $data['opensips_grid_field'] = $this->opensips->opensips_form->opensips_customer_build_opensips_list($edit_id);

            $data['ip_pricelist'] = form_dropdown('ip_pricelist', $this->db_model->build_dropdown("id,name", "pricelists", "reseller_id", "0"), '');

            $this->load->module('did/did');
            $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer($edit_id, "customer");
            if ($this->session->userdata('logintype') == 1) {
                $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,note", "reseller_pricing", "where_arr", array("reseller_id" => $this->session->userdata('logintype'))), '');
            } else {
                $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');
            }
            $this->load->module('invoices/invoices');
            $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

            $this->load->module('reports/reports');
            $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $data['form'] = $this->form->build_form($this->accounts_form->get_customer_form_fields(), $edit_data);
            $this->load->view('view_customer_details', $data);
        } else {
            redirect(base_url() . 'accounts/customer_list/');
        }
    }

    function customer_save() {
        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
		  redirect(base_url() . 'accounts/customer_list/');
		  exit;
                } else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
            $data["account_data"]["0"] = $add_array;
	    $edit_id = $add_array["id"];
            $data["ipmap_grid_field"] = json_decode($this->accounts_form->build_ip_list_for_customer($edit_id, "customer"));
            $data["animap_grid_field"] = json_decode($this->accounts_form->build_animap_list_for_customer($edit_id));
            $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges(), '');

            $this->load->module('package/package');
            $data['charges_grid_field'] = $this->package->package_form->build_charges_list_for_customer($edit_id, "customer");

            $this->load->module('rates/rates');
            $data['pattern_grid_fields'] = $this->rates->rates_form->build_pattern_list_for_customer($edit_id);
            $data['pattern_grid_buttons'] = $this->rates->rates_form->set_pattern_grid_buttons($edit_id);

            $this->load->module('freeswitch/freeswitch');
            $data["fs_grid_buttons"] = $this->freeswitch->freeswitch_form->fsdevices_build_grid_buttons($edit_id);
            $data['sipiax_grid_field'] = $this->freeswitch->freeswitch_form->build_devices_list_for_customer();

            $this->load->module('opensips/opensips');
            $data["opensips_grid_buttons"] = $this->opensips->opensips_form->opensips_customer_build_grid_buttons($edit_id);
            $data['opensips_grid_field'] = $this->opensips->opensips_form->opensips_customer_build_opensips_list($edit_id);

            $data['ip_pricelist'] = form_dropdown('ip_pricelist', $this->db_model->build_dropdown("id,name", "pricelists", "reseller_id", "0"), '');

            $this->load->module('did/did');
            $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer($edit_id, "customer");
            if ($this->session->userdata('logintype') == 1) {
                $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,note", "reseller_pricing", "where_arr", array("reseller_id" => $this->session->userdata('logintype'))), '');
            } else {
                $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');
            }
            $this->load->module('invoices/invoices');
            $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

            $this->load->module('reports/reports');
            $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();
            $this->load->view('view_customer_details', $data);
        } else {
            $data['page_title'] = 'Create Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->add_account($add_array);
		  $this->session->set_userdata('astpp_notification', 'Account Setup Completed!');
		  redirect(base_url() . 'accounts/customer_list/');
		  exit;
                } else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
	    $this->load->view('view_accounts_create', $data);
        }
    }

    function customer_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('customer_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/customer_list/');
        }
    }

    function customer_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    /**
     * -------Here we write code for controller accounts functions add_callerid------
     * Add caller ids against account no
     * @account_number: Account No
     */
    function customer_add_callerid($id = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Caller ID';
        $account_num = $this->accounts_model->get_account_number($id);
        $result = $this->accounts_model->get_callerid($id);
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $values) {
                $data['accountid'] = $values['accountid'];
                $data['callerid_name'] = $values['callerid_name'];
                $data['callerid_number'] = $values['callerid_number'];
                $data['status'] = $values['status'];
                $data['flag'] = '1';
            }
        } else {
            $data['accountid'] = $id;
            $data['callerid_name'] = '';
            $data['callerid_number'] = '';
            $data['status'] = '';
            $data['flag'] = '0';
        }
        $data['accountid'] = $account_num['number'];
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_callerid_fields(), $data);
        if (($this->input->post())) {
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();

                $data['accountid'] = $this->input->post('accountid');
                $data['form'] = $this->form->build_form($this->accounts_form->get_customer_callerid_fields(), $data);
            } else {
                $post_array = $this->input->post();
                if ($post_array['flag'] == '1') {
                    $this->accounts_model->edit_callerid($this->input->post());
                    $this->session->set_flashdata('astpp_notification', 'Account CallerID Updated Successfully!');
                    redirect(base_url() . 'accounts/customer_list/');
                    exit;
                } else {
                    $this->accounts_model->add_callerid($this->input->post());
                    $this->session->set_flashdata('astpp_notification', 'Account CallerID Added Successfully!');
                    redirect(base_url() . 'accounts/customer_list/');
                    exit;
                }
            }
        }

        $this->load->view('view_accounts_add_callerid', $data);
    }

    function reseller_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Reseller Account';
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_reseller_fields(), '');
        $this->load->view('view_accounts_create', $data);
    }

    function reseller_edit($edit_id = '') {
        $data['page_title'] = 'Edit Reseller Details';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "accounts", $where);

        $data["account_data"] = $account->result_array();
        $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges(), '');

        $this->load->module('package/package');
        $data['charges_grid_field'] = $this->package->package_form->build_charges_list_for_customer($edit_id, "reseller");

        $data["sipiax_grid_field"] = json_decode($this->accounts_form->build_sipiax_list_for_customer());

        $this->load->module('did/did');
        $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer($edit_id, "reseller");
        $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');


        $this->load->module('invoices/invoices');
        $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

        $this->load->module('reports/reports');
        $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_reseller_fields(), $edit_data);
        $this->load->view('view_reseller_details', $data);
    }

    function reseller_save() {
        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->accounts_form->get_form_reseller_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Reseller Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
		  redirect(base_url().'accounts/reseller_list/');
		  exit;
                }else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
	    $data["account_data"]["0"] = $add_array;
	    $edit_id = $add_array["id"];
	    $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges(), '');

	    $this->load->module('package/package');
	    $data['charges_grid_field'] = $this->package->package_form->build_charges_list_for_customer($edit_id, "reseller");

	    $data["sipiax_grid_field"] = json_decode($this->accounts_form->build_sipiax_list_for_customer());

	    $this->load->module('did/did');
	    $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer($edit_id, "reseller");
	    $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');


	    $this->load->module('invoices/invoices');
	    $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

	    $this->load->module('reports/reports');
	    $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

            $this->load->view('view_reseller_details', $data);
        } else {
            $data['page_title'] = 'Create Reseller Account';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $add_array['account_by_reseller'] = 'reseller account';
		  $this->accounts_model->add_account($add_array);
		  $this->session->set_flashdata('astpp_notification', 'Account Setup Completed!');
		  redirect(base_url() . 'accounts/reseller_list/');
		  exit;
                }else {
                    $data['validation_errors'] = json_encode(array("0"=>$check_authentication));
                }
            }
	    $this->load->view('view_accounts_create', $data);
        }
    }

    function provider_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Provider Account';
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_provider_fields(), '');

        $this->load->view('view_accounts_create', $data);
    }

    function provider_edit($edit_id = '') {
        $data['page_title'] = 'Edit Provider Details';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "accounts", $where);
        $data['account_data'] = $account->result_array();

        $data["ipmap_grid_field"] = $this->accounts_form->build_ip_list_for_customer($edit_id, "provider");

        $this->load->module('invoices/invoices');
        $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

        $this->load->module('reports/reports');
        $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_provider_fields(), $edit_data);
        $this->load->view('view_provider_details', $data);
    }

    function provider_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_provider_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Provider Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
		  redirect(base_url() . 'accounts/provider_list/');
		  exit;
                }else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
	    $data['account_data']["0"] = $add_array;
	    $edit_id = $add_array["id"];
	    $data["ipmap_grid_field"] = $this->accounts_form->build_ip_list_for_customer($edit_id, "provider");

	    $this->load->module('invoices/invoices');
	    $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

	    $this->load->module('reports/reports');
	    $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

            $this->load->view('view_provider_details', $data);
        } else {
            $data['page_title'] = 'Create Provider Account';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->add_account($add_array);
		  $this->session->set_flashdata('astpp_notification', 'Account Setup Completed!');
		  redirect(base_url() . 'accounts/provider_list/');
		  exit;
                }else {
		    $data['validation_errors'] = json_encode(array("0"=>$check_authentication));
//                     $data['validation_errors'] = $check_authentication;
                }
            }
	    $this->load->view('view_accounts_create', $data);
        }
    }

    function admin_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Admin Account';
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_admin_fields(), '');

        $this->load->view('view_accounts_create', $data);
    }

    function admin_edit($edit_id = '') {
        $data['page_title'] = 'Edit Admin Details';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "accounts", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_admin_fields(), $edit_data);
        $this->load->view('view_admin_details', $data);
    }

    function admin_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_admin_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Admin Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');

		  redirect(base_url() . 'accounts/admin_list/');
		  exit;
                }else {
		    $data['validation_errors'] = json_encode(array("0"=>$check_authentication));
//                     $data['validation_errors'] = $check_authentication;
                }
            }
            $this->load->view('view_accounts_details', $data);
        } else {
            $data['page_title'] = 'Create Admin Account';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->add_account($add_array);
		  $this->session->set_flashdata('astpp_notification', 'Account Setup Completed!');
		  redirect(base_url() . 'accounts/admin_list/');
		  exit;
                }else {
		    $data['validation_errors'] = json_encode(array("0"=>$check_authentication));
//                     $data['validation_errors'] = $check_authentication;
                }
            }$this->load->view('view_accounts_create', $data);
        }
    }

    function subadmin_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Subadmin Account';
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_subadmin_fields(), '');

        $this->load->view('view_accounts_create', $data);
    }

    function subadmin_edit($edit_id = '') {
        $data['page_title'] = 'Edit Subadmin Details';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "accounts", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_subadmin_fields(), $edit_data);
        $this->load->view('view_subadmin_details', $data);
    }

    function subadmin_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_subadmin_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Subadmin Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
		  redirect(base_url() . 'accounts/subadmin_list/');
		  exit;
                }else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
            $this->load->view('view_subadmin_details', $data);
        } else {
            $data['page_title'] = 'Create Subadmin Account';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->add_account($add_array);
		  $this->session->set_flashdata('astpp_notification', 'Account Setup Completed!');
		  redirect(base_url() . 'accounts/subadmin_list/');
		  exit;
                }else {
		    $data['validation_errors'] = json_encode(array("0"=>$check_authentication));
//                     $data['validation_errors'] = $check_authentication;
                }
            }
	    $this->load->view('view_accounts_create', $data);
        }
    }

    function callshop_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Callshop Account';
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_callshop_fields(), '');

        $this->load->view('view_callshop_details', $data);
    }

    function callshop_edit($edit_id = '') {
        $data['page_title'] = 'Edit Callshop Details';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "accounts", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_callshop_fields(), $edit_data);
        $this->load->view('view_callshop_details', $data);
    }

    function callshop_save() {

        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->accounts_form->get_form_callshop_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Callshop Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
		  redirect(base_url() . 'accounts/callshop_list/');
		  exit;
                }else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
            $this->load->view('view_accounts_details', $data);
        } else {
            $data['page_title'] = 'Create Callshop Account';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->validate_customer_data($add_array);
                if ($check_authentication == 1) {                
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $this->accounts_model->add_account($add_array);
		  $this->session->set_flashdata('astpp_notification', 'Account Setup Completed!');
		  redirect(base_url() . 'accounts/callshop_list/');
		  exit;
                }else {
                    $data['validation_errors'] = $check_authentication;
                }
            }
	    $this->load->view('view_callshop_details', $data);
        }
    }

    /**
     * -------Here we write code for controller accounts functions account_detail------
     * Account detail info through account number with checking account no exit or not.
     */
    function account_detail($accountid) { //build_account_info
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Account Details';
        $where = array('accountid' => urldecode($accountid));
        $account = $this->db_model->getSelect("*", "accounts", $where);
        $data["account_data"] = $account[0];
        $data['sweeplist'] = $this->common_model->get_sweep_list();
        $data['currency_list'] = $this->common_model->get_currency_list();
        $data['config'] = $this->common_model->get_system_config();
        $data['country_list'] = $this->common_model->get_country_list();
        $Timezone = $this->db_model->getSelect("id,gmtzone", "timezone", "");
        $Timezone_list = array();
        foreach ($Timezone as $timezone_value) {
            $Timezone_list[$timezone_value->id] = $timezone_value->gmtzone;
        }
        $data["Timezone_list"] = $Timezone_list;
        $pricelist = $this->db_model->getSelect("name", "pricelists", "");
        $pricelist_list = array();
        foreach ($pricelist as $pricelist_value) {
            $pricelist_list[$pricelist_value->name] = $pricelist_value->name;
        }

        $data["Price_list"] = $pricelist_list;
        $data["language_list"] = Common_model::$global_config['language_list'];

        /* Charges data fetch display in drop down list */
        $data['chargelist'] = $this->Astpp_common->list_applyable_charges();

        /* Charges Grid field array declaired here */
        $data['charges_grid_fields'] = array("0" => array("0" => "Description", "1" => "400"),
            "1" => array("0" => "Charges", "1" => "100"),
            "2" => array("0" => "Cycle", "1" => "100")
        );

        $this->load->view('view_accounts_details', $data);
    }

    function chargelist_json($accountid) {
        $json_data = array();
        $sweeplist = $this->common_model->get_sweep_list();

        $select = "charges.description,charges.charge,charges.sweep";
        $table = "charges";
        $jionTable = array('charge_to_account', 'accounts');
        $jionCondition = array('charges.id = charge_to_account.charge_id', 'accounts.number = charge_to_account.cardnum');
        $type = array('left', 'inner');
        $where = array('accounts.accountid' => $accountid);
        $order_type = 'charges.id';
        $order_by = "ASC";

        $account_charge_count = $this->db_model->getCountWithJion($table, $select, $where, $jionTable, $jionCondition, $type);

        $count_all = $account_charge_count;
        $config['total_rows'] = $count_all;
        $config['per_page'] = $_GET['rp'];

        $page_no = $_GET['page'];
        $json_data['page'] = $page_no;

        $json_data['total'] = $config['total_rows'];
        $perpage = $config['per_page'];
        $start = ($page_no - 1) * $perpage;
        if ($start < 0)
            $start = 0;

        $account_charge_list = $this->db_model->getAllJionQuery($table, $select, $where, $jionTable, $jionCondition, $type, $perpage, $start, $order_by, $order_type, "");
        if ($account_charge_list->num_rows > 0) {
            foreach ($account_charge_list->result() as $key => $charges_value) {
                $json_data['rows'][] = array('cell' => array(
                        $charges_value->description,
                        $charges_value->charge,
                        $sweeplist[$charges_value->sweep]
                        ));
            }
        }
        echo json_encode($json_data);
    }

    function admin_list() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Admin List';
        $data['cur_menu_no'] = 1;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_admin();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_admin();

        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_admin_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function admin_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_admin_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_admin_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function subadmin_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Sub-Admin List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_subadmin();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_subadmin();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_subadmin_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function subadmin_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_subadmin_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_subadmin_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_subadmin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function subadmin_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('subadmin_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/subadmin_list/');
        }
    }

    function subadmin_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function customer_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Customer List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_customer();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_customer();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_search_customer_form());

        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function customer_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_customer_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_customer_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_customer());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function provider_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Provider List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_provider();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_provider();

        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_provider_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function provider_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_provider_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_provider_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_provider());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function provider_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('provider_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/provider_list/');
        }
    }

    function provider_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
//        redirect(base_url() . 'accounts/customer_account_list/');
    }

    function reseller_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Reseller List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_reseller();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_reseller();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_reseller_search_form());

        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function reseller_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_reseller_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_reseller_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_reseller());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function reseller_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('reseller_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/reseller_list/');
        }
    }

    function admin_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function admin_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('admin_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/admin_list/');
        }
    }

    function reseller_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function callshop_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Call Shop List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_callshop();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_callshop();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_callshop_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    function callshop_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_callshop_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_callshop_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_callshop());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function callshop_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('callshop_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/callshop_list/');
        }
    }

    function callshop_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function customer_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Account Removed Completed!');
        redirect(base_url() . 'accounts/customer_list/');
    }

    function reseller_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Account Removed Completed!');
        redirect(base_url() . 'accounts/reseller_list/');
    }

    function provider_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Account Removed Completed!');
        redirect(base_url() . 'accounts/provider_list/');
    }

    function admin_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Account Removed Completed!');
        redirect(base_url() . 'accounts/admin_list/');
    }

    function subadmin_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Account Removed Completed!');
        redirect(base_url() . 'accounts/subadmin_list/');
    }

    function callshop_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Account Removed Completed!');
        redirect(base_url() . 'accounts/callshop_list/');
    }

    function customer_ipmap_json($accountid, $accounttype) {
        $json_data = array();
        $where = array("accountid" => $accountid);
        $count_all = $this->db_model->countQuery("*", "ip_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ip_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);

        $grid_fields = json_decode($this->accounts_form->build_ip_list_for_customer($accountid, $accounttype));
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_animap_json($accountid) {
        $json_data = array();
        $where = array("accountid" => $accountid);
        $count_all = $this->db_model->countQuery("*", "ani_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ani_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);

        $grid_fields = json_decode($this->accounts_form->build_animap_list_for_customer($accountid));
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_iax_sip_json($account_number = NULL) {

        $account_device_list = array();
        $account = $account_number;
        if ($account) {
            $data['account'] = $account;
            $data['account_number'] = $account_number;
            $this->load->model('common_model');

            $rt_db = 0;
            if ($rt_db) {

                $sip_names = $this->common_model->list_sip_account_rt($account_number);

                $iax_names = $this->common_model->list_iax_account_rt($account_number);

                foreach ($sip_names as $key => $value) {
                    $deviceinfo = $this->common_model->get_sip_account_rt($value['name']);
                    $row = array();
                    $row['tech'] = "SIP";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }

                foreach ($iax_names as $key => $value) {
                    $deviceinfo = $this->common_model->get_iax_account_rt($value['name']);
                    $row = array();
                    $row['tech'] = "IAX2";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }
            }

            $fs_db = 1;
            if ($fs_db) {
                $sip_devices = $this->common_model->fs_list_sip_usernames($account_number);
                if (count($sip_devices) > 0) {
                    foreach ($sip_devices as $key => $record) {

                        $deviceinfo = $this->switch_config_model->fs_retrieve_sip_user($record['id']);
                        $row = array();
                        $row['tech'] = "SIP";
                        $row['type'] = "user@" . $record['domain'];
                        $row['username'] = $record['username'];
                        $row['secret'] = $deviceinfo['password'];
                        $row['context'] = $deviceinfo['context'];
                        array_push($account_device_list, $row);
                    }
                }
            }

            $freepbx_db = 0;
            if ($freepbx_db) {
                $sip_names_freepbx = $this->common_model->list_sip_account_freepbx($account_number);
                $iax_names_freepbx = $this->common_model->list_iax_account_freepbx($account_number);

                foreach ($sip_names_freepbx as $key => $value) {
                    $deviceinfo = $this->common_model->get_sip_account_freepbx($value['name']);
                    $row = array();
                    $row['tech'] = "SIP";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }
                foreach ($iax_names_freepbx as $key => $value) {
                    $deviceinfo = $this->common_model->get_iax_account_freepbx($value['name']);
                    $row = array();
                    $row['tech'] = "IAX2";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }
            }
        }

        $count_all = count($account_device_list);
        $config['total_rows'] = $count_all;
        $config['per_page'] = $_GET['rp'];
        $page_no = $_GET['page'];

        $json_data['page'] = $page_no;
        $json_data['total'] = $config['total_rows'];

        $perpage = $config['per_page'];
        $start = ($page_no - 1) * $perpage;
        if ($start < 0)
            $start = 0;

        for ($i = $start; $i <= ($config['per_page'] + $start - 1); $i++) {
            if (isset($account_device_list[$i]['tech']) && $account_device_list[$i]['tech'] != "") {
                $json_data['rows'][] = array('cell' => array(
                        $account_device_list[$i]['tech'],
                        $account_device_list[$i]['type'],
                        $account_device_list[$i]['username'],
                        $account_device_list[$i]['secret'],
                        $account_device_list[$i]['context'],
                        ));
            }
        }

        echo json_encode($json_data);
    }

    function customer_details_json($module, $accountid) {
        if ($module == "pattern") {
            $this->load->module('rates/rates');
            $this->rates->customer_block_pattern_list($accountid);
        }
        if ($module == "freeswitch") {
            $this->load->module('freeswitch/freeswitch');
            $this->freeswitch->customer_fssipdevices_json($accountid);
        }
        if ($module == "did") {
            $this->load->module('did/did');
            $this->did->customer_did($accountid, "customer");
        }
        if ($module == "invoices") {
            $this->load->module('invoices/invoices');
            $this->invoices->customer_invoices($accountid);
        }
        if ($module == "package") {
            $this->load->module('package/package');
            $this->package->customer_charge_list($accountid, "customer");
        }
        if ($module == "reports") {
            $this->load->module('reports/reports');
            $this->reports->customer_cdrreport($accountid);
        }
        if ($module == "opensips") {
            $this->load->module('opensips/opensips');
            $this->opensips->customer_opensips_json($accountid);
        }
    }

    function customer_add_blockpatterns($accountid) {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Unblocked Prefixes List';
        $this->session->set_userdata('advance_search', 0);
        $this->load->module('rates/rates');
        $data['patters_grid_fields'] = $this->rates->rates_form->build_outbound_list_for_customer();
        $data["accountid"] = $accountid;
        $this->load->view('view_block_prefix_list', $data);
    }

    function customer_add_blockpatterns_json($accountid) {
        $this->load->module('rates/rates');
        $json_data = array();
        $count_all = $this->rates_model->getunblocked_pattern_list($accountid,false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates->rates_model->getunblocked_pattern_list($accountid,true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates->rates_form->build_outbound_list_for_customer());
        $json_data['rows'] = $this->rates->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function reseller_details_json($module, $accountid) {

        if ($module == "did") {
            $this->load->module('did/did');
            $this->did->customer_did($accountid, "reseller");
        }
        if ($module == "invoices") {
            $this->load->module('invoices/invoices');
            $this->invoices->customer_invoices($accountid);
        }
        if ($module == "package") {
            $this->load->module('package/package');
            $this->package->customer_charge_list($accountid, "reseller");
        }
    }

    function provider_details_json($module, $accountid) {
        if ($module == "invoices") {
            $this->load->module('invoices/invoices');
            $this->invoices->customer_invoices($accountid);
        }
    }

    function customer_block_prefix($accountid) {
        $result = $this->accounts_model->insert_block($this->input->post('prefixies', true), $accountid);
        echo $result;
        exit;
    }

    function customer_charges_action($action, $accountid, $accounttype, $chargeid = "") {
        if ($action == "add") {
            $charge_id = $this->input->post("applayable_charge", true);
            if ($charge_id != "") {
                $insert_arr = array("charge_id" => $charge_id,
                    "accountid" => $accountid, "status" => "1");
                $this->db->insert("charge_to_account", $insert_arr);
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
            } else {
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
            }
        }
        if ($action == "delete") {
            $this->db_model->delete("charge_to_account", array("id" => $chargeid));
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
        }
    }

    function customer_add_postcharges($accounttype, $accountid) {
        $charge = $this->input->post("amount", true);
        if ($charge != "") {
            $charge = $this->common_model->add_calculate_currency($charge, "", '', false, false);
            $date = date('Y-m-d H:i:s');
            $insert_arr = array("accountid" => $accountid, "description" => $this->input->post("desc", true),
                "created_date" => $date, "debit" => $charge,
                "charge_type" => "post_charge");
            $this->db->insert("invoice_item", $insert_arr);

            $this->accounts_model->update_balance($charge, $accountid, "debit");
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
        } else {
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
        }
    }

    function customer_dids_action($action, $accountid, $accounttype, $did_id = "") {
        if ($action == "add") {
            $did_id = $this->input->post("free_did_list", true);
            if ($did_id != "") {
                $did_query = $this->db_model->getSelect("*", "dids", array("id" => $did_id));
                $did_arr = $did_query->result_array();

                $account_query = $this->db_model->getSelect("*", "accounts", array("id" => $accountid));
                $account_arr = $account_query->result_array();
                $available_bal = $this->db_model->get_available_bal($account_arr[0]);
                if ($available_bal >= $did_arr[0]["setup"]) {
                    if ($did_arr[0]["allocation_bill_status"] == 1) {
                        $available_bal = $this->db_model->update_balance($did_arr[0]["setup"], $accountid, "debit");
                    }
                    $this->db_model->update("dids", array("accountid" => $accountid), array("id" => $did_id));
                    redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
                } else {
                    $this->session->set_flashdata('astpp_notification', 'Insuffiecient fund to purchase this did');
                    redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
                }
            } else {
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
            }
        }
        if ($action == "delete") {
            $this->db_model->update("dids", array("accountid" => "0"), array("id" => $did_id));
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
        }
    }

    function customer_ipmap_action($action, $accountid, $accounttype, $ipmapid = "") {
        if ($action == "add") {
            $ip = $this->input->post("ip", true);
            if ($ip != "") {
                if ($accounttype == "provider") {
                    $insert_arr = array("name" => $this->input->post("name", true), "ip" => $ip, "accountid" => $accountid,
                        "context" => "default", "pricelist_id" => "0");
                } else {
                    $insert_arr = array("name" => $this->input->post("name", true), "ip" => $ip, "accountid" => $accountid,
                        "prefix" => $this->input->post("prefix", true), "context" => "default",
                        "pricelist_id" => $this->input->post("ip_pricelist", true));
                }
                $ip_flag = $this->db->insert("ip_map", $insert_arr);
                if ($ip_flag) {
                    $this->load->library('freeswitch_lib');
                    $this->load->module('freeswitch/freeswitch');
                    $command = "api reloadacl";
                    $response = $this->freeswitch_model->reload_freeswitch($command);
                    $this->session->set_userdata('astpp_notification',$response);
                }
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#accounts");
            } else {
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#accounts");
            }
        }
        if ($action == "delete") {
            $ip_flag = $this->db_model->delete("ip_map", array("id" => $ipmapid));
            if ($ip_flag) {
                $this->load->library('freeswitch_lib');
                $this->load->model("freeswitch_model");
                $command = "api reloadacl";
                $this->freeswitch_model->reload_freeswitch($command);
            }
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#accounts");
        }
    }

    function customer_animap_action($action, $accountid, $aniid = "") {
        if ($action == "add") {
            $ani = $this->input->post("ANI", true);
            if ($ani != "") {
                $insert_arr = array("number" => $this->input->post("ANI", true), "accountid" => $accountid,
                     "context" => "default");
                $this->db->insert("ani_map", $insert_arr);
                redirect(base_url() . "accounts/customer_edit/$accountid#accounts");
            } else {
                redirect(base_url() . "accounts/customer_edit/$accountid#accounts");
            }
        }
        if ($action == "delete") {
            $this->db_model->delete("ani_map", array("id" => $aniid));
            redirect(base_url() . "accounts/customer_edit/$accountid#accounts");
        }
    }

    function customer_delete_block_pattern($accountid, $patternid) {
        $this->db_model->delete("block_patterns", array("id" => $patternid));
        redirect(base_url() . "accounts/customer_edit/$accountid#block_prefixes");
    }

    function callshop_selected_delete() {
        echo $this->delete_multiple();
    }

    function reseller_selected_delete() {
        echo $this->delete_multiple();
    }

    function provider_selected_delete() {
        echo $this->delete_multiple();
    }

    function subadmin_selected_delete() {
        echo $this->delete_multiple();
    }

    function admin_selected_delete() {
        echo $this->delete_multiple();
    }

    function customer_selected_delete() {
        echo $this->delete_multiple();
    }

    function delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("accounts");
    }

    function user_animap_json($accountid) {
        $json_data = array();
        $where = array("accountid" => $accountid);
        $count_all = $this->db_model->countQuery("*", "ani_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ani_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        $grid_fields = json_decode($this->accounts_form->build_animap_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_account_taxes($action = false, $id = false) {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Account Tax List';

        if ($action == false)
            $action = "list";

        if ($action == 'list') {
            $this->load->view('view_account_taxes_list', $data);
        } elseif ($action == 'add') {

            if (($this->input->post())) {
                $post_array = $this->input->post();
                $query = $this->accounts_model->remove_all_account_tax($post_array['account_id']);

                foreach ($post_array as $key => $value) {
                    $id = explode("_", $key);
                    if ($id[0] == 'tax') {
                        $data = array(
                            'accountid' => $post_array['account_id'],
                            'taxes_id' => $post_array[$key],
                        );
                        $this->accounts_model->add_account_tax($data);
                    }
                }
                $this->session->set_flashdata('astpp_notification', 'Account Tax added successfully!');
                redirect(base_url() . 'accounts/customer_list/');
            }
            $data['id'] = array();
            $data['taxesList'] = $this->common_model->get_list_taxes();
            $this->load->view('view_accounting_taxes_add', $data);
        } elseif ($action == 'edit') {
            $taxes_id = $this->accounts_model->get_accounttax_by_id($id);
            $account_num = $this->accounts_model->get_account_number($id);
            $data['accountnum'] = $account_num['number'];
            $data['account_id'] = $id;
            for ($i = 0; $i < count($taxes_id); $i++) {
                $tax_ids[] = $taxes_id[$i]['taxes_id'];
            }
            $data['tax_ids'] = $tax_ids;

            $data['tax_id'] = $taxes_id;

            if (($this->input->post())) {
                $post_array = $this->input->post();
                $accountinfo = $this->accounts_model->get_account_by_number($post_array['account_id']);
                $query = $this->accounts_model->remove_all_account_tax($post_array['account_id']);
                foreach ($post_array as $key => $value) {
                    $id = explode("_", $key);
                    if ($id[0] == 'tax') {
                        $data = array(
                            'accountid' => $post_array['account_id'],
                            'taxes_id' => $post_array[$key],
                        );
                        $this->accounts_model->add_account_tax($data);
                    }
                }
                if ($accountinfo['type'] == '0') {
                    $link = base_url() . '/accounts/customer_list/';
                } else {
                    $link = base_url() . '/accounts/reseller_list/';
                }
                $this->session->set_flashdata('astpp_notification', 'Account Tax added successfully!');
                redirect($link);
            }
            $data['taxesList'] = $this->common_model->get_list_taxes();
            $this->load->view('view_accounting_taxes_add', $data);
        } elseif ($action == 'delete') {
            $this->accounting_model->remove_account_tax($id);
            $this->session->set_flashdata('astpp_notification', 'Account Tax removed successfully!');
            redirect(base_url() . 'accounting/account_taxes/');
        }
    }

    /**
     * -------Here we write code for controller accounting functions vallid_account_tax------
     * here this function called by ajax form and vallidate the account number
     * @$_POST['username']: Account Number
     */
    function valid_account_tax() {
        $tax_id = '';
        if (!empty($_POST['username'])) {

            $account_num = mysql_real_escape_string($_POST['username']);
            $row = $this->accounts_model->check_account_num($account_num);
            if (isset($row['accountid']) && $row['accountid'] != '') {
                $taxes_id = $this->accounts_model->get_accounttax_by_id($row['accountid']);
                if ($taxes_id) {
                    foreach ($taxes_id as $id) {
                        $tax_id.=$id['taxes_id'] . ",";
                    }

                    $tax_id = rtrim($tax_id, ",");
                    echo $row['accountid'] . ',' . $tax_id;
                } else {
                    echo $row['accountid'];
                }
            }
        }
    }

    function customer_payment_process_add($id = '') {
        $account = $this->accounts_model->get_account_by_number($id);
        $currency = $this->accounts_model->get_currency_by_id($account['currency_id']);
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Process Payment';
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_payment_fields($currency['currency'], $account['number'], $currency['currency'], $id), '');
        $this->load->view('view_accounts_process_payment', $data);
    }

    function customer_payment_save($id = '') {
        if ($this->input->post()) {
            $post_array = $this->input->post();
            $post_array['credit'] = $this->common_model->add_calculate_currency($post_array['credit'], "", '', false, false);
            $logintype = $this->session->userdata('logintype');
            $username = $this->session->userdata('username');
	    $login_user_data = $this->session->userdata("accountinfo");
            $accountinfo = $this->accounts_model->get_account_by_number($post_array['id']);

            if ($accountinfo['type'] == '0') {
                $link = base_url() . '/accounts/customer_list/';
            } else {
                $link = base_url() . '/accounts/reseller_list/';
            }

            if ($logintype == 1 || $logintype == 5) {
                if ($accountinfo['reseller_id'] == $login_user_data["id"]) {
                    $response = $this->accounts_model->account_process_payment($post_array);
                    $this->common_model->status_message($response);
                    $this->session->set_flashdata('astpp_notification', "Account refilled successfully....");
                    redirect(base_url() . '/accounts/reseller_list/');
                    exit;
                } else {
                    $this->session->set_flashdata('astpp_errormsg', "You are not allowed to add amount to this account.");
                    redirect($link);
                    exit;
                }
            } else {
                $response = $this->accounts_model->account_process_payment($post_array);
                $this->session->set_flashdata('astpp_notification', "Account refilled successfully....");
                redirect($link);
                exit;
            }
        }
        $account = $this->accounts_model->get_account_by_number($post_array['id']);
        $currency = $this->accounts_model->get_currency_by_id($account['currency_id']);
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Process Payment';
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_payment_fields($currency['currency'], $account['number'], $currency['currency'], $id), '');
        $this->load->view('view_accounts_process_payment', $data);
    }

    function customer_fssipdevices_action($action, $id, $accountid) {
        $this->load->module('freeswitch/freeswitch');
        if ($action == "delete") {
            $this->freeswitch->freeswitch_model->delete_freeswith_devices($id);
            redirect(base_url() . "accounts/customer_edit/$accountid#accounts");
        }
        if ($action == "edit") {
            $this->freeswitch->customer_fssipdevices_edit($id, $accountid);
        }
    }

    function customer_opensips_action($action, $id, $accountid) {
        $this->load->module('opensips/opensips');
        if ($action == "delete") {
            $this->opensips->opensips_model->remove_opensips($accountid);
            redirect(base_url() . "accounts/customer_edit/$id#accounts");
        }
        if ($action == "edit") {
	    
            $this->opensips->customer_opensips_edit($id, $accountid);
        }
    }

    function reseller_edit_account() {
        $account_data = $this->session->userdata("accountinfo");

        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->accounts_form->get_reseller_own_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Reseller Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $this->accounts_model->edit_account($add_array, $add_array['id']);
                $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
                redirect(base_url() . '/dashboard/');
            }
            $this->load->view('view_reseller_edit_details_own', $data);
        } else {

            $data['page_title'] = 'Edit Reseller Details';
            $where = array('id' => $account_data["id"]);
            $account = $this->db_model->getSelect("*", "accounts", $where);
            $data["account_data"] = $account->result_array();

            foreach ($account->result_array() as $key => $value) {
                $editable_data = $value;
            }
            $data['form'] = $this->form->build_form($this->accounts_form->get_reseller_own_form_fields(), $editable_data);
            $this->load->view('view_reseller_edit_details_own', $data);
        }
    }

}

?>
 
