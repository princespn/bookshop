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
class Promotional_rules_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'rule_id';

	// ------------------------------------------------------------------------

	/**
	 * Promotional_rules_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('promotional_rules');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PROMOTIONAL_RULES);

		if (!$this->db->insert(TBL_PROMOTIONAL_RULES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//now add promotional items
		$data['rule_id'] = $this->db->insert_id();
		$this->insert_promo_items($data);

		$row = array(
			'id'       => $data['rule_id'],
			'data'     => $vars,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_promotional_items($id = '')
	{
		if (!$q = $this->db->where($this->id, valid_id($id))->get(TBL_PROMOTIONAL_ITEMS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param bool $public
	 * @param bool $get_cache
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = 1, $public = FALSE, $get_cache = TRUE)
	{
		$sql = 'SELECT p.*, n.*, g.product_name as item_name, h.product_name,
                 DATE_FORMAT(p.start_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS start_date_formatted,
                 DATE_FORMAT(p.end_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS end_date_formatted ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_PROMOTIONAL_RULES) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_PROMOTIONAL_RULES) . ' p
                            WHERE p.' . $this->id . '  > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_PROMOTIONAL_RULES) . ' p
					  LEFT JOIN ' . $this->db->dbprefix(TBL_PROMOTIONAL_ITEMS) . ' n
                        ON p.' . $this->id . ' = n. ' . $this->id . '
                      LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' g
                        ON (p.item_id = g.product_id
                        AND g.language_id = \'' . (int)$lang_id . '\')
                     LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' h
                        ON (n.product_id = h.product_id
                        AND g.language_id = \'' . (int)$lang_id . '\')
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
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_PROMOTIONAL_RULES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, n.product_name as item_name
                 FROM ' . $this->db->dbprefix(TBL_PROMOTIONAL_RULES) . ' p 
                 LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' n 
                    ON p.item_id = n.product_id
                    AND language_id = \'' . (int)$lang_id . '\'';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_PROMOTIONAL_RULES), $options['query']);

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
				'total'          => $this->dbv->get_table_totals($options, TBL_PROMOTIONAL_RULES),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return bool
	 */
	public function get_rules($type = '')
	{
		$sql = 'SELECT *,
                    start_date AS sql_start,
                    end_date AS sql_end,
                    DATE_FORMAT(start_date, \'' . $this->config->item('sql_date_format') . '\')
                        AS start_date,
                    DATE_FORMAT(end_date, \'' . $this->config->item('sql_date_format') . '\')
                        AS end_date
                FROM ' . $this->db->dbprefix(TBL_PROMOTIONAL_RULES) . '
                    WHERE status = \'1\'
                        AND rule = \'' . $type . '\'
                        AND start_date < NOW() AND end_date > NOW()';

		$sql .= '  ORDER BY sort_order ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
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

			$this->dbv->update(TBL_PROMOTIONAL_RULES, $this->id, $vars);
		}

		$this->dbv->db_sort_order(TBL_PROMOTIONAL_RULES, $this->id, 'sort_order');

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
	 * @return bool|false|string
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PROMOTIONAL_RULES);

		if (!$this->db->where($this->id, $vars[ $this->id ])->update(TBL_PROMOTIONAL_RULES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//update promotional items
		$this->update_promo_items($data);

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
		$required = $this->config->item('promotion_rules', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_PROMOTIONAL_RULES);

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

					$rule .= '|in_list[' . implode(',', config_option('promo_rules')) . ']';

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
					
					break;

				case 'operator':

					$rule .= '|in_list[' . implode(',', config_option('rule_operator')) . ']';

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

				case 'action':

					$rule .= '|in_list[' . implode(',', config_option('promo_rule_actions')) . ']';

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

					break;

				default:

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

					break;
			}
		}

		//set the promo data
		switch ($data['action'])
		{
			case 'special_offer':

				$rule = 'trim|required|integer';

				$this->form_validation->set_rules('promo_amount', 'lang:promo_amount' , $rule);
				$this->form_validation->set_rules('product_id', 'lang:free_product' , $rule);

				break;

			case 'quantity_discount':

				$this->form_validation->set_rules('promo_amount', 'lang:promo_amount' , 'trim|required|numeric');
				$this->form_validation->set_rules('discount_type', 'lang:free_product' , 'in_list[' . implode(',', config_option('flat_percent')) . ']');

				break;
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

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	protected function insert_promo_items($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PROMOTIONAL_ITEMS);

		if (!$this->db->insert(TBL_PROMOTIONAL_ITEMS, $vars))
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
	 * @param array $data
	 * @return bool
	 */
	protected function update_promo_items($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PROMOTIONAL_ITEMS);

		if (!$this->db->where($this->id, $vars[ $this->id ])->update(TBL_PROMOTIONAL_ITEMS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}
}

/* End of file Promotional_rules_model.php */
/* Location: ./application/models/Promotional_rules_model.php */