<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "login";
$route['404_override'] = '';
$route['getbalance/(:any)'] = "getbalance/index/$1";

$route['settings/configuration'] = "systems/configuration/";
$route['settings/configuration_json'] = "systems/configuration_json/";

// $route['rategroup/rategroup_list'] = "pricing/price_list/";
// $route['rategroup/rategroup_list_json'] = "pricing/price_list_json/";


// $route['configuration/taxes_list'] = "taxes/taxes_list/";
// $route['configuration/taxes_list_json'] = "taxes/taxes_list_json/";
// $route['configuration/taxes_edit/(:any)'] = "taxes/taxes_edit/$1";
// $route['configuration/taxes_delete/(:any)'] = "taxes/taxes_delete/$1";


$route['logout'] = "login/logout/";




// $route['settings/configuration/(:any)'] = "systems/configuration/$1";


/* End of file routes.php */
/* Location: ./application/config/routes.php */
