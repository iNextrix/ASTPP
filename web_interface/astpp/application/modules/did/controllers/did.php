<?php

class DID extends MX_Controller {

    function DID() {
        parent::__construct();

        $this->load->helper('template_inheritance');
        $this->load->library('session');
        $this->load->library('did_form');
        $this->load->library('astpp/form');
        $this->load->model('did_model');
        $this->load->library('csvreader');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function did_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add Dids';
        $data['form'] = $this->form->build_form($this->did_form->get_dids_form_fields(), '');

        $this->load->view('view_did_add_edit', $data);
    }

    function did_edit($edit_id = '') {
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

        $data['form'] = $this->form->build_form($this->did_form->get_dids_form_fields(), $edit_data);
        $this->load->view('view_did_add_edit', $data);
    }
    function did_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->did_form->get_dids_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Account Details';
            $data['page_title'] = 'Edit Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['setup'] = $this->common_model->add_calculate_currency($add_array['setup'], '', '', false, false);
                $add_array['disconnectionfee'] = $this->common_model->add_calculate_currency($add_array['disconnectionfee'], '', '', false, false);
                $add_array['monthlycost'] = $this->common_model->add_calculate_currency($add_array['monthlycost'], '', '', false, false);
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->did_model->edit_did($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["number"]." DID updated Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['setup'] = $this->common_model->add_calculate_currency($add_array['setup'], '', '', false, false);
                $add_array['disconnectionfee'] = $this->common_model->add_calculate_currency($add_array['disconnectionfee'], '', '', false, false);
                $add_array['monthlycost'] = $this->common_model->add_calculate_currency($add_array['monthlycost'], '', '', false, false);
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $response = $this->did_model->add_did($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["number"]." DID added Successfully."));
                exit;
                exit;
            }
        }
    }

    function did_remove($id) {
        $this->did_model->remove_did($id);
        $this->session->set_flashdata('astpp_notification', 'Did removed successfully!');
        redirect(base_url() . 'did/did_list/');
    }

    function did_list() {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Manage DIDs | DIDS';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'DIDs List';
        $data['cur_menu_no'] = 4;
        $this->session->set_userdata('did_search', 0);
        $data['grid_fields'] = $this->did_form->build_did_list_for_admin();
        if ($this->session->userdata('logintype') == 2) {
            $data["grid_buttons"] = $this->did_form->build_grid_buttons();
        } else {
            $data["grid_buttons"] = json_encode(array());
        }

        $data['form_search'] = $this->form->build_serach_form($this->did_form->get_search_did_form());
        $this->load->view('view_did_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function did_list_json() {
        $json_data = array();

        $count_all = $this->did_model->getdid_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->did_model->getdid_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->did_form->build_did_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function did_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('did_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'did/did_list/');
        }
    }

    function did_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('did_search', "");
    }

    /* -------Here we write code for controller did functions did_import------
     * @Purpose this function check if account number exist or not then remove from database.
     * @params $account_number: Account Number
     * @return Return Appropreate message If Account Delete or not.
     */

    function did_import() {
        $data['page_title'] = 'Import DID';
        $this->load->view('view_did_import', $data);
    }

    function did_bulk_import() {
        $csv_tmp_data = $this->csvreader->parse_file($_FILES["didimport"]["tmp_name"]);
        $new_did_arr = array();
        $ori_array = array();
        $origination_rate_array = array('pattern' => '', 'comment' => '', 'pricelist_id' => '', 'inc' => '', 'includedseconds' => '', 'cost' => '',
            'connectcost' => '',
        );
        if (isset($_FILES['didimport']['name'])) {
            $error = $_FILES['didimport']['error'];
            if ($error == 0) {
                $uploadedFile = $_FILES["didimport"]["tmp_name"];

                if ($_FILES["didimport"]["type"] == "text/csv") {
                    if (is_uploaded_file($uploadedFile)) {
                        $csv_tmp_data = $this->csvreader->parse_file($uploadedFile);

                        foreach ($csv_tmp_data as $key => $csv_data) {
                            $flag = '1';
                            foreach ($csv_data as $field_key => $field_value) {
                                if ($key != 0) {
                                    if ($field_key == 'number' && !is_numeric($field_value)) {
                                        $flag = '0';
                                    }
                                    if ($flag == '1') {
                                        if ($field_key == 'country') {
                                            $new_did_arr[$key]['country_id'] = $this->did_model->get_coutry_id_by_name($field_value);
                                        } else if ($field_key == 'account') {
                                            $new_did_arr[$key]['accountid'] = $field_value;
                                        } else if ($field_key == 'increment') {
                                            $new_did_arr[$key]['inc'] = $field_value;
                                        } else if ($field_key == 'provider') {
                                            $new_did_arr[$key]['provider_id'] = $field_value;
                                        } else {
                                            $new_did_arr[$key][$field_key] = $field_value;
                                        }
                                        $ori_array[$key] = $this->did_model->array_outboundroutes($csv_data, '1');
                                    }
                                }
                            }
                        }
                        if (empty($new_did_arr) || empty($ori_array)) {
                            echo 'Plase Enter valid did to import!';
                        }
                        $result = $this->did_model->bulk_insert_dids($new_did_arr);

                        $this->load->module('rates/rates');
                        $this->rates->rates_model->bulk_insert_inboundrates($ori_array); //means origination rates inserted
                        echo $result . ' did Imported successfully!';
                    }
                } else {
                    echo 'Please upload on Only csv file...........!';
                }
            } else {
                echo 'Please upload on Only csv file...........!';
            }
        } else {
            echo 'Please upload on Only csv file...........!';
        }
    }

    function customer_did($accountid, $accounttype) {
        $json_data = array();
        $where = array('accountid' => $accountid);
        $count_all = $this->db_model->countQuery("*", "dids", $where);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->getSelect("*", "dids", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        $did_grid_fields = json_decode($this->did_form->build_did_list_for_customer($accountid, $accounttype));
        $json_data['rows'] = $this->form->build_grid($query, $did_grid_fields);

        echo json_encode($json_data);
    }

    function user_did($accountid) {
        $json_data = array();
        $where = array('accountid' => $accountid);
        $count_all = $this->db_model->countQuery("*", "dids", $where);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->getSelect("*", "dids", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        $did_grid_fields = json_decode($this->did_form->build_did_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $did_grid_fields);

        echo json_encode($json_data);
    }

    function did_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("dids");
    }

    /**
     * -------Here we write code for controller did functions manage------
     * @action: Add, Edit, Delete, List DID
     * @id: DID number
     */
    function did_reseller_edit($action = false, $id = false) {
        if ($action == 'edit') {
            if (($this->input->post())) {
                $post = $this->input->post();
                $post['setup'] = $this->common_model->add_calculate_currency($post['setup'], '', '', false, false);
                $post['disconnectionfee'] = $this->common_model->add_calculate_currency($post['disconnectionfee'], '', '', false, false);
                $post['monthlycost'] = $this->common_model->add_calculate_currency($post['monthlycost'], '', '', false, false);
                $post['connectcost'] = $this->common_model->add_calculate_currency($post['connectcost'], '', '', false, false);
                $post['cost'] = $this->common_model->add_calculate_currency($post['cost'], '', '', false, false);
                $response = $this->did_model->edit_did_reseller($post);
                $this->session->set_flashdata('astpp_notification', 'DID updated successfully!');
                redirect(base_url() . 'did/did_list/');
            } else {
                if ($this->session->userdata('logintype') == 1) {
                    $accountinfo = $this->did_model->get_account($this->session->userdata('username'));
                    $reseller_didinfo = $this->did_model->get_did_reseller_new($id, $this->session->userdata['accountinfo']['id']);
                    if ($accountinfo['reseller_id'] != "0") {
                        $didinfo = $this->did_model->get_did_reseller_new($id, $accountinfo['reseller_id']);
                    } else {

                        $didinfo = $this->did_model->get_did_by_number($id);
                    }
                    $didinfo['setup'] = $this->common_model->to_calculate_currency($didinfo['setup'], '', '', true, false);
                    $didinfo['disconnectionfee'] = $this->common_model->to_calculate_currency($didinfo['disconnectionfee'], '', '', true, false);
                    $didinfo['monthlycost'] = $this->common_model->to_calculate_currency($didinfo['monthlycost'], '', '', true, false);
                    $didinfo['connectcost'] = $this->common_model->to_calculate_currency($didinfo['connectcost'], '', '', true, false);
                    $didinfo['cost'] = $this->common_model->to_calculate_currency($didinfo['cost'], '', '', true, false);

                    $didinfo['country'] = $this->common->get_field_name("country", "countrycode", $didinfo['country_id']);
                    $didinfo['provider'] = $this->common->get_field_name("number", "accounts", $didinfo['provider_id']);

                    $data['did'] = $didinfo['number'];

                    $reseller_didinfo['setup'] = $this->common_model->to_calculate_currency($reseller_didinfo['setup'], '', '', true, false);
                    $reseller_didinfo['disconnectionfee'] = $this->common_model->to_calculate_currency($reseller_didinfo['disconnectionfee'], '', '', true, false);
                    $reseller_didinfo['monthlycost'] = $this->common_model->to_calculate_currency($reseller_didinfo['monthlycost'], '', '', true, false);
                    $reseller_didinfo['connectcost'] = $this->common_model->to_calculate_currency($reseller_didinfo['connectcost'], '', '', true, false);
                    $reseller_didinfo['cost'] = $this->common_model->to_calculate_currency($reseller_didinfo['cost'], '', '', true, false);
                    
                    $data['reseller_didinfo'] = $reseller_didinfo;

                    $data['didinfo'] = $didinfo;
                    $data['accountinfo'] = $accountinfo;
                    $this->load->view('view_did_manage_reseller_add', $data);
                }
            }
        }
        if ($action == 'delete') {
            if ($did = $this->did_model->get_did_by_number($id)) {
                $response = $this->did_model->remove_did_pricing($did, $this->session->userdata['accountinfo']['id']);
                $this->session->set_flashdata('astpp_notification', 'DID deleted successfully!');

                redirect(base_url() . 'did/did_list/');
            } else {
                $this->session->set_flashdata('astpp_errormsg', "Invalid card number.");
                redirect(base_url() . 'did/did_list/');
            }
        }
    }

}

?>
 