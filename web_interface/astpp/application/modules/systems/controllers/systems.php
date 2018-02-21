<?php

class Systems extends CI_Controller {

    function Systems() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("system_form");
        $this->load->library('astpp/form');
        $this->load->model('system_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function configuration_edit($edit_id = '') {
        $data['page_title'] = 'Edit System Configurations';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "system", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->system_form->get_configuration_form_fields(), $edit_data);
        $this->load->view('view_configuration_add_edit', $data);
    }

    function configuration_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('configuration_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'systems/configuration/');
        }
    }

    function configuration_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('did_search', "");
    }

    function configuration_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->system_form->get_configuration_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit System Configurations';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->system_model->edit_configuration($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> " Configuration updates Successfully."));
                exit;
            }
        }
    }

    function configuration() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Configuration List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->system_form->build_system_list_for_admin();
        $data["grid_buttons"] = $this->system_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->system_form->get_configuration_search_form());
        $this->load->view('view_configuration_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function configuration_json() {

        $json_data = array();
        $count_all = $this->system_model->getsystem_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->system_model->getsystem_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->system_form->build_system_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function template() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Email Template List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->system_form->build_template_list_for_admin();
        $data["grid_buttons"] = $this->system_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->system_form->get_template_search_form());
        $this->load->view('view_template_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function template_json() {
        $json_data = array();
        $count_all = $this->system_model->gettemplate_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->system_model->gettemplate_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->system_form->build_template_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function template_edit($edit_id = '') {
        $data['page_title'] = 'Edit Email template';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "default_templates", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->system_form->get_template_form_fields(), $edit_data);
        $this->load->view('view_template_add_edit', $data);
    }

    function template_save() {
        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->system_form->get_template_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit template';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $this->system_model->edit_template($add_array, $add_array['id']);
                $this->session->set_flashdata('astpp_notification', 'Template updated successfully!');
                redirect(base_url() . 'systems/template/');
                exit;
            }
        } else {
            $data['page_title'] = 'Termination Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $this->system_model->add_template($add_array);
                $this->session->set_flashdata('astpp_notification', 'Template added successfully!');
                redirect(base_url() . 'systems/template/');
                exit;
            }
        }
        $this->load->view('view_trunk_add_edit', $data);
    }

    function template_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('template_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'systems/template/');
        }
    }

    function template_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('did_search', "");
    }

}

?>
 