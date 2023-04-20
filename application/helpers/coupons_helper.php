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
 * @param array $coupon
 * @param array $data
 * @return float|int|mixed
 */
function calc_coupon($coupon = array(), $data = array())
{
	$amount = empty($coupon['amount']) ? 0 : $coupon['amount'];

	if ($coupon['percent'] == 'percent')
	{
		$amount = ($data['sub_total'] + $data['discounts']) * show_percent($coupon['amount']);
	}

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param $data
 * @return mixed
 */
function check_order_coupon_amount($data)
{
	return $data['coupons'];
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_admin_coupon_data($data = array())
{
	$vars = array(
		'text'              => $data['coupon_code'],
		'amount'            => $data['coupon_amount'],
		'percent'           => $data['coupon_type'],
		'free_shipping'     => $data['free_shipping'],
		'minimum_order'     => $data['minimum_order'],
		'required_products' => array(),
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $coupon
 * @param array $data
 * @return array
 */
function format_coupon_data($coupon = array(), $data = array())
{
	$vars = array(
		'cart_id'    => $data['cart_id'],
		'type'       => 'coupon',
		'text'       => $coupon['coupon_code'],
		'amount'     => $coupon['coupon_amount'],
		'percent'    => $coupon['coupon_type'],
		'sub_data'   => array('free_shipping'           => $coupon['free_shipping'],
		                      'minimum_order'           => $coupon['minimum_order'],
		                      'enable_recurring_coupon' => $coupon['enable_recurring_coupon'],
		                      'required_products'       => array(),
		                      'required_groups'         => array(),
		),
		'sort_order' => '1',
	);

	return $vars;
}

/* End of file coupons_helper.php */
/* Location: ./application/helpers/coupons_helper.php */