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
class Utilities_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * @param int $decimal
	 * @return array
	 */
	public function change_decimal_types($decimal = 2)
	{
		//table => column
		$tables = array('affiliate_commission_rules'       => array('sale_amount', 'bonus_amount'),
		                'cart_items'                       => array('unit_price', 'discount_amount'),
		                'cart_totals'                      => array('amount'),
		                'invoice_items'                    => array('unit_price'),
		                'invoice_payments'                 => array('amount', 'fee'),
		                'invoice_totals'                   => array('amount'),
		                'members_subscriptions'            => array('product_price'),
		                'module_shipping_flat_rate'        => array('amount'),
		                'module_shipping_free_shipping'    => array('amount'),
		                'module_shipping_percentage'       => array('amount'),
		                'module_shipping_per_item'         => array('amount'),
		                'module_shipping_unit_based'       => array('min_amount', 'max_amount', 'amount'),
		                'orders_gift_certificates_history' => array('amount'),
		                'orders_gift_certificates'         => array('amount', 'redeemed'),
		                'orders_items'                     => array('unit_price', 'discount_amount'),
		                'orders_shipping'                  => array('rate'),
		                'products_to_aff_groups'           => array('commission_level_1',
		                                                            'commission_level_2',
		                                                            'commission_level_3',
		                                                            'commission_level_4',
		                                                            'commission_level_5',
		                                                            'commission_level_6',
		                                                            'commission_level_7',
		                                                            'commission_level_8',
		                                                            'commission_level_9',
		                                                            'commission_level10'),
		                'products_to_attributes_values'    => array('price'),
		                'products_to_pricing'              => array('amount', 'initial_amount'),
		                'promotional_items'                => array('promo_amount'),
		                'tax_rates'                        => array('tax_amount'),
		                'affiliate_commissions'            => array('commission_amount', 'sale_amount', 'fee'),
		                'affiliate_groups'                 => array('fee_amount'),
		                'coupons'                          => array('coupon_amount', 'minimum_order'),
		                'affiliate_payments'               => array('payment_amount'),
		                'invoices'                         => array('total'),
		                'orders'                           => array('order_total'),
		                'products'                         => array('producct_price', 'product_sale_price', 'shipping_cost'),
		);

		foreach ($tables as $t => $c)
		{
			//go through each table as check the column
			$t = 'jrox_' . $t;
			if ($this->db->table_exists($t))
			{
				$fields = $this->db->list_fields($t);

				foreach ($fields as $f)
				{
					if (in_array($f, $c))
					{
						$default = number_format('0', $decimal);
						$sql = "ALTER TABLE `" . $t . "` CHANGE `" . $f . "` `" . $f . "` DECIMAL(" . DEFAULT_COLUMN_DECIMAL_LENGTH . ", " . $decimal . ") NOT NULL DEFAULT '" . $default . "';";

						if (!$q = $this->db->query($sql))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}
					}
				}
			}
		}

		return array('success' => TRUE, 'msg_text' => lang('system_updated_successfully'));
	}
}

/* End of file Updates_model.php */
/* Location: ./application/models/Updates_model.php */