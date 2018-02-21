<?php

class Callingcards extends CI_Controller {

    function Callingcards() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->helper('form');
        $this->load->model('callingcards_model');
        $this->load->library("callingcard_form");
        $this->load->library("astpp/form");


        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');
        $this->load->helper('csv');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . 'login/login');
    }

    function callingcards_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'List Calling Cards';
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->callingcard_form->build_cc_list_for_admin();
        $data["grid_buttons"] = $this->callingcard_form->build_grid_buttons();

        $data['form_search'] = $this->form->build_serach_form($this->callingcard_form->get_callingcards_search_form());
        $this->load->view('view_callingcard_list', $data);
    }

    /**
     * -------Here we write code for controller callingcards functions manage_json------
     * Listing of Calling Cards
     */
    function callingcards_list_json() {
        $json_data = array();

        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $brands = $this->callingcards_model->list_cc_brands_reseller($this->session->userdata('username'));
        } else {
            $brands = $this->callingcards_model->get_cc_brands();
        }
        if (!empty($brands)) {
            $brandsql = " IN (";
            $list = implode("','", $brands);
            $list = "'" . $list . "'";
            $brandsql .= $list;
            $brandsql .= ")";
        } else {
            $brands = "";
        }

        $count_all = $this->callingcards_model->getCCList(false, $brands, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);

        $json_data = $paging_data["json_paging"];
        $query = $this->callingcards_model->getCCList(true, $brands, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);

        $grid_fields = json_decode($this->callingcard_form->build_cc_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }

    function callingcards_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';

        $data['cur_menu_no'] = 1;
        $data['page_title'] = 'Add New Calling Card';

        $data['form'] = $this->form->build_form($this->callingcard_form->get_callingcard_form_fields(), '');

        $this->load->view('view_cc_add_edit', $data);
    }

    function callingcards_edit($edit_id = '') {

        $data['page_title'] = 'Edit Customer Account';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "callingcards", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $edit_data['value'] = $this->common_model->to_calculate_currency($edit_data['value'], '', '', false, false);

        $data['form'] = $this->form->build_form($this->callingcard_form->get_callingcard_form_fields(), $edit_data);
        $this->load->view('view_cc_add_edit', $data);
    }

    function callingcards_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->callingcard_form->get_callingcard_form_fields(), $add_array);
        if ($this->form_validation->run() == FALSE) {
            $data['validation_errors'] = validation_errors();
            echo $data['validation_errors'];
            exit;
        } else {
            $add_array['value'] = $this->common_model->add_calculate_currency($add_array['value'], '', '', false, false);
            $response = $this->callingcards_model->add_callingcard($add_array);
            echo json_encode(array("SUCCESS"=> "Calling card Setup Completed."));
            exit;
        }
    }

    function callingcards_delete($id) {
        $this->callingcards_model->remove_callingcard($id);
        $this->session->set_flashdata('astpp_notification', 'Calling card Remove successfully.!');
        redirect(base_url() . 'callingcards/callingcards_list/');
    }

    function callingcards_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('callingcard_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'callingcards/callingcards_list/');
        }
    }

    function callingcards_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    /**
     * -------Here we write code for controller callingcards functions update_status------
     * update status of card
     */
    function callingcards_update_status() {
        $data['form'] = $this->form->build_form($this->callingcard_form->get_callingcard_updatestatus_form_fields(), '');
        if ($this->input->post()) {
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $response = $this->callingcards_model->update_status_card($this->input->post());
                $this->session->set_flashdata('astpp_notification', 'Calling card status Updated successfully.!');
                redirect(base_url() . 'callingcards/callingcards_list/');
            }
        }
        $this->load->view('view_cc_update_status', $data);
    }

    /**
     * -------Here we write code for controller callingcards functions refill------
     * Refill Card no 
     * @$card_number: Card Number
     */
    function callingcards_refill($card_number = '') {

        if (!isset($card_number) || $card_number == '') {
            $data_post = $this->input->post();
            $card_number = $data_post['id'];
        }
        $where = array('id' => $card_number);
        $callingcard = $this->db_model->getSelect("*", "callingcards", $where);

        foreach ($callingcard->result_array() as $key => $value) {
            $edit_data = $value;
        }

        $data['form'] = $this->form->build_form($this->callingcard_form->get_callingcard_refill_form_fields($edit_data['id'], $edit_data['cardnumber']), '');
        if ($this->input->post()) {
            $data_post = $this->input->post();

            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $data_post['value'] = $this->common_model->add_calculate_currency($data_post['value'], '', '', false, false);
                $response = $this->callingcards_model->refill_card($data_post);
                $this->session->set_flashdata('astpp_notification', 'Calling card refill successfully.!');
                redirect(base_url() . 'callingcards/callingcards_list/');
            }
        }
        $this->load->view('view_cc_refill', $data);
    }

    function brands() {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Calling Cards | Brands';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Brands List';
        $data['cur_menu_no'] = 5;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->callingcard_form->build_ccbrand_list_for_admin();
        $data["grid_buttons"] = $this->callingcard_form->build_grid_buttons_ccbrand();
        $data['form_search'] = $this->form->build_serach_form($this->callingcard_form->get_ccbrands_search_form());
        $this->load->view('view_ccbrand_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function brands_json() {
        $json_data = array();

        $count_all = $this->callingcards_model->getccbrand_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->callingcards_model->getccbrand_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->callingcard_form->build_ccbrand_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function brands_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add New Calling Card';
        $data['form'] = $this->form->build_form($this->callingcard_form->get_ccbrands_form_fields(), '');

        $this->load->view('view_ccbrand_add_edit', $data);
    }

    function brands_edit($edit_id = '') {
        $data['page_title'] = 'Edit Customer Account';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "callingcardbrands", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $edit_data['maint_fee_pennies'] = $this->common_model->add_calculate_currency($edit_data['maint_fee_pennies'], '', '', true, false);
        $edit_data['disconnect_fee_pennies'] = $this->common_model->add_calculate_currency($edit_data['disconnect_fee_pennies'], '', '', true, false);
        $edit_data['minute_fee_pennies'] = $this->common_model->add_calculate_currency($edit_data['minute_fee_pennies'], '', '', true, false);
        $edit_data['min_length_pennies'] = $this->common_model->add_calculate_currency($edit_data['min_length_pennies'], '', '', true, false);

        $data['form'] = $this->form->build_form($this->callingcard_form->get_ccbrands_form_fields(), $edit_data);
        $this->load->view('view_ccbrand_add_edit', $data);
    }

    function brands_save() {
        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->callingcard_form->get_ccbrands_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Cc Brands';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['maint_fee_pennies'] = $this->common_model->add_calculate_currency($add_array['maint_fee_pennies'], '', '', false, false);
                $add_array['disconnect_fee_pennies'] = $this->common_model->add_calculate_currency($add_array['disconnect_fee_pennies'], '', '', false, false);
                $add_array['minute_fee_pennies'] = $this->common_model->add_calculate_currency($add_array['minute_fee_pennies'], '', '', false, false);
                $add_array['min_length_pennies'] = $this->common_model->add_calculate_currency($add_array['min_length_pennies'], '', '', false, false);
                $this->callingcards_model->edit_ccbrand($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "Brand Updated successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Cc Brands';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['maint_fee_pennies'] = $this->common_model->add_calculate_currency($add_array['maint_fee_pennies'], '', '', false, false);
                $add_array['disconnect_fee_pennies'] = $this->common_model->add_calculate_currency($add_array['disconnect_fee_pennies'], '', '', false, false);
                $add_array['minute_fee_pennies'] = $this->common_model->add_calculate_currency($add_array['minute_fee_pennies'], '', '', false, false);
                $add_array['min_length_pennies'] = $this->common_model->add_calculate_currency($add_array['min_length_pennies'], '', '', false, false);

                $response = $this->callingcards_model->add_ccbrand($add_array);
                echo json_encode(array("SUCCESS"=> "Brand added successfully."));
                exit;
            }
        }
    }

    function brands_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('ccbrand_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'callingcards/brands//');
        }
    }

    function brands_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function brands_delete($id) {
        $this->callingcards_model->remove_ccbrand($id);
        $this->session->set_flashdata('astpp_notification', 'Calling card Brand Remove successfully.!');
        redirect(base_url() . 'callingcards/brands/');
    }

    function callingcards_cdrs() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Calling Cards CDRs';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->callingcard_form->build_cdrreport_list_for_admin();
        $data["grid_buttons"] = $this->callingcard_form->build_grid_buttons_cdrs();
        $data['form_search'] = $this->form->build_serach_form($this->callingcard_form->get_cc_cdr_form());
        $this->load->view('view_callingcard_cdr_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function callingcards_cdrs_json() {

        $json_data = array();
        $count_all = $this->callingcards_model->getcallingcard_cdr(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->callingcards_model->getcallingcard_cdr(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->callingcard_form->build_cdrreport_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }

    function callingcards_export_cc_cdr_xls() {
        $query = $this->callingcards_model->getcallingcard_cdr('1', '', false);

        $cc_array = array();
        $cc_array[] = array("Date", "CallerID", "Called Number", "Card Number", "Bill Seconds", "Disposition", "Debit", "Destination", "Pricelist", "Code");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row['cardnumber'] = $this->common->get_field_name('cardnumber', 'callingcards', $row['callingcard_id']);
                $cc_array[] = array(
                    $row['callstart'],
                    $row['clid'],
                    $row['destination'],
                    $row['cardnumber'],
                    $row['seconds'],
                    $row['disposition'],
                    $this->common_model->calculate_currency($row['debit']),
                    $row['notes'],
                    $row['pricelist_id'],
                    $row['pattern']
                );
            }
        }
        $this->load->helper('csv');
        array_to_csv($cc_array, 'CallingCard_CDR_' . date("Y-m-d") . '.xls');
    }

    function callingcards_export_cc_cdr_pdf() {
        $query = $this->callingcards_model->getcallingcard_cdr('1', '', false);
        $cc_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');

        $this->fpdf->tablewidths = array(25, 25, 21, 18, 10, 30, 16, 20, 14, 13);
        $cc_array[] = array("Date", "CallerID", "Called Number", "Card Number", "Bill Seconds", "Disposition", "Debit", "Destination", "Pricelist", "Code");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                $row['cardnumber'] = $this->common->get_field_name('cardnumber', 'callingcards', $row['callingcard_id']);
                $cc_array[] = array(
                    $row['callstart'],
                    $row['clid'],
                    $row['destination'],
                    $row['cardnumber'],
                    $row['seconds'],
                    $row['disposition'],
                    $this->common_model->calculate_currency($row['debit']),
                    $row['notes'],
                    $row['pricelist_id'],
                    $row['pattern']
                );
            }
        }

        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();

        $this->fpdf->SetFont('Arial', '', 15);
        $this->fpdf->SetXY(60, 5);
        $this->fpdf->Cell(100, 10, "CallingCard CDR Report " . date('Y-m-d'));

        $this->fpdf->SetY(20);
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->lMargin = 2;

        $dimensions = $this->fpdf->export_pdf($cc_array, "5");
        $this->fpdf->Output('CallingCard_CDR_' . date("Y-m-d") . '.pdf', "D");
    }

    function callingcards_cdrs_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('cc_cdr_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/customer_list/');
        }
    }

    function callingcards_cdrs_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function callingcards_view($id = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'View - Calling Cards';

        if ($cc = $this->callingcards_model->get_card_by_number($id)) {
            $data['cc'] = $cc;
            $data['cdrs'] = $this->callingcards_model->get_callingcard_cdrs($id);
            $data['cc']['account'] = $this->common->get_field_name('number', 'accounts', $cc['account_id']);
        } else {
            echo "This card is not available.";
            return;
        }
        $this->load->view('view_cc_add', $data);
    }

    function callingcards_add_callerid($id = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Caller ID';
        $data['callingcard_number'] = $this->common->get_field_name('cardnumber', 'callingcards', $id);
        $result = $this->callingcards_model->get_callerid($id);
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $values) {
                $data['callingcard_id'] = $id;
                $data['callerid_name'] = $values['callerid_name'];
                $data['callerid_number'] = $values['callerid_number'];
                $data['status'] = $values['status'];
                $data['id'] = $values['callingcard_id'];
                $mode = 'edit';
            }
        } else {
            $data['callingcard_id'] = $id;
            $data['callerid_name'] = '';
            $data['callerid_number'] = '';
            $data['status'] = '';
            $data['id'] = '';
            $mode = 'add';
        }
        $data['form'] = $this->form->build_form($this->callingcard_form->get_cc_callerid_fields($mode), $data);
        if (($this->input->post())) {
            $add_array = $this->input->post();
            if (isset($add_array['edit'])) {
                $this->callingcards_model->edit_callerid($this->input->post());
                $this->session->set_flashdata('astpp_notification', 'CallerID update successfully...');
                redirect(base_url() . 'callingcards/callingcards_list/');
            } else {
                $this->callingcards_model->add_callerid($this->input->post());
                $this->session->set_flashdata('astpp_notification', 'CallerID Added successfully...');
                redirect(base_url() . 'callingcards/callingcards_list/');
            }
            exit;
        }
        $this->load->view('view_cc_add_callerid', $data);
    }

}

?>