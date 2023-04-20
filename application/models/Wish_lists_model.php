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
class Wish_lists_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'wish_list_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $pid
	 * @return array
	 */
	public function add_member_wish($id = '', $pid = '')
	{
		//first check if there is already a wish list for the use
		if ($row = $this->get_wish_list($id))
		{
			$wid = $row['wish_list_id'];
		}
		else
		{
			$row = $this->create(array('member_id' => $id));

			$wid = $row['data']['wish_list_id'];
		}

		//add the list
		if (!$this->db->insert(TBL_PRODUCTS_TO_WISH_LISTS, array('product_id' => (int)$pid,
		                                                         'wish_list_id' => (int)$wid)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array('success' => TRUE);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = array('member_id' => $data['member_id'],
		);

		if (!$q = $this->db->insert(TBL_WISH_LISTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars['wish_list_id'] = $this->db->insert_id();

		//generate rewards
		$this->rewards->add_reward_points($data['member_id'], 'reward_wish_list');

		return sc(array('success'  => TRUE,
		                'data'     => $vars,
		                'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete(TBL_WISH_LISTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'msg_text' => lang('record_deleted_successfully')));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $pid
	 * @return bool|false|string
	 */
	public function delete_member_wish($id = '', $pid = '')
	{
		$row = $this->get_wish_list(($id));

		if (!empty($row))
		{
			$this->db->where('wish_list_id', (int)$row['wish_list_id']);
			if (!$this->db->where('product_id', $pid)->delete(TBL_PRODUCTS_TO_WISH_LISTS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			return sc(array('success'  => TRUE,
			                'id'       => $id,
			                'msg_text' => lang('record_deleted_successfully')));
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_wish_list($id = '')
	{
		//first check if there is already a wish list for the user
		if (!$q = $this->db->where('member_id', (int)$id)->get(TBL_WISH_LISTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_WISH_LISTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_WISH_LISTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c
                    ON (p.member_id = c.member_id)';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_WISH_LISTS), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];


		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_WISH_LISTS),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_wish_list($options = '', $id = '', $lang_id = 1)
	{
		//set the default sort order
		$sort = $this->config->item(TBL_WISH_LISTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT
                      p.*, r.*, d.*, c.*, h.*, ';

		if (sess('discount_group'))
		{
			$select .= ' f.priority AS disc_priority,
                      f.group_amount AS disc_group_amount,
                      f.quantity AS disc_quantity,
                      f.discount_type AS disc_type, 
                       m.amount AS subscription_amount,';
		}

		$select .= ' p.product_id AS product_id,
                        (SELECT AVG(ratings)
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' y
						    WHERE p.product_id = y.product_id
						    AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations') && config_enabled('sts_tax_product_display_price_with_tax'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$count = 'SELECT COUNT(p.product_id) AS total 
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_WISH_LISTS) . ' p   
					LEFT JOIN ' . $this->db->dbprefix(TBL_WISH_LISTS) . ' s
                            ON s.wish_list_id = p.wish_list_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' r
                           ON p.product_id = r.product_id       
					 WHERE s.member_id = \'' . valid_id($id) . '\' 
					        AND r.product_status = \'1\'
                            AND r.hidden_product = \'0\'
                            AND r.date_expires >= ' . local_time('sql');  //for pagination totals

		$sql = ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_WISH_LISTS) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_WISH_LISTS) . ' s
                            ON s.wish_list_id = p.wish_list_id
						LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' n
                            ON s.member_id = n.member_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' r
                            ON p.product_id = r.product_id    
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                            ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                            ON p.product_id = c.product_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                            ON r.tax_class_id = t.tax_class_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATE_RULES) . ' u
                            ON t.tax_class_id = u.tax_class_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_PRICING) . ' m
                            ON p.product_id = m.product_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATES) . ' w
                             ON w.tax_rate_id = u.tax_rate_id ';

		if (sess('discount_group'))
		{
			$sql .= ' LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_DISC_GROUPS) . ' f
                            ON p.product_id = f.product_id
                            AND f.quantity = \'1\'
                            AND f.group_id = \'' . sess('discount_group') . '\'
                            AND f.start_date <= ' . local_time('sql') . '
                            AND f.end_date > ' . local_time('sql');
		}

		$sql .= ' LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                            ON p.product_id = h.product_id
                            AND h.product_default = \'1\'
                        WHERE s.member_id = \'' . valid_id($id) . '\' 
                            AND r.product_status = \'1\'
                            AND r.hidden_product = \'0\'
                            AND r.date_expires >= ' . local_time('sql');

		$order = ' GROUP BY p.product_id
                        ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the unique cache file
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
					'success' => TRUE,
					'values'  => $q->result_array(),
					'total'   => $this->dbv->get_query_total($count),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

}

/* End of file Wish_lists_model.php */
/* Location: ./application/models/Wish_lists_model.php */