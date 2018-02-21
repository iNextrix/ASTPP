<?php

class Rates extends MX_Controller {

    function Rates() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library('rates_form');
        $this->load->library('astpp/form');
        $this->load->model('rates_model');
        $this->load->library('csvreader');
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function terminationrates_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Termination Rate List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->rates_form->build_outbound_list_for_admin();
        $data["grid_buttons"] = $this->rates_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->rates_form->get_termination_search_form());
        $data['form_batch_update'] = $this->form->build_batchupdate_form($this->rates_form->termination_batch_update_form());
        $this->load->view('view_outbound_rates_list', $data);
    }
    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function terminationrates_list_json() {
        $json_data = array();
        $count_all = $this->rates_model->getoutbound_rates_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates_model->getoutbound_rates_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates_form->build_outbound_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function terminationrates_import() {
        $data['page_title'] = 'Import Termination Rates';
        $this->load->view('view_import_terminationrates', $data);
    }

    function origination_import() {
        $data['page_title'] = 'Import Termination Rates';
        $this->load->view('view_import_inboundrates', $data);
    }

    function terminationrates_rates_import() {
        $new_final_arr = array();
        $new_final_arr_key = array('code' => 'pattern',
            'prepend' => 'prepend',
            'destination' => 'comment',
            'connect cost' => 'connectcost',
            'included seconds' => 'includedseconds',
            'per minute cost' => 'cost',
            'increment' => 'inc',
            'trunk' => 'trunk_id',
            'precedence' => 'precedence'
        );
        if (isset($_FILES['rateimport']['name'])) {
            $error = $_FILES['rateimport']['error'];
            if ($error == 0) {
                $uploadedFile = $_FILES["rateimport"]["tmp_name"];
                if ($_FILES["rateimport"]["type"] == "text/csv" || $_FILES["rateimport"]["type"]=="application/csv" ||$_FILES["rateimport"]["type"]="text/x-comma-separated-values"){    
		if (is_uploaded_file($uploadedFile)) {
                        $csv_tmp_data = $this->csvreader->parse_file($uploadedFile);
                        foreach ($csv_tmp_data as $key => $csv_data) {
                            foreach ($csv_data as $field_key => $field_value) {
                                if ($new_final_arr_key[strtolower($field_key)] != '' && $key != '0' && $field_value != "") {
                                    if ($new_final_arr_key[strtolower($field_key)] == 'pattern') {
                                        $csv_data['prepend'] = isset($csv_data['prepend'])?$csv_data['prepend']:"";
                                        $new_final_arr[$key][$new_final_arr_key[strtolower($field_key)]] = "^".$field_value . ".*";
                                    } else {
                                        $new_final_arr[$key][$new_final_arr_key[strtolower($field_key)]] = $field_value;
                                    }
                                    $new_final_arr[$key]["trunk_id"] = $_POST['trunk_id'];
                                    if(!isset($csv_data["Code"]) || !is_numeric($csv_data["Code"])){
                                        unset($new_final_arr[$key]);
                                    }

                                }
                            }
                        }
                        if(!empty($new_final_arr)){
                            $result = $this->rates_model->bulk_insert_terminationrates($new_final_arr);
                            echo $result . ' termination rates Imported successfully!';
                        }else{
                            echo "There is an Error in csv File.";
                        }
                    }
                } else {
                    echo 'Please upload on Only csv file...........!';
                }
          } else {
                echo 'Please select one csv file...........!';
            }
        } else {
            echo 'Please upload on Only csv file...........!' . $error;
        }
    }

    function origination_import_file() {
	
        $new_final_arr = array();
        $new_final_arr_key = array('code' => 'pattern',
            'countrycode' => '',
            'area code' => '',
            'destination' => 'comment',
            'connect cost' => 'connectcost',
            'included seconds' => 'includedseconds',
            'per minute cost' => 'cost',
            'increment' => 'inc',
            'Pricelist' => 'pricelist_id',
            'precedence' => 'precedence'
        );
         if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
					$account_data = $this->session->userdata("accountinfo");
	  }
        if (isset($_FILES['rateimport']['name'])) {
            $error = $_FILES['rateimport']['error'];
            if ($error == 0) {
                $uploadedFile = $_FILES["rateimport"]["tmp_name"];
		if ($_FILES["rateimport"]["type"] == "text/csv" || $_FILES["rateimport"]["type"]=="application/csv" ||$_FILES["rateimport"]["type"]="text/x-comma-separated-values"){
                    if (is_uploaded_file($uploadedFile)) {
                        $csv_tmp_data = $this->csvreader->parse_file($uploadedFile);
                        foreach ($csv_tmp_data as $key => $csv_data) {
                            foreach ($csv_data as $field_key => $field_value) {
                                if ($new_final_arr_key[strtolower($field_key)] != '' && $key != '0' && $field_value != "") {
                                    if ($new_final_arr_key[strtolower($field_key)] == 'pattern') {
                                        $new_final_arr[$key][$new_final_arr_key[strtolower($field_key)]] = "^".$field_value.".*";
                                    } else {
                                        $new_final_arr[$key][$new_final_arr_key[strtolower($field_key)]] = $field_value;
                                    }
                                    $new_final_arr[$key]["pricelist_id"] = $_POST['pricelist_id'];
				    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
					$new_final_arr[$key]["reseller_id"] = $account_data['id'];
				    }
                                    if(!isset($csv_data["Code"]) || !is_numeric($csv_data["Code"])){
                                        unset($new_final_arr[$key]);
                                    }
                                   

                                }
                            }
                        }
                        if(!empty($new_final_arr)){
                            $result = $this->rates_model->bulk_insert_inboundrates($new_final_arr);
                            echo $result . ' origination rates imported successfully! Press close button now...';
                        }else{
                            echo "There is an Error in csv File.";
                        }
                    }
                } else {
                    echo 'Please upload on Only csv file...........!';
                }
            } else {
                echo 'Please select one csv file...........!';
            }
        } else {
            echo 'Please upload on Only csv file...........!' . $error;
        }
    }
    function origination_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Origination Rates';
        $data['form'] = $this->form->build_form($this->rates_form->get_inbound_form_fields(), '');

        $this->load->view('view_inboundrates_add_edit', $data);
    }

    function origination_edit($edit_id = '') {
        $data['page_title'] = 'Origination Rates';
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array('id' => $edit_id, "reseller_id" => $reseller, "status" => "1");
        } else {
            $where = array('id' => $edit_id, "status" => "1");
        }
        $account = $this->db_model->getSelect("*", "routes", $where);
        if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data['connectcost'], '', '', false, false);
            $edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data['cost'], '', '', false, false);
            $edit_data['pattern'] = filter_var($edit_data['pattern'], FILTER_SANITIZE_NUMBER_INT);

            $data['form'] = $this->form->build_form($this->rates_form->get_inbound_form_fields(), $edit_data);
            $this->load->view('view_inboundrates_add_edit', $data);
        } else {
            redirect(base_url() . 'rates/origination_list/');
        }
    }

    function origination_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->rates_form->get_inbound_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Origination Rates';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->edit_inbound($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "Origination updated successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Termination Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {

                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->add_inbound($add_array);
                echo json_encode(array("SUCCESS"=> "Origination added successfully."));
                exit;
            }
        }
    }

    function origination_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('inboundrates_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'rates/origination_list/');
        }
    }

    function origination_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function terminationrates_delete($id) {
        $this->rates_model->remove_outbound($id);
        $this->session->set_flashdata('astpp_notification', 'Termination removed successfully!');
        redirect(base_url() . '/rates/terminationrates_list/');
    }

    function origination_delete($id) {
        $this->rates_model->remove_inbound($id);
        $this->session->set_flashdata('astpp_notification', 'Origination removed successfully!');
        redirect(base_url() . '/rates/origination_list/');
    }

    function origination_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Origination Rate List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->rates_form->build_inbound_list_for_admin();
        $data["grid_buttons"] = $this->rates_form->build_grid_buttons_inbound();
        $data['form_search'] = $this->form->build_serach_form($this->rates_form->get_inbound_search_form());
        $data['form_batch_update'] = $this->form->build_batchupdate_form($this->rates_form->inbound_batch_update_form());
        $this->load->view('view_inbound_rates_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions inboundrates------
     */
    function origination_list_json() {
        $json_data = array();
        $count_all = $this->rates_model->getinbound_rates_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates_model->getinbound_rates_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates_form->build_inbound_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function terminationrates_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Termination Rates';
        $data['form'] = $this->form->build_form($this->rates_form->get_termination_form_fields(), '');

        $this->load->view('view_outboundrates_add_edit', $data);
    }

    function terminationrates_edit($edit_id = '') {
        $data['page_title'] = 'Termination Rates';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "outbound_routes", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
	$edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data['connectcost'], '', '', false, false);
	$edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data['cost'], '', '', false, false);

        $edit_data['pattern'] = filter_var($edit_data['pattern'], FILTER_SANITIZE_NUMBER_INT);
        $data['form'] = $this->form->build_form($this->rates_form->get_termination_form_fields(), $edit_data);
        $this->load->view('view_outboundrates_add_edit', $data);
    }

    function terminationrates_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->rates_form->get_termination_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Termination Rates';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->edit_outbound($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "Termination updated successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Termination Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {

                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->add_outbound($add_array);
                echo json_encode(array("SUCCESS"=> "Termination added successfully."));
                exit;
            }
        }
        $this->load->view('view_outboundrates_add_edit', $data);
    }

    function terminationrates_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('terminationrates_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'rates/terminationrates_list/');
        }
    }

    function terminationrates_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function customer_block_pattern_list($accountid) {
        $json_data = array();
        $where = array('accountid' => $accountid);

        $count_all = $this->db_model->countQuery("*", "block_patterns", $where);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $pattern_data = $this->db_model->getSelect("*", "block_patterns", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        $grid_fields = json_decode($this->rates_form->build_pattern_list_for_customer($accountid));
        $json_data['rows'] = $this->form->build_grid($pattern_data, $grid_fields);

        echo json_encode($json_data);
    }

    function terminationrates_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("outbound_routes");
    }

    function origination_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("routes");
    }

    function user_inboundrates_list_json() {
        $json_data = array();
        $count_all = $this->rates_model->getinbound_rates_list_for_user(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates_model->getinbound_rates_list_for_user(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates_form->build_inbound_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }

    function user_inboundrates_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('inboundrates_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_rates_list/');
        }
    }

    function user_inboundrates_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }
    function customer_rates_download_sample_file($file_name){
        $this->load->helper('download');
	$full_path = base_url()."assets/Rates_File/".$file_name.".csv";
        $file = file_get_contents($full_path);
        force_download("samplefile.csv", $file); 
    }
    function terminationrates_batch_update(){
        $batch_update_arr = $this->input->post();
	$batch_update_arr["cost"]["cost"] = isset($batch_update_arr["cost"]["cost"])?$this->common_model->add_calculate_currency($batch_update_arr["cost"]["cost"], '', '', true, false):"0.0000";
	$batch_update_arr["connectcost"]["connectcost"] = isset($batch_update_arr["connectcost"]["connectcost"])?$this->common_model->add_calculate_currency($batch_update_arr["connectcost"]["connectcost"], '', '', true, false):"0.0000";
//        $batch_update_arr = array("inc"=> array("inc"=>"1","operator"=>"3"),"cost"=> array("cost"=>"1","operator"=>"4"));
        $result = $this->rates_model->termination_rates_batch_update($batch_update_arr);
        echo json_encode(array("SUCCESS"=> "Termination Rates Batch Updated successfully."));
        exit;
    }
    
    function origination_batch_update(){
        $batch_update_arr = $this->input->post();
	$batch_update_arr["cost"]["cost"] = isset($batch_update_arr["cost"]["cost"])?$this->common_model->add_calculate_currency($batch_update_arr["cost"]["cost"], '', '', true, false):"0.0000";
//        $batch_update_arr = array("inc"=> array("inc"=>"1","operator"=>"3"),"cost"=> array("cost"=>"1","operator"=>"4"));
        $result = $this->rates_model->inboundrates_rates_batch_update($batch_update_arr);
        echo json_encode(array("SUCCESS"=> "Origination Rates Batch Updated successfully."));
        exit;
    }
    
}

?>
 
