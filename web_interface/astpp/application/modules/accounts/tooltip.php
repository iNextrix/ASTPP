<?php
// $style = "class='tooltip_text'"; 
$tooltip_data = array(
	// ------------------------ACCOUNT MODULE START-------------------------
	/*CUSTOMER EDIT SECTION START*/
	//PANEL ACCESS SECTION START FOR CUSTOMER
	"customer_form_reseller_id" => "Reseller are nothing but is the parent of this specific customer. if the specific account needs to create for admin then select option should be admin and if the account is going to add for any of reseller then you have to select specific reseller account so that customer profile will be added under the selected reseller.",

	"customer_form_number" => "Account Number is 10 digits unique identifier string. It can be their phone number or random generated string or the custom unique string.",

	"customer_form_password" => "The password that needs to be provided to the customer so he/she can login into portal.",

	"customer_form_pin" => "Calling Card Pin Important if customer is using calling card feature. Length of pin is configurable, admin can change it from calling card configuration.",

	"customer_form_email" => "E-mail address of the users which use to do login also.",

	"customer_form_permission_id" => "Accessiblity assignment",

	"customer_form_first_used" => "The date-time when the account used first time",

	"customer_form_validfordays" => "The valid days for the account before account got inactivite",

	"customer_form_expiry" => "The date on which the account got expired/inactivated",

	"customer_form_status" => "Existing state of account",

	"customer_form_sip_device_flag" => "Yes - By selecting check-box sip device is automatically created for that new user account. NO - If you have set option “No” then system will not create any sip device for the account.",
	//PANEL ACCESS SECTION END FOR CUSTOMER

	// ACCOUNT ACCESS SECTION START FOR CUSTOMER
	"customer_form_maxchannels" => "Defined the number of concurrent calls to allow the particular customer. <a href='//youtu.be/sKnFW40pYDM' target='_blank' style='color:white;text-decoration:underline;'>Check for more info</a>",

	"customer_form_cps" => "It will manage the calls within the call per second. <a href='//youtu.be/sKnFW40pYDM' target='_blank' style='color:white;text-decoration:underline;'>Check for more info</a>", 

	"customer_form_localization_id" => "It will manage the number translation for caller id and destination number.",

	"customer_form_local_call" => "Allow customers to do local calls or not based on the selection.",

	"customer_form_charge_per_min" => "Local call/On-net/Sip2Sip call charge per minute",

	"customer_form_loss_less_routing" => "Allow calls if origination rate is less than termination rates",

	"customer_form_is_recording" => "Allow call recording",

	"customer_form_allow_ip_management" => "To allow IP management or not",

	"customer_form_notifications" => "Allow to send the notifications to the customer.",

	"customer_form_paypal_permission" => "By setting option “YES” customer will have an access to do payment using available payment gateway.By setting option “NO” customer will not able to do any kind of payment using available payment gateway.",
	// ACCOUNT ACCESS SECTION END FOR CUSTOMER

	//PROFILE SECTION SECTION START FOR CUSTOMER
	"customer_form_first_name" => "Customer First name",

	"customer_form_last_name" => "Customer Last name",

	"customer_form_company_name" => "Customer Company name",

	"customer_form_telephone_1" => "Customer Phone number",

	"customer_form_notification_email" => "Customers can define the notifications email here.",	

	"customer_form_address_1" => "Customer address",

	"customer_form_address_2" => "Customer other address (If any)",

	"customer_form_city" => "Customer City name",

	"customer_form_province" => "Customer Province name",

	"customer_form_postal_code" => "Customer Zip Code name",

	"customer_form_country_id" => "Customer Country",

	"customer_form_timezone_id" => "Customer Timezone , NOTE : ASTPP does not support daylight saving by default. You will need to change the account timezone manually to handle that.",

	"customer_form_currency_id" => "Set currency for the new accounts.",
	//PROFILE SECTION SECTION END FOR CUSTOMER

	//BILLING SECTION SECTION START FOR CUSTOMER
	"customer_form_posttoexternal" => "Select customer account type. Prepaid OR Postpaid. For prepaid customers, system will generate receipts as soon as any charges will be applied to them. For Postpaid, system will generate invoice on defined Billing Day.",

	"customer_form_credit_limit" => "Customer account’s credit limit. Credit limit is only used for the postpaid account.",

	"customer_form_pricelist_id" => "Rate group is an essential field for billing. Without rate group customer wouldn’t be able to make any calls. You can create rate group by navigating to Tariff -> Rate group.",

	"customer_form_non_cli_pricelist_id" => "Rate Group selected based on the CLI Pool selected options.",

	"customer_form_cli_pool" => "To select the rate group or NON-CLI rate group based on caller id number. <a href='//youtu.be/HSfV_oWMaNc' target='_blank' style='color:white;text-decoration:underline;'>Check for more info</a>",

	"customer_form_sweep_id" => "Billing schedule for invoice generation.",

	"customer_form_invoice_day" => "If billing schedule is monthly then you will be able to define the day on which you want customer invoice should be generated.",

	"customer_form_tax_number" => "Display the tax number in invoices.",

	"customer_form_generate_invoice" => "Allow to generate invoices for zero amount.",

	"customer_form_invoice_note" => "It will be display invoice note while generate the invoices.",

	"customer_form_reference" => "To define the reference for the customer.",
	//BILLING SECTION SECTION END FOR CUSTOMER
	/*CUSTOMER EDIT SECTION END*/

	/*MASS CREATE CUSTOMER SECTION START*/

	/*ACCOUNT DETAILS SECTION START FOR CUSTOMER*/
	"customer_bulk_form_count" => "How many number of account you want to generate.",

	"customer_bulk_form_prefix" => "Set the prefix from where the account number should be start.",

	"customer_bulk_form_account_length" => "Set the number of string length for the account number.",

	"customer_bulk_form_pin" => "If you want to generate calling-card customer then set option “YES” so it will create PIN number for all accounts.",

	"customer_bulk_form_validfordays" => "Valid days for customer account.",

	"customer_bulk_form_currency_id" => "Set currency for the new accounts.",

	"customer_bulk_form_country_id" => "Set country for new accounts.",

	"customer_bulk_form_timezone_id" => "Set proper timezone for new account. NOTE : ASTPP does not support daylight saving by default. You will need to change the account timezone manually to handle that.",
	/*ACCOUNT DETAILS SECTION END FOR CUSTOMER*/

	/*BILLING SETTINGS SECTION START FOR CUSTOMER*/
	"customer_bulk_form_posttoexternal" => "Set type of account (Prepaid/Postpaid)",

	"customer_bulk_form_balance" => "Set initial balance if you want to offer at the time of account creation.",

	"customer_bulk_form_credit_limit" => "For postpaid customers you can set credit limit.",

	"customer_bulk_form_cli_pool" => "To select the rate group or NON-CLI rate group based on caller id number. <a href='//youtu.be/HSfV_oWMaNc' target='_blank' style='color:white;text-decoration:underline;'>Check for more info</a>",

	"customer_bulk_form_pricelist_id" => "Rate group is an essential field for billing. Without rate group customer wouldn’t be able to make any calls. You can create rate group by navigating to Tariff -> Rate group.",

	"customer_bulk_form_non_cli_pricelist_id" => "Rate Group selected based on the CLI Pool selected options.",

	"customer_form_tax_id" => "Applicable taxes on invoice",

	"customer_bulk_form_sweep_id" => "Billing schedule for invoice generation.",
	/*BILLING SETTINGS SECTION END FOR CUSTOMER*/

	/*Create Reseller Section start*/
	//PANEL ACCESS SECTION START FOR RESELLER
	"reseller_form_reseller_id" => "Reseller are nothing but is the parent of this specific customer. if the specific account needs to create for admin then select option should be admin and if the account is going to add for any of reseller then you have to select specific reseller account so that customer profile will be added under the selected reseller.",

	"reseller_form_number" => "Account Number is 10 digits unique identifier string. It can be their phone number or random generated string or the custom unique string.",

	"reseller_form_password" => "The password that needs to be provided to the customer so he/she can login into portal.",

	"reseller_form_pin" => "Calling Card Pin Important if customer is using calling card feature. Length of pin is configurable, admin can change it from calling card configuration.",

	"reseller_form_email" => "E-mail address of the users which use to do login also.",

	"reseller_form_permission_id" => "Accessibility assignment",

	"reseller_form_is_distributor" => "Type of Reseller. <a href='//youtu.be/T87ZVbtj2sI' target='_blank' style='color:white;text-decoration:underline;'>Check for more info</a>",
	//PANEL ACCESS SECTION END FOR RESELLER

	//Account Settings For Reseller Start
	"reseller_form_maxchannels" => "Defined the number of concurrent calls to allow the particular customer. <a href='//youtu.be/sKnFW40pYDM' target='_blank' style='color:white;text-decoration:underline;'>Check for more info</a>",

	"reseller_form_cps" => "It will manage the calls within the call per second. <a href='//youtu.be/sKnFW40pYDM' target='_blank' style='color:white;text-decoration:underline;'>Check for more info</a>",

	"reseller_form_notifications" => "Allow to send the notifications to the customer.",
	//Account Settings For Reseller End

	//PROFILE SECTION SECTION START FOR RESELLER
	"reseller_form_first_name" => "Reseller First name",

	"reseller_form_last_name" => "Reseller Last name",

	"reseller_form_company_name" => "Reseller Company name",

	"reseller_form_telephone_1" => "Reseller Phone number",

	"reseller_form_notification_email" => "Reseller can define the notifications email here.",	

	"reseller_form_address_1" => "Reseller address",

	"reseller_form_address_2" => "Reseller other address (If any)",

	"reseller_form_city" => "Reseller City name",

	"reseller_form_province" => "Reseller Province name",

	"reseller_form_postal_code" => "Reseller Zip Code name",

	"reseller_form_country_id" => "Reseller Country",

	"reseller_form_timezone_id" => "Reseller Timezone , NOTE : ASTPP does not support daylight saving by default. You will need to change the account timezone manually to handle that.",

	"reseller_form_currency_id" => "Set currency for the new accounts.",

	"reseller_form_invoice_config_flag" => "Reflect the same details in reseller's company profile",
	//PROFILE SECTION SECTION END FOR RESELLER

	/*BILLING SETTINGS SECTION START FOR RESELLER*/
	"reseller_form_posttoexternal" => "Set type of account (Prepaid/Postpaid)",

	"reseller_form_credit_limit" => "For postpaid customers you can set credit limit.",

	"reseller_form_pricelist_id" => "Rate group is an essential field for billing. Without rate group customer wouldn’t be able to make any calls. You can create rate group by navigating to Tariff -> Rate group.",

	"reseller_form_non_cli_pricelist_id" => "Rate Group selected based on the CLI Pool selected options.",

	"reseller_form_sweep_id" => "Billing schedule for invoice generation.",

	"reseller_form_invoice_day" => "If billing schedule is monthly then you will be able to define the day on which you want customer invoice should be generated.",

	"reseller_form_tax_number" => "Display the tax number in invoices.",

	"reseller_form_generate_invoice" => "Allow to generate invoices for zero amount.",

	"reseller_form_invoice_note" => "It will be display invoice note while generate the invoices.",

	"reseller_form_reference" => "To define the reference for the customer.",

	"reseller_form_invoice_interval" => '',
	/*BILLING SETTINGS SECTION END FOR RESELLER*/

	/*Edit rseller*/

	"reseller_form_registration_url" => 'URL to share/use to signup the new account directly under this reseller',

	"reseller_form_status" => 'The status for the added entry',

	"reseller_form_tax_id[]" => 'From globally configured taxes, select applicable for this reseller',

	"reseller_form_loss_less_routing" => "Allow calls if origination rate is less than termination rates",

	"reseller_form_charge_per_min" => "Local call/On-net/Sip2Sip call charge per minute",

	"reseller_form_notify_flag" => 'To enable the low balance alert or not',

	"reseller_form_notify_credit_limit" => 'What balance considered as low to trigger the alert',

	// "reseller_form_notify_flag" => '',
	/*Create Reseller Section end*/

	/*Create Panel Access For Admin Section Start*/
	"admin_form_number" => "Account Number is 10 digits unique identifier string. It can be their phone number or random generated string or the custom unique string.",

	"admin_form_password" => "The password that needs to be provided to the admin so he/she can login into portal.",

	"admin_form_email" => "E-mail address of the admin which use to do login also.",

	"admin_form_permission_id" => "Accessibility assignment",
	/*Create Panel Access Admin Section End*/

	/*Create Profile Section For Admin Start*/
	"admin_form_first_name" => "Admin First name",

	"admin_form_last_name" => "Admin Last name",

	"admin_form_notification_email" => "Admin can define the notifications email here.",

	"admin_form_telephone_1" => "Primary Phone Number",

	"admin_form_telephone_2" => "Secondary Phone Number",
	/*Create Profile Section For Admin End*/

	/*Edit Profile Section For Admin Start*/
	"admin_form_company_name" => "Admin Company name",

	"admin_form_address_1" => "Admin address",

	"admin_form_address_2" => "Admin other address (If any)",

	"admin_form_city" => "Admin City name",

	"admin_form_province" => "Admin Province name",

	"admin_form_postal_code" => "Admin Zip Code name",

	"admin_form_country_id" => "Admin Country",

	"admin_form_timezone_id" => "Admin Timezone , NOTE : ASTPP does not support daylight saving by default. You will need to change the account timezone manually to handle that.",

	"admin_form_currency_id" => "Set currency for the new accounts.",

	"admin_form_status" => "Status of Account",

	"ani_map_number" => "Assigned caller-id list to allow pin less usage of Access Number",

	"did_purchase_country_id" => "Sort list the DIDs to purchase based on the Country",

	"did_purchase_provience" => "Sort list the DIDs to purchase based on the Province",

	"did_purchase_city" => "Sort list the DIDs to purchase based on the City",

	"did_purchase_free_didlist" => "Available DIDs based on searched criterion",

	"purchase_products_applayable_product" => "Assigned Products list to this account",

	"customer_alert_threshold_notify_flag" => "To enable the low balance alert or not",

	"customer_alert_threshold_notify_credit_limit" => "What balance considered as low to trigger the alert",
	/*Edit Profile Section For Admin End*/

	/*Create Sip Devices*/
	"sipdevices_form_fs_username" => "SIP extension username",

	"sipdevices_form_fs_password" => "SIP extension password",

	"sipdevices_form_effective_caller_id_name" => "Caller name presentation for the extension",

	"sipdevices_form_effective_caller_id_number" => "Caller number presentation for the extension",

	"sipdevices_form_reseller_id" => "Reseller account to which this extension belogs",

	"sipdevices_form_accountcode" => "Customer account to which this extension belongs",

	"sipdevices_form_status" => "The status for the added entry",

	"sipdevices_form_sip_profile_id" => "Sip profile to which this extension is belongs",
	/*End*/

	/*Voicemail Options*/
	"sipdevices_form_voicemail_enabled" => "To enable voicemail or not",

	"sipdevices_form_voicemail_password" => "Password to retrieve voicemail",

	"sipdevices_form_voicemail_mail_to" => "Email address to receive notification of voicemail on",

	"sipdevices_form_voicemail_attach_file" => "Whether to have attached voicemail in email or not",

	"sipdevices_form_vm_keep_local_after_email" => "After receiving voicemail in email copy of it from system should be removed or not",

	"sipdevices_form_vm_send_all_message" => "To send all the voicemail to email as notification and/or attachment",
	/*End*/

    // ------------------------ACCOUNT MODULE END-------------------------

);
?>

