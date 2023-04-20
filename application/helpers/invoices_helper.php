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
 * Calculate invoice totals
 *
 * Calculate the amounts for each line item quantity in the invoice
 *
 * @param array $data
 * @return int|string
 */
function calc_invoice_totals($data = array())
{
	$total = '0';
	foreach ($data as $v)
	{
		$total += ($v['unit_price'] * $v['quantity']);
	}

	return $total;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return int|mixed|string
 */
function calc_invoice_amount($data = array())
{
	$amount = '0';

	$amount += calc_invoice_totals($data['items']);
	$amount += is_var($data, 'shipping_amount', FALSE, '0');
	$amount += is_var($data, 'tax_amount', FALSE, '0');

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return float|int|mixed
 */
function check_certificate_amount($data = array())
{
	$cert = $data['gift_certificates'];

	if (empty($data['total_with_shipping']) || empty($data['total']))
	{
		$total = $data['sub_total_discounts'] + $data['coupons'] + $data['taxes'];

		$cert = -1 * $total;
	}

	return $cert;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return float|int|mixed
 */
function check_coupon_amount($data = array())
{
	$coupon = $data['coupons'];

	if ($data['total_with_shipping'] == 0)
	{
		if (empty($data['gift_certificates']))
		{
			$coupon = -1 * $data['sub_total'] - $data['discounts'];

		}
	}

	return $coupon;
}

// ------------------------------------------------------------------------

/**
 * @return mixed
 */
function generate_invoice_number()
{
	$CI = &get_instance();

	//next invoice number
	$i = 1;
	$num = (int)config_option('sts_invoice_next_invoice_number') + $i;

	while ($CI->dbv->check_unique($num, TBL_INVOICES, 'invoice_number'))
	{
		$num += $i;
		$i++;
	}

	//format invoice number
	$invoice = $CI->config->item('sts_invoice_number_format');

	$vars = array('year'   => date('Y'),
	              'month'  => date('m'),
	              'day'    => date('d'),
	              'number' => $num,
	);

	foreach ($vars as $k => $v)
	{
		$invoice = str_replace('{' . $k . '}', $v, $invoice);
	}
	update_next_invoice_number();

	return $invoice;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param string $col
 * @return bool
 */
function get_payment_status($id = '', $col = 'payment_status')
{
	$CI = &get_instance();

	if (!$q = $CI->db->where('payment_status_id', $id)->get(TBL_PAYMENT_STATUS))
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
 * @param array $data
 * @param string $type
 * @return array
 */
function format_invoice_data($data = array(), $type = 'checkout')
{
	$CI = &get_instance();

	switch ($type)
	{
		case 'cron':

			//get total
			$total = $data['order']['items']['data']['unit_price'] * $data['order']['items']['data']['quantity'];

			if (!empty($data['order']['shipping']['rate']))
			{
				$total += $data['order']['shipping']['rate'];
			}

			if (!empty($data['order']['taxes']))
			{
				$total += $data['order']['taxes'];
			}

			//set the due date
			$due_date = get_time(now() + (3600 * 24 * config_item('sts_cron_generate_subscription_days_before')), TRUE);

			$invoice = array(
				'invoice_number'         => generate_invoice_number(),
				'order_id'               => is_var($data['order'], 'order_id'),
				'payment_status_id'      => '1', //unpaid
				'member_id'              => is_var($data['order'], 'member_id'),
				'customer_name'          => is_var($data['order'], 'order_name'),
				'customer_company'       => is_var($data['order'], 'order_company'),
				'customer_address_1'     => is_var($data['order'], 'order_address_1'),
				'customer_address_2'     => is_var($data['order'], 'order_address_2'),
				'customer_city'          => is_var($data['order'], 'order_city'),
				'customer_postal_code'   => is_var($data['order'], 'order_postal_code'),
				'customer_state'         => is_var($data['order'], 'order_state'),
				'customer_country'       => is_var($data['order'], 'order_country'),
				'customer_telephone'     => is_var($data['order'], 'order_telephone'),
				'customer_primary_email' => is_var($data['order'], 'order_primary_email'),
				'shipping_name'          => is_var($data['order'], 'shipping_name'),
				'shipping_company'       => is_var($data['order'], 'shipping_company'),
				'shipping_address_1'     => is_var($data['order'], 'shipping_address_1'),
				'shipping_address_2'     => is_var($data['order'], 'shipping_address_2'),
				'shipping_city'          => is_var($data['order'], 'shipping_city'),
				'shipping_postal_code'   => is_var($data['order'], 'shipping_postal_code'),
				'shipping_state'         => is_var($data['order'], 'shipping_state'),
				'shipping_country'       => is_var($data['order'], 'shipping_country'),
				'date_purchased'         => empty($data['date_ordered']) ? get_time(now(), TRUE) : $data['date_ordered'],
				'due_date'               => empty($data['due_date']) ? $due_date : $data['due_date'],
				'total'                  => $total,
				'ip_address'             => is_var($data['order'], 'ip_address', FALSE, $CI->input->ip_address()),
				'language_id'            => $data['order']['language_id'],
				'currency_id'            => $data['order']['currency_id'],
				'currency_code'          => $data['order']['currency_code'],
				'currency_value'         => $data['order']['currency_value'],
				'invoice_notes'          => is_var($data, 'invoice_notes'),
				'affiliate_id'           => config_enabled('sts_affiliate_enable_recurring_commission') ? is_var($data['order'], 'affiliate_id') : '0',
				'tracking_data'          => '',
			);

			break;

		case 'order':  //create invoice from an order
			$invoice = array(
				'invoice_number'         => generate_invoice_number(),
				'order_id'               => is_var($data['order'], 'order_id'),
				'payment_status_id'      => '1', //unpaid
				'member_id'              => is_var($data['order'], 'member_id'),
				'customer_name'          => check_order_field($data['order'], 'order_name'),
				'customer_company'       => check_order_field($data['order'], 'order_company'),
				'customer_address_1'     => is_var($data['order'], 'order_address_1'),
				'customer_address_2'     => is_var($data['order'], 'order_address_2'),
				'customer_city'          => is_var($data['order'], 'order_city'),
				'customer_postal_code'   => is_var($data['order'], 'order_postal_code'),
				'customer_state'         => is_var($data['order'], 'order_state'),
				'customer_country'       => is_var($data['order'], 'order_country'),
				'customer_telephone'     => is_var($data['order'], 'order_telephone'),
				'customer_primary_email' => is_var($data['order'], 'order_primary_email'),
				'shipping_name'          => is_var($data['order'], 'shipping_name'),
				'shipping_company'       => is_var($data['order'], 'shipping_company'),
				'shipping_address_1'     => is_var($data['order'], 'shipping_address_1'),
				'shipping_address_2'     => is_var($data['order'], 'shipping_address_2'),
				'shipping_city'          => is_var($data['order'], 'shipping_city'),
				'shipping_postal_code'   => is_var($data['order'], 'shipping_postal_code'),
				'shipping_state'         => is_var($data['order'], 'shipping_state'),
				'shipping_country'       => is_var($data['order'], 'shipping_country'),
				'date_purchased'         => empty($data['date_purchased']) ? get_time(now(), TRUE) : $data['date_purchased'],
				'due_date'               => empty($data['due_date']) ? get_time(now(), TRUE) : $data['due_date'],
				'total'                  => $data['totals']['total_with_shipping'],
				'ip_address'             => is_var($data['order'], 'ip_address', $CI->input->ip_address()),
				'language_id'            => $data['order']['language_id'],
				'currency_id'            => $data['order']['currency_id'],
				'currency_code'          => $data['order']['currency_code'],
				'currency_value'         => $data['order']['currency_value'],
				'invoice_notes'          => is_var($data, 'invoice_notes'),
				'affiliate_id'           => is_var($data['order'], 'affiliate_id'),
				'tracking_data'          => '',
			);

			break;

		case 'manual': //create a single invoice from the admin area

			//get billing address
			$billing = $CI->dbv->get_record(TBL_MEMBERS_ADDRESSES, 'id', $data['billing_address_id']);

			//get shipping address
			if (!empty($data['shipping_address_id']))
			{
				$shipping = $CI->dbv->get_record(TBL_MEMBERS_ADDRESSES, 'id', $data['shipping_address_id']);
			}

			$invoice = array(
				'invoice_number'         => generate_invoice_number(),
				'order_id'               => is_var($data, 'order_id'),
				'payment_status_id'      => is_var($data, 'payment_status_id', FALSE, 1),
				'member_id'              => is_var($data, 'member_id'),
				'customer_name'          => check_order_field($billing, 'name'),
				'customer_company'       => is_var($billing, 'company'),
				'customer_address_1'     => is_var($billing, 'address_1'),
				'customer_address_2'     => is_var($billing, 'address_2'),
				'customer_city'          => is_var($billing, 'city'),
				'customer_postal_code'   => is_var($billing, 'postal_code'),
				'customer_state'         => is_var($billing, 'state'),
				'customer_country'       => is_var($billing, 'country'),
				'customer_telephone'     => is_var($data, 'customers_telephone', FALSE, is_var($billing, 'phone')),
				'customer_primary_email' => is_var($data, 'customers_primary_email'),
				'shipping_name'          => check_order_field($shipping, 'name'),
				'shipping_company'       => is_var($shipping, 'company'),
				'shipping_address_1'     => is_var($shipping, 'address_1'),
				'shipping_address_2'     => is_var($shipping, 'address_2'),
				'shipping_city'          => is_var($shipping, 'city'),
				'shipping_postal_code'   => is_var($shipping, 'postal_code'),
				'shipping_state'         => is_var($shipping, 'state'),
				'shipping_country'       => is_var($shipping, 'country'),
				'date_purchased'         => empty($data['date_purchased']) ? get_time(now(), TRUE) : $data['date_purchased'],
				'due_date'               => empty($data['due_date']) ? get_time(now(), TRUE) : $data['due_date'],
				'total'                  => calc_invoice_amount($data),
				'ip_address'             => is_var($data, 'ip_address', $CI->input->ip_address()),
				'language_id'            => sess('default_lang_id'),
				'currency_id'            => $CI->config->item('currency_id', 'currency'),
				'currency_code'          => $CI->config->item('code', 'currency'),
				'currency_value'         => $CI->config->item('value', 'currency'),
				'invoice_notes'          => is_var($data, 'invoice_notes'),
				'affiliate_id'           => is_var($data, 'affiliate_id'),
				'tracking_data'          => '',
			);

			break;

		default: //create the invoice via checkout

			$invoice = array(
				'invoice_number'         => generate_invoice_number(),
				'order_id'               => is_var($data['order'], 'order_id'),
				'payment_status_id'      => $data['cart']['totals']['total_with_shipping'] > 0 ? '1' : '2', //unpaid
				'member_id'              => is_var($data['member'], 'member_id'),
				'customer_name'          => check_order_field($data['customer'], 'name'),
				'customer_company'       => check_order_field($data['customer'], 'company'),
				'customer_address_1'     => is_var($data['customer'], 'billing_address_1'),
				'customer_address_2'     => is_var($data['customer'], 'billing_address_2'),
				'customer_city'          => is_var($data['customer'], 'billing_city'),
				'customer_postal_code'   => is_var($data['customer'], 'billing_postal_code'),
				'customer_state'         => is_var($data['customer'], 'billing_state'),
				'customer_country'       => is_var($data['customer'], 'billing_country'),
				'customer_telephone'     => is_var($data['customer'], 'phone'),
				'customer_primary_email' => is_var($data['customer'], 'primary_email'),
				'shipping_name'          => check_order_field($data['customer'], 'name', 'shipping'),
				'shipping_company'       => is_var($data['customer'], 'shipping_company'),
				'shipping_address_1'     => is_var($data['customer'], 'shipping_address_1'),
				'shipping_address_2'     => is_var($data['customer'], 'shipping_address_2'),
				'shipping_city'          => is_var($data['customer'], 'shipping_city'),
				'shipping_postal_code'   => is_var($data['customer'], 'shipping_postal_code'),
				'shipping_state'         => is_var($data['customer'], 'shipping_state'),
				'shipping_country'       => is_var($data['customer'], 'shipping_country'),
				'date_purchased'         => empty($data['date_purchased']) ? get_time(now(), TRUE) : $data['date_purchased'],
				'due_date'               => empty($data['due_date']) ? get_time(now(), TRUE) : $data['due_date'],
				'total'                  => $data['cart']['totals']['total_with_shipping'],
				'ip_address'             => $CI->input->ip_address(),
				'language_id'            => $data['language'],
				'currency_id'            => $data['currency']['currency_id'],
				'currency_code'          => $data['currency']['code'],
				'currency_value'         => $data['currency']['value'],
				'invoice_notes'          => is_var($data, 'invoice_notes'),
				'affiliate_id'           => is_var($data['affiliate'], 'member_id'),
				'tracking_data'          => is_var($data, 'affiliate', TRUE),
			);

			break;
	}

	return $invoice;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param array $data
 * @param string $type
 * @return array
 */
function format_invoice_items($id = '', $data = array(), $type = 'checkout')
{
	//set order items
	$items = array();

	switch ($type)
	{
		case 'cron':

			$vars = array(
				'invoice_id'        => $id,
				'product_id'        => $data['order']['items']['data']['product_id'],
				'invoice_item_name' => $data['order']['items']['data']['order_item_name'],
				'item_notes'        => format_invoice_notes($data['order']['items']['data']['attribute_data']),
				'quantity'          => $data['order']['items']['data']['quantity'],
				'product_sku'       => $data['order']['items']['data']['product_sku'],
				'unit_price'        => $data['order']['items']['data']['unit_price'],
			);

			array_push($items, $vars);

			break;

		case 'order': //create invoice from an order

			if (!empty($data['items']))
			{
				foreach ($data['items'] as $v)
				{
					$vars = array(
						'invoice_id'        => $id,
						'product_id'        => $v['product_id'],
						'invoice_item_name' => $v['product_name'],
						'item_notes'        => !empty($v['attribute_data']) ? format_invoice_notes($v['attribute_data'], FALSE) : '',
						'quantity'          => $v['quantity'],
						'product_sku'       => $v['product_sku'],
						'unit_price'        => $v['unit_price'],
					);

					array_push($items, $vars);
				}

				//now check for coupon discounts
				if (!empty($data['totals']['coupon_codes']))
				{
					foreach ($data['totals']['coupon_codes'] as $c)
					{
						$vars = array(
							'invoice_id'        => $id,
							'product_id'        => 0,
							'invoice_item_name' => lang('coupon_code') . ' - (' . $c['text'] . ')',
							'item_notes'        => '',
							'quantity'          => 1,
							'product_sku'       => '',
							'unit_price'        => is_var($data['totals'], 'coupons'),
						);
					}

					array_push($items, $vars);
				}
			}

			break;

		case 'manual':

			if (!empty($data['items']))
			{
				foreach ($data['items'] as $v)
				{
					$vars = array(
						'invoice_id'        => $id,
						'product_id'        => '0',
						'invoice_item_name' => $v['invoice_item_name'],
						'item_notes'        => $v['item_notes'],
						'quantity'          => $v['quantity'],
						'product_sku'       => '0',
						'unit_price'        => $v['unit_price'],
					);

					array_push($items, $vars);
				}
			}

			break;

		default:

			if (!empty($data['order']['items']))
			{
				foreach ($data['order']['items'] as $v)
				{
					$vars = array(
						'invoice_id'        => $id,
						'product_id'        => $v['product_id'],
						'invoice_item_name' => $v['order_item_name'],
						'item_notes'        => !empty($v['attribute_data']) ? format_invoice_notes($v['attribute_data']) : '',
						'quantity'          => $v['quantity'],
						'product_sku'       => $v['product_sku'],
						'unit_price'        => $v['unit_price'],
					);

					array_push($items, $vars);
				}

				//add discounts from groups
				if (!empty($data['cart']['totals']['discounts']))
				{
					$vars = array(
						'invoice_id'        => $id,
						'product_id'        => 0,
						'invoice_item_name' => lang('discounts'),
						'item_notes'        => '',
						'quantity'          => 1,
						'weight'            => 0,
						'product_sku'       => '',
						'unit_price'        => $data['cart']['totals']['discounts'],
					);


					if (!empty($data['member']['disc_group_name']))
					{
						$vars['invoice_item_name'] .= ' - (' . $data['member']['disc_group_name'] . ')';
					}

					array_push($items, $vars);
				}

				//now check for coupon discounts
				if (!empty($data['cart']['totals']['coupon_codes']))
				{
					foreach ($data['cart']['totals']['coupon_codes'] as $c)
					{
						$vars = array(
							'invoice_id'        => $id,
							'product_id'        => 0,
							'invoice_item_name' => lang('coupon_code') . ' - (' . $c['text'] . ')',
							'item_notes'        => '',
							'quantity'          => 1,
							'weight'            => 0,
							'product_sku'       => '',
							'unit_price'        => check_coupon_amount($data['cart']['totals']),
						);
					}

					array_push($items, $vars);
				}

				//now check for gift certificates
				if (!empty($data['cart']['totals']['gift_certificates']))
				{
					$vars = array(
						'invoice_id'        => $id,
						'product_id'        => 0,
						'invoice_item_name' => lang('gift_certificate') . ' - <br /><small>' . $data['cart']['totals']['gift_certificate']['code'] . '</small>',
						'item_notes'        => '',
						'quantity'          => 1,
						'weight'            => 0,
						'product_sku'       => '',
						'unit_price'        => check_certificate_amount($data['cart']['totals']),
					);

					array_push($items, $vars);
				}
			}

			break;

	}

	return $items;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param array $data
 * @param string $type
 * @return array
 */
function format_invoice_totals($id = '', $data = array(), $type = 'checkout')
{
	$totals = array();

	switch ($type)
	{
		case 'cron':

			//get shipping if any
			if (!empty($data['order']['shipping']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'shipping',
					'description' => lang('shipping_description'),
					'amount'      => $data['order']['shipping']['rate'],
					'sub_data'    => '',
					'sort_order'  => 2,
				);

				array_push($totals, $vars);
			}

			//get tax
			if (!empty($data['order']['taxes']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'tax',
					'description' => lang('taxes'),
					'amount'      => is_var($data['order'], 'taxes'),
					'sort_order'  => 3,
				);

				array_push($totals, $vars);
			}

			//get rewards
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'points',
				'description' => lang('rewards_points'),
				'amount'      => $data['order']['items']['data']['points'],
				'sort_order'  => 3,
			);

			array_push($totals, $vars);

			//get total
			$total = $data['order']['items']['data']['unit_price'] * $data['order']['items']['data']['quantity'];

			//get sub total
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'sub_total',
				'description' => lang('sub_total'),
				'amount'      => $total,
				'sort_order'  => 1,
			);

			array_push($totals, $vars);

			if (!empty($data['order']['shipping']['rate']))
			{
				$total += $data['order']['shipping']['rate'];
			}

			if (!empty($data['order']['taxes']))
			{
				$total += $data['order']['taxes'];
			}

			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'total',
				'description' => lang('total'),
				'amount'      => $total,
				'sort_order'  => 5,
			);

			array_push($totals, $vars);

			break;

		case 'order':

			//get shipping if any
			if (!empty($data['totals']['shipping']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'shipping',
					'description' => lang('shipping_description'),
					'amount'      => $data['totals']['shipping'],
					'sub_data'    => is_var($data['totals']['shipping'], 'shipping_taxes', TRUE),
					'sort_order'  => 2,
				);

				array_push($totals, $vars);
			}

			//get tax
			if (!empty($data['totals']['taxes']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'tax',
					'description' => lang('taxes'),
					'amount'      => is_var($data['totals'], 'taxes'),
					'sort_order'  => 3,
				);

				array_push($totals, $vars);
			}

			//get rewards
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'points',
				'description' => lang('rewards_points'),
				'amount'      => get_total_points($data['order']['items']),
				'sort_order'  => 4,
			);

			array_push($totals, $vars);

			//get sub total
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'sub_total',
				'description' => lang('sub_total'),
				'amount'      => $data['totals']['sub_total'],
				'sort_order'  => 1,
			);

			array_push($totals, $vars);

			//get total
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'total',
				'description' => lang('total'),
				'amount'      => $data['totals']['total_with_shipping'],
				'sort_order'  => 5,
			);

			array_push($totals, $vars);

			break;

		case 'manual':

			//get shipping if any
			if (!empty($data['shipping_amount']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'shipping',
					'description' => lang('shipping_description'),
					'amount'      => $data['shipping_amount'],
					'sub_data'    => '',
					'sort_order'  => 2,
				);

				array_push($totals, $vars);
			}

			//get tax
			if (!empty($data['tax_amount']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'tax',
					'description' => lang('taxes'),
					'amount'      => is_var($data, 'tax_amount'),
					'sort_order'  => 3,
				);

				array_push($totals, $vars);
			}

			//get rewards
			if (!empty($data['reward_points']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'points',
					'description' => lang('rewards_points'),
					'amount'      => is_var($data, 'reward_points'),
					'sort_order'  => 4,
				);

				array_push($totals, $vars);
			}

			//get sub total
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'sub_total',
				'description' => lang('sub_total'),
				'amount'      => calc_invoice_totals($data['items']),
				'sort_order'  => 1,
			);

			array_push($totals, $vars);

			//get total
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'total',
				'description' => lang('total'),
				'amount'      => calc_invoice_amount($data),
				'sort_order'  => 6,
			);

			array_push($totals, $vars);

			break;

		default:

			//get shipping if any
			if (!empty($data['shipping']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'shipping',
					'description' => lang('shipping_description'),
					'amount'      => $data['cart']['totals']['shipping'],
					'sub_data'    => is_var($data['shipping'], 'shipping_taxes', TRUE),
					'sort_order'  => 2,
				);

				array_push($totals, $vars);
			}

			//get tax
			if (!empty($data['cart']['totals']['taxes']))
			{
				$vars = array(
					'invoice_id'  => $id,
					'type'        => 'tax',
					'description' => lang('taxes'),
					'amount'      => is_var($data['cart']['totals'], 'taxes'),
					'sort_order'  => 3,
				);

				array_push($totals, $vars);
			}

			//get rewards
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'points',
				'description' => lang('rewards_points'),
				'amount'      => get_total_points($data['order']['items']),
				'sort_order'  => 4,
			);

			array_push($totals, $vars);

			//get sub total
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'sub_total',
				'description' => lang('sub_total'),
				'amount'      => $data['cart']['totals']['sub_total'],
				'sort_order'  => 1,
			);

			array_push($totals, $vars);

			//get total
			$vars = array(
				'invoice_id'  => $id,
				'type'        => 'total',
				'description' => lang('total'),
				'amount'      => $data['cart']['totals']['total_with_shipping'],
				'sort_order'  => 6,
			);

			array_push($totals, $vars);

			break;
	}

	return $totals;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function format_invoice_notes($data = array())
{
	$notes = '';

	$vars = is_array($data) ? $data : unserialize($data);

	if (!empty($vars))
	{
		foreach ($vars as $v)
		{
			if (!empty($v['attribute_name']))
			{
				$notes .= $v['attribute_name'] . " - ";

				switch ($v['attribute_type'])
				{
					case 'select':
					case 'radio':
					case 'textbox':
					case 'image':

						$notes .= !empty($v['option_name']) ? $v['option_name'] : '';

						break;

					case 'file':

						$notes .= !empty($v['file_name']) ? $v['file_name'] : '';

						break;

					default:

						$notes .= !empty($v['value']) ? $v['value'] : '';

						break;
				}

				$notes .= "\n";
			}
		}
	}

	return $notes;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param array $data
 * @param string $type
 * @return array
 */
function format_payment_data($id = '', $data = array(), $type = 'invoice')
{
	switch ($type)
	{
		case 'invoice':

			$vars = array(
				'invoice_id'     => $id,
				'date'           => empty($data['payment_date']) ? get_time(now(), TRUE) : $data['payment_date'],
				'amount'         => $data['transaction']['amount'],
				'fee'            => is_var($data['transaction'], 'fee'),
				'transaction_id' => $data['transaction']['transaction_id'],
				'method'         => $data['transaction']['module'],
				'description'    => $data['transaction']['description'],
				'notes'          => is_var($data['transaction'], 'payment_notes'),
				'debug_info'     => !empty($data['transaction']['debug']) ? base64_encode($data['transaction']['debug']) : '',
				'cc_type'        => is_var($data['transaction']['card_data'], 'cc_type'),
				'cc_last_four'   => is_var($data['transaction']['card_data'], 'cc_four'),
				'cc_month'       => is_var($data['transaction']['card_data'], 'cc_month'),
				'cc_year'        => is_var($data['transaction']['card_data'], 'cc_year'),
			);

			break;
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return int|mixed
 */
function get_subtotal($data = array())
{
	$a = 0;

	foreach ($data as $v)
	{
		if ($v['type'] == 'sub_total')
		{
			$a = $v['amount'];
		}
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed
 */
function invoice_number($data = array())
{
	//show the type of invoice number
	$invoice = empty($data['invoice_number']) ? $data['invoice_id'] : $data['invoice_number'];

	return $invoice;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_credit_payment($data = array())
{
	if (!empty($data['credits']))
	{
		foreach ($data['totals'] as $v)
		{
			if ($v['type'] == 'total')
			{
				$invoice = $v['amount'];
			}
		}

		if ($data['credits']['amount'] >= $invoice)
		{
			$data['credits']['amount'] = $data['credits']['amount'] - $invoice;
			$payment = $invoice;
		}
		else
		{
			$payment = $data['credits']['amount'];
			$data['credits']['amount'] = '0';
		}

		$data['credits']['invoice_id'] = $data['invoice_id'];
		$data['credits']['notes'] .= lang('applied_payment') . ' ' . lang('invoice_id') . ' ' . $data['invoice_number'] . ' ' . lang('amount') . ' ' . $payment . "\n";

		$data['payment'] = array(
			'date'           => get_time(now(), TRUE),
			'method'         => 'credit',
			'transaction_id' => $data['credits']['transaction_id'],
			'invoice_id'     => $data['invoice_id'],
			'amount'         => $payment,
			'fee'            => '0',
			'description'    => lang('applied_credit'),
			'cc_type'        => 'Visa',
			'cc_last_four'   => '',
			'cc_month'       => '01',
			'cc_year'        => date('Y'),
			'notes'          => lang('applied_credit') . ' ' . lang('invoice_id') . ' ' . $data['invoice_number'],
		);
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * Update next invoice number
 */
function update_next_invoice_number()
{
	$CI = &get_instance();

	$CI->db->where('settings_key', 'sts_invoice_next_invoice_number');
	$CI->db->update('settings', array('settings_value' => $CI->config->item('sts_invoice_next_invoice_number') + 1));
}

/* End of file invoices_helper.php */
/* Location: ./application/helpers/invoices_helper.php */