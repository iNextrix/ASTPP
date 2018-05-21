<?php

if (! defined ( 'BASEPATH' )) {
	exit ( 'No direct script access allowed' );
}

/*
 * |--------------------------------------------------------------------------
 * | File and Directory Modes
 * |--------------------------------------------------------------------------
 * |
 * | These prefs are used when checking and setting modes when working
 * | with the file system. The defaults are fine on servers with proper
 * | security, but you may wish (or even need) to change the values in
 * | certain environments (Apache running a separate process for each
 * | user, PHP under CGI with Apache suEXEC, etc.). Octal values should
 * | always be used to set the mode correctly.
 * |
 */
define ( 'FILE_READ_MODE', 0644 );
define ( 'FILE_WRITE_MODE', 0666 );
define ( 'DIR_READ_MODE', 0755 );
define ( 'DIR_WRITE_MODE', 0777 );

/*
 * |--------------------------------------------------------------------------
 * | File Stream Modes
 * |--------------------------------------------------------------------------
 * |
 * | These modes are used when working with fopen()/popen()
 * |
 */

define ( 'FOPEN_READ', 'rb' );
define ( 'FOPEN_READ_WRITE', 'r+b' );
define ( 'FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb' ); // truncates existing file data, use with care
define ( 'FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b' ); // truncates existing file data, use with care
define ( 'FOPEN_WRITE_CREATE', 'ab' );
define ( 'FOPEN_READ_WRITE_CREATE', 'a+b' );
define ( 'FOPEN_WRITE_CREATE_STRICT', 'xb' );
define ( 'FOPEN_READ_WRITE_CREATE_STRICT', 'x+b' );
define ( "RESELLERPROFILE_ARRAY", serialize ( array (
		"My Profile" => "user/user_myprofile/",
		"Change Password" => "user/user_change_password/",
		"Subscriptions" => "user/user_subscriptions/",
		"Invoice" => "user/user_invoices_list/",
		"Refill Report" => "user/user_refill_report/",
		"Charges History" => "user/user_charges_history/",
		"Packages" => "user/user_packages/",
		"Company Profile" => "user/user_invoice_config/",
		"CDRs" => "user/user_cdrs/",
		"Emails" => "user/user_emails/",
		"Alert Threshold" => "user/user_alert_threshold/" 
) ) );
define ( "CUSTOMEREDIT_ARRAY", serialize ( array (
		"Customer Profile" => "accounts/customer_edit/",
		"SIP Devices" => "accounts/customer_sipdevices/",
		"Opensips Device" => "accounts/customer_opensips/",
		"IP Settings" => "accounts/customer_ipmap/",
		"Caller ID" => "accounts/customer_animap/",
		"Speed Dial" => "accounts/customer_speeddial/",
		"Blocked Codes" => "accounts/customer_blocked_prefixes/",
		"DID" => "accounts/customer_dids/",
		"Subscription" => "accounts/customer_subscription/",
		"Invoice" => "accounts/customer_invoices/",
		"Refill Report" => "accounts/customer_refillreport/",
		"Charges History" => "accounts/customer_charges/",
		"CDRs" => "accounts/customer_cdrs/",
		"Emails" => "accounts/customer_emailhistory/",
		"Alert Threshold" => "accounts/customer_alert_threshold/" 
) ) );

define ( "PROVIDEREDIT_ARRAY", serialize ( array (
		"Provider Profile" => "accounts/provider_edit/",
		"SIP Devices" => "accounts/provider_sipdevices/",
		"Opensips Device" => "accounts/provider_opensips/",
		"IP Settings" => "accounts/provider_ipmap/",
		"Caller ID" => "accounts/provider_animap/",
		"Speed Dial" => "accounts/provider_speeddial/",
		"Blocked Codes" => "accounts/provider_blocked_prefixes/",
		"DID" => "accounts/provider_dids/",
		"Subscription" => "accounts/provider_subscription/",
		"Invoice" => "accounts/provider_invoices/",
		"Refill Report" => "accounts/provider_refillreport/",
		"Charges History" => "accounts/provider_charges/",
		"CDRs" => "accounts/provider_cdrs/",
		"Emails" => "accounts/provider_emailhistory/",
		"Alert Threshold" => "accounts/provider_alert_threshold/" 
) ) );
define ( "RESELLEREDIT_ARRAY", serialize ( array (
		"Reseller Profile" => "accounts/reseller_edit/",
		"DID" => "accounts/reseller_dids/",
		"Subscription" => "accounts/reseller_subscription/",
		"Invoice" => "accounts/reseller_invoices/",
		"Refill Report" => "accounts/reseller_refillreport/",
		"Charges History" => "accounts/reseller_charges/",
		"Packages" => "accounts/reseller_packages/",
		"Company Profile" => "accounts/reseller_invoice_config/",
		"CDRs" => "accounts/reseller_cdrs/",
		"Emails" => "accounts/reseller_emailhistory/",
		"Alert Threshold" => "accounts/reseller_alert_threshold/" 
) ) );
define ( "PACKAGEEDIT_ARRAY", serialize ( array (
		"Package Details" => "package/package_edit/",
		"Package Codes" => "package/package_pattern_list/" 
) ) );
define ( "CUSTOMERPROFILE_ARRAY", serialize ( array (
		"My Profile" => "user/user_myprofile/",
		"Change Password" => "user/user_change_password" 
) ) );
define ( "DATABASE_DIRECTORY", FCPATH . 'database_backup' . DIRECTORY_SEPARATOR );
define ( 'LOCALE_REQUEST_PARAM', 'lang' );
define ( 'WEBSITE_DOMAIN', 'messages' );

/*
 * |--------------------------------------------------------------------------
 * | Define default language for UI
 * |--------------------------------------------------------------------------
 * |
 * | Set en_EN for English language
 * | Set es_ES for Spanish language
 * | Set fr_FR for French language
 * | Set pt_BR for portuguese Brazilian language 
 * |
 */
define ('DEFAULT_LANGUAGE','en_EN');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
