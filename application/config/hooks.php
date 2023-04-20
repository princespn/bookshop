<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller'] = array(
	'class'    => 'Init_config',
	'function' => 'finalize',
	'filename' => 'Init_config.php',
	'filepath' => 'hooks',
);

if (file_exists(APPPATH . 'config/custom_hooks.php'))
{
	require_once(APPPATH . 'config/custom_hooks.php');
}

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */