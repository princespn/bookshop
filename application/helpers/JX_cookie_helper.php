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
 * @return bool
 */
function check_file_manager_cookie()
{
	if (get_cookie('FM-' . config_item('sess_adm_cookie_name')))
	{
		return TRUE;
	}

	set_file_manager_cookie();
}

// ------------------------------------------------------------------------

/**
 *
 */
function set_file_manager_cookie()
{
	$CI = &get_instance();

	$value = sha1($CI->input->ip_address() . config_item('encryption_key'));

	$cookie = array('name'   => 'FM-' . config_item('sess_adm_cookie_name'),
	                'value'  => $value,
	                'expire' => 0,
	);

	//set the affiliate cookie
	set_cookie($cookie);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function set_tracking_cookie($data = array())
{
	$CI = &get_instance();

	$c_array = array(
		$data['member_id'],
		$data['username'],
		$CI->aff->generate_tracking_code($data, TRUE));

	$c_string = implode('-', $c_array);

	$cookie_value = config_option('encrypt_tracking_data') == TRUE ? $CI->encryption->encrypt($c_string) : $c_string;

	set_cookie(array('name'   => $CI->config->item('tracking_cookie_name'),
	                 'value'  => $cookie_value,
	                 'expire' => 60 * 60 * 24 * $CI->config->item('sts_affiliate_cookie_timer'),
	));

	return $c_string;
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function check_cookie_consent()
{
	if (config_enabled('sts_site_enable_eu_cookie_modal'))
	{
		if (get_cookie(COOKIE_CONSENT) == FALSE)
		{
			return TRUE;
		}

	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $int
 * @param string $type
 * @param string $format
 * @return false|string
 */
function set_date($int = '', $type = 'year', $format = 'D, d M Y H:i:s')
{
	$add = '+' . $int . ' ' . $type;
	return date($format, strtotime($add));
}

// ------------------------------------------------------------------------

/**
 * @param string $field
 * @return array|bool|mixed
 */
function get_affiliate_cookie($field = '')
{
	$CI = &get_instance();

	$data = array();
	if ($c = get_cookie(config_option('tracking_cookie_name')))
	{
		//validate cookie data
		if (config_option('encrypt_tracking_data') == TRUE)
		{
			$c = $CI->encryption->decrypt($c);
		}

		list($data['member_id'], $data['username'], $data['tracking_code']) = explode('-', $c);

		return !empty($field) ? $data[$field] : $data;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function set_age_verification_cookie()
{
	set_cookie(array('name'   => config_item('age_restricted_cookie_name'),
	                 'value'  => TRUE,
	                 'expire' => '0',
	));

	return TRUE;
}

/* End of file JX_cookie_helper.php */
/* Location: ./application/helpers/JX_cookie_helper.php */