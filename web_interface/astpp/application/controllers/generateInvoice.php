<?php
class GenerateInvoice extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        if(!defined( 'CRON' ) )  
          exit();
        $this->load->model("db_model");
        $this->load->library("astpp/common");
        $this->load->library('fpdf');
        $this->load->library('pdf');
        
    }
    function getInvoiceData(){

        $where = array("posttoexternal"=> 1,"deleted" => "0");
        $query = $this->db_model->getSelect("*", "accounts", $where);
        if($query->num_rows >0){
            $account_data = $query->result_array();
//             print_r($account_data);exit;
            foreach($account_data as $data_key =>$account_value){
		if($this->validate_invoice_date($account_value))
		  $this->process_invoice($account_value);
            }
        }
        exit;
    }
    
    function validate_invoice_date($account_value)
    {
	$day = date('d');
        $rawDate = date("Y-m-d");
        $week = date('N', strtotime($rawDate));
        $last_invoice_date = $this->get_invoice_date($account_value["id"]);
        $last_invoice_date = ($last_invoice_date)?$last_invoice_date:$account_value['creation'];
        $date_diff = $this->date_diff_custom($last_invoice_date,true);
        echo "---------Account ID ".$account_value["id"]."-----------Sweep ID ".$account_value["sweep_id"]."-------Last Invoice Date ".$last_invoice_date."----------\n";
        print_r($date_diff);
        if($account_value["sweep_id"] == 0 && $date_diff['Days'] >= 1)
	    return true;
	elseif($account_value["sweep_id"] == 1 && $date_diff['Days'] >= 7)
	    return true;  
	elseif($account_value["sweep_id"] == 2 && $date_diff['Months'] >= 1)
	    return true;  
	elseif($account_value["sweep_id"] == 3 && $date_diff['Months'] >= 3)
	    return true;  
	elseif($account_value["sweep_id"] == 4 && $date_diff['Months'] >= 6)
	    return true;  
	elseif($account_value["sweep_id"] == 5 && $date_diff['Months'] >= 12)
	    return true;  
        else
	  return false;
    }
    
    function process_invoice($accountdata)
    {
	$start_date = ""; $end_date=""; $sort_order = 1;
        $last_invoice_date = $this->get_invoice_date($accountdata["id"]);
        $start_date = ($last_invoice_date)?$last_invoice_date:$accountdata['creation'];
        $end_date = date("Y-m-d H:i:s");
	$invoice_data_count = $this->count_invoice_data($accountdata['id'],$start_date,$end_date);
	if($invoice_data_count > 0){
	    $invoiceid = $this->create_invoice($accountdata['id'],$start_date,$end_date);
	    $update_cdrs = $this->update_cdrs_data($accountdata['id'],$start_date,$end_date,$invoiceid);
	    $sub_total = $this->count_invoice_subtotal($accountdata['id'],$invoiceid);
	    $sort_order = $this->insert_invoice_total_data($invoiceid,$sub_total,$sort_order);
	    $sort_order = $this->apply_invoice_taxes($invoiceid,$accountdata['id'],$sort_order);
	    $invoice_total = $this->set_invoice_total($invoiceid,$sort_order);
	    $this->download_invoice($invoiceid,$accountdata);
	}
    }
    
   
    function get_invoice_date($accountid){
        $where = array("accountid"=>$accountid);
        $query = $this->db_model->getSelect("invoice_date", "invoices", $where);
        if($query->num_rows >0){
            $invoiceid = $query->result();
            return $invoiceid[0]->invoice_date;
        }
        return false;
    }
    function count_invoice_data($accountid,$start_date="",$end_date=""){
        $cdr_query = ""; $inv_data_query ="";
        $cdr_query = "select count(id) as count from cdrs where accountid = ".$accountid;
        $inv_data_query = "select count(id) as count from invoice_item where accountid=".$accountid;
        
	$cdr_query .= " AND callstart >='".$start_date."' AND callstart <= '".$end_date."' AND invoiceid=0";
	$inv_data_query .= " AND created_date >='".$start_date."' AND created_date <= '".$end_date."'  AND invoiceid=0";
	    
        $query = $cdr_query." UNION ".$inv_data_query;	
        $invoice_data = $this->db->query($query);
        if($invoice_data->num_rows > 0){
            $invoice_data = $invoice_data->result_array();
            foreach($invoice_data as $data_key => $data_value){
                if($data_value['count'] > 0){
                    return $data_value['count'];
                }
            }
        }
        return "0";
    }
    function update_cdrs_data($accountid,$start_date="",$end_date="",$invoiceid){
        $cdr_query = ""; $inv_data_query ="";
        $cdr_query = "Update cdrs SET status = 1, invoiceid = '".$invoiceid."' where accountid = ".$accountid;
        $inv_data_query = "update invoice_item SET invoiceid = '".$invoiceid."' where accountid=".$accountid;
        
	$cdr_query .= " AND callstart >='".$start_date."' AND callstart <= '".$end_date."' AND invoiceid=0";
	$inv_data_query .= " AND created_date >='".$start_date."' AND created_date <= '".$end_date."'  AND invoiceid=0";

        $cdr_data = $this->db->query($cdr_query);
        $invoice_data = $this->db->query($inv_data_query);
        return true;
    }
    
    function create_invoice($accountid,$from_date,$to_date){
        $due_date = date("Y-m-d H:i:s",strtotime($to_date." +1 week"));
        $invoice_data = array("accountid"=>$accountid,"invoice_date"=>$to_date,
                            "from_date"=>$from_date,"to_date"=>$to_date,
                            "due_date"=>$due_date,"paid_status"=>0);
        $this->db->insert("invoices",$invoice_data);
        $invoiceid = $this->db->insert_id();
        return $invoiceid;
    }
    function count_invoice_subtotal($accountid,$invoiceid){
        $total=0;
	
	$subtotal_query = "select SUM(invoice_item.debit) as invoice_debit , SUM(invoice_item.credit) as invoice_credit from invoice_item WHERE invoice_item.invoiceid = ".$invoiceid;
        $subtotal_data = $this->db->query($subtotal_query);
        $subtotal_data = $subtotal_data->result_array();
        foreach($subtotal_data as $subtotal_key =>$subtotal_value){
            //$total = ($subtotal_value['cdrs_debit']+$subtotal_value['cdrs_credit'])-($subtotal_value['cust_debit'] + $subtotal_value['cust_credit']);
	  $total += ($subtotal_value['invoice_debit'] - $subtotal_value['invoice_credit']);
        }
	
	$invoice_item_total = $total; 
	
	$subtotal_query = "select SUM(cdrs.debit) as cdrs_debit , SUM(cdrs.credit) as cdrs_credit  from cdrs WHERE cdrs.invoiceid = ".$invoiceid;		
        $subtotal_data = $this->db->query($subtotal_query);
        $subtotal_data = $subtotal_data->result_array();
        foreach($subtotal_data as $subtotal_key =>$subtotal_value){
            //$total = ($subtotal_value['cdrs_debit']+$subtotal_value['cdrs_credit'])-($subtotal_value['cust_debit'] + $subtotal_value['cust_credit']);
	  $total += $subtotal_value['cdrs_debit'];
        }                        
        return $total;
    }
    
    function insert_invoice_total_data($invoiceid,$sub_total,$sort_order){
        $invoice_total_arr = array("invoiceid"=>$invoiceid,"sort_order"=>$sort_order,
            "value"=>$sub_total, "title"=>"Sub Total","text"=>"Sub Total","class"=>"1");
        $this->db->insert("invoices_total",$invoice_total_arr);
        return $sort_order++;
    }
    
    function apply_invoice_taxes($invoiceid,$accountid,$sort_order){
        $tax_priority="";
        $where = array("accountid"=>$accountid);
        $accounttax_query = $this->db_model->getSelectWithOrder("*", "taxes_to_accounts_view", $where,"ASC","taxes_priority");
        if($accounttax_query->num_rows > 0){
            $accounttax_query = $accounttax_query->result_array();
            foreach($accounttax_query as $tax_key => $tax_value){
                if($tax_value["taxes_priority"] == ""){
                    $tax_priority = $tax_value["taxes_priority"];
                }else if($tax_value["taxes_priority"] > $tax_priority){
                    $query = $this->db_model->getSelect("SUM(value) as total", "invoices_total", array("invoiceid"=> $invoiceid));
                    $query =  $query->result_array();
                    $sub_total = $query["0"]["total"];
                }
                $tax_total = (($sub_total * ( $tax_value['taxes_rate'] / 100 )) + $tax_value['taxes_amount'] );
                $tax_array = array("invoiceid"=>$invoiceid,"title"=>"TAX","text"=>$tax_value['taxes_description'],
                    "tax"=>$tax_value['taxes_rate'],"value"=>$tax_total,"class"=>"2","sort_order"=>$sort_order);
                $this->db->insert("invoices_total",$tax_array);
                $sort_order++;
            }
        }
        return $sort_order;
    }
    function set_invoice_total($invoiceid,$sort_order){
        $query = $this->db_model->getSelect("SUM(value) as total", "invoices_total", array("invoiceid"=> $invoiceid));
        $query =  $query->result_array();
        $sub_total = $query["0"]["total"];
        
        $invoice_total_arr = array("invoiceid"=>$invoiceid,"sort_order"=>$sort_order,
            "value"=>$sub_total,"title"=>"Total","text"=>"Total","class"=>"9");
        $this->db->insert("invoices_total",$invoice_total_arr);
        return true;
    }
    /**
     * -------Here we write code for controller accounts functions download_invoice------
     * Invoice detail in pdf format
     * @invoiceid: Invoice ID
     */
    function download_invoice($invoiceid,$accountdata)
    {
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
        $download_path = "/tmp/".'invoice_'.date('dmY').".pdf";
        $this->fpdf->Output($download_path, "F");
        
        $this->common->mail_to_users("email_new_invoice",$data['accountinfo'],$download_path,$invoice_total);
    }    
    
    function dateDifference($startDate, $endDate)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
            return false;

        $years = date('Y', $endDate) - date('Y', $startDate);
        $endMonth = date('m', $endDate);
        $startMonth = date('m', $startDate);

        // Calculate months
        $months = $endMonth - $startMonth;
//        if ($months <= 0)  {
        if ($months < 0)  {
            $months += 12;
            $years--;
        }
        if ($years < 0)
            return false;

        // Calculate the days
                    $offsets = array();
                    if ($years > 0)
                        $offsets[] = $years . (($years == 1) ? ' year' : ' years');
                    if ($months > 0)
                        $offsets[] = $months . (($months == 1) ? ' month' : ' months');
                    $offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';
//                    $days = $endDate - strtotime($offsets, $startDate);
                    $days = $endDate - $startDate;
                    $days = number_format($days/(3600*24),0); 
//                    $days = date('z', $days);   

        return array($years, $months, $days);
    }     
    function calculate_currency($amount,$accountdata){
        $base_currency =  $this->db_model->getSelect("value", "system", array("name"=> "base_currency"));
        $base_currency = $base_currency->result_array();
        $base_currency = $base_currency[0]["value"];

        $from_currency =  $this->db_model->getSelect("currencyrate", "currency", array("currency"=> $base_currency));
        $from_currency = $from_currency->result_array();
        $from_currency = $from_currency[0]["currencyrate"];

        $to_currency =  $this->db_model->getSelect("currencyrate,currency", "currency", array("id"=> $accountdata["currency_id"]));
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
}?> 
