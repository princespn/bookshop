<?php defined('BASEPATH') OR exit('No direct script access allowed');
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

// ------------------------------------------------------------------------

/**
 * @return array
 */
function check_requirements()
{
	$data = array();

	$data['errors'] = '';
	$data['success'] = '';

	if (phpversion() < '5.4')
	{
		$data['errors'] .= install_alert('php_54_required', 'danger');
	}
	else
	{
		$data['success'] .= install_alert('php_version_is_good - ' . phpversion());
	}

	if (!extension_loaded('mysqli'))
	{
		$data['errors'] .= install_alert('mysql_required', 'danger');
	}
	else
	{
		$data['success'] .= install_alert('mysql_installed');
	}

	if (!extension_loaded('curl'))
	{
		$data['errors'] .= install_alert('curl_extension_required', 'danger');
	}
	else
	{
		$data['success'] .= install_alert('curl_installed');
	}

	//check if the files are writable
	if (!is_writable(APPPATH . 'config/config.php'))
	{
		$data['errors']  .=  install_alert(APPPATH . 'config/config.php' . ' ' . lang('file_not_writable') . ' chmod(777)', 'danger');
	}
	else
	{
		$data['success'] .= install_alert(APPPATH . 'config/config.php' . ' ' . lang('file_is_writable'));
	}

	if (!is_writable(APPPATH . 'config/database.php'))
	{
		$data['errors']  .=  install_alert(APPPATH . 'config/database.php' . ' ' . lang('file_not_writable') . ' chmod(777)', 'danger');
	}
	else
	{
		$data['success'] .= install_alert(APPPATH . 'config/database.php' . ' ' . lang('file_is_writable'));
	}

	if (!file_exists(FCPATH . '/install_files/install.sql'))
	{
		$data['errors']  .=  install_alert(lang('please_upload_all_install_files'), 'danger');
	}
	elseif (!file_exists(FCPATH . '/install_files/config_sample.txt'))
	{
		$data['errors']  .=  install_alert(lang('please_upload_all_install_files'), 'danger');
	}
	elseif (!file_exists(FCPATH . '/install_files/database_sample.txt'))
	{
		$data['errors']  .=  install_alert(lang('please_upload_all_install_files'), 'danger');
	}
	elseif (!file_exists(FCPATH . '/install_files/cpaneluapi.class.php'))
	{
		$data['errors']  .=  install_alert(lang('please_upload_all_install_files'), 'danger');
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function install_url()
{
	$pageURL = 'http';
	if (!empty($_SERVER["HTTPS"]) == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

// ------------------------------------------------------------------------

/**
 * @param int $len
 * @return false|string
 */
function random_install_string($len = 8)
{
	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#%^&*()_+={[}]<>:;';
	return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
}

// ------------------------------------------------------------------------

/**
 * @param $file
 * @return bool|false|string
 */
function read_install_file($file)
{
	if (function_exists('file_get_contents'))
	{
		return file_get_contents($file);
	}

	if ( ! $fp = @fopen($file, FOPEN_READ))
	{
		return FALSE;
	}

	flock($fp, LOCK_SH);

	$data = '';
	if (filesize($file) > 0)
	{
		$data =& fread($fp, filesize($file));
	}

	flock($fp, LOCK_UN);
	fclose($fp);

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return string
 */
function get_install_admin_url($data = array(), $type = 'install')
{
	$url = $data['base_domain'];

	if (!empty($data['base_sub_domain']))
	{
		$url = $data['base_sub_domain'] . '.' . $data['base_domain'];
	}
	if (!empty($data['base_folder_path']))
	{
		$url .= $data['base_folder_path'];
	}

	$p = $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';

	return $p . '://' . $url . '/' . $type;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @return mixed|string
 */
function format_folder_path($type = 'install')
{
	$folder = '';
	$page_data = parse_url(install_url());

	if (!empty($page_data['path']))
	{
		$folder = str_replace('/' . $type, '', $page_data['path']);
		$folder = rtrim($folder, '/');
	}

	return $folder;
}

// ------------------------------------------------------------------------

/**
 * @param string $domain
 * @return false|mixed|string
 */
function format_install_domain($domain = '')
{
	if (empty($domain))
	{
		$domain = $_SERVER["SERVER_NAME"];
	}

	$url = parse_url($domain,  PHP_URL_PATH);

	if (substr($url, 0,4) == 'www.')
	{
		$url = substr($url, 4);
	}

	return $url;
}

// ------------------------------------------------------------------------

/**
 * @param string $msg
 * @param string $alert
 * @return string
 */
function install_alert($msg = '', $alert = 'success')
{
	$html = '<div ><h4 class="text-' . $alert . '">';

	$alert == 'success' ? $html .= i('fa fa-check-circle') : $html .= i('fa fa-times-circle');

	$html .= ' ' . lang($msg);
	$html .= '</h4></div>';

	return $html;
}

/* End of file install_helper.php */
/* Location: ./application/helpers/install_helper.php */