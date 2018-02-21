<?php

class Trunk extends CI_Controller {

    function Trunk() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("trunk_form");
        $this->load->library('astpp/form');
        $this->load->model('trunk_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function trunk_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Trunk List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->trunk_form->build_trunk_list_for_admin();
        $data["grid_buttons"] = $this->trunk_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->trunk_form->get_trunk_search_form());
        $this->load->view('view_trunk_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function trunk_list_json() {
        $json_data = array();
        $count_all = $this->trunk_model->gettrunk_list(false);
        $paging_data = $this->form->load_grid_config($count_all,$_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->trunk_model->gettrunk_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->trunk_form->build_trunk_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function trunk_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Trunk add';
        $data['form'] = $this->form->build_form($this->trunk_form->get_trunk_form_fields(), '');

        $this->load->view('view_trunk_add_edit', $data);
    }

    function trunk_edit($edit_id = '') {
        $data['page_title'] = 'Edit Trunk';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "trunks", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $edit_data["reseller_id"] = explode(",", $edit_data["reseller_id"]);
        $data['form'] = $this->form->build_form($this->trunk_form->get_trunk_form_fields(), $edit_data);
        $this->load->view('view_trunk_add_edit', $data);
    }

    function trunk_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->trunk_form->get_trunk_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Trunk Rates';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->trunk_model->edit_trunk($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["name"]." Trunk updated Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Termination Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->trunk_model->add_trunk($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["name"]." Trunk added Successfully."));
                exit;
            }
        }
    }

    function trunk_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('trunk_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'trunk/trunk_list/');
        }
    }

    function trunk_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function trunk_remove($id) {
        $this->trunk_model->remove_trunk($id);
        $this->session->set_flashdata('astpp_notification', 'Trunks removed successfully!');
        redirect(base_url() . 'trunk/trunk_list/');
    }

    function trunk_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        echo $this->db_model->update("trunks", array("status" => "2"), $where);
    }

}

?>
 
