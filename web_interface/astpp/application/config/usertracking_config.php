<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| User Identifier
|--------------------------------------------------------------------------
|
| Indicate an existing model, library, or helper function that returns the
| current user's identification.  If you leave this blank or null, then the 
| tracking system will not add records for userIdentifers.
|
| Use the following format:
|   For Helper Function:    $config['userTracking']['userIdentifier'] = array("helper", "helperName", "helperFunction", "argument array"); 
|   For Library Function:   $config['userTracking']['userIdentifier'] = array("library", "libraryName", "libraryFunction", "argument array");
|   For Helper Function:    $config['userTracking']['userIdentifier'] = array("model", "modelName", "modelFunction", "argument array");
|
| If you don't leave this blank/null, and the function fails, the system will add a record to the database,
| but the userIdentifer field will contain the string *ERROR RETRIEVING USER IDENTIFIER*
|
| default: null
*/
$config['usertracking']['user_identifier'] = NULL;



/*
|--------------------------------------------------------------------------
| AutoTracking
|--------------------------------------------------------------------------
|
| Setting this option to true will cause the tracking system to turn on
| without having to manually initialize it in the controller.
|
| You must have the following code added to your config/hooks.php file:
|
|   $hook['post_controller_constructor'][] = array('class' => 'Usertracking', 
|                                                  'function' => 'autoTrack',
|                                                  'filename' => 'Usertracking.php',
|                                                  'filepath' => 'libraries');
|
| default: false
*/
$config['usertracking']['auto_track'] = FALSE;



/*
|--------------------------------------------------------------------------
| AutoTracking Filter
|--------------------------------------------------------------------------
|
| Indicate existing model, library, or helper functions that must evaluate to an
| expected result in order for the tracking to function.
|     e.g. only track users if(isLoggedIn() == true), or something like that.
|
| Use the following format:
|   For Helper Function:    $config['userTracking']['trackingFilter'][] = array("helper", "helperName", "helperFunction", "expectedResult", [optional] "argument array"); 
|   For Library Function:   $config['userTracking']['trackingFilter'][] = array("library", "libraryName", "libraryFunction", "expectedResult", [optional] "argument array");
|   For Helper Function:    $config['userTracking']['trackingFilter'][] = array("model", "modelName", "modelFunction", "expectedResult", [optional] "argument array");
|
| Example of a filter that calls the isLoggedIn() function from the generalHelper, send it no
| arguments, and expects a result of true in order to enable tracking for the page that's being loaded
|   $config['userTracking']['trackingFilter'][] = array("helper", "generalHelper", "isLoggedIn", true);
|
| default: [] = null
*/
$config['usertracking']['tracking_filter'][] = NULL;


/*
|--------------------------------------------------------------------------
| AutoTracking Filter Logic
|--------------------------------------------------------------------------
|
| If you have multiple trackingFilter array elements, then you can define
| the boolean logic.  "AND" logic will require all functions to return TRUE,
| while "OR" logic will require only one function of the ones listed to return
| TRUE.
|
| default: "OR"
*/
$config['usertracking']['tracking_filter_logic'] = "OR";



/*
|--------------------------------------------------------------------------
| AutoBuild Database
|--------------------------------------------------------------------------
|
| Setting this option to true will cause UserTracking to automatically
| build the database required database tables if they do not already exist.
|
| default: true
*/
$config['usertracking']['auto_build_db'] = TRUE;


/*
|--------------------------------------------------------------------------
| AutoFix Database
|--------------------------------------------------------------------------
|
| Setting this option to true will cause UserTracking to automatically fix
| existing required database tables that are not correctly setup.
|
| If the plugin determines that there is a malforme database table, it will back
| it up and create a new, blank table with the proper columns.
|
| default: true
*/
$config['usertracking']['auto_fix_db'] = TRUE;


?>