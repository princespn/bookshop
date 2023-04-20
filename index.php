<?php
/**
 *
 * Copyright (c) 2007-2020, JROX Technologies, Inc.
 *
 * This script may be only used and modified in accordance to the license
 * agreement attached (license.txt) except where expressly noted within
 * commented areas of the code body. This copyright notice and the
 * comments above and below must remain intact at all times.  By using this
 * code you agree to indemnify JROX Technologies, Inc, its corporate agents
 * and affiliates from any liability that might arise from its use.
 *
 * Selling the code for this program without prior written consent is
 * expressly forbidden and in violation of Domestic and International
 * copyright laws.
 *
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
define('PUBPATH', realpath(dirname(__FILE__)));
define('HOMEPATH', realpath(dirname(dirname(__FILE__))));

$system_path = 'system';

//$system_path = '../system';

$application_folder = 'application';
//$application_folder = '../application';

$view_folder = '';


// Set the current directory correctly for CLI requests
if (defined('STDIN'))
{
	chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE)
{
	$system_path = $_temp . '/';
}
else
{
	// Ensure there's a trailing slash
	$system_path = rtrim($system_path, '/') . '/';
}

// Is the system path correct?
if (!is_dir($system_path))
{
	header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
	echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: ' . pathinfo(__FILE__, PATHINFO_BASENAME);
	exit(3); // EXIT_CONFIG
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// Path to the system folder
define('BASEPATH', str_replace('\\', '/', $system_path));

// Path to the front controller (this file)
define('FCPATH', dirname(__FILE__) . '/');

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

// The path to the "application" folder
if (is_dir($application_folder))
{
	if (($_temp = realpath($application_folder)) !== FALSE)
	{
		$application_folder = $_temp;
	}

	define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);
}
else
{
	if (!is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
		exit(3); // EXIT_CONFIG
	}

	define('APPPATH', BASEPATH . $application_folder . DIRECTORY_SEPARATOR);
}

// The path to the "views" folder
if (!is_dir($view_folder))
{
	if (!empty($view_folder) && is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR))
	{
		$view_folder = APPPATH . $view_folder;
	}
	elseif (!is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
		exit(3); // EXIT_CONFIG
	}
	else
	{
		$view_folder = APPPATH . 'views';
	}
}

if (($_temp = realpath($view_folder)) !== FALSE)
{
	$view_folder = $_temp . DIRECTORY_SEPARATOR;
}
else
{
	$view_folder = rtrim($view_folder, '/\\') . DIRECTORY_SEPARATOR;
}

define('VIEWPATH', $view_folder);

require_once(APPPATH . '/config/debug.php');

require_once APPPATH . 'vendor/autoload.php';

//add gloabl defines
if (file_exists(APPPATH . '/config/defines.php'))
{
	require_once(APPPATH . '/config/defines.php');
}
if (file_exists(APPPATH . '/config/admin_defines.php'))
{
	require_once(APPPATH . '/config/admin_defines.php');
}

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
		break;

	case 'testing':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}

		break;

	case 'production':
		ini_set('display_errors', 0);
		error_reporting(0);

		break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}

require_once BASEPATH . 'core/CodeIgniter.php';
