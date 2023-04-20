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
class Blog_groups_model extends CI_Model
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

		if (!$q = $this->db->get(TBL_BLOG_GROUPS))
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
		if (!$q = $this->db->insert(TBL_BLOG_GROUPS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data['group_id'] = $this->db->insert_id();

		return sc(array('success'  => TRUE,
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
		if ($id != config_option('sts_members_default_blog_group'))
		{
			//update group IDs
			$this->dbv->reset_id(TBL_MEMBERS_BLOG_GROUPS,
				$this->id,
				$id,
				config_option('sts_members_default_blog_group')
			);

			if (!$this->db->where($this->id, $id)->delete(TBL_BLOG_GROUPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array('success'  => TRUE,
			             'data'     => $id,
			             'msg_text' => lang('system_updated_successfully'));
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return bool|false|string
	 */
	public function get_blog_groups($form = TRUE)
	{
		$cache = __METHOD__;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->order_by('sort_order', 'ASC')->get(TBL_BLOG_GROUPS))
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

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_BLOG_GROUPS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(member_id)
								FROM ' . $this->db->dbprefix(TBL_MEMBERS_BLOG_GROUPS) . '
                                WHERE p.group_id = ' . $this->db->dbprefix(TBL_MEMBERS_BLOG_GROUPS) . '.group_id)
                            AS total';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_BLOG_GROUPS) . ' p ';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_BLOG_GROUPS), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
					LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_BLOG_GROUPS),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $group_id
	 * @return false|string
	 */
	public function insert_member_group($id = '', $group_id = '')
	{
		if (!$this->db->insert(TBL_MEMBERS_BLOG_GROUPS, array('member_id' => $id, $this->id => $group_id)))
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
		if ($row = $this->dbv->get_record(TBL_MEMBERS_BLOG_GROUPS, 'member_id', $id, TRUE))
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
		if (!$this->db->where('member_id', $id)->update(TBL_MEMBERS_BLOG_GROUPS, array($this->id => $group_id)))
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
				$v = array('group_name'        => valid_id($v['group_name'], TRUE),
				           'group_description' => valid_id($v['group_description'], TRUE),
				);

				if (!$this->db->where('group_id', $k)->update(TBL_BLOG_GROUPS, $v))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array('success'  => TRUE,
			             'data'     => $data,
			             'msg_text' => lang('mass_update_successful'),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file blog_groups_model.php */
/* Location: ./application/models/blog_groups_model.php */