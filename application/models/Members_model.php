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
class Members_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'member_id';

	// ------------------------------------------------------------------------

	/**
	 * Members_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('members');
	}

	// ------------------------------------------------------------------------

	/**
	 * Add member address
	 *
	 * Add a new address for a specific member
	 *
	 * @param $data
	 *
	 * @return bool|string
	 */
	public function add_address($data)
	{
		//clean the input data first
		if (!empty($data['address_1']))
		{
			$data = $this->dbv->clean($data, TBL_MEMBERS_ADDRESSES);

			//run the db query
			if (!$this->db->insert(TBL_MEMBERS_ADDRESSES, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $this->db->insert_id(),
				'msg_text' => lang('record_inserted_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @param string $col
	 * @return string
	 */
	public function check_sponsor($str = '', $col = 'member_id')
	{
		$this->db->where('username', strtolower(trim($str)));
		$this->db->or_where('primary_email', strtolower(trim($str)));
		$this->db->or_where('member_id', (int)$str);
		$this->db->limit(1);

		if (!$q = $this->db->get(TBL_MEMBERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? '0' : $row[$col];
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool
	 */
	public function confirm_registration($str = '')
	{
		if (!$q = $this->db->where('confirm_id', xss_clean($str))->get(TBL_MEMBERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			$vars = array('confirm_id' => '', 'email_confirmed' => '1');

			$vars['status'] = config_enabled('sts_affiliate_admin_approval_required') ? '0' : '1';

			if (!$q = $this->db->where($this->id, $row['member_id'])->update(TBL_MEMBERS, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function create($data = array(), $type = '')
	{
		//format required fields like username and password
		$data = format_member_data($data);

		$fields = array();
		$types = array('account', 'billing', 'shipping', 'payment');

		foreach ($types as $v)
		{
			$fields[$v] = format_addresses($v, $data);
		}

		//add the account data first
		$mem = $this->add_member($fields['account'], $type);

		//set the member id
		$data['member_id'] = $mem['member_id'];

		//add addresses
		$address_types = array('billing', 'shipping', 'payment');

		foreach ($address_types as $v)
		{
			if (!empty($fields[$v]))
			{
				$fields[$v]['member_id'] = $data['member_id'];
				$fields[$v][$v . '_default'] = '1';

				if ($v == 'billing')
				{
					if (!empty($fields[$v]['use_different_billing']))
					{
						$fields[$v][$v . '_default'] = '1';
					}
					elseif (empty($fields[$v][$v . '_default']))
					{
						$fields[$v][$v . '_default'] = '0';
					}
				}

				//set default names
				if (empty($fields[$v]['fname']))
				{
					//set default names
					$fields[$v]['fname'] = $fields['account']['fname'];
					$fields[$v]['lname'] = $fields['account']['lname'];
				}

				$this->add_address($fields[$v]);
			}
		}

		$data = $this->add_member_groups($data);

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return bool|false|string
	 */
	public function init_new_member($data = array(), $type = '')
	{
		//add the account data first
		$mem = $this->add_member($data, $type);

		$data['member_id'] = $mem['member_id'];

		$data = $this->add_member_groups($data);

		$row = array(
			'id'       => $data['member_id'],
			'data'     => $data,
			'msg_text' => lang('record_inserted_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function add_member_groups($data = array())
	{
		//set sponsor if any
		$data['sponsor_id'] = !empty($data['sponsor_id']) ? (int)$data['sponsor_id'] : '0';
		$data['original_sponsor_id'] = !empty($data['original_sponsor_id']) ? (int)$data['original_sponsor_id'] : $data['sponsor_id'];

		$this->add_sponsor($data);

		//add the members password
		$data['password'] = !empty($data['password']) ? $data['password'] : random_string('alnum', DEFAULT_MEMBER_PASSWORD_LENGTH);

		$this->add_member_password($data);

		//add affiliate group
		$this->add_group(TBL_MEMBERS_AFFILIATE_GROUPS, array('member_id' => (int)$data['member_id'],
		                                                     'group_id'  => config_option('sts_affiliate_default_registration_group'),
		));

		//add discount group
		$this->add_group(TBL_MEMBERS_DISCOUNT_GROUPS, array('member_id' => (int)$data['member_id'],
		                                                    'group_id'  => config_option('sts_members_default_discount_group'),
		));

		//add contact group
		$this->add_group(TBL_MEMBERS_BLOG_GROUPS, array('member_id' => (int)$data['member_id'],
		                                                'group_id'  => config_option('sts_members_default_blog_group'),
		));

		//permissions
		$this->add_permissions($data);

		//add profile
		$this->add_profile($data);

		//addd member alerts
		$this->add_alerts($data);

		//add custom fields
		$this->add_member_custom_fields($data);

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_alerts($data = array())
	{
		if (!$q = $this->db->where($this->id, $data[$this->id])->get(TBL_MEMBERS_ALERTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() < 1)
		{
			$data = $this->dbv->clean($data, TBL_MEMBERS_ALERTS);

			//run the db query
			if (!$this->db->insert(TBL_MEMBERS_ALERTS, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $this->db->insert_id(),
				'msg_text' => lang('record_inserted_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return bool|false|string
	 */
	public function add_member($data = array(), $type = 'checkout')
	{
		//clean the input data
		$sdata = $this->dbv->clean($data, TBL_MEMBERS, FALSE, 'member_id');

		//check the type of user to add
		switch ($type)
		{
			case 'checkout':

				$sdata['status'] = empty($sdata['status']) ? '1' : $sdata['status'];
				$sdata['is_customer'] = empty($sdata['is_customer']) ? '1' : $sdata['is_customer'];

				break;

			case 'affiliate':

				$sdata['is_affiliate'] = empty($sdata['is_affiliate']) ? '1' : $sdata['is_affiliate'];

				break;
		}

		$sdata['is_affiliate'] = $this->lc->check_aff(is_var($sdata, 'is_affiliate'));

		//run the db query
		if (!$this->db->insert(TBL_MEMBERS, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'member_id' => $this->db->insert_id(),
			'msg_text'  => lang('record_inserted_successfully'),
			'success'   => TRUE,
			'data'      => $sdata,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_member_password($data = array())
	{
		if (!$q = $this->db->where($this->id, $data[$this->id])->get(TBL_MEMBERS_PASSWORDS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() < 1)
		{
			$data = $this->dbv->clean($data, TBL_MEMBERS_PASSWORDS);

			//run the db query
			if (!$this->db->insert(TBL_MEMBERS_PASSWORDS, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $this->db->insert_id(),
				'msg_text' => lang('record_inserted_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_group($table = '', $data = array())
	{
		$data = $this->dbv->clean($data, $table);

		if (!$q = $this->db->where($this->id, $data[$this->id])->get($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() < 1)
		{
			$data = $this->dbv->clean($data, $table);

			//run the db query
			if (!$this->db->insert($table, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $this->db->insert_id(),
				'msg_text' => lang('record_inserted_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_permissions($data = array())
	{

		if (!$q = $this->db->where($this->id, $data[$this->id])->get(TBL_MEMBERS_PERMISSIONS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() < 1)
		{
			$data = $this->dbv->clean($data, TBL_MEMBERS_PERMISSIONS);

			//run the db query
			if (!$this->db->insert(TBL_MEMBERS_PERMISSIONS, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $this->db->insert_id(),
				'msg_text' => lang('record_inserted_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return bool|false|string
	 */
	public function add_social_user($data = array(), $type = '')
	{
		$vars = format_social_data($data, $type);
		$row = $this->init_new_member(generic_user($vars), 'affiliate');

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_profile($data = array())
	{
		if (!$q = $this->db->where($this->id, $data[$this->id])->get(TBL_MEMBERS_PROFILES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() < 1)
		{
			$data = $this->dbv->clean($data, TBL_MEMBERS_PROFILES);

			//run the db query
			if (!$this->db->insert(TBL_MEMBERS_PROFILES, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $this->db->insert_id(),
				'msg_text' => lang('record_inserted_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function add_member_custom_fields($data = array())
	{
		//get the custom fields
		$fields = $this->form->get_member_custom_fields($data['member_id']);

		if (!empty($fields))
		{
			foreach ($fields as $v)
			{
				if (isset($data[$v['form_field']]))
				{
					$vars = array('member_id'       => $data['member_id'],
					              'custom_field_id' => $v['custom_field_id'],
					              'data'            => $data[$v['form_field']],
					);

					//run the db query
					if (!$this->db->insert(TBL_MEMBERS_TO_CUSTOM_FIELDS, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_sponsor($data = array())
	{
		if (!$q = $this->db->where($this->id, $data[$this->id])->get(TBL_MEMBERS_SPONSORS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() < 1)
		{
			$data = $this->dbv->clean($data, TBL_MEMBERS_SPONSORS);

			//run the db query
			if (!$this->db->insert(TBL_MEMBERS_SPONSORS, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $this->db->insert_id(),
				'msg_text' => lang('record_inserted_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Search for specific fields
	 *
	 * Search for specific fields via ajax.
	 * Used for select2 dropdowns
	 *
	 * @param string $field
	 * @param string $term
	 * @param int $limit
	 *
	 * @return array
	 */
	public function ajax_search($field = 'username', $term = '', $limit = TPL_AJAX_LIMIT, $none = TRUE)
	{
		//set the default array when nothing is set
		$select = array();
		if ($none == TRUE)
		{
			$select[] = array('member_id' => '0',
			                  'username'  => 'none');
		}

		//check what fields to search
		if ($field == 'full_name')
		{
			$this->db->like('fname', $term);
			$this->db->or_like('lname', $term);
			$this->db->or_like('username', $term);
			$this->db->or_like('primary_email', $term);

			$this->db->select('member_id, fname, lname, username,primary_email');

			$this->db->limit($limit);

			if (!$q = $this->db->get(TBL_MEMBERS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				foreach ($q->result_array() as $row)
				{
					$a = array(
						'member_id' => $row['member_id'],
						'username'  => $row['fname'] . ' ' . $row['lname'] . ' - ' . $row['primary_email'],
					);

					array_push($select, $a);
				}
			}

			$rows = $select;
		}
		else
		{
			$this->db->like($field, $term);

			$this->db->select($this->id . ', ' . $field);

			$this->db->limit($limit);

			if (!$q = $this->db->get(TBL_MEMBERS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$e = $q->result_array();

				$rows = array_merge($select, $e);
			}
			else
			{
				$rows = $select;
			}
		}

		return $rows;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check email
	 *
	 * Function for checking if an email address exists
	 * in the member table
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function check_email($id = '')
	{
		//check for blocked free email accounts
		if (!check_free_email_accounts($id))
		{
			return FALSE;
		}

		return $this->check_field('primary_email', $id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Check username
	 *
	 * Function for checking if a username exists
	 * in the member table
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function check_username($id = '')
	{
		if ($this->check_route_names($id))
		{
			return $this->check_field('username', $id);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function check_route_names($id = '')
	{
		//check to make sure valid controller names are not used as usernames
		if (in_array($id, config_option('restricted_usernames'))) return FALSE;

		$this->load->helper('directory');

		$files = directory_map('./application/controllers');
		asort($files);

		$options = array();
		foreach ($files as $f)
		{
			if (is_string($f))
			{
				$f = strtolower(str_replace('.php', '', $f));

				if ($id == $f)
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $field
	 * @return bool|mixed|string
	 */
	public function checkout_get_member($field = '')
	{
		if (sess('user_logged_in') && sess('member_id'))
		{
			$member_id = sess('member_id');

			//lets add new addresses if they are available
			$this->checkout_add_address(sess('checkout_customer_data'));
		}
		elseif (sess('checkout_customer_data', 'register') == '1')
		{
			$member = $this->create(sess('checkout_customer_data'), 'checkout');

			//reset the session data
			$this->session->set_userdata('checkout_customer_data', $member);

			$member_id = $member['member_id'];
		}

		return !empty($member_id) ? $field == 'member_id' ? $member_id : $this->get_details($member_id) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function checkout_add_address($data = array())
	{
		$fields = array();
		$types = array('billing', 'shipping', 'payment');

		foreach ($types as $v)
		{
			$fields[$v] = format_addresses($v, $data);
		}

		foreach ($types as $v)
		{
			if (!empty($fields[$v]) && !empty($data['add_' . $v . '_address']))
			{
				$fields[$v]['member_id'] = $data['member_id'];
				$fields[$v][$v . '_default'] = 1;

				//set default names
				if (empty($fields[$v]['fname']))
				{
					//set default names
					$fields[$v]['fname'] = $data['fname'];
					$fields[$v]['lname'] = $data['lname'];
				}

				$this->add_address($fields[$v]);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Check field
	 *
	 * Check if a specified value exists in the member table
	 *
	 * @param string $field
	 * @param string $id
	 *
	 * @return bool
	 */
	public function check_field($field = '', $id = '')
	{
		$member_id = !$this->input->post('member_id') ? '' : (int)$this->input->post('member_id');

		if (!$this->dbv->validate_field(TBL_MEMBERS, $field, $id, 'member_id', $member_id))
		{
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		$tables = array(TBL_MEMBERS_EMAIL_MAILING_LIST, TBL_MEMBERS);

		foreach ($tables as $v)
		{
			if (!$this->db->where($this->id, $id)->delete($v))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$this->dbv->reset_id(TBL_MEMBERS_SPONSORS, 'sponsor_id', $id);

		$row['success'] = TRUE;
		$row['msg_text'] = lang('record_deleted_successfully');

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete address
	 *
	 * Delete a member address from the db
	 *
	 * @param string $id
	 *
	 * @return bool|string
	 */
	public function delete_address($id = '')
	{
		if (!$this->db->where('id', $id)->delete(TBL_MEMBERS_ADDRESSES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $id,
			'msg_text' => lang('record_deleted_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function get_user_birthdays()
	{
		$sql = 'SELECT member_id 
				FROM ' . $this->db->dbprefix(TBL_MEMBERS) . '
				WHERE birthdate = CURRENT_DATE()';

		//run the db query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $list_id
	 * @return bool
	 */
	public function delete_member_lists($id = '', $list_id = '')
	{
		$this->db->where($this->id, valid_id($id));

		if (!empty($list_id))
		{
			$this->db->where('list_id', (int)$list_id);
		}

		if (!$this->db->delete(TBL_MEMBERS_EMAIL_MAILING_LIST))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Member Access Data
	 *
	 * Gets the member's current password from
	 * the members_passwords table
	 *
	 * @param string $id
	 *
	 * @return bool|string
	 */
	public function get_access_data($id = '')
	{
		if (!$q = $this->db->where('member_id', (int)$id)->get(TBL_MEMBERS_PASSWORDS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() == 1)
		{
			$row = $q->row_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get member rows
	 *
	 * Get the member rows from the members table
	 *
	 * @param string $options
	 * @param array $type
	 *
	 * @return bool|string
	 */
	public function get_rows($options = '', $type = array())
	{
		//set the sort order for this query
		$sort = $this->config->item(TBL_MEMBERS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		//set the default group
		$groups = TBL_MEMBERS_AFFILIATE_GROUPS;

		if (!empty($type['table']))
		{
			$groups = 'members_' . $type['table'] . '_groups';
		}

		//run the query
		$this->db->select(TBL_MEMBERS . '.*,' .
			$groups . '.*,' .
			TBL_MEMBERS_PROFILES . '.*,' .
			TBL_MEMBERS . '.member_id AS mid');

		//join the members groups
		$this->db->join($groups,
			TBL_MEMBERS . '.' . $this->id . '=' . $groups . '.' . $this->id,
			'left');

		//join the members_profile
		$this->db->join(TBL_MEMBERS_PROFILES,
			TBL_MEMBERS . '.' . $this->id . '=' . TBL_MEMBERS_PROFILES . '.' . $this->id,
			'left');

		//set any other query filters
		if (!empty($options['query']))
		{
			$this->dbv->validate_columns([TBL_MEMBERS], $options['query']);

			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$this->db->where($k, $v);
			}
		}

		//set the filtering group table, field, and id if any
		if (!empty($type['type_id']) && !empty($type['group_id']))
		{
			$this->db->where($type['type_id'], (int)$type['group_id']);
		}

		$this->db->order_by($options['sort_column'], $options['sort_order']);

		$this->db->group_by(TBL_MEMBERS . '.' . $this->id);

		//run the query
		if (!$q = $this->db->get(TBL_MEMBERS, $options['limit'], $options['offset']))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->get_table_totals($options, $type),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_member_group($type = '', $id = '')
	{
		if (!$q = $this->db->where($this->id, $id)->get($type))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get table totals
	 *
	 * Get the total rows per table for pagination purposes
	 *
	 * @param string $options
	 * @param array $type
	 *
	 * @return mixed
	 */
	public function get_table_totals($options = '', $type = array(), $search = FALSE)
	{
		//set the default group
		$groups = TBL_MEMBERS_AFFILIATE_GROUPS;

		if (!empty($type['table']))
		{
			$groups = 'members_' . $type['table'] . '_groups';
		}

		//run the sql query
		$this->db->select(TBL_MEMBERS . '.*');

		//join the members groups
		$this->db->join($groups,
			TBL_MEMBERS . '.' . $this->id . '=' . $groups . '.' . $this->id,
			'left');

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				if ($search == TRUE)
				{
					$columns = $this->db->list_fields(TBL_MEMBERS);

					foreach ($columns as $f)
					{
						$this->db->or_like($this->db->dbprefix(TBL_MEMBERS) . '.' . $f, $v);
					}
				}
				else
				{
					$this->db->where($k, $v);
				}

			}
		}

		if (!empty($type['type_id']) && !empty($type['group_id']))
		{
			$this->db->where($type['type_id'], (int)$type['group_id']);
		}

		return $this->db->count_all_results(TBL_MEMBERS);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get member notes
	 *
	 * Get all notes for each member in the member_notes table
	 *
	 * @param string $id
	 *
	 * @return bool|string
	 */
	public function get_member_notes($id = '')
	{
		if (!$q = $this->db->where($this->id, $id)->order_by('id', 'DESC')
			->limit(TPL_AJAX_LIMIT)->get(TBL_MEMBERS_NOTES)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get a specific address
	 *
	 * Get a specific member's address data
	 *
	 * @param string $id
	 * @param string $member_id
	 *
	 * @return bool|string
	 */
	public function get_member_address($id = '', $member_id = '')
	{
		//run the query and join the corresponding region and country fields
		$sql = 'SELECT *
				    FROM ' . $this->db->dbprefix(TBL_MEMBERS_ADDRESSES) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' a ON p.state= a.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' s ON p.country= s.country_id
                    WHERE p.id = ' . (int)$id . '';

		if (!empty($member_id))
		{
			$sql .= ' AND ' . $this->id . ' = \'' . (int)$member_id . '\'';
		}

		//run the db query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			//add the array of regions / states
			$row['regions_array'] = $this->regions->load_country_regions($row['country_id'], TRUE);

			//set the default country for dropdowns
			$row['country_array'] = array($row['country'] => $row['country_name']);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $col
	 * @param bool $address
	 * @return bool|false|string
	 */
	public function get_basic_member($id = '', $col = 'username', $address = FALSE)
	{
		$sql =  'SELECT p.*, r.*, ';
		if ($address == TRUE)
		{
			$sql .= ' a.*, t.*, s.*,';
		}

		$sql .= ' p.fname AS fname, p.lname AS lname
 					FROM ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' r
				        ON p.' . $this->id . '= r.' . $this->id . '';

		if ($address == TRUE)
		{
			$sql .= '  LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ADDRESSES) . ' a
				            ON p.' . $this->id . '= a.' . $this->id . '
				            AND billing_default = \'1\'
				        LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' t ON a.state= t.region_id
	                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' s ON a.country= s.country_id';
		}

		$sql .= '  WHERE p.' . $col . ' = \'' . $id . '\' 
					AND p.status = \'1\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get member details
	 *
	 * Get all details foreach member from the db
	 *
	 * @param string $id
	 * @param bool|FALSE $address
	 * @param bool|FALSE $public
	 *
	 * @return bool|string
	 */
	public function get_details($id = '', $address = FALSE, $public = FALSE)
	{
		//set the query ID
		$col = $public == FALSE ? $this->id : LOGIN_USERNAME_FIELD;

		$sql = 'SELECT p.*, r.*, g.*, y.*, f.*,
                    e.group_name AS disc_group_name,
                    e.group_amount AS disc_group_amount,
                    e.discount_type AS disc_type,
                    g.aff_group_name,
                    a.group_id AS affiliate_group,
                    d.group_id AS discount_group,
                    c.group_id AS blog_group,
                    t.group_name AS blog_group_name,
                    n.username AS sponsor_username,
                    n.fname AS sponsor_fname,
                    n.lname AS sponsor_lname,
                    n.primary_email AS sponsor_primary_email,
                    p.member_id AS member_id,
                    s.sponsor_id,
                    s.original_sponsor_id,
                    DATE_FORMAT(p.birthdate, \'' . $this->config->item('sql_date_format') . '\')
                        AS birthdate,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
				    FROM ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' r
				        ON p.' . $this->id . '= r.' . $this->id . '
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' s
                        ON p.' . $this->id . '= s.' . $this->id . '
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ALERTS) . ' y
                        ON p.member_id = y.member_id       
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PERMISSIONS) . ' f
                        ON p.member_id = f.member_id           
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_AFFILIATE_GROUPS) . ' a
                        ON p.' . $this->id . '= a.' . $this->id . '
                    LEFT JOIN ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . ' g
                        ON a.group_id = g.group_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_DISCOUNT_GROUPS) . ' d
                        ON p.' . $this->id . '= d.' . $this->id . '
                    LEFT JOIN ' . $this->db->dbprefix(TBL_DISCOUNT_GROUPS) . ' e
                        ON d.group_id = e.group_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_BLOG_GROUPS) . ' c
                        ON p.' . $this->id . '= c.' . $this->id . '
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_GROUPS) . ' t
                        ON c.group_id = t.group_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' n
                        ON s.sponsor_id = n.' . $this->id . '
                    WHERE p.' . $col . '= \'' . valid_id($id, $col) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.status = \'1\'';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() == 1)
		{
			$row = $q->row_array();

			//add the members subscribed lists if any
			$row['mailing_lists'] = $this->lists->get_member_lists($row['member_id']);

			//add addresses if any
			if ($address == TRUE)
			{
				$row['addresses'] = $this->get_member_addresses($row['member_id']);
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get member addresses
	 *
	 * Get all addresses for each member in the
	 * members_addresses table
	 *
	 * @param string $id
	 *
	 * @return bool|string
	 */
	public function get_member_addresses($id = '')
	{
		//run the query and join the corresponding region and country fields
		$sql = 'SELECT *
				    FROM ' . $this->db->dbprefix(TBL_MEMBERS_ADDRESSES) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' a ON p.state= a.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' s ON p.country= s.country_id
                    WHERE p.' . $this->id . '= ' . (int)$id . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_member_lists($id = '')
	{
		$this->db->where($this->id, valid_id($id));

		if (!$q = $this->db->order_by('list_id', 'ASC')->get(TBL_MEMBERS_EMAIL_MAILING_LIST))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function insert_member_lists($data = array())
	{
		if (!empty($data['mailing_lists']))
		{
			foreach ($data['mailing_lists'] as $v)
			{
				$this->insert_member_list($v, $data);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $list_id
	 * @param array $data
	 * @return bool
	 */
	public function insert_member_list($list_id = '', $data = array())
	{
		$sdata = format_mailing_list_member_data($list_id, $data['primary_email'], $data);

		if (!$this->db->insert(TBL_MEMBERS_EMAIL_MAILING_LIST, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 */
	public function login_checkout_user($id = '')
	{
		//login the user if it's not a guest
		if ($row = $this->mem->get_details($id, TRUE))
		{
			//get default addresses
			foreach ($row['addresses'] as $k => $v)
			{
				if ($v['billing_default'] == 1)
				{
					$row['member_address'] = $v;
				}
			}

			//remove unneeded fields
			unset($row['addresses']);

			//set the session data for the member
			$this->login->set_login_data($row, 'member');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return false|string
	 */
	public function mass_update($data = array(), $type = '')
	{
		if (!empty($data))
		{
			foreach ($data as $v)
			{
				switch ($type)
				{
					case 'active':
					case 'inactive':

						$status = $type == 'active' ? '1' : '0';

						$this->update_status($v, $status);

						if ($type == 'active')
						{
							$this->update_status($v, $status, 'email_confirmed');
						}

						break;

					case 'delete':

						$this->delete($v);

						break;

					case 'activate_affiliate':
					case 'deactivate_affiliate':

						$status = $type == 'activate_affiliate' ?  $this->lc->check_aff('1', 'admin') : '0';

						$this->update_status($v, $status, 'is_affiliate');

						break;
				}
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
	 * @param string $name
	 * @return int|string
	 */
	public function random_username($name = '')
	{
		$length = $this->config->item('sts_affiliate_min_username_length');
		$append = '';

		$check = FALSE;
		$i = 1;
		$next_id = $this->config->item('sts_affiliate_next_username_numeric_sequence');

		while ($check == FALSE)
		{
			switch ($this->config->item('sts_affiliate_random_username_type'))
			{
				case 'sequential_numeric':

					$name = $next_id + 1;

					if ($this->check_username($name))
					{
						$check = TRUE;
						//update next username sequence
						$this->set->update_db_settings(array('sts_affiliate_next_username_numeric_sequence' => $next_id));
					}

					$next_id += 1;

					break;

				case 'random_numeric':

					$name = random_string('nozero', $this->config->item('sts_affiliate_min_username_length'));

					if ($this->check_username($name))
					{
						$check = TRUE;
					}
					else
					{
						$name = random_string('nozero', $this->config->item('sts_affiliate_min_username_length'));
					}

					break;

				default:

					if (strlen($name) < $length)
					{
						$append = random_string('nozero', ($length - strlen($name)));
					}

					$name = strtolower($name . $append);

					if ($this->check_username($name))
					{
						$check = TRUE;
					}
					else
					{
						$name = substr($name, 0, $length) . $i;
					}

					$i++;

					break;
			}
		}

		return $name;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function random_email()
	{
		$check = FALSE;

		while ($check == FALSE)
		{
			$name = random_string('nozero', $this->config->item('sts_affiliate_min_username_length'));
			$email = 'user' . $name . '@' . config_item('base_domain');

			if ($this->check_email($email))
			{
				$check = TRUE;
			}
			else
			{
				$name = random_string('nozero', $this->config->item('sts_affiliate_min_username_length'));
				$email = 'user' . $name . '@' . config_item('base_domain');
			}
		}

		return $email;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function reset_password($data = array())
	{
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('password', 'lang:password', 'trim|strip_tags|required|min_length[' . config_option('min_member_password_length') . ']|max_length[' . config_option('max_member_password_length') . ']');
		$this->form_validation->set_rules('confirm_password', 'lang:confirm_password', 'trim|required|matches[password]');

		if ($this->form_validation->run())
		{
			$row = $this->update_member_password($data);
		}
		else
		{
			$row = array(
				'error'    => TRUE,
				'msg_text' => validation_errors(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param array $type
	 * @return bool|false|string
	 */
	public function search($options = '', $type = array())
	{
		//set the sort order for this query
		$sort = $this->config->item(TBL_MEMBERS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		//set the default group
		$groups = TBL_MEMBERS_AFFILIATE_GROUPS;

		if (!empty($type['table']))
		{
			$groups = 'members_' . $type['table'] . '_groups';
		}

		//run the query
		$this->db->select(TBL_MEMBERS . '.*,' .
			$groups . '.*,' .
			TBL_MEMBERS_PROFILES . '.*,' .
			TBL_MEMBERS . '.member_id AS mid');

		//join the members groups
		$this->db->join($groups,
			TBL_MEMBERS . '.' . $this->id . '=' . $groups . '.' . $this->id,
			'left');

		//join the members_profile
		$this->db->join(TBL_MEMBERS_PROFILES,
			TBL_MEMBERS . '.' . $this->id . '=' . TBL_MEMBERS_PROFILES . '.' . $this->id,
			'left');

		//set any other query filters
		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_MEMBERS);

				foreach ($columns as $f)
				{
					$this->db->or_like($this->db->dbprefix(TBL_MEMBERS) . '.' . $f, $v);
				}
			}
		}

		//set the filtering group table, field, and id if any
		if (!empty($type['type_id']) && !empty($type['group_id']))
		{
			$this->db->where($type['type_id'], (int)$type['group_id']);
		}

		$this->db->order_by($options['sort_column'], $options['sort_order']);

		$this->db->group_by(TBL_MEMBERS . '.' . $this->id);

		//run the query
		if (!$q = $this->db->get(TBL_MEMBERS, $options['limit'], $options['offset']))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->get_table_totals($options, $type, TRUE),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $status
	 * @param string $column
	 * @return bool
	 */
	public function update_status($id = '', $status = '0', $column = 'status')
	{
		if (!$this->db->where($this->id, $id)->update(TBL_MEMBERS, array($column => $status)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $form_fields
	 * @param string $type
	 * @return bool|false|string
	 */
	public function update($data = array(), $form_fields = array(), $type = 'admin')
	{
		//clean the input data first
		$sdata = $this->dbv->clean($data, TBL_MEMBERS);

		//run the db query
		$this->db->where($this->id, valid_id($data[$this->id]));

		if (!$this->db->update(TBL_MEMBERS, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//update any custom fields
		$this->update_member_custom_fields($data, $form_fields);

		if ($type == 'admin')
		{
			//update sponsor
			$this->update_member_sponsor($data);

			//update groups
			$this->update_member_groups($data);

			//update mailing lists
			$this->update_member_lists($data);

			//update profile data
			$this->update_member_profile($data);

			//update member alerts
			$this->update_member_alerts($data);

			//update permissions
			$this->update_member_permissions($data);
		}

		$row = array(
			'id'       => $data[$this->id],
			'data'     => $sdata,
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
	public function update_member_password($data = array())
	{
		$sdata = $this->dbv->clean($data, TBL_MEMBERS_PASSWORDS);

		//run the db query
		if (!$this->db->where($this->id, $data['member_id'])->update(TBL_MEMBERS_PASSWORDS, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data['member_id'],
			'data'     => $data['password'],
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update member custom fields
	 *
	 * Add or Update custom fields for members
	 *
	 * @param array $data
	 * @param array $form_fields
	 *
	 * @return bool
	 */
	public function update_member_custom_fields($data = array(), $form_fields = array())
	{
		//lets loop the form fields

		if (!empty($form_fields))
		{
			foreach ($form_fields as $v)
			{
				//check if the following field exists
				if ($v['custom'] == 1 && !empty($data[$v['form_field']]))
				{
					//lets get the member custom field id first
					$q = $this->db->where('field_id', $v['field_id'])->get(TBL_MEMBERS_CUSTOM_FIELDS);

					if ($q->num_rows() > 0)
					{
						$c = $q->row_array();

						//check if the value is there
						$this->db->where($this->id, $data[$this->id]);
						$this->db->where('custom_field_id', $c['custom_field_id']);

						if (!$q = $this->db->get(TBL_MEMBERS_TO_CUSTOM_FIELDS))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}

						//set the data for insertion/update
						$vars = array('data'            => $data[$v['form_field']],
						              'custom_field_id' => $c['custom_field_id'],
						              'member_id'       => $data[$this->id],
						);

						$func = 'insert';

						if ($q->num_rows() > 0)
						{
							//if it is lets update
							$d = $q->row_array();

							$this->db->where('mfc_id', $d['mfc_id']);

							$func = 'update';
						}

						if (!$this->db->$func(TBL_MEMBERS_TO_CUSTOM_FIELDS, $vars))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}
					}
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Update address in database
	 *
	 * Run the sql query to update the address fields
	 * in the members_address table
	 *
	 * @param $data
	 *
	 * @return bool|string
	 */
	public function update_address($data)
	{
		//clean the input data first
		$data = $this->dbv->clean($data, TBL_MEMBERS_ADDRESSES);

		//run the db query
		$this->db->where('id', (int)$data['id']);

		if (!$this->db->update(TBL_MEMBERS_ADDRESSES, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data[$this->id],
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return bool
	 */
	public function update_member_note($id = '', $data = array())
	{
		$data = $this->dbv->clean($data, TBL_MEMBERS_NOTES);

		if (!$q = $this->db->where('id', $data['pk'])
			->update(TBL_MEMBERS_NOTES, array('note' => strip_tags($data['value'])))
		)
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
	public function update_member_permissions($data = array())
	{
		$data = $this->dbv->clean($data, TBL_MEMBERS_PERMISSIONS);

		if (!$this->db->where($this->id, $data[$this->id])
			->update(TBL_MEMBERS_PERMISSIONS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('permissions_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update password
	 *
	 * Update the member password
	 *
	 * @param array $data
	 *
	 * @return bool|string
	 */
	public function update_password($data = array())
	{
		$vars = array('password' => password_hash($data['password'], PASSWORD_DEFAULT),
		);

		if (!$this->db->where($this->id, $data['member_id'])
			->update(TBL_MEMBERS_PASSWORDS, $vars)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('password_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update_member_profile($data = array())
	{
		$sdata = $this->dbv->clean($data, TBL_MEMBERS_PROFILES);

		if (!$this->db->where($this->id, $data[$this->id])->update(TBL_MEMBERS_PROFILES, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data['member_id'],
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
	public function update_member_alerts($data = array())
	{
		$sdata = $this->dbv->clean($data, TBL_MEMBERS_ALERTS);

		if (!$this->db->where($this->id, $data['member_id'])->update(TBL_MEMBERS_ALERTS, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data['member_id'],
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
	public function update_member_lists($data = array())
	{
		//if it's empty, delete all lists for the member
		if (empty($data['mailing_lists']))
		{
			$this->delete_member_lists($data['member_id']);

			return TRUE;
		}

		//check if there are any lists to remove
		if ($a = $this->get_member_lists($data['member_id']))
		{
			foreach ($a as $v)
			{
				if (in_array($v['list_id'], $data['mailing_lists']))
				{
					//remove from the list
					$data['mailing_lists'] = array_diff($data['mailing_lists'], array($v['list_id']));

					//check if we're updateing the email address
					if ($data['primary_email'] != $v['email_address'])
					{
						//update the email for the list
						$this->lists->update_user($v['list_id'],
							$v['email_address'],
							array('email_address' => $data['primary_email']),
							$data['member_id']
						);
					}
				}
				else
				{
					//remove the list from db
					$this->delete_member_lists($data['member_id'], $v['list_id']);
				}
			}
		}

		//now add the remaining lists if any...
		$this->insert_member_lists($data);

		$row = array(
			'id'       => $data['member_id'],
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
	public function update_member_sponsor($data = array())
	{

		$sdata = $this->dbv->clean($data, TBL_MEMBERS_SPONSORS);

		if ($data['member_id'] != $sdata['sponsor_id'])
		{
			if (!$this->db->where($this->id, $data['member_id'])->update(TBL_MEMBERS_SPONSORS, $sdata))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array(
				'id'       => $data['member_id'],
				'msg_text' => lang('system_updated_successfully'),
				'success'  => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update_member_group($type = '', $data = array())
	{
		$sdata = $this->dbv->clean($data, $type);

		if (!$this->db->where($this->id, $data['member_id'])->update($type, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data['member_id'],
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_member_groups($data = array())
	{
		foreach ($data as $k => $v)
		{
			switch ($k)
			{
				case 'affiliate_group':

					$data['group_id'] = $data['affiliate_group'];
					$t = TBL_MEMBERS_AFFILIATE_GROUPS;

					break;

				case 'discount_group':

					$data['group_id'] = $data['discount_group'];
					$t = TBL_MEMBERS_DISCOUNT_GROUPS;

					break;

				case 'blog_group':

					$data['group_id'] = $data['blog_group'];
					$t = TBL_MEMBERS_BLOG_GROUPS;

					break;
			}

			if (!empty($t))
			{
				if ($row = $this->get_member_group($t, $data['member_id']))
				{
					//let's update it
					$this->update_member_group($t, $data);
				}
				else //add it in the table as it ain't there....
				{
					$this->add_group($t, $data);
				}
			}

			unset($t); //reset the tables...
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function update_social_user($id = '', $data = array(), $type = '')
	{
		$vars = format_social_data($data, $type, $id);
		//run the db query
		$this->db->where($this->id, valid_id($vars[$this->id]));

		//clean the input data first
		$sdata = $this->dbv->clean($vars, TBL_MEMBERS);

		if (!$this->db->update(TBL_MEMBERS, $sdata))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$this->update_member_profile($vars);

		$row = array(
			'id'       => $vars['member_id'],
			'data'     => $vars,
			'msg_text' => lang('record_inserted_successfully'),
			'success'  => TRUE,
		);
		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $func
	 * @param array $data
	 * @param array $form_fields
	 * @return false|string
	 */
	public function validate($func = 'update', $data = array(), $form_fields = array())
	{
		//set the validation array
		$this->form_validation->set_data($data);

		//run the validation for users only
		foreach ($form_fields as $v)
		{
			//setup rules for each field
			$a = $this->form->generate_form_rule($func, $v);

			$this->form_validation->set_rules($v['form_field'], $v['field_name'], $a['rule']);

			//generate custom error messages if needed
			if (!empty($a['msg']))
			{
				foreach ($a['msg'] as $b => $c)
				{
					$this->form_validation->set_message($b, $c);
				}
			}
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors());
		}

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Verify password
	 *
	 * Verify the new passwords being reset to
	 *
	 * @param array $data
	 * @param bool|FALSE $update
	 * @param string $member_id
	 *
	 * @return bool|string
	 */
	public function verify_password($data = array(), $update = FALSE, $member_id = '')
	{
		$pass = $this->get_access_data($member_id);

		if (!$pass)
		{
			$msg = 'invalid_user';
		}
		else
		{
			//check the password via phpass
			if (password_verify($data['current'], $pass['password']))
			{
				$this->form_validation->set_data($data);

				$this->form_validation->set_rules('password', 'lang:password',
					array('trim', 'required', 'xss_clean',
					      'min_length[' . $this->config->item('min_member_password_length') . ']',
					      'max_length[' . $this->config->item('max_member_password_length') . ']',
					      'matches[confirm]'));

				$this->form_validation->set_rules('confirm', 'lang:confirm_password', 'trim|required');

				if ($this->form_validation->run())
				{
					return $update == TRUE ? $this->update_password($data) : TRUE;
				}
				else
				{
					$msg = validation_errors();
				}
			}
			else
			{
				$msg = 'invalid_current_password';
			}


		}

		$row = array(
			'msg_text' => lang($msg),
			'error'    => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 *  Validate address input
	 *
	 * Run validation on all address input fields
	 * and send it to the create or update method to process
	 *
	 * @param string $func
	 * @param array $data
	 * @param bool|FALSE $return
	 *
	 * @return array|bool|string
	 */
	public function validate_address($func = 'add_address', $data = array(), $return = FALSE)
	{
		//set the validation array
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item(TBL_MEMBERS_ADDRESSES, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_MEMBERS_ADDRESSES);

		//go through each table field and lets see if it is part of the input
		foreach ($fields as $f)
		{
			if (isset($data[$f]))
			{
				//set the default rule
				$rule = 'trim|xss_clean';

				//if this field is a required field, let's set that
				if (in_array($f, $required))
				{
					$rule .= '|required';
				}

				//check for other required validation rules
				switch ($f)
				{
					//numbers only
					case 'state':
					case 'country':

						$rule .= '|integer';

						break;

					//set other default rules
					default:

						$rule .= '|max_length[255]';

						break;
				}

				$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
			}
		}

		if ($this->form_validation->run())
		{
			//do validation only and return data
			if ($return)
			{
				return $data;
			}

			//run the sql method to update
			$row = $this->$func($data);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate_profile($data = array())
	{
		//set the validation array
		$this->form_validation->set_data($data);

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_MEMBERS_PROFILES);

		//go through each table field and lets see if it is part of the input
		foreach ($fields as $f)
		{
			if (isset($data[$f]))
			{
				//set the default rule
				$rule = 'trim|strip_tags';

				//check for other required validation rules
				switch ($f)
				{
					//set other default rules
					default:

						$rule .= '|strip_tags';

						break;
				}

				$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
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
			$row = array('error'        => TRUE,
			             'error_fields' => generate_error_fields(),
			             'msg_text'     => validation_errors(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $member_id
	 * @return bool
	 */
	public function verify_address_ownership($id = '', $member_id = '')
	{
		//lets get the address first and check if it belongs to the user
		if (!$q = $this->db->where('id', $id)->get(TBL_MEMBERS_ADDRESSES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			if ($row['member_id'] == $member_id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}

/* End of file Members_model.php */
/* Location: ./application/models/Members_model.php */