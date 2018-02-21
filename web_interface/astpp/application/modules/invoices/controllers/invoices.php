<?php

class Invoices extends MX_Controller {

    function Invoices() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library('invoices_form');
        $this->load->library('astpp/form');
        $this->load->model('invoices_model');
        $this->load->model('Astpp_common');
        $this->load->model('common_model');
        $this->load->library('fpdf');
        $this->load->library('pdf');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function invoice_list() { 
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Invoices List';
        $this->session->set_userdata('advance_search',0);
        $data['grid_fields']= $this->invoices_form->build_invoices_list_for_admin();
        $data["grid_buttons"] = $this->invoices_form->build_grid_buttons();
        $data['form_search']=$this->form->build_serach_form($this->invoices_form->get_invoice_search_form());
        $this->load->view('view_invoices_list',$data);
    }
    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function invoice_list_json() {
	
        $json_data = array();
 
	$count_all = $this->invoices_model->getcharges_list(false);
        
        $paging_data =  $this->form->load_grid_config($count_all,10,1);
        $json_data = $paging_data["json_paging"];
	
        $query = $this->invoices_model->getcharges_list(true,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        
        $grid_fields= json_decode($this->invoices_form->build_invoices_list_for_admin());
        
        
        $json_data['rows'] = $this->form->build_grid($query,$grid_fields);
        
        echo json_encode($json_data);
        
    }
      function invoice_conf() {
          
        $data['page_title'] = 'Invoice Configuration';

//        $invoiceconf = $this->charges_model->get_invoiceconf();
//        $data['invoiceconf'] = $invoiceconf;
        
        if ($this->input->post('action')) {
           $post_array=$this->input->post();
           
           $this->invoices_model->save_invoiceconf($post_array);
            $this->session->set_userdata('astpp_notification', 'Invoice Configuration Updated Sucessfully!');
        }
$invoiceconf=array();
        $invoiceconf = $this->invoices_model->get_invoiceconf();
//        $data['invoiceconf'] = $invoiceconf;

        $data['form']=$this->form->build_form($this->invoices_form->get_invoiceconf_form_fields(),$invoiceconf);
        
        
        $this->load->view('view_invoiceconf', $data);
    }
      function customer_invoices($accountid){
        $json_data = array();
        $where = array('accountid' => $accountid);
        $count_all = $this->db_model->countQuery("*","invoices",$where);
        
        $paging_data =  $this->form->load_grid_config($count_all,10,1);
        $json_data = $paging_data["json_paging"];
	
        $Invoice_grid_data = $this->db_model->select("*","invoices",$where,"invoice_date","desc",$paging_data["paging"]["page_no"],$paging_data["paging"]["start"]);
        $grid_fields= json_decode($this->invoices_form->build_invoices_list_for_admin());
        
        $json_data['rows'] = $this->form->build_grid($Invoice_grid_data,$grid_fields);
        
        echo json_encode($json_data);
    }
    
    /**
     * -------Here we write code for controller accounts functions view_invoice------
     * We fetch invoice detail from CDRS table through Invoice ID
     * @invoiceid: Invoice ID
     */
    function invoice_list_view_invoice($invoiceid=false) {
        
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Invoice Detail';
        
        $cdrs_query = $this->invoices_model->getCdrs_invoice($invoiceid);
        
        $invoice_cdr_list = array();
        $cdr_list = array();
        if ($cdrs_query->num_rows() > 0) {
            foreach ($cdrs_query->result_array() as $cdr) {
                $cdr['charge'] = $this->common_model->calculate_currency($cdr['debit'] - $cdr['credit']);
                array_push($cdr_list, $cdr);
            }
        }
        $data['invoice_cdr_list'] = $cdr_list;

        $invoice_total_query = $this->Astpp_common->get_invoice_total($invoiceid);
        
        $total_list = array();
        $invoice_total_list = array();

        if ($invoice_total_query->num_rows() > 0) {
            foreach ($invoice_total_query->result_array() as $total) {
                array_push($total_list, $total);
            }
        }

        $data['invoice_total_list'] = $total_list;

        $invoicedata = $this->Astpp_common->get_invoice($invoiceid);
               

        $data['invoiceid'] = @$invoicedata[0]['invoiceid'];
        $data['invoicedate'] = @$invoicedata[0]['date'];
        $data['accountid'] = @$invoicedata[0]['accountid'];

//        echo "<pre>";
//        print_r(@$invoicedata);
//        exit;
        if(!empty($invoicedata)){
        $accountinfo = $this->invoices_model->get_account_including_closed(@$invoicedata[0]['accountid']);
        $data['accountinfo'] = $accountinfo;
        }
        //Get invoice header information
        $invoiceconf = $this->invoices_model->get_invoiceconf($accountinfo['reseller']);
        $data['invoiceconf'] = $invoiceconf;
        $this->load->view('view_account_invoice_detail', $data);
    }
    
    
    function invoice_download($invoiceid) { 
        $accountid = $this->common->get_field_name('accountid', 'invoices', $invoiceid);	
        $accountdata = $this->db_model->getSelect("*","accounts",array("id"=>$accountid));
        $accountdata =  $accountdata->result_array();
        $accountdata = $accountdata[0];
        $accountdata["currency_id"] = $this->common->get_field_name('currency', 'currency', $accountdata["currency_id"]);
	$currency = $accountdata["currency_id"];

	
	$cdrs_query = $this->db_model->getSelect("*", "cdrs", array("invoiceid"=> $invoiceid));
        $invoice_cdr_list = array();
        $cdr_list = array();
        if($cdrs_query->num_rows()>0)
        {
            foreach($cdrs_query->result_array() as $cdr)
            {
                $cdr['charge'] = $this->calculate_currency($cdr['debit'] - $cdr['credit'],$accountdata);
                array_push( $cdr_list, $cdr );
            }
        }

        $charge_query = $this->db_model->getSelect("*", "invoice_item", array("invoiceid"=> $invoiceid));
        $charge_list = array();
        if($charge_query->num_rows()>0)
        {
            foreach($charge_query->result_array() as $charge)
            {
                $charge['charge'] = $this->calculate_currency($charge['debit'] - $charge['credit'],$accountdata);
                array_push( $charge_list, $charge );
            }
        }        
        $data['invoice_cdr_list'] = $cdr_list;

        $total_list = array();
        $invoice_total_list = array();

        $invoice_total_query = $this->db_model->getSelect("*", "invoices_total", array("invoiceid"=> $invoiceid));
        if($invoice_total_query->num_rows()>0){
            foreach($invoice_total_query->result_array() as $total) {
                array_push( $total_list, $total );
            }
        }		
        $data['invoice_total_list'] = $total_list;	

        $invoicedata  = $this->db_model->getSelect("*", "invoices", array("id"=> $invoiceid));
        $invoicedata = $invoicedata->result_array();
        
        $data['invoiceid'] = @$invoicedata[0]['id'];
        $data['invoice_date'] = @$invoicedata[0]['invoice_date'];
        $data['accountid'] = @$invoicedata[0]['accountid'];
	
	$data['from_date'] = @$invoicedata[0]['from_date'];
	$data['to_date'] = @$invoicedata[0]['to_date'];

//        $accountinfo = $this->accounts_model->get_account_including_closed( @$invoicedata[0]['accountid'] );
        $data['accountinfo'] = $accountdata;

        //Get invoice header information
//        if($accountdata['reseller_id']=='0')
	      $accountid = '-1';
//        else
//            $accountid = $accountdata['reseller_id'];
        
        $invoiceconf  = $this->db_model->getSelect("*", "invoice_conf", array("accountid"=> $accountid));
        $invoiceconf = $invoiceconf->result_array();
        $data['invoiceconf'] = $invoiceconf[0];

        
        //FOR the header company information
        $result_company[0]['value'] = $data['invoiceconf']['company_name'];						//Company Name
        $result_company[1]['value'] = $data['invoiceconf']['address'];							//Address
        $result_company[2]['value'] = $data['invoiceconf']['city'] . " - " . $data['invoiceconf']['zipcode']; 		//City - Zip
        $result_company[3]['value'] = $data['invoiceconf']['country'];							//Country
        $result_company[4]['value'] = "Phone: " . $data['invoiceconf']['telephone']; 					//Phone
        $result_company[5]['value'] = "Email: " . $data['invoiceconf']['emailaddress']; 				//Fax
        $result_company[6]['value'] = "Web Site: " . $data['invoiceconf']['website']; 					//Website

        //FOR the Customer Address
        $customer_address = "";
        if ( $data['accountinfo']['first_name'] != "")
            $customer_address .= $data['accountinfo']['first_name'] . " ";

        if ( $data['accountinfo']['last_name'] != "")
            $customer_address .= $data['accountinfo']['last_name'] . "\n";
        else
            $customer_address .= "\n";

        if ( $data['accountinfo']['address_1'] != "")
            $customer_address .= $data['accountinfo']['address_1'] . "," .$data['accountinfo']['address_2'] ."," . $data['accountinfo']['address_3'] . "\n";

        if ( $data['accountinfo']['city'] != "")
            $customer_address .= $data['accountinfo']['city'] . "\n";

        if ( $data['accountinfo']['province'] != "")
            $customer_address .= $data['accountinfo']['province'];

        if ( $data['accountinfo']['country_id'] != "")
            $customer_address .= $this->common->get_field_name('country', 'countrycode', $data['accountinfo']['country_id']);            

        if ( $data['accountinfo']['postal_code'] != "")
            $customer_address .= " - " . $data['accountinfo']['postal_code'] . "\n";
        else
            $customer_address .= "\n";

        if ( $data['accountinfo']['telephone_1'] != "")
            $customer_address .= "Phone: " . $data['accountinfo']['telephone_1'] . "," . $data['accountinfo']['telephone_2']. "\n";

        if ( $data['accountinfo']['email'] != "")
            $customer_address .= "Email: " . $data['accountinfo']['email'] . "\n";


        $this->fpdf = new PDF('P','pt');
        $this->fpdf->initialize('P','mm','A4');
        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial','',12);

        $y_axis = 14;
        //Loop For Company Address.
        for ($i = 0; $i < count($result_company); $i++) {
            if ($i == 1) {
                $this->fpdf->SetFont('Arial', '', 8);
            }
            $this->fpdf->Cell(40, 5, $result_company[$i]['value']);
            $this->fpdf->SetXY(10, $y_axis);
            $y_axis +=4;
        }

        //Right header part
        $this->fpdf->SetFont('Arial', '', 18);
        $this->fpdf->SetXY(170, 10);
        $this->fpdf->Cell(40, 10, "INVOICE");

        $this->fpdf->SetFont('Arial', 'B', 8);
        $this->fpdf->SetXY(166, 15);
        $this->fpdf->Cell(40, 10, "Invoice Date");

        $this->fpdf->SetFont('Arial', '', 8);
        $this->fpdf->SetXY(185, 15);
        $this->fpdf->Cell(40, 10, date("Y-m-d",strtotime($data['invoice_date'])));

        //Customer Address.
        $this->fpdf->SetFont('Arial', 'B', 8);
        $this->fpdf->SetXY(10, 50);
        $this->fpdf->SetFillColor(231, 231, 231);
        $this->fpdf->Cell(80, 5, "Bill To:", 1, 1, 'L', true);

        $this->fpdf->SetFont('Arial', '', 9);
        $this->fpdf->SetXY(10, 55);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->Multicell(80, 4, $customer_address, 1, 1, 'L', true);

        //Middle portion.
        //Card Number
        $this->fpdf->SetFont('Arial', 'B', 8);
        $this->fpdf->SetXY(20, 90);
        $this->fpdf->SetFillColor(231, 231, 231);
        $this->fpdf->Cell(45, 6, "Account Number", 1, 1, 'L', true);

        $this->fpdf->SetFont('Arial', '', 8);
        $this->fpdf->SetXY(20, 96);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->Cell(45, 5, $data['accountinfo']['number'], 1, 1, 'L', true);

        //Account Number
        $this->fpdf->SetFont('Arial', 'B', 8);
        $this->fpdf->SetXY(65, 90);
        $this->fpdf->SetFillColor(231, 231, 231);
        $this->fpdf->Cell(40, 6, "Invoice Number", 1, 1, 'L', true);

        $this->fpdf->SetFont('Arial', '', 8);
        $this->fpdf->SetXY(65, 96);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->Cell(40, 5, $invoiceid, 1, 1, 'L', true);

        //Invoice Number
        $this->fpdf->SetFont('Arial', 'B', 8);
        $this->fpdf->SetXY(105, 90);
        $this->fpdf->SetFillColor(231, 231, 231);
        $this->fpdf->Cell(40, 6, "Invoice From", 1, 1, 'L', true);

        $this->fpdf->SetFont('Arial', '', 8);
        $this->fpdf->SetXY(105, 96);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->Cell(40, 5, date("Y-m-d",strtotime($data['from_date'])), 1, 1, 'L', true);

        //Invoice Date
        $this->fpdf->SetFont('Arial', 'B', 8);
        $this->fpdf->SetXY(145, 90);
        $this->fpdf->SetFillColor(231, 231, 231);
        $this->fpdf->Cell(40, 6,"Invoice To", 1, 1, 'L', true);

        $currency = $this->common->get_field_name('currency', 'currency', $data['accountinfo']['currency_id']);
        
        $this->fpdf->SetFont('Arial', '', 8);
        $this->fpdf->SetXY(145, 96);
        $this->fpdf->SetFillColor(255, 255, 255);                
        $this->fpdf->Cell(40, 5,date("Y-m-d",strtotime($data['to_date'])), 1, 1, 'L', true);

// 	echo "<pre>";print_r($charge_list); 
	$y = 115; 
	if(!empty($charge_list)){
            $charges_array = array('did_charge'=>"DID Charges",
                           'post_charge'=>"Post Charge",
                           'monthly_charge'=>"Monthly Charge",
                           'periodic_charge'=>"Periodic Charge",
                           'account_refill'=>"Account Refill");
            /*Invoice Item Table*/
            $this->fpdf->SetFont('Arial', 'B', 8);
	    $this->fpdf->SetXY(10, 110);
            $this->fpdf->SetFillColor(231, 231, 231);
            $this->fpdf->Cell(190, 6, "Invoice Item", 1, 1, 'L', true);


            $this->fpdf->SetFont('Arial', 'B', 8);
            $this->fpdf->SetFillColor(231, 231, 231);
            $this->fpdf->SetXY(10, 115);
            $this->fpdf->Cell(30, 5, "Date & Time", 1, 1, 'L', true);

            $this->fpdf->SetXY(40, 115);
            $this->fpdf->Cell(30, 5, "Description", 1, 1, 'L', true);

            $this->fpdf->SetXY(70, 115);
            $this->fpdf->Cell(30, 5, "Charges Name", 1, 1, 'L', true);

            $this->fpdf->SetXY(100, 115);
            $this->fpdf->Cell(35, 5, "Package Name", 1, 1, 'L', true);

            $this->fpdf->SetXY(135, 115);
            $this->fpdf->Cell(30, 5, "Charge Type", 1, 1, 'L', true);

            $this->fpdf->SetXY(165, 115);
            $this->fpdf->Cell(35, 5, "Charge (in $currency)", 1, 1, 'L', true);

	    $this->fpdf->SetFont('Arial', '', 8);  
            $this->fpdf->tablewidths = array(30,30,30,35, 30, 35);            
            $data_final = array();
	    
            foreach ($charge_list as $charge_key => $charge_value) {
                $charge_name = $this->common->get_field_name('description', 'charges',$charge_value['charge_id']);
                $package_name = $this->common->get_field_name('package_name', 'packages',$charge_value['package_id']);
                $data_final[$charge_key][0] = $charge_value['created_date'];
                $data_final[$charge_key][1] = $charge_value['description'];
                $data_final[$charge_key][2] = ($charge_name)?$charge_name:"";
                $data_final[$charge_key][3] = ($package_name)?$package_name:"";
                $data_final[$charge_key][4] = $charges_array[$charge_value['charge_type']];		
                $data_final[$charge_key][5] = $this->format_currency($this->calculate_currency(($charge_value['charge'] * -1),$accountdata));                
                $y += 8;
            }
            
            //Generating the table of the invoice entures.
            $dimensions = $this->fpdf->morepagestable($data_final, "5");
        }        	
	
	if(!empty($data['invoice_cdr_list'])){
	      $y += 5;
	      $this->fpdf->SetFont('Arial', 'B', 8);
	      $this->fpdf->SetXY(10, $y);
	      $this->fpdf->SetFillColor(231, 231, 231);
	      $this->fpdf->Cell(190, 6, "CDR Records", 1, 1, 'L', true);
		      
	      //Header for the detailed table.
	      $this->fpdf->SetFont('Arial', 'B', 8);
	      $this->fpdf->SetFillColor(231, 231, 231);
	      $this->fpdf->SetXY(10, $y+6);
	      $this->fpdf->Cell(30, 5, "Date & Time", 1, 1, 'L', true);

	      $this->fpdf->SetXY(40, $y+6);
	      $this->fpdf->Cell(30, 5, "Caller*ID", 1, 1, 'L', true);

	      $this->fpdf->SetXY(70, $y+6);
	      $this->fpdf->Cell(30, 5, "Called Number", 1, 1, 'L', true);

	      $this->fpdf->SetXY(100, $y+6);
	      $this->fpdf->Cell(50, 5, "Disposition", 1, 1, 'L', true);

	      $this->fpdf->SetXY(150, $y+6);
	      $this->fpdf->Cell(25, 5, "Duration", 1, 1, 'L', true);

	      $this->fpdf->SetXY(175, $y+6);
	      $this->fpdf->Cell(25, 5, "Charge", 1, 1, 'L', true);

	      $this->fpdf->SetFont('Arial', '', 8);
	      $this->fpdf->SetFillColor(255, 255, 255);

	      $this->fpdf->tablewidths = array(30, 30, 30, 50, 25, 25);
	      $data_final = array();
	      if(isset($data['invoice_cdr_list']) && count($data['invoice_cdr_list']) > 0)
	      {
		  foreach ($data['invoice_cdr_list'] as $key => $value) {
		      $data_final[$key][0] = $value['callstart'];
		      $data_final[$key][1] = $value['callerid'];
		      $data_final[$key][2] = $value['callednum'];
		      $data_final[$key][3] = $value['disposition'];
		      $data_final[$key][4] = $value['billseconds'];		                
		      $data_final[$key][5] = $this->format_currency($this->calculate_currency(($value['charge']),$accountdata));
		  }
	      }

	      //Generating the table of the invoice entures.
	      $dimensions = $this->fpdf->morepagestable($data_final, "5");        
        }
        foreach($data['invoice_total_list'] as $key => $values)
        {
            $data_to_total[$key] = $values;
        }        
        foreach ($data_to_total as $key => $value) {
            $data_final_total[$key][0] = "";
            $data_final_total[$key][1] = "";
            $data_final_total[$key][2] = "";
            $data_final_total[$key][3] = $value['title'];
            $data_final_total[$key][4] = $value['text'];
            $data_final_total[$key][5] = $this->format_currency($value['value'])." ".$currency;
	    $invoice_total = $data_final_total[$key][5];
        }

        //Total list
        $this->fpdf->tablewidths = array(30,30,30,50, 25, 25);
        $dimensions = $this->fpdf->table_total($data_final_total, "5");
        
	

        //To output the file to the folder.
        $download_path = 'invoice_'.date('dmY').".pdf";
        $this->fpdf->Output($download_path, "D");
        
    }
    
    function calculate_currency($amount,$accountdata){
        $base_currency =  $this->db_model->getSelect("value", "system", array("name"=> "base_currency"));
        $base_currency = $base_currency->result_array();
        
        $base_currency = $base_currency[0]["value"];

        $from_currency =  $this->db_model->getSelect("currencyrate", "currency", array("currency"=> $base_currency));
        $from_currency = $from_currency->result_array();
        
        $from_currency = $from_currency[0]["currencyrate"];
	
        $to_currency =  $this->db_model->getSelect("currencyrate", "currency", array("currency"=> $accountdata["currency_id"]));
        $to_currency_arr = $to_currency->result_array();
        $to_currency = $to_currency_arr[0]["currencyrate"];
        
        $cal_amount = ($amount * $to_currency) / $from_currency;
        return $cal_amount;
    }
    
    function format_currency($amount) {
	$dp =  $this->db_model->getSelect("value", "system", array("name"=> "decimalpoints"));
        $dp = $dp->result_array();
        $dp = $dp[0]["value"];
	
        return money_format('%.' . $dp . 'n', $amount);
    }
    
    function date_diff_custom($end='2020-06-09 10:30:00', $out_in_array=false){
        $intervalo = date_diff(date_create(), date_create($end));
        $out = $intervalo->format("Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");
        if(!$out_in_array)
            return $out;
        $a_out = array();
        array_walk(explode(',',$out),
        function($val,$key) use(&$a_out){
            $v=explode(':',$val);
            $a_out[$v[0]] = $v[1];
        });
        return $a_out;
    }
    
}

?>
 