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
class Affiliate_payments_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'aff_pay_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_AFFILIATE_PAYMENTS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT *
				FROM ' . $this->db->dbprefix(TBL_AFFILIATE_PAYMENTS) . ' p ';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_AFFILIATE_PAYMENTS ), $options[ 'query' ]);

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
				'total'          => $this->dbv->get_table_totals($options, TBL_AFFILIATE_PAYMENTS),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get affiliate payment details
	 *
	 * @param string $id
	 * @return bool|string
	 */
	public function get_details($id = '')
	{
		$sql = 'SELECT p.*,
                 b.username,
                 DATE_FORMAT(p.payment_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS payment_date_formatted,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_AFFILIATE_PAYMENTS) . ' p
                        WHERE p.' . $this->id . ' < ' . (int)$id . '
                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                            AS prev,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_AFFILIATE_PAYMENTS) . ' p
                        WHERE p.' . $this->id . ' > ' . (int)$id . '
                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                            AS next
                        FROM ' . $this->db->dbprefix(TBL_AFFILIATE_PAYMENTS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' b
                            ON p.member_id = b.member_id
                        WHERE p.' . $this->id . '= ' . (int)$id;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get affiliate payments per member
	 *
	 * @param string $id
	 * @param int $limit
	 * @param bool $get_cache
	 * @return bool|string
	 */
	public function get_user_payments($id = '', $limit = MEMBER_RECORD_LIMIT, $get_cache = TRUE)
	{
		$sort = $this->config->item(TBL_AFFILIATE_PAYMENTS, 'db_sort_order');

		$sql = 'SELECT *
                FROM ' . $this->db->dbprefix(TBL_AFFILIATE_PAYMENTS) . ' c
                    WHERE member_id =\'' . (int)$id . '\'
                    ORDER BY ' . $sort[ 'column' ] . ' ' . $sort[ 'order' ] . '
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

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		// Save into the cache
		$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Update affiliate payment
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function update($data = array())
	{
		foreach ($data as $k => $v)
		{
			$this->mod->update_module_setting($k, $v);
		}

		$row = array( 'success'  => TRUE,
		              'data'     => $data,
		              'msg_text' => lang('system_updated_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Run form validation on payments
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		$row = $this->mod->get_module_details(valid_id($data[ 'module_id' ]));

		//validate the module configuration settings...
		if (!empty($row[ 'values' ]))
		{
			foreach ($row[ 'values' ] as $v)
			{
				$rule = 'trim|required';

				$lang = format_settings_label($v[ 'key' ], $row[ 'module' ][ 'module_type' ], $row[ 'module' ][ 'module_folder' ]);

				switch ($v[ 'type' ])
				{
					case 'text':

						$rule .= !empty($v[ 'function' ]) ? '|' . trim($v[ 'function' ]) : '';

						switch ($lang)
						{
							case 'end_date':
								$start = 'module_' . $row[ 'module' ][ 'module_type' ] . '_' . $row[ 'module' ][ 'module_folder' ] . '_start_date';
								$start = strtotime(start_date_to_sql($data[ $start ]));
								$end = strtotime(end_date_to_sql($data[ $v[ 'key' ] ]));

								if ($start > $end)
								{
									return array( 'error'    => TRUE,
									              'msg_text' => lang('end_date_must_be_greater_than_start_date'),
									);
								}

								break;
						}

						break;

					case 'dropdown':

						$options = array();
						foreach (options($v[ 'function' ]) as $a => $b)
						{
							array_push($options, $a);
						}

						$rule .= '|in_list[' . implode(',', $options) . ']';

						break;
				}

				$this->form_validation->set_rules($v[ 'key' ], 'lang:' . $lang, $rule);
			}
		}

		if ($this->form_validation->run())
		{
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data),
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

	// ------------------------------------------------------------------------

	/**
	 * Create an affiliate payment
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function create_payment($data = array())
	{
		$data = $this->dbv->clean($data, TBL_AFFILIATE_PAYMENTS);

		if (!$this->db->insert(TBL_AFFILIATE_PAYMENTS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Mark commissions as paid after payment is made
	 *
	 * @param array $data
	 * @param string $note
	 * @return array
	 */
	public function mark_commissions_paid($data = array(), $note = '')
	{
		//add a new affiliate payment in db
		$vars = array( 'member_id'       => $data['member_id'],
		               'payment_name'    => $data[ 'name' ],
		               'payment_date'    => get_time(now(), TRUE),
		               'payment_type'    => $data[ 'type' ],
		               'payment_amount'  => $data[ 'amount' ],
		               'payment_details' => $note
		);

		$row = $this->create_payment($vars);

		if (!empty($row[ 'id' ]))
		{
			$this->db->where('member_id', $data['member_id']);
			$this->db->where('comm_status', 'unpaid');
			$this->db->where('approved', '1');

			$vars = array( 'comm_status' => 'paid',
			               'date_paid'   => get_time(now(), TRUE),
			               'payment_id'  => $row[ 'id' ]
			);

			$this->db->update(TBL_AFFILIATE_COMMISSIONS, $vars);
		}

		return array('success' => TRUE,
		             'msg_text' => lang('payment_created_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $note
	 * @return bool|string
	 */
	public function mark_as_paid($data = array(), $note = '')
	{
		if (!empty($data[ 'select' ]))
		{
			foreach ($data[ 'select' ] as $v)
			{
				//add a new affiliate payment in db
				$vars = array( 'member_id'       => $v,
				               'payment_name'    => $data[ 'member' ][ $v ][ 'member_name' ],
				               'payment_date'    => get_time(now(), TRUE),
				               'payment_type'    => $data[ 'payment_type' ],
				               'payment_amount'  => $data[ 'member' ][ $v ][ 'total_amount' ],
				               'payment_details' => $note,
				);

				$row = $this->create_payment($vars);

				if (!empty($row[ 'id' ]))
				{
					$this->db->where('member_id', $v);
					$this->db->where('comm_status', 'unpaid');
					$this->db->where('approved', '1');

					$vars = array( 'comm_status' => 'paid',
					               'date_paid'   => get_time(now(), TRUE),
					               'payment_id'  => $row[ 'id' ]
					);

					$this->db->update(TBL_AFFILIATE_COMMISSIONS, $vars);
				}
			}
		}

		$row = array( 'type'  => 'success',
		              'data' => $data,
		              'msg_text' => lang('payments_created_successfully')
		);

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Affiliate_payments_model.php */
/* Location: ./application/models/Affiliate_payments_model.php */