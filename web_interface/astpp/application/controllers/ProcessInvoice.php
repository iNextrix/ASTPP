<?php

// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
// ASTPP Version 3.0 and above
// License https://www.gnu.org/licenses/agpl-3.0.html
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ##############################################################################
class ProcessInvoice extends MX_Controller
{

    public static $global_config;

    public $Error_flag = false;

    public $CurrentDate = "";

    public $fp = "";

    function __construct()
    {
        parent::__construct();
        $this->load->model("db_model");
        $this->load->library("astpp/common");
        // $this->load->library('html2pdf');
        ini_set("memory_limit", "2048M");
        ini_set("max_execution_time", "259200");
        $this->get_system_config();
        
        $this->fp = fopen("/var/log/astpp/astpp-invoice.log", "a+");
        
        // Set custom current date to generate invoice for specific date.
        $this->CurrentDate = gmdate("Y-m-d H:i:s");
        // $this->CurrentDate = "2017-05-10 00:00:01";
    }

    /*
     * Function to get system configuration array.
     */
    function get_system_config()
    {
        $query = $this->db->get("system");
        $config = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $config[$row['name']] = $row['value'];
        }
        self::$global_config['system_config'] = $config;
    }

    /*
     * Get last invoice date so script will start Invoice generation from that date.
     */
    function GetLastInvoiceDate($AccountData)
    {
        $last_invoice_date = $this->common->get_invoice_date("to_date", $AccountData["id"], $AccountData['reseller_id'], "to_date");
        $last_invoice_date = ($last_invoice_date) ? $last_invoice_date : $AccountData['creation'];
        
        //Samir Doshi : Invoice was generating with static hours, minutes and seconds.
        //$last_invoice_date = date("Y-m-d 00:00:01", strtotime($last_invoice_date));
        $last_invoice_date = gmdate ( "Y-m-d H:i:s", strtotime ( '+1 seconds',strtotime($last_invoice_date)) );
        
        return $last_invoice_date;
    }

    /*
     * Method to process invoices for all type of cycles for postpaid customers.
     */
    function GenerateInvoice()
    {
        if ($this->Error_flag) {
            $this->PrintLogger("::::: INVOICE PROCESS START ::::: \n");
            $this->PrintLogger(":::::" . gmdate("Y-m-d H:i:s") . ":::::\n");
        }
        
        // Fetch postpaid acocunts.
        $where = array(
            "posttoexternal" => 1,
            "deleted" => "0",
            "status" => "0"
        );
        $query = $this->db_model->getSelect("*", "accounts", $where);
        
        // If count > 0
        if ($query->num_rows() > 0) {
            $account_data = $query->result_array();
            
            // Generate invoice process
            foreach ($account_data as $data_key => $AccountData) {
                $StartDate = $this->GetLastInvoiceDate($AccountData);
                $EndDate = gmdate("Y-m-d") . " 23:59:59";
                
                $AccountData['sweep_id'] = (int) $AccountData['sweep_id'];
                
                switch ($AccountData['sweep_id']) {
                    /*
                     * Invoice process for Daily customers.
                     */
                    case 0:
                        if (Strtotime($StartDate) > strtotime($this->CurrentDate)) {
                            $StartDate = date("Y-m-d 00:00:01", strtotime($this->CurrentDate . " - 1 days"));
                        }
                        $EndDate = date("Y-m-d 23:59:59", strtotime($this->CurrentDate . " - 1 days"));
                        $this->Generate_Daily_Invoice($AccountData, $StartDate, $EndDate);
                        break;
                    /*
                     * Invoice process for monthly customers.
                     */
                    case 2:
                        
                        if ($this->Error_flag) {
                            $this->PrintLogger("Current day : " . gmdate("d", strtotime("-1 days")) . " ||| " . $AccountData['invoice_day']);
                        }
                        
                        // $AccountData['invoice_day'] = "01";
                        if (gmdate("d", strtotime("-1 days")) == $AccountData['invoice_day']) {
                            $EndDate = date("Y-m-" . $AccountData['invoice_day'] . " 23:59:59", strtotime($StartDate . " + 1 month"));
                            if (Strtotime($EndDate) > strtotime($this->CurrentDate)) {
                                $EndDate = $this->CurrentDate;
                            }
                            $this->Generate_Monthly_Invoice($AccountData, $StartDate, $EndDate);
                        }
                        break;
                }
                
                // Due date Notifications implementation by Samir
                $account_invoices = $this->db_model->getSelect("*", "invoices", array(
                    "accountid" => $AccountData['id'],
                    "status <>" => "0",
                    "confirm" => "0",
                    "deleted" => "0"                    
                ));
                
                foreach ($account_invoices->result_array() as $invoice_key => $invoice_value) {
                    
                    // print_r($invoice_value);
                    $invoice_paid_amount = 0;
                    $invoice_total_query = $this->db_model->select("sum(debit) as debit,sum(credit) as credit,created_date", "invoice_details", array(
                        "invoiceid" => $invoice_value['id'],
                        "item_type" => "INVPAY"
                    ), "created_date", "DESC", "1", "0");
                    
                    if ($invoice_total_query->num_rows() > 0) {
                        $invoice_total_query = $invoice_total_query->result_array();
                        $invoice_paid_amount = $invoice_total_query[0]['credit'];
                    }
                    
                    // If true then mark invoice as PAID
                    if ($invoice_paid_amount > 0 && $invoice_value['amount'] <= $invoice_paid_amount) {
                        $this->db->where(array(
                            "id" => $invoice_value['id']
                        ));
                        $this->db->update("invoices", array(
                            "status" => 0
                        ));
                    }
                                        
                    $invoiceconf = $this->common->Get_Invoice_configuration($AccountData);
                    $days_difference = floor((strtotime($invoice_value['due_date']) - strtotime($this->CurrentDate)) / (60 * 60 * 24));
                    
                    if ($invoiceconf['invoice_due_notification'] == '1' && ($invoiceconf['notify_before_day'] == $days_difference || $this->CurrentDate == $invoice_value['due_date'])) {                        
                        // echo "Notify customers for due date";
                        if ($invoiceconf['invoice_notification']) {
                            $this->send_email_notification('', '', $AccountData, $invoiceconf, $invoice_value, 'invoice_due_reminder',$days_difference);
                        }
                    }                                      
                }
            }
            

             /*
             * Send an email notification after invoice generation.
             */
            $screen_path = getcwd() . "/cron";
            $screen_filename = "Email_Broadcast_" . strtotime('now');
            $command = "cd " . $screen_path . " && /usr/bin/screen -d -m -S  $screen_filename php cron.php BroadcastEmail";
            // exec($command);
        }
        if ($this->Error_flag) {
            $this->PrintLogger("INVOICE PROCESS END");
            $this->PrintLogger(gmdate("Y-m-d H:i:s"));
        }
        exit();
    }

    /*
     * Process daily invoices.
     */
    function Generate_Daily_Invoice($AccountData, $StartDate, $EndDate)
    {
        if ($this->Error_flag) {
            // Print Start date and End Date
            $this->PrintLogger("PROCESS DIALY INVOICE");
            $this->PrintLogger("START DATE : " . $StartDate . " ::::: END DATE" . $EndDate);
        }
        // Get invoice configuration for white lable invoice gneration.
        $InvoiceConf = $this->common->Get_Invoice_configuration($AccountData);
        
        if ($this->Error_flag) {
            // Print Invoice Configuration which we are going to use in invoice.
            $this->PrintLogger("GET INVOICE CONFIGURATION");
        }
        
        // Get last generated invoice for admin or reseller
        $last_invoice_ID = $this->common->get_invoice_date("invoiceid", "", $AccountData['reseller_id']);
        if ($this->Error_flag) {
            $this->PrintLogger("GET INVOICE DATE " . $last_invoice_ID);
        }
        
        if ($last_invoice_ID && $last_invoice_ID > 0) {
            $last_invoice_ID = ($last_invoice_ID + 1);
            if ($last_invoice_ID < $InvoiceConf['invoice_start_from'])
                $last_invoice_ID = $InvoiceConf['invoice_start_from'];
        } else {
            $last_invoice_ID = $InvoiceConf['invoice_start_from'];
        }
        $last_invoice_ID = str_pad($last_invoice_ID, 6, '0', STR_PAD_LEFT);
        
        // Generate one blank invoice
        $InvocieID = $this->create_invoice($AccountData, $StartDate, $EndDate, $last_invoice_ID, $InvoiceConf);
        // Process other charges and subscription.
        $this->ProcessCharges($AccountData, $StartDate, $EndDate, $InvocieID, $this->CurrentDate);
        
        // Set invoice subtotal.
        $SubTotal = $this->GetSubTotal($AccountData, $StartDate, $EndDate, $InvocieID);
        if ($SubTotal) {
            // Apply taxes on invoice amount.
            $sort_order = $this->common_model->apply_invoice_taxes($InvocieID, $AccountData, $EndDate);
        }
        // set invoice total
        $InvoiceTotal = $this->SetInvoiceTotal($AccountData, $InvocieID);
        $this->download_invoice($InvocieID, $AccountData, $InvoiceConf);
    }

    /*
     * Process monthly invoices.
     */
    function Generate_Monthly_Invoice($AccountData, $StartDate, $EndDate)
    {
        if ($this->Error_flag) {
            // Print Start date and End Date
            $this->PrintLogger("PROCESS DIALY INVOICE");
            $this->PrintLogger("START DATE : " . $StartDate . " ::::: END DATE" . $EndDate);
        }
        // Get invoice configuration for white lable invoice gneration.
        $InvoiceConf = $this->common->Get_Invoice_configuration($AccountData);
        
        if ($this->Error_flag) {
            // Print Invoice Configuration which we are going to use in invoice.
            $this->PrintLogger($InvoiceConf);
        }
        
        // Get last generated invoice for admin or reseller
        $last_invoice_ID = $this->common->get_invoice_date("invoiceid", "", $AccountData['reseller_id']);
        if ($last_invoice_ID && $last_invoice_ID > 0) {
            $last_invoice_ID = ($last_invoice_ID + 1);
            if ($last_invoice_ID < $InvoiceConf['invoice_start_from'])
                $last_invoice_ID = $InvoiceConf['invoice_start_from'];
        } else {
            $last_invoice_ID = $InvoiceConf['invoice_start_from'];
        }
        $last_invoice_ID = str_pad($last_invoice_ID, 6, '0', STR_PAD_LEFT);
        
        // Generate one blank invoice
        $InvocieID = $this->create_invoice($AccountData, $StartDate, $EndDate, $last_invoice_ID, $InvoiceConf);
        // Process other charges and subscription.
        $this->ProcessCharges($AccountData, $StartDate, $EndDate, $InvocieID, $this->CurrentDate);
        // Set invoice subtotal.
        $SubTotal = $this->GetSubTotal($AccountData, $StartDate, $EndDate, $InvocieID);
        if ($SubTotal) {
            // Apply taxes on invoice amount.
            $sort_order = $this->common_model->apply_invoice_taxes($InvocieID, $AccountData, $EndDate);
        }
        // set invoice total
        $InvoiceTotal = $this->SetInvoiceTotal($AccountData, $InvocieID);
        $this->download_invoice($InvocieID, $AccountData, $InvoiceConf);
    }

    /*
     * Create blank invoice at the starting of the process.
     */
    function create_invoice($AccountData, $StartDate, $EndDate, $last_invoice_ID, $InvoiceConf)
    {
        
        // set due date
        if ($InvoiceConf['interval'] > 0) {
            $DueDate = date("Y-m-d 23:59:59", strtotime($this->CurrentDate . " +" . $InvoiceConf['interval'] . " days"));
        } else {
            $DueDate = date("Y-m-d 23:59:59", strtotime($this->CurrentDate . " +7 days"));
        }
        
        $balance = ($AccountData['credit_limit'] - $AccountData['balance']);
        $automatic_flag = self::$global_config['system_config']['automatic_invoice'];
        
        // Automatic flag is use to manage the invoice generate with confirm mode or with out confirm mode.
        if ($automatic_flag == 1) {
            $InvoiceData = array(
                "accountid" => $AccountData['id'],
                "invoice_prefix" => $InvoiceConf['invoice_prefix'],
                "invoiceid" => $last_invoice_ID,
                "reseller_id" => $AccountData['reseller_id'],
                "invoice_date" => $this->CurrentDate,
                "from_date" => $StartDate,
                "to_date" => $EndDate,
                "due_date" => $DueDate,
                "status" => 1,
                "amount" => "0.00",
                "balance" => $balance
            );
        } else {
            $InvoiceData = array(
                "accountid" => $AccountData['id'],
                "invoice_prefix" => $InvoiceConf['invoice_prefix'],
                "invoiceid" => $last_invoice_ID,
                "reseller_id" => $AccountData['reseller_id'],
                "invoice_date" => $this->CurrentDate,
                "from_date" => $StartDate,
                "to_date" => $EndDate,
                "due_date" => $DueDate,
                "status" => 1,
                "amount" => "0.00",
                "balance" => $balance,
                "confirm" => 1
            );
        }
        if ($this->Error_flag) {
            $this->PrintLogger($InvoiceData);
        }
        // Generate insert entry for invoices.
        $this->db->insert("invoices", $InvoiceData);
        $invoiceid = $this->db->insert_id();
        // Return last inserted id of invoice as a invocieid.
        return $invoiceid;
    }

    /*
     * Process subscription and other charges like DIDs etc...
     */
    function ProcessCharges($AccountData, $StartDate, $EndDate, $InvocieID, $NowDate)
    {
        if ($this->Error_flag) {
            $this->PrintLogger("CHARGES CALCULATION PROCESS START");
        }
        
        // Assign invoice id to the post entries which is already inserted from the GUI and
        $inv_data_query = "update invoice_details SET invoiceid = '" . $InvocieID . "'";
        $inv_data_query .= " where created_date >='" . $StartDate . "' AND created_date <= '" . $EndDate . "'  AND invoiceid=0 AND item_type !='PAYMENT'";
        $this->db->query($inv_data_query);
        
        // Reset current date for subscription billing.
        $NowDate = date("Y-m-d 23:59:59", strtotime($NowDate));
        require_once (APPPATH . 'controllers/ProcessCharges.php');
        $ProcessCharges = new ProcessCharges();
        
        // Process subscription charges for postpaid customers.
        $ProcessCharges->process_subscriptions($AccountData, $StartDate, $EndDate, $InvocieID, $NowDate);
        // Process DID charges for postpaid customers.
        $ProcessCharges->process_DID_charges($AccountData, $StartDate, $EndDate, $InvocieID, $NowDate);
        
        if ($this->Error_flag) {
            $this->PrintLogger("CHARGES CALCULATION PROCESS END");
        }
    }

    /*
     * Generate entry in invoice details for CDRs usage and return subtotal of the invoice.
     */
    function GetSubTotal($AccountData, $StartDate, $EndDate, $InvocieID)
    {
        $SubTotal = "0.0000";
        
        // Generate CDRs entry for invoices in invoice detail table.
        $CDRqr = "select calltype,sum(debit) as debit,sum(billseconds) as duration from cdrs where accountid = " . $AccountData['id'] . " AND callstart >='" . $StartDate . "' AND callstart <= '" . $EndDate . "' AND invoiceid=0 group by calltype";
        $this->PrintLogger($CDRqr);
        $CDRdata = $this->db->query($CDRqr);
        if ($CDRdata->num_rows() > 0) {
            $CDRdata = $CDRdata->result_array();
            // echo '<pre>'; print_r($cdr_data); exit;
            foreach ($CDRdata as $CDRvalue) {
                
                // If Call is FREE then forcefully set debit = 0 for invoice.
                if ($CDRvalue['calltype'] == "FREE")
                    $CDRvalue['debit'] = 0;
                
                $tempArr = array(
                    "accountid" => $AccountData['id'],
                    "reseller_id" => $AccountData['reseller_id'],
                    "item_id" => "0",
                    "description" => $CDRvalue['calltype'] . " CALLS FOR PERIOD OF (" . $StartDate . " to " . $EndDate . ")",
                    "debit" => $CDRvalue['debit'],
                    "item_type" => $CDRvalue['calltype'],
                    "created_date" => $this->CurrentDate,
                    "invoiceid" => $InvocieID,
                    "quantity" => $CDRvalue['duration']
                );
                if ($this->Error_flag) {
                    $this->PrintLogger($tempArr);
                }
                $this->db->insert("invoice_details", $tempArr);
            }
        }
        
        // Get the subtotal.
        $Invoicequery = "select count(id) as count,sum(debit) as debit,sum(credit) as credit from invoice_details where accountid=" . $AccountData['id'] . " AND invoiceid =" . $InvocieID . " AND item_type != 'FREE'";
        
        $this->PrintLogger($Invoicequery);
        
        $Invoicequery = $this->db->query($Invoicequery);
        if ($Invoicequery->num_rows() > 0) {
            $InvData = $Invoicequery->result_array();
            if ($this->Error_flag) {
                $this->PrintLogger($InvData);
            }
            foreach ($InvData as $totalvalue) {
                if ($totalvalue['count'] > 0) {
                    $SubTotal = ($totalvalue['debit'] - $totalvalue['credit']);
                    return $SubTotal;
                }
            }
        }
        return $SubTotal;
    }

    /*
     * Set invoice total by excluding FreeCalls.
     */
    function SetInvoiceTotal($AccountData, $InvocieID)
    {
        $query = $this->db_model->getSelect("SUM(debit) as total", "invoice_details", array(
            "invoiceid" => $InvocieID,
            "item_type <>" => "FREECALL"
        ));
        $query = $query->result_array();
        $TotalAmt = $query["0"]["total"];
        
        if ($this->Error_flag) {
            $this->PrintLogger("Invoice Total :::::" . $TotalAmt);
        }
        
        $updateArr = array(
            "amount" => $TotalAmt
        );
        $this->db->where(array(
            "id" => $InvocieID
        ));
        $this->db->update("invoices", $updateArr);
        
        return true;
    }

    /*
     * Invoice Download code which generate invoice PDF.
     */
    function download_invoice($InvocieID, $AccountData, $InvoiceConf)
    {
        $InvoiceData = $this->db_model->getSelect("*", "invoices", array(
            "id" => $InvocieID
        ));
        $InvoiceData = $InvoiceData->result_array();
        $InvoiceData = $InvoiceData[0];
        $FilePath = FCPATH . "invoices/" . $AccountData["id"] . '/' . $InvoiceData['invoice_prefix'] . "" . $InvoiceData['invoiceid'] . ".pdf";
        $Filenm = $InvoiceData['invoice_prefix'] . $InvoiceData['invoiceid'] . ".pdf";
        
        $this->common->get_invoice_template($InvoiceData, $AccountData, false, true);
        if ($InvoiceConf['invoice_notification']) {
            $this->send_email_notification($FilePath, $Filenm, $AccountData, $InvoiceConf, $InvoiceData);
        }
    }

    /*
     * Send an email notification methid which log the invoice email notification in mail details table.
     */
    function send_email_notification($FilePath = '', $Filenm = '', $AccountData, $invoice_conf, $invData, $email_template = 'email_new_invoice',$days_difference = '')
    {
        $TemplateData = array();
        $where = array(
            'name' => $email_template
        );
        $EmailTemplate = $this->db_model->getSelect("*", "default_templates", $where);
        foreach ($EmailTemplate->result_array() as $TemplateVal) {
            $TemplateData = $TemplateVal;
            $TemplateData['subject'] = str_replace('#NAME#', $AccountData['first_name'] . " " . $AccountData['last_name'], $TemplateData['subject']);
            $TemplateData['subject'] = str_replace('#INVOICE_NUMBER#', $invData['invoice_prefix'] . $invData['invoiceid'], $TemplateData['subject']);
            
            $TemplateData['template'] = str_replace('#NAME#', $AccountData['first_name'] . " " . $AccountData['last_name'], $TemplateData['template']);
            $TemplateData['template'] = str_replace('#INVOICE_NUMBER#', $invData['invoice_prefix'] . $invData['invoiceid'], $TemplateData['template']);
            
            // @TODO : Currency conversion pending
            $TemplateData['template'] = str_replace('#AMOUNT#', $invData['amount'], $TemplateData['template']);
            
            $TemplateData['template'] = str_replace("#COMPANY_EMAIL#", $invoice_conf['emailaddress'], $TemplateData['template']);
            $TemplateData['template'] = str_replace("#COMPANY_NAME#", $invoice_conf['company_name'], $TemplateData['template']);
            $TemplateData['template'] = str_replace("#COMPANY_WEBSITE#", $invoice_conf['website'], $TemplateData['template']);
            
            // @TODO : Timezone conversion pending
            $TemplateData['template'] = str_replace("#INVOICE_DATE#", $invData['invoice_date'], $TemplateData['template']);
            
            if ($days_difference == 0)
                $invData['due_date'] = "Today";
            $TemplateData['template'] = str_replace("#DUE_DATE#", $invData['due_date'], $TemplateData['template']);
        }
        
        if ($Filenm != '') {
            $dir_path = getcwd() . "/attachments/";
            $path = $dir_path . $Filenm;
            $command = "cp " . $FilePath . " " . $path;
            exec($command);
        }
        $email_array = array(
            'accountid' => $AccountData['id'],
            'subject' => $TemplateData['subject'],
            'body' => $TemplateData['template'],
            'from' => $invoice_conf['emailaddress'],
            'to' => $AccountData['email'],
            'status' => "1",
            'attachment' => $Filenm,
            'template' => ''
        );
        // echo "<pre>"; print_r($TemplateData); exit;
        $this->db->insert("mail_details", $email_array);
    }

    function PrintLogger($Message)
    {
        if (is_array($Message)) {
            foreach ($Message as $MessageKey => $MessageValue) {
                if (is_array($MessageValue)) {
                    foreach ($MessageValue as $LogKey => $LogValue) {
                        fwrite($this->fp, "::::: " . $LogKey . " ::::: " . $LogValue . " :::::\n");
                    }
                } else {
                    fwrite($this->fp, "::::: " . $MessageKey . " ::::: " . $MessageValue . " :::::\n");
                }
            }
        } else {
            if ($this->Error_flag) {
                fwrite($this->fp, "::::: " . $Message . " :::::\n");
            }
        }
    }
}

?>