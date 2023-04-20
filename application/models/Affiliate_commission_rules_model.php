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
class Affiliate_commission_rules_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'id';

	// ------------------------------------------------------------------------

	/**
	 * Create rule
	 *
	 * Create a new commission rule
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_AFFILIATE_COMMISSION_RULES);

		if (!$this->db->insert(TBL_AFFILIATE_COMMISSION_RULES, $vars))
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
	 * Generate rule commission
	 *
	 * Generate the commission set by rules
	 *
	 * @param array $data
	 * @param array $rule
	 */
	public function generate_rule_comm($data = array(), $rule = array())
	{
		//check if we're restricting by level...
		if (config_option('sts_affiliate_commission_levels') > 1 && !empty($rule['level']))
		{
			if ($rule['level'] != $data['commission_level'])
			{
				return;
			}
		}

		//check if we're upgrading groups or giving out a bonus commission
		switch ($rule['action'])
		{
			case 'issue_bonus_commission':

				$vars = format_rule_comm($data, $rule);

				return $this->comm->create_commission($vars);

				break;

			case 'assign_affiliate_group':

				$this->aff_group->update_user_group($data['member_id'], $rule['group_id']);

				break;

			case 'issue_reward_points': //from affiliate commission rules

				$this->rewards->add_reward_points($data['member_id'], 'reward_affiliate_rule', $rule['bonus_amount']);

				break;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Rule Details
	 *
	 * @param string $id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_details($id = '', $public = FALSE)
	{
		$sql = 'SELECT *,
                 DATE_FORMAT(p.end_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS end_date_formatted ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSION_RULES) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSION_RULES) . ' p
                            WHERE p.' . $this->id . '  > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSION_RULES) . ' p
                        WHERE p.' . $this->id . ' = ' . (int)$id;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get commission rules
	 *
	 * Get commission rules that are active
	 *
	 * @return bool
	 */
	public function get_comm_rules()
	{
		$this->db->order_by('sort_order', 'ASC');
		if (!$q = $this->db->where('status', '1')->get(TBL_AFFILIATE_COMMISSION_RULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_AFFILIATE_COMMISSION_RULES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *
                 FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSION_RULES) . ' p';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_AFFILIATE_COMMISSION_RULES), $options['query']);

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
				'total'   => $this->dbv->get_table_totals($options, TBL_AFFILIATE_COMMISSION_RULES),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Initialize commission rules
	 *
	 * Get commissions rules and initialize them to apply to specific triggers
	 *
	 * @param array $data
	 * @param string $amount
	 * @return array
	 */
	public function init_comm_rules($data = array(), $amount = '')
	{
		//get rules
		$rows = $this->get_comm_rules();

		if (!empty($rows))
		{
			//set comm array
			$comm_array = array();

			//format amount
			$amount = format_amount($amount, FALSE);

			//loop through rules
			foreach ($rows as $v)
			{
				//check if the rules have an expiration...
				if ($v['enable_end_date'] == 1)
				{
					if (strtotime($v['end_date']) < now())
					{
						continue; //its expired, let's move on...
					}
				}

				//loop through the commissions if more than one
				foreach ($data as $c)
				{
					//set the amount
					$set_amount = $v['sale_type'] == 'amount_of_sale' ? $amount : $c['commission_amount'];

					switch ($v['operator'])
					{
						case 'greater_than':

							if ($set_amount > $v['sale_amount'])
							{
								$comm = $this->generate_rule_comm($c, $v);
							}

							break;

						case 'less_than':

							if ($set_amount < $v['sale_amount'])
							{
								$comm = $this->generate_rule_comm($c, $v);
							}

							break;

						case 'equal_to':

							if ($set_amount == $v['sale_amount'])
							{
								$comm = $this->generate_rule_comm($c, $v);
							}

							break;
					}

					if (!empty($comm))
					{
						array_push($comm_array, $comm);
					}
				}
			}

			return $comm_array;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update
	 *
	 * Mass update commission rules
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function mass_update($data = array())
	{
		foreach ($data as $k => $v)
		{
			$vars = array('sort_order' => $v,
			              'id'         => $k);

			$this->dbv->update(TBL_AFFILIATE_COMMISSION_RULES, $this->id, $vars);
		}

		$this->dbv->db_sort_order(TBL_AFFILIATE_COMMISSION_RULES, $this->id, 'sort_order');

		$row = array(
			'data'     => $vars,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $rules
	 * @param array $data
	 * @return bool|false|string
	 */
	public function process_cron_rules($rules = array(), $data = array())
	{
		if (!empty($rules))
		{
			foreach ($rules as $rule)
			{
				if ($rule['enable_end_date'] == 1)
				{
					if (strtotime($rule['end_date']) < now())
					{
						continue; //its expired, let's move on...
					}
				}

				switch ($rule['sale_type'])
				{
					case 'total_amount_of_commissions':

					$sql = 'SELECT SUM(commission_amount) AS amount 
							FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
							WHERE member_id = \'' . valid_id($data['member_id']) . '\'';

						break;

					case 'total_amount_of_sales':

						$sql = 'SELECT SUM(sale_amount) AS amount 
							FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
							WHERE member_id = \'' . valid_id($data['member_id']) . '\'';

						break;

					case 'total_amount_of_referrals':

						$sql = 'SELECT COUNT(member_id) AS amount 
							FROM ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' p
							WHERE original_sponsor_id = \'' . valid_id($data['member_id']) . '\'';

						break;

					case 'total_amount_of_clicks':

						$sql = 'SELECT COUNT(member_id) AS amount 
							FROM ' . $this->db->dbprefix(TBL_AFFILIATE_TRAFFIC) . ' p
							WHERE member_id = \'' . valid_id($data['member_id']) . '\'';

						break;
				}

				if (!empty($sql))
				{
					switch ($rule['time_limit'])
					{
						case 'current_month':

							$sql .= ' AND MONTH(date) = ' . date('m') . '
											AND YEAR(date) = ' . date('Y');

							break;

						case 'current_year':

							$sql .= ' AND YEAR(date) = ' . date('Y');

							break;

						case 'last_month':

							$sql .= ' AND MONTH(date) = ' . (date('m') - 1) . '
											AND YEAR(date) = ' . date('Y');

							break;

						case 'last_year':

							$sql .= ' AND YEAR(date) = ' . (date('Y') - 1);

							break;
					}

					$sql .= ' AND performance_paid = \'0\'';

					if (!$q = $this->db->query($sql))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}

					if ($q->num_rows() > 0)
					{
						$t = $q->row_array();

						switch ($rule['operator'])
						{
							case 'greater_than':

								if ($t['amount'] > $rule['sale_amount'])
								{
									$comm = $this->generate_rule_comm($data, $rule);
								}

								break;

							case 'less_than':

								if ($t['amount'] < $rule['sale_amount'])
								{
									$comm = $this->generate_rule_comm($data, $rule);
								}

								break;

							case 'equal_to':

								if ($t['amount'] == $rule['sale_amount'])
								{
									$comm = $this->generate_rule_comm($data, $rule);
								}

								break;
						}

						//update table with performance paid
						$this->update_performance_paid($data, $rule);

						if (!empty($comm))
						{
							$row = array(
								'msg_text' => lang('rewards_generated_successfully'),
								'success'  => TRUE,
							);
						}
					}
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $rule
	 * @return bool
	 */
	public function update_performance_paid($data = array(), $rule = array())
	{
		switch ($rule['sale_type'])
		{
			case 'total_amount_of_commissions':

				$sql = 'UPDATE ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
				            SET performance_paid = \'1\'
							WHERE member_id = \'' . valid_id($data['member_id']) . '\'';

				break;

			case 'total_amount_of_sales':

				$sql = 'UPDATE ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
							SET performance_paid = \'1\'
							WHERE member_id = \'' . valid_id($data['member_id']) . '\'';

				break;

			case 'total_amount_of_referrals':

				$sql = 'UPDATE ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' m
							SET performance_paid = \'1\'
							WHERE original_sponsor_id = \'' . valid_id($data['member_id']) . '\'';

				break;

			case 'total_amount_of_clicks':

				$sql = 'UPDATE ' . $this->db->dbprefix(TBL_AFFILIATE_TRAFFIC) . ' p
							SET performance_paid = \'1\'
							WHERE member_id = \'' . valid_id($data['member_id']) . '\'';

				break;
		}

		if (!empty($sql))
		{
			switch ($rule['time_limit'])
			{
				case 'current_month':

					$sql .= ' AND MONTH(date) = ' . date('m') . '
											AND YEAR(date) = ' . date('Y');

					break;

				case 'current_year':

					$sql .= ' AND YEAR(date) = ' . date('Y');

					break;

				case 'last_month':

					$sql .= ' AND MONTH(date) = ' . (date('m') - 1) . '
											AND YEAR(date) = ' . date('Y');

					break;

				case 'last_year':

					$sql .= ' AND YEAR(date) = ' . (date('Y') - 1);

					break;
			}

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Update rule
	 *
	 * Update specific rule
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_AFFILIATE_COMMISSION_RULES);

		if (!$this->db->where($this->id, $vars[$this->id])->update(TBL_AFFILIATE_COMMISSION_RULES, $vars))
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
	 * Validate rule
	 *
	 * Validate date for rule
	 *
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('affiliate_commission_rules', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_AFFILIATE_COMMISSION_RULES);

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
				case 'sale_type':

					$rule .= '|in_list[' . implode(',', config_option('rule_sale_types')) . ']';

					break;

				case 'time_limit':

					$rule .= '|in_list[' . implode(',', config_option('rule_time_limit')) . ']';

					break;

				case 'end_date':

					$rule .= '|end_date_to_sql';

					break;

				case 'operator':

					$rule .= '|in_list[' . implode(',', config_option('rule_operator')) . ']';

					break;

				default:

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					break;
			}

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, TRUE),
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

/* End of file Affiliate_commission_rules_model.php */
/* Location: ./application/models/Affiliate_commission_rules_model.php */