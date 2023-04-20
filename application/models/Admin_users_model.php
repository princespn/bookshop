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
class Admin_users_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $admin_id = 'admin_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return bool|false|string
	 */
	public function ajax_search($term = '')
	{
		$this->db->like('username', $term);
		$this->db->select('admin_id, username');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_ADMIN_USERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check Username
	 *
	 * check for unique admin username
	 *
	 * @param string $u
	 * @return bool
	 */
	public function check_username($u = '')
	{
		$username = empty($u) ? $this->input->post('username') : $u;

		if (!empty($username))
		{
			$this->db->where('username', $username);

			if ($this->input->post($this->admin_id))
			{
				$this->db->where($this->admin_id . ' !=', (int)$this->input->post($this->admin_id));
			}

			if (!$q = $this->db->get(TBL_ADMIN_USERS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check email address
	 *
	 * check for unique email address
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function check_email($email = '')
	{
		$email = empty($email) ? $this->input->post('primary_email') : $email;

		if (!empty($email))
		{
			$this->db->where('primary_email', $email);

			if ($this->input->post($this->admin_id))
			{
				$this->db->where($this->admin_id . ' !=', (int)$this->input->post($this->admin_id));
			}

			if (!$q = $this->db->get(TBL_ADMIN_USERS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Create admin
	 *
	 * create a new admin user account
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_ADMIN_USERS);

		if (!$this->db->insert(TBL_ADMIN_USERS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//add to alerts table
		$this->db->insert(TBL_ADMIN_ALERTS, array( 'admin_id' => $this->db->insert_id() ));

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' =>  lang('admin_user') . ' ' . $data['username'] . ' ' . lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete admin
	 *
	 * delete the admin user
	 *
	 * @param string $id
	 *
	 * @return string
	 * @throws Exception
	 */
	public function delete($id = '')
	{
		$data = $this->get_admin_details($id);
		
		if (!$this->db->where($this->admin_id, $id)->delete(TBL_ADMIN_ALERTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!$this->db->where($this->admin_id, $id)->delete(TBL_ADMIN_USERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
		
		$row = array(
			'msg_text' => $data['username'] . ' ' . lang('record_deleted_successfully'),
			'success'  => TRUE,
			'vars' => $data,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get alerts
	 *
	 * Get admin alerts
	 *
	 * @return mixed
	 */
	public function get_alerts()
	{
		$fields = $this->db->list_fields(TBL_ADMIN_ALERTS);

		foreach ($fields as $k => $v)
		{
			if ($v == 'admin_id') unset($fields[$k]);
		}

		return $fields;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get admin details
	 *
	 * get the database details for the admin user
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public function get_admin_details($id = '')
	{
		$sql = 'SELECT *,
				    (SELECT ' . $this->admin_id . '
				        FROM ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' p
				        WHERE p.' . $this->admin_id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->admin_id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->admin_id . '
				        FROM ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' p
				        WHERE p.' . $this->admin_id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->admin_id . '` ASC LIMIT 1)
				        AS next
				    FROM ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_ALERTS) . ' a ON p.' . $this->admin_id . '= a.' . $this->admin_id . '
                    WHERE p.' . $this->admin_id . '= ' . (int)$id . '';

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
	 * Update admin details
	 *
	 * update admin details
	 *
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		if ($data[ 'admin_id' ] == 1) //super admin has all permissions
		{
			$data[ 'status' ] = 'active';
			$data[ 'show_assigned_tickets_only' ] = '0';
		}

		$alerts = array();
		foreach ($data as $k => $v)
		{
			if (preg_match('/alert_*/', $k))
			{
				$alerts[ $k ] = $v;
				unset($data[ $k ]);
			}
		}
		
		$data = $this->dbv->clean($data, TBL_ADMIN_USERS);

		if (!$this->db->where($this->admin_id, $data[ 'admin_id' ])->update(TBL_ADMIN_USERS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!$this->db->where($this->admin_id, $data[ 'admin_id' ])->update(TBL_ADMIN_ALERTS, $alerts))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data[ $this->admin_id ],
			'msg_text' => lang('admin_user') . ' ' . $data['username'] . ' ' . lang('updated_successfully'),
			'success'  => TRUE,
			'row'      => $data,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update status
	 *
	 * update the status field for the admin
	 *
	 * @param string $id
	 * @param string $status
	 *
	 * @return bool
	 */
	public function update_status($id = '', $status = 'active')
	{
		$this->db->where('admin_id', $id);

		if (!$this->db->update(TBL_ADMIN_USERS, array( 'status' => $status )))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array('success' => TRUE,
		             'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate the admin data
	 *
	 * validate the submitted form for account creation and update
	 *
	 * @param string $func
	 * @param array  $data
	 * @param bool   $return
	 *
	 * @return array|bool
	 */
	public function validate($func = 'create', $data = array(), $return = FALSE)
	{
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('status', 'lang:status', 'required');
		$this->form_validation->set_rules('fname', 'lang:first_name', 'trim|strip_tags|required|max_length[50]');
		$this->form_validation->set_rules('lname', 'lang:last_name', 'trim|strip_tags|required|max_length[50]');

		$this->form_validation->set_rules('rows_per_page', 'lang:rows_per_page', 'trim|numeric');
		$this->form_validation->set_rules('admin_home_page', 'lang:admin_home_page', 'trim|alpha_dash');

		if ($func == 'create')
		{
			$this->form_validation->set_rules('apassword', 'lang:password', 'trim|required|min_length[8]|max_length[30]|matches[passconf]');
			$this->form_validation->set_rules('passconf', 'lang:confirm_password', 'trim|required');

			$this->form_validation->set_rules(
				'username', 'lang:username',
				'trim|required|strtolower|min_length[' . $this->config->item('min_admin_username_length') . ']|max_length[' . $this->config->item('max_admin_username_length') . ']|alpha_numeric|is_unique[' . TBL_ADMIN_USERS . '.' . 'username' . ']',
				array(
					'required'  => '%s ' . lang('field_is_required'),
					'is_unique' => '%s ' . lang('already_exists'),
				)
			);

			$this->form_validation->set_rules(
				'primary_email', 'lang:email_address',
				'trim|required|strtolower|valid_email|is_unique[' . TBL_ADMIN_USERS . '.primary_email]',
				array(
					'required'  => '%s ' . lang('field_is_required'),
					'is_unique' => '%s ' . lang('already_exists'),
				)
			);
		}
		else
		{
			if ($this->input->post('apassword'))
			{
				$this->form_validation->set_rules('apassword', 'lang:password', 'trim|required|min_length[' .  config_item('min_admin_password_length') . ']|max_length[30]|matches[passconf]');
				$this->form_validation->set_rules('passconf', 'lang:confirm_password', 'trim|required');
			}

			$this->form_validation->set_rules(
				'username', 'lang:username',
				array(
					'trim', 'required', 'strtolower', 'min_length[6]', 'max_length[20]', 'alpha_numeric',
					array( 'check_username', array( $this->admins, 'check_username' ) )
				)
			);

			$this->form_validation->set_message('check_username', '%s ' . lang('already_exists'));

			$this->form_validation->set_rules(
				'primary_email', 'lang:email_address',
				array(
					'trim', 'required', 'strtolower', 'valid_email',
					array( 'check_email', array( $this->admins, 'check_email' ) )
				),
				array(
					'required'    => '%s ' . lang('field_is_required'),
					'check_email' => '%s ' . lang('already_exists'),
				)
			);

			$this->form_validation->set_message('check_email', '%s ' . lang('already_exists'));
		}


		if ($this->form_validation->run())
		{
			if ($return)
			{
				return $this->dbv->validated($data);
			}

			$row = $this->$func($this->dbv->validated($data));
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Admin_users_model.php */
/* Location: ./application/models/Admin_users_model.php */