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
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
class Tracking_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'tracking_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_referrals($options = '')
	{
		$sort = $this->config->item(TBL_TRACKING_REFERRALS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, m.username 
				FROM ' . $this->db->dbprefix(TBL_TRACKING_REFERRALS) . ' p 
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                    ON p.member_id = m.member_id';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_TRACKING_REFERRALS), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' 
		        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_TRACKING_REFERRALS),
				'debug_db_query' => $this->db->last_query(),
			);
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
		$sort = $this->config->item(TBL_TRACKING, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, m.username ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', COUNT(e.id) AS total';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_TRACKING) . ' p 
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                    ON p.member_id = m.member_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TRACKING_REFERRALS) . ' e 
                    ON p.tracking_id = e.tracking_id ';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_TRACKING), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= 'GROUP BY p.tracking_id
		        ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' 
		        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_TRACKING),
				'debug_db_query' => $this->db->last_query(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @param bool $get_cache
	 * @return bool|false|string
	 */
	public function get_details($id = '', $public = FALSE, $get_cache = FALSE)
	{
		$sql = 'SELECT p.*, n.username,
                 DATE_FORMAT(p.end_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS end_date';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_TRACKING) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_TRACKING) . ' p
                            WHERE p.' . $this->id . '  > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_TRACKING) . ' p
					  LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' n
                        ON p.member_id = n.member_id
                      WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.status = \'1\'';
		}

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
	 * @param array $data
	 * @return array
	 */
	public function insert_referral($data = array())
	{
		$vars = array(
			'tracking_id'       => $data['tracking_id'],
			'member_id' => is_var($data, 'member_id'),
			'referrer'  => $url = !$this->agent->referrer() ? lang('unknown_referrer') : $this->agent->referrer(),
		);

		if (!$this->db->insert(TBL_TRACKING_REFERRALS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $vars;
	}
}

/* End of file Tracking_model.php */
/* Location: ./application/models/Tracking_model.php */