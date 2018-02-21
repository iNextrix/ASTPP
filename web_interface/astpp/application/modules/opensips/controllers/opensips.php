<?php

class Opensips extends MX_Controller {

    function Opensips() {
        parent::__construct();

        $this->load->helper('template_inheritance');
        $this->load->library('session');
        $this->load->library("opensips_form");
        $this->load->library('astpp/form');
        $this->load->model('opensips_model');
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";

        $this->opensips_db = $this->load->database($opensipdsn, true);
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function opensips_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Opensips';
        $data['form'] = $this->form->build_form($this->opensips_form->get_opensips_form_fields(), '');

        $this->load->view('view_opensips_add_edit', $data);
    }
     
    function opensips_edit($edit_id = '') {
        $data['page_title'] = 'Edit opensisp devices ';
        $this->opensips_db->where('id', $edit_id);
        $account = $this->opensips_db->get("subscriber");
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }

        $data['form'] = $this->form->build_form($this->opensips_form->get_opensips_form_fields(), $edit_data);
        $this->load->view('view_opensips_add_edit', $data);
    }

    function customer_opensips_edit($accountid, $edit_id) {
        $data['page_title'] = 'Edit opensisp devices';
        $where = array('id' => $edit_id);
        $this->opensips_db->where($where);
        $account = $this->opensips_db->get("subscriber");
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->opensips_form->get_opensips_form_fields_for_customer($accountid), $edit_data);
        $this->load->view('view_opensips_add_edit', $data);
    }
     function customer_opensips_add($accountid='') {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Opensips';
        $data['form'] = $this->form->build_form($this->opensips_form->get_opensips_form_fields_for_customer($accountid), '');

        $this->load->view('view_opensips_add_edit', $data);
    }
    function opensips_save() {
        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->opensips_form->get_opensips_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit opensisp devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->opensips_model->edit_opensipsdevices($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> " OpenSIPs updated Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Add Opensips';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {

                $this->opensips_model->add_opensipsdevices($add_array);
                echo json_encode(array("SUCCESS"=> "OpenSIPs added Successfully."));
                exit;
            }
        }
    }

    function customer_opensips_save($user_flg = false) {
        $array_add = $this->input->post();
//         print_r($array_add);exit;
        $data['form'] = $this->form->build_form($this->opensips_form->get_opensips_form_fields_for_customer($array_add["accountcode"]), $array_add);
        if ($array_add['id'] != '') {
            $data['page_title'] = 'Edit Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->opensips_model->edit_opensipsdevices($array_add, $array_add['id']);
                echo json_encode(array("SUCCESS"=> "OpenSIPs edited Successfully."));
                exit;
            }
        }else{
	      if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            }else{
		$this->opensips_model->add_opensipsdevices($array_add);
                echo json_encode(array("SUCCESS"=> "OpenSIPs added Successfully."));
                exit;
	    }
        }
    }

    function customer_opensips_json($accountid) {
	
        $json_data = array();
        $count_all = $this->opensips_model->getopensipsdevice_customer_list(false, $accountid);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp']=10, $_GET['page']=1);
        $json_data = $paging_data["json_paging"];

        $query = $this->opensips_model->getopensipsdevice_customer_list(true, $accountid, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->opensips_form->opensips_customer_build_opensips_list($accountid));
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function opensips_add_customer($add_data) {
        $this->opensips_model->add_opensipsdevices($add_array);
    }

    function opensips_remove($id) {
        $this->opensips_model->remove_opensips($id);
        $this->session->set_flashdata('astpp_notification', 'OpenSips removed successfully!');
        redirect(base_url() . 'opensips/opensips_list/');
    }

    function opensips_list() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Opensips Devices List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->opensips_form->build_opensips_list();
        $data["grid_buttons"] = $this->opensips_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->opensips_form->get_search_opensips_form());
        $this->load->view('view_opensips_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function opensips_list_json() {
        $json_data = array();
        $count_all = $this->opensips_model->getopensipsdevice_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp']=10, $_GET['page']=1);
        $json_data = $paging_data["json_paging"];

        $query = $this->opensips_model->getopensipsdevice_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->opensips_form->build_opensips_list());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function opensips_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('opensipsdevice_list_search', $this->input->post());
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'opensips/opensips_list/');
        }
    }

    function opensips_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

//    dispather List add edit delete
    function dispatcher_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Dispatcher';
        $data['form'] = $this->form->build_form($this->opensips_form->get_dispatcher_form_fields(), '');

        $this->load->view('view_dispatcher_add_edit', $data);
    }

    function dispatcher_edit($edit_id = '') {
        $data['page_title'] = 'Dispatcher ';
        $this->opensips_db->where('id', $edit_id);
        $account = $this->opensips_db->get("dispatcher");
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }

        $data['form'] = $this->form->build_form($this->opensips_form->get_dispatcher_form_fields(), $edit_data);
        $this->load->view('view_dispatcher_add_edit', $data);
    }
    function dispatcher_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->opensips_form->get_dispatcher_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Dispatcher';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];exit;
            } else {
                $this->opensips_model->edit_opensipsdispatcher($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "Dispatcher updated Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Add Taxes';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                 echo $data['validation_errors'];exit;
            } else {
                $this->opensips_model->add_opensipsdispatcher($add_array);
                echo json_encode(array("SUCCESS"=> "Dispatcher added Successfully."));
                exit;
            }
        } 
    }

    function dispatcher_remove($id) {
        $this->opensips_model->remove_dispatcher($id);
        $this->session->set_flashdata('astpp_notification', 'Dispatcher removed successfully!');
        redirect(base_url() . 'opensips/dispatcher_list/');
    }

    function dispatcher_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Opensips Dispatcher List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->opensips_form->build_opensipsdispatcher_list();
        $data["grid_buttons"] = $this->opensips_form->build_grid_dispatcherbuttons();
        $data['form_search'] = $this->form->build_serach_form($this->opensips_form->get_search_dispatcher_form());
        $this->load->view('view_dispatcher_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function dispatcher_list_json() {
        $json_data = array();
        $count_all = $this->opensips_model->getopensipsdispatcher_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp']=10, $_GET['page']=1);
        $json_data = $paging_data["json_paging"];

        $query = $this->opensips_model->getopensipsdispatcher_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->opensips_form->build_opensipsdispatcher_list());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }

    function dispatcher_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('opensipsdevice_list_search', $this->input->post());
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'opensips/opensips_list/');
        }
    }

    function dispatcher_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

}

?>
 