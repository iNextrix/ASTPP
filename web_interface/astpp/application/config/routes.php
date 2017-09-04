<?php

if (! defined ( 'BASEPATH' )) {
	exit ( 'No direct script access allowed' );
}
/*
 * | -------------------------------------------------------------------------
 * | URI ROUTING
 * | -------------------------------------------------------------------------
 * | This file lets you re-map URI requests to specific controller functions.
 * |
 * | Typically there is a one-to-one relationship between a URL string
 * | and its corresponding controller class/method. The segments in a
 * | URL normally follow this pattern:
 * |
 * | example.com/class/method/id/
 * |
 * | In some instances, however, you may want to remap this relationship
 * | so that a different class/function is called than the one
 * | corresponding to the URL.
 * |
 * | Please see the user guide for complete details:
 * |
 * | http://codeigniter.com/user_guide/general/routing.html
 * |
 * | -------------------------------------------------------------------------
 * | RESERVED ROUTES
 * | -------------------------------------------------------------------------
 * |
 * | There area two reserved routes:
 * |
 * | $route['default_controller'] = 'welcome';
 * |
 * | This route indicates which controller class should be loaded if the
 * | URI contains no data. In the above example, the "welcome" class
 * | would be loaded.
 * |
 * | $route['404_override'] = 'errors/page_missing';
 * |
 * | This route will tell the Router what URI segments to use if those provided
 * | in the URL cannot be matched to a valid route.
 * |
 */

$route ['default_controller'] = "login";
$route ['404_override'] = '';
$route ['getbalance/(:any)'] = "getbalance/index/$1";

$route ['settings/configuration'] = "systems/configuration/";
$route ['settings/configuration_json'] = "systems/configuration_json/";
/*
 * ASTPP 3.0
 * routes create status show on/off
 */
$route ['get_status/(:any)'] = "getstatus/customer_list_status/$1";
/**
 * ********************************************
 */

$route ['forgotpassword'] = "signup/forgotpassword";
$route ['confirmpassword'] = "signup/confirmpassword";
$route ['confirm_pass'] = "signup/confirm_pass";
$route ['confirmpass'] = "signup/confirmpass";

$route ['signup'] = "signup/index";
$route ['signup/(:any)'] = "signup/index/$1";

$route ['signup/signup_save'] = "signup/signup_save";
$route ['signup/signup_success'] = "signup/signup_success";
$route ['signup/signup_confirm'] = "signup/signup_confirm";
$route ['signup/signup_inactive'] = "signup/signup_inactive";

$route ['logout'] = "login/logout/";




// $route['settings/configuration/(:any)'] = "systems/configuration/$1";


/* End of file routes.php */
/* Location: ./application/config/routes.php */
