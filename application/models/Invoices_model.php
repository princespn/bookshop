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
class Invoices_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'invoice_id';

	// ------------------------------------------------------------------------

	/**
	 * Invoices_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('invoices');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function add_refund($data = array(), $type = 'manual')
	{
		$vars = $this->dbv->clean($data, TBL_INVOICE_PAYMENTS);

		//set amount to negative
		$vars['amount'] = $data['refund_amount'] > 0 ? $data['refund_amount'] * -1 : $data['refund_amount'];
		$vars['fee'] *= -1;
		$vars['description'] = $vars['transaction_id'] . ' ' . lang('refund') . ' - ' . lang($type);
		unset($vars['invoice_payment_id']);

		if (!$this->db->insert(TBL_INVOICE_PAYMENTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars['invoice_payment_id'] = $this->db->insert_id();

		//check to mark the invoice as paid
		$vars['refunded'] = $this->mark_refunded($vars['invoice_id']);

		return array('id'       => $vars['invoice_payment_id'],
		             'success'  => TRUE,
		             'msg_text' => lang('payment_refunded_successfully'),
		             'data'     => $vars);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $field
	 * @param string $term
	 * @param int $limit
	 * @return array
	 */
	public function ajax_search($field = 'invoice_number', $term = '', $limit = TPL_AJAX_LIMIT)
	{
		//set the default array when nothing is set
		$select[] = array('invoice_id'     => '0',
		                  'invoice_number' => 'none');

		//check what fields to search
		$this->db->like($field, $term);

		$this->db->select($this->id . ', ' . $field);

		$this->db->limit($limit);

		if (!$q = $this->db->get(TBL_INVOICES))
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
	 * @return bool|false|string
	 */
	public function cancel_invoices()
	{
		$sql = 'UPDATE ' . $this->db->dbprefix(TBL_INVOICES) .
			' SET payment_status_id = \'4\'
			WHERE due_date < NOW() - INTERVAL  ' . config_item('sts_cron_cancel_unpaid_invoices_after_days') . ' DAY
			AND payment_status_id = \'1\'';

		if (!$this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$rows = $this->db->affected_rows();

		if (!empty($rows))
		{
			$row = array(
				'msg_text' => $rows . ' ' . lang('invoices_cancelled_successfully'),
				'success'  => TRUE,
			);
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $id
	 * @return mixed
	 */
	public function create_payment($data = array(), $id = '')
	{
		$sdata = !empty($id) ? format_payment_data($id, $data) : $data;

		$vars = $this->dbv->clean($sdata, TBL_INVOICE_PAYMENTS);

		if (!$this->db->insert(TBL_INVOICE_PAYMENTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars['invoice_payment_id'] = $this->db->insert_id();

		//check to mark the invoice as paid
		$vars['paid'] = $this->mark_paid($vars['amount'], $vars['invoice_id']);

		return $vars;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return bool|false|string
	 */
	public function create_invoice($data = array(), $type = 'checkout')
	{
		$vars = format_invoice_data($data, $type);

		if (!$this->db->insert(TBL_INVOICES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//set the invoice id
		$vars['invoice_id'] = $this->db->insert_id();
		$vars['payment_status'] = $vars['payment_status_id'] == '1' ? 'unpaid' : 'paid';

		//add invoice items
		$vars['items'] = $this->insert_invoice_items($vars['invoice_id'], $data, $type);

		//add invoice totals
		$vars['totals'] = $this->insert_invoice_totals($vars['invoice_id'], $data, $type);

		//increase next invoice number
		update_next_invoice_number($vars['invoice_number']);

		$row = array(
			'success'  => TRUE,
			'id'       => $vars['invoice_id'],
			'data'     => $vars,
			'msg_text' => lang('invoice_created_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $sub
	 * @return bool|false|string
	 */
	public function get_invoices_due($sub = FALSE)
	{
		$sql = 'SELECT p.*, d.sub_id, d.payment_type, m.*, t.amount AS sub_total,
					b.region_name AS customer_state_name,
                    b.region_code AS customer_state_code,
                    s.country_name AS customer_country_name,
                    s.country_iso_code_2 AS customer_country_code_2,
                    s.country_iso_code_3 AS customer_country_code_3,
                    r.region_name AS shipping_state_name,
                    r.region_code AS shipping_state_code,
                    c.country_name AS shipping_country_name,
                    c.country_iso_code_2 AS shipping_country_iso_code_2,
                    c.country_iso_code_3 AS shipping_country_iso_code_3,
						p.invoice_id AS invoice_id, d.order_id, d.subscription_id
                	FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' d
                        ON p.invoice_id = d.invoice_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' b
                        ON p.customer_state = b.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' s
                        ON p.customer_country = s.country_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' r
                        ON p.shipping_state = r.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' c
                        ON p.shipping_country = c.country_id    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICE_TOTALS) . ' t
                        ON p.invoice_id = t.invoice_id
                        AND type = \'sub_total\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MODULES) . ' m
                        ON m.module_folder = d.payment_type
                    WHERE p.due_date < NOW()
                        AND p.payment_status_id = \'1\'
                    ORDER BY p.due_date ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$rows = $q->result_array();

			if ($sub == TRUE)
			{
				foreach ($rows as $k => $v)
				{
					if (empty($v['payment_type']) || empty($v['module_status']))
					{
						unset($rows[$k]);
					}
					else
					{
						//now get the products for each invoice
						$rows[$k]['items'] = $this->get_invoice_items($v['invoice_id'], FALSE);

						$rows[$k]['module'] = array('module_status' => $v['module_status'],
						                            'module_id' => $v['module_id'],
						                            'module_type' => $v['module_type'],
						                            'module_name' => $v['module_name'],
						                            'module_description' => $v['module_description'],
						                            'module_folder' => $v['module_folder'],
						                            'module_sort_order' => $v['module_sort_order'],
						                            );
					}
				}
			}
		}

		$q->free_result();

		return !empty($rows) ? sc($rows) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get user invoices
	 *
	 * Get all user invoices for the member from the db
	 *
	 * @param string $member_id
	 * @param int $limit
	 * @return bool|string
	 */
	public function get_user_invoices($id = '', $limit = MEMBER_RECORD_LIMIT, $get_cache = TRUE)
	{
		$sort = $this->config->item(TBL_INVOICES, 'db_sort_order');

		$sql = 'SELECT p.*, c.payment_status, c.color
					  FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
					    LEFT JOIN ' . $this->db->dbprefix(TBL_PAYMENT_STATUS) . ' c
					        ON p.payment_status_id = c.payment_status_id
					    WHERE member_id = \'' . $id . '\'
					        ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
					        LIMIT ' . $limit;

		//check if we have a cache file and return that instead
		$cache = __METHOD__ . md5($sql);

		if ($row = $this->init->cache($cache, 'public_db_query'))
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
			$row = array(
				'rows'           => $q->result_array(),
				'debug_db_query' => $this->db->last_query(),
			);

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_INVOICES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT *, p.invoice_id AS invoice_id '; //for the page rows only

		$count = 'SELECT COUNT(p.invoice_id) AS total 	
					FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p '; //for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PAYMENT_STATUS) . ' d
                        ON p.payment_status_id = d.payment_status_id';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_INVOICES, TBL_PAYMENT_STATUS), $options['query']);

			$sql .= $options['where_string'];
			$count .= $options['where_string'];
		}

		//set the order and limit clause
		$order = ' GROUP BY p.invoice_id
                    ORDER BY p.' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . md5($select . $sql . $order);
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
					'success'        => TRUE,
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_payment_rows($options = '')
	{
		$sort = $this->config->item(TBL_INVOICE_PAYMENTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT p.*, d.invoice_number'; //for the page rows only

		$count = 'SELECT COUNT(p.invoice_payment_id) AS total 
					FROM ' . $this->db->dbprefix(TBL_INVOICE_PAYMENTS) . ' p '; //for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(TBL_INVOICE_PAYMENTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' d
                        ON p.invoice_id = d.invoice_id';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_INVOICES, TBL_PAYMENT_STATUS), $options['query']);

			$sql .= $options['where_string'];
			$count .= $options['where_string'];
		}

		//set the order and limit clause
		$order = ' GROUP BY p.invoice_payment_id
                    ORDER BY p.' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . md5($select . $sql . $order);
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
	 * Invoice details
	 *
	 * Get details for specified invoice id
	 *
	 * @param string $id
	 * @return bool|string
	 */
	public function get_details($id = '', $public = FALSE, $product_data = FALSE, $get_cache = FALSE)
	{
		//let's get the invoice details first
		$sql = 'SELECT p.*';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ', d.payment_status, d.color, o.order_number,
                        a.region_name AS customer_region_name,
                        a.region_code AS customer_state_code,
                        a.region_id AS customer_region_id,
                        s.country_name AS customer_country_name,
                        s.country_iso_code_2 AS customer_country_code_2,
                        s.country_iso_code_3 AS customer_country_code_3,
                        s.country_id AS customer_country_id,
                        r.region_name AS shipping_region_name,
                        r.region_code AS shipping_state_code,
                        r.region_name AS shipping_region_id,
                        c.country_name AS shipping_country_name,
                        c.country_iso_code_2 AS shipping_country_iso_code_2,
                        c.country_iso_code_3 AS shipping_country_iso_code_3,
                        c.country_id AS shipping_country_id,
                        b.username AS affiliate_username,
                        DATE_FORMAT(p.date_purchased,\'' . $this->config->item('sql_date_format') . '\')
                            AS date_purchased_formatted,
                        DATE_FORMAT(p.due_date,\'' . $this->config->item('sql_date_format') . '\')
                            AS due_date_formatted,
					    (SELECT SUM(amount)
					        FROM ' . $this->db->dbprefix(TBL_INVOICE_PAYMENTS) . ' m
						    WHERE p.invoice_id = m.invoice_id)
                                AS payments
                        FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PAYMENT_STATUS) . ' d 
                            ON p.payment_status_id = d.payment_status_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS) . ' o 
                            ON o.order_id = p.order_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' a 
                            ON p.customer_state = a.region_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' s 
                            ON p.customer_country = s.country_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' r 
                            ON p.shipping_state = r.region_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' c 
                            ON p.shipping_country = c.country_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' b 
                            ON p.affiliate_id = b.member_id
                        WHERE invoice_id = \'' . valid_id($id) . '\'';

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
			$row['items'] = $this->get_invoice_items($id, $product_data);

			//get invoice totals if any
			$row['totals'] = $this->get_invoice_totals($id);

			//get invoice payments if any
			$row['payments'] = $this->get_invoice_payments($id);

			//get any commissions if any
			$row['commissions'] = $this->comm->get_commissions($id, 'invoice_id', FALSE);

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return array|false
	 */
	public function get_payment_statuses($form = FALSE)
	{
		if (!$q = $this->db->get(TBL_PAYMENT_STATUS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $form == TRUE ? format_array($q->result_array(), 'payment_status_id', 'payment_status') : $q->result_array();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_payment_details($id = '', $public = FALSE)
	{
		$sql = 'SELECT p.*, d.invoice_number ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT p.invoice_payment_id
                            FROM ' . $this->db->dbprefix(TBL_INVOICE_PAYMENTS) . ' p
                            WHERE p.invoice_payment_id < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT p.invoice_payment_id
                            FROM ' . $this->db->dbprefix(TBL_INVOICE_PAYMENTS) . ' p
                            WHERE p.invoice_payment_id > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ', DATE_FORMAT(date,\'' . $this->config->item('sql_date_format') . '\')
                            AS date_formatted
                FROM ' . $this->db->dbprefix(TBL_INVOICE_PAYMENTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' d
                        ON p.invoice_id = d.invoice_id
                    WHERE invoice_payment_id = \'' . valid_id($id) . '\'';

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $amount
	 * @param string $id
	 * @return bool
	 */
	public function mark_paid($amount = '', $id = '')
	{
		//get the invoice first
		if (!$q = $this->db->where($this->id, $id)->get(TBL_INVOICES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			if (format_amount($row['total'], FALSE) <= format_amount($amount, FALSE))
			{
				if (!$this->db->where($this->id, $id)
					->update(TBL_INVOICES, array('payment_status_id' => '2'))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function mark_refunded($id = '')
	{
		if (!$this->db->where($this->id, $id)->update(TBL_INVOICES, array('payment_status_id' => '3')))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
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
	 * @param string $options
	 * @return bool|false|string
	 */
	public function search($options = '')
	{
		$sort = $this->config->item(TBL_INVOICES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT *, p.invoice_id AS invoice_id '; //for the page rows only

		$count = 'SELECT COUNT(p.invoice_id) AS total 	
					FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p '; //for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PAYMENT_STATUS) . ' d
                        ON p.payment_status_id = d.payment_status_id';

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_INVOICES);

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
		$order = ' GROUP BY p.invoice_id
                    ORDER BY p.' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . md5($select . $sql . $order);
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
					'success'        => TRUE,
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $status
	 * @param string $column
	 * @return bool
	 */
	public function update_status($id = '', $status = '0', $column = 'payment_status_id')
	{
		if (!$this->db->where($this->id, $id)->update(TBL_INVOICES, array($column => $status)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		//update the invoice first...
		$this->update_invoice($data);

		//update items
		$this->update_items($data);

		//update totals
		$this->update_totals($data);

		return array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function update_invoice($data = array())
	{
		//update invoice
		$sdata = $this->dbv->clean($data, TBL_INVOICES);

		//update db
		if (!$this->db->where($this->id, $data[$this->id])->update(TBL_INVOICES, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update_payment($data = array())
	{
		//update invoice
		$sdata = $this->dbv->clean($data, TBL_INVOICE_PAYMENTS);

		//update db
		if (!$this->db->where('invoice_payment_id', $data['invoice_payment_id'])->update(TBL_INVOICE_PAYMENTS, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array('success'  => TRUE,
		             'data'     => $sdata,
		             'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $func
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate_payment($func = 'create', $data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('invoice_payments', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_INVOICE_PAYMENTS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|strip_tags|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			//go through each field type first and validate based on it....
			$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

			//now let's add any proprietary rules for this form...
			switch ($f->name)
			{
				case 'cc_last_four':

					$rule .= '|integer|max_length[4]';

					break;
			}

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array('data'    => $this->dbv->validated($data),
			             'success' => TRUE,
			);
		}
		else
		{
			$row = array('msg_text' => validation_errors(),
			             'error'    => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $func
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate($func = 'create', $data = array())
	{
		$row = array('msg_text' => '',
		             'data'     => $data,
		);

		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('invoices_' . $func, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_INVOICES);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|strip_tags|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			//go through each field type first and validate based on it....
			$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

			//now let's add any proprietary rules for this form...
			switch ($f->name)
			{
				case 'customer_primary_email':

					$rule .= '|strtolower|valid_email';

					break;
			}

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			$row['data'] = $this->dbv->validated($data);
		}
		else
		{
			$row['error'] = TRUE;
			$row['msg_text'] .= validation_errors();
		}

		//get the list of fields required for this
		$required = $this->config->item('invoices_items', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_INVOICE_ITEMS);

		//validate the items
		foreach ($data['items'] as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);

			//go through each field and
			foreach ($fields as $f)
			{
				//set the default rule
				$rule = 'trim|xss_clean';

				//if this field is a required field, let's set that
				if (in_array($f->name, $required))
				{
					$rule .= '|required';
				}

				//go through each field type first and validate based on it....
				$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

				$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
			}

			if ($this->form_validation->run())
			{
				$row['data']['items'][$k] = $this->dbv->validated($v);
			}
			else
			{
				$row['error'] = TRUE;
				$row['msg_text'] .= validation_errors();
			}
		}

		if (empty($row['error']))
		{
			$row['success'] = TRUE;
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	protected function delete_invoice_item($id = '')
	{
		if (!$this->db->where('invoice_item_id', $id)->delete(TBL_INVOICE_ITEMS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	protected function get_invoice_totals($id = '')
	{
		if (!$q = $this->db->where($this->id, $id)
			->order_by('sort_order', 'ASC')
			->get(TBL_INVOICE_TOTALS)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get invoice items
	 *
	 * Get the associated products for specified invoice
	 *
	 * @param string $id
	 * @return bool
	 */
	protected function get_invoice_items($id = '', $product_data = FALSE)
	{
		$sort = $this->config->item(TBL_INVOICE_ITEMS, 'db_sort_order');

		$sql = 'SELECT * 
				 FROM ' . $this->db->dbprefix(TBL_INVOICE_ITEMS) . ' p ';

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

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get invoice payments
	 *
	 * Get the associated payments for specified invoice
	 *
	 * @param string $id
	 * @return bool
	 */
	protected function get_invoice_payments($id = '')
	{
		$sort = $this->config->item(TBL_INVOICE_PAYMENTS, 'db_sort_order');

		$this->db->select('*,
                    DATE_FORMAT(date,\'' . $this->config->item('sql_date_format') . '\')
                        AS date_formatted');

		if (!$q = $this->db->where($this->id, $id)
			->order_by($sort['column'], $sort['order'])
			->get(TBL_INVOICE_PAYMENTS)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @param string $type
	 * @param bool $format
	 * @return array
	 */
	protected function insert_invoice_items($id = '', $data = array(), $type = 'checkout', $format = TRUE)
	{
		$row = $format == TRUE ? format_invoice_items($id, $data, $type) : $data;

		foreach ($row as $v)
		{
			if (!$this->db->insert(TBL_INVOICE_ITEMS, $v))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @param string $type
	 * @param bool $format
	 * @return array
	 */
	protected function insert_invoice_totals($id = '', $data = array(), $type = 'checkout', $format = TRUE)
	{
		$row = $format == TRUE ? format_invoice_totals($id, $data, $type) : $data;

		foreach ($row as $v)
		{
			if (!$this->db->insert(TBL_INVOICE_TOTALS, $v))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	protected function update_totals($data = array())
	{
		if (!empty($data['totals']))
		{
			$total = 0;
			foreach ($data['totals'] as $k => $v)
			{
				//run the db query
				if (!$this->db->where('invoice_total_id', $k)->update(TBL_INVOICE_TOTALS, array('amount' => $v)))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		//update subtotal
		$vars = array('amount' => calc_invoice_amount($data));
		if (!$this->db->where('invoice_total_id', $data['sub_total_id'])->update(TBL_INVOICE_TOTALS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//update total
		$vars = array('total' => $data['totals'][$data['total_id']]);
		if (!$this->db->where('invoice_id', $data[$this->id])->update(TBL_INVOICES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//update the invoice itself
		$vars[$this->id] = $data[$this->id];
		$this->update_invoice($vars);

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	protected function update_items($data = array())
	{
		if (!empty($data['items']))
		{
			//init the array...
			$dbs = array();

			//get items from the db first...
			$b = $this->get_invoice_items($data[$this->id]);

			//let's add these to the array for comparison..
			foreach ($b as $v)
			{
				array_push($dbs, $v['invoice_item_id']);
			}

			//now loop through the new items
			foreach ($data['items'] as $v)
			{
				if (!empty($v['invoice_item_id']))
				{
					if (in_array($v['invoice_item_id'], $dbs))
					{
						//let's update the item in the db...
						if (!$this->db->where('invoice_item_id', $v['invoice_item_id'])->update(TBL_INVOICE_ITEMS, $v))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}

						//remove from the array so we can delete the reset later
						$dbs = array_diff($dbs, array($v['invoice_item_id']));
					}
				}
				else
				{
					//now if there's some new ones's to add, let's add it....
					$v['invoice_id'] = $data['invoice_id'];
					if (!$this->db->insert(TBL_INVOICE_ITEMS, $v))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}
			//delete other items  not part of the invoice
			if (count($dbs) > 0)
			{
				foreach ($dbs as $v)
				{
					$this->delete_invoice_item($v);
				}
			}
		}

		return TRUE;
	}
}
