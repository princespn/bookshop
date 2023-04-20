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
class Affiliate_groups_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'group_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param bool $none
	 * @return bool|string
	 */
	public function ajax_search($term = '', $none = FALSE)
	{
		$row = array();

		$this->db->like('aff_group_name', $term);
		$this->db->select('group_id, aff_group_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_AFFILIATE_GROUPS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		if ($none == TRUE)
		{
			array_push($row, array('group_id'       => '0',
			                       'aff_group_name' => lang('none')));
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * create a new group
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_AFFILIATE_GROUPS);

		//get tier
		$data['tier'] = $this->db->count_all_results(TBL_AFFILIATE_GROUPS) + 1;

		if (!$this->db->insert(TBL_AFFILIATE_GROUPS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data['group_id'] = $this->db->insert_id();

		$row = array(
			'data'     => $data,
			'msg_text' => lang('affiliate_group') . ' ' .lang('record_added_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|string
	 */
	public function delete($id = '')
	{
		if ($id != config_option('sts_affiliate_default_registration_group'))
		{
			//update member group IDs
			$this->dbv->reset_id(TBL_MEMBERS_AFFILIATE_GROUPS,
				$this->id,
				$id,
				config_option('sts_affiliate_default_registration_group')
			);

			//update product group IDs
			$this->dbv->reset_id(TBL_PRODUCTS,
				'affiliate_group',
				$id,
				config_option('sts_affiliate_default_registration_group')
			);

			//reset affiliate marketing tools
			$this->aff->reset_module_group_id($id);

			//update product specific groups
			if (!$this->db->where('group_id', $id)->delete(TBL_PRODUCTS_TO_AFF_GROUPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if (!$this->db->where($this->id, $id)->delete(TBL_AFFILIATE_GROUPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array('success'  => TRUE,
			             'data'     => $id,
			             'msg_text' => lang('record_deleted_successfully'));

			//order the tier groups numerically
			$this->dbv->db_sort_order(TBL_AFFILIATE_GROUPS, 'group_id', 'tier');
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => lang('could_not_delete_default_group'),
			);
		}

		return empty($row) ? FALSE : sc($row);

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $group_id
	 * @param string $product_id
	 * @return bool
	 */
	public function delete_product_affiliate_group($group_id = '', $product_id = '')
	{
		if (!$this->db->delete(TBL_PRODUCTS_TO_AFF_GROUPS, array('group_id'   => $group_id,
		                                                         'product_id' => $product_id))
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return array|bool
	 */
	public function get_affiliate_groups($form = TRUE)
	{
		$cache = __METHOD__;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->order_by('tier', 'ASC')->get(TBL_AFFILIATE_GROUPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $form == TRUE ? format_array($q->result_array(), 'group_id', 'aff_group_name') : $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get details
	 *
	 * This returns the database details of each affiliate group by ID
	 *
	 * @param string $id
	 *
	 * @return bool|string
	 */
	public function get_details($id = '')
	{
		$sql = 'SELECT ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . '.*,
					(SELECT ' . $this->id . '
						FROM ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . '
						WHERE ' . $this->id . ' < ' . (int)$id . '
						ORDER BY `' . $this->id . '`
						DESC LIMIT 1)
					AS prev,
					(SELECT ' . $this->id . '
						FROM ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . '
						WHERE ' . $this->id . ' > ' . (int)$id . '
						ORDER BY `' . $this->id . '`
						ASC LIMIT 1)
					AS next
				FROM ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . '
				WHERE ' . $this->id . '= ' . (int)$id;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * get affiliate groups
	 *
	 * @param string $options
	 *
	 * @return bool|string
	 */
	public function get_rows($options = '')
	{
		//set the cache file
		$cache = __METHOD__ . $options['md5'];
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$sort = $this->config->item(TBL_AFFILIATE_GROUPS, 'db_sort_order');

			$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
			$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

			$sql = 'SELECT *';

			if (!$this->config->item('disable_sql_category_count'))
			{
				$sql .= ', (SELECT COUNT(member_id)
								FROM ' . $this->db->dbprefix(TBL_MEMBERS_AFFILIATE_GROUPS) . '
                                WHERE p.group_id = ' . $this->db->dbprefix(TBL_MEMBERS_AFFILIATE_GROUPS) . '.group_id)
                            AS total';
			}

			$sql .= ' FROM ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . ' p';

			if (!empty($options['query']))
			{
				$this->dbv->validate_columns(array(TBL_AFFILIATE_GROUPS), $options['query']);

				$sql .= $options['where_string'];
			}

			$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
						LIMIT ' . $options['offset'] . ', ' . $options['limit'];

			$query = $this->db->query($sql);

			if ($query->num_rows() > 0)
			{
				$row = array(
					'values'  => $query->result_array(),
					'total'   => $this->get_table_totals($options),
					'success' => TRUE,
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * generate total number of rows for the table
	 *
	 * @param string $options
	 *
	 * @return mixed
	 */
	public function get_table_totals($options = '')
	{
		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(TBL_AFFILIATE_GROUPS, $options['query']);

			foreach ($options['query'] as $k => $v)
			{
				if ($k == 'order' OR $k == 'column')
				{
					continue;
				}
				$this->db->where($k, $v);
			}
		}

		return $this->db->count_all_results(TBL_AFFILIATE_GROUPS);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $group_id
	 * @param bool $one_row
	 * @return bool|string
	 */
	public function get_product_affiliate_groups($id = '', $group_id = '', $one_row = FALSE)
	{

		$sql = 'SELECT p.*,
                      b.aff_group_name,
                      b.commission_type
                     FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_AFF_GROUPS) . ' p
                     LEFT JOIN ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . ' b
						    ON p.group_id = b.group_id
					 WHERE p.product_id = \'' . (int)$id . '\'';

		if (!empty($group_id))
		{
			$sql .= ' AND p.group_id = \'' . (int)$group_id . '\'';
		}

		$sql .= ' ORDER BY b.aff_group_name ASC';

		//set the cache file
		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $one_row == TRUE ? $q->row_array() : $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $group_id
	 * @param string $product_id
	 * @return bool
	 */
	public function insert_product_affiliate_group($group_id = '', $product_id = '')
	{
		$vars = array('product_id' => $product_id,
		              'group_id'   => $group_id,
		);

		if (!$this->db->insert(TBL_PRODUCTS_TO_AFF_GROUPS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update affiliate group amounts
	 *
	 * Update all affiliate group commission amounts en masse instead of
	 * doing it on a per affiliate group basis
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function update_groups($data = array())
	{
		foreach ($data['groups'] as $k => $v)
		{
			$vars = array('tier'            => $v['tier'],
			              'commission_type' => $v['commission_type']);

			foreach ($v['commission_amounts'] as $a => $b)
			{
				$vars['commission_level_' . $a] = $b;
			}

			if (!$this->db->where($this->id, $k)->update(TBL_AFFILIATE_GROUPS, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array('success'  => TRUE,
		                'data'     => $data['groups'],
		                'msg_text' => lang('mass_update_successful'))
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $group_id
	 * @return string
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
	 * @return string
	 */
	public function update_group_membership($id = '', $group_id = '')
	{
		if ($row = $this->dbv->get_record(TBL_MEMBERS_AFFILIATE_GROUPS, 'member_id', $id, TRUE))
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
	 * @return string
	 */
	public function insert_member_group($id = '', $group_id = '')
	{
		if (!$this->db->insert(TBL_MEMBERS_AFFILIATE_GROUPS, array('member_id' => $id, $this->id => $group_id)))
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
	 * @param string $id
	 * @param string $group_id
	 * @return string
	 */
	public function update_member_group($id = '', $group_id = '')
	{
		if (!$this->db->where('member_id', $id)->update(TBL_MEMBERS_AFFILIATE_GROUPS, array($this->id => $group_id)))
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
	 * @param string $id
	 * @param int $status
	 * @return bool
	 */
	public function set_product_commission_status($id = '', $status = 1)
	{
		if (!$this->db->where('product_id', $id)
			->update(TBL_PRODUCTS, array('enable_custom_commissions' => $status))
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * update a group
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function update($data = array())
	{
		$data = $this->dbv->clean($data, TBL_AFFILIATE_GROUPS);

		$this->db->where($this->id, $data['group_id']);
		if (!$this->db->update(TBL_AFFILIATE_GROUPS, $data))
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
	 * @param string $id
	 * @param array $data
	 * @return bool|string
	 */
	public function update_product_affiliate_groups($id = '', $data = array())
	{
		$a = $data['affiliate_groups']; //new groups
		$b = $this->get_product_affiliate_groups($id); //groups in the db

		$c = array();
		if (!empty($b))
		{
			foreach ($b as $v) //let's delete all the groups not in the current one
			{
				if (!in_array($v['group_id'], $a))
				{
					//delete the group from db
					$this->delete_product_affiliate_group($v['group_id'], $v['product_id']);
				}
				else
				{
					array_push($c, $v['group_id']);
				}
			}
		}

		//now add the new ones
		if (!empty($a))
		{
			foreach ($a as $v)
			{
				if (!in_array($v, $c))
				{
					//insert the group into db
					$this->insert_product_affiliate_group($v, $id);
				}
			}
		}

		//set the option for the product if there are affiliate groups for it
		$status = !empty($a) ? '1' : '0';
		$this->set_product_commission_status($id, $status);

		$row = array(
			'data'     => $id,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update user group
	 *
	 * This is used for updating the user's affiliate group membership
	 * when performance or commission rules are run.
	 *
	 * @param string $member_id
	 * @param string $group_id
	 *
	 * @return bool
	 */
	public function update_user_group($member_id = '', $group_id = '')
	{
		$this->db->where('member_id', $member_id);
		if (!$this->db->update(TBL_MEMBERS_AFFILIATE_GROUPS, array('group_id' => $group_id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate input data
	 *
	 * This validates all input data for creating an affiliate group
	 *
	 * @param string $func
	 * @param array $data
	 * @param bool $return
	 *
	 * @return array|bool
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('aff_group_name', 'lang:group_name', 'trim|required|min_length[2]|max_length[50]');
		$this->form_validation->set_rules('aff_group_description', 'lang:group_description', 'trim|max_length[255]');
		$this->form_validation->set_rules('commission_type', 'lang:commission_type', 'trim|required');

		for ($i = 1; $i <= config_option('sts_affiliate_commission_levels'); $i++)
		{
			$this->form_validation->set_rules('commission_level_' . $i, 'lang:commission_level_' . $i, 'trim|required|numeric');
		}

		if ($this->form_validation->run())
		{
			return sc(array('success' => TRUE,
			                'data'    => $this->dbv->validated($data),
			));
		}

		return FALSE;
	}
}

/* End of file Affiliate_groups_model.php */
/* Location: ./application/models/Affiliate_groups_model.php */