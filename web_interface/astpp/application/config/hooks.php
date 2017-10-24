<?php

if (! defined ( 'BASEPATH' )) {
	exit ( 'No direct script access allowed' );
}
/*
 * | -------------------------------------------------------------------------
 * | Hooks
 * | -------------------------------------------------------------------------
 * | This file lets you define "hooks" to extend CI without hacking the core
 * | files. Please see the user guide for info:
 * |
 * | http://codeigniter.com/user_guide/general/hooks.html
 * |
 */
/*
 *
 * Purpose : Display logo based on domain name
 *
 */
$hook ['pre_system'] = array (
		'class' => 'Router',
		'function' => 'route',
		'filename' => 'router.php',
		'filepath' => 'hooks' 
);

$hook ['pre_controller'] = array (
		'class' => 'Router',
		'function' => 'config',
		'filename' => 'router.php',
		'filepath' => 'hooks' 
);
/*********************************************************/

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
