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
if (! defined ( 'BASEPATH' )) {
	exit ( 'No direct script access allowed' );
}
class invoices_form {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
	}
	function build_invoices_list_for_admin() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		$logintype = $this->CI->session->userdata ( 'logintype' );
		$url = ($logintype == 0 || $logintype == 3) ? "/user/user_invoice_download/" : '/invoices/invoice_main_download/';
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Number" ),
						"110",
						"id",
						"id,'',type",
						"invoices",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Type" ),
						"130",
						"id",
						"id,'',type",
						"invoices",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account" ),
						"130",
						"accountid",
						"first_name,last_name,number",
						"accounts",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Generated<br/> Date" ),
						"140",
						"invoice_date",
						"invoice_date",
						"",
						"get_invoice_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "From Date" ),
						"120",
						"from_date",
						"from_date",
						"",
						"get_from_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Due Date" ),
						"130",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Last <br/>Pay Date" ),
						"100",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount($currency)" ),
						"120",
						"id",
						"id",
						"id",
						"get_invoice_total",
						"",
						"true",
						"right" 
				),
				
				array (
						gettext ( "Outstanding <br/>Amount($currency)" ),
						"140",
						"",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				// array("Payment", "110", "payment", "", "", ""),
				array (
						gettext ( "Action" ),
						"120",
						"",
						"",
						"",
						array (
								"DOWNLOAD" => array (
										"url" => $url,
										"mode" => "single" 
								) 
						)
						// "VIEW" => array("url" => "invoices/invoice_summary_payment/", "mode" => "popup")
						 
				) 
		) );
		return $grid_field_arr;
	}
	function build_invoices_list_for_customer_admin() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		$logintype = $this->CI->session->userdata ( 'logintype' );
		$url = ($logintype == 0 || $logintype == 3) ? "/user/user_invoice_download/" : '/invoices/invoice_main_download/';
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Number" ),
						"110",
						"id",
						"id,'',type",
						"invoices",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Type" ),
						"110",
						"id",
						"id,'',type",
						"invoices",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Generated<br/> Date" ),
						"120",
						"invoice_date",
						"invoice_date",
						"",
						"get_invoice_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "From Date" ),
						"120",
						"from_date",
						"from_date",
						"",
						"get_from_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Due Date" ),
						"130",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Last <br/>Pay Date" ),
						"100",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount($currency)" ),
						"100",
						"id",
						"id",
						"id",
						"get_invoice_total",
						"",
						"true",
						"right" 
				),
				
				array (
						gettext ( "Outstanding <br/>Amount($currency)" ),
						"100",
						"",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Action" ),
						"120",
						"",
						"",
						"",
						array (
								"DOWNLOAD" => array (
										"url" => $url,
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_invoices_list_for_customer() {
		$url = ($this->CI->session->userdata ( 'logintype' ) == 0) ? "/user/user_invoice_download/" : '/invoices/invoice_main_download/';
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Number" ),
						"100",
						"id",
						"id,'',type",
						"invoices",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account" ),
						"110",
						"accountid",
						"first_name,last_name,number",
						"accounts",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Generated Date" ),
						"140",
						"invoice_date",
						"invoice_date",
						"",
						"get_invoice_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "From Date" ),
						"140",
						"from_date",
						"from_date",
						"",
						"get_from_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Due Date" ),
						"150",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Last Pay Date" ),
						"150",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount)" ),
						"150",
						"id",
						"id",
						"id",
						"get_invoice_total",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Outstanding Amount" ),
						"150",
						"",
						"",
						"",
						"" 
				),
				// array("Payment", "110", "payment", "", "", ""),
				array (
						gettext ( "Action" ),
						"160",
						"",
						"",
						"",
						array (
								"DOWNLOAD" => array (
										"url" => $url,
										"mode" => "single" 
								) 
						)
						// "VIEW" => array("url" => "invoices/invoice_summary_payment/", "mode" => "popup")
						 
				) 
		) );
		return $grid_field_arr;
	}
	function get_invoice_search_form() {
		$account_data = $this->CI->session->userdata ( "accountinfo" );
		$reseller_id = $account_data ['type'] == 1 ? $account_data ['id'] : 0;
		$form ['forms'] = array (
				"",
				array (
						'id' => "invoice_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				array (
						gettext ( 'Number' ),
						'INPUT',
						array (
								'name' => 'invoiceid[invoiceid]',
								'',
								'id' => 'invoiceid',
								'size' => '15',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'invoiceid[invoiceid-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'From Date' ),
						'INPUT',
						array (
								'name' => 'from_date[0]',
								'id' => 'invoice_from_date',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'',
						'from_date[from_date-date]' 
				),
				array (
						gettext ( 'To Date' ),
						'INPUT',
						array (
								'name' => 'to_date[0]',
								'id' => 'invoice_to_date',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'',
						'from_date[from_date-date]' 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'amount[amount]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'amount[amount-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Generated Date' ),
						'INPUT',
						array (
								'name' => 'invoice_date[0]',
								'',
								'size' => '20',
								'class' => "text field",
								'id' => 'invoice_date' 
						),
						'',
						'tOOL TIP',
						'',
						'invoice_date[invoice_date-date]' 
				),
				// array('Invoice', 'deleted', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_invoice_details'),
				array (
						gettext ( 'Account' ),
						'accountid',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number',
						'accounts',
						'build_dropdown_deleted',
						'where_arr',
						array (
								"reseller_id" => $reseller_id,
								"type" => "GLOBAL" 
						) 
				),
				array (
						'',
						'HIDDEN',
						'ajax_search',
						'1',
						'',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						'advance_search',
						'1',
						'',
						'',
						'' 
				) 
		);
		
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "invoice_search_btn",
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => gettext ( 'Clear' ),
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function build_grid_buttons() {
		$buttons_json = json_encode ( array () );
		return $buttons_json;
	}
	function get_invoiceconf_form_fields($invoiceconf = '0') {
		if (! empty ( $invoiceconf )) {
			if ($invoiceconf ['logo'] != '') {
				$logo = $invoiceconf ['file'];
			} else {
				$logo = $invoiceconf ['logo'];
			}
			if ($invoiceconf ['favicon'] != '') {
				$favicon = $invoiceconf ['file_fav'];
			} else {
				$favicon = $invoiceconf ['favicon'];
			}
			$accountid = $invoiceconf ['accountid'];
			if ($logo != '') {
				$file_name = base_url () . "upload/$logo";
				$image_path = array (
						'Existing Image',
						'IMAGE',
						array (
								'type' => 'image',
								'name' => 'image',
								'style' => 'width:100%;margin-top:20px;',
								'src' => $file_name 
						),
						'',
						'tOOL TIP',
						'' 
				);
				$delete_logo = array (
						'Delete logo',
						'DEL_BUTTON',
						array (
								'value' => 'ankit',
								'style' => 'margin-top:20px;',
								'name' => 'button',
								'id' => 'logo_delete',
								'size' => '20',
								'class' => "btn btn-line-parrot" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				);
			} else {
				$image_path = array (
						'Existing Image',
						'HIDDEN',
						array (
								'type' => '',
								'name' => '',
								'style' => 'width:250px;' 
						),
						'',
						'tOOL TIP',
						'' 
				);
				$delete_logo = array (
						'Delete logo',
						'HIDDEN',
						array (
								'value' => 'ankit',
								'style' => 'margin-top:0px;',
								'name' => 'button',
								'id' => 'logo_delete',
								'size' => '20',
								'maxlength' => '100',
								'class' => "btn btn-line-parrot" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				);
				// $image_path=array();
			}
			if ($favicon != '') {
				
				$file_name_fav = base_url () . "upload/$favicon";
				
				$image_fav = array (
						'Existing Favicon',
						'IMAGE',
						array (
								'type' => 'image',
								'name' => 'image',
								'style' => 'width:100%;margin-top:20px;',
								'src' => $file_name_fav 
						),
						'',
						'tOOL TIP',
						'' 
				);
				$delete_fav = array (
						'Delete Favicon',
						'DEL_BUTTON',
						array (
								'value' => '',
								'style' => 'margin-top:20px;',
								'name' => 'button',
								'id' => 'fav_delete',
								'size' => '20',
								'class' => "btn btn-line-parrot" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				);
			} else {
				$image_fav = array (
						'Existing Favicon',
						'HIDDEN',
						array (
								'type' => '',
								'name' => '',
								'style' => 'width:250px;' 
						),
						'',
						'tOOL TIP',
						'' 
				);
				$delete_fav = array (
						'Delete Favicon',
						'HIDDEN',
						array (
								'value' => '',
								'style' => 'margin-top:0px;',
								'name' => 'button',
								'id' => 'fav_delete',
								'size' => '20',
								'maxlength' => '100',
								'class' => "btn btn-line-parrot" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				);
				// $image_path=array();
			}
		} else {
			$logo = '';
			$file_name = '';
			$favicon = '';
			$file_name_fav = '';
			$accountid = 0;
			$image_path = array (
					'Existing Logo',
					'HIDDEN',
					array (
							'type' => '',
							'name' => '' 
					),
					'',
					'tOOL TIP',
					'' 
			);
			$delete_logo = array (
					'Delete logo',
					'HIDDEN',
					array (
							'value' => '',
							'style' => 'margin-top:0px;',
							'name' => 'button',
							'onclick' => 'return image_delete(' . $accountid . ')',
							'size' => '20',
							'maxlength' => '100',
							'class' => "btn btn-line-parrot" 
					),
					'',
					'tOOL TIP',
					'Please Enter account number' 
			);
			$image_fav = array (
					'Existing Favicon',
					'HIDDEN',
					array (
							'type' => '',
							'name' => '' 
					),
					'',
					'tOOL TIP',
					'' 
			);
			$delete_fav = array (
					'Delete Favicon',
					'HIDDEN',
					array (
							'value' => '',
							'style' => 'margin-top:0px;',
							'name' => 'button',
							'onclick' => 'return image_delete(' . $accountid . ')',
							'size' => '20',
							'maxlength' => '100',
							'class' => "btn btn-line-parrot" 
					),
					'',
					'tOOL TIP',
					'Please Enter account number' 
			);
			// $image_path=array();
		}
		$form ['forms'] = array (
				base_url () . 'invoices/invoice_conf/',
				array (
						'id' => 'invoice_conf_form',
						'method' => 'POST',
						'name' => 'invoice_conf_form',
						'enctype' => 'multipart/form-data' 
				) 
		);
		$form ['Configuration '] = array (
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'id' 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'accountid' 
						),
						'',
						'',
						'',
						'' 
				),
				// array('', 'HIDDEN', array('name' => 'start_name','value' => '1'), '', '', ''),
				array (
						gettext ( 'Company name' ),
						'INPUT',
						array (
								'name' => 'company_name',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Address' ),
						'INPUT',
						array (
								'name' => 'address',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'City' ),
						'INPUT',
						array (
								'name' => 'city',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Province' ),
						'INPUT',
						array (
								'name' => 'province',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Country' ),
						'INPUT',
						array (
								'name' => 'country',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Zipcode' ),
						'INPUT',
						array (
								'name' => 'zipcode',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Telephone' ),
						'INPUT',
						array (
								'name' => 'telephone',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Fax' ),
						'INPUT',
						array (
								'name' => 'fax',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Email Address' ),
						'INPUT',
						array (
								'name' => 'emailaddress',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Website' ),
						'INPUT',
						array (
								'name' => 'website',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Company Tax number' ),
						'INPUT',
						array (
								'name' => 'invoice_taxes_number',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				)
		)
		;
		$form ['Invoice Configuration '] = array (
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'id' 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'accountid' 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						gettext ( 'Invoice Notification' ),
						'invoice_notification',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_allow_invoice' 
				),
				array (
						gettext ( 'Invoice Due Notification' ),
						'invoice_due_notification',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_allow_invoice' 
				),
				array (
						gettext ( 'Invoice Due Days' ),
						'INPUT',
						array (
								'name' => 'interval',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Notify before due days' ),
						'INPUT',
						array (
								'name' => 'notify_before_day',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Invoice Prefix' ),
						'INPUT',
						array (
								'name' => 'invoice_prefix',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Invoice Start Form' ),
						'INPUT',
						array (
								'name' => 'invoice_start_from',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						'',
						'HIDDEN',
						array (
								'name' => '' 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						array (
								'name' => '' 
						),
						'',
						'',
						'',
						'' 
				) 
		);
		$form ['Portal personalization'] = array (
				
				array (
						gettext ( 'Domain' ),
						'INPUT',
						array (
								'name' => 'domain',
								'size' => '20',
								'maxlength' => '100',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Header' ),
						'INPUT',
						array (
								'name' => 'website_title',
								'size' => '100',
								'maxlength' => '100',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Footer' ),
						'INPUT',
						array (
								'name' => 'website_footer',
								'size' => '200',
								'maxlength' => '200',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Logo' ),
						'IMAGE',
						array (
								'name' => 'file',
								'size' => '20',
								'maxlength' => '100',
								'class' => "",
								'id' => 'uploadFile',
								'type' => 'file' 
						),
						'class' => '',
						'tOOL TIP',
						'Please Enter account number' 
				),
				// array('', 'BLANL_DIV', array('name'=>'accountid','id'=>'imagePreview'),'', '', '', ''),
				$delete_logo,
				$image_path,
				array (
						'Favicon',
						'IMAGE',
						array (
								'name' => 'file_fav',
								'size' => '20',
								'maxlength' => '100',
								'class' => "",
								'id' => 'uploadFav',
								'type' => 'file' 
						),
						'class' => '',
						'tOOL TIP',
						'Please Enter account number' 
				),
				$delete_fav,
				$image_fav 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		
		return $form;
	}
}
?>
