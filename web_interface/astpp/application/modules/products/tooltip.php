<?php
/*Create Product*/
$tooltip_data = array(
	/*Basic Information*/
	"product_add_form_product_category" => "To defined product type please select the appropriate product category. Packages is a default category for all.",

	"product_add_form_product_name" => "Name of package want to create.",

	"product_add_form_country_id" => "Select the country so it will help to users for product identification when they are looking for specific product for specific country.",

	"product_add_form_product_description" => "Description for the package.",

	"product_add_form_product_buy_cost" => "Define the buy cost for the reseller or other entity.

	Buy Cost is product purchase price without TAX. so when customer or reseller will order this product at that time the TAX amount will be applicable additionally.",

	"product_add_form_can_purchase" => "Select if customer can purchase this product or not. If yes than each customer can purchase it. If no then no customer see this package. Then this package is for particular customer that admin can only assign to that particular customer.",

	"product_add_form_status" => "Select the status for the package active or inactive.",
	/*End*/

	/*Product Details*/
	"product_add_form_can_resell" => "Define yes if reseller can resell it or no if admin create the package for his own purpose then reseller is unable to purchase the created package.

	If Admin wants to create personalized package then needs to set reseller can resell product “NO” so those product will not listed to reseller portal for the reselling purpose. This personalized product will be listed under the admin account so they can assign it to their customer manually.",

	"product_add_form_setup_fee" => "Setup fee for the package.

	Setup Fee is one time cost will be applicable to users at the first time when they will place order.",

	"product_add_form_billing_type" => "Select the billing type, if one time then the package will be assigned one time then terminate and if recurring is select then the package will renew as the billing days is defined.

	One Time - “One Time” package will not be renew automatically. it will be released after the defined days.

	Recurring - Package will be renewed automatically based on the defined days with respect to order date.

	Monthly - Monthly package will be renewed automatically by monthly with respect to order date.",

	"product_add_form_free_minutes" => "Free minute for the package.",

	"product_add_form_release_no_balance" => "What action should system take if at the time of package renewal customer account does not have enough fund.

	YES: It will release the package from customer account if they does not have enough balance to renew this product

	NO: It will apply the changes to customer account without taking care of balance and renew the product.",

	"product_add_form_commission" => "Commission is defined by admin, if reseller resell the admin package then what amount of commission needs to offer them it will be define here.",

	"product_add_form_price" => "Price for the package excluded TAX. Please make sure it can be recurring based on the Billing Type you have selected.",

	"product_add_form_billing_days" => "Total availability time for that package. customer can use this service for the defined days then it will be either renewed or it will be terminated.",

	"product_add_form_product_rate_group" => "Select the rate group of customer.",

	"product_add_form_pro_rate" => "Applicable rates will be based on selected type for premature termination of product",

	"product_add_form_applicable_for" => "Package applicable for Inbound,Outbound or both.",

	"product_add_form_apply_on_existing_account" => "Assign this product to existing accounts of selected rate group or not",

	"product_add_form_product_rate_group[]" => "Selected rategroup's existing account may or may not automatically assigned this product" ,
	/*End*/

	/*DID Category*/
	"product_add_form_number" => "Number to create DID.",

	"product_add_form_provider_id" => "Select the provider for the DID.",

	"product_add_form_city" => "City for the which DID is creating.",

	"product_add_form_province" => "Province for the which DID is creating.",
	/*End*/

	/*DID Category(Product Details)*/
	"product_add_form_connectcost" => "Connection fee to charge customer minimum when their call will be connected",

	"product_add_form_cost" => "Cost per minute",

	"product_add_form_inc" => "Rate of increment to calculate call cost.Example : 60 to charge every minute",

	"product_add_form_maxchannels" => "Its define how many calls are running on the same time.",

	"product_add_form_includedseconds" => "Define seconds will be free from the call duration for each call",

	"product_add_form_init_inc" => "Subsequent billing seconds charged after Initial Increment.",

	"product_add_form_leg_timeout" => "Call will automatically hang up after ringing call timeout seconds.",
	/*End*/

	/*Edit DID Category(Product Details)*/
	"product_edit_form_product_category" => "To defined product type please select the appropriate product category. Packages is a default category for all.",

	"product_edit_form_product_name" => "Name of package want to create.",

	"product_edit_form_country_id" => "Select the country so it will help to users for product identification when they are looking for specific product for specific country.",

	"product_edit_form_connectcost" => "Connection fee to charge customer minimum when their call will be connected",

	"product_edit_form_number" => "Number to create DID.",

	"product_edit_form_provider_id" => "Select the provider for the DID.",

	"product_edit_form_city" => "City for the which DID is creating.",

	"product_edit_form_province" => "Province for the which DID is creating.",

	"product_edit_form_status" => "Select the status for the package active or inactive.",

	"product_edit_form_cost" => "Cost per minute",

	"product_edit_form_inc" => "Rate of increment to calculate call cost.Example : 60 to charge every minute",

	"product_edit_form_maxchannels" => "Its define how many calls are running on the same time.",

	"product_edit_form_includedseconds" => "Define seconds will be free from the call duration for each call",

	"product_add_edit_init_inc" => "Subsequent billing seconds charged after Initial Increment.",

	"product_edit_form_leg_timeout" => "Call will automatically hang up after ringing call timeout seconds.",

	"product_edit_form_billing_days" => "Total availability time for that package. customer can use this service for the defined days then it will be either renewed or it will be terminated.",

	"product_edit_form_billing_type" => "Select the billing type, if one time then the package will be assigned one time then terminate and if recurring is select then the package will renew as the billing days is defined.",

	"product_edit_form_price" => "Price for the package excluded TAX. Please make sure it can be recurring based on the Billing Type you have selected.",

	"product_edit_form_setup_fee" => "Setup fee for the package.",

	"product_edit_form_product_buy_cost" => "Define the buy cost for the reseller or other entity.

	Buy Cost is product purchase price without TAX. so when customer or reseller will order this product at that time the TAX amount will be applicable additionally.",

	"product_edit_form_init_inc" => "Subsequent billing seconds charged after Initial Increment.",
	/*End*/
);

?>