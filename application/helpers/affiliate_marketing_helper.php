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
 * @package      eCommerce Suite
 * @author       JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link         https://www.jrox.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function check_affiliate()
{
	if (sess('username') && sess('is_affiliate') == '1')
	{
		return TRUE;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 */
function init_affiliate_store($data = array())
{
	$CI = &get_instance();

	$row = module_enabled('affiliate_stores', 'affiliate_marketing');

	if (!empty($row))
	{
		$CI->db->where('member_id', (int)$data['member_id']);
		$CI->db->where('status', '1');
		if (!$q = $CI->db->get('module_affiliate_marketing_affiliate_stores'))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$CI->session->set_userdata('affiliate_store', TRUE);

			$CI->db->where('member_id', $data['member_id']);

			if (!$q = $CI->db->get('module_affiliate_marketing_affiliate_stores_products'))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$p = array();

				foreach ($q->result_array() as $v)
				{
					$p[] = $v['product_id'];
				}

				$CI->session->set_userdata('affiliate_store_products', $p);
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param bool $lang
 * @return bool|string
 */
function affiliate_store_button($id = '', $lang = TRUE)
{
	if (sess('affiliate_store'))
	{
		if (is_array($_SESSION['affiliate_store_products']) && in_array($id, $_SESSION['affiliate_store_products']))
		{
			$a = '<a href="' . site_url('shop/affiliate_store/remove/' . $id) . '" class="btn btn-danger remove-affiliate-button">
			<i class="fa fa-minus-circle"></i> <span class="d-none d-md-inline-block"> ';

			if ($lang == TRUE)
			{
				$a .= lang('remove_from_your_store');
			}

			$a .= '</span></a>';
		}
		else
		{
			$a = '<a href="' . site_url('shop/affiliate_store/add/' . $id) . '" class="btn btn-success add-affiliate-button">
			<i class="fa fa-plus-circle"></i> <span class="d-none d-md-inline-block"> ';

			if ($lang == TRUE)
			{
				$a .= lang('add_to_affiliate_store');
			}

			$a .= '</span></a>';
		}

		return $a;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $append
 * @return string
 */
function aff_tools_url($append = '')
{
	$username = sess('username');

	return site_url($username) . '/'. $append;
}

// ------------------------------------------------------------------------

function rel()
{
	return config_enabled('enable_no_follow_links') ? 'rel="nofollow"' : 'rel="sponsored"';
}

// ------------------------------------------------------------------------

/**
 * @param string $username
 * @param string $append
 * @return string
 */
function affiliate_url($username = '', $append = '')
{
	if (empty($username))
	{
		$username = sess('username');
	}

	switch (config_item('sts_affiliate_link_type'))
	{
		case 'regular':

			$url = '{{site_url}}{{username}}';

			break;

		case 'subdomain':

			$url = DEFAULT_AFFILIATE_LINK_PROTOCOL . '{{username}}.' . config_item('base_domain');

			break;

		case 'custom':

			$url = config_item('sts_affiliate_custom_link');

			break;
	}

	$url = str_replace('{{username}}', $username, $url);
	$url = str_replace('{{site_url}}', site_url(), $url);

	return $url . $append;
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function show_profile()
{
	if (config_enabled('sts_affiliate_show_widget_profiles'))
	{
		if (config_option('affiliate_data', 'member_id'))
		{
			return TRUE;
		}
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool|false|string
 */
function check_referral_data($data = array())
{
	$CI =& get_instance();

	if (!empty($data['original_sponsor_id']))
	{
		$vars = $CI->aff->get_affiliate_data($data['original_sponsor_id'], 'member_id');
	}

	return !empty($vars) ? sc($vars) : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $sub
 * @return bool
 */
function validate_subdomain($sub = '')
{
	$CI =& get_instance();

	$subs = explode(',', $CI->config->item('sts_affiliate_restrict_subdomains'));

	foreach ($subs as $v)
	{
		if (trim($v) == trim($sub))
		{
			return FALSE;
		}
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_traffic_data($data = array())
{
	$CI =& get_instance();

	$vars = array('date'          => get_time(now(), TRUE),
	              'member_id'     => $data['member_id'],
	              'tracking_code' => $data['tracking_code'],
	              'tool_type'     => is_var($data, 'tool_type'),
	              'tool_id'       => is_var($data, 'tool_id'),
	              'referrer'      => $CI->agent->referrer(),
	              'ip_address'    => $CI->input->ip_address(),
	              'user_agent'    => $CI->agent->agent_string(),
	              'os'            => $CI->agent->platform(),
	              'browser'       => $CI->agent->browser(),
	              'isp'           => gethostbyaddr($CI->input->ip_address()),
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param $v
 * @return string
 */
function tools_photo($v)
{
	$CI =& get_instance();

	if (file_exists(PUBPATH . '/images/modules/module_' . $v['module_type'] . '_' . $v['module_file_name'] . '.' . $CI->config->item('member_marketing_tool_ext')))
	{
		$url = base_url('images/modules/module_' . $v['module_type'] . '_' . $v['module_file_name'] . '.' . $CI->config->item('member_marketing_tool_ext'));
	}
	else
	{
		$url = base_url('images/modules/tools.png');
	}

	return img($url, $v['module_file_name'], 'class="img-responsive img-thumbnail"');
}

// ------------------------------------------------------------------------

/**
 * @param string $col
 * @return bool|mixed
 */
function get_sponsor($col = 'member_id')
{
	if (config_item('affiliate_data'))
	{
		$aff = config_item('affiliate_data');

		return !empty($col) ? $aff[$col] : $aff;
	}

	return FALSE;
}

/* End of file affiliate_marketing_helper.php */
/* Location: ./application/helpers/affiliate_marketing_helper.php */