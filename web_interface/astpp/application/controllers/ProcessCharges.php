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
class ProcessCharges extends MX_Controller {
	public $Error_flag = false;
	public $currentdate = "";
	//public $fp = "";
	function __construct() {
		parent::__construct ();
		$this->load->model ( "db_model" );
		$this->load->model ( "common_model" );
		$this->load->library ( "astpp/common" );
		
		// Set custom current date to generate invoice for specific date.
		$this->currentdate = gmdate ( "Y-m-d H:i:s" );
		// $this->currentdate = "2016-02-15 00:00:01";
		
		//$this->fp = fopen ( "/tmp/astpp-invoice.log", "a+" );
	}
	
	/*
	 * GetUpdateBalance method use to process prepaid customer's charges.
	 * This method we directly call from cron script and bill the services and charges which is associated with prepaid customers.
	 */
	function GetUpdateBalance() {
		$Accounts = $this->get_table_data ( "*", "accounts", array (
				"posttoexternal" => 0,
				"status" => "0",
				"deleted" => "0" 
		) );
		foreach ( $Accounts as $AccountData ) {
			$this->process_subscriptions ( $AccountData, $this->currentdate, $this->currentdate, 0, $this->currentdate );
			$this->process_DID_charges ( $AccountData, $this->currentdate, $this->currentdate, 0, $this->currentdate );
		}
	}
	
	/*
	 * BillAccountCharges is custom method which we use to manage instant billing for any type of services at the time of delete or release the services before the invoice process for prepaid or postpaid both type of customers.
	 * We define different different cases to manage it you can add it more if needed.
	 */
	function BillAccountCharges($BillType, $params) {
	    	   
		switch ($BillType) {
			
			case "SUBSCRIPTION" :
				if (is_array ( $params )) {
					$this->ProcessDailyCharges ( true, $params ['ChargeID'] );
				} else {
					$this->ProcessDailyCharges ( true, $params );
				}
				break;
			case "ACCOUNTSUBSCRIPTION" :
				$AccountData = $params ['AccountInfo'];
				$LastBillDate = $this->common->get_field_name ( 'charge_upto', 'charge_to_account', array (
						'accountid' => $AccountData ['id'],
						'charge_id' => $params ['ChargeID'] 
				) );
				if ($LastBillDate == '0000-00-00 00:00:00' || $LastBillDate == '') {
					$LastBillDate = $this->common->get_field_name ( 'assign_date', 'charge_to_account', array (
							'accountid' => $AccountData ['id'],
							'charge_id' => $params ['ChargeID'] 
					) );
				}
				if ($AccountData ['sweep_id'] == 0) {
					$end_date = date ( "Y-m-d 23:59:59", strtotime ( $LastBillDate ) );
				} else {
					$end_date = date ( "Y-m-" . $AccountData ['invoice_day'] . " 23:59:59", strtotime ( $LastBillDate . " + 1 month" ) );
					$inv_date = date ( "Y-m-" . $AccountData ['invoice_day'] . " 23:59:59", strtotime ( $LastBillDate ) );
					if (strtotime ( $inv_date ) > strtotime ( date ( "Y-m-d 23:59:59", strtotime ( $this->currentdate ) ) )) {
						$end_date = $inv_date;
					}
				}
				$this->process_subscriptions ( $AccountData, $LastBillDate, $end_date, 0, $this->currentdate );
				break;
			case "DIDs" :
				if (! is_array ( $params )) {
					$params ['DIDid'] = $params;
				}
				$DIDData = $this->get_table_data ( "*", "dids", array (
						"id" => $params ['DIDid'] 
				) );
				$DIDData = $DIDData [0];
				if ($DIDData ['charge_upto'] == '0000-00-00 00:00:00') {
					$DIDData ['charge_upto'] = $DIDData ['assign_date'];
				}
				$LastBillDate = $DIDData ['charge_upto'];
				
				$AccountData = $this->get_table_data ( "*", "accounts", array (
						"id" => $DIDData ['accountid'] 
				) );
				$AccountData = $AccountData [0];
				$AccountData ['DIDid'] = $params ['DIDid'];
				if ($AccountData ['sweep_id'] == 0) {
					$end_date = date ( "Y-m-d 23:59:59", strtotime ( $LastBillDate ) );
				} else {
					$end_date = date ( "Y-m-" . $AccountData ['invoice_day'] . " 23:59:59", strtotime ( $LastBillDate . " + 1 month" ) );
					$inv_date = date ( "Y-m-" . $AccountData ['invoice_day'] . " 23:59:59", strtotime ( $LastBillDate ) );
					/*
					 * if(strtotime($inv_date) > strtotime(date("Y-m-d 23:59:59",strtotime($this->currentdate)))){
					 * $end_date = $inv_date;
					 * }
					 */
				}
				$this->process_DID_charges ( $AccountData, $LastBillDate, $this->currentdate, 0, $this->currentdate, true );
				break;
		}
	}
	/*
	 * ProcessDailyCharges method use to manage daily subscription for any type of customer (postpaid or prepaid both)
	 */
	function ProcessDailyCharges($ManualFlag = false, $ChargeID = 0) {
		if ($ManualFlag) {
			$ChargeData = $this->get_table_data ( "*", "charges", array (
					"id" => $ChargeID 
			) );
		} else {
			$ChargeData = $this->get_table_data ( "*", "charges", array (
					"sweep_id" => "0" 
			) );
		}
		if ($ChargeData && ! empty ( $ChargeData )) {
			foreach ( $ChargeData as $ChargeKey => $chargeValue ) {
				$AccountCharges = $this->Get_account_charges ( $chargeValue, $this->currentdate, true );
				if ($AccountCharges->num_rows () > 0) {
					$AccountCharges = $AccountCharges->result_array ();
					foreach ( $AccountCharges as $AccChargeValue ) {
						if ($AccChargeValue ['charge_upto'] == '0000-00-00 00:00:00') {
							$AccChargeValue ['charge_upto'] = $AccChargeValue ['assign_date'];
						}
						$last_invoice_date = false;
						$AccountData = $this->get_table_data ( "*", "accounts", array (
								"id" => $AccChargeValue ['accountid'] 
						) );
						$AccountData = $AccountData [0];
						$AccountData ['sweep_id'] = $chargeValue ["sweep_id"];
						$itemArr = $this->Build_ItemArr ( $chargeValue ['description'], $chargeValue ['id'], "SUBCHRG", $chargeValue ["sweep_id"], $AccChargeValue ['assign_date'], $AccChargeValue ['charge_upto'], $this->currentdate );
						
						if ($AccountData ["posttoexternal"] == 0) {
							$ChargeData ['charge_upto'] = $AccChargeValue ['charge_upto'];
							$ChargeData ['charge'] = $chargeValue ['charge'];
							$last_invoice_date = $this->process_prepaid_customer ( $AccountData, $itemArr, $ChargeData, $this->currentdate );
						} else {
							$last_invoice_date = $this->calculate_charges ( $AccountData, 0, $itemArr, $chargeValue ["charge"], $AccChargeValue ['charge_upto'], $this->currentdate, $chargeValue ["pro_rate"] );
						}
						// update last billing date to appropreate field.
						if ($last_invoice_date) {
							$this->db->update ( "charge_to_account", array (
									"charge_upto" => $last_invoice_date 
							), array (
									"charge_id" => $chargeValue ["id"],
									"accountid" => $AccountData ["id"] 
							) );
						}
					}
				}
			}
		}
		if (! $ManualFlag) {
			exit ();
		}
	}
	
	/*
	 * process_subscriptions method use to manage subscription charges associated with accounts.
	 */
	function process_subscriptions($AccountData, $StartDate, $EndDate, $InvocieID, $NowDate) {
		if ($this->Error_flag) {
			echo ":::::::::::::::::::::::: SUBSCRIPTION BILLING START ::::::::::::::::::::::::\n";
		}
		
		// Get account charges.
		$AccountCharges = $this->Get_account_charges ( $AccountData, $NowDate );
		if ($AccountCharges->num_rows () > 0) {
			$AccountCharges = $AccountCharges->result_array ();
			
			foreach ( $AccountCharges as $AccChargeValue ) {
				if ($this->Error_flag) {
					echo "<pre>";
					print_r ( $AccChargeValue );
				}
				// Get information from database of active charges.
				$ChargeData = $this->get_table_data ( "*", "charges", array (
						"id" => $AccChargeValue ['charge_id'],
						"status" => "0" 
				) );
				if ($ChargeData && ! empty ( $ChargeData )) {
					$ChargeData = $ChargeData [0];
					if ($AccChargeValue ['charge_upto'] == '0000-00-00 00:00:00') {
						$AccChargeValue ['charge_upto'] = $AccChargeValue ['assign_date'];
					}
					// build an array for further process.
					$itemArr = $this->Build_ItemArr ( $ChargeData ['description'], $ChargeData ['id'], "SUBCHRG", $ChargeData ["sweep_id"], $AccChargeValue ['assign_date'], $AccChargeValue ['charge_upto'], $NowDate );
					// Check account type is postpaid or prepaid.
					if ($AccountData ["posttoexternal"] == 0) {
						$ChargeData ['charge_upto'] = $AccChargeValue ['charge_upto'];
						$ChargeData ['charge'] = $ChargeData ['charge'];
						// If account is prepaid then process it using this method.
						$last_invoice_date = $this->process_prepaid_customer ( $AccountData, $itemArr, $ChargeData, $NowDate );
					} else {
						// If account type is postpaid.
						$last_invoice_date = $this->calculate_charges ( $AccountData, $InvocieID, $itemArr, $ChargeData ["charge"], $StartDate, $EndDate, $ChargeData ["pro_rate"] );
					}
					// update last billing date to appropreate field.
					if ($last_invoice_date) {
						$this->db->update ( "charge_to_account", array (
								"charge_upto" => $last_invoice_date 
						), array (
								"charge_id" => $ChargeData ["id"],
								"accountid" => $AccountData ["id"] 
						) );
					}
				}
			}
		}
		if ($this->Error_flag) {
			echo ":::::::::::::::::::::::: SUBSCRIPTION BILLING END ::::::::::::::::::::::::\n";
		}
	}
	/*
	 * process_subscriptions method use to manage subscription charges associated with accounts.
	 */
	function process_DID_charges($AccountData, $StartDate, $EndDate, $InvocieID, $NowDate, $ManualFlag = false) {
		if ($this->Error_flag) {
			echo ":::::::::::::::::::::::: DIDs BILLING START ::::::::::::::::::::::::\n";
		}
		if ($ManualFlag) {
			$DIDsData = $this->get_table_data ( "*", "dids", array (
					"status" => "0",
					"accountid " => $AccountData ["id"],
					"id" => $AccountData ["DIDid"] 
			) );
		} else {
			$DIDsData = $this->get_table_data ( "*", "dids", array (
					"status" => "0",
					"accountid " => $AccountData ["id"] 
			) );
		}
		$AccountData ['sweep_id'] = '2';
		if ($DIDsData) {
			foreach ( $DIDsData as $DIDvalue ) {
				if ($DIDvalue ['charge_upto'] == '0000-00-00 00:00:00') {
					$DIDvalue ['charge_upto'] = $DIDvalue ['assign_date'];
				}
//ASTPP_DID_monthly_billing_issue
				$next_invoice_date = date("Y-m-d H:i:s",strtotime($DIDvalue ['charge_upto']." + 1 month"));
				$next_invoice_date = date("Y-m-d 23:59:59",strtotime($next_invoice_date." - 1 day"));
				if ($DIDvalue ['charge_upto'] != "0000-00-00 00:00:00" && strtotime ( $next_invoice_date ) < strtotime ( $EndDate )) {
//END
					$itemArr = $this->Build_ItemArr ( $DIDvalue ['number'], $DIDvalue ['id'], "DIDCHRG", "2", $DIDvalue ['assign_date'], $DIDvalue ['charge_upto'], $NowDate );
					if ($DIDvalue ['parent_id'] > 0) {
						$ParentID = $DIDvalue ['parent_id'];
						// If DID purchase by reseller then do reseller billing.
						while ( $ParentID != 0 ) {
							if ($this->Error_flag) {
								echo ":::::::::::::::::::::::: Reseller DIDs BILLING START " . $ParentID . "::::::::::::::::::::::::\n";
							}
							// Get Reseller Account Details
							$reseller_dids = $this->get_table_data ( "*", "reseller_pricing", array (
									"reseller_id" => $ParentID,
									"did_id" => $DIDvalue ["id"] 
							) );
							$Reseller_Account_Data = $this->get_table_data ( "*", "accounts", array (
									"id" => $ParentID 
							) );
							$Reseller_Account_Data = $Reseller_Account_Data ['0'];
							$Reseller_Account_Data ['sweep_id'] = '2';
							$reseller_dids = $reseller_dids [0];
							
							if ($AccountData ["posttoexternal"] == 0) {
								$ChargeData ['charge_upto'] = $DIDvalue ['charge_upto'];
								$ChargeData ['charge'] = $DIDvalue ['monthlycost'];
								$last_invoice_date = $this->process_prepaid_customer ( $AccountData, $itemArr, $ChargeData, $NowDate );
							} else {
								// Apply charges to Resellers.
								$last_invoice_date = $this->calculate_charges ( $Reseller_Account_Data, $InvocieID, $itemArr, $reseller_dids ["monthlycost"], $StartDate, $EndDate, "1" );
							}
							// Update last billing date for users DID.
							if ($last_invoice_date)
								$this->db->update ( "reseller_pricing", array (
										"charge_upto" => $last_invoice_date 
								), array (
										"did_id" => $DIDvalue ["id"],
										"reseller_id" => $Reseller_Account_Data ["id"] 
								) );
							
							$ParentID = $reseller_dids ['parent_id'];
							if ($this->Error_flag) {
								echo ":::::::::::::::::::::::: Reseller DIDs BILLING END ::::::::::::::::::::::::\n";
							}
						}
					}
					
					// Apply charges for DID usage.
					$last_invoice_date = $this->calculate_charges ( $AccountData, $InvocieID, $itemArr, $DIDvalue ["monthlycost"], $StartDate, $EndDate, "1" );
					if ($last_invoice_date)
						// Update last billing date for users DID.
						$this->db->update ( "dids", array (
								"charge_upto" => $last_invoice_date 
						), array (
								"id" => $DIDvalue ["id"],
								"accountid" => $AccountData ["id"] 
						) );
				}
			}
		}
		if ($this->Error_flag) {
		    echo ":::::::::::::::::::::::: DIDs BILLING END ::::::::::::::::::::::::\n";
		}
	}
	function Get_Date_Range_Array($billing_cycle, $itemArr, $StartDate, $EndDate) {
		$last_invoice_date = $itemArr ['LastBillDate'];
		
		$DateRangArr = array ();
		while ( strtotime ( date ( "Y-m-d", strtotime ( $last_invoice_date ) ) ) < strtotime ( date ( "Y-m-d", strtotime ( $EndDate . " -1 day" ) ) ) ) {
			$startdate = $last_invoice_date;
			$temp_last_date = $last_invoice_date;
			$last_invoice_date = date ( "Y-m-d 23:59:59", strtotime ( "+" . $billing_cycle, strtotime ( $last_invoice_date ) ) );
			if (strtotime ( $last_invoice_date ) > strtotime ( $EndDate )) {
				$last_invoice_date = $EndDate;
			}
			
			if ($this->Error_flag) {
				echo "Method : calculate_charges ::::::::::::::" . $startdate . " ::::::::::::::" . $last_invoice_date . "\n";
			}
			// check if billing cycle is daily then set date for the same day.
			if ($billing_cycle == "1 day") {
				$DateRangArr [] = array (
						"StartDate" => date ( "Y-m-d 00:00:01", strtotime ( $startdate ) ),
						"EndDate" => $temp_last_date 
				);
			} else {
				$DateRangArr [] = array (
						"StartDate" => date ( "Y-m-d 00:00:01", strtotime ( $startdate ) ),
						"EndDate" => $last_invoice_date 
				);
			}
		}
		return $DateRangArr;
	}
	
	// Calculate charges for the specific date range for defined services and log the entries in table.
	function calculate_charges($AccountData, $InvocieID, $itemArr, $Charge, $StartDate, $EndDate, $pro_rate = "1") {
	    
		if ($this->Error_flag) {
			echo "<pre> :::::::::::::::::::::::: Item Array :::::::::::::::::::::::: \n";
			print_r ( $itemArr );
		}
		
		$billing_cycle = ($itemArr ['BillCycle'] == "0") ? "1 day" : "1 month";
		$last_invoice_date = $itemArr ['LastBillDate'];
		
		echo "Last Invoice date : ".$last_invoice_date." || Billing Cycle : ".$billing_cycle;
		
		// get array between start and end date based on the billing cycle.
		$DateRangArr = $this->Get_Date_Range_Array ( $billing_cycle, $itemArr, $StartDate, $EndDate );
		if (! empty ( $DateRangArr )) {
			if ($this->Error_flag) {
				echo "<pre> :::::::: Date Range Array :::::::: \n";
				print_r ( $DateRangArr );
			}
			$daylen = 60 * 60 * 24;
			foreach ( $DateRangArr as $DateValue ) {
				
				$last_invoice_date = $DateValue ['EndDate'];
				$start_date_str_time = strtotime ( $DateValue ['StartDate'] );
				$end_date_str_time = strtotime ( $DateValue ['EndDate'] );
				
				$Temp_End_Date = new DateTime ( gmdate ( "Y-m-d H:i:s", strtotime ( "+1 second", strtotime ( $DateValue ['EndDate'] ) ) ) );
				$Temp_Start_Date = new DateTime ( $DateValue ['StartDate'] );
				$diff = $Temp_End_Date->diff ( $Temp_Start_Date );
				
				$hours = $diff->h;
				$hours = $hours + ($diff->days * 24);
				
				$days_diff = 0;
				$month = (($diff->format ( '%y' ) * 12) + $diff->format ( '%m' ));
				$total_num_of_day = cal_days_in_month ( CAL_GREGORIAN, date ( 'm', $start_date_str_time ), date ( 'Y', $start_date_str_time ) );
				
				// default we assing product charge to temp charges for the billing.
				$temp_charge = $Charge;
				
				// if bill cycle is daily and the difference between start and end date is more then 23 hours then cosider full day.
				if ($itemArr ['BillCycle'] == "0" && $hours >= 23) {
					$temp_charge = $Charge;
				} else {
					// If bill cycle is monthly and prorate is enable then we charge to customer based on usage day.
					if ($itemArr ['BillCycle'] == "2" && $pro_rate == "0") {
						$days_diff = floor ( ($end_date_str_time - $start_date_str_time) / $daylen );
						$charge_per_day = ($Charge / $total_num_of_day);
						$temp_charge = ($charge_per_day * $days_diff);
						if ($this->Error_flag) {
							echo "Actual Charge : " . $Charge . " Charge Per Day : " . $charge_per_day . " Charge for Range : " . $temp_charge . "\n";
						}
					}
				}
				
				
				if ($this->Error_flag) {
				    echo ( "<pre> Month difference between the date : " . $month . " Days are " . $days_diff . " Total No Of Days " . $total_num_of_day . "\n");
				}
				// If product charges more then 0 then we log entries in database.
				if ($temp_charge > 0) {
					$this->Manage_invoice_item ( $AccountData, $InvocieID, $itemArr, $temp_charge, $DateValue ['StartDate'], $DateValue ['EndDate'], $EndDate );
				}
			}
		}
		
		echo ( "Last Invoice id : ".$last_invoice_date);
		
		return $last_invoice_date;
	}
	
	// Manage invoice detail data for postpaid and prepaid customers.
	function Manage_invoice_item($AccountData, $InvocieID, $itemArr, $charge, $ItemStartDate, $ItemEndDate, $InvoiceEndDate) {
		$AccountData ["balance"] = $this->common->get_field_name ( 'balance', 'accounts', $AccountData ['id'] );
		$Balance = ($AccountData ["credit_limit"] - $AccountData ["balance"]);
		
		// If account type is prepaid then we generate receitp and manage data.
		if ($AccountData ["posttoexternal"] == 0) {
			// Do code for prepaid customers.
			$Balance = $AccountData ["balance"];
			
			$InvoiceConf = $this->common->Get_Invoice_configuration ( $AccountData );
			$last_invoice_ID = $this->common->get_invoice_date ( "invoiceid", "", $AccountData ['reseller_id'] );
			if ($last_invoice_ID && $last_invoice_ID > 0) {
				$last_invoice_ID = ($last_invoice_ID + 1);
			} else {
				$last_invoice_ID = $InvoiceConf ['invoice_start_from'];
			}
			$DueDate = date ( "Y-m-d 23:59:59", strtotime ( $this->currentdate . " +" . $InvoiceConf ['interval'] . " days" ) );
			$InvoiceData = array (
					"accountid" => $AccountData ['id'],
					"invoice_prefix" => $InvoiceConf ['invoice_prefix'],
					"invoiceid" => $last_invoice_ID,
					"reseller_id" => $AccountData ['reseller_id'],
					"invoice_date" => $this->currentdate,
					"from_date" => $ItemStartDate,
					"to_date" => $ItemEndDate,
					"due_date" => $DueDate,
					"status" => 1,
					"amount" => "0.00",
					"balance" => $Balance,
					"confirm" => 1,
					"type" => "R" 
			);
			
			$this->db->insert ( "invoices", $InvoiceData );
			$InvocieID = $this->db->insert_id ();
		} else {
			// Balance calculation for postpaid customers.
			$Balance = ($AccountData ["credit_limit"] - $AccountData ["balance"]);
		}
		// Update account balance.
		if ($charge > 0) {
			if ($AccountData ["posttoexternal"] == 0) {
				$this->db->set ( 'balance', "(balance - " . $charge . ")", FALSE );
			} else {
				$this->db->set ( 'balance', "(balance + " . $charge . ")", FALSE );
			}
			$this->db->where ( 'id', $AccountData ["id"] );
			$this->db->update ( "accounts" );
		}
		// Log detail entries of invoice item in invoice item table.
		$InvoiceItemArr = array (
				"accountid" => $AccountData ["id"],
				"reseller_id" => $AccountData ["reseller_id"],
				"description" => trim ( $itemArr ["Description"] . "-" . $ItemStartDate . " to " . $ItemEndDate ),
				"item_id" => $itemArr ["ItemID"],
				"debit" => $charge,
				"invoiceid" => $InvocieID,
				"created_date" => $itemArr ["CurrentDate"],
				"item_type" => $itemArr ["Type"],
				"before_balance" => $AccountData ["balance"],
				"after_balance" => ($AccountData ["balance"] - $charge) 
		);
		if ($this->Error_flag) {
			echo "<pre> ::::::::::: Invoice Detail Array ::::::::::: \n";
			print_r ( $InvoiceItemArr );
		}
		$this->db->insert ( "invoice_details", $InvoiceItemArr );
		
		// Suppose account is prepaid then process account taxes and set total of invoice.
		if ($AccountData ["posttoexternal"] == 0) {
			if ($charge) {
				$sort_order = $this->common_model->apply_invoice_taxes ( $InvocieID, $AccountData, $ItemEndDate );
			}
			$InvoiceTotal = $this->SetInvoiceTotal ( $InvocieID );
		}
	}
	// Set invoice total
	function SetInvoiceTotal($InvocieID) {
		$query = $this->db_model->getSelect ( "SUM(debit) as total", "invoice_details", array (
				"invoiceid" => $InvocieID,
				"item_type <>" => "FREECALL" 
		) );
		$query = $query->result_array ();
		$TotalAmt = $query ["0"] ["total"];
		
		if ($this->Error_flag) {
			echo "<pre> ::::::::::: Invoice Total ::::::::::: " . $TotalAmt . "\n";
		}
		
		$updateArr = array (
				"amount" => $TotalAmt 
		);
		$this->db->where ( array (
				"id" => $InvocieID 
		) );
		$this->db->update ( "invoices", $updateArr );
		
		return true;
	}
	function process_prepaid_customer($AccountData, $itemArr, $ChargeData, $NowDate) {
		
		// $InvocieID = $this->Generate_Receipt();
		$billing_cycle = ($itemArr ['BillCycle'] == "0") ? "1 day" : "1 month";
		$ItemStartDate = $ChargeData ['charge_upto'];
		$ItemEndDate = date ( "Y-m-d 23:59:59", strtotime ( $ChargeData ['charge_upto'] . " + " . $billing_cycle ) );
		$this->Manage_invoice_item ( $AccountData, '0', $itemArr, $ChargeData ["charge"], $ItemStartDate, $ItemEndDate, $NowDate );
		
		return $ItemEndDate;
	}
	function get_table_data($select, $table, $where) {
		$query = $this->db_model->getSelect ( $select, $table, $where );
		if ($query->num_rows () > 0) {
			$query_result = $query->result_array ();
			return $query_result;
		} else {
			return false;
		}
	}
	function Get_account_charges($AccountData, $NowDate, $flag = false) {
		if ($flag) {
			$this->db->where ( 'charge_id', $AccountData ['id'] );
		} else {
			$this->db->where ( 'accountid', $AccountData ['id'] );
		}
		$this->db->where ( "(charge_upto ='0000-00-00 00:00:00' OR charge_upto <='" . $NowDate . "')" );
		$this->db->select ( '*' );
		$data = $this->db->get ( 'charge_to_account' );
		// echo $this->db->last_query(); exit;
		return $data;
	}
	
	// Build an array for different services.
	function Build_ItemArr($description, $ItemID, $Type, $Cycle, $AssignDate, $BillDate, $NowDate) {
		$ItemArr = array (
				'Description' => $description,
				"ItemID" => $ItemID,
				"Type" => $Type,
				"BillCycle" => $Cycle,
				"AssignDate" => $AssignDate,
				"LastBillDate" => $BillDate,
				"CurrentDate" => $NowDate 
		);
		return $ItemArr;
	}
	
	/*function PrintLogger($Message) {
	    if (is_array ( $Message )) {
	        foreach ( $Message as $MessageKey => $MessageValue ) {
	            if (is_array ( $MessageValue )) {
	                foreach ( $MessageValue as $LogKey => $LogValue ) {
	                    fwrite ( $this->fp, "::::: " . $LogKey . " ::::: " . $LogValue . " :::::\n" );
	                }
	            } else {
	                fwrite ( $this->fp, "::::: " . $MessageKey . " ::::: " . $MessageValue . " :::::\n" );
	            }
	        }
	    } else {
	        if ($this->Error_flag) {
	            fwrite ( $this->fp, "::::: " . $Message . " :::::\n" );
	        }
	    }
	}*/
}

?> 

















