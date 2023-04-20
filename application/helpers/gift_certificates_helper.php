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
 * @param array $item
 * @param string $type
 * @return array
 */
function format_new_cert($data = array(), $item = array(), $type = '')
{
	$CI =& get_instance();

	switch ($type)
	{
		case 'admin':

			$vars = array('order_id'    => $data['order_id'],
			              'status'      => '1',
			              'description' => '',
			              'code'        => $CI->gift->generate_serial(),
			              'from_name'   => $data['order_name'],
			              'from_email'  => $data['order_primary_email'],
			              'to_name'     => is_var($data, 'to_name'),
			              'to_email'    => is_var($data, 'to_email'),
			              'theme_id'    => DEFAULT_GIFT_CERTIFICATE_THEME_ID,
			              'message'     => is_var($data, 'to_message'),
			              'amount'      => $item['unit_price'],
			              'redeemed'    => 0,
			              'notes'       => '',
			);

			break;

		case 'points':

			$vars = array('order_id'    => '0',
			              'status'      => '1',
			              'description' => lang('gift_certificate_loyalty_rewards'),
			              'code'        => $CI->gift->generate_serial(),
			              'from_name'   => config_item('sts_site_name'),
			              'from_email'  => config_item('sts_site_email'),
			              'to_name'     => is_var($data, 'fname'),
			              'to_email'    => is_var($data, 'primary_email'),
			              'theme_id'    => DEFAULT_GIFT_CERTIFICATE_THEME_ID,
			              'message'     => is_var($item, 'to_message'),
			              'amount'      => $item['amount'],
			              'redeemed'    => 0,
			              'notes'       => '',
			);

			break;

		default:

			$vars = array('order_id'    => $data['order']['order_id'],
			              'status'      => '1',
			              'description' => '',
			              'code'        => $CI->gift->generate_serial(),
			              'from_name'   => $data['order']['order_name'],
			              'from_email'  => $data['order']['order_primary_email'],
			              'to_name'     => is_var($data, 'to_name'),
			              'to_email'    => is_var($data, 'to_email'),
			              'theme_id'    => DEFAULT_GIFT_CERTIFICATE_THEME_ID,
			              'message'     => is_var($data, 'to_message'),
			              'amount'      => $item['unit_price'],
			              'redeemed'    => 0,
			              'notes'       => '',
			);

			break;
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $cert
 * @param array $data
 * @return array
 */
function format_certificates_data($cert = array(), $data = array())
{
	$vars = array(
		'cart_id'    => $data['cart_id'],
		'type'       => 'gift_certificate',
		'text'       => $cert['code'],
		'amount'     => $cert['amount'] + $cert['redeemed'],
		'percent'    => 'flat',
		'sub_data'   => serialize(array('required_email' => $cert['to_email'],
		                                'message'        => $cert['message'],
		)),
		'sort_order' => '2',
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param int $points
 * @return float|int
 */
function redeem_points($points = 0)
{
	$amount = 0;

	if ($points > config_item('sts_rewards_point_conversion'))
	{
		$amount =  (int)$points / (int)config_item('sts_rewards_point_conversion');
	}

	return $amount;
}

/* End of file gift_certificates_helper.php */
/* Location: ./application/helpers/gift_certificates_helper.php */