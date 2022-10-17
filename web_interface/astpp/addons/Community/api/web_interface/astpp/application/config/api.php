<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 |--------------------------------------------------------------------------
 | API Auth Token
 |--------------------------------------------------------------------------
 |
 | API auth token variable (Keep it unique for each setup)
 | Header Parameter 
 | You can use http://passwordsgenerator.net/ to generate random key
 |
 |
 */
 $astpp_config = parse_ini_file ( "/var/lib/astpp/astpp-config.conf" );
 //Riya 3490 Mobile Dialer - Recharge functional is not working
 //Riya ASTPPENT-3993 API- Token keys ISSUE
 $config['api_x_auth_token'] = isset($astpp_config ['api_x_auth_token']) && $astpp_config ['api_x_auth_token'] != "<AUTH_KEY>
" ? $astpp_config ['api_x_auth_token'] :
     "nOcBurg5KvyurOri8A9ds3rXWafZ99Du";
 //end
/*
 |--------------------------------------------------------------------------
 | Token Key
 |--------------------------------------------------------------------------
 |
 | API token key to encrypt id
 | You can use http://passwordsgenerator.net/ to generate random key
 |
 |
 */
 //Riya 3490 Mobile Dialer - Recharge functional is not working
 //Riya ASTPPENT-3993 API- Token keys ISSUE
 $config['token_key'] = isset($astpp_config ['token_key']) && $astpp_config ['token_key'] != "<TOKEN_KEY>" ? $astpp_config ['token_key'] : "8CA7*^bnlAOMqR%?vYMBR62P8Ks1BN4L";
 //end
/*
 |--------------------------------------------------------------------------
 |  Iv Key
 |--------------------------------------------------------------------------
 |
 | API IV key (Change it if you want to make token generation process more stronger)
 | You can use http://passwordsgenerator.net/ to generate random key
 |
 |
 */
 //Riya 3490 Mobile Dialer - Recharge functional is not working
 //Riya ASTPPENT-3993 API- Token keys ISSUE
 $config['iv_key'] = isset($astpp_config ['iv_key']) && $astpp_config ['iv_key'] != "<IV_KEY>" ? $astpp_config ['iv_key'] : "UwCs*^jjkNU53u%?QjGb2CAycS3Wqg94";
 //end
/*
 |--------------------------------------------------------------------------
 |  Paypal Recharge Amounts
 |--------------------------------------------------------------------------
 |
 | Define paypal recharge amounts for mobile dialers
 | Amount needs to define in ASTPP System base currency
 |
 |
 */
$config['recharge_amounts'] = array(
		array('amount' => '5.00','description' => 'Recharge your account with #AMOUNT#'),
		array('amount' => '10.00','description' => 'Recharge your account with #AMOUNT#'),
		array('amount' => '20.00','description' => 'Recharge your account with #AMOUNT#'),
		array('amount' => '50.00','description' => 'Recharge your account with #AMOUNT#'),
		array('amount' => '100.00','description' => 'Recharge your account with #AMOUNT#')
);


/*
 |--------------------------------------------------------------------------
 |  URL to load in mobile app
 |--------------------------------------------------------------------------
 |
 | Define url which needs to be loaded in mobile app.
 |
 |
 */
$config['urls'] = array(
		'contact_us' => 'http://www.astppbilling.org/contact-us/',
		'about_us' => 'http://www.astppbilling.org/about-us/'
);

/*
 |--------------------------------------------------------------------------
 | api Default Language
 |--------------------------------------------------------------------------
 |
 | What language should the data be returned in by default?
 |
 |	Default: english
 |
 */
$config['api_default_language'] = 'english';

/*
|--------------------------------------------------------------------------
| api Format
|--------------------------------------------------------------------------
|
| What format should the data be returned in by default?
|
|	Default: xml
|
*/
$config['api_default_format'] = 'json';

/*
|--------------------------------------------------------------------------
| Enable emulate request
|--------------------------------------------------------------------------
|
| Should we enable emulation of the request (e.g. used in Mootools request)?
|
|	Default: false
|
*/
$config['enable_emulate_request'] = TRUE;

/*
|--------------------------------------------------------------------------
| Global IP Whitelisting
|--------------------------------------------------------------------------
|
| Limit connections to your api server to whitelisted IP addresses.
|
| Usage:
| 1. Set to true *and* select an auth option for extreme security (client's IP
|	 address must be in whitelist and they must also log in)
| 2. Set to true with auth set to false to allow whitelisted IPs access with no login.
| 3. Set to false here but set 'auth_override_class_method' to 'whitelist' to
|	 apirict certain methods to IPs in your whitelist
|
*/
$config['api_ip_whitelist_enabled'] = false;

/*
|--------------------------------------------------------------------------
| api IP Whitelist
|--------------------------------------------------------------------------
|
| Limit connections to your api server to a comma separated
| list of IP addresses
|
| Example: $config['api_ip_whitelist'] = '123.456.789.0, 987.654.32.1';
|
| 127.0.0.1 and 0.0.0.0 are allowed by default.
|
*/
$config['api_ip_whitelist'] = '';

/*
|--------------------------------------------------------------------------
| api Ignore HTTP Accept
|--------------------------------------------------------------------------
|
| Set to TRUE to ignore the HTTP Accept and speed up each request a little.
| Only do this if you are using the $this->api_format or /format/xml in URLs
|
|	FALSE
|
*/
$config['api_ignore_http_accept'] = FALSE;

/*
|--------------------------------------------------------------------------
| api AJAX Only
|--------------------------------------------------------------------------
|
| Set to TRUE to only allow AJAX requests. If TRUE and the request is not
| coming from AJAX, a 505 response with the error message "Only AJAX
| requests are accepted." will be returned. This is good for production
| environments. Set to FALSE to also accept HTTP requests.
|
|	FALSE
|
*/
$config['api_ajax_only'] = FALSE;

/*
 * No need to configure it here now.  
 */

/*
 |--------------------------------------------------------------------------
 |  Paypal client id for mobile dialers
 |--------------------------------------------------------------------------
 |
 | Set Paypal Client Id
 |
 |
 */
//$config['paypal_clientid'] = 'AesmXG3FHp7YwmSyNVdfNWA55PCLuzB-BvZXEx45ja71I-NOUXj3-Td7QsXgUwExBFPcmzwdiMK3mfqS';

/*
 |--------------------------------------------------------------------------
 |  Paypal Secret Key for mobile dialers
 |--------------------------------------------------------------------------
 |
 | Set Paypal Secret key
 |
 |
 */
//$config['paypal_secret_key'] = 'EMYS2nmaEejZqIHnM85CnS6ogKS4XuPKm6tsnAX0iT_RlKlDpcM-v-VrIQ-3lTdsYQyZm4dv-IGeLbh1';

/*
 |--------------------------------------------------------------------------
 |  IOS Inapp purchase production type
 |--------------------------------------------------------------------------
 |
 | Set IOS Inapp purchase production type (Sandbox / Production)
 |
 |
 */
//$config['ios_inapp_production_type'] = 'Sandbox';

/*
 |--------------------------------------------------------------------------
 |  IOS Inapp secret
 |--------------------------------------------------------------------------
 |
 | Set IOS Inapp purchase secret
 |
 |
 */
//$config['ios_inapp_secret'] = 'inapp_secret';

/*
 |--------------------------------------------------------------------------
 | api log enable
 |--------------------------------------------------------------------------
 |
 | Do you want to enable debug log?
 |
 |	Default: FALSE
 |
 */
//$config['api_debug_log'] = TRUE;


/* End of file config.php */
/* Location: ./system/application/config/api.php */
