<?php
$tooltip_data = array(
	/*Create Gateway (Basic Information)*/
	"gateway_form_name" => "Appropriate name of gateway.",

	"gateway_form_sip_profile_id" => "Sip profile associated with gateway",

	"gateway_form_username" => "Username for the device based authenticated Gateway",

	"gateway_form_password" => "Password for the device based authenticated Gateway",

	"gateway_form_proxy" => "Host of the gateway",

	"gateway_form_outbound-proxy" => "For the cluster setup, Host of Sip proxy in front",

	"gateway_form_register" => "Whether the gateway is userame/passowrd or ip authenticated",

	"gateway_form_caller-id-in-from" => "Whether to replace the invite from user with the channel's caller-id",

	"gateway_form_status" => "Status of gateway",
	/*End*/

	/*Create Gateway (Optional Information)*/

	"gateway_form_from-domain" => "Domain to use in from: *optional",

	"gateway_form_from-user" => "Username to use in from: *optional same as  username, if blank",

	"gateway_form_realm" => "Auth realm: *optional same as gateway name, if blank",

	"gateway_form_extension-in-contact" => "Whether to set customer value as contact username",

	"gateway_form_extension" => "Set value as contact username",

	"gateway_form_expire-seconds" => "Seconds to expired registration and re-register",

	"gateway_form_register-transport" => "Transport protocol for registration",

	"gateway_form_contact-params" => "Extra sip params to send in the contact",

	"gateway_form_ping" => "Seconds to send options ping, failure will unregister and/or mark it down",	

	"gateway_form_retry-seconds" => "Seconds before a retry when a failure or timeout occurs",	

	"gateway_form_register-proxy" => "Rregisteration on this proxy: *optional* same as proxy, if blank",	

	"gateway_form_channel" => "",	

	"gateway_form_dialplan_variable" => "Dialplan Varible",
	/*End*/		

	// ---------------Freeswitch Server Section------------------------

	"fsserver_form_freeswitch_host" => "Freeswitch Host Details",

	"fsserver_form_freeswitch_password" => "Freeswitch Password",

	"fsserver_form_freeswitch_port" => "Freeswitch Port",

	/*Create Sip Devices*/
	"sipdevices_form_fs_username" => "SIP extension username",

	"sipdevices_form_fs_password" => "SIP extension password",

	"sipdevices_form_effective_caller_id_name" => "Caller name presentation for the extension",

	"sipdevices_form_effective_caller_id_number" => "Caller number presentation for the extension",

	"sipdevices_form_reseller_id" => "Reseller account to which this extension belogs",

	"sipdevices_form_accountcode" => "Customer account to which this extension belongs",

	"sipdevices_form_status" => "The status for the added entry",

	"sipdevices_form_sip_profile_id" => "Sip profile to which this extension is belongs",

	"sipdevices_form_codec" => "Specify the codec name which you want to select when call comes on SIP device or call make using SIP device.",
	/*End*/

	/*Voicemail Options*/
	"sipdevices_form_voicemail_enabled" => "To enable voicemail or not",

	"sipdevices_form_voicemail_password" => "Password to retrieve voicemail",

	"sipdevices_form_voicemail_mail_to" => "Email address to receive notification of voicemail on",

	"sipdevices_form_voicemail_attach_file" => "Whether to have attached voicemail in email or not",

	"sipdevices_form_vm_keep_local_after_email" => "After receiving voicemail in email copy of it from system should be removed or not",

	"sipdevices_form_vm_send_all_message" => "To send all the voicemail to email as notification and/or attachment",
	/*End*/

	/*Create SIP Profile*/
	"form1_name" => "Sip profile name",

	"form1_sip_port" => "The port on which freeswitch profile is listening",

	"form1_sip_ip" => "The IP on which freeswitch profile is listening",

	"form1_sipstatus" => "The status for the added entry"
	/*End*/
);
?>