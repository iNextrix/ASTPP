<?php

class Package extends MX_Controller {

    function Package() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library('package_form');
        $this->load->library('astpp/form');
        $this->load->model('package_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function package_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'Create Package';
        $data['page_title'] = 'Create Package';
        $data['form'] = $this->form->build_form($this->package_form->get_package_form_fields(), '');

        $this->load->view('view_package_add', $data);
    }

    function package_edit($edit_id = '') {
        $data['page_title'] = 'Packages';
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array('id' => $edit_id, "reseller_id" => $account_data['id'], "status" => "1");
        } else {
            $where = array('id' => $edit_id, "status" => "1");
        }
        $account = $this->db_model->getSelect("*", " packages", $where);
        if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $data['form'] = $this->form->build_form($this->package_form->get_package_form_fields(), $edit_data);
            
            $data['pattern_grid_fields'] = $this->package_form->build_pattern_list_for_customer($edit_id);
            $data['pattern_grid_buttons'] = $this->package_form->set_pattern_grid_buttons($edit_id);
            $data["package_id"] = $edit_id;
            $this->load->view('view_packages_edit', $data);
        } else {
            redirect(base_url() . 'package/package_list/');
        }
    }

    function package_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->package_form->get_package_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Package Details';
            if ($this->form_validation->run() == FALSE) {

                $data['form'] = $this->form->build_form($this->package_form->get_package_form_fields(), $add_array);
                $data['pattern_grid_fields'] = $this->package_form->build_pattern_list_for_customer($add_array['id']);
                $data['pattern_grid_buttons'] = $this->package_form->set_pattern_grid_buttons($add_array['id']);
                $data["package_id"] = $add_array['id'];
                
                
                $data['validation_errors'] = validation_errors();
                $this->load->view('view_packages_edit', $data);        
            } else {
                $this->package_model->edit_package($add_array, $add_array['id']);
                $this->session->set_flashdata('astpp_notification', 'Packages Updated successfully!');

                redirect(base_url() . 'package/package_list/');
                exit;
            }
        } else {
            $data['page_title'] = 'Create Package Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                $this->load->view('view_package_add', $data);
            } else {
                $this->package_model->add_package($add_array);
                $this->session->set_flashdata('astpp_notification', 'Packages added successfully!');
                redirect(base_url() . 'package/package_list/');
                exit;
            }
        }
    }

    function package_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('package_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'package/package_list/');
        }
    }

    function package_delete($id) {
        $this->package_model->remove_package($id);
        $this->session->set_flashdata('astpp_notification', 'package Removed Completed!');
        redirect(base_url() . 'package/package_list/');
    }

    function package_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function package_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Package List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->package_form->build_package_list_for_admin();
        $data["grid_buttons"] = $this->package_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->package_form->get_package_search_form());
        $this->load->view('view_package_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function package_list_json() {
        $json_data = array();
        $count_all = $this->package_model->getpackage_list(false);
        $paging_data = $this->form->load_grid_config($count_all, 10, 1);
        $json_data = $paging_data["json_paging"];

        $query = $this->package_model->getpackage_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->package_form->build_package_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_charge_list($accountid, $accounttype) {
        $json_data = array();

        $select = "charge_to_account.id,charges.description,charges.charge,charges.sweep_id";
        $table = "charges";
        $jionTable = array('charge_to_account', 'accounts');
        $jionCondition = array('charges.id = charge_to_account.charge_id', 'accounts.id = charge_to_account.accountid');
        $type = array('left', 'inner');
        $where = array('accounts.id' => $accountid);
        $order_type = 'charges.id';
        $order_by = "ASC";

        $count_all = $this->db_model->getCountWithJion($table, $select, $where, $jionTable, $jionCondition, $type);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $account_charge_list = $this->db_model->getAllJionQuery($table, $select, $where, $jionTable, $jionCondition, $type, $paging_data["paging"]["page_no"], $paging_data["paging"]["start"], $order_by, $order_type, "");
        $grid_fields = json_decode($this->package_form->build_charges_list_for_customer($accountid, $accounttype));
        $json_data['rows'] = $this->form->build_grid($account_charge_list, $grid_fields);

        echo json_encode($json_data);
    }

    function package_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("packages");
    }

    function package_counter() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Package Usage Report';
        $data['grid_fields'] = $this->package_form->build_package_counter_list_for_admin();
        $this->load->view('view_package_counter_report', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function package_counter_json() {
        $json_data = array();
        $count_all = $this->package_model->getpackage_counter_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->package_model->getpackage_counter_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->package_form->build_package_counter_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }
    function package_pattern_json($package_id){
        $json_data = array();
        $where = array('package_id' => $package_id);

        $count_all = $this->db_model->countQuery("*", "package_patterns", $where);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $pattern_data = $this->db_model->getSelect("*", "package_patterns", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        $grid_fields = json_decode($this->package_form->build_pattern_list_for_customer($package_id));
        $json_data['rows'] = $this->form->build_grid($pattern_data, $grid_fields);

        echo json_encode($json_data);
    }
    function customer_add_patterns($packageid) {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Unblocked Prefixes List';
        $this->session->set_userdata('advance_search', 0);
        $this->load->module('rates/rates');
        $data['patters_grid_fields'] = $this->rates->rates_form->build_outbound_list_for_customer();
        $data["packageid"] = $packageid;
        $this->load->view('view_prefix_list', $data);
    }
    function customer_add_patterns_json($accountid) {
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
    function customer_package_prefix($packageid) {
        $result = $this->package_model->insert_package_pattern($this->input->post('prefixies', true),$packageid);
        echo $result;
        exit;
    }
    function customer_delete_package_pattern($packageid, $patternid) {
        $this->db_model->delete("package_patterns", array("id" => $patternid));
        redirect(base_url() . "package/package_edit/$packageid#package_patterns");
    }    
}

?>
 