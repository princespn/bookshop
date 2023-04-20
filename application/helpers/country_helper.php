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
 * @param array $data
 * @param bool $format_array
 * @param string $lang
 * @return array
 */
function show_country_regions($data = array(), $format_array = FALSE, $lang = 'all_regions')
{
	$t = count($data) + 1;
	if ($format_array == TRUE)
	{
		$data[ $t ] = (array('country_id' => '0', 'country_name' => lang($lang)));
	}
	else
	{
		$data[0] = lang($lang);
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $num
 * @param string $col
 * @param string $id
 * @return bool
 */
function get_country_name($num = '', $col = '', $id = 'country_id')
{
	$CI =& get_instance();

	$CI->db->where($id, $num);
	$query = $CI->db->get('countries');

	if ($query->num_rows() > 0)
	{
		$cn = $query->row_array();

		$data = !empty($col) ? $cn[ $col ] : $cn;

		return $data;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $countries
 * @param bool $all
 * @param string $text
 * @return array|false
 */
function load_countries_dropdown($countries = '', $all = FALSE, $text = 'all_countries')
{
	$CI =& get_instance();

	$a = array();
	$b = array();

	if ($all == TRUE)
	{
		array_push($a, '');
		array_push($b, $CI->lang->line($text));
	}

	foreach ($countries as $value)
	{
		array_push($a, $value['country_id']);
		array_push($b, $value['country_name']);
	}

	$c = array_combine($a, $b);

	return $c;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @return bool
 */
function check_ship_to($id = '')
{
	$countries = load_countries_array(TRUE);

	foreach ($countries as $v)
	{
		if ($id == $v['country_id'])
		{
			return TRUE;
		}
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return mixed
 */
function check_country($str = '')
{
	$CI =& get_instance();

	return $CI->country->check_country($str);
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @return array
 */
function get_default_country($id = '')
{
	$CI =& get_instance();

	$cid = empty($id) ? $CI->config->item('sts_site_default_country') : $id;

	$c = get_country_name($cid);

	$vars = array(
		'regions_array' => $CI->regions->load_country_regions($c['country_id'], TRUE),
		'country_array' => array($c['country_id'] => $c['country_name']),
	);

	return $vars;
}


/* End of file country_helper.php */
/* Location: ./application/helpers/country_helper.php */