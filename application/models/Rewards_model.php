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
class Rewards_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'rule_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $type
	 * @param string $points
	 * @return bool|false|string
	 */
	public function add_reward_points($id = '', $type = '', $points = '0')
	{
		switch ($type)
		{
			case 'reward_product_points': //products purchased
			case 'reward_affiliate_rule':  //affiliate commission rules

				$vars = array(
					'member_id' => valid_id($id),
					'type' => $type,
					'points' => (int)$points,
				);

				$row = $this->insert_reward_points($vars);

				break;

			default:

				//query the rewards table for the correct reward
				$rows = $this->get_reward_rules($type);

				if (!empty($rows))
				{
					foreach ($rows as $v)
					{
						$vars = array(
							'member_id' => valid_id($id),
							'type'      => $type,
							'points'    => $v['points'],
						);

						$row = $this->insert_reward_points($vars);
					}
				}

				break;
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_REWARDS);

		if (!$this->db->insert(TBL_REWARDS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'data'     => $vars,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_history($options = '')
	{
		$sort = $this->config->item(TBL_REWARDS_HISTORY, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, m.fname, m.lname
                 FROM ' . $this->db->dbprefix(TBL_REWARDS_HISTORY) . ' p 
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . '  m
                        ON p.member_id = m.member_id';


		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_REWARDS_HISTORY), $options['query']);

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
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_REWARDS_HISTORY),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $rule
	 * @return bool|false|string
	 */
	public function get_reward_rules($rule = '')
	{
		$sql = 'SELECT *  
				FROM ' . $this->db->dbprefix(TBL_REWARDS) . ' p
	                WHERE  p.status = \'1\'
						AND p.start_date < NOW() AND p.end_date > NOW()';

		if (!empty($rule))
		{
			$sql .= ' AND rule = \'' . url_title($rule) . '\'';
		}

		$sql .= ' ORDER BY sort_order ASC';

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @param bool $get_cache
	 * @return bool|false|string
	 */
	public function get_details($id = '', $public = FALSE, $get_cache = TRUE)
	{
		$sql = 'SELECT *,
                 DATE_FORMAT(p.start_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS start_date_formatted ,
                  DATE_FORMAT(p.end_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS end_date_formatted    ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_REWARDS) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_REWARDS) . ' p
                            WHERE p.' . $this->id . '  > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_REWARDS) . ' p
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
	 * @return bool|false|string
	 */
	public function insert_reward_points($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_REWARDS_HISTORY);

		if (!$this->db->insert(TBL_REWARDS_HISTORY, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$this->update_user_points($vars);

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'data'     => $vars,
			'success'  => TRUE,
		);

		return empty($row) ? FALSE  :  sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		foreach ($data as $k => $v)
		{
			$vars = array('sort_order' => $v,
			              'rule_id'         => $k);

			$this->dbv->update(TBL_REWARDS, $this->id, $vars);
		}

		$this->dbv->db_sort_order(TBL_REWARDS, $this->id, 'sort_order');

		$row = array(
			'data'     => $vars,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $operator
	 * @return bool
	 */
	public function update_user_points($data = array(), $operator = '+')
	{
		$sql = 'UPDATE ' . $this->db->dbprefix(TBL_MEMBERS) . '
                    SET points = points ' . $operator . ' \'' . $data['points'] . '\' 
                    WHERE member_id = \'' . valid_id($data['member_id']) . '\'';

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
		$vars = $this->dbv->clean($data, TBL_REWARDS);

		if (!$this->db->where($this->id, $vars[ $this->id ])->update(TBL_REWARDS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

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
	 * @return array
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('rewards', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_REWARDS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			switch ($f->name)
			{
				case 'rule':

					$rule .= '|in_list[' . implode(',', config_option('reward_types')) . ']';

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

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

					$rule .= '|end_date_to_sql';

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

					break;

				default:

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

					break;
			}
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, FALSE),
			);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
			);
		}

		return $row;
	}
}