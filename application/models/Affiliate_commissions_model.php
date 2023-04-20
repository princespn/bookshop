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
class Affiliate_commissions_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'comm_id';

	// ------------------------------------------------------------------------

	/**
	 * Affiliate_commissions_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('affiliate_commissions');
	}

	// ------------------------------------------------------------------------

	/**
	 * Approve Commissions
	 *
	 * Approve all commissions
	 *
	 * @return string
	 */
	public function approve_commissions()
	{
		if (!$this->db->where('approved', '0')->update(TBL_AFFILIATE_COMMISSIONS, array('comm_status' => 'unpaid',
			'approved' => '1')))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array(
			'msg_text' => lang('commissions_approved_successfully'),
			'success'  => TRUE,
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * Approve commissions automatically
	 *
	 * @return bool|string
	 */
	public function auto_approve_commissions()
	{
		if (config_item('sts_affiliate_auto_approve_commissions') > 0)
		{
			$sql = 'UPDATE ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) .
				' SET approved = \'1\', comm_status = \'unpaid\'
			WHERE date < (CURDATE() - INTERVAL ' . config_item('sts_affiliate_auto_approve_commissions') . ' DAY)
			AND approved = \'0\' AND comm_status != \'paid\'';

			if (!$this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$rows = $this->db->affected_rows();

			if (!empty($rows))
			{
				$row = array(
					'msg_text' => $rows . ' ' . lang('commissions_auto_approved_successfully'),
					'success'  => TRUE,
				);
			}
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check Transaction ID
	 *
	 * @param string $id
	 * @return bool
	 */
	public function check_trans_id($id = '')
	{
		if (REQUIRE_UNIQUE_AFFILIATE_TRANSACTION_IDS)
		{
			return $this->dbv->get_record(TBL_AFFILIATE_COMMISSIONS, 'trans_id', $id, TRUE, TRUE);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Create Affiliate Commission
	 *
	 * @param array $data
	 * @return string
	 */
	public function create($data = array())
	{
		//check if you want to create a record for a zero commission amount...
		if (empty($data['commission_amount']))
		{
			if (!defined('ENABLE_ZERO_AMOUNT_COMMISSIONS')) return;
		}

		$data = $this->dbv->clean($data, TBL_AFFILIATE_COMMISSIONS);

		if (!$this->db->insert(TBL_AFFILIATE_COMMISSIONS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//set the order id
		$data[ 'comm_id' ] = $this->db->insert_id();

		return sc(array( 'success'  => TRUE,
		                 'data'     => $data,
		                 'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * Create commission for each product
	 *
	 * @param int $level
	 * @param array $item
	 * @param array $member
	 * @param array $data
	 * @return mixed
	 */
	public function create_product_commission($level = 1, $item = array(), $member = array(), $data = array())
	{
		//amount
		$amount = $item[ 'unit_price' ];

		if (!empty($item[ 'discount_amount' ]))
		{
			$amount += $item['discount_amount'];
		}

		if (!empty($item[ 'item_coupons' ]))
		{
			$amount += $item[ 'item_coupons' ];
		}

		if (config_enabled('sts_tax_product_display_price_with_tax') && !empty($item['tax_data']['taxes']))
		{
			$amount -= $item['tax_data']['taxes'];
		}

		$amount *= $item['quantity'];

		//check if the item has custom commissions enabled
		if ($item[ 'enable_custom_commissions' ] == 1)
		{
			$group = $this->aff_group->get_product_affiliate_groups($item[ 'product_id' ], $member[ 'group_id' ], TRUE);

			//check if the commission level is enabled for this product
			if ($group[ 'enable_level_' . $level ] == 1)
			{
				$member[ 'commission_level_' . $level ] = $group[ 'commission_level_' . $level ];
				$member[ 'commission_type' ] = $group[ 'commission_type' ];
			}
		}

		//set transaction ID with product name
		$name = !empty($item['invoice_item_name']) ? $item['invoice_item_name'] : is_var($item, 'product_name');
		$data[ 'transaction' ][ 'transaction_id' ] .= ' - ' . $name;

		//generate default level commission
		return $this->create_commission(format_commission_data($level, $amount, $member, $data));
	}

	// ------------------------------------------------------------------------

	/**
	 * Create a Commission
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function create_commission($data = array())
	{
		$row = $this->create($data);

		return $row[ 'data' ];
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete a Commission
	 *
	 * @param string $id
	 * @return string
	 */
	public function delete($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete(TBL_AFFILIATE_COMMISSIONS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
		                 'msg_text' => lang('commission') . ' ' . $id . ' ' . lang('record_deleted_successfully') ));
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Commission Rows in Admin Area
	 *
	 * @param string $options
	 * @return bool|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_AFFILIATE_COMMISSIONS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$select = 'SELECT p.*,
                      commission_amount + fee AS total_amount,
                      d.invoice_number,
                      m.username ';

		$count = 'SELECT COUNT(p.comm_id) AS total '; //for pagination totals

		$sql = ' FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
                 LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' d
                        ON p.invoice_id = d.invoice_id
                 LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        ON p.member_id = m.member_id';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_AFFILIATE_COMMISSIONS ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$order = ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
                    LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		if (!$q = $this->db->query($select . $sql . $order))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_query_total($count . $sql),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Search Commissions
	 *
	 * @param string $options
	 * @return bool|string
	 */
	public function search($options = '')
	{
		$sort = $this->config->item(TBL_AFFILIATE_COMMISSIONS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$select = 'SELECT p.*,
                      commission_amount + fee AS total_amount,
                      d.invoice_number,
                      m.username ';

		$count = 'SELECT COUNT(p.comm_id) AS total 
				FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
                 LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' d
                        ON p.invoice_id = d.invoice_id
                 LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        ON p.member_id = m.member_id '; //for pagination totals

		$sql = ' FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
                 LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' d
                        ON p.invoice_id = d.invoice_id
                 LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        ON p.member_id = m.member_id';

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_AFFILIATE_COMMISSIONS);

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

				$sql .= 'OR m.username LIKE \'%' . $v . '%\' ESCAPE \'!\'';
				$count .= 'OR m.username LIKE \'%' . $v . '%\' ESCAPE \'!\'';

				$sql .= 'OR d.invoice_number LIKE \'%' . $v . '%\' ESCAPE \'!\'';
				$count .= 'OR d.invoice_number LIKE \'%' . $v . '%\' ESCAPE \'!\'';
			}
		}

		$order = ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
                    LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		if (!$q = $this->db->query($select . $sql . $order))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_query_total($count),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Commissions
	 *
	 * @param string $id
	 * @param string $col
	 * @param bool $public
	 * @param int $limit
	 * @param bool $get_cache
	 * @return bool|string
	 */
	public function get_commissions($id = '', $col = 'member_id', $public = TRUE, $limit = MEMBER_RECORD_LIMIT, $get_cache = TRUE)
	{
		$sort = $this->config->item(TBL_AFFILIATE_COMMISSIONS, 'db_sort_order');

		$sql = 'SELECT *,
                      commission_amount + fee AS total_amount,
                      (SELECT username
                        FROM ' . $this->db->dbprefix('members') . ' m
                        WHERE m.member_id = c.member_id)
                            AS username
                    FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' c
                    WHERE ' . $col . '=\'' . (int)$id . '\'';

		if ($public == TRUE && config_enabled('sts_affiliate_show_pending_comms_members'))
		{
			$sql .= ' AND c.comm_status != \'pending\' ';
		}

		$sql .= ' ORDER BY ' . $sort[ 'column' ] . ' ' . $sort[ 'order' ] . '
                        LIMIT ' . $limit;

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

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		// Save into the cache
		$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Commission Details
	 *
	 * @param string $id
	 * @param bool $public
	 * @param bool $get_cache
	 * @return bool|string
	 */
	public function get_details($id = '', $public = FALSE, $get_cache = TRUE)
	{
		$sql = 'SELECT *, d.invoice_id AS invoice_id,
                 b.username,
                 DATE_FORMAT(p.date,\'' . $this->config->item('sql_date_format') . '\')
                    AS date_formatted,
                 DATE_FORMAT(p.date_paid,\'' . $this->config->item('sql_date_format') . '\')
                    AS date_paid_formatted,
                      c.region_name AS customer_region_name,
                        c.region_code AS customer_state_code,
                        c.region_name AS customer_region_id,
                        k.country_name AS customer_country_name,
                        k.country_iso_code_2 AS customer_country_iso_code_2,
                        k.country_iso_code_3 AS customer_country_iso_code_3,
                      t.region_name AS shipping_region_name,
                        t.region_code AS shipping_state_code,
                        t.region_name AS shipping_region_id,
                        r.country_name AS shipping_country_name,
                        r.country_iso_code_2 AS shipping_country_iso_code_2,
                        r.country_iso_code_3 AS shipping_country_iso_code_3';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)valid_id($id) . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)valid_id($id) . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' d
                            ON p.invoice_id = d.invoice_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' c
			                ON d.customer_state = c.region_id
			            LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' k
			                ON d.customer_country = k.country_id    
			            LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' t
			                ON d.shipping_state = t.region_id
			            LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' r
			                ON d.shipping_country = r.country_id        
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' b
                            ON p.member_id = b.member_id
                        WHERE p.' . $this->id . '= ' . (int)valid_id($id);

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

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Generate Signup Bonuses for Commissions
	 *
	 * @param array $data
	 * @return array
	 */
	public function generate_signup_bonuses($data = array())
	{
		//generate user signup bonus
		if (!empty($data['member_id']))
		{
			$s = $this->generate_bonus($data);
			$data['signup_bonus'] = $s['data'];
		}

		//generate affiliate signup bonus
		if (!empty($data['original_sponsor_id']))
		{
			$r = $this->generate_bonus($data, 'referral_');
			$data['referral_bonus'] = $r['data'];
		}

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Create the Bonus
	 *
	 * @param array $data
	 * @param string $type
	 * @return bool|string
	 */
	public function generate_bonus($data = array(), $type = '')
	{
		if (config_enabled('sts_affiliate_enable_' . $type . 'signup_bonus'))
		{
			$vars = $this->create(format_signup_bonus($data, $type));

			return $vars;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Generate Commissions on Payment
	 *
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function generate_commissions($data = array(), $type = 'checkout')
	{
		$commissions = array();

		$do_per_product = config_option('sts_affiliate_total_sale_commissions');

		switch ($type)
		{
			case 'admin':

				$sub_total = get_subtotal($data['totals']);
				$affiliate_id = array('member_id' => $data['affiliate_id']);
				$items = $data['items'];

				if (!$this->check_self_commissions($data['member_id'], $affiliate_id))
				{
					return;
				}

				break;

			case 'cron':

				$sub_total = $data['invoice']['sub_total'];
				$affiliate_id = array('member_id' => $data['invoice']['affiliate_id']);
				$items = $data['order']['items'];

				if (!$this->check_self_commissions($data['invoice']['member_id'], $affiliate_id))
				{
					return;
				}

				break;

			case 'invoice':

				$sub_total = get_subtotal($data['invoice']['totals']);
				$affiliate_id = array('member_id' => $data['invoice']['affiliate_id']);
				$items = $data['order']['items'];

				if (!$this->check_self_commissions($data['invoice']['member_id'], $affiliate_id))
				{
					return;
				}

				break;

			case 'external':

				$sub_total = $data['sub_total'];
				$affiliate_id = array('member_id' => $data['affiliate']['member_id']);
				$items = '';
				$do_per_product = 'total_sale';

				break;

			default:

				$sub_total = checkout_get_cart_subtotal($data['cart']['totals']);
				$affiliate_id = $data['affiliate'];
				$items = $data[ 'cart' ][ 'items' ];

				if (!$this->check_self_commissions($data['invoice']['data']['member_id'], $affiliate_id['member_id']))
				{
					return;
				}

				break;
		}

		//get upline
		$row = $this->downline->check_upline($affiliate_id);

		//go through each member in the downline
		$row = array_reverse($row, TRUE);

		foreach ($row as $k => $v)
		{
			//we're paying out per product commissions
			if ($do_per_product == 'per_product')
			{
				foreach ($items as $c)
				{
					if ($c[ 'disable_commissions' ] == '0')
					{
						$comm = $this->create_product_commission($k, $c, $v, $data);

						if (!empty($comm))
						{
							array_push($commissions, array_merge($v, $comm));
						}
					}
				}
			}
			else //let's pay out commissions based on the entire transaction amount
			{
				$comm = $this->create_commission(format_commission_data($k, $sub_total, $v, $data));

				array_push($commissions, array_merge($v, $comm));
			}
		}

		//check for commission rules
		$rule_comms = $this->comm_rules->init_comm_rules($commissions, $sub_total);

		if (is_array($rule_comms) && count($rule_comms) > 0)
		{
			$commissions = array_merge($commissions, $rule_comms);
		}

		return $commissions;
	}

	// ------------------------------------------------------------------------

	/**
	 * Restric commissions from being self generated
	 *
	 * @param string $member_id
	 * @param string $affiliate_id
	 * @return bool
	 */
	public function check_self_commissions($member_id = '0', $affiliate_id = '0')
	{
		if (config_enabled('sts_affiliate_restrict_self_commission'))
		{
			if ($member_id == $affiliate_id)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Commission Rules
	 *
	 * @param string $options
	 * @return bool|string
	 */
	public function get_rules($options = '')
	{
		$sort = $this->config->item(TBL_AFFILIATE_COMMISSION_RULES, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT *
                 FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSION_RULES) . ' p';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_AFFILIATE_COMMISSIONS ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
                    LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_AFFILIATE_COMMISSION_RULES),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update commissions
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data[ 'comm_id' ]))
		{
			foreach ($data[ 'comm_id' ] as $v)
			{
				if ($data[ 'change-status' ] == 'delete')
				{
					$this->delete($v);
				}
				else
				{
					switch ($data[ 'change-status' ])
					{
						case '1':
						case '0':

							$vars[ 'approved' ] = $data[ 'change-status' ];

							if ($data['change-status'] == 1)
							{
								$vars['comm_status'] = 'unpaid';
							}

							break;

						case 'unpaid':
						case 'pending':

							$vars[ 'comm_status' ] = $data[ 'change-status' ];

							break;
					}

					if (!$this->db->where($this->id, $v)->update(TBL_AFFILIATE_COMMISSIONS, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}

			$row = array( 'success'  => TRUE,
			              'data'     => $data,
			              'msg_text' => lang('mass_update_successful'),
			);
		}

		//order the tier groups numerically
		$this->dbv->db_sort_order(TBL_BRANDS, 'brand_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update a commission
	 *
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$data = $this->dbv->clean($data, TBL_AFFILIATE_COMMISSIONS);

		if (!$q = $this->db->where($this->id, valid_id($data[ $this->id ]))->update(TBL_AFFILIATE_COMMISSIONS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('commission') . ' ' . $data[$this->id] . ' ' . lang('record_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Update commission status
	 *
	 * @param string $status
	 * @param string $id
	 * @return array|bool
	 */
	public function update_status($status = '', $id = '')
	{
		switch ($status)
		{
			case '1':
			case '0':

				$column = 'approved';
				break;

			default:

				$column = 'comm_status';

				break;
		}

		$vars = array( $column => $status );

		//if approved, set the comm_status to pending as well
		if ($status == '1')
		{
			$vars[ 'comm_status' ] = 'unpaid';
		}

		if (!$this->db->where($this->id, $id)
			->update(TBL_AFFILIATE_COMMISSIONS, $vars)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'data'     => $id,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Run form validation on commission data
	 *
	 * @param string $func
	 * @param array $data
	 * @return bool|string
	 */
	public function validate($func = 'create', $data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('affiliate_commissions', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_AFFILIATE_COMMISSIONS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|strip_tags|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			//check if this is an admin or member response
			switch ($f)
			{
				case 'invoice_id':
				case 'member_id':
				case 'payment_id':
				case 'approved':
				case 'commission_level':

					$rule .= '|integer';

					break;

				case 'commission_amount':

					if ($func == 'create' && empty($data[ 'use_group_amounts' ]))
					{
						$rule .= '|required';
					}

					$rule .= '|numeric';

					break;

				case 'sale_amount':
				case 'fee':

					$rule .= '|numeric';

					break;

				case 'date':
				case 'date_paid':

					$rule .= '|date_to_sql';

					break;

				case 'trans_id':

					$rule .= '|max_length[50]';

					break;
			}

			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array( 'success'  => TRUE,
			              'data'     => $this->dbv->validated($data),
			);
		}
		else
		{
			$row = array( 'error'    => TRUE,
			              'msg_text' => validation_errors(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Affiliate_commissions_model.php */
/* Location: ./application/models/Affiliate_commissions_model.php */