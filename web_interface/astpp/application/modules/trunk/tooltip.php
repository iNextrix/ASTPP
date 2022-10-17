<?php
$tooltip_data = array(
	/*Create Trunk (Information)*/
	"trunks_form_name" => "Name of the trunk for identification.",

	"trunks_form_provider_id" => "Select appropriate provider which is associated with this specific trunk.",

	"trunks_form_gateway_id" => "Select the Gateway where you want to terminate the call using this trunk",

	"trunks_form_failover_gateway_id" => "Failover Gateway is use to do second attempt for call termination in case of first gateway are down or not responding.",

	"trunks_form_failover_gateway_id1" => "Failover Gateway is use to do third attempt for call termination in case of second gateway are down or not responding.",

	"trunks_form_localization_id" => "It will manage the number translation for caller id and destination number.",

	"trunks_form_sip_cid_type" => "Modify how the Caller ID will show up in SIP header of the outbound leg.",
	/*End*/

	/*Create Trunk (Settings)*/
	"trunks_form_codec" => "Specify the codec name which you want to send to termination part.",

	"trunks_form_leg_timeout" => "Ring time out time.",

	"trunks_form_maxchannels" => "How many concurrent call you want to allow using the same trunk that you need to define here.",

	"trunks_form_cps" => "How many CPS (Call Per Second) you want to allow using the same trunk that you need to define here.",

	"trunks_form_precedence" => "Set the priority of trunk.",

	"trunks_form_status" => "Status for the trunk (Active/Inactive)",
	/*End*/
);
?>