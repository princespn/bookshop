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
 * @param string $str
 * @return mixed
 */
function check_region($str = '')
{
	$CI =& get_instance();

	return $CI->regions->check_region($str);
}

// ------------------------------------------------------------------------

/**
 * @return bool|mixed|string
 */
function get_region_title()
{
	$CI = &get_instance();

	$title = lang('manage_regions');

	if ($CI->input->get('country_id'))
	{
		return get_country_name((int)$CI->input->get('country_id'), 'country_name');
	}

	return $title;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param bool $format_array
 * @return mixed
 */
function load_regions($id = '', $format_array = TRUE)
{
	$CI = &get_instance();

	return $CI->regions->load_country_regions($id, $format_array);
}

// ------------------------------------------------------------------------

/**
 * @param string $num
 * @param string $col
 * @param string $id
 * @return string
 */
function get_region_name($num = '', $col = '', $id = 'region_id')
{
	$CI =& get_instance();

	$CI->db->where($id, $num);
	$query = $CI->db->get('regions');

	if ($query->num_rows() > 0)
	{
		$cn = $query->row_array();

		$data = !empty($col) ? $cn[ $col ] : $cn;

		return $data;
	}

	return $num;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $ship
 * @return bool|mixed
 */
function match_region_rates($data = array(), $ship = array())
{
	foreach ($data as $v)
	{
		//check if we have a match
		if (!empty($v['zone_id']))
		{
			if (!empty($ship))
			{
				foreach ($ship as $z)
				{
					if ($z['zone_id'] == $v['zone_id'])
					{
						return $v;
					}
				}
			}
		}
	}

	return FALSE;
}

function check_proximity($data = array())
{
	if (!empty($data[0]))
	{
		return $data[0];
	}
	elseif (!empty($data[1]))
	{
		return $data[1];
	}
	elseif (!empty($data[2]))
	{
		 return $data[2];
	}

	return FALSE;
}

/* End of file regions_helper.php */
/* Location: ./application/helpers/regions_helper.php */