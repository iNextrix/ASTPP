<?php

class pricing extends CI_Controller {

    function pricing() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("pricing_form");
        $this->load->library('astpp/form');
        $this->load->model('pricing_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function price_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'Create Rate Group';
        $data['page_title'] = 'Create Rate Group';
        $data['form'] = $this->form->build_form($this->pricing_form->get_pricing_form_fields(), '');

        $this->load->view('view_price_add_edit', $data);
    }

    function price_edit($edit_id = '') {
        $data['page_title'] = 'Edit Rate Group';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", " pricelists", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->pricing_form->get_pricing_form_fields(), $edit_data);
        $this->load->view('view_price_add_edit', $data);
    }

    function price_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->pricing_form->get_pricing_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Price Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->pricing_model->edit_price($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["name"]." Pricelists updates Successfully."));
                exit;
            }
            $this->load->view('view_price_add_edit', $data);
        } else {
            $data['page_title'] = 'Create Price Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->pricing_model->add_price($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["name"]." Pricelists added Successfully."));
                exit;
            }
        }
    }

    function price_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('price_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/customer_list/');
        }
    }

    function price_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function price_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Rate Group';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->pricing_form->build_pricing_list_for_admin();
        $data["grid_buttons"] = $this->pricing_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->pricing_form->get_pricing_search_form());
        $this->load->view('view_price_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function price_list_json() {
        $json_data = array();
        $count_all = $this->pricing_model->getpricing_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->pricing_model->getpricing_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->pricing_form->build_pricing_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function price_delete($pricelist_id) {
        $where = array("id" => $pricelist_id);
        $this->db_model->update("pricelists", array("status" => "2"), $where);
        $this->session->set_flashdata('astpp_notification', 'Pricelist Deleted successfully!');
        redirect(base_url() . 'pricing/price_list/');
    }

    function price_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        echo $this->db_model->update("pricelists", array("status" => "2"), $where);
    }

}

?>
 