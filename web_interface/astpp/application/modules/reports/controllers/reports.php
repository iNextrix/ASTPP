<?php

class Reports extends MX_Controller {

    function Reports() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("reports_form");
        $this->load->library('astpp/form');
        $this->load->model('reports_model');
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function customerReport_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('customer_cdr_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'reports/customerReport/');
        }
    }

    function customerReport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
//        redirect(base_url() . 'accounts/reseller_list/');
    }

    function customerReport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Customer CDRs Report';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_report_list_for_admin();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_customer_cdr_form());
        $this->load->view('view_configuration_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function customerReport_json() {
        $json_data = array();
        $count_all = $this->reports_model->getsystem_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->reports_model->getsystem_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_report_list_for_admin());
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_customer_cdr_form());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function resellerReport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Resellers CDRs Report';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_report_list_for_reseller();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons_reseller();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_reseller_cdr_form());
        $this->load->view('view_cdr_reseller_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function resellerReport_json() {
        $json_data = array();
        $count_all = $this->reports_model->getreseller_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->getreseller_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_report_list_for_reseller());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function resellerReport_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('reseller_cdr_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'reports/resellerReport/');
        }
    }

    function resellerReport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function providerReport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Provider CDRs Report';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_report_list_for_provider();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_provider_cdr_form());
        $this->load->view('view_cdr_provider_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function providerReport_json() {
        $json_data = array();
        $count_all = $this->reports_model->getprovider_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->getprovider_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_report_list_for_provider());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function providerReport_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('provider_cdr_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'reports/providerReport/');
        }
    }

    function providerReport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function customerReport_export_cdr_xls() {
        $query = $this->reports_model->getcustomercdrs(true, '', '', false);
        $customer_array = array();
        if ($this->session->userdata['logintype'] == 2) {
//             $customer_array[] = array("Date", "CallerID", "Called Number", "Account Number", "Bill Seconds", "Disposition", "Debit", "Cost", "Trunk", "Provider", "Pricelist", "Code", "Destination", "Call Type");
            
            $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",  "Trunk", "Rate Group",  "Call Type");
            
        } else {
           $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",   "Rate Group",  "Call Type");
        }
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                if ($this->session->userdata['logintype'] == 2) {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
			$row['pattern'],
                        $row['notes'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['disposition'],
			$row['number'],
                        $row['trunk_id'],
                        $row['pricelist_id'],
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
			$row['billseconds'],
			$this->common_model->calculate_currency($row['debit']),
			$this->common_model->calculate_currency($row['cost']),
			$row['disposition'],
			$row['number'],
			$row['pricelist_id'],
			$row['calltype']
                    );
                }
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Customer_CDR_' . date("Y-m-d") . '.xls');
    }

    function customerReport_export_cdr_pdf() {
        $query = $this->reports_model->getcustomercdrs(true, '', '', false);
        $customer_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');

        if ($this->session->userdata['logintype'] == 2) {
            $this->fpdf->tablewidths = array(20, 20, 16, 16, 10, 18, 13, 13, 16, 14, 12, 10, 15);
//             $customer_array[] = array("Date", "CallerID", "Called Number", "Account Number", "BillSec", "Dispo.", "Debit", "Cost", "Trunk", "Provider", "Pricelist", "Code", "Destination", "Call Type");
            
            
             $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",  "Trunk", "Rate Group",  "Call Type");
            
        } else {
            $this->fpdf->tablewidths = array(22, 24, 20, 18, 10, 27, 13, 13, 14, 13, 15, 16);
          $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",   "Rate Group",  "Call Type");
        }
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                if ($this->session->userdata['logintype'] == 2) {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['disposition'],
                        $row['number'],
			$row['trunk_id'],
                        $row['pricelist_id'],
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
                         $row['billseconds'],
                         $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['disposition'],
                        $row['number'],
			$row['pricelist_id'],
                        $row['calltype']
                    );
                }
            }
        }

        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();

        $this->fpdf->SetFont('Arial', '', 15);
        $this->fpdf->SetXY(60, 5);
        $this->fpdf->Cell(100, 10, "Customer CDR Report " . date('Y-m-d'));

        $this->fpdf->SetY(20);
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->lMargin = 2;

        $dimensions = $this->fpdf->export_pdf($customer_array, "5");
        $this->fpdf->Output('Customer_CDR_' . date("Y-m-d") . '.pdf', "D");
    }

    function resellerReport_export_cdr_xls() {
        $query = $this->reports_model->getresellercdrs(true, '', '', false);
        $customer_array = array();
        if ($this->session->userdata['logintype'] == 2) {
              $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",  "Trunk", "Rate Group",  "Call Type");
        } else {
            $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",   "Rate Group",  "Call Type");
        }
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                if ($this->session->userdata['logintype'] == 2) {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['number'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        
                        $row['trunk_id'],
                        $row['provider_id'],
                        $row['pricelist_id'],
                        $row['pattern'],
                        $row['notes'],
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['number'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['pricelist_id'],
                        $row['pattern'],
                        $row['notes'],
                        $row['calltype']
                    );
                }
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Reseller_CDR_' . date("Y-m-d") . '.xls');
    }

    function resellerReport_export_cdr_pdf() {
        $query = $this->reports_model->getresellercdrs(true);
        $customer_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');

        if ($this->session->userdata['logintype'] == 2) {
            $this->fpdf->tablewidths = array(20, 20, 16, 16, 10, 18, 13, 13, 16, 14, 12, 10, 15);
             $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",  "Trunk", "Rate Group",  "Call Type");
        } else {
            $this->fpdf->tablewidths = array(22, 24, 20, 18, 10, 27, 13, 13, 14, 13, 15, 16);
        $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",   "Rate Group",  "Call Type");
        }
       
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                if ($this->session->userdata['logintype'] == 2) {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
			$row['pattern'],
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['number'],
                        $row['trunk_id'],
                        $row['pricelist_id'],
			$row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['number'],
                        $row['pricelist_id'],
			$row['calltype']
                    );
                }
            }
        }

        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();

        $this->fpdf->SetFont('Arial', '', 15);
        $this->fpdf->SetXY(60, 5);
        $this->fpdf->Cell(100, 10, "Reseller CDR Report " . date('Y-m-d'));

        $this->fpdf->SetY(20);
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->lMargin = 2;

        $dimensions = $this->fpdf->export_pdf($customer_array, "5");
        $this->fpdf->Output('Reseller_CDR_' . date("Y-m-d") . '.pdf', "D");
    }

    function providerReport_export_cdr_xls() {
        $query = $this->reports_model->getprovidercdrs(true, '', '', false);
        $customer_array = array();
        if ($this->session->userdata['logintype'] == 2) {
               $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",  "Trunk", "Rate Group",  "Call Type");
        } else {
             $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",   "Rate Group",  "Call Type");
        }
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                if ($this->session->userdata['logintype'] == 2) {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['number'],
                        $row['trunk_id'],
                        $row['pricelist_id'],
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['number'],
                        $row['pricelist_id'],
                        $row['calltype']
                    );
                }
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Provider_CDR_' . date("Y-m-d") . '.xls');
    }

    function providerReport_export_cdr_pdf() {
        $query = $this->reports_model->getprovidercdrs(true);
        $customer_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');

        if ($this->session->userdata['logintype'] == 2) {
            $this->fpdf->tablewidths = array(20, 20, 16, 16, 10, 18, 13, 13, 16, 14, 12, 10, 15);
            $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",  "Trunk", "Rate Group",  "Call Type");
        } else {
            $this->fpdf->tablewidths = array(22, 24, 20, 18, 10, 27, 13, 13, 14, 13, 15, 16);
           $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account Number",   "Rate Group",  "Call Type");
        }
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                if ($this->session->userdata['logintype'] == 2) {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
			$row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['number'],
                        $row['trunk_id'],
                        $row['pricelist_id'],
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
                        $row['pattern'],
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['number'],
                        $row['pricelist_id'],
                        $row['calltype']
                    );
                }
            }
        }

        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();

        $this->fpdf->SetFont('Arial', '', 15);
        $this->fpdf->SetXY(60, 5);
        $this->fpdf->Cell(100, 10, "Provider CDR Report " . date('Y-m-d'));

        $this->fpdf->SetY(20);
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->lMargin = 2;

        $dimensions = $this->fpdf->export_pdf($customer_array, "5");
        $this->fpdf->Output('Provider_CDR_' . date("Y-m-d") . '.pdf', "D");
    }

    function userReport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Customer Summary Report';
        $this->session->set_userdata('advance_search', 0);
        $data["account"] = $data['ip_pricelist'] = form_dropdown('account',$this->db_model->build_concat_dropdown('id,first_name,last_name,number', 'accounts', 'where_arr', array("type"=>"0", "deleted" => "0")), '');
        if(isset($_POST) && !empty($_POST)){
            $search_data = $_POST;
            $this->session->set_userdata('user_sum_search', $search_data);
        }
        $this->load->view('view_adminReports_userReport', $data);
    }
    function userReport_clear_search_sum_Report(){
        $this->session->set_userdata('user_sum_search',"");
        redirect(base_url()."reports/userReport/");
    }
    

    function userReport_grid() {

	$where = "";
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $reseller_id = $accountinfo["id"];
        } else {
            $reseller_id = "0";
        }
        $com_reseller_id = "";
        $reseller_query = $this->db_model->getSelect("id", "accounts", array("reseller_id"=>$reseller_id,"type"=>"0"));
        $reseller_query = $reseller_query->result_array();
        $com_reseller_id = "";
        foreach($reseller_query as $reseller_value){
            $com_reseller_id .= $reseller_value["id"].",";
        }
        $com_reseller_id = rtrim($com_reseller_id,",");
        if($com_reseller_id != '')
	        $where =  " where accountid IN ($com_reseller_id)";
        if(isset($this->session->userdata["user_sum_search"]) && !empty($this->session->userdata["user_sum_search"])){
            $where = " where ";
            $where_len = strlen($where);
            $search_data = $this->session->userdata("user_sum_search");
            if (!empty($search_data['start_date'])) {
                $where .="callstart >= '".$search_data['start_date']."' ";
            }
            if (!empty($search_data['end_date'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" callstart <= '".$search_data['end_date']."' ";
            }
            if (!empty($search_data['account'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" accountid = '".$search_data['account']."' ";
            }
        }
        
        $json_data = array();
        $sql1 = "SELECT count(*) as total_count FROM customer_cdrs $where GROUP BY pattern,accountid order by accountid";
        $query1 = $this->db->query($sql1);        
        $count_all = $query1->num_rows();
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $sql1 = "SELECT accountid,uniqueid,notes,pattern, COUNT(*) AS attempts, AVG(billseconds) AS acd,"
                . " MAX(billseconds) AS mcd, SUM(billseconds) AS billable, "
                . " SUM(debit) AS cost, SUM(cost) AS price FROM customer_cdrs $where
                      GROUP BY pattern,accountid order by accountid limit ".$paging_data["paging"]["start"].",". $paging_data["paging"]["page_no"];
        $query1 = $this->db->query($sql1);        
        
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['price'];
                $cost = $row1['cost'];
                $profit = $row1['cost'] - $row1['price'];
                $sql2 = "SELECT COUNT(*) AS completed FROM customer_cdrs
                  where disposition IN ('SUCCESS','NORMAL_CLEARING') AND pattern='".$row1['pattern']."' 
                    AND accountid='".$row1['accountid']."'";
                $query2 = $this->db->query($sql2);
                $row2 = $query2->row_array();
                $cmplt = ($row2['completed'] != 0) ? $row2['completed'] : 0;
                $asr =  ($cmplt/$atmpt)* 100;

                $json_data['rows'][] = array('cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    round($acd/60, 2),
                    round($mcd/60, 2),                        
                    round($bill/60, 2),                        
                    $this->common_model->calculate_currency($row1["price"]),
                    $this->common_model->calculate_currency($cost),
                    $this->common_model->calculate_currency($profit)));
                }
        }
        echo json_encode($json_data);        
    }

    /**
     * -------Here we write code for controller adminReports functions resellerReport------
     * Reseller report with call record info from start date to end date with IDD code and destination
     */
      function reseller_summery_Report() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Reseller Summary Report';
        $data['cur_menu_no'] = 5;
        $data["account"] = $data['ip_pricelist'] = form_dropdown('account',$this->db_model->build_concat_dropdown('id,first_name,last_name,number', 'accounts', 'where_arr', array("type"=>"1", "deleted" => "0")), '');
//        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_provider_summary_search_form());
        if(isset($_POST) && !empty($_POST)){
            $search_data = $_POST;
            $this->session->set_userdata('reseller_sum_search', $search_data);
        }
        $this->load->view('view_adminReports_resellerReport', $data);
          
      }
    function reseller_clear_search_sum_Report(){
        $this->session->set_userdata('reseller_sum_search',"");
        redirect(base_url()."reports/reseller_summery_Report/");
    }
    function reseller_sum_Report() {
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $reseller_id = $accountinfo["id"];
        } else {
            $reseller_id = "0";
        }
	$com_reseller_id = "";
        $reseller_query = $this->db_model->getSelect("id", "accounts", array("reseller_id"=>$reseller_id,"type"=>"1"));
        $reseller_query = $reseller_query->result_array();
        foreach($reseller_query as $reseller_value){
            $com_reseller_id .= $reseller_value["id"].",";
        }
        $com_reseller_id = rtrim($com_reseller_id,",");
        

	if($com_reseller_id != ""){
	 	 $where =  " where accountid IN ($com_reseller_id) ";
	}else{
		$where =  " where accountid  NOT IN ($reseller_id)";
	} 

       if(isset($this->session->userdata["reseller_sum_search"]) && !empty($this->session->userdata["reseller_sum_search"])){
            $where = " where ";
            $where_len = strlen($where);
            $search_data = $this->session->userdata("reseller_sum_search");
          
            if (!empty($search_data['start_date'])) {
                $where .="callstart >= '".$search_data['start_date']."' ";
            }
            if (!empty($search_data['end_date'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" callstart <= '".$search_data['end_date']."' ";
            }
            if (!empty($search_data['account'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" accountid = '".$search_data['account']."' ";
            }
        }
        $this->db_model->build_search('reseller_sum_search');
        $json_data = array();
        $sql1 = "SELECT count(*) as total_count FROM reseller_cdrs $where GROUP BY pattern,accountid order by accountid";
        $query1 = $this->db->query($sql1);        
        $count_all = $query1->num_rows();
        
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $this->db_model->build_search('provider_summary_search');
        $sql1 = "SELECT accountid,uniqueid,notes,pattern, COUNT(*) AS attempts, AVG(billseconds) AS acd,"
                . " MAX(billseconds) AS mcd, SUM(billseconds) AS billable, "
                . " SUM(debit) AS cost, SUM(cost) AS price FROM reseller_cdrs $where 
                      GROUP BY pattern,accountid order by accountid  limit ".$paging_data["paging"]["start"].",". $paging_data["paging"]["page_no"];

        $query1 = $this->db->query($sql1);        
        
                if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['price'];
                $cost = $row1['cost'];
                $profit = $row1['cost'] - $row1['price'];
                $sql2 = "SELECT COUNT(*) AS completed FROM reseller_cdrs
                  where disposition IN ('SUCCESS','NORMAL_CLEARING') AND pattern='".$row1['pattern']."' 
                    AND accountid='".$row1['accountid']."'";
                $query2 = $this->db->query($sql2);
                $row2 = $query2->row_array();
                $cmplt = ($row2['completed'] != 0) ? $row2['completed'] : 0;
                $asr =  ($cmplt/$atmpt)* 100;

                $json_data['rows'][] = array('cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    round($acd/60, 2),
                    round($mcd/60, 2),                        
                    round($bill/60, 2),                        
                    $this->common_model->calculate_currency($row1["price"]),
                    $this->common_model->calculate_currency($cost),
                    $this->common_model->calculate_currency($profit)));
                }
        }

        echo json_encode($json_data);        
    }

    function provider_summery_Report() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Provider Summary Report';
        $data['cur_menu_no'] = 5;
        $this->session->set_userdata('advance_search', 0);
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_provider_summary_search_form());
        if(isset($_POST) && !empty($_POST)){
            $search_data = $_POST;
            $this->session->set_userdata('provider_sum_search', $search_data);
        }
        $this->load->view('view_adminReports_providerReport', $data);
    }
    function provider_clear_search_sum_Report(){
        $this->session->set_userdata('provider_sum_search',"");
        redirect(base_url()."reports/provider_summery_Report/");
    }
    function provider_sum_Report() {
	
	if ($this->session->userdata('logintype') == 3) {
            $account_data = $this->session->userdata("accountinfo");
            $where = " where accountid = ".$account_data['id'];
        } else {
            $where = "";
        }
        
        $where =  "";
        if(isset($this->session->userdata["provider_sum_search"]) && !empty($this->session->userdata["provider_sum_search"])){
            $where = " where ";
            $where_len = strlen($where);
            $search_data = $this->session->userdata("provider_sum_search");
            if (!empty($search_data['start_date'])) {
                $where .="callstart >= '".$search_data['start_date']."' ";
            }
            if (!empty($search_data['end_date'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" callstart <= '".$search_data['end_date']."' ";
            }
            if (!empty($search_data['number'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" accountid = '".$search_data['number']."' ";
            }
        }
        $this->db_model->build_search('provider_summary_search');
        $json_data = array();
        $sql1 = "SELECT count(*) as total_count FROM provider_cdrs $where GROUP BY pattern,accountid order by accountid";
        $query1 = $this->db->query($sql1);        
        $count_all = $query1->num_rows();
        
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $this->db_model->build_search('provider_summary_search');
        $sql1 = "SELECT accountid,uniqueid,notes,pattern, COUNT(*) AS attempts, AVG(billseconds) AS acd,"
                . " MAX(billseconds) AS mcd, SUM(billseconds) AS billable, "
                . " SUM(debit) AS cost, SUM(cost) AS price FROM provider_cdrs $where 
                      GROUP BY pattern,accountid order by accountid  limit ".$paging_data["paging"]["start"].",". $paging_data["paging"]["page_no"];
        $query1 = $this->db->query($sql1);        
        
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['price'];
                $cost = $row1['cost'];

                $sql2 = "SELECT COUNT(*) AS completed FROM provider_cdrs
                  where disposition IN ('SUCCESS','NORMAL_CLEARING') AND pattern='".$row1['pattern']."' 
                    AND accountid='".$row1['accountid']."'";
                $query2 = $this->db->query($sql2);
                $row2 = $query2->row_array();
                $cmplt = ($row2['completed'] != 0) ? $row2['completed'] : 0;
                $asr =  ($cmplt/$atmpt)* 100;

                $json_data['rows'][] = array('cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    round($acd/60, 2),
                    round($mcd/60, 2),                        
                    round($bill/60, 2),                        
                    $this->common_model->calculate_currency($cost)));

                }
        }
        echo json_encode($json_data);        
    }

    function user_cdrreport() {
        $json_data = array();
        $count_all = $this->reports_model->getuser_cdrs_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->getuser_cdrs_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_report_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function user_cdrreport_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('customer_cdr_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_cdrs_report/');
        }
    }

    function user_cdrreport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function user_paymentreport() {
        $json_data = array();
        $count_all = $this->reports_model->getuser_payment_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->getuser_payment_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_payment_report_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function user_paymentreport_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('cdr_payment_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_cdrs_report/');
        }
    }

    function user_paymentreport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function customer_cdrreport($accountid) {
        $json_data = array();
        $count_all = $this->reports_model->getcustomer_cdrs_list(false, "", "", $accountid);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->getcustomer_cdrs_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"], $accountid);
        $grid_fields = json_decode($this->reports_form->build_report_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_paymentreport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Customer Payment Report';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_payment_report_for_user();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_user_cdr_payment_form());
        $this->load->view('view_payment_report', $data);
    }

    function customer_paymentreport_json() {
        $json_data = array();
        $count_all = $this->reports_model->getcustomer_payment_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->getcustomer_payment_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_payment_report_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_paymentreport_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('cdr_payment_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_cdrs_report/');
        }
    }

    function customer_paymentreport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }
    function reseller_commissionreport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Reseller Commission Report';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_commission_report_for_admin();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->reseller_commission_search_form());
        $this->load->view('view_commission_report', $data);
    }

    function reseller_commissionreport_json() {
        $json_data = array();
        $count_all = $this->reports_model->getreseller_commission_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->getreseller_commission_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_commission_report_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }
    function reseller_commissionreport_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('reseller_commission_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'reports/reseller_commissionreport/');
        }
    }

    function reseller_commissionreport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('reseller_commission_search', "");
    }

}

?>
 
