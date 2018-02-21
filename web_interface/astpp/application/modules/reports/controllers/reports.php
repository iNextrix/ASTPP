<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################
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
        $this->session->set_userdata('customer_cdr_list_search', "");
//        redirect(base_url() . 'accounts/reseller_list/');
    }

    function customerReport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Customer CDRs Report';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_report_list_for_admin();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_customer_cdr_form());
        
        $this->load->view('view_customercdrs_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function customerReport_json() {
        $json_data = array();
        $count_all = $this->reports_model->getcustomer_cdrs_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->reports_model->getcustomer_cdrs_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_report_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function resellerReport() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Resellers CDRs Report';
        $data['search_flag'] = true;
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
//echo "<pre>"; print_r($query->result()); exit;        
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
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_report_list_for_provider();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons_provider();
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
            
            $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account",  "Trunk", "Rate Group",  "Call Type");
            
        } else {
           $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Bill Seconds","Debit", "Cost","Disposition", "Account",   "Rate Group",  "Call Type");
        }
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                if ($this->session->userdata['logintype'] == 2) {
                    $customer_array[] = array(
			$this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
			$this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['disposition'],
			$this->common->get_field_name('number','accounts',array("id"=>$row['accountid'])),
                        $this->common->get_field_name('name','trunks',$row['trunk_id']),
                        $this->common->get_field_name('name','pricelists',$row['pricelist_id']),
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
                        $this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
			$row['billseconds'],
			$this->common_model->calculate_currency($row['debit']),
			$this->common_model->calculate_currency($row['cost']),
			$row['disposition'],
			$this->common->get_field_name('number','accounts',array("id"=>$row['accountid'])),
			$this->common->get_field_name('name','pricelists',$row['pricelist_id']),
			$row['calltype']
                    );
                }
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Customer_CDR_' . date("Y-m-d") . '.csv');
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
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
			$this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['disposition'],
                        $row['number'],
			$this->common->get_field_name('name','trunks',$row['trunk_id']),
                        $this->common->get_field_name('name','pricelists',$row['pricelist_id']),
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
                        $this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['disposition'],
                        $row['number'],
			$this->common->get_field_name('name','pricelists',$row['pricelist_id']),
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
        $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Duration","Debit", "Cost","Disposition", "Account",   "Rate Group",  "Call Type");
        if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $customer_array[] = array(
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
                        filter_var($row['pattern'], FILTER_SANITIZE_NUMBER_INT),
                        $row['notes'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['disposition'],
                        $this->common->build_concat_string("first_name,last_name,number", "accounts",$row['accountid']),
                        $this->common->get_field_name('name','pricelists',$row['pricelist_id']),
                        $row['calltype'],
                    );
                }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Reseller_CDR_' . date("Y-m-d") . '.csv');
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
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
			$this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['number'],
                        $this->common->get_field_name('name','trunks',$row['trunk_id']),
                        $this->common->get_field_name('name','pricelists',$row['pricelist_id']),
			$row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
                        $this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['number'],
                        $this->common->get_field_name('name','pricelists',$row['pricelist_id']),
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
        $customer_array[] = array("Date", "CallerID", "Called Number","Code",  "Destination", "Duration","Debit","Disposition", "Account");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                    $customer_array[] = array(
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
                        filter_var($row['pattern'], FILTER_SANITIZE_NUMBER_INT),
                        $row['notes'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $row['disposition'],
			$this->common->build_concat_string("first_name,last_name,number", "accounts",$row['accountid']),
                    );
                }
            }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Provider_CDR_' . date("Y-m-d") . '.csv');
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
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
                        $this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
			$row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
			$row['number'],
                        $this->common->get_field_name('name','trunks',$row['trunk_id']),
                        $this->common->get_field_name('name','pricelists',$row['pricelist_id']),
                        $row['calltype']
                    );
                } else {
                    $customer_array[] = array(
                        $this->common->convert_GMT_to('','',$row['callstart']),
                        $row['callerid'],
                        $row['callednum'],
                        $this->common->get_only_numeric_val('','',$row['pattern']),
                        $row['notes'],
                        $row['billseconds'],
                        $row['disposition'],
                        $this->common_model->calculate_currency($row['debit']),
                        $this->common_model->calculate_currency($row['cost']),
                        $row['number'],
                        $this->common->get_field_name('name','pricelists',$row['pricelist_id']),
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

    function customersummary() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Customer Summary Report';
        $data['search_flag'] = true;
//        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_customersummary();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons_customersummary();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_customersummary_search_form());
        $this->load->view('view_customersummary_report', $data);
    }
    function customersummary_json() {
        $json_data = array();      
        $count_all = $this->reports_model->get_customersummary_report_list(false,0,0);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query1 =$this->reports_model->get_customersummary_report_list(true,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $i=0;
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $bill = $row1['billable'];
                if($row1['completed']>0){
                  $acd = round($bill/$row1['completed']);
                }
                else{
                  $acd=0;
                }
                $mcd = $row1['mcd'];
               // $acd = 
                $cost = $row1['cost'];
                $profit = $row1['cost'] - $row1['price'];
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
                $asr =  ($cmplt/$atmpt)* 100;
                $debit=$this->common_model->calculate_currency($row1['price']);
                $cost=$this->common_model->calculate_currency($cost);
                $profit=$this->common_model->calculate_currency($profit);
		$billsec = $bill > 0 ? floor($bill/60).":".($bill % 60) : "00:00";
		$maxsec = $mcd > 0 ? floor($mcd/60).":".($mcd % 60) : "00:00";
		$avgsec = $acd > 0 ? floor($acd/60).":".($acd % 60) : "00:00";
                $json_data['rows'][] = array('cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
		    $avgsec,
                    $maxsec,                        
                    $billsec,                        
                    $debit,
                    $cost,
                    $profit));
                    $i++;
                }
        }
        echo json_encode($json_data);        
    }
  /*  function customersummary_json() {
        $json_data = array();      
        $count_all = $this->reports_model->get_customersummary_report_list(false,0,0);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query1 =$this->reports_model->get_customersummary_report_list(true,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['price'];
                $cost = $row1['cost'];
                $profit = $row1['cost'] - $row1['price'];
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
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
                    $this->common_model->calculate_currency($cost),
                    $this->common_model->calculate_currency($row1["price"]),
                    $this->common_model->calculate_currency($profit)));
                }
        }
        echo json_encode($json_data);        
    }
      */
    function customersummary_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('customersummary_reports_search', $this->input->post());
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'reports/customersummary/');
        }
    }
      function customersummary_clearsearchfilter(){
           $this->session->set_userdata('advance_search', 0);
           $this->session->set_userdata('customersummary_reports_search', "");
      }
    /**
     * -------Here we write code for controller adminReports functions resellerReport------
     * Reseller report with call record info from start date to end date with IDD code and destination
     */
      function resellersummary() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Reseller Summary Report';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search',0);
        $data['grid_fields'] = $this->reports_form->build_resellersummary();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons_resellersummary();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_resellersummary_search_form());
        $this->load->view('view_resellersummary_report', $data);
      }
      //changes astpp21...
      function resellersummary_json(){
        $json_data = array();
        $count_all=$this->reports_model->get_resellersummary_report(false,0,0);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query1=$this->reports_model->get_resellersummary_report(true,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $i=0;
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['debit'];
                $cost = $row1['cost'];
                $profit = $row1['profit'];
                $profit=$this->common_model->calculate_currency($profit);
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
                $debit=$this->common_model->calculate_currency($row1["debit"]);
                $cost=$this->common_model->calculate_currency($cost);
                $asr =  ($cmplt/$atmpt)* 100;
		$billsec = $bill > 0 ? floor($bill/60).":".($bill % 60) : "00:00";
		$maxsec = $mcd > 0 ? floor($mcd/60).":".($mcd % 60) : "00:00";
		$avgsec = $acd > 0 ? floor($acd/60).":".($acd % 60) : "00:00";
                $json_data['rows'][$i] = array('cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    $avgsec,
                    $maxsec,
                    $billsec,
                    $debit,
                    $cost,
                    $profit));
                    $i++;
                }
        }
        echo json_encode($json_data); 
      }

  /*    function resellersummary_json(){
        $json_data = array();
        $count_all=$this->reports_model->get_resellersummary_report(false,0,0);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query1=$this->reports_model->get_resellersummary_report(true,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $completed_calls=0;
        $acd_total=0;
        $mcd_total=0;
        $debit_total=0;
        $i=0;
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $attempted_total+=$atmpt;
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['debit'];
                $cost = $row1['cost'];
                $profit = $row1['profit'];
                $profit=$this->common_model->calculate_currency($profit);
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
                $debit=$this->common_model->calculate_currency($row1["debit"]);
                $cost=$this->common_model->calculate_currency($cost);
                $asr =  ($cmplt/$atmpt)* 100;
		$min_sec = $bill > 0 ? floor($bill/60).":".($bill % 60) : "00:00";
                $json_data['rows'][$i] = array('cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    round($acd/60, 2),
                    round($mcd/60, 2),                        
                    $min_sec,
                    $debit,
                    $cost,
                    $profit));
                    $i++;
                }
        }
        echo json_encode($json_data); 
      }*/
      function resellersummary_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('resellersummary_reports_search', $this->input->post());
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'reports/resellersummary/');
        }
    }
      function resellersummary_clearsearchfilter(){
           $this->session->set_userdata('advance_search', 0);
           $this->session->set_userdata('resellersummary_reports_search', "");
      }
    function providersummary() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Provider Summary Report';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_providersummary();
        $data["grid_buttons"] = $this->reports_form->build_grid_buttons_providersummary();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_providersummary_search_form());
        $this->load->view('view_providersummary_report', $data);
    }
    function providersummary_json() {
        $json_data = array();
        $count_all=$this->reports_model->get_providersummary_report_list(false,0,0);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query1=$this->reports_model->get_providersummary_report_list(true,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);       
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['price'];
                $cost = $row1['cost'];
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
                $asr =  ($cmplt/$atmpt)* 100;
//                $profit = $row1['cost'] - $row1['price'];
                $json_data['rows'][] = array('cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['provider_id']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    round($acd/60, 2),
                    round($mcd/60, 2),                        
             //       round($bill/60, 2),                        
		floor($bill/60).":".($bill%60),
                    $this->common_model->calculate_currency($cost),
//                    $this->common_model->calculate_currency($profit)
                    ));

                }
        }
        echo json_encode($json_data);        
    }
    function providersummary_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('providersummary_reports_search', $this->input->post());
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'reports/providersummary/');
        }
    }
    function providersummary_clearsearchfilter(){
	$this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('providersummary_search',"");
        redirect(base_url()."reports/providersummery/");
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
            $this->session->set_userdata('user_cdrs_report_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_cdrs_report/');
        }
    }

    function user_cdrreport_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('user_cdrs_report_search', "");
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

    function customer_cdrreport($accountid,$entity_type) {
    //echo 'dada'; exit;
        $json_data = array();
        $count_all = $this->reports_model->users_cdrs_list(false,$accountid,$entity_type, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->reports_model->users_cdrs_list(true,$accountid,$entity_type,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->reports_form->build_report_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function paymentreport() {
//         echo 'asdvasvd';exit;
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Payment Report';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->reports_form->build_payment_report_for_user();
        $data['form_search'] = $this->form->build_serach_form($this->reports_form->get_user_cdr_payment_form());
        $this->load->view('view_payment_report', $data);
    }

    function paymentreport_json() {
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
    
     function userReport_export_cdr_xls() {
        $query = $this->reports_model->getcustomercdrs(true, '', '', false);
        $customer_array = array();

            $customer_array[] = array("Date", "CallerID", "Called Number","Destination", "Bill Seconds","Debit","Disposition","Call Type");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
               
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
			$row['pattern'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $row['disposition'],
                        $row['calltype']
                    );
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Customer_CDR_' . date("Y-m-d") . '.csv');
    }

    function userReport_export_cdr_pdf() {
        $query = $this->reports_model->getcustomercdrs(true, '', '', false);
        $customer_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');
            $this->fpdf->tablewidths = array(20, 20, 20, 20, 20, 20, 20, 20);
            $customer_array[] = array("Date", "CallerID", "Called Number","Destination", "Bill Seconds","Debit","Disposition","Call Type");

        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                
                    $customer_array[] = array(
                        $row['callstart'],
                        $row['callerid'],
                        $row['callednum'],
			$row['pattern'],
                        $row['billseconds'],
                        $this->common_model->calculate_currency($row['debit']),
                        $row['disposition'],
                        $row['calltype']
                    );
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
    function customer_traffic_summary_json(){
        
        $json_data = array();$total_duration=0;
        $count_all = $this->reports_model->get_traffic_list(false, "", "");
       // echo "<pre>";print_r($count_all);exit;
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
	    $result=$this->db_model->build_search('cdr_list_search');
            $account_data = $this->session->userdata("accountinfo");
//             echo 'ssssssss';
            $query_calls = $this->db_model->getSelect('count(*) as answerd_call,sum(billseconds) as total_calls',"cdrs", array('disposition'=>'NORMAL_CLEARING',"reseller_id" => $account_data['id']));
        } else {
	    $result=$this->db_model->build_search('cdr_list_search');
// 	    echo 'sdvdsv';
            $query_calls = $this->db_model->getSelect('count(*) as answerd_call,sum(billseconds) as total_calls', "cdrs", array('disposition'=>'NORMAL_CLEARING',"reseller_id" => "0"));
        }
       
      //echo $this->db->last_query();exit;
        if($query_calls->num_rows > 0){
            $result1=$query_calls->result_array();
            $total_answeredcall=$result1[0]['answerd_call'];
//		$total_duration_calls=$result1[0]['total_calls'];
        }
//         print_r($result1);
//         echo $total_answeredcall;
//         exit;
        $calls=$this->reports_model->get_answer_Call('cdrs');
	$total_duration=$this->reports_model->get_total_count_call();
        $total=0;$profit_total=0;
        if($count_all->num_rows > 0){
            $result=$count_all->result_array();
            $total=$result[0]['count'];
            $profit_total=$result[0]['profit_total'];
        }
	//echo $total_duration_calls."<br>";exit;
        $query = $this->reports_model->get_traffic_list(true, '', '');
            if($this->session->userdata['userlevel_logintype']==-1 ){
		    $to_currency = common_model::$global_config['system_config']['base_currency'];
                }
            else{
                $to_currency1 = $this->session->userdata['accountinfo']['currency_id'];
                $to_currency= $this->common->get_field_name('currency','currency',$to_currency1);
            }
        $first=$query->result_array();
// echo "<pre>";print_r($first);exit;
        $asr_total='0';$aloc_total='0';$sell_total='0';$buy_total='0';$profit_total='0';$margin=0;$margin_total=0;$aloc=0;
        foreach ($first as $key => $value) {
             $first[$key]['answer_call']=0;
            foreach($calls as $ans_key => $ans_val){
                if(date("Y-m-d", strtotime($ans_val['callstart'])) == date("Y-m-d", strtotime($value['callstart']))){
                
                    $first[$key]['answer_call']=$ans_val['answerd_call'];
		}
            }
            
//		$total_duration=$total_duration + $value['duration'];

//                $per=$value['calls'] * 200 / $total;
//                $margin = $value['profit'] * 100 / $profit_total;
//                $asr=  $first[$key]['answer_call'] * 100 / $total_answeredcall;
//                $aloc=$value['duration'] * 100 / $total;
                

                 if($total !=0 ){
                    $per=$value['calls'] * 200 / $total;
                    
                }
//                    $margin = $value['profit'] * 100 / $profit_total;
			@$margin=((($value['sell_rate'] - $value['buy_rate'])/$value['sell_rate'])) * 100;
			@$margin_total=((($sell_total - $buy_total ) / $sell_total)) * 100;
//                if($total_duration!=0){
//		  $aloc=$value['duration'] * 100 / $total_duration;
	$aloc_duration=$value['duration'] / $value['calls'];
	$aloc= sprintf ( "%02d", intval ( $aloc_duration / 60 ) ) . ":" . sprintf ( "%02d", intval ( $aloc_duration % 60 ) );
//	echo $value['duration'] ."-------". $value['calls'];
  //              }
                if($total_answeredcall==0)
                {
                    $asr='0';
                }else{
                    $asr= $first[$key]['answer_call'] * 100 / $total_answeredcall;
                }
//                 echo "<pre>";
//             print_r($first);
//             echo $asr;
//             exit;
                $asr_total=$asr_total+$asr;
                $aloc_total_final=$total_duration/$total_answeredcall;
		$aloc_total= sprintf ( "%02d", intval ( $aloc_total_final / 60 ) ) . ":" . sprintf ( "%02d", intval ( $aloc_total_final % 60 ) );


                $sell_total=$value['sell_rate']+$sell_total;
                $buy_total=$buy_total+$value['buy_rate'];
                $profit_total=$profit_total+$value['profit'];
                //$margin_total=$margin_total+$margin;

//                $total_duration=$total_duration+$value['duration'];
//                     echo $margin."=======".$per."--".$total."-".$value['calls']."----".$value['duration']."--".$total_duration."<br>";
                $toal_per=$json_data['rows'][] = array('cell' => array(
                        $value['callstart'],
                        $this->get_seconds_to_time($value['duration']),
                        '<img alt="progress_animated" src="/assets/images/progress_animated.gif" width="'.$per.'" height="35px;" style="margin-top:-8px;float:left;margin-left:-10px;"></img>',
                        $value['calls'],
                        number_format($asr,2)."%", 
                        $aloc,
                        number_format($value['sell_rate'],2)." ".$to_currency,
                        number_format($value['buy_rate'],2)." ".$to_currency,
                        number_format( $value['profit'],2)." ".$to_currency,
                        number_format($margin,2)."%",
//                        $this->common->convert_to_currency($value['profit'],'','',true,false),
                     ));
        }
		
//		echo "---------".$total_duration;

        @$json_data['rows'][count($json_data['rows'])]=array('cell' => array('<b>Total</b>','<b>'.$this->get_seconds_to_time($total_duration).'</b>','','<b>'.$total.'</b>','<b>'.number_format($asr_total,2)."%</b>",
            '<b>'.($aloc_total),
            '<b>'.$sell_total." ".$to_currency."</b>",'<b>'.$buy_total." ".$to_currency.'</b>','<b>'.$profit_total." ".$to_currency.'</b>',
            '<b>'.number_format($margin_total,2)."%</b>"));
//            echo count($json_data['rows']);echo "<pre>";print_r($json_data);exit;
        echo json_encode($json_data);
    }
    function get_seconds_to_time($sec){
 	  //$sec="0.00";
      // 	  3648
      $sec = ($sec == 0 )? "0.00":floor($sec/60).":".($sec % 60);
      return $sec;
      // 	  $h = floor($sec /3600);
      // 	  $m = floor(($sec - $h *3600) / 60);
      // 	  $s = $sec % 60;
      // 	if($h < 10 )
      //  	      $h="0".$h;
      // 	  if( $m < 10 )
      // 	      $m="0".$m;
      // 	  if($s < 10)
      // 	      $s="0".$s;
      // 	  return $h.":".$m.":".$s;
      }

function customersummary_export_cdr_xls() {
        
$query = $this->reports_model->get_customersummary_report_list(true, '', '', true);
        $customer_array[] = array("Account", "Code", "Destination","Attempted Calls",  "Completed Calls", "ASR","ACD", "MCD","Bilable", "Debit",  "Cost", "Profit");
       
        if ($query->num_rows() > 0) {

             foreach ($query->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                  $billsec = $bill > 0 ? floor($bill/60).":".($bill % 60) : "00:00";
                  $maxsec = $mcd > 0 ? floor($mcd/60).":".($mcd % 60) : "00:00";
                $price = $row1['price'];
                $cost = $row1['cost'];
                $profit = $row1['cost'] - $row1['price'];
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
                $avgsec = $acd > 0 ? floor($acd/60).":".($acd % 60) : "00:00";
                $asr =  ($cmplt/$atmpt)* 100;

                $customer_array[] = array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    $avgsec ,
                     $maxsec,                  
                     $billsec,      
                    $this->common_model->calculate_currency($row1["price"]),                  
                    $this->common_model->calculate_currency($cost),
                    
                    $this->common_model->calculate_currency($profit));
                }}
//echo "<pre>"; print_r($customer_array); exit;
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Customer_Summary_' . date("Y-m-d") . '.csv');
    }

function resellersummary_export_cdr_xls() {
        
$query = $this->reports_model->get_resellersummary_report(true, '', '', true);
        $customer_array[] = array("Account", "Code", "Destination","Attempted Calls",  "Completed Calls", "ASR","ACD", "MCD","Bilable", "Price",  "Cost", "Profit");
       
        if ($query->num_rows() > 0) {

             foreach ($query->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['debit'];
                $cost = $row1['cost'];
                 $profit = $row1['profit'];
                   $profit=$this->common_model->calculate_currency($profit);
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
                 $debit=$this->common_model->calculate_currency($row1["debit"]);
                $cost=$this->common_model->calculate_currency($cost);
                $asr =  ($cmplt/$atmpt)* 100;
	$billsec = $bill > 0 ? floor($bill/60).":".($bill % 60) : "00:00";
		$maxsec = $mcd > 0 ? floor($mcd/60).":".($mcd % 60) : "00:00";
		$avgsec = $acd > 0 ? floor($acd/60).":".($acd % 60) : "00:00";
                $customer_array[] = array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['accountid']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                     round($asr, 2),
                    $avgsec,
                    $maxsec,
                    $billsec,
                    $debit,
                    $cost,
                    $profit);
                   
              
               
                
             
              
             
	
                }}
//echo "<pre>"; print_r($customer_array); exit;
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Reseller_Summary_' . date("Y-m-d") . '.csv');
    }

function providersummary_export_cdr_xls() {
        
$query = $this->reports_model->get_providersummary_report_list(true, '', '', true);
        $customer_array[] = array("Provider", "Code", "Destination","Attempted Calls",  "Completed Calls", "ASR","ACD", "MCD","Bilable" , "Cost");
       
        if ($query->num_rows() > 0) {

             foreach ($query->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['price'];
                $cost = $row1['cost'];
                $cmplt = ($row1['completed'] != 0) ? $row1['completed'] : 0;
                $asr =  ($cmplt/$atmpt)* 100;
//                $profit = $row1['cost'] - $row1['price'];
                $customer_array[] = array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts",$row1['provider_id']),
                    $this->common->get_only_numeric_val("","",$row1["pattern"]),
                    $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    round($acd/60, 2),
                    round($mcd/60, 2),                        
                    round($bill/60, 2),                        
                    $this->common_model->calculate_currency($cost)
//                    $this->common_model->calculate_currency($profit)
                    );

                }}
//echo "<pre>"; print_r($customer_array); exit;
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Provider_Summary_' . date("Y-m-d") . '.csv');
    }




}

?>
 
