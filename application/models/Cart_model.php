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
class Cart_model extends CI_Model
{
	/**
	 * @var array|bool|string
	 */
	protected $cart = array();

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $cart_id = '';

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $item_key = '';

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $product_item = '';

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $current_qty = '';

	// ------------------------------------------------------------------------

	/**
	 * @var int
	 */
	protected $gc_probability = 10;

	// ------------------------------------------------------------------------

	/**
	 * delete old cart ids after 30 days
	 *
	 * @var int
	 */
	protected $gc_interval = GC_CART_INTERVAL;

	// ------------------------------------------------------------------------

	/**
	 * Cart_model constructor.
	 */
	public function __construct()
	{
		//load shopping cart into session data
		$this->load->helper('cart');

		//set session ID
		$this->cart_id = sess('cart_id');

		//run clean up todo
		$this->cart_gc();
	}

	// ------------------------------------------------------------------------

	/**
	 * Add item
	 *
	 * Adds an item in the cart
	 *
	 * @param array $post
	 * @return bool|string
	 */
	public function add_item($data = array(), $post = array())
	{
		//first check if the product is already in the shopping cart table using the product key
		if (!$this->check_product_item())
		{
			//if not, add it
			$this->insert_row($data, $post);
		}
		else
		{
			//if it is there,  update the quantity
			$this->update_quantity($post['quantity']);
		}

		//now get all the products in the cart and overwrite the $this->session->cart_details
		$this->update_session_cart();

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Add upload
	 *
	 * Add a new uploaded file for the cart for file type options
	 *
	 * @param array $data
	 *
	 * @return bool|string
	 */
	public function add_upload($data = array())
	{
		//add the file to the cart uploads table
		$row = array(
			'key'       => random_string('sha1'),
			'member_id' => sess('member_id'),
			'file_name' => $data['file_data']['file_name'],
		);

		if (!$this->db->insert(TBL_CART_UPLOADS, $row))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$this->update_session_cart();

		$row['success'] = TRUE;
		$row['msg_text'] = lang('file_uploaded_successfully');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function get_random_id($data = array())
	{
		//for selecting a random product in the cart for recommended products
		if (!empty($data['items']))
		{
			$k = array_rand($data['items']);

			return $data['items'][$k]['product_id'];
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * check discounts for the product such as discount groups
	 *
	 * @param array $data
	 * @return bool
	 */
	public function check_discounts($data = array())
	{
		//check if there are promo rules available
		$product_rules = $this->promo->get_rules('per_item');

		if (!empty($data['items']))
		{
			//check for quantity discounts
			foreach ($data['items'] as $v)
			{
				$vars = array('discount_amount' => 0,
				              'discount_data'   => array(),
				);

				//check for group discounts first
				if (sess('discount_group'))
				{
					//lets get the discount groups for each product if any
					$p = $this->prod->get_product_discount_groups($v['product_id'], TRUE);

					$d = calculate_group_discount(array('amount'   => $v['unit_price'],
					                                    'group_id' => sess('discount_group'),
					                                    'groups'   => $p,
					                                    'quantity' => $v['quantity']));

					///set the discount amount
					$vars['discount_amount'] += $d['discount'];

					//add discount description to the cart
					array_push($vars['discount_data'], $d['data']);

				}

				//now check for promotional discounts
				if (!empty($product_rules))
				{
					//set the default amount
					$v['amount'] = $v['unit_price'] - $vars['discount_amount'];

					//get promotional rules for this item
					$promo_rules = process_rules('per_item', $v, $product_rules);

					if (!empty($promo_rules['discount']))
					{
						$vars['discount_amount'] += $promo_rules['discount'];
					}
					if (!empty($promo_rules['data']))
					{
						array_push($vars['discount_data'], $promo_rules['data']);
					}
				}

				//save the discount data for later...
				$vars['discount_data'] = !empty($vars['discount_data']) ? serialize($vars['discount_data']) : '';

				if ($vars['discount_amount'] > $v['unit_price'])
				{
					$vars['discount_amount'] = $v['unit_price'];
				}

				//set to negative value
				$vars['discount_amount'] = -1 * $vars['discount_amount'];

				$this->db->where('item_id', $v['item_id']);
				$this->db->where('cart_id', $v['cart_id']);

				if (!$this->db->update(TBL_CART_ITEMS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy cart
	 *
	 * Reset and destroy and cart contents from the database
	 *
	 * @return bool
	 */
	public function destroy()
	{
		$tables = array(TBL_CART_ITEMS, TBL_CART_TOTALS, TBL_CART);

		foreach ($tables as $v)
		{
			if (!$this->db->where('cart_id', sess('cart_id'))->delete($v))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$vars = array('cart_id', 'cart_details', 'checkout_shipping_options',
		              'checkout_customer_data', 'checkout_shipping_selected', 'checkout_payment_option',
		              'checkout_shipping_option', 'cart_charge_shipping', 'checkout_coupon_code',
		);

		foreach ($vars as $v)
		{
			$this->session->unset_userdata($v);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function delete_cart_total($id = '')
	{
		if (!$this->db->where('id', $id)->delete(TBL_CART_TOTALS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete item
	 *
	 * Deletes an item from the shopping cart table
	 *
	 * @param string $id
	 *
	 * @return bool|string
	 */
	public function delete_item($id = '')
	{
		if (sess('cart_id'))
		{
			$this->db->where('cart_id', sess('cart_id'));
			$this->db->where('item_id', $id);

			if (!$this->db->delete(TBL_CART_ITEMS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array('success'  => TRUE,
		             'msg_text' => lang('item_deleted_successfully'),
		);

		//now get all the products in the cart and overwrite the $this->session->cart_details
		$this->update_session_cart();

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cart
	 *
	 * Get the users shopping cart with option for all items
	 * in the cart
	 *
	 * @param bool|FALSE $get_items
	 *
	 * @return bool|string
	 */
	public function get_cart($get_items = TRUE)
	{
		if (!empty($_SESSION['cart_id']))
		{
			$this->db->where('cart_id', sess('cart_id'));

			if (!$q = $this->db->get(TBL_CART))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				$this->cart_id = $row['cart_id'];

				if ($get_items == TRUE)
				{
					$row['sub_items'] = $this->get_cart_sub_totals();

					$row['items'] = $this->get_cart_items();

					if (!empty($row['items']))
					{
						$row = cart_totals($row);
					}
				}
			}
			else
			{
				//invalid cart_id so let's delete it
				$this->session->unset_userdata('cart_id');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function get_cart_sub_totals()
	{
		if (!$q = $this->db->where('cart_id', $this->cart_id)->get(TBL_CART_TOTALS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cart upload
	 *
	 * Get the file uploaded to the cart from cart_upload table
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function get_cart_upload($key = '')
	{
		if (!$q = $this->db->where('key', $key)->get(TBL_CART_UPLOADS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate cart
	 *
	 * Validate the current cart's quantity for updates
	 *
	 * @param array $data
	 *
	 * @return bool|string
	 */
	public function validate_cart($data = array()) //quantity
	{
		$error = array();

		if (!empty($data))
		{
			$p = $this->get_cart_items();

			foreach ($p as $v)
			{
				if ($data[$v['item_id']] > 0)
				{
					//check min quantity
					$msg = validate_item('min_quantity', $v, array('quantity' => $data[$v['item_id']]));

					if (!empty($msg))
					{
						array_push($error, $v['product_name'] . ' ' . $msg);
					}

					//check max quantity
					if ($msg = check_max_quantity($v, $data[$v['item_id']]))
					{
						array_push($error, $v['product_name'] . ' - ' . $msg);
					}

					//check main product inventory
					if ($msg = check_inventory($v, $data[$v['item_id']]))
					{
						array_push($error, $v['product_name'] . ' - ' . $msg);
					}

					//check for select options only
					if (!empty($v['attribute_data']))
					{
						//let's get the attributes from the db
						$a = unserialize(($v['attribute_data']));

						//check attributes inventory
						$select = array('select', 'radio', 'image');
						foreach ($a as $b)
						{
							//we're only gonna check attributes that have options
							if (in_array($b['attribute_type'], $select))
							{
								if ($option = check_attribute_inventory($b, $data[$v['item_id']]))
								{
									if (!empty($option['msg']))
									{
										array_push($error, $b['attribute_name'] . ' - ' . $option['msg']);
									};
								}
							}
						}
					}
				}
			}
		}

		//check if we have any errors
		if (count($error) > 0)
		{
			$row = array(
				'result'   => 'error',
				'msg_text' => format_errors($error),
			);
		}
		else
		{
			$row = array(
				'result'     => 'success',
				'cart_items' => $p,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update cart
	 *
	 * Update the quantity for the current user's cart
	 *
	 * @param array $data
	 *
	 * @return bool|string
	 */
	public function update_cart($post = array(), $data = array())
	{
		if (!empty($post))
		{
			foreach ($post as $k => $v)
			{
				if ($v > 0)
				{
					$vars = array('quantity' => $v);

					$this->db->where('cart_id', $this->cart_id);
					$this->db->where('item_id', $k);

					if (!$this->db->update(TBL_CART_ITEMS, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
				else
				{
					$this->delete_item($k);
				}
			}
		}

		$row = array('result'   => 'success',
		             'msg_text' => 'cart_updated_successfully',
		);

		//now get all the products in the cart and overwrite the $this->session->cart_details
		$this->update_session_cart();

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update quantity
	 *
	 * Update a specific item's quantity in the cart
	 *
	 * @param int $qty
	 *
	 * @return bool
	 */
	public function update_quantity($qty = 1)
	{
		$sql = 'UPDATE `' . $this->db->dbprefix(TBL_CART_ITEMS) . '`
                SET `quantity` = quantity + ' . $qty . '
                WHERE `cart_id` = \'' . $this->cart_id . '\'
                AND `key` = \'' . $this->item_key . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Update session cart
	 *
	 * Update the current user's session with all the cart's contents so we
	 * don't have to query it on every single page
	 *
	 * @return bool
	 */
	public function update_session_cart()
	{
		$cart = $this->get_cart();

		if (!empty($cart))
		{
			//check for discounts and update cart accordingly
			$this->check_discounts($cart);

			$sql = 'UPDATE ' . $this->db->dbprefix(TBL_CART) . '
                        SET  `date_modified` = NOW()
                        WHERE  `cart_id` = \'' . valid_id($cart['cart_id'], TRUE) . '\'';

			if (!$this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			//set the cart data to the session for easy access
			$this->session->set_userdata('cart_details', $cart);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate item
	 *
	 * Validate the item being added to the cart for
	 * quantity, inventory and availability
	 *
	 * @param array $data
	 * @param array $post
	 * @param bool|TRUE $add_cart
	 *
	 * @return bool|string
	 */
	public function validate($data = array(), $post = array())
	{
		//set default id and quantity
		if (empty($post))
		{
			$post = array(
				'product_id' => $data['product_id'],
				'quantity'   => 1,
			);
		}

		//if there's no cart id, let's create one!
		if (empty($this->cart_id))
		{
			$this->cart_id = $this->add_cart_id();
		}

		//calculate the md5 of the product including its attribute options
		$this->item_key = $this->get_item_key($post);

		//set the error msg array
		$error = array();

		//check required fields
		$vars = array(
			'date_expires', 'min_quantity',
		);

		foreach ($vars as $v)
		{
			$msg = validate_item($v, $data, $post);

			if (!empty($msg))
			{
				array_push($error, $msg);
			}
		}

		//check for only one subscription item per cart
		$msg = $this->check_single_subscription($data);

		if (!empty($msg))
		{
			array_push($error, $msg);
		}

		//get the product from the cart first
		$this->product_item = $this->check_product_item();

		//set the current quantity
		$this->current_qty = !empty($this->product_item['quantity']) ? $this->product_item['quantity'] + $post['quantity'] : $post['quantity'];

		//check max quantity
		if ($msg = check_max_quantity($data, $this->current_qty))
		{
			array_push($error, $msg);
		}

		//check inventory
		if ($msg = check_inventory($data, $this->current_qty))
		{
			array_push($error, $msg);
		}

		//check attributes
		if (!empty($data['attributes']))
		{
			$post['attribute_data'] = array();

			//check each product attribute if its required
			foreach ($data['attributes'] as $v)
			{
				$post['attribute_data'][$v['prod_att_id']] = array('attribute_type' => '',
				                                                   'value'          => '');

				$msg = '';
				//check for valid attribute data
				if (!empty($post['attribute_id'][$v['prod_att_id']]))
				{
					$option = validate_attribute($v, $post['attribute_id'][$v['prod_att_id']], $this->current_qty);

					$post['attribute_data'][$v['prod_att_id']] = $option;

					if (!empty($option['msg']))
					{
						$msg = $option['msg'];
					}
				}
				else
				{
					if ($v['required'] == 1)
					{
						//attribute is required
						$msg = $v['attribute_name'] . ' ' . lang('required');
					}
				}

				if (!empty($msg))
				{
					array_push($error, $msg);
				}

				//set the option data
				if (!empty($post['attribute_data']))
				{
					$a = empty($post['attribute_id'][$v['prod_att_id']]) ? '' : $post['attribute_id'][$v['prod_att_id']];
					$post['attribute_data'][$v['prod_att_id']]['attribute_type'] = $v['attribute_type'];
					$post['attribute_data'][$v['prod_att_id']]['value'] = empty($option['option_name']) ? $a : $option['option_name'];
				}
			}
		}

		//check if there are any errors
		if (count($error) > 0)
		{
			$row = array(
				'error'    => TRUE,
				'msg_text' => format_errors($error),
			);
		}
		else
		{
			$row = array(
				'success' => TRUE,
				'post'    => $post,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Add cart ID
	 *
	 * Add a new cart ID for the user's session
	 *
	 * @return mixed
	 */
	protected function add_cart_id()
	{
		$row = array(
			'member_id' => sess('member_id'),
			'cart_id'   => random_string('sha1'),
		);

		if (!$this->db->insert(TBL_CART, $row))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//set the session cart id
		$this->session->set_userdata('cart_id', $row['cart_id']);

		return $row['cart_id'];
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return mixed|string
	 */
	protected function check_single_subscription($data = array())
	{
		if ($data['product_type'] == 'subscription')
		{
			//now lets check all the products in the cart
			$this->db->where('cart_id', $this->cart_id);
			$this->db->join(TBL_PRODUCTS,
				$this->db->dbprefix(TBL_PRODUCTS) . '.product_id = ' .
				$this->db->dbprefix(TBL_CART_ITEMS) . '.product_id', 'left');
			if (!$q = $this->db->get(TBL_CART_ITEMS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				if ($this->check_subscription($row))
				{
					return lang('one_subscription_per_purchase');
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|mixed
	 */
	public function check_subscription($data = array())
	{
		foreach ($data as $v)
		{
			if ($v['product_type'] == 'subscription')
			{
				return $v;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check product item
	 *
	 * Check for the existenc of specific item in the cart by key and cart ID
	 *
	 * @return bool
	 */
	protected function check_product_item()
	{
		$this->db->where('key', $this->item_key);
		$this->db->where('cart_id', $this->cart_id);

		if (!$q = $this->db->get(TBL_CART_ITEMS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get item key
	 *
	 * Get unique key that identifies the item being added to the cart
	 * Use md5 to calculate key for product and its attributes
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	protected function get_item_key($data = array())
	{
		$vars = array('cart_id'    => $this->cart_id,
		              'product_id' => $data['product_id'],
		              'amount'     => is_var($data, 'amount'));

		//check if there are attributes
		if (!empty($data['attribute_id']) && count($data['attribute_id']) > 0)
		{
			$vars['attribute_id'] = $data['attribute_id'];
		}

		return md5(serialize($vars));
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cart items
	 *
	 * Get all the items in a single user's cart
	 *
	 * @param bool|FALSE $attributes
	 *
	 * @return bool|string
	 */
	protected function get_cart_items()
	{
		$sql = 'SELECT p.*, d.*, c.*, h.*, v.*, ';

		if (config_enabled('sts_tax_enable_tax_calculations'))
		{
			$sql .= ' GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes, ';
		}

		$sql .= ' p.product_id AS product_id
                FROM ' . $this->db->dbprefix(TBL_CART_ITEMS) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                    ON p.product_id = d.product_id
                    AND d.language_id = \'' . sess('default_lang_id') . '\'
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . '  c
                    ON p.product_id = c.product_id
                LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPLIERS) . '  v
                    ON c.supplier_id = v.supplier_id
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                    ON p.product_id = h.product_id
                    AND h.product_default = \'1\'
                LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                    ON c.tax_class_id = t.tax_class_id
                LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATE_RULES) . ' u
                    ON t.tax_class_id = u.tax_class_id
                LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATES) . ' w
                    ON w.tax_rate_id = u.tax_rate_id
                WHERE cart_id = \'' . $this->cart_id . '\'
                    GROUP BY p.item_id';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = init_cart_items($q->result_array());
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Insert row
	 *
	 * Insert a row in the cart items table
	 *
	 * @param array $post
	 *
	 * @return mixed
	 */
	protected function insert_row($data = array(), $post = array())
	{
		//check if this is a subscription based item or just one off (general)
		switch ($data['product_type'])
		{
			case 'subscription':

				//amount is the prod_price_id
				if (!empty($data['pricing_options']))
				{
					foreach ($data['pricing_options'] as $v)
					{
						if ($v['prod_price_id'] == $post['amount'])
						{
							$amount = !empty($v['enable_initial_amount']) ? $v['initial_amount'] : $v['amount'];

							$post['unit_price'] = check_attribute_price($post, $amount);
							$post['pricing_data'] = serialize($v);
						}
					}
				}

				break;

			default:

				//set the correct price and tax amount
				$post['unit_price'] = sale_price($data, $post);

				break;
		}

		$post['unit_tax'] = empty($data['taxes']) ? 0 : $data['taxes'];

		$data = $this->dbv->filter_columns($post, TBL_CART_ITEMS);

		if (!empty($data['attribute_data']))
		{
			$data['attribute_data'] = serialize($data['attribute_data']);
		}
		else
		{
			$data['attribute_data'] = '';
		}

		$data['cart_id'] = $this->cart_id;
		$data['key'] = $this->item_key;

		if (!$this->db->insert(TBL_CART_ITEMS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $this->db->insert_id();
	}

	// ------------------------------------------------------------------------

	/**
	 *
	 */
	protected function cart_gc()
	{
		srand(time());
		if ((rand() % 100) < $this->gc_probability)
		{
			$sql = 'DELETE FROM ' . $this->db->dbprefix(TBL_CART) . '
						WHERE date_modified < DATE_SUB(NOW(), INTERVAL ' . $this->gc_interval . ' DAY)';

			if (!$this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			log_message('debug', 'cart garbage collection performed.');
		}
	}
}

/* End of file Cart_model.php */
/* Location: ./application/models/Cart_model.php */