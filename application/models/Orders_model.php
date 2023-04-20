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
class Orders_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'order_id';

	// ------------------------------------------------------------------------

	/**
	 * Orders_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('orders');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $field
	 * @param string $term
	 * @param int $limit
	 * @return array
	 */
	public function ajax_search($field = 'order_number', $term = '', $limit = TPL_AJAX_LIMIT)
	{
		//set the default array when nothing is set
		$select[] = array('order_id'     => '0',
		                  'order_number' => 'none');

		//check what fields to search
		$this->db->like($field, $term);

		$this->db->select($this->id . ', ' . $field);

		$this->db->limit($limit);

		if (!$q = $this->db->get(TBL_ORDERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$e = $q->result_array();

			$rows = array_merge($select, $e);
		}
		else
		{
			$rows = $select;
		}

		return $rows;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $notes
	 * @return bool|false|string
	 */
	public function create_admin_order($data = array(), $notes = '')
	{
		//insert the order data first
		$data['order_notes'] = $notes;

		$data['order'] = $this->insert_order($data, 'admin');

		//add order items
		$data['order']['items'] = $this->insert_order_items($data['order']['order_id'], $data['items'], sess('default_lang_id'));

		//add order shipping
		if (!empty($data['shipping']))
		{
			$data['order']['shipping'] = $this->insert_order_shipping($data['order']['order_id'], $data['shipping']);
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function create_order($data = array())
	{
		//set the order id
		$order = $this->insert_order($data);

		//add order items
		$order['items'] = $this->insert_order_items($order['order_id'], $data['cart']['items'], $data['language']);

		//add order shipping
		if (!empty($data['shipping']))
		{
			$order['shipping'] = $this->insert_order_shipping($order['order_id'], $data['shipping']);
		}

		return $order;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param int $lang_id
	 * @return array
	 */
	public function create_subscription_order($data = array(), $lang_id = 1)
	{
		//set the order id
		$order = $this->insert_order($data, 'cron');

		if (!empty($order))
		{
			//add order items
			$item = format_order_item($order['order_id'], $data, $lang_id, 'cron');
			$order['items'] = $this->insert_item($item);

			$order['taxes'] = $data['tax_amount'];

			//add order shipping
			if (!empty($data['shipping_data']))
			{
				$ship_data = unserialize($data['shipping_data']);
				$ship_data['shipping_total'] = $data['shipping_amount'];
				$order['shipping'] = $this->insert_order_shipping($order['order_id'],  $ship_data);
			}
		}


		return array(
			'success'  => TRUE,
			'order'    => $order,
			'msg_text' => lang('order') . ' ' . $order['order_id'] . ' ' . lang('created_successfully'),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @param bool $product_data
	 * @param bool $get_cache
	 * @return bool|false|string
	 */
	public function get_details($id = '', $public = FALSE, $product_data = FALSE, $get_cache = FALSE)
	{
		//let's get the invoice details first
		$sql = 'SELECT p.*';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT p.' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT p.' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ',  d.*,
                    a.invoice_id,
                    a.invoice_number,
                    a.payment_status_id,
                    z.payment_status AS invoice_payment_status,
                    z.color AS invoice_payment_color,
                    e.order_status, e.color,
                    m.username AS affiliate_username,
                    b.region_name AS order_state_name,
                    b.region_code AS order_state_code,
                    s.country_name AS order_country_name,
                    s.country_iso_code_2 AS order_country_code_2,
                    s.country_iso_code_3 AS order_country_code_3,
                    r.region_name AS shipping_state_name,
                    r.region_code AS shipping_state_code,
                    c.country_name AS shipping_country_name,
                    c.country_iso_code_2 AS shipping_country_iso_code_2,
                    c.country_iso_code_3 AS shipping_country_iso_code_3,
                    p.order_id AS order_id
                    FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS_STATUS) . ' e
                        ON p.order_status_id = e.order_status_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS_SHIPPING) . ' d
                        ON p.order_id = d.order_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' a
                        ON p.order_id = a.order_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PAYMENT_STATUS) . ' z
					        ON z.payment_status_id = a.payment_status_id    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' b
                        ON p.order_state = b.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' s
                        ON p.order_country = s.country_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' r
                        ON p.shipping_state = r.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        ON p.affiliate_id = m.member_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' c
                        ON p.shipping_country = c.country_id
                        WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'';

		//check if we have a cache file and return that instead
		$cache = __METHOD__ . md5($sql);
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';

		if ($row = $this->init->cache($cache, $cache_type))
		{
			if ($get_cache == TRUE)
			{
				return sc($row);
			}
		}

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			//now get the products for each invoice
			$row['items'] = $this->get_order_items($id, $product_data);

			if (!empty($row['shipping_data']))
			{
				$row['shipping'] = unserialize($row['shipping_data']);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}


		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @return bool
	 */
	public function get_status_details($id)
	{
		if (!$q = $this->db->where('order_status_id', $id)->get(TBL_ORDERS_STATUS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $limit
	 * @return bool|false|string
	 */
	public function get_user_orders($id = '', $limit = MEMBER_RECORD_LIMIT)
	{
		$sort = $this->config->item(TBL_ORDERS, 'db_sort_order');

		//set the cache file
		$cache = __METHOD__ . $id . $limit;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT p.*,
                          a.invoice_number,
                          c.order_status,
                          c.color
					FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS_STATUS) . ' c
					    ON p.order_status_id = c.order_status_id
					LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' a
					    ON p.order_id = a.order_id
					WHERE p.member_id = \'' . (int)$id . '\'
					    ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
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
	 * @param bool $product_data
	 * @return bool
	 */
	public function get_order_items($id = '', $product_data = FALSE)
	{
		$sort = $this->config->item(TBL_ORDERS_ITEMS, 'db_sort_order');

		$sql = 'SELECT * 
				 FROM ' . $this->db->dbprefix(TBL_ORDERS_ITEMS) . ' p ';

		if ($product_data == TRUE)
		{
			$sql .= 'LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' c 
                            ON p.product_id = c.product_id';
		}

		$sql .= ' WHERE p.' . $this->id . '= \'' . $id . '\'
				ORDER BY ' . $sort['column'] . ' ' . $sort['order'];

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_order_totals($id = '')
	{
		//get the existing order's details and totals for shipping...
		$vars = $this->get_details($id);

		foreach ($vars['items'] as $k => $v)
		{
			$vars['items'][$k] = array_merge($v, $this->prod->get_details($v['product_id']));
		}

		//generate totals
		$totals = get_order_totals($vars);

		$vars['items'] = $totals['items'];
		$vars['totals'] = $totals['totals'];

		return $vars;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return array|false
	 */
	public function get_statuses($form = FALSE)
	{
		if (!$q = $this->db->get(TBL_ORDERS_STATUS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $form == TRUE ? format_array($q->result_array(), 'order_status_id', 'order_status') : $q->result_array();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_ORDERS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT p.*, d.*, p.order_id AS order_id, a.invoice_id,
					 z.payment_status AS invoice_payment_status,
                     z.color AS invoice_payment_color'; //for the page rows only

		$count = 'SELECT COUNT(p.order_id) AS total
					 FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p '; //for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p
		            LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' a
                        ON p.order_id = a.order_id
		            LEFT JOIN ' . $this->db->dbprefix(TBL_PAYMENT_STATUS) . ' z
					    ON z.payment_status_id = a.payment_status_id    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS_STATUS) . ' d
                        ON p.order_status_id = d.order_status_id';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_ORDERS, TBL_ORDERS_STATUS), $options['query']);

			$sql .= $options['where_string'];
			$count .= $options['where_string'];
		}

		//set the order and limit clause
		$order = ' GROUP BY p.order_id
                    ORDER BY p.' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . $options['md5'];
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'         => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
					'total'          => $this->dbv->get_query_total($count),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return mixed
	 */
	public function insert_order($data = array(), $type = 'checkout')
	{
		$order = $this->dbv->clean(format_order_data($data, $type), TBL_ORDERS);

		if (!$this->db->insert(TBL_ORDERS, $order))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$order['order_id'] = $this->db->insert_id();

		//set the order id
		return $order;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return false|string
	 */
	public function mass_update($data = array(), $type = '')
	{
		if (!empty($data))
		{
			foreach ($data as $v)
			{
				$this->update_status($v, $type);
			}
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @return bool
	 */
	public function mark_paid($id)
	{
		if (!$this->db->where('order_id', $id)->update(TBL_ORDERS, array('payment_status' => '1')))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @return array
	 */
	public function mark_done($id)
	{
		if (!$this->db->where('order_id', $id)->update(TBL_ORDERS, array('parent_order'    => $id,
		                                                                 'order_status_id' => config_item('sts_order_default_order_status_paid'))))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array(
			'success'  => TRUE,
			'order'    => $id,
			'msg_text' => lang('system_updated_successfully'),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $order_id
	 * @return bool
	 */
	public function remove_item($id = '', $order_id = '')
	{
		if (!$this->db->where('order_item_id', $id)->delete(TBL_ORDERS_ITEMS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function search($options = '')
	{
		$sort = $this->config->item(TBL_ORDERS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT *, p.order_id AS order_id '; //for the page rows only

		$count = 'SELECT COUNT(p.order_id) AS total
					 FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p '; //for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS_STATUS) . ' d
                        ON p.order_status_id = d.order_status_id';

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_ORDERS);

				$i = 1;
				foreach ($columns as $f)
				{
					if ($i == 1)
					{
						$sql .= ' WHERE p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= ' WHERE p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}
					else
					{
						$sql .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}

					$i++;
				}
			}
		}

		//set the order and limit clause
		$order = ' GROUP BY p.order_id
                    ORDER BY p.' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . $options['md5'];
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'         => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
					'total'          => $this->dbv->get_query_total($count),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function update_tracking($data = array())
	{
		$this->db->where('order_id', (int)$data['order_id']);

		if (!$this->db->update(TBL_ORDERS, array('shipping_data' => serialize($data))))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if (!$q = $this->db->where('order_id', (int)$data['order_id'])->get(TBL_ORDERS_SHIPPING))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $this->dbv->update(TBL_ORDERS_SHIPPING, 'order_id', $this->input->post(NULL, TRUE));
		}
		else
		{
			//insert new record
			$row = $this->dbv->create(TBL_ORDERS_SHIPPING, $this->input->post(NULL, TRUE));
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update_order_shipping($id = '', $data = array())
	{
		$this->db->where('order_id', (int)$id);

		if (!$this->db->update(TBL_ORDERS, array('shipping_data' => serialize($data))))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars = format_order_shipping($id, $data);

		$this->db->where('order_id', (int)$id);

		if (!$this->db->update(TBL_ORDERS_SHIPPING, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $id,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => array('shipping_carrier' => $vars['carrier'],
			                    'shipping_service' => $vars['service'],
			                    'shipping_rate'    => format_amount($vars['rate'], FALSE)),
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $status
	 * @param string $column
	 * @return false|string
	 */
	public function update_status($id = '', $status = '0', $column = 'order_status_id')
	{
		if (!$this->db->where($this->id, $id)->update(TBL_ORDERS, array($column => $status)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $id,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $post
	 * @param int $lang_id
	 * @return bool
	 */
	public function update_current_order_contents($data = array(), $post = array(), $lang_id = 1)
	{
		$data = format_order_item($post['order_id'], $data, $lang_id);

		//initialize the array for comparison
		$a = serialize(array(
			'product_sku'     => $data['product_sku'],
			'product_id'      => $data['product_id'],
			'attribute_data'  => $data['attribute_data'],
			'pricing_data'    => $data['pricing_data'],
			'order_item_name' => $data['order_item_name'],
			'unit_price'      => format_amount($data['unit_price'], FALSE),
		));

		$row = $this->get_order_items($post['order_id']);

		//insert item by default
		$new_item = TRUE;

		if (!empty($row))
		{
			foreach ($row as $v)
			{
				$b = serialize(array(
					'product_sku'     => $v['product_sku'],
					'product_id'      => $v['product_id'],
					'attribute_data'  => $v['attribute_data'],
					'pricing_data'    => $data['pricing_data'],
					'order_item_name' => $v['order_item_name'],
					'unit_price'      => format_amount($v['unit_price'], FALSE),
				));

				//compare if the product is already in the table, then just update the quantity
				if (md5($a) == md5($b))
				{
					//update quantity
					$qty = $data['quantity'] + $v['quantity'];

					$this->update_item_quantity($v['order_item_id'], $qty);

					$new_item = FALSE;
				}
			}
		}

		if ($new_item == TRUE)
		{
			$this->insert_item($data);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $qty
	 * @param string $order_id
	 * @return bool
	 */
	public function update_item_quantity($id = '', $qty = '', $order_id = '')
	{
		if (!$this->db->where('order_item_id', $id)->update(TBL_ORDERS_ITEMS, array('quantity' => $qty)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @param string $type
	 * @return bool|false|string
	 */
	public function update_order($id = '', $data = array(), $type = 'order')
	{
		$data = $this->dbv->clean($data, TBL_ORDERS);

		if (!$this->db->where('order_id', $id)->update(TBL_ORDERS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$r = array();

		//get region data as well...
		if (!empty($data[$type . '_state']))
		{
			$b = get_region_name($data[$type . '_state']);
		}
		if (!empty($data[$type . '_country']))
		{
			$c = get_country_name($data[$type . '_country']);
			$r = array_merge($b, $c);
		}

		//get order status
		if (!empty($data['order_status_id']))
		{
			$s = $this->get_status_details($data['order_status_id']);
			$r = array_merge($r, $s);
		}


		$row = array(
			'success'  => TRUE,
			'data'     => array_merge($data, $r),
			'msg_text' => lang('order_updated_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $types
	 * @return array
	 */
	public function update_card_types($data = array(), $types = array())
	{
		$a = array();

		//check if the list is already in the table
		if (!empty($data['cc_types']))
		{
			foreach ($data['cc_types'] as $v)
			{
				if (!empty($v['cc_type_id']))
				{
					$row = $this->dbv->update(TBL_CC_TYPES, 'cc_type_id', $v);

					array_push($a, $v['cc_type_id']);
				}
				else
				{
					$row = $this->dbv->create(TBL_CC_TYPES, $v);
				}
			}
		}

		//let's delete all the types not in the current one
		if (!empty($types))
		{
			foreach ($types as $v)
			{
				if (!empty($v['cc_type_id']))
				{
					if (!in_array($v['cc_type_id'], $a))
					{
						$this->dbv->delete(TBL_CC_TYPES, 'cc_type_id', $v['cc_type_id']);
					}
				}
			}
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $statuses
	 * @return array
	 */
	public function update_statuses($data = array(), $statuses = array())
	{
		$table = $data['type'] == 'payment' ? TBL_PAYMENT_STATUS : TBL_ORDERS_STATUS;
		$a = array();

		//check if the list is already in the table
		if (!empty($data[$data['type'] . '_statuses']))
		{
			foreach ($data[$data['type'] . '_statuses'] as $v)
			{
				if (!empty($v[$data['type'] . '_status_id']))
				{
					$row = $this->dbv->update($table, $data['type'] . '_status_id', $v);

					array_push($a, $v[$data['type'] . '_status_id']);
				}
				else
				{
					$row = $this->dbv->create($table, $v);
				}
			}
		}

		//let's delete all the statuses not in the current one
		if (!empty($statuses))
		{
			foreach ($statuses as $v)
			{
				if (!empty($v[$data['type'] . '_status_id']))
				{
					if (!in_array($v[$data['type'] . '_status_id'], $a))
					{
						$this->dbv->delete($table, $data['type'] . '_status_id', $v[$data['type'] . '_status_id']);
					}
				}
			}
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param array $data
	 * @return array
	 */
	public function validate_address($type = 'shipping', $data = array())
	{
		$row = array('error'         => '',
		             'error_fields'  => array(),
		             'customer_data' => array());

		//get all the fields for validation
		$fields = $this->form->init_form(2, sess('default_lang_id'), '', TRUE);

		//check for all required fields first.
		$p = $this->form->validate_fields($type, $data, $fields['values']);

		//check for errors
		if (!empty($p['error']))
		{
			$row['error'] .= $p['msg'];
			$row['error_fields'] = array_merge($row['error_fields'], $p['error_fields']);
		}

		$row['customer_data'] = array_merge(sess('order_client_data'), $p['data']);

		$row['customer_data'] = order_member_data($type, '', $row['customer_data']);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate_shipping($data = array())
	{
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('member_id', 'lang:member_id', 'trim|integer');

		$vars = array('name'        => 'shipping_name',
		              'company'     => 'company',
		              'address_1'   => 'address_1',
		              'address_2'   => 'address_2',
		              'city'        => 'city',
		              'postal_code' => 'postal_code',
		);

		foreach ($vars as $k => $v)
		{
			$this->form_validation->set_rules('shipping_' . $k, 'lang:' . $v, 'trim|strip_tags|max_length[255]');
		}

		$required = !empty($data['charge_shipping']) ? 'trim|integer|is_natural_no_zero' : 'trim|integer';
		$this->form_validation->set_rules('shipping_state', 'lang:state_province', $required);
		$this->form_validation->set_rules('shipping_country', 'lang:country', $required);

		if ($this->form_validation->run())
		{
			return array('success' => TRUE,
			             'data'    => $this->dbv->validated($data));
		}

		return array('error'        => TRUE,
		             'msg'          => validation_errors(),
		             'error_fields' => generate_error_fields($data));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function validate_client($data = array(), $type = 'create')
	{
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('member_id', 'lang:member_id', 'trim|integer');
		if ($type == 'create')
		{
			$this->form_validation->set_rules('fname', 'lang:first_name', 'trim|strip_tags|required|max_length[255]');
			$this->form_validation->set_rules('lname', 'lang:last_name', 'trim|strip_tags|required|max_length[255]');
		}
		else
		{
			$this->form_validation->set_rules('order_name', 'lang:order_name', 'trim|strip_tags|required|max_length[255]');
		}
		$this->form_validation->set_rules('order_company', 'lang:company', 'trim|strip_tags|max_length[255]');
		$this->form_validation->set_rules('order_primary_email', 'lang:email_address', 'trim|required|valid_email');
		$this->form_validation->set_rules('order_telephone', 'lang:telephone', 'trim|strip_tags|max_length[255]');

		$this->form_validation->set_rules('affiliate_id', 'lang:referred_by', 'trim|integer');

		$this->form_validation->set_rules('order_address_1', 'lang:address_1', 'trim|strip_tags|max_length[255]');
		$this->form_validation->set_rules('order_address_2', 'lang:address_2', 'trim|strip_tags|max_length[255]');
		$this->form_validation->set_rules('order_city', 'lang:city', 'trim|strip_tags|max_length[255]');
		$this->form_validation->set_rules('order_state', 'lang:state_province', 'trim|integer');
		$this->form_validation->set_rules('order_country', 'lang:country', 'trim|integer');
		$this->form_validation->set_rules('order_postal_code', 'lang:postal_code', 'trim|strip_tags|max_length[255]');

		if ($this->form_validation->run())
		{
			$data = $this->dbv->validated($data);

			//get the member's details and merge it to the array
			if (!empty($data['member_id']))
			{
				$mem = $this->mem->get_details($data['member_id']);
				$data = array_merge($mem, $data);
			}

			return array('success' => TRUE,
			             'data'    => $this->dbv->validated($data));
		}

		return array('error'        => TRUE,
		             'msg'          => validation_errors(),
		             'error_fields' => generate_error_fields($data));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $post
	 * @return bool|false|string
	 */
	public function validate_product($data = array(), $post = array())
	{
		//reset product quantities for checksum
		$qty = $post['quantity'];
		unset($post['quantity']);

		$post = $this->dbv->clean($post);

		$error = array();

		//check min quantity
		if ($msg = check_min_quantity($data, $qty))
		{
			array_push($error, $data['product_name'] . ' ' . $msg);
		}

		//check max quantity
		if ($msg = check_max_quantity($data, $qty))
		{
			array_push($error, $data['product_name'] . ' - ' . $msg);
		}

		//check main product inventory
		if ($msg = check_inventory($data, $qty))
		{
			array_push($error, $data['product_name'] . ' - ' . $msg);
		}

		//check for select options only
		if (!empty($post['attribute_id']))
		{
			$post['attribute_data'] = array();

			if (!empty($data['attributes']))
			{
				//check attributes inventory
				foreach ($data['attributes'] as $v)
				{
					$msg = '';

					//check for valid attribute data
					if (!empty($post['attribute_id'][$v['prod_att_id']]))
					{
						$option = validate_attribute($v, $post['attribute_id'][$v['prod_att_id']], $qty);

						$post['attribute_data'][$v['prod_att_id']] = $option;
						$post['attribute_data'][$v['prod_att_id']]['value'] = $post['attribute_id'][$v['prod_att_id']];
						$post['attribute_data'][$v['prod_att_id']]['attribute_type'] = $v['attribute_type'];

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
				}
			}
		}

		//check if we have any errors
		if (count($error) > 0)
		{
			$row = array(
				'error' => TRUE,
				'msg'   => format_errors($error),
			);
		}
		else
		{
			//set product and price
			$post['product_name'] = $data['product_name'];

			if (!empty($post['pricing_data']))
			{
				$p = unserialize(base64_decode($post['pricing_data']));

				$post['product_price'] = !empty($p['enable_initial_amount']) ? $p['initial_amount'] : $p['amount'];
				$post['pricing_data'] = serialize($p);
			}
			else
			{
				$post['product_price'] = $data['product_sale_price'] > 0 ? $data['product_sale_price'] : $data['product_price'];

			}

			$post['checksum'] = md5(serialize($post));
			$post['quantity'] = $qty;

			$row = array(
				'success' => TRUE,
				'data'    => array_merge($data, $post),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	protected function insert_order_shipping($id = '', $data = array())
	{
		$shipping = format_order_shipping($id, $data);

		if (!$this->db->insert(TBL_ORDERS_SHIPPING, $shipping))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $shipping;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	protected function insert_item($data = array())
	{
		$v = $this->dbv->filter_columns($data, TBL_ORDERS_ITEMS);

		if (!$this->db->insert(TBL_ORDERS_ITEMS, $v))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'data'     => $v,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @param int $lang_id
	 * @return array
	 */
	protected function insert_order_items($id = '', $data = array(), $lang_id = 1)
	{
		$items = format_order_items($id, $data, $lang_id);

		foreach ($items as $v)
		{
			$this->insert_item($v);
		}

		return $items;
	}
}
/* End of file Orders_model.php */
/* Location: ./application/models/Orders_model.php */