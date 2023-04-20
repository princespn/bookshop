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
class Members_credits_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'mcr_id';

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function add_credit($data = array())
	{
		$data = $this->dbv->clean(format_member_credit($data), TBL_MEMBERS_CREDITS);

		//run the db query
		if (!$this->db->insert(TBL_MEMBERS_CREDITS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_inserted_successfully'),
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array|bool
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_MEMBERS_CREDITS, TRUE);

		if (!$this->db->insert(TBL_MEMBERS_CREDITS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_added_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function delete($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete(TBL_MEMBERS_CREDITS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row['msg_text'] = lang('record_deleted_successfully');

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_details($id = '')
	{
		$sql = 'SELECT *,
					DATE_FORMAT(p.date, \'' . $this->config->item('sql_date_format') . '\')
                    AS date,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_MEMBERS_CREDITS) . ' p
                        WHERE p.' . $this->id . ' < ' . (int)$id . '
                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                        AS prev,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_MEMBERS_CREDITS) . ' p
                        WHERE p.' . $this->id . ' > ' . (int)$id . '
                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                        AS next
                    FROM ' . $this->db->dbprefix(TBL_MEMBERS_CREDITS) . ' p
                     LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' b
                            ON p.member_id = b.member_id
                    WHERE p.' . $this->id . '= ' . (int)$id . '';

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
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_MEMBERS_CREDITS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, 
					c.fname,
					c.lname,
					c.username
				FROM ' . $this->db->dbprefix(TBL_MEMBERS_CREDITS) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c
                    ON p.member_id = c.member_id';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_MEMBERS_CREDITS), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
					LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$cache = __METHOD__ . md5($sql);

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'  => $q->result_array(),
					'total'   => $this->dbv->get_table_totals($options, TBL_MEMBERS_CREDITS),
					'success' => TRUE,
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);

	}

	// ------------------------------------------------------------------------

	public function get_user_credits($id = '', $limit = FALSE)
	{
		$this->db->where('member_id', (int)$id);
		$this->db->where('amount >', '0');
		if (!$q = $this->db->get(TBL_MEMBERS_CREDITS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $limit == TRUE ? $q->row_array() : $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update($id = '', $data = array())
	{
		if (!$this->db->where($this->id, $id)->update(TBL_MEMBERS_CREDITS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array(
			'id'       => $id,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'row'      => $data,
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		//get the list of fields required for this
		$required = $this->config->item('member_credits', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_MEMBERS_CREDITS);

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
			$rule .= generate_db_rule($f->type, $f->max_length);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}
		else
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
			);
		}

		return $row;
	}
}

/* End of file Members_credits_model.php */
/* Location: ./application/models/Members_credits_model.php */