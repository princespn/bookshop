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
 * Validate item
 *
 * Validate an item added to the cart for inventory
 * and quantity amounts
 *
 * @param string $type
 * @param array $data
 * @param array $post
 *
 * @return mixed|string
 */
function validate_item($type = '', $data = array(), $post = array())
{
	$CI = &get_instance();

	//check date available
	if (get_time() > strtotime($data['date_expires']))
	{
		return lang('product_not_available');
	}

	//run only for general products and not memberships
	if ($data['product_type'] == 'general')
	{
		//set default quantity
		$qty = empty($post['quantity']) ? 1 : $post['quantity'];

		switch ($type)
		{
			//check if there is a minimum amount for each order
			case 'min_quantity':

				if ($data['min_quantity_required'] > $qty)
				{
					return lang('minimum_quantity_required_for_product') . ' - ' . $data['min_quantity_required'];
				}

				break;
		}
	}

	return '';
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function check_subscription($data = array())
{
	if (!empty($data))
	{
		foreach ($data['items'] as $v)
		{
			if ($v['product_type'] == 'subscription')
			{
				return TRUE;
			}
		}
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool|mixed
 */
function check_subscription_id($data = array())
{
	if (!empty($data['transaction']['customer_token']))
	{
		return $data['transaction']['customer_token'];
	}
	elseif (!empty($data['transaction']['subscription_id']))
	{
		return $data['transaction']['subscription_id'];
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool $format
 * @return int|string
 */
function cart_qty_total($data = array(), $format = TRUE)
{
	return format_amount($data['quantity'] * cart_unit_price($data, FALSE), $format);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function init_cart_items($data = array())
{
	//add discount group data to the cart items if any
	foreach ($data as $k => $v)
	{
		if (!empty($v['discount_data']))
		{
			$data[$k]['discount_data'] = unserialize(($v['discount_data']));
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function cart_discount_text($data = array())
{
	$str = '';

	if (!empty($data['type']))
	{
		switch ($data['type'])
		{
			case 'group_discount':

				if ($data['amount'] > '0')
				{
					if ($data['discount_type'] == 'percent')
					{
						$str .= (int)$data['amount'] . '% ' . lang('percent') . ' ' . lang('discount_per_item') . ' <br />';
					}
					else
					{
						$str .= format_amount($data['amount']) . ' ' . lang('discount_per_item') . '<br />';
					}
				}

				break;

			case 'quantity_discount':

				$str = lang('purchase') . ' ' . lang($data['operator']) . ' ' . $data['quantity'] . ' ' . lang('get') . ' ';

				if ($data['amount'] > '0')
				{
					if ($data['discount_type'] == 'percent')
					{
						$str .= (int)$data['amount'] . '% ' . lang('percent') . ' ' . lang('off');
					}
					else
					{
						$str .= format_amount($data['amount']) . ' ' . lang('off');
					}

					$str .= ' ' . lang('per_item') . '<br />';
				}

				break;

			case 'flat':

				if ($data['amount'] > '0')
				{
					$str = lang('discount_amount') . ' - ' . format_amount($data['amount']) . ' ' . lang('off');
				}

				break;

			case 'special_offer':

				$product = unserialize($data['product_name']);

				$str = lang('purchase') . ' ' . lang($data['operator']) . ' ' . $data['quantity'] . ' ' . lang('get') . ' ';

				$str .= (int)$data['amount'] . ' ';

				$str .= $data['amount'] > 1 ? lang('free_items') : lang('free_item');

				$str .= ' - ' . $product[sess('default_lang_id')];

				break;
		}
	}

	return $str;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return float|int
 */
function cart_total_weight($data = array(), $type = '')
{
	$w = 0;

	foreach ($data as $v)
	{
		if ($v['charge_shipping'] == 1)
		{
			$w += $v['quantity'] * calc_weight($v, $type);
		}
	}

	return $w;
}

/* End of file cart_helper.php */
/* Location: ./application/helpers/cart_helper.php */