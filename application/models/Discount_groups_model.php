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
class Discount_groups_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'group_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param bool $none
	 * @return bool|false|string
	 */
	public function ajax_search($term = '', $none = FALSE)
	{
		$row = array();

		$this->db->like('group_name', $term);
		$this->db->select('group_id, group_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_DISCOUNT_GROUPS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}
		if ($none == TRUE)
		{
			array_push($row, array('group_id'   => '0',
			                       'group_name' => lang('none')));
		}
		
		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_DISCOUNT_GROUPS);

		if (!$q = $this->db->insert(TBL_DISCOUNT_GROUPS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data[ 'group_id' ] = $this->db->insert_id();

		return sc(array( 'success'  => TRUE,
		                 'data'     => $data,
		                 'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function delete($id = '')
	{
		if ($id != config_option('sts_members_default_discount_group'))
		{
			//update member group IDs
			$this->dbv->reset_id(TBL_MEMBERS_DISCOUNT_GROUPS,
				$this->id,
				$id,
				config_option('sts_members_default_discount_group')
			);

			//update product group IDs
			$this->dbv->reset_id(TBL_PRODUCTS,
				'discount_group',
				$id,
				config_option('sts_members_default_discount_group')
			);

			//update product specific groups
			if (!$this->db->where('group_id', $id)->delete(TBL_PRODUCTS_TO_DISC_GROUPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if (!$this->db->where($this->id, $id)->delete(TBL_DISCOUNT_GROUPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array( 'success'  => TRUE,
			              'data'     => $id,
			              'msg_text' => lang('record_deleted_successfully') );
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
		$sort = $this->config->item(TBL_DISCOUNT_GROUPS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT *';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(member_id)
								FROM ' . $this->db->dbprefix(TBL_MEMBERS_DISCOUNT_GROUPS) . '
                                WHERE p.group_id = ' . $this->db->dbprefix(TBL_MEMBERS_DISCOUNT_GROUPS) . '.group_id)
                            AS total';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_DISCOUNT_GROUPS) . ' p ';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_DISCOUNT_GROUPS ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
					LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		$query = $this->db->query($sql);
		

		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'         => $query->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_DISCOUNT_GROUPS),
				'success'        => TRUE,
			);

			return sc($row);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_details($id = '')
	{
		$sql = 'SELECT ' . $this->db->dbprefix(TBL_DISCOUNT_GROUPS) . '.*,
					(SELECT ' . $this->id . '
						FROM ' . $this->db->dbprefix(TBL_DISCOUNT_GROUPS) . '
						WHERE ' . $this->id . ' < ' . (int)$id . '
						ORDER BY `' . $this->id . '`
						DESC LIMIT 1)
					AS prev,
					(SELECT ' . $this->id . '
						FROM ' . $this->db->dbprefix(TBL_DISCOUNT_GROUPS) . '
						WHERE ' . $this->id . ' > ' . (int)$id . '
						ORDER BY `' . $this->id . '`
						ASC LIMIT 1)
					AS next
				FROM ' . $this->db->dbprefix(TBL_DISCOUNT_GROUPS) . '
				WHERE ' . $this->id . '= ' . (int)$id;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return array|bool|false
	 */
	public function get_discount_groups($form = TRUE)
	{
		$cache = __METHOD__;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->order_by('sort_order', 'ASC')->get(TBL_DISCOUNT_GROUPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $form == TRUE ? format_array($q->result_array(), 'group_id', 'group_name') : $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $group_id
	 * @return false|string
	 */
	public function insert_member_group($id = '', $group_id = '')
	{
		if (!$this->db->insert(TBL_MEMBERS_DISCOUNT_GROUPS, array('member_id' => $id, $this->id => $group_id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('record_inserted_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $group_id
	 * @return false|string
	 */
	public function mass_update($data = array(), $group_id = '')
	{
		if (!empty($data))
		{
			foreach ($data as $v)
			{
				$this->update_group_membership($v, valid_id($group_id));
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
	 * @param string $id
	 * @param string $group_id
	 * @return false|string
	 */
	public function update_group_membership($id = '', $group_id = '')
	{
		if ($row = $this->dbv->get_record(TBL_MEMBERS_DISCOUNT_GROUPS, 'member_id', $id, TRUE))
		{
			//update it
			$this->update_member_group($id, $group_id);
		}
		else
		{
			//insert it
			$this->insert_member_group($id, $group_id);
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $group_id
	 * @return false|string
	 */
	public function update_member_group($id = '', $group_id = '')
	{
		if (!$this->db->where('member_id', $id)->update(TBL_MEMBERS_DISCOUNT_GROUPS, array($this->id => $group_id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('record_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update_groups($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				$v = array( 'group_name'        => valid_id($v[ 'group_name' ], TRUE),
				            'group_amount'      => $v[ 'group_amount' ],
				            'discount_type'     => $v[ 'discount_type' ]
				);

				if (!$this->db->where('group_id', $k)->update(TBL_DISCOUNT_GROUPS, $v))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array( 'success'  => TRUE,
			              'data'     => $data,
			              'msg_text' => lang('mass_update_successful'),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array|bool
	 */
	public function update($data = array())
	{
		$data = $this->dbv->clean($data, TBL_DISCOUNT_GROUPS);

		$this->db->where($this->id, valid_id($data[ 'group_id' ]));
		if (!$this->db->update(TBL_DISCOUNT_GROUPS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('discount_groups', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_DISCOUNT_GROUPS);

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
				case 'group_amount':

					$rule .= '|numeric';

					break;

				case 'sort_order':

					$rule .= '|integer';

					break;
			}

			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		if ($this->form_validation->run())
		{
			return sc(array( 'success' => TRUE,
			                 'data'    => $this->dbv->validated($data, FALSE),
			));
		}

		return FALSE;
	}
}

/* End of file Discount_groups_model.php */
/* Location: ./application/models/Discount_groups_model.php */