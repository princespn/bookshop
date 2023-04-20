<?php
$version = "9.14.0";
if (session_id() == '') session_start();

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');
date_default_timezone_set('Europe/Rome');
setlocale(LC_CTYPE, 'en_US'); //correct transliteration

define('BASEPATH', TRUE);
define('PUBPATH', realpath(dirname(__FILE__)));

if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/application/config/config.php'))
{
	$full_path = dirname(dirname(dirname(dirname(__FILE__))));
}
else
{
	$full_path = dirname(dirname(dirname(__FILE__)));
}

require_once ($full_path . '/application/config/config.php');
require_once ($full_path . '/application/config/admin_defines.php');
require_once ($full_path . '/application/config/filemanager.php');

if ($config['check_file_manager_cookie'] == TRUE)
{
	if (!empty($_COOKIE['FM-' . $config['sess_adm_cookie_name']]))
	{
		$value = sha1($_SERVER['REMOTE_ADDR'] . $config['encryption_key']);

		if ($_COOKIE['FM-' . $config['sess_adm_cookie_name']] != $value)
		{
			die('Invalid Key.  Please Login');
		}
	}
	else
	{
		die('Invalid Access. Please Login Via Admin Again');
	}
}



/*
|--------------------------------------------------------------------------
| Optional security
|--------------------------------------------------------------------------
|
| if set to true only those will access RF whose url contains the access key(akey) like:
| <input type="button" href="../filemanager/dialog.php?field_id=imgField&lang=en_EN&akey=myPrivateKey" value="Files">
| in tinymce a new parameter added: filemanager_access_key:"myPrivateKey"
| example tinymce config:
|
| tiny init ...
| external_filemanager_path:"../filemanager/",
| filemanager_title:"Filemanager" ,
| filemanager_access_key:"myPrivateKey" ,
| ...
|
*/

define('USE_ACCESS_KEYS', true); // TRUE or FALSE

/*
|--------------------------------------------------------------------------
| DON'T COPY THIS VARIABLES IN FOLDERS config.php FILES
|--------------------------------------------------------------------------
*/

define('DEBUG_ERROR_MESSAGE', true); // TRUE or FALSE

/*
|--------------------------------------------------------------------------
| Path configuration
|--------------------------------------------------------------------------
| In this configuration the folder tree is
| root
|    |- source <- upload folder
|    |- thumbs <- thumbnail folder [must have write permission (755)]
|    |- filemanager
|    |- js
|    |   |- tinymce
|    |   |   |- plugins
|    |   |   |   |- responsivefilemanager
|    |   |   |   |   |- plugin.min.js
*/



return array_merge(
	$config,
	array(
		'ext'=> array_merge(
			$config['ext_img'],
			$config['ext_file'],
			$config['ext_misc'],
			$config['ext_video'],
			$config['ext_music']
		),
		// For a list of options see: https://developers.aviary.com/docs/web/setup-guide#constructor-config
		'aviary_defaults_config' => array(
			'apiKey'     => $config['aviary_apiKey'],
			'language'   => $config['aviary_language'],
			'theme'      => $config['aviary_theme'],
			'tools'      => $config['aviary_tools'],
			'maxSize'    => $config['aviary_maxSize']
		),
	)
);
?>
