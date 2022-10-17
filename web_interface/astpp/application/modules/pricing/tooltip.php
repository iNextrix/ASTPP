<?php
$tooltip_data = array(
	/*Create Rate Group(Basic Section)*/
	"pricing_form_reseller_id" => "Reseller is parent entity of this Rate Group who is owner of this group.",

	"pricing_form_name" => "Name of Rate Group use for identification.",

	"pricing_form_routing_prefix" => "If you are offering prefix based routing to your customers then you can define the prefix in this field. this prefix will use to route call based on the dialed prefixes.",

	"pricing_form_status" => "Select status of rate group (Active/Inactive)",

	"pricing_form_pricelist_id_admin" => "If Routing Prefix is configured then based on the selected Admin's rate group the call route will be generated and rates will be applicable",
	/*End*/

	/*Create Rate Group(Billing Section)*/
	"pricing_form_markup" => "Additional charges will be applicable on call cost.Example : If 10% markup defined in rate group and customer made call of $1 then system will charge customers 10% extra on $1 and that will be $1.1.",

	"pricing_form_initially_increment" => "Initial block of increment which is use to do billing of calls.",

	"pricing_form_inc" => "Rate of increment to calculate call cost.Example : 60 to charge every minute.This increment will be useful when increment is not defined in origination rate.",

	"pricing_form_routing_type" => "ASTPP offers various type of routing strategies which you can define from here.",

	"pricing_form_trunk_id" => "Select the trunks for LCR and routing.If no trunks selected then customers who are having the same rate group wouldn’t be able to make outbound calls.",
	/*End*/

	/*Create Duplicate Rate Group Information*/
	"pricing_duplicate_form_name" => "Please mention name of new Rate Group. Its just to use for identity of Rate Group.",

	"pricing_duplicate_form_pricelist_id" => "Here you need to select the Rate Group from which you would like to copy all the data including Rate Group settings with allocated Rates."
	/*End*/
)
?>