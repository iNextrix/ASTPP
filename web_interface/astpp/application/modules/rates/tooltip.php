<?php
$tooltip_data = array(
	/*Create Origination Rate(Rate Information)*/

	"origination_rate_form_reseller_id" => "Reseller is owner of this rates who is going to add this rate for their specific usage. also reseller dropdown use to filter data of Rate Group which is next field.",

	"origination_rate_form_pricelist_id" => "Based on reseller dropdown value the available rate group will be listed here. select any one group for which you would like to configure rate.",

	"origination_rate_form_pattern" => "Prefix of origination rate. Example: 91",

	"origination_rate_form_comment" => "Description for rate. Example : India",

	"origination_rate_form_country_id" => "Select appropriate country for the prefix which you are going to configure.",

	"origination_rate_form_call_type" => "Select the call type",

	"origination_rate_form_routing_type" => "Routing Type is most essential field of this form. you can choose the strategy of routing calls for customer and based on that system will do termination of calls.",

	"origination_rate_form_status" => "Select status of Origination Rates (Active / Inactive).",

	"origination_rate_form_trunk_id_new[1]" => "To force call to route using Force trunk 1.",

	"origination_rate_form_trunk_id_new[2]" => "To force call to route using Force trunk 2.",

	"origination_rate_form_trunk_id_new[3]" => "To force call to route using Force trunk 3.",

	"origination_rate_form_trunk_id" => "To force call to route using Force trunk",
	/*End*/

	/*Billing Information Section*/
	"origination_rate_form_connectcost" => "Connection fee to charge customer minimum when their call will be connected",

	"origination_rate_form_includedseconds" => "Define seconds will be free from the call duration for each call",

	"origination_rate_form_cost" => "Cost per minute",

	"origination_rate_form_init_inc" => "Very first rate of increment to calculate call cost.",

	"origination_rate_form_inc" => "Rate of increment to calculate call cost Example : 60 to charge every minute.",

	"origination_rate_form_effective_date" => "Date-time from when the rate will be start applying",
	/*End*/

	// --------------------Termination Rate Section---------------------------

	/*Create Termination Rate (Rate Information)*/
	"termination_rate_form_trunk_id" => "Trunk for which the termination rate is being added",

	"termination_rate_form_pattern" => "The Code/Prefix of the termination rate",

	"termination_rate_form_comment" => "The destination country and/or type, i.e. Spain-Mobile, Canada-Landline etc.",

	"termination_rate_form_strip" => "The number if match with beginning of the dialed number then remove/strip it",

	"termination_rate_form_prepend" => "The number add before dialed number",

	"termination_rate_form_status" => "Activeness of the code",
	/*End*/

	/*Create Termination Rate (Billing Information)*/
	"termination_rate_form_connectcost" => "One time cost when call connection established",
	"termination_rate_form_includedseconds" => "The number seconds will not be charged/free seconds",

	"termination_rate_form_cost" => "Cost to charge per minute",

	"termination_rate_form_init_inc" => "Minimum billing seconds charged.",

	"termination_rate_form_inc" => "Subsequent billing seconds charged after Initial Increment.",

	"termination_rate_form_precedence" => "For multiple rates under different trunks with same 'Code' with '0' having highest priority",

	"termination_rate_form_effective_date" => "Date-time from when the rate will be start applying",
	/*End*/

	"import_origination_rate_pricelist_id" => "The rate group to which the imported rates assigned",

	"import_origination_rate_trunk_id" => "Trunk to select forcefully for imported rates",

	"import_termination_rate_trunk_id" => "The trunk to which the imported rates assigned",

	"import_termination_rate_mapper_trunk_id" => "The trunk to which the imported rates assigned"
)
?>