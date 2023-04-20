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
 * @param bool $amount
 * @param string $type
 * @param bool $serialize
 * @return mixed|string
 */
function format_subscription_shipping($data = array(), $amount = FALSE, $type = 'checkout', $serialize = TRUE)
{
	$amt = '';

	switch ($type)
	{
		case 'checkout':

			if (!empty($data['cart']['totals']['subscription']['shipping']['shipping_total']))
			{
				$amt = $data['cart']['totals']['subscription']['shipping']['shipping_total'];

				$shipping = $data['cart']['totals']['subscription']['shipping'];

				if ($amount == TRUE)
				{
					return $amt;
				}

				return $serialize == TRUE ? serialize($shipping) : $shipping;
			}

			break;

		case 'admin':

			if (!empty($data['shipping']['shipping_total']))
			{
				$amt = $data['shipping']['shipping_total'];

				$shipping = $data['shipping'];

				if ($amount == TRUE)
				{
					return $amt;
				}

				return $serialize == TRUE ? serialize($shipping) : $shipping;
			}

			break;

	}

	return $amt;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $item
 * @param string $amount
 * @param string $type
 * @return int|mixed
 */
function format_subscription_tax($data = array(), $item = array(), $amount = '0', $type = 'checkout')
{
	$CI = &get_instance();

	$amt = 0;

	switch ($type)
	{
		case 'checkout':

			if (!empty($data['cart']['totals']['subscription']['taxes']))
			{
				$amt = $data['cart']['totals']['subscription']['taxes'];
			}

			break;

		case 'admin':

			if (!empty($item['unit_tax']))
			{
				$CI->session->set_userdata('order_shipping_address_data', $data);

				$t = calc_tax($amount, set_tax_array($item['unit_tax']), $item['order_item_id'], 'admin');

				$amt = $t['taxes'];
			}

			break;

	}

	return $amt;
}

//for setting up subscription pricing

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $product
 * @return float|int|mixed
 */
function format_cart_price($data = array(), $product = array())
{
	if (!empty($data['cart']['totals']['subscription']['sub_total']))
	{
		return $data['cart']['totals']['subscription']['sub_total'];
	}
	else
	{
		$pricing_data = unserialize($product['pricing_data']);

		$price = $pricing_data['amount'];

		//calculate discount amount
		foreach ($data['cart']['items'] as $v)
		{
			if ($v['product_id'] == $product['product_id'])
			{
				$price = check_attribute_price($v, $price, TRUE);

				if (!empty($v['discount_data']) && is_array($v['discount_data']))
				{
					foreach ($v['discount_data'] as $e)
					{
						$price -= $e['discount_type'] == 'flat' ? $e['amount'] : $price * show_percent($e['amount']);
					}
				}
				/*
				//get taxes
				if (!empty($v['taxes']))
				{
					$t = calc_tax($price, set_tax_array($v['taxes']));

					if (!config_enabled('sts_tax_product_display_price_with_tax'))
					{
						$price += $t['taxes'];
					}
				}

				//get shipping
				if (!empty($v['charge_shipping']) && !empty($data['cart']['totals']['shipping']))
				{
					$price += $data['cart']['totals']['shipping'];
				}
				*/
			}
		}

		return $price;

	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $product
 * @return array|bool
 */
function format_cart_subscription($data = array(), $product = array())
{
	if (!empty($product['pricing_data']))
	{
		$pricing_data = unserialize($product['pricing_data']);

		$vars = array('member_id'           => $data['member']['member_id'],
		              'product_id'          => $product['product_id'],
		              'order_id'            => $data['order']['order_id'],
		              'invoice_id'          => is_var($data['invoice'], 'id'),
		              'start_date'          => current_date('Y-m-d'),
		              'next_due_date'       => get_next_due_date($pricing_data, 'add'),
		              'product_price'       => format_cart_price($data, $product),
		              'shipping_amount'     => format_subscription_shipping($data, TRUE),
		              'tax_amount'          => format_subscription_tax($data),
		              'interval_amount'     => $pricing_data['interval_amount'],
		              'interval_type'       => $pricing_data['interval_type'],
		              'intervals_required'  => $pricing_data['recurrence'],
		              'intervals_generated' => '0',
		              'payment_type'        => empty($data['payment']['method']) ? 'manual' : $data['payment']['method'],
		              'subscription_id'     => check_subscription_id($data),
		              'status'              => '1',
		              'shipping_data'       => format_subscription_shipping($data),
		              'attribute_data'      => is_var($product, 'attribute_data'),
		              'specification_data'  => is_var($product, 'specification_data'),
		              'notes'               => '',
		);

		return $vars;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $item
 * @return array|bool
 */
function format_order_subscription($data = array(), $item = array())
{
	if (!empty($item['pricing_data']))
	{
		$pricing_data = unserialize($item['pricing_data']);

		$price =  get_subscription_price($pricing_data['amount'], $data, $item);

		$vars = array('member_id'           => is_var($data, 'member_id'),
		              'product_id'          => $item['product_id'],
		              'order_id'            => is_var($data, 'order_id'),
		              'invoice_id'          => is_var($data, 'invoice_id'),
		              'start_date'          => current_date('Y-m-d'),
		              'next_due_date'       => get_next_due_date($pricing_data, 'add'),
		              'product_price'       => $price,
		              'shipping_amount'     => format_subscription_shipping($data, TRUE, 'admin'),
		              'tax_amount'          => format_subscription_tax($data, $item, $price,'admin'),
		              'interval_amount'     => is_var($pricing_data, 'interval_amount'),
		              'interval_type'       => is_var($pricing_data, 'interval_type'),
		              'intervals_required'  => is_var($pricing_data, 'recurrence'),
		              'intervals_generated' => '0',
		              'payment_type'        => is_var($data, 'payment_type'),
		              'subscription_id'     => check_subscription_id($data),
		              'status'              => '1',
		              'shipping_data'       => format_subscription_shipping($data, FALSE, 'admin'),
		              'attribute_data'      => $item['attribute_data'],
		              'specification_data'  => $item['specification_data'],
		              'notes'               => '',
		);

		return $vars;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $amount
 * @param array $data
 * @return float|int|string
 */
function check_subscription_discount($amount = '0', $data = array())
{
	$order = unserialize($data['order_data']);

	if (!empty($order['cart']['items']))
	{
		foreach ($order['cart']['items'] as $v)
		{
			if (!empty($v['discount_data']))
			{
				foreach ($v['discount_data'] as $d)
				{
					$amount -= $d['discount_type'] == 'percent' ? ($amount * show_percent($d['amount'])) : $d['amount'];
				}
			}
		}
	}

	//check for coupons
	if (!empty($data['coupon_data']))
	{
		$c = unserialize($data['coupon_data']);
		$amount -= $c['coupon_type'] == 'flat' ? $c['amount'] : $amount * show_percent($c['amount']);
	}

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param string $amount
 * @param array $data
 * @param array $item
 * @return float|int|string
 */
function get_subscription_price($amount = '0', $data = array(), $item = array())
{
	//get attribute amount
	$amount = check_attribute_price($item, $amount, TRUE);

	//get discounts
	$amount = check_subscription_discount($amount, $data);

	//get taxes
	$amount = check_subscription_taxes($amount, $item['unit_tax']);

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param $amount
 * @param array $data
 * @return mixed
 */
function check_subscription_taxes($amount, $data = array())
{
	$tax = calc_tax($amount, set_tax_array($data));

	if (config_enabled('sts_tax_product_display_price_with_tax'))
	{
		return $amount;
	}
	else
	{
		if (config_enabled('sts_tax_use_compound_tax_amounts'))
		{
			$amount += $tax['taxes_compounded'];
		}
		else
		{
			$amount += $tax['taxes'];
		}
	}

	return $amount;
}

/* End of file subscriptions_helper.php */
/* Location: ./application/helpers/subscriptions_helper.php */