<?php

class Charges extends CI_Controller {

    function Charges() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("charges_form");
        $this->load->library('astpp/form');
        $this->load->model('charges_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function periodiccharges_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'add Charges';
        $data['form'] = $this->form->build_form($this->charges_form->get_charegs_form_fields(), '');

        $this->load->view('view_periodiccharges_add_edit', $data);
    }

    function periodiccharges_edit($edit_id = '') {
        $data['page_title'] = 'Edit Charges ';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "charges", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $edit_data['charge'] = $this->common_model->to_calculate_currency($edit_data['charge'], '', '', true, false);
        
        $data['form'] = $this->form->build_form($this->charges_form->get_charegs_form_fields(), $edit_data);
        $this->load->view('view_periodiccharges_add_edit', $data);
    }

    function periodiccharges_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->charges_form->get_charegs_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'add Charges';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                if ($add_array['pricelist_id'] == '') {
                    $add_array['pricelist_id'] = '0';
                }
                $add_array['charge'] = $this->common_model->add_calculate_currency($add_array['charge'], '', '', false, false);
                $this->charges_model->edit_charge($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["description"]." Charges updates Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['charge'] = $this->common_model->add_calculate_currency($add_array['charge'], '', '', false, false);
                $this->charges_model->add_charge($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["description"]." Charges added Successfully."));
                exit;
            }
        }
    }

    function periodiccharges_delete($id) {
        $this->charges_model->remove_charge($id);
        $this->session->set_flashdata('astpp_notification', 'Tax removed successfully!');
        redirect(base_url() . 'charges/periodiccharges/');
    }

    function periodiccharges_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('charges_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/admin_list/');
        }
    }

    function periodiccharges_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('charges_list_search', "");
    }

    function periodiccharges() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Periodic Charges List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->charges_form->build_charge_list_for_admin();
        $data["grid_buttons"] = $this->charges_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->charges_form->get_charges_search_form());

        $this->load->view('view_charges_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function periodiccharges_json() {
        $json_data = array();
        $count_all = $this->charges_model->getcharges_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->charges_model->getcharges_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->charges_form->build_charge_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function periodiccharges_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("charges");
    }

}

?>
 