<?php 
$config = array (
	
	$route ['personalized_rates/personalized_rates_list'] = "custom_rates/custom_rates_list/",
	$route ['personalized_rates/personalized_rates_list_json'] = "custom_rates/custom_rates_list_json/",
	$route ['personalized_rates/personalized_rates_add'] = "custom_rates/custom_rate_add/",
	$route ['personalized_rates/personalized_rates_save'] = "custom_rates/custom_rate_save/",
	$route ['personalized_rates/personalized_rates_delete_multiple'] = "custom_rates/custom_rate_delete_multiple/",
	$route ['personalized_rates/personalized_rates_export_cdr_xls'] = "custom_rates/custom_rate_export_cdr_xls/",
	$route ['personalized_rates/personalized_rates_list_search'] = "custom_rates/custom_rates_list_search/",
	$route ['personalized_rates/personalized_rates_edit/(:any)'] = "custom_rates/custom_rate_edit/$1",
	$route ['personalized_rates/personalized_rates_list_json'] = "custom_rates/custom_rates_list_json/",

	$route ['personalized_rates/personalized_rates_(:any)'] = "custom_rates/custom_rate_$1/",
	$route ['personalized_rates/personalized_rates_batch_update'] = "custom_rates/custom_rate_batch_update",
	$route ['personalized_rates/personalized_rates_delete'] = "custom_rates/custom_rate_delete/",
	$route ['personalized_rates/personalized_rates_list_clearsearchfilter'] = "custom_rates/custom_rates_list_clearsearchfilter/",

	//For Reseller
	$route ['personalized_rates/resellersrates_xls'] = "custom_rates/resellersrates_xls/",
);
?>