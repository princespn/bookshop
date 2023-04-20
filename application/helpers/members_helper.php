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
 * @param array $data
 * @return array
 */
function format_member_credit($data = array())
{
	$data['date'] = date('Y-m-d', get_time(now()));

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function generate_login_code($data = array())
{
	$CI =& get_instance();

	$code = config_item('encryption_key') . $CI->input->ip_address() . $data['member_id'] . $data['username'];

	return sha1($code);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return mixed
 */
function check_sponsor($str = '')
{
	$CI =& get_instance();

	return $CI->mem->check_sponsor($str);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return bool
 */
function check_member_id($str = '')
{
	$CI =& get_instance();

	$row = $CI->dbv->get_record(TBL_MEMBERS, 'member_id', (int)$str, TRUE);

	return !empty($row) ? FALSE : $row['member_id'];
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return false|string
 */
function check_password($str = '')
{
	return substr($str, 0, config_option('max_member_password_length'));
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return mixed
 */
function format_addresses($type = '', $data = array())
{
	$form[$type] = array($type . '_default' => '1');

	foreach ($data as $k => $v)
	{
		if (preg_match('/' . $type . '_*/', $k))
		{
			$k = str_replace($type . '_', '', $k);
			$form[$type][$k] = $v;
		}
		else
		{
			$form['account'][$k] = $v;
		}
	}

	return $form[$type];
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function init_member_template()
{
	$CI = &get_instance();

	$tpl = TPL_ADMIN_MEMBERS_VIEW;

	if ($CI->input->get('column') == 'points')
	{
		$tpl = TPL_ADMIN_MEMBER_POINTS_VIEW;
	}

	return $tpl;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function generic_user($data = array())
{
	$CI = &get_instance();

	$user = $CI->mem->random_username('user');
	$email = $CI->mem->random_email();

	$vars = array('fname'           => !empty($data['fname']) ? $data['fname'] : lang('new'),
	              'lname'           => !empty($data['lname']) ? $data['lname'] : lang('user'),
	              'username'        => !empty($data['username']) ? $data['username'] : $user,
	              'position'        => lang('contact'),
	              'date'     => get_time(now(), TRUE),
	              'primary_email'   =>  is_var($data, 'primary_email', FALSE, $email),
	              'profile_photo'   => is_var($data, 'profile_photo'),
	              'facebook_id'     => is_var($data, 'facebook_id'),
	              'twitter_id'      => is_var($data, 'twitter_id'),
	              'status'          => is_var($data, 'status'),
	              'email_confirmed' => is_var($data, 'email_confirmed'),
	);

	return format_member_data($vars);
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function set_default_address_data()
{
	//format default data for adding a new address so no errors show up
	$CI = &get_instance();

	$vars = list_fields(array(TBL_MEMBERS_ADDRESSES));
	$vars['regions_array'] = $CI->regions->load_country_regions(config_option('sts_site_default_country'), TRUE);
	$vars['country'] = $CI->config->item('sts_site_default_country');
	$vars['country_name'] = get_country_name(config_option('sts_site_default_country'), 'country_name');


	return $vars;
}

//format data posted from registration/checkout forms

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_member_data($data = array())
{
	$CI =& get_instance();

	//add username
	if (empty($data['username']))
	{
		$data['username'] = $CI->mem->random_username($data['fname']);
	}

	//add password
	if (empty($data['password']))
	{
		$data['password'] = random_string('alnum', config_item('default_member_password_length'));
	}

	//add sponsor if any
	if (config_item('affiliate_data'))
	{
		$data['original_sponsor_id'] = config_option('affiliate_data', 'member_id');
	}
	else
	{
		$data['original_sponsor_id'] = empty($data['sponsor_id']) ? get_sponsor('member_id') : (int)$data['sponsor_id'];
	}

	$data['sponsor_id'] = $CI->downline->get_downline_sponsor($data['original_sponsor_id']);

	//set ip address
	if (empty($data['last_login_ip']))
	{
		$data['last_login_ip'] = $CI->input->ip_address();
	}

	if (config_enabled(('sts_email_require_confirmation_on_signup')))
	{
		$data['confirm_id'] = confirm_id();
		$data['email_confirmed'] = is_var($data, 'email_confirmed');
	}

	//check for automatic affiliates
	$data['is_affiliate'] = config_enabled('sts_affiliate_admin_approval_required') ? '0' : config_item('sts_affiliate_enable_auto_affiliates');

	//check for mailing list box


	$data['date'] = get_time(now(), TRUE);

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @param string $id
 * @return array
 */
function format_social_data($data = array(), $type = '', $id = '')
{
	$vars = array('fname'           => is_var($data, 'firstName'),
	              'lname'           => is_var($data, 'lastName'),
	              'primary_email'   => is_var($data, 'email'),
	              'profile_photo'   => is_var($data, 'photoURL'),
	              'status'          => '1',
	              'email_confirmed' => '1',
	);

	if (!empty($id))
	{
		$vars['member_id'] = (int)$id;
	}

	switch ($type)
	{
		case 'Facebook':

			$vars['facebook_id'] = is_var($data, 'profileURL');

			break;

		case 'Twitter':

			$vars['twitter_id'] = is_var($data, 'profileURL');

			break;
	}


	return $vars;
}

/* End of file members_helper.php */
/* Location: ./application/helpers/members_helper.php */