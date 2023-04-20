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
 * @param string $amount
 * @param bool $redirect
 * @return bool
 */
function check_minimum_purchase($amount = '0', $redirect = FALSE)
{
	if (config_option('sts_cart_minimum_purchase_checkout') > 0)
	{
		if ($amount < config_option('sts_cart_minimum_purchase_checkout'))
		{
			if ($redirect == TRUE)
			{
				redirect('cart');
			}
			else
			{
				return FALSE;
			}
		}
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @return mixed
 */
function check_totals()
{
	if (sess('cart_charge_shipping'))
	{
		return $_SESSION['cart_details']['totals']['total_with_shipping'];
	}
	else
	{
		return $_SESSION['cart_details']['totals']['total'];
	}

}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function check_shipping($data = array())
{
	$CI =& get_instance();

	//unset it first
	$CI->session->unset_userdata('cart_charge_shipping');

	if (!empty($data))
	{
		foreach ($data as $v)
		{
			if ($v['charge_shipping'] == '1')
			{
				$CI->session->set_userdata('cart_charge_shipping', TRUE);

				return TRUE;
			}
		}
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $fields
 * @return array
 */
function checkout_shipping_as_billing($fields = array())
{
	$a = array();

	foreach (sess('checkout_customer_data') as $k => $v)
	{
		if ($k != 'shipping_address_id')
		{
			$c = str_replace('shipping', 'billing', $k);
			$a[$c] = $v;
		}
	}

	foreach ($fields as $v)
	{
		if ($v['sub_form'] == 'billing' && $v['show_public'] == 1)
		{
			if ($v['form_field'] == 'billing_fname')
			{
				$a[$v['form_field']] = $a['fname'];
			}
			else
			{
				if ($v['form_field'] == 'billing_lname')
				{
					$a[$v['form_field']] = $a['lname'];
				}
			}
		}
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return int|mixed
 */
function checkout_get_cart_subtotal($data = array())
{
	$amount = $data['total'] - $data['taxes'];

	return $amount > 0 ? $amount : 0;
}

// ------------------------------------------------------------------------

/**
 * @param array $cart
 * @param array $data
 * @return array
 */
function checkout_free_shipping($cart = array(), $data = array())
{
	//check if a free shipping coupon is set
	if (!empty($cart['totals']['coupon_codes']))
	{
		foreach ($cart['totals']['coupon_codes'] as $v)
		{
			if (!empty($v['sub']['free_shipping']))
			{
				$free_shipping = TRUE;
			}
		}
	}

	if (!empty($free_shipping))
	{
		foreach ($data as $k => $v)
		{
			$data[$k]['shipping_total'] = '0';
			$data[$k]['shipping_description'] .= ' <small class="text-muted free-shipping">' . lang('free_shipping') . '</small>';
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function checkout_offsite_url()
{
	//check if the offsite page has a redirect URL set for it
	$CI = &get_instance();

	//set the default URL
	$url = 'thank_you';

	if ($CI->config->item('module_payment_gateways_' . sess('checkout_payment_module') . '_redirect_url'))
	{
		$url = $CI->config->item('module_payment_gateways_' . sess('checkout_payment_module') . '_redirect_url');
	}

	return $url;
}

// ------------------------------------------------------------------------

/**
 * @param bool $error
 * @return mixed
 */
function checkout_referral_id($error = FALSE)
{
	if (config_item('affiliate_data'))
	{
		return config_item('affiliate_data', 'member_id');
	}

	//if referral code is required we need to redirect
	if (config_enabled('sts_affiliate_require_referral_code'))
	{
		if ($error == TRUE)
		{
			redirect(site_url('cart/referral'));
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $redirect
 */
function checkout_process_order($data = array(), $redirect = 'checkout/order/process')
{
	//this function redirects users after an internal payment is made whether its an error or not

	//default URL to send order to
	$url = ssl_url($redirect);

	//check if we are using ajax for the form
	if (is_ajax())
	{
		if ($data['type'] == 'success')
		{
			$response = array('type'     => 'success',
			                  'redirect' => !empty($data['redirect_url']) ? $data['redirect_url'] : $url,
			);
		}
		else
		{
			$response = array('type' => 'error');
		}

		//set response data
		$response['msg'] = is_var($data, 'msg_text');
		$response['debug'] = !empty($data['debug_info']) ? $data['debug_info'] : '';

		ajax_response($response);
	}
	else
	{
		// no ajax so we'll just redirect to the correct pages instead...
		if ($data['type'] == 'success')
		{
			$url = !empty($data['redirect_url']) ? $data['redirect_url'] : $url;
		}
		else
		{
			$url = ssl_url('checkout/cart/?step=payment');
		}

		//add flash data for errors
		redirect_flashdata($url, is_var($data, 'msg_text'), $data['type']);

	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool $format
 * @return array
 */
function update_cart_totals($data = array(), $format = TRUE) //public cart totals
{
	$a = array('sub_total'    => $data['totals']['sub_total'],
	           'taxes'        => $data['totals']['taxes'],
	           'discounts'    => $data['totals']['discounts'],
	           'coupons'      => $data['totals']['coupons'],
	           'shipping'     => $data['totals']['shipping'],
	           'subscription' => is_var($data['totals']['subscription'], 'amount'),
	           'total'        => $data['totals']['total_with_shipping'],
	           'original_total' =>  $data['totals']['total_with_shipping'],
	);

	if ($a['total'] == 0)
	{
		$a['coupons'] = -1 * ($a['sub_total'] + $a['discounts'] + $a['shipping'] + $a['taxes']);
	}

	foreach ($a as $k => $v)
	{
		if ($k == 'original_total')
		{
			$a[$k] = format_amount($v, FALSE, FALSE);
		}
		else
		{
			$a[$k] = $format == TRUE ? format_amount($v) : $v;
		}
	}

	return $a;
}

/* End of file checkout_helper.php */
/* Location: ./application/helpers/checkout_helper.php */