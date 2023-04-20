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
 * @param string $total
 * @param string $str
 * @return string
 */
function check_plural($total = '', $str = '')
{
	$str = $total > 1 ? plural($str) : singular($str);

	return $total . ' ' . $str;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @param bool $show_symbols
 * @param bool $convert
 * @return int|string
 */
function format_totals($data = array(), $type = '', $show_symbols = TRUE, $convert = TRUE)
{
	$total = 0;

	switch ($type)
	{
		case 'invoice':

			$total = $data['sub_total'] + $data['tax_amount'] + $data['shipping_amount'] - $data['discount_amount'];

			break;
	}

	//format the amount with symbols and currency conversion
	return format_amount($total, $show_symbols, $convert);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return int|mixed|string
 */
function product_price($data = array(), $prediscount = FALSE, $regular = FALSE)
{
	//show the product price and check if it discounted or requires login, etc.

	//first if the user must be logged in to view the price
	if ($data['login_for_price'] == 1 && !sess('user_logged_in'))
	{
		return lang('please_login_to_view_price');
	}

	//set what type of product this is first
	switch ($data['product_type'])
	{
		case 'subscription':

			$price = !empty($data['enable_initial_amount']) ? $data['initial_amount'] : $data['amount'];

			break;

		case 'third_party':

			$price =  $data['product_price'];

			break;

		default:

			$price =  $data['product_price'];

			if ($regular == FALSE)
			{
				//check if we have a sale price if not set the default price
				$price = sale_price($data, '');

				//check if the user is logged in
				if (sess('user_logged_in') && $prediscount == FALSE)
				{
					$price = discount_price($price, $_SESSION);
				}
			}

			break;
	}

	//format the price
	if (config_enabled('sts_tax_product_display_price_with_tax'))
	{
		return format_amount($price);
	}

	return format_amount(format_price($price, $data));
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool $format
 * @return int|mixed|string
 */
function cart_unit_price($data = array(), $format = TRUE)
{
	$price = ($data['unit_price']) + ($data['discount_amount']);

	if (!empty($data['item_coupons']))
	{
		$price += $data['item_coupons'];
	}

	if ($price <= 0)
	{
		$price = 0;
	} //set prices to zero on negatives

	if (config_enabled('sts_tax_product_display_price_with_tax'))
	{
		$p = $price;
	}
	else
	{
		$p = format_price($price, $data);
	}

	return $format == TRUE ? format_amount($p, TRUE) : $p;
}


// ------------------------------------------------------------------------

/**
 *  * Format amount
 *
 * Format the specified amount for local conversion
 * currency symbols
 *
 * @param int|string $amount
 * @param bool|TRUE $show_symbols
 * @param bool|TRUE $convert
 * @param bool $non_zero
 * @param bool $decimal
 * @return float|int|string
 */
function format_amount($amount = '0', $show_symbols = TRUE, $convert = TRUE, $non_zero = TRUE, $decimal = FALSE)
{
	$CI = &get_instance();

	//set the localized currency symbols
	$c = $CI->config->item('currency');

	if (config_enabled('sts_cart_allow_currency_conversion'))
	{
		if (config_item('custom_currency_array'))
		{
			$c = config_item('custom_currency_array');
		}
	}

	$amount = round($amount, $c['decimal_places'], PHP_ROUND_HALF_DOWN);

	//are we converting to a different currency?
	if ($convert == TRUE)
	{
		$amount = $amount * $c['value'];
	}

	//don't show the negative!
	if ($non_zero == FALSE && $amount < 0)
	{
		$amount = 0;
	}

	$point = $decimal == TRUE ? '.' : $c['decimal_point'];

	//are we showing dollar signs?
	if ($show_symbols == TRUE)
	{
		//how the amount will look
		$amount = number_format($amount, $c['decimal_places'], $point, $c['thousands_point']);
		$amount = $c['symbol_left'] . $amount . $c['symbol_right'];
	}
	else
	{
		//how the amount will look
		$amount = number_format($amount, $c['decimal_places'], $point, '');
	}

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param string $amount
 * @return float|int|string
 */
function input_amount($amount = '0')
{
	return format_amount((float)$amount, FALSE, FALSE, TRUE, TRUE);
}

// ------------------------------------------------------------------------


function check_currency($str = '')
{
	if (sess('custom_currency'))
	{
		$a = config_item('currency');
		return $a['code'];
	}

	return config_item($str);
}

// ------------------------------------------------------------------------

/**
 * Sale price
 *
 * Generate the sale price of the product with
 * option for adding / subtracting attribute option amounts
 *
 * @param array $data
 * @param array $post
 * @return int|mixed
 */
function sale_price($data = array(), $post = array())
{
	$amount = $data['product_sale_price'] > 0 ? $data['product_sale_price'] : $data['product_price'];

	$amount = check_attribute_price($post, $amount);

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param array $cert
 * @return array
 */
function check_cert_value($cert = array())
{
	$data = update_cart_totals(sess('cart_details'), FALSE);

	foreach ($data as $k => $v)
	{
		$data[$k] = format_amount($v);
	}

	$data['cert_amount'] = format_amount($cert['amount'] * '-1');

	$data['cert_code'] = $cert['code'];

	return $data;

}

// ------------------------------------------------------------------------

/**
 * @param string $amount
 * @param array $data
 * @return float|int|mixed|string
 */
function discount_price($amount = '', $data = array())
{
	$discount = 0;

	if (!empty($data['disc_group_amount']) && !empty($data['disc_type']))
	{
		$discount = $data['disc_type'] == 'percent' ? $amount * show_percent($data['disc_group_amount']) : $data['disc_group_amount'];
	}

	$a = $amount - $discount;

	return $a < 0 ? 0 : $a;
}

// ------------------------------------------------------------------------

/**
 * @param int $amount
 * @param array $tax
 * @return float|int|string
 */
function unit_price($amount = 0, $tax = array())
{
	if (config_enabled('sts_tax_product_display_price_with_tax'))
	{
		if (config_enabled('sts_tax_use_compound_tax_amounts'))
		{
			$amount -= $tax['taxes_compounded'];
		}
		else
		{
			$amount -= $tax['taxes'];
		}
	}

	return format_amount($amount, FALSE, FALSE, TRUE, TRUE);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param int $amount
 * @param bool $unserialize
 * @return int
 */
function check_attribute_price($data = array(), $amount = 0, $unserialize = FALSE)
{
	//check if we are adding extra amounts per item through attributes

	if (!empty($data['attribute_data']))
	{
		if ($unserialize == TRUE)
		{
			$data['attribute_data'] = unserialize($data['attribute_data']);
		}

		foreach ($data['attribute_data'] as $v)
		{
			if (!empty($v['price']))
			{
				if ($v['price'] > 0)
				{
					$amount = $v['price_add'] == '-' ? $amount - $v['price'] : $amount + $v['price'];
				}
			}
		}
	}

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param $amount
 * @param $data
 * @return float|int|string
 */
function get_coupon_amount($amount, $data)
{
	$coupon = 0;

	if (!empty($data['coupon_data']) && is_array($data['coupon_data']))
	{
		$coupon = $data['coupon_data']['percent'] == 'percent' ? $amount * show_percent($data['coupon_data']['amount']) : $data['coupon_data']['amount'];
	}

	return format_amount($coupon, FALSE, FALSE, TRUE, TRUE);
}

// ------------------------------------------------------------------------

/**
 * @param $amount
 * @param $data
 * @param bool $format
 * @return float|int|mixed|string
 */
function get_discount_amount($amount, $data, $format = FALSE)
{
	$discount = '0';

	if (!empty($data['discount_data']) && is_array($data['discount_data']))
	{
		foreach ($data['discount_data'] as $e)
		{
			if (!empty($e['discount_type']))
			{
				$d = $e['discount_type'] == 'flat' ? $e['amount'] : $amount * show_percent($e['amount']);
				$amount -= $d;
				$discount += $d;
			}
		}
	}

	return $format == FALSE ? format_amount($discount, FALSE, FALSE, TRUE, TRUE) : $amount;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function cart_pricing($data = array())
{
	if (!empty($data['pricing_data']))
	{
		$a = unserialize($data['pricing_data']);

		$price = '';

		//check for trial amounts
		$a['amount'] = check_attribute_price($data, $a['amount'], TRUE);

		if (!empty($a['enable_initial_amount']))
		{
			$price .= lang('for') . ' ' . $a['initial_interval'] . ' ';
			$price .= $a['initial_interval'] > 1 ? plural($a['initial_interval_type']) : $a['initial_interval_type'];
			$price .= ' ' . lang('then') . '<br />';

			if (!empty($data['discount_data']) && is_array($data['discount_data']))
			{
				foreach ($data['discount_data'] as $e)
				{
					if (!empty($e['discount_type']))
					{
						$a['amount'] -= $e['discount_type'] == 'flat' ? $e['amount'] : $a['amount'] * show_percent($e['amount']);
					}
				}
			}

			$price .= ' ' . format_amount($a['amount']);
		}

		$price .= ' ' . lang($a['name']);

		return $price;
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $amount
 * @param array $data
 * @param bool $tax
 * @return float|int|string
 */
function format_price($amount = '', $data = array(), $tax = TRUE)
{
	//check if taxes are set for this product
	if ($tax == TRUE && config_enabled('sts_tax_enable_tax_calculations'))
	{
		if (!empty($data['taxes']))
		{
			$amount = unit_price($amount, calc_tax($amount, set_tax_array($data['taxes'])));
		}
	}

	return $amount;
}

/**
 * Calculate discount
 *
 * check if a specific item has a discounted amount for the group
 * or for that item only
 *
 * @param        $amount
 * @param string $id
 * @param array $data
 * @param int $qty
 *
 * @return int
 */

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function calculate_group_discount($data = array())
{
	$amount = array('discount' => 0,
	                'data'     => array(),
	);

	if (!empty($data['groups']))
	{
		foreach ($data['groups'] as $v)
		{
			if ($v['group_id'] == $data['group_id'])
			{
				if ($data['quantity'] >= $v['quantity'])
				{
					$amount['discount'] = $v['discount_type'] == 'flat' ? $v['group_amount'] : rd($data['amount'] * show_percent($v['group_amount']));
					$amount['data'] = array(
						'type'          => 'group_discount',
						'quantity'      => $v['quantity'],
						'amount'        => $v['group_amount'],
						'discount_type' => $v['discount_type'],
						'points'        => $v['points'],
						'expires'       => display_date($v['sql_end'], FALSE, 3),
					);

					return $amount;
				}
			}
		}
	}


	if (sess('disc_group_amount'))
	{
		//check if there is a default discount amount since there isn't one for the product specifically
		$amount['discount'] = sess('disc_type') == 'flat' ? sess('disc_group_amount') : $data['amount'] * show_percent(sess('disc_group_amount'));

		$amount['data'] = array(
			'type'          => 'group_discount',
			'quantity'      => 1,
			'amount'        => sess('disc_group_amount'),
			'discount_type' => sess('disc_type'),
			'points'        => '0',
		);
	}

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param $num
 * @param int $c
 * @return string
 */
function num2words($num, $c = 1)
{
	$ZERO = 'zero';
	$MINUS = 'minus';
	$lowName = array(
		/* zero is shown as "" since it is never used in combined forms */
		/* 0 .. 19 */
		"", "one", "two", "three", "four", "five",
		"six", "seven", "eight", "nine", "ten",
		"eleven", "twelve", "thirteen", "fourteen", "fifteen",
		"sixteen", "seventeen", "eighteen", "nineteen");

	$tys = array(
		/* 0, 10, 20, 30 ... 90 */
		"", "", "twenty", "thirty", "forty", "fifty",
		"sixty", "seventy", "eighty", "ninety");

	$groupName = array(
		/* We only need up to a quintillion, since a long is about 9 * 10 ^ 18 */
		/* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
		"", "hundred", "thousand", "million", "billion",
		"trillion", "quadrillion", "quintillion");

	$divisor = array(
		/* How many of this group is needed to form one of the succeeding group. */
		/* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
		100, 10, 1000, 1000, 1000, 1000, 1000, 1000);

	$num = str_replace(",", "", $num);
	$num = number_format($num, 2, '.', '');
	$cents = substr($num, strlen($num) - 2, strlen($num) - 1);
	$num = (int)$num;

	$s = "";

	if ($num == 0)
	{
		$s = $ZERO;
	}
	$negative = ($num < 0);
	if ($negative)
	{
		$num = -$num;
	}
	// Work least significant digit to most, right to left.
	// until high order part is all 0s.
	for ($i = 0; $num > 0; $i++)
	{
		$remdr = (int)($num % $divisor[$i]);
		$num = $num / $divisor[$i];
		// check for 1100 .. 1999, 2100..2999, ... 5200..5999
		// but not 1000..1099,  2000..2099, ...
		// Special case written as fifty-nine hundred.
		// e.g. thousands digit is 1..5 and hundreds digit is 1..9
		// Only when no further higher order.
		/*
		if ( $i == 1  && 1 <= $num && $num <= 2 ){
			if ( $remdr > 0 ){
				$remdr = ($num * 10);
				$num = 0;
			} // end if
		} // end if
		*/
		if ($remdr == 0)
		{
			continue;
		}
		$t = "";
		if ($remdr < 20)
		{
			$t = $lowName[$remdr];
		}
		else
		{
			if ($remdr < 100)
			{
				$units = (int)$remdr % 10;
				$tens = (int)$remdr / 10;
				$t = $tys [$tens];
				if ($units != 0)
				{
					$t .= "-" . $lowName[$units];
				}
			}
			else
			{
				$t = num2words($remdr, 0);
			}
		}
		$s = $t . " " . $groupName[$i] . " " . $s;
		$num = (int)$num;
	} // end for
	$s = trim($s);
	if ($negative)
	{
		$s = $MINUS . " " . $s;
	}

	if ($c == 1)
	{
		$s .= " and $cents/100";
	}

	return $s;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function total_dropdown($data = array())
{
	$vars = array();

	if (!empty($data))
	{
		for ($i = 1; $i <= count($data); $i++)
		{
			$vars[$i] = $i;
		}
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param string $total
 * @return array
 */
function total_tiers($total = '')
{
	$tiers = array();

	for ($i = 1; $i <= $total; $i++)
	{
		$tiers[$i] = $i;
	}

	return $tiers;
}

/**
 * Quantity total
 *
 * Generate the total amounts for the quantity of the product
 *
 * @param array $data
 * @param bool|FALSE $show_symbols
 *
 * @return int|string
 */

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool|FALSE $show_symbols
 * @return float|int|string
 */
function quantity_total($data = array(), $show_symbols = FALSE)
{
	$price = !empty($data['discount_amount']) ? $data['unit_price'] + $data['discount_amount'] : $data['unit_price'];

	return format_amount($data['quantity'] * unit_price($price, $data['tax_amount']), $show_symbols);
}

/**
 * Show percent
 *
 * Convert a number to the percentage equivalent
 *
 * @param string $num
 *
 * @return float
 */

// ------------------------------------------------------------------------

/**
 * @param string $num
 * @return float|int
 */
function show_percent($num = '')
{
	return $num / 100;
}

// ------------------------------------------------------------------------

function kmbt($n = '') {
	if ($n < 1000)
	{
		// Anything less than a thousand
		$n_format = number_format($n);
	}
	elseif ($n < 1000000) {
		// Anything less than a million
		$n_format = number_format($n / 1000, 0) . 'K';
	} else if ($n < 1000000000) {
		// Anything less than a billion
		$n_format = number_format($n / 1000000, 0) . 'M';
	} else {
		// At least a billion
		$n_format = number_format($n / 1000000000, 0) . 'B';
	}

	return $n_format;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return float|int
 */
function shipping_totals($data = array())
{
	//add up all the shipping costs including taxes and handling

	$amount = 0;

	$amount += !empty($data['shipping_amount']) ? (float)$data['shipping_amount'] : 0;
	$amount += !empty($data['shipping_taxes']['shipping']) ? (float)$data['shipping_taxes']['shipping'] : 0;
	$amount += !empty($data['shipping_taxes']['handling']) ? (float)$data['shipping_taxes']['handling'] : 0;

	return $amount;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return float|int
 */
function calc_weight($data = array(), $type = '')
{
	$CI = &get_instance();

	$t = empty($type) ? $CI->config->item('sts_site_default_weight') : $type;

	//convert weight and standardize it
	foreach ($CI->config->item('weight_options') as $v)
	{
		if ($v['weight_id'] == $t)
		{
			$a = $v['value'];
		}

		if ($v['weight_id'] == $data['weight_type'])
		{
			$b = $v['value'];
		}
	}

	return $data['weight'] * ($a / $b);
}

// ------------------------------------------------------------------------

/**
 * @param bool $uppercase
 * @param int $length
 * @param int $parts
 * @param string $type
 * @return string
 */
function generate_serial($uppercase = SERIAL_CODE_UPPERCASE,
                         $length = SERIAL_CODE_LENGTH,
                         $parts = SERIAL_CODE_PARTS,
                         $type = SERIAL_CODE_STRING_TYPE)
{
	$serial = '';

	for ($i = 0; $i < $parts; $i++)
	{
		$serial .= random_string($type, $length);

		if ($i < ($parts - 1))
		{
			$serial .= '-';
		}
	}

	return $uppercase == TRUE ? strtoupper($serial) : $serial;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function cart_totals($data = array())
{
	$row = array('sub_total'           => 0,
	             'sub_total_discounts' => 0,
	             'taxes'               => 0,
	             'discounts'           => 0,
	             'coupons'             => 0,
	             'gift_certificates'   => 0,
	             'shipping'            => 0,
	             'total'               => 0,
	             'shipping_item'       => array(),
	             'coupon_codes'        => array(),
	             'tax_items'           => array(),
	             'subscription'        => array(),
	);

	foreach ($data['items'] as $k => $p)
	{
		$row['sub_total'] += ($p['unit_price'] * $p['quantity']);

		$row['sub_total_discounts'] += (($p['unit_price'] + $p['discount_amount']) * $p['quantity']);

		//set discounts
		$row['discounts'] += ($p['discount_amount'] * $p['quantity']);

	}

	//check if there are coupons and such
	if (!empty($data['sub_items']))
	{
		foreach ($data['sub_items'] as $v)
		{
			//get coupon calculation and apply it
			if ($v['type'] == 'coupon')
			{
				//how many products are in the cart?
				$total_items = 0;
				foreach ($data['items'] as $p)
				{
					$total_items += $p['quantity'];
				}

				//check if there are specific products to apply the coupon to
				if (!empty($v['sub_data']))
				{
					$v['sub'] = unserialize(($v['sub_data']));
				}

				//calculate coupons for each product
				foreach ($data['items'] as $k => $p)
				{
					if (!empty($v['sub']['required_products']))
					{
						if (!in_array($p['product_id'], $v['sub']['required_products']))
						{
							continue;
						}
					}

					$price = $p['unit_price'] + $p['discount_amount'];
					$data['items'][$k]['item_coupons'] = 0;
					$data['items'][$k]['coupon_data'] = $v;

					//check the type of coupon
					if ($v['percent'] == 'percent')
					{
						$data['items'][$k]['item_coupons'] = -1 * ($price * show_percent($v['amount']));
					}
					else
					{
						$data['items'][$k]['item_coupons'] = -1 * ($v['amount'] / $total_items);
					}

					$row['coupons'] += ($p['quantity'] * $data['items'][$k]['item_coupons']);
				}

				array_push($row['coupon_codes'], $v);
			}
			else
			{
				if ($v['type'] == 'gift_certificate')
				{
					if (defined('TREAT_GIFT_CERTIFICATES_AS_DISCOUNTS'))
					{
						$row['gift_certificates'] += -1 * $v['amount'];
						$row['gift_certificate'] = sess('checkout_gift_certificate_data');
					}
				}
				elseif ($v['type'] == 'shipping')
				{
					$row['shipping'] += $v['amount'];
					$row['shipping_item'] = sess('checkout_shipping_selected');
				}
			}
		}
	}

	//calculate discounts
	foreach ($data['items'] as $k => $p)
	{
		//add discounts and coupons to the unit price first
		$amount = $p['unit_price'] + $p['discount_amount'];

		if (!empty($p['item_coupons']))
		{
			$amount += $p['item_coupons'];
		}

		if (defined('TREAT_GIFT_CERTIFICATES_AS_DISCOUNTS'))
		{
			$amount += ($row['gift_certificates'] / count($data['items']));
		}

		//calculate taxes for each product
		$t = calc_tax($amount, set_tax_array($p['taxes']), $p['item_id']);

		//add tax data to the items array
		$data['items'][$k]['tax_data'] = $t;

		$row['taxes'] += $t['taxes'] * $p['quantity'];

		array_push($row['tax_items'], $t);

		//check for subscriptions
		if ($p['product_type'] == 'subscription')
		{
			$row['subscription'] = calc_subscription($p, $row);
			$row['subscription']['shipping'] = sess('checkout_shipping_selected');
		}
	}

	//add gift certificates if any
	if (!defined('TREAT_GIFT_CERTIFICATES_AS_DISCOUNTS'))
	{
		if (!empty($data['sub_items']))
		{
			foreach ($data['sub_items'] as $v)
			{
				if ($v['type'] == 'gift_certificate')
				{
					$row['gift_certificates'] += -1 * $v['amount'];
					$row['gift_certificate'] = sess('checkout_gift_certificate_data');
				}
			}
		}
	}

	//calculate totals
	$row['total'] = rd($row['sub_total'])
		+ rd($row['discounts'])
		+ rd($row['coupons'])
		+ rd($row['gift_certificates']);

	if (!config_enabled('sts_tax_product_display_price_with_tax'))
	{
		$row['total'] += rd($row['taxes']);
	}

	if (sess('cart_charge_shipping'))
	{
		$row ['total_with_shipping'] = $row['total'] + $row['shipping'];
	}
	else
	{
		$row ['total_with_shipping'] = $row['total'];
		$row['shipping'] = '0';
		$row['shipping_item'] = array();
	}

	if ($row['total'] < 0)
	{
		$row['total'] = 0;
	}

	if ($row['total_with_shipping'] < 0)
	{
		$row['total_with_shipping'] = 0;
	}

	$data['totals'] = $row;

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $cart
 * @return array
 */
function calc_subscription($data = array(), $cart = array())
{

	$row = array('text'           => '',
	             'sub_total'      => '0',
	             'amount'         => '0',
	             'discount'       => '0',
	             'coupon'         => '0',
	             'total_discount' => '0',
	             'taxes'          => '0');


	//get pricing data
	$a = unserialize($data['pricing_data']);

	$row['text'] .= lang('for') . ' ';

	if (!empty($a['enable_initial_amount']) && $a['initial_interval'] > 0)
	{
		$row['text'] .= $a['initial_interval'] . ' ';
		$row['text'] .= $a['initial_interval'] > 1 ? plural($a['initial_interval_type']) : $a['initial_interval_type'];

	}
	else
	{
		$row['text'] .= $a['interval_amount'] . ' ';
		$row['text'] .= $a['interval_amount'] > 1 ? plural($a['interval_type']) : $a['interval_type'];
	}

	$row['text'] .= ' ' . lang('then') . ' ';

	//calculate subscription amount

	//get attribute amount
	$a['amount'] = check_attribute_price($data, $a['amount'], TRUE);

	//get discount
	$row['discount'] += get_discount_amount($a['amount'], $data);
	$a['amount'] -= $row['discount'];

	//get coupon amount
	$row['coupon'] += get_coupon_amount($a['amount'], $data);
	$a['amount'] -= $row['coupon'];

	$row['total_discount'] = $row['discount'] + $row['coupon'];

	$row['sub_total'] = $a['amount'];

	//get taxes
	if (!empty($data['taxes']))
	{
		$t = calc_tax($a['amount'], set_tax_array($data['taxes']));

		if (!config_enabled('sts_tax_product_display_price_with_tax'))
		{
			$a['amount'] += $t['taxes'];
		}

		$row['taxes'] = $t['taxes'];
	}

	//get shipping
	if (!empty($data['charge_shipping']))
	{
		$a['amount'] += $cart['shipping'];
	}

	$row['text'] .= ' ' . format_amount($a['amount']);


	$row['text'] .= ' ' . lang($a['name']);

	$row['amount'] = format_amount($a['amount'], FALSE, FALSE, TRUE, TRUE);
	$row['sub_total'] = format_amount($row['sub_total'], FALSE, FALSE, TRUE, TRUE);

	return $row;
}

// ------------------------------------------------------------------------

/**
 * @param string $num
 * @return float
 */
function rd($num = '')
{
	return round($num, DEFAULT_DECIMAL_ROUNDOFF, DEFAULT_ROUND_UP);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return int|mixed
 */
function cart_subtotal($data = array())
{
	$amount = $data['totals']['sub_total_discounts'];
	$amount += !empty($data['totals']['coupons']) ? $data['totals']['coupons'] : 0;

	return $amount;
}

/* End of file JX_number_helper.php */
/* Location: ./application/helpers/JX_number_helper.php */