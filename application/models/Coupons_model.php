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
class Coupons_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'coupon_id';

	// ------------------------------------------------------------------------

	/**
	 * Coupons_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('coupons');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function apply_coupon($data = array())
	{
		//check if there is a coupon for the cart already.  if so, then override
		if (!$q = $this->db->where('cart_id', $data['totals']['cart_id'])->get(TBL_CART_TOTALS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//delete the old one and replace
		if ($q->num_rows() > 0)
		{
			$this->db->where('cart_id', $data['totals']['cart_id']);
			$this->db->where('type', 'coupon');

			if (!$q = $this->db->delete(TBL_CART_TOTALS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//add the new one
		if (!$this->db->insert(TBL_CART_TOTALS, $data['totals']))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array('success'  => TRUE,
		             'msg_text' => 'coupon_applied_successfully',
		             'coupon'   => array_merge($data['coupon_data'], $data['totals']),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool
	 */
	public function check_code($str = '')
	{
		$str = empty($str) ? $this->input->post($this->id) : $str;

		if (!empty($str))
		{
			$this->db->where('coupon_code', $str);

			if ($this->input->post($this->id))
			{
				$this->db->where($this->id . ' !=', (int)$this->input->post($this->id));
			}

			if (!$q = $this->db->get(TBL_COUPONS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function check_coupon_minimums()
	{
		//check for coupon requirements
		if (sess('cart_details', 'sub_items'))
		{
			$cart = sess('cart_details');

			foreach ($cart['sub_items'] as $v)
			{
				if ($v['type'] == 'coupon' && !empty($v['sub_data']))
				{
					$sub = unserialize($v['sub_data']);

					if (!empty($sub['minimum_order']))
					{
						if ($sub['minimum_order'] > $cart['totals']['total'])
						{
							//remove the coupon code
							$this->cart->delete_cart_total($v['id']);
						}
					}
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_COUPONS);

		if (!$this->db->insert(TBL_COUPONS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data['coupon_id'] = $this->db->insert_id();

		//insert restricted_products if any
		$this->update_coupon_products($data);

		$row = array(
			'id'       => $data['coupon_id'],
			'data'     => $data,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $limit
	 * @param bool $expire
	 * @return bool|false|string
	 */
	public function get_user_coupons($id = '', $limit = MEMBER_RECORD_LIMIT, $expire = TRUE)
	{
		$sort = $this->config->item(TBL_COUPONS, 'db_sort_order');

		//set the cache file
		$cache = __METHOD__ . $id . $limit . $expire;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT *
					FROM ' . $this->db->dbprefix(TBL_COUPONS) . '
					    WHERE member_id = \'' . (int)$id . '\'
					    AND status = \'1\'';

			if ($expire == TRUE)
			{
				$sql .= ' AND ( start_date < CURDATE()
                            AND end_date > CURDATE() )';
			}

			$sql .= ' ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
					    LIMIT ' . $limit;

			//run the query
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'rows'           => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $col
	 * @param bool $public
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_details($id = '', $col = 'coupon_id', $public = FALSE, $lang_id = 1)
	{
		$sql = 'SELECT p.*, b.username,';

		if ($public == FALSE)
		{
			$sql .= '(SELECT ' . $this->id . '
						FROM ' . $this->db->dbprefix(TBL_COUPONS) . '
						WHERE ' . $this->id . ' < ' . (int)$id . '
						ORDER BY `' . $this->id . '`
						DESC LIMIT 1)
					AS prev,
					(SELECT ' . $this->id . '
						FROM ' . $this->db->dbprefix(TBL_COUPONS) . '
						WHERE ' . $this->id . ' > ' . (int)$id . '
						ORDER BY `' . $this->id . '`
						ASC LIMIT 1) 
					AS next, ';
		}

		$sql .= ' DATE_FORMAT(start_date, \'' . $this->config->item('sql_date_format') . '\')
                        AS start_date,
                    DATE_FORMAT(end_date, \'' . $this->config->item('sql_date_format') . '\')
                        AS end_date
                FROM ' . $this->db->dbprefix(TBL_COUPONS) . ' p
                 LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' b
                                ON p.member_id = b.member_id
                    WHERE ' . $col . ' = \'' . xss_clean($id) . '\'';


		if ($public == TRUE)
		{
			$sql .= ' AND start_date < NOW() AND end_date > NOW()';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
			$row['select_products'] = $this->get_coupon_products($row['coupon_id'], '', $lang_id);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function generate_serial()
	{
		$length = 14;
		/*
		do
		{
			$code = 
			//$code = generate_serial(SERIAL_CODE_UPPERCASE, DEFAULT_COUPON_CODE_LENGTH, 1, SERIAL_CODE_STRING_TYPE);
		} while ($p = $this->get_details($code, 'coupon_code', FALSE));
		*/
		
		$code = '';
	    for($i = 0; $i < $length; $i++) {
	        $code .= mt_rand(0, 9);
	    }

		return $code;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $cart_id
	 * @return array
	 */
	public function remove_coupon($id = '', $cart_id = '')
	{

		$this->db->where('cart_id', $cart_id);
		if (!$this->db->where('id', $id)->delete(TBL_CART_TOTALS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array('success'  => TRUE,
		             'msg_text' => lang('coupon_removed_successfully'),
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function update_coupon_use($data = array())
	{
		$sql = 'UPDATE ' . $this->db->dbprefix(TBL_COUPONS) . '
                    SET coupon_uses = coupon_uses + \'1\'
                    WHERE coupon_id = \'' . (int)$data['coupon_id'] . '\'';

		if (!$this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_COUPONS);

		if (!$this->db->where($this->id, $vars[$this->id])->update(TBL_COUPONS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$this->update_coupon_products($data);

		$row = array(
			'id'       => $vars[$this->id],
			'data'     => $vars,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_coupon_products($data = array())
	{
		if (!empty($data['select_products']))
		{
			foreach ($data['select_products'] as $v)
			{
				if (!$this->get_coupon_products($data['coupon_id'], $v))
				{
					$vars = array('coupon_id'  => $data['coupon_id'],
					              'product_id' => $v);

					$this->dbv->create(TBL_COUPONS_PRODUCTS, $vars);
				}
			}

			//delete the rest
			$a = $this->get_coupon_products($data['coupon_id']);

			if (!empty($a))
			{
				foreach ($a as $v)
				{
					if (!in_array($v['product_id'], $data['select_products']))
					{
						$this->dbv->delete(TBL_COUPONS_PRODUCTS, 'id', $v['id']);
					}
				}
			}
		}
		else
		{
			$this->dbv->delete(TBL_COUPONS_PRODUCTS, 'coupon_id', $data['coupon_id']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$required = $this->config->item('coupon_codes', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_COUPONS);

		foreach ($fields as $f)
		{
			switch ($f->name)
			{
				case 'coupon_code':

					if (CONTROLLER_FUNCTION == 'create')
					{
						$this->form_validation->set_rules(
							'coupon_code', 'lang:coupon_code',
							'trim|required|is_unique[' . TBL_COUPONS . '.coupon_code]',
							array(
								'is_unique' => '%s ' . lang('already_in_use'),
							)
						);
					}
					else
					{
						$this->form_validation->set_rules(
							'coupon_code', 'lang:coupon_code',
							array(
								'trim', 'required', 'url_title',
								array('check_code', array($this->coupon, 'check_code')),
							)
						);

						$this->form_validation->set_message('check_code', '%s ' . lang('already_in_use'));
					}

					break;

				case 'start_date':

					$this->form_validation->set_rules(
						'start_date', 'lang:start_date',
						array(
							'trim', 'required', 'start_date_to_sql',
							array('check_start_date', array($this->dbv, 'check_start_date')),
						)
					);

					$this->form_validation->set_message('check_start_date', '%s ' . lang('must_be_earlier_than_end_date'));

					break;

				case 'end_date':

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, 'trim|required|end_date_to_sql');

					break;

				default:
					//set the default rule
					$rule = 'trim|xss_clean';

					//if this field is a required field, let's set that
					if (is_array($required) && in_array($f->name, $required))
					{
						$rule .= '|required';
					}

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

					break;
			}
		}

		//check for restrict_products
		if (!empty($data['restrict_products']))
		{
			if (empty($data['select_products']))
			{
				$this->form_validation->set_rules('select_products', 'lang:product_restrictions', 'trim|required');
			}
		}

		if (!$this->form_validation->run())
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
			);
		}
		else
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function validate_admin_coupon($id = '', $data = array())
	{
		//set the default error message
		$row = array('error'    => TRUE,
		             'msg_text' => lang('invalid_coupon'));

		//check for valid coupon first
		if ($p = $this->get_details(trim($id), 'coupon_code', TRUE))
		{
			//check if we've used this too much
			if (!empty($p['uses_per_coupon']))
			{
				if ($p['coupon_uses'] >= $p['uses_per_coupon'])
				{
					return array('error'    => TRUE,
					             'msg_text' => lang('maximum_usage_reached_for_coupon'));
				}
			}

			//check if the minimum order has been set
			if ($p['minimum_order'] > 0)
			{
				if ($p['minimum_order'] > $data['totals']['total'])
				{
					return array('error'    => TRUE,
					             'msg_text' => format_amount($p['minimum_order']) . ' ' . lang('minimum_cart_amount_required'));
				}
			}

			//set the coupon array
			$coupon = format_admin_coupon_data($p);

			//check for specific products in the coupon
			if ($p['restrict_products'] == 1)
			{
				if ($prod = $this->get_coupon_products($p['coupon_id']))
				{
					foreach ($prod as $v)
					{
						//loop through the items and apply the coupon
						array_push($coupon['required_products'], $v['product_id']);
					}
				}

				//check if there any products to add
				if (count($coupon['required_products']) == 0)
				{
					return array('error'    => TRUE,
					             'msg_text' => lang('no_products_in_cart_to_apply_coupon'));
				}
			}

			$row = array('success'     => TRUE,
			             'coupon_data' => array_merge($p, $coupon),
			             'totals'      => $coupon,
			);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function validate_coupon($id = '', $data = array())
	{
		$row = array('error'    => TRUE,
		             'msg_text' => lang('invalid_coupon'));

		//first check if the coupon is already in the cart
		if ($this->check_cart_coupon($id, $data['cart_id']))
		{
			return array('error'    => TRUE,
			             'msg_text' => lang('coupon_already_added'));
		}

		//check for valid coupon first
		if ($p = $this->get_details(trim($id), 'coupon_code', TRUE))
		{
			//check if we've used this too much
			if (!empty($p['uses_per_coupon']))
			{
				if ($p['coupon_uses'] >= $p['uses_per_coupon'])
				{
					return array('error'    => TRUE,
					             'msg_text' => lang('maximum_usage_reached_for_coupon'));
				}
			}

			//check if the minimum order has been set
			if ($p['minimum_order'] > 0)
			{
				if ($p['minimum_order'] > $data['totals']['total'])
				{
					return array('error'    => TRUE,
					             'msg_text' => format_amount($p['minimum_order']) . ' ' . lang('minimum_cart_amount_required'));
				}
			}

			//set the coupon array
			$coupon = format_coupon_data($p, $data);

			//check for specific products in the coupon
			if ($p['restrict_products'] == 1)
			{
				if ($prod = $this->get_coupon_products($p['coupon_id']))
				{
					foreach ($prod as $v)
					{
						//loop through the items and apply the coupon
						array_push($coupon['sub_data']['required_products'], $v['product_id']);
					}
				}

				//check if there any products to add
				if (count($coupon['sub_data']['required_products']) == 0)
				{
					return array('error'    => TRUE,
					             'msg_text' => lang('no_products_in_cart_to_apply_coupon'));
				}
			}

			//save the sub data....
			$coupon['sub_data'] = serialize($coupon['sub_data']);

			$row = array('success'     => TRUE,
			             'coupon_data' => $p,
			             'totals'      => $coupon,
			);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $product_id
	 * @param int $lang_id
	 * @return bool
	 */
	protected function get_coupon_products($id = '', $product_id = '', $lang_id = 1)
	{

		$sql = 'SELECT
                  p.*,
                  d.product_name
                FROM ' . $this->db->dbprefix(TBL_COUPONS_PRODUCTS) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                    ON p.product_id = d.product_id
                    AND d.language_id = \'' . $lang_id . '\'
                WHERE p.`' . $this->id . '` = \'' . valid_id($id) . '\'';


		if (!empty($product_id))
		{
			$sql .= ' AND p.product_id = \'' . valid_id($product_id) . '\'';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $cart_id
	 * @return bool
	 */
	protected function check_cart_coupon($id = '', $cart_id = '')
	{
		$this->db->where('cart_id', $cart_id);

		if (!$q = $this->db->where('text', $id)->get(TBL_CART_TOTALS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? TRUE : FALSE;
	}
}

/* End of file Coupons_model.php */
/* Location: ./application/models/Coupons_model.php */