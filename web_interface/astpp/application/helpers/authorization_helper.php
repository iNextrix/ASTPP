<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/*$admin_pages = array('account_list', 'create','payment_process','edit','account_detail_add','remove_account_details','account_detail','accountDetailsPopup','view_invoice','remove','search','clearsearchfilter', 'search_callingcard_account_list', 'search_did_account_list', 'search_did_provider_list', 'search_trunks_provider_list', 'search_trunks_reseller_list', 'search_outbound_reseller_list', 'search_counters_account_list','search_configuration_reseller_list','search_fssipdevices_account_list','search_fscdrs_account_list', 'invoice_json', 'ip_json', 'ani_json', 'dids_json', 'chargelist_json', 'account_detail_json', 'iax_sip_json', 'periodiccharges', 'calccharge', 'counters', 'counters_search', 'clearsearchfilter_counters', 'counters_grid', 'importroutes', 'packages', 'packages_search', 'clearsearchfilter_packages', 'packages_grid', 'periodiccharges_search', 'clearsearchfilter_periodiccharges', 'periodiccharges_grid', 'routes_search', 'clearsearchfilter_routes', 'routes_grid', 'routes', 'pricelist_search', 'pricelists_grid', 'configuration', 'update_brands_select', 'configuration_search', 'clearsearchfilter_configuration', 'configuration_grid', 'purgedeactivated', 'taxes', 'taxes_search','clearsearchfilter_taxes','taxes_grid', 'add', 'brands', 'brands_add','brands_search','brands_grid','cdrs','brands_cdrs_search', 'clearsearchfilter_brands_cdrs', 'brands_cdrs_grid', 'cclist', 'refill', 'remove', 'reset_card', 'update_status', 'view', 'cards_search', 'clearsearchfilter_cards', 'manage_json');
*/
			
			
if( !function_exists( 'validate_access' ) )
{
	function validate_access($user_type,$method, $controller)
	{
		switch($user_type){
			case 0:
				if($controller=="user"){
					$user_pages = array('dashboard','logout', 'cclist', 'viewcard_json', 'manage_json', 'didslist', 'dids_json', 'remove_dids', 'edit_did', 'editdid', 'accountsdetail', 'account_detail_json', 'chargelist_json', 'userdids_json','user_invoice_list','userinvoice_json','get_action_buttons_invoice','add_callerid','redirect_notification', 'search','clearsearchfilter','edit_account','update');
				}
				elseif($controller=="useranimapping"){
					$user_pages = array('index', 'animappinglists', 'manage_json', 'import');
				}
				elseif($controller=="userdid") {
					$user_pages = array('index', 'manage', 'manage_json', 'import');
				}
				elseif($controller=="accounts"){
					$user_pages = array('download_invoice','view_invoice');
                                }
                                elseif($controller=="userreports"){
					$user_pages = array('index','myReport','myReport_grid','myReport_search','clearsearchfilter_myReports','export_cdr_user_xls','export_cdr_user_pdf','myccReport','myccReport_grid','myccReport_search','clearsearchfilter_myccReports','export_cc_cdr_xls','export_cc_cdr_pdf');
                                }
				else{
					$user_pages = array();
				}
			return (in_array($method, $user_pages)? true: false);
			break;
			case 1:
				if($controller=="accounts"){
					$reseller_pages = array('account_list', 'create','payment_process','edit','account_detail_add','remove_account_details','account_detail','accountDetailsPopup','view_invoice','remove','search','clearsearchfilter', 'search_callingcard_account_list', 'search_did_account_list', 'search_did_provider_list', 'search_trunks_provider_list', 'search_trunks_reseller_list', 'search_outbound_reseller_list', 'search_counters_account_list','search_configuration_reseller_list','search_fssipdevices_account_list','search_fscdrs_account_list', 'invoice_json', 'ip_json', 'ani_json', 'dids_json', 'chargelist_json', 'account_detail_json', 'iax_sip_json','download_invoice','add_callerid');
				}
				elseif($controller=="rates"){
					$reseller_pages = array('periodiccharges', 'calccharge', 'counters', 'counters_search', 'clearsearchfilter_counters', 'counters_grid', 'importroutes', 'packages', 'packages_search', 'clearsearchfilter_packages', 'packages_grid', 'periodiccharges_search', 'clearsearchfilter_periodiccharges', 'periodiccharges_grid', 'routes_search', 'clearsearchfilter_routes', 'routes_grid', 'routes', 'pricelist_search', 'clearsearchfilter','pricelists', 'pricelists_grid');
				}
				elseif($controller=="callingcards"){
					$reseller_pages = array('add', 'brands', 'brands_add','brands_search', 'clearsearchfilter', 'brands_grid','cdrs','brands_cdrs_search', 'clearsearchfilter_brands_cdrs', 'brands_cdrs_grid', 'cclist', 'refill', 'remove', 'reset_card', 'update_status', 'view', 'cards_search', 'clearsearchfilter_cards', 'manage_json','add_callerid','export_cc_cdr_pdf','export_cc_cdr_xls');
				}
				elseif($controller=="callshops"){
					$reseller_pages = array('add','listAll', 'remove', 'boothReport', 'booths_list', 'manage_booths_json', 'remove_callshop_booth', 'booth_detail', 'booth_action', 'add_booth', 'booth_search', 'clearsearchfilter_booth', 'callshop_booth_list');
				}
				elseif($controller=="did"){
					$reseller_pages = array('manage', 'build_dids_reseller', 'build_dids', 'did_search', 'clearsearchfilter_did', 'manage_json', 'import');
				}
				elseif($controller=="rates"){
					$reseller_pages = array('periodiccharges', 'calccharge', 'counters', 'counters_search', 'clearsearchfilter_counters', 'counters_grid', 'importroutes', 'packages', 'packages_search', 'clearsearchfilter_packages', 'packages_grid', 'periodiccharges_search', 'clearsearchfilter_periodiccharges', 'periodiccharges_grid', 'routes_search', 'clearsearchfilter_routes', 'routes_grid', 'routes', 'pricelist_search', 'clearsearchfilter', 'pricelists_grid', 'pricelists');
				}
				elseif($controller=="adminreports"){
					$reseller_pages = array('reseller_search', 'clearsearchfilter_reseller', 'resellerReport', 'reseller_list', 'userReport', 'user_list', 'user_search', 'clearsearchfilter_reseller');
				}
				elseif($controller=="accounting"){
					$reseller_pages = array('get_action_buttons_taxes','account_taxes','account_taxes_grid','valid_account_tax','invoice_list','account_invoice_grid','get_action_buttons_invoice','get_account_details','search','clearsearchfilter','search_taxes','clearsearchfilter_taxes');
                                    
                                }
                                elseif($controller=="systems"){
					$reseller_pages = array( 'template','template_grid','get_action_buttons_tem','get_action_button','search','clearsearchfilter');
				}
				elseif($controller=="cdrreports"){
					$reseller_pages = array('index','customerReport','customerReport_grid','customerReport_search','clearsearchfilter_customerReports','export_cdr_customer_xls','export_cdr_customer_pdf','resellerReport','resellerReport_grid','resellerReport_search','clearsearchfilter_resellerReports','export_cdr_reseller_xls','export_cdr_reseller_pdf');
                                }
				else{
					$reseller_pages = array();
				}
			
			return (in_array($method, $reseller_pages)? true: false);
			break;
			case '2':
				if($controller=="accounts"){
					$admin_pages = array('account_list', 'create','payment_process','edit','account_detail_add','remove_account_details','account_detail','accountDetailsPopup','view_invoice','remove','search','clearsearchfilter', 'search_callingcard_account_list', 'search_did_account_list', 'search_did_provider_list', 'search_trunks_provider_list', 'search_trunks_reseller_list', 'search_outbound_reseller_list', 'search_counters_account_list','search_configuration_reseller_list','search_fssipdevices_account_list','search_fscdrs_account_list', 'invoice_json', 'ip_json', 'ani_json', 'dids_json', 'chargelist_json', 'account_detail_json', 'iax_sip_json','download_invoice','add_callerid');
				}
				elseif($controller=="rates"){
					$admin_pages = array('periodiccharges', 'calccharge', 'counters', 'counters_search', 'clearsearchfilter_counters', 'counters_grid', 'importroutes', 'packages', 'packages_search', 'clearsearchfilter_packages', 'packages_grid', 'periodiccharges_search', 'clearsearchfilter_periodiccharges', 'periodiccharges_grid', 'routes_search', 'clearsearchfilter_routes', 'routes_grid', 'routes', 'pricelist_search', 'clearsearchfilter', 'pricelists_grid', 'pricelists');
				}
				elseif($controller=="systems"){
					$admin_pages = array( 'configuration', 'update_brands_select', 'configuration_search', 'clearsearchfilter_configuration', 'configuration_grid', 'purgedeactivated', 'taxes', 'taxes_search','clearsearchfilter_taxes','taxes_grid','template','template_grid','get_action_buttons_tem','get_action_button','search','clearsearchfilter');
				}
				elseif($controller=="callingcards"){
					$admin_pages = array('add', 'brands', 'brands_add','brands_search', 'clearsearchfilter', 'brands_grid','cdrs','brands_cdrs_search', 'clearsearchfilter_brands_cdrs', 'brands_cdrs_grid', 'cclist', 'refill', 'remove', 'reset_card', 'update_status', 'view', 'cards_search', 'clearsearchfilter_cards', 'manage_json','add_callerid','export_cc_cdr_pdf','export_cc_cdr_xls');
				}	
				elseif($controller=="did"){
					$admin_pages = array('manage', 'build_dids_reseller', 'build_dids', 'did_search', 'clearsearchfilter_did', 'manage_json', 'import');
				}
				elseif($controller=="lcr"){
					$admin_pages = array('providers', 'provider_search', 'clearsearchfilter', 'providers_grid', 'trunks', 'trunks_search', 'clearsearchfilter_trunks', 'trunks_grid', 'outbound_search', 'clearsearchfilter_outbound', 'outbound', 'outbound_grid', 'import_outbound');
				}
				elseif($controller=="statistics"){
					$admin_pages = array('error_search','clearsearchfilter_error', 'listerrors', 'trunkstats_search', 'clearsearchfilter_trunkstats', 'trunkstats', 'viewcdrs', 'fscdrs_search', 'clearsearchfilter_fscdrs', 'viewfscdrs');
				}
				elseif($controller=="switchconfig"){
					$admin_pages = array('fssipdevices_search', 'clearsearchfilter_fssipdevices', 'fssipdevices','fssipdevices_grid', 'acl_list', 'acl_grid', '');
				}
				elseif($controller=="adminreports"){
					$admin_pages = array('reseller_search', 'clearsearchfilter_reseller', 'reseller_list', 'resellerReport', 'provider_search', 'clearsearchfilter_provider', 'provider_list', 'providerReport');
				}
				elseif($controller=="callshops"){
					$admin_pages = array('add','listAll', 'remove', 'boothReport', 'booths_list', 'manage_booths_json', 'remove_callshop_booth', 'booth_detail', 'booth_action', 'add_booth');
				}
				elseif($controller=="accounting"){
				      $admin_pages = array('get_action_buttons_taxes','account_taxes','account_taxes_grid','valid_account_tax','invoice_list','account_invoice_grid','get_action_buttons_invoice','get_account_details','search','clearsearchfilter','search_taxes','clearsearchfilter_taxes');
                                }
                                elseif($controller=="opensipsconfig"){
					$admin_pages = array('opensipdevice','opensipdevice_grid','get_action_buttons','dispatcher','dispatcher_grid','get_action_button_dispatcher');
				}
				elseif($controller=="cdrreports"){
					$admin_pages = array('index','customerReport','customerReport_grid','customerReport_search','clearsearchfilter_customerReports','export_cdr_customer_xls','export_cdr_customer_pdf','resellerReport','resellerReport_grid','resellerReport_search','clearsearchfilter_resellerReports','export_cdr_reseller_xls','export_cdr_reseller_pdf','providerReport','providerReport_grid','providerReport_search','clearsearchfilter_providerReports','export_cdr_provider_xls','export_cdr_provider_pdf');
                                }
				else{
					$admin_pages = array();
				}
			
			return (in_array($method, $admin_pages)? true: false);
			break;
			case 3:
				if($controller=="lcr"){
					$vendor_pages = array('outbound_search', 'clearsearchfilter_outbound', 'outbound', 'outbound_grid', 'import_outbound');
				}
				elseif($controller=="statistics"){
					$vendor_pages = array('trunkstats_search', 'clearsearchfilter_trunkstats', 'trunkstats');
				}
				else{
					$vendor_pages = array();
				}
				return (in_array($method, $vendor_pages)? true: false);
			break;
			case 4:
			if($controller=="accounts"){
					$customer_pages = array('account_list', 'create','payment_process','edit','account_detail_add','remove_account_details','account_detail','accountDetailsPopup','view_invoice','remove','search','clearsearchfilter', 'search_callingcard_account_list', 'search_did_account_list', 'search_did_provider_list', 'search_trunks_provider_list', 'search_trunks_reseller_list', 'search_outbound_reseller_list', 'search_counters_account_list','search_configuration_reseller_list','search_fssipdevices_account_list','search_fscdrs_account_list', 'invoice_json', 'ip_json', 'ani_json', 'dids_json', 'chargelist_json', 'account_detail_json', 'iax_sip_json');
				}
				elseif($controller=="callingcards"){
					$customer_pages = array('add', 'brands', 'brands_add','brands_search', 'clearsearchfilter', 'brands_grid','cdrs','brands_cdrs_search', 'clearsearchfilter_brands_cdrs', 'brands_cdrs_grid', 'cclist', 'refill', 'remove', 'reset_card', 'update_status', 'view', 'cards_search', 'clearsearchfilter_cards', 'manage_json');
				}
				elseif($controller=="callshops"){
					$customer_pages = array('add','listAll', 'remove', 'boothReport', 'booths_list', 'manage_booths_json', 'remove_callshop_booth', 'booth_detail', 'booth_action', 'add_booth');
				}
				elseif($controller=="did"){
					$customer_pages = array('manage', 'build_dids_reseller', 'build_dids', 'did_search', 'clearsearchfilter_did', 'manage_json', 'import');
				}
				elseif($controller=="rates"){
					$customer_pages = array('periodiccharges', 'calccharge', 'counters', 'counters_search', 'clearsearchfilter_counters', 'counters_grid', 'importroutes', 'packages', 'packages_search', 'clearsearchfilter_packages', 'packages_grid', 'periodiccharges_search', 'clearsearchfilter_periodiccharges', 'periodiccharges_grid', 'routes_search', 'clearsearchfilter_routes', 'routes_grid', 'routes', 'pricelist_search', 'clearsearchfilter', 'pricelists_grid', 'pricelists');
				}
				elseif($controller=="statistics"){
					$customer_pages = array('error_search','clearsearchfilter_error', 'listerrors', 'trunkstats_search', 'clearsearchfilter_trunkstats', 'trunkstats', 'viewcdrs', 'fscdrs_search', 'clearsearchfilter_fscdrs', 'viewfscdrs');
				}
				elseif($controller=="switchconfig"){
					$customer_pages = array('fssipdevices_search', 'clearsearchfilter_fssipdevices', 'fssipdevices','fssipdevices_grid', 'acl_list', 'acl_grid', '');
				}
				elseif($controller=="systems"){
					$customer_pages = array( 'configuration', 'update_brands_select', 'configuration_search', 'clearsearchfilter_configuration', 'configuration_grid', 'purgedeactivated', 'taxes', 'taxes_search','clearsearchfilter_taxes','taxes_grid');
				}
				else{
					$customer_pages = array();
				}
				return (in_array($method, $customer_pages)? true: false);
			break;
			case 5:
			if($controller=="callshops"){
				$callshop_pages = array('add','listAll', 'remove', 'boothReport', 'booths_list', 'manage_booths_json', 'remove_callshop_booth', 'booth_detail', 'booth_action', 'add_booth');
				}
			elseif($controller=="callingcards"){
					$callshop_pages = array('add', 'brands', 'brands_add','brands_search', 'clearsearchfilter', 'brands_grid','cdrs','brands_cdrs_search', 'clearsearchfilter_brands_cdrs', 'brands_cdrs_grid', 'cclist', 'refill', 'remove', 'reset_card', 'update_status', 'view', 'cards_search', 'clearsearchfilter_cards', 'manage_json');
				}
				elseif($controller=="rates"){
					$callshop_pages = array('periodiccharges', 'calccharge', 'counters', 'counters_search', 'clearsearchfilter_counters', 'counters_grid', 'importroutes', 'packages', 'packages_search', 'clearsearchfilter_packages', 'packages_grid', 'periodiccharges_search', 'clearsearchfilter_periodiccharges', 'periodiccharges_grid', 'routes_search', 'clearsearchfilter_routes', 'routes_grid', 'routes', 'pricelist_search', 'clearsearchfilter', 'pricelists_grid', 'pricelists');
				}
				else{
					$callshop_pages = array();
				}
			return (in_array($method, $callshop_pages)? true: false);
			break;
			default:
			return false;
			
		}
		return false;
		
	}
}
/* End of file authorization_helper.php */
/* Location: ./application/helpers/authorization_helper.php */