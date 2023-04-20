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
 * @return string
 */
function generate_order_number()
{
	$CI = &get_instance();

	$a = random_string(ORDER_NUMBER_GENERATOR_TYPE, ORDER_NUMBER_GENERATOR_LENGTH);

	while ($CI->dbv->check_unique($a, TBL_ORDERS, 'order_number'))
	{
		$a = random_string(ORDER_NUMBER_GENERATOR_TYPE, ORDER_NUMBER_GENERATOR_LENGTH);
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool|mixed
 */
function get_order_data($data = array())
{
	if (!empty($data['order_data']))
	{
		return unserialize($data['order_data']);
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_order_details($data = array())
{
	if (empty($data['shipping_country']))
	{
		$data['shipping_country'] = config_item('sts_site_shipping_country_id');
		$data['shipping_country_name'] = config_item('sts_site_shipping_country_name');
		$data['shipping_state'] = config_item('sts_site_shipping_state');
	}

	if (!empty($data['items']))
	{
		foreach ($data['items'] as $k => $v)
		{
			if (!empty($v['attribute_data']))
			{
				$data['items'][$k]['attribute_data'] = unserialize($v['attribute_data']);
			}
			if (!empty($v['specification_data']))
			{
				$data['items'][$k]['specification_data'] = unserialize($v['specification_data']);
			}
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @param string $parent_order
 * @return array
 */
function format_order_data($data = array(), $type = 'checkout', $parent_order = '0')
{
	$CI = &get_instance();

	if ($type == 'admin')
	{
		//format the data to be sent for creating an order
		$order = array(
			'order_number'         => generate_order_number(),
			'parent_order'         => $parent_order,
			'payment_status'       => '0',
			'order_status_id'      => config_option('sts_order_default_order_status_unpaid'),
			'member_id'            => is_var($data['billing_address'], 'member_id'),
			'affiliate_id'         => is_var($data['billing_address'], 'affiliate_id'),
			'tracking_data'        => is_var($data, 'affiliate', TRUE),
			'order_name'           => check_order_field($data['billing_address'], 'billing_address_name'),
			'order_company'        => check_order_field($data['billing_address'], 'order_company'),
			'order_address_1'      => is_var($data['billing_address'], 'billing_address_1'),
			'order_address_2'      => is_var($data['billing_address'], 'billing_address_2'),
			'order_city'           => is_var($data['billing_address'], 'billing_city'),
			'order_postal_code'    => is_var($data['billing_address'], 'billing_postal_code'),
			'order_state'          => is_var($data['billing_address'], 'billing_state'),
			'order_country'        => is_var($data['billing_address'], 'billing_country'),
			'order_telephone'      => is_var($data['billing_address'], 'order_telephone'),
			'order_primary_email'  => is_var($data['billing_address'], 'order_primary_email'),
			'shipping_name'        => check_order_field($data['shipping_address'], 'name', 'shipping'),
			'shipping_company'     => is_var($data['shipping_address'], 'company', $data['billing_address']['order_company']),
			'shipping_address_1'   => is_var($data['shipping_address'], 'shipping_address_1'),
			'shipping_address_2'   => is_var($data['shipping_address'], 'shipping_address_2'),
			'shipping_city'        => is_var($data['shipping_address'], 'shipping_city'),
			'shipping_postal_code' => is_var($data['shipping_address'], 'shipping_postal_code'),
			'shipping_state'       => is_var($data['shipping_address'], 'shipping_state'),
			'shipping_country'     => is_var($data['shipping_address'], 'shipping_country'),
			'shipping_data'        => is_var($data, 'shipping', TRUE),
			'coupon_data'          => is_var($data, 'coupons', TRUE),
			'date_ordered'         => get_time(now(), TRUE), //mysql format
			'due_date'             => empty($data['due_date']) ? get_time(now(), TRUE) : $data['due_date'],
			'order_total'          => $data['totals']['total_with_shipping'],
			'cart_data'            => '',
			'ip_address'           => $CI->input->ip_address(),
			'language_id'          => sess('default_lang_id'),
			'currency_id'          => $CI->config->item('currency_id', 'currency'),
			'currency_code'        => $CI->config->item('code', 'currency'),
			'currency_value'       => $CI->config->item('value', 'currency'),
			'order_notes'          => is_var($data, 'order_notes'),
			'user_agent'           => $CI->agent->agent_string(),
			'order_data'           => serialize(sess('order_contents_data')),
		);
	}
	elseif ($type == 'cron')
	{
		$order = $data;
		$order['order_id'] = '';
		$order['order_number'] = generate_order_number();
		$order['order_status_id'] = config_option('sts_order_default_order_status_unpaid');
		$order['parent_order'] = $data['order_id'];
		$order['payment_status'] = '0';
		$order['date_ordered'] = get_time(now(), TRUE);
		$order['due_date'] = $data['next_due_date'] . ' 23:59:59';
	}
	else
	{
		//format the data to be sent for creating an order
		$order = array(
			'order_number'         => generate_order_number(),
			'parent_order'         => $parent_order,
			'payment_status'       => $data['cart']['totals']['total_with_shipping'] > 0 ? '0' : '1',
			'order_status_id'      => config_option('sts_order_default_order_status_unpaid'),
			'member_id'            => is_var($data['member'], 'member_id'),
			'affiliate_id'         => is_var($data['affiliate'], 'member_id'),
			'tracking_data'        => is_var($data, 'affiliate', TRUE),
			'order_name'           => check_order_field($data['customer'], 'name'),
			'order_company'        => check_order_field($data['customer'], 'company'),
			'order_address_1'      => is_var($data['customer'], 'billing_address_1'),
			'order_address_2'      => is_var($data['customer'], 'billing_address_2'),
			'order_city'           => is_var($data['customer'], 'billing_city'),
			'order_postal_code'    => is_var($data['customer'], 'billing_postal_code'),
			'order_state'          => is_var($data['customer'], 'billing_state'),
			'order_country'        => is_var($data['customer'], 'billing_country'),
			'order_telephone'      => is_var($data['customer'], 'phone'),
			'order_primary_email'  => is_var($data['customer'], 'primary_email'),
			'shipping_name'        => check_order_field($data['customer'], 'name', 'shipping'),
			'shipping_company'     => is_var($data['customer'], 'shipping_company'),
			'shipping_address_1'   => is_var($data['customer'], 'shipping_address_1'),
			'shipping_address_2'   => is_var($data['customer'], 'shipping_address_2'),
			'shipping_city'        => is_var($data['customer'], 'shipping_city'),
			'shipping_postal_code' => is_var($data['customer'], 'shipping_postal_code'),
			'shipping_state'       => is_var($data['customer'], 'shipping_state'),
			'shipping_country'     => is_var($data['customer'], 'shipping_country'),
			'shipping_data'        => is_var($data['cart']['totals'], 'shipping_item', TRUE),
			'coupon_data'          => is_var($data['cart']['totals'], 'coupon_codes', TRUE),
			'date_ordered'         => get_time(now(), TRUE), //mysql format
			'due_date'             => empty($data['due_date']) ? get_time(now(), TRUE) : $data['due_date'],
			'order_total'          => $data['cart']['totals']['total_with_shipping'],
			'cart_data'            => serialize($data['cart']),
			'ip_address'           => $CI->input->ip_address(),
			'language_id'          => $data['language'],
			'currency_id'          => $data['currency']['currency_id'],
			'currency_code'        => $data['currency']['code'],
			'currency_value'       => $data['currency']['value'],
			'order_notes'          => is_var($data, 'order_notes'),
			'user_agent'           => is_var($data, 'user_agent'),
			'order_data'           => serialize($data),
		);
	}

	return $order;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $id
 * @param int $lang_id
 * @return string
 */
function get_order_specs($data = array(), $id = '', $lang_id = 1)
{
	$CI = &get_instance();

	if (!empty($data['specs']))
	{
		$specs = $data['specs'];
	}
	else
	{
		$specs = $CI->specs->get_product_specs($id, $lang_id, 'prod_spec_id,spec_value,specification_name');
	}

	return !empty($specs) ? serialize($specs) : '';
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param string $col
 * @return bool
 */
function get_order_status($id = '', $col = 'order_status')
{
	$CI = &get_instance();

	if (!$q = $CI->db->where('order_status_id', $id)->get(TBL_ORDERS_STATUS))
	{
		get_error(__FILE__, __METHOD__, __LINE__);
	}

	if ($q->num_rows() > 0)
	{
		$row = $q->row_array();

		return $row[$col];
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param $id
 * @param array $data
 * @param string $lang_id
 * @param string $type
 * @return array
 */
function format_order_item($id, $data = array(), $lang_id = '', $type = '')
{

	if ($type == 'cron')
	{
		$data['order_id'] = $id;
		$data['points'] = get_order_points($data);
		$data['order_item_name'] = $data['product_name'];
		$data['unit_price'] = $data['product_price'];
		$data['quantity'] = '1';


	}
	else
	{
		$data['order_id'] = $id;
		$data['order_item_name'] = $data['product_name'];
		$data['order_item_notes'] = is_var($data, 'order_item_notes');
		$data['specification_data'] = get_order_specs($data, $data['product_id'], $lang_id);
		$data['pricing_data'] = is_var($data, 'pricing_data');
		$data['unit_tax'] = is_var($data, 'taxes');
		$data['points'] = get_order_points($data);
		$data['unit_price'] = order_price($data, 'price', FALSE);

		//serialize data if it hasn't been
		if (!empty($data['attribute_data']))
		{
			if (is_array($data['attribute_data']))
			{
				$data['attribute_data'] = serialize($data['attribute_data']);
			}
		}
		else
		{
			$data['attribute_data'] = '';
		}
	}

	//serialize data if it hasn't been
	if (!empty($data['discount_data']))
	{
		if (is_array($data['discount_data']))
		{
			$data['discount_data'] = serialize($data['discount_data']);
		}
	}
	else
	{
		$data['discount_data'] = '';
	}

	if (!empty($data['specification_data']))
	{
		if (is_array($data['specification_data']))
		{
			$data['specification_data'] = serialize($data['specification_data']);
		}
	}
	else
	{
		$data['specification_data'] = '';
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param array $data
 * @param int $lang_id
 * @return array
 */
function format_order_items($id = '', $data = array(), $lang_id = 1)
{
	$CI = &get_instance();

	//set order items
	$items = array();

	if (!empty($data))
	{
		foreach ($data as $k => $v)
		{
			$v = format_order_item($id, $v, $lang_id);

			array_push($items, $v);
			//check if there any promo items to add from rules (ex. buy 3 get 1 free)
			if (!empty($v['discount_data']))
			{
				$discounts = !is_array($v['discount_data']) ? unserialize($v['discount_data']) : $v['discount_data'];
				foreach ($discounts as $d)
				{
					//special offer (buy 3 get 1 free)
					if (!empty($d['type']) && ($d['type'] == 'special_offer'))
					{
						//get the product data
						$item = $CI->prod->get_details($d['product_id'], $lang_id, TRUE, FALSE);

						//set the price to free
						$item['order_id'] = $id;
						$item['order_item_name'] = $item['product_name'];
						$item['order_item_notes'] = lang('free_item');
						$item['item_notes'] = lang('free_item');
						$item['specification_data'] = get_order_specs($item, $item['product_id'], $lang_id);
						$item['unit_price'] = '0';
						$item['discount_price'] = '0';
						$item['quantity'] = $d['amount'];

						array_push($items, $item);
					}
				}
			}
		}
	}

	return $items;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param array $data
 * @return array
 */
function format_order_shipping($id = '', $data = array())
{
	if (!empty($data))
	{
		$vars = array(
			'order_id'       => $id,
			'carrier'        => is_var($data, 'carrier'),
			'service'        => is_var($data, 'service'),
			'rate'           => is_var($data, 'shipping_total'),
			'rate_id'        => is_var($data, 'rate_id'),
			'tracking_id'    => is_var($data, 'tracking_id'),
			'api_id'         => is_var($data, 'shipment_id'),
			'label'          => is_var($data, 'label'),
			'shipping_notes' => is_var($data, 'shipping_notes'),
			'shipping_data'  => serialize($data),
		);
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return float|int|string
 */
function cart_order_price($data = array())
{
	$price = $data['unit_price'];

	if ($price <= 0)
	{
		$price = 0;
	} //set prices to zero on negatives

	$p = format_price($price, $data);

	return $p;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @param bool $format
 * @return float|int|mixed|string
 */
function order_price($data = array(), $type = 'price', $format = FALSE)
{
	if (isset($data['unit_price']))
	{
		$price = $data['unit_price'];
	}
	else
	{
		$price = empty($data['product_sale_price']) ? $data['product_sale_price'] : $data['product_price'];

		//check for attribute pricing
		if (!empty($data['attribute_data']))
		{
			if (is_serialized($data['attribute_data']))
			{
				$data['attribute_data'] = unserialize($data['attribute_data']);
			}

			foreach ($data['attribute_data'] as $v)
			{
				if (!empty($v['price_add']))
				{
					if ($v['price_add'] == '-')
					{
						$price -= $v['price'];
					}
					else
					{
						$price += $v['price'];
					}
				}
			}
		}
	}

	$amount = $type == 'sub_total' ? $data['quantity'] * $price : $price;

	return $format == TRUE ? format_amount($amount) : $amount;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function order_update_quantity($data = array())
{
	$CI = &get_instance();

	$cart = array();

	if (sess('order_contents_data'))
	{
		$c = sess('order_contents_data');

		foreach ($c as $k => $v)
		{
			if ($data['item'][$k] < 1)
			{
				continue;
			}
			else
			{
				$v['quantity'] = $data['item'][$k];
				$v['unit_price'] = $data['price'][$k];
			}

			array_push($cart, $v);
		}
	}

	if (!empty($cart))
	{
		//save the data as a new session
		$CI->session->set_userdata('order_contents_data', $cart);
	}
	else
	{
		$CI->session->unset_userdata('order_contents_data');
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function update_order_contents($data = array())
{
	$CI = &get_instance();

	$cart = array();

	$new_item = TRUE;

	//check if the product is already in the cart
	if (sess('order_contents_data'))
	{
		foreach (sess('order_contents_data') as $k => $v)
		{
			//if it is update the quantity
			if ($data['checksum'] == $v['checksum'])
			{
				if ($data['product_type'] == 'subscription')
				{
					return $cart;
				}
				else
				{
					//update the quantity
					$v['quantity'] += $data['quantity'];
					$new_item = FALSE;
				}
			}

			array_push($cart, $v);
		}
	}

	//now add the new product
	if ($new_item == TRUE)
	{
		array_push($cart, $data);
	}

	//save the data as a new session
	$CI->session->set_userdata('order_contents_data', $cart);

	return $cart;
}

// ------------------------------------------------------------------------

/**
 *
 */
function reset_order_data()
{
	$CI = &get_instance();

	$vars = array('contents', 'discount', 'coupon', 'shipping', 'certificate', 'shipping_address', 'billing_address');

	foreach ($vars as $v)
	{
		$CI->session->unset_userdata('order_' . $v . '_data');
	}
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function get_order_cart()
{
	$vars = array(
		'items'            => sess('order_contents_data'),
		'discounts'        => sess('order_discount_data'),
		'coupons'          => sess('order_coupon_data'),
		'shipping'         => sess('order_shipping_data'),
		'shipping_address' => sess('order_shipping_address_data'),
		'gift_certificate' => sess('order_certificate_data'),
		'billing_address'  => sess('order_billing_address_data'),
	);

	//generate totals
	$totals = get_order_totals($vars);

	$vars['items'] = $totals['items'];
	$vars['totals'] = $totals['totals'];

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function get_order_totals($data = array()) //admin area order totals
{
	$row = array('sub_total'           => 0,
	             'sub_total_discounts' => 0,
	             'taxes'               => 0,
	             'discounts'           => 0,
	             'coupons'             => 0,
	             'shipping'            => 0,
	             'total'               => 0,
	             'total_with_shipping' => 0,
	             'shipping_item'       => array(),
	             'coupon_codes'        => array(),
	             'tax_items'           => array(),
	);

	if (!empty($data['items']))
	{
		foreach ($data['items'] as $k => $p)
		{
			$data['items'][$k]['unit_price'] = order_price($p, 'price', FALSE);

			$row['sub_total'] += ($data['items'][$k]['unit_price'] * $p['quantity']);
		}

		//check for coupons and apply it for each item
		if (!empty($data['coupons']))
		{
			//how many products are in the cart?
			$total_items = 0;
			if ($data['coupons']['coupon_type'] == 'flat')
			{
				$row['coupons'] = (-1 * $data['coupons']['amount']);
			}

			//check each product if it qualifies for the coupon code
			foreach ($data['items'] as $k => $p)
			{
				$total_items += $p['quantity'];

				if (!empty($data['coupons']['required_products']))
				{
					if (!in_array($p['product_id'], $data['coupons']['required_products']))
					{
						continue;
					}
				}

				//set coupon items
				$data['items'][$k]['item_coupons'] = 0;

				//check the type of coupon
				if ($data['coupons']['coupon_type'] == 'percent')
				{
					$data['items'][$k]['item_coupons'] = -1 * ($p['unit_price'] * show_percent($data['coupons']['amount']));
					$row['coupons'] += ($p['quantity'] * $data['items'][$k]['item_coupons']);
				}
				else
				{
					$data['items'][$k]['item_coupons'] = -1 * ($data['coupons']['amount'] / $total_items);
				}

				//$data['items'][ $k ]['unit_price'] -= $data['items'][ $k ]['item_coupons'];
			}

			array_push($row['coupon_codes'], $data['coupons']);
		}

		//check for shipping amount and add
		if (!empty($data['shipping']))
		{
			$row['shipping'] = $data['shipping']['shipping_total'];
			$row['shipping_item'] = $data['shipping'];
		}

		//check taxes
		foreach ($data['items'] as $k => $p)
		{
			//add discounts and coupons to the unit price first

			if (!empty($p['item_coupons']))
			{
				$p['unit_price'] += $p['item_coupons'];
			}

			$t = calc_tax($p['unit_price'], $p['tax_rates'], $p['product_id'], 'admin');

			//add tax data to the items array
			$data['items'][$k]['tax_data'] = $t;
			$data['items'][$k]['unit_tax'] = $t['taxes'];
			$data['items'][$k]['subtotal_tax'] = $t['taxes'] * $p['quantity'];

			$row['taxes'] += $t['taxes'] * $p['quantity'];

			array_push($row['tax_items'], $t);
		}

		//calculate totals
		$row['total'] = $row['sub_total'] + $row['discounts'] + $row['coupons'] + $row['taxes'];

		$row ['total_with_shipping'] = $row['total'] + $row['shipping'];

		if ($row['total'] < 0)
		{
			$row['total'] = 0;
		}

		if ($row['total_with_shipping'] < 0)
		{
			$row['total_with_shipping'] = 0;
		}
	}

	$data['totals'] = $row;

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $cart
 * @param array $data
 * @return array
 */
function order_free_shipping($cart = array(), $data = array())
{
	//check if a free shipping coupon is set
	if (!empty($cart['coupons']))
	{
		if (!empty($cart['coupons']['free_shipping']))
		{
			$free_shipping = TRUE;
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
 * @param array $data
 * @return string
 */
function order_specs($data = array())
{
	$html = '<strong>' . $data['specification_name'] . '</strong> - ' . $data['spec_value'];

	return $html;
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function set_default_order_data()
{
	//add order field data when creating a new order

	$CI = &get_instance();

	$vars = list_fields(array(TBL_ORDERS));
	$vars['fname'] = '';
	$vars['lname'] = '';

	//check for default member data
	if ($CI->input->get('member_id'))
	{
		if ($mem = $CI->mem->get_details($CI->input->get('member_id')))
		{
			$vars = array(
				'member_id'           => $mem['member_id'],
				'fname'               => $mem['fname'],
				'lname'               => $mem['lname'],
				'order_company'       => $mem['company'],
				'order_primary_email' => $mem['primary_email'],
				'order_telephone'     => $mem['home_phone'],
				'sponsor_id'          => $mem['sponsor_id'],
				'sponsor_username'    => $mem['sponsor_username'],

			);
		}
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param string $k
 * @param array $data
 * @param bool $toggle
 * @return string
 */
function order_attributes($k = '', $data = array(), $toggle = TRUE)
{
	$html = '';

	if (!empty($data['attribute_name']))
	{
		if ($data['attribute_type'] != 'image')
		{
			if ($toggle == TRUE)
			{
				$html .= '<a data-toggle="collapse" href="#option-info-' . $k . '" style="text-decoration: none"><strong>+ ' . $data['attribute_name'] . '</strong></a> - ';
			}
			else
			{

				$html .= '<strong>' . $data['attribute_name'] . '</strong> - ';
			}
		}

		switch ($data['attribute_type'])
		{
			case 'select':
			case 'radio':

				$html .= $data['option_name'];

				break;

			case 'image':
				$html .= img(array('src'   => $data['path'],
				                   'class' => 'img-cart',
				                   'style' => 'max-height: 100px'));

				break;

			case 'file':

				if ($toggle == TRUE)
				{
					$html .= anchor(admin_url(TBL_ORDERS . '/download/' . $data['file_name']), $data['file_name'] . ' ' . i('fa fa-download'));
				}
				else
				{

					$html .= $data['file_name'];
				}

				break;

			default:

				$html .= $data['value'];

				break;
		}
	}

	return $html;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return int|mixed
 */
function get_total_points($data = array())
{
	$points = 0;
	foreach ($data as $v)
	{
		$points += $v['points'];
	}

	return $points;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed|string
 */
function get_order_points($data = array())
{
	$points = empty($data['points']) ? '0' : $data['points'];

	if (!empty($data['attribute_data']))
	{
		$att = is_array($data['attribute_data']) ? $data['attribute_data'] : unserialize($data['attribute_data']);

		if (!empty($att) && is_array($att))
		{
			foreach ($att as $v)
			{
				if (!empty($v['points_add']) && !empty($v['points']))
				{
					$points = $v['points_add'] == '-' ? $points - $v['points'] : $points + $v['points'];
				}
			}
		}
	}

	return $points;
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function initialize_order_data()
{
	$CI = &get_instance();

	$vars = array(
		'member'      => $CI->mem->checkout_get_member(),
		'customer'    => sess('checkout_customer_data'),
		'transaction' => sess('transaction_data'),
		'cart'        => sess('cart_details'),
		'shipping'    => sess('checkout_shipping_selected'),
		'affiliate'   => check_order_affiliate(),
		'order_notes' => sess('checkout_order_notes'),
		'language'    => sess('default_lang_id'),
		'coupon'      => sess('checkout_coupon_code'),
		'certificate' => sess('checkout_gift_certificate_data'),
		'currency'    => $CI->config->item('currency'),
		'user_agent'  => browser_info(),
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @return bool|mixed
 */
function check_order_affiliate($id = '')
{
	$CI = &get_instance();

	if (!empty($id))
	{
		if ($row = $CI->mem->get_details($id))
		{
			return $row;
		}
	}

	if (sess('lifetime_tracking_data'))
	{
		return sess('lifetime_tracking_data');
	}


	return sess('tracking_data');
}

// ------------------------------------------------------------------------

/**
 * @param bool $serialize
 * @return array|string
 */
function browser_info($serialize = TRUE)
{
	$CI = &get_instance();

	$agents = array('browser', 'version', 'mobile', 'platform', 'agent_string', 'languages',
	);

	$info = array();
	foreach ($agents as $v)
	{
		$info[$v] = $CI->agent->$v();
	}

	return $serialize == TRUE ? serialize($info) : $info;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed
 */
function check_order_discount($data = array())
{
	return $data['discounts'] + $data['coupons'];
}

/* End of file orders_helper.php */
/* Location: ./application/helpers/orders_helper.php */