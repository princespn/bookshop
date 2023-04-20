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
 * @param string $type
 * @param array $item
 * @param array $rules
 * @return array|bool
 */
function process_rules($type = 'per_item', $item = array(), $rules = array())
{
	$vars = array('discount' => '0',
	              'data'     => array(),
	);

	if (!empty($rules))
	{
		switch ($type)
		{
			case 'per_item':

				foreach ($rules as $v)
				{
					if (!empty($v['item_id']) && ($item['product_id'] == $v['item_id']))
					{
						switch ($v['type'])
						{
							case 'item_quantity':

								switch ($v['operator'])
								{
									case 'greater_than': //greater than

										if ($item['quantity'] > $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'greater_than_equal_to': //greater than equal to

										if ($item['quantity'] >= $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'equal_to': //equal to

										if ($item['quantity'] = $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'less_than': //less than

										if ($item['quantity'] < $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'less_than_equal_to': //less than equal to

										if ($item['quantity'] <= $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;
								}

								break;

							case 'total_item_price':

								$amount = $item['quantity'] * ($item['unit_price'] - $item['discount_amount']);

								switch ($v['operator'])
								{
									case 'greater_than': //greater than

										if ($amount > $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'greater_than_equal_to': //greater than equal to

										if ($amount >= $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'equal_to': //equal to

										if ($amount = $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'less_than': //less than

										if ($amount < $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;

									case 'less_than_equal_to': //less than equal to

										if ($amount <= $v['amount'])
										{
											return match_rule($v, $item['amount']);
										}

										break;
								}

								break;
						}
					}
				}

				break;

			case 'cart_amount':

				// todo

				break;
		}
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $amount
 * @return bool
 */
function match_rule($data = array(), $amount = '')
{
	$CI = &get_instance();

	//promo data are the actual products or discounts given
	$promo = $CI->promo->get_promotional_items($data['rule_id']);

	if (!empty($promo))
	{
		switch ($data['action'])
		{
			case 'special_offer': //give one of these products if they purchase a specific product

				if (!empty($promo['product_id']))
				{
					$vars['data'] = array(
						'type'         => 'special_offer',
						'quantity'     => $data['amount'],
						'operator'     => $data['operator'],
						'amount'       => $promo['promo_amount'],
						'product_id'   => $promo['product_id'],
						'product_name' => serialize(get_product_names($promo['product_id'])),
					);
				}
				
				break;

			case 'quantity_discount':
				//give a discount if the user purchases a specific quantity

				if ($promo['discount_type'] == 'percent')
				{
					$vars['discount'] = show_percent($promo['promo_amount']) * $amount;
				}
				else
				{
					$vars['discount'] = $promo['promo_amount'];
				}

				$vars['data'] = array(
					'type'          => 'quantity_discount',
					'quantity'      => $data['amount'],
					'operator'      => $data['operator'],
					'amount'        => $promo['promo_amount'],
					'discount_type' => $promo['discount_type'],
				);

				break;

		}

		return empty($vars) ? FALSE : $vars;
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function get_promo_rule($data = array())
{
	$rule = '<div class="text-capitalize">';

	switch ($data['rule'])
	{
		case 'per_item':

			$rule .= lang('if') . ' <span class="rewardLinks">' . lang('item') . '</span> ' .
				lang('in_cart') . ' ' . lang('is') . ' <span class="rewardLinks">' .
				lang($data['item_name']) . '</span> ' . lang('and') . ' 
						<span class="rewardLinks">' . $data['type'] . '</span> ' .
				lang('is') . ' <span class="rewardLinks">' . lang($data['operator']) . '</span> <span class="rewardLinks">';


			$rule .= $data['type'] == 'item_quantity' ? (int) $data['amount'] :  $data['amount'];

			$rule .= ' </span> ' .
				lang('set') . ' <span class="rewardLinks">' . lang($data['action']) . ' ';

			break;

		case 'cart_amount': //@todo

			$rule .= lang('if') . ' <span class="rewardLinks">' . lang($data['sale_type']) . ' ' . lang('is') . ' ' . lang($data['operator']) . ' ' . $data['sale_amount'] . '</span> ' . lang('then') . ' <span class="rewardLinks">' . lang($data['action']) . ' ';

			break;
	}

	$rule .= '</div>';

	return $rule;
}

/* End of file promotional_rules_helper.php */
/* Location: ./application/helpers/promotional_rules_helper.php */