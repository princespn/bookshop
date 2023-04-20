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
 * @param string $data
 * @return array|bool
 */
function set_tax_array($data = '')
{
	if (!empty($data))
	{
		//check if there are multiple tax rates separated by /
		$a = explode('/', $data);

		$row = array();

		foreach ($a as $v)
		{
			//now list the tax amounts per zone separated by :
			list($zone_id, $tax_type, $amount_type, $tax_amount, $calculation) = explode(':', $v);

			$b = array('zone_id'     => $zone_id,
			           'tax_type'    => $tax_type,
			           'amount_type' => $amount_type,
			           'tax_amount'  => $tax_amount,
			           'calculation' => $calculation,
			);

			array_push($row, $b);
		}
	}

	return !empty($row) ? $row : FALSE;
}

/**
 * Calculate tax
 *
 * Calculate tax amount for each product
 *
 * @param $amount
 * @param array $data
 * @param $item_id
 * @return mixed
 */
function calc_tax($amount = 0, $data = array(), $item_id = '', $type = 'checkout')
{
	$CI = &get_instance();

	//initialize the tax array
	$row = array('item_id'          => $item_id,
	             'taxes'            => 0,
	             'taxes_compounded' => 0,
	             'tax_items'        => array(),
	             'tax_calc'         => array(),
	);

	if (!empty($data) && $amount > 0)
	{
		$amount_compounded = $amount;

		foreach ($data as $k => $v)
		{
			$v = $CI->zone->check_tax_zone($v, $type);

			if (!empty($v))
			{
				if ($v['tax_type'] != 'shipping')
				{
					array_push($row['tax_calc'], $v);

					//calculate regular taxes
					$tax = $v['amount_type'] == 'percent' ? check_included_tax($amount, show_percent($v['tax_amount'])) : $v['tax_amount'];

					//calculate compounded tax rate
					$tax_cp = $v['amount_type'] == 'percent' ? check_included_tax($amount_compounded, show_percent($v['tax_amount'])) : $v['tax_amount'];
					$amount_compounded += $tax_cp;

					//add to the tax amount
					$row['taxes'] += rd($tax);
					$row['taxes_compounded'] += rd($tax_cp);

					//save each line item
					array_push($row['tax_items'], array('type'           => $v['tax_type'],
					                                    'tax'            => rd($tax),
					                                    'tax_compounded' => rd($tax_cp),
					));

					if (!config_option('sts_tax_use_compound_tax_amounts'))
					{
						return $row;
					}
				}
			}
		}
	}

	return $row;
}

// ------------------------------------------------------------------------

/**
 * @param string $amount
 * @param string $tax
 * @return float|int|string
 */
function check_included_tax($amount = '', $tax = '')
{
	if (config_enabled('sts_tax_product_display_price_with_tax'))
	{
		switch (strtoupper(DEFAULT_VAT_FORMULA))
		{
			case 'EUROPE':

				$a = $amount - ($amount / (1 + $tax));

				break;

			default:

				$a = $amount * $tax;

				break;
		}

	}
	else
	{
		$a = $amount * $tax;
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param $amount
 * @param string $type
 * @return array
 */
function calc_shipping_taxes($amount, $type = '')
{
	$CI = &get_instance();

	$tax = array('sub_total' => $amount, 'shipping' => 0, 'handling' => 0);

	//let's see if we're adding taxes to the shipping amount
	if (config_enabled('module_shipping_' . $type . '_charge_shipping_tax'))
	{
		$taxes = $CI->tax->get_rate_rules($CI->config->item('module_shipping_' . $type . '_tax_class'));

		if (!empty($taxes))
		{
			foreach ($taxes as $v)
			{
				$a = $v['amount_type'] == 'percent' ? $amount * show_percent($v['tax_amount']) : $v['tax_amount'];

				if ($v['tax_type'] == 'shipping')
				{
					$tax['shipping'] += $a;
				}
				elseif ($v['tax_type'] == 'handling')
				{
					$tax['handling'] += $a;
				}
			}
		}
	}

	return $tax;
}


/* End of file tax_helper.php */
/* Location: ./application/helpers/tax_helper.php */