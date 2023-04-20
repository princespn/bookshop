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
class Login_model extends CI_Model
{
	/**
	 * Login_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function check_user_login()
	{
		$u = $this->config->item('user_login_email_field');
		$p = $this->config->item('user_login_password_field');

		if ($row = $this->mem->get_details($this->input->post($u, TRUE), TRUE, TRUE))
		{
			//get the password
			$pass = $this->mem->get_access_data($row['member_id']);

			//check the password
			if (password_verify($this->input->post($p, TRUE), $pass['password']))
			{
				if (!empty($row['addresses']))
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
				}

				//set the session data for the member
				$this->set_login_data($row, 'member');

				return TRUE;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function check_admin_login()
	{
		$u = $this->config->item('admin_login_username_field');
		$p = $this->config->item('admin_login_password_field');

		$this->db->where('username', $this->input->post($u, TRUE));
		$this->db->where('status', 'active');

		$this->db->join(TBL_ADMIN_GROUPS, TBL_ADMIN_USERS . '.admin_group_id = ' . TBL_ADMIN_GROUPS . '.admin_group_id', 'left');

		//run query
		if (!$q = $this->db->get(TBL_ADMIN_USERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() == 1)
		{
			$row['admin'] = $q->row_array();

			if (password_verify($this->input->post($p, TRUE), $row['admin']['apassword']))
			{
				unset($row['admin']['apassword']);

				//get the session language
				$b = $this->dbv->get_record(TBL_LANGUAGES, 'language_id', (int)$this->input->post('language'));
				$row['default_language'] = $b['name'];
				$row['default_lang_id'] = $b['language_id'];

				//set permissions
				if (!empty($row['admin']['permissions']))
				{
					$row['admin']['permissions'] = $this->set_permissions(unserialize($row['admin']['permissions']));
				}

				$this->set_login_data($row);

				return TRUE;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $email
	 * @param string $code
	 * @return bool
	 */
	public function check_reset_access($email = '', $code = '')
	{
		$this->db->where('primary_email', $email);
		$this->db->where('status', 'active');

		if (!empty($code))
		{
			$this->db->where('confirm_id', $code);
		}

		$q = $this->db->get(TBL_ADMIN_USERS);

		if ($q->num_rows() == 1)
		{
			return $q->row_array();
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $code
	 * @param string $table
	 * @return bool
	 * @throws Exception
	 */
	public function check_reset_confirmation($code = '', $table = TBL_ADMIN_USERS)
	{
		$this->db->where('confirm_id', xss_clean($code));

		$q = $this->db->get($table);

		if ($q->num_rows() == 1)
		{
			return TRUE;
		}

		throw new Exception(lang('invalid_confirmation_code'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param $id
	 * @return false|string
	 */
	public function generate_confirm_id($table = '', $id)
	{
		$cid = confirm_id();

		$mid = $table == TBL_ADMIN_USERS ? 'admin_id' : 'member_id';

		$this->db->where($mid, $id)->update($table, array('confirm_id' => $cid));

		return $cid;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function process_social($data = array(), $type = '')
	{
		//if it is given check if the user is already in the db
		if (!$row = $this->verify_social_user($data['email']))
		{
			// if not, add them
			$vars = $this->mem->add_social_user($data, $type);
			$row = $vars['data'];
		}
		else
		{
			//update the user
			$vars = $this->mem->update_social_user($row['member_id'], $data);

			$row = $vars['data'];
		}

		//get all the member data for the session
		$row = $this->mem->get_details($row['member_id']);

		// //set the login session and redirect
		if (!empty($row['addresses']))
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
		}

		//set the session data for the member
		$this->set_login_data($row, 'member');

		return array('success' => TRUE,
		             'data'    => $row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 */
	public function set_login_data($data = array(), $type = 'admin')
	{
		if ($type == 'member')
		{
			$this->session->set_userdata('user_logged_in', lang('user_logged_in_successfully'));

			//update cart if the user is logged in
			if (sess('cart_id'))
			{
				$this->db->where('cart_id', sess('cart_id'));

				if (!$this->db->update(TBL_CART, array('member_id' => $data['member_id'])))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
			else
			{
				//check if there is a cart for this user
				if (!$q = $this->db->where('member_id', $data['member_id'])->get(TBL_CART))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->row_array();
					$this->session->set_userdata('cart_id', $row['cart_id']);
				}
			}
		}

		$this->session->set_userdata($data);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @return bool
	 */
	public function update_pass($data = array(), $table = TBL_ADMIN_USERS)
	{
		if ($table == TBL_ADMIN_USERS)
		{
			$update = array('apassword'  => password_hash($data['cpass'], PASSWORD_DEFAULT),
			                'confirm_id' => '',
			);

			$this->db->where('confirm_id', xss_clean($data['code']));

			if ($this->db->update($table, $update))
			{
				return TRUE;
			}
		}
		else //update members
		{
			$this->db->where('confirm_id', xss_clean($data['code']));

			$q = $this->db->get($table);

			if ($q->num_rows() == 1)
			{
				$row = $q->row_array();
			}

			$this->db->where('member_id', $row['member_id']);

			if (!$this->db->update($table, array('confirm_id' => '')))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$this->db->where('member_id', $row['member_id']);

			if ($this->db->update(TBL_MEMBERS_PASSWORDS, array('password' => password_hash($data['cpass'], PASSWORD_DEFAULT))))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param $id
	 * @return bool
	 */
	public function update_login_data($table = '', $id)
	{
		$update_data = array('last_login_date' => get_time('', TRUE),
		                     'last_login_ip'   => $this->input->server('REMOTE_ADDR'),
		);

		if ($table == 'admin')
		{
			$this->db->where('admin_id', $id);
			$this->db->update(TBL_ADMIN_USERS, $update_data);
		}
		else
		{
			$this->db->where('member_id', $id);
			$this->db->update(TBL_MEMBERS, $update_data);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function validate_admin_login()
	{
		if (config_enabled('sts_form_enable_admin_login_captcha'))
		{
			$this->form_validation->set_rules(
				CAPTCHA_FIELD, 'lang:captcha',
				array(
					'required',
					array('check_captcha', array($this->dbv, 'check_captcha')),
				)
			);

			$this->form_validation->set_message('check_captcha', lang('invalid_security_captcha'));

			if (!$this->form_validation->run())
			{
				return FALSE;
			}
		}

		$this->form_validation->reset_validation();
		$this->form_validation->set_data($_POST);

		$this->form_validation->set_rules($this->config->item('admin_login_username_field'), 'lang:username',
			'trim|required|strtolower|min_length[' . $this->config->item('min_admin_username_length') . ']|max_length[' . $this->config->item('max_admin_username_length') . ']|alpha_numeric');

		$this->form_validation->set_rules(
			$this->config->item('admin_login_password_field'), 'lang:password',
			array(
				'trim', 'required',
				'min_length[' . config_item('min_admin_password_length') . ']',
				'max_length[' . config_item('max_admin_password_length') . ']',
				array('check_admin_login', array($this->login, 'check_admin_login')),
			)
		);

		$this->form_validation->set_message('check_admin_login', lang('invalid_login_access'));

		if ($this->form_validation->run())
		{
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @return array|bool
	 */
	public function validate_reset_confirm($data = array(), $table = TBL_ADMIN_USERS)
	{
		$min = $table == TBL_ADMIN_USERS ? config_item('min_admin_password_length') : config_item('min_member_password_length');
		$max = $table == TBL_ADMIN_USERS ? config_item('max_admin_password_length') : config_item('max_member_password_length');

		$this->form_validation->set_rules(
			'cpass', 'lang:password',
			'trim|xss_clean|required|min_length[' . $min . ']|max_length[' . $max . ']|matches[cpassconf]');

		$this->form_validation->set_rules('cpassconf', 'lang:confirm_password', 'trim|xss_clean|required');

		if ($this->form_validation->run())
		{
			if ($this->update_pass($data, $table))
			{
				return array('msg_text' => lang('password_reset_successfully'),
				             'success'  => TRUE,
				);
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @return bool|false|string
	 */
	public function validate_pass_reset($data = array(), $table = '')
	{
		if (valid_email($data['email']))
		{
			$this->db->where('primary_email', xss_clean($data['email']));

			if ($table == TBL_ADMIN_USERS)
			{
				$this->db->where('status', 'active');
			}
			else
			{
				$this->db->where('status', '1');
			}

			$q = $this->db->get($table);

			if ($q->num_rows() == 1)
			{
				$row = array('data'    => $q->row_array(),
				             'success' => TRUE,
				);

				if ($table == TBL_ADMIN_USERS)
				{
					//set confirmation link
					$confirm_id = $this->generate_confirm_id($table, $row['data']['admin_id']);

					$row['data']['reset_admin_password_link_text'] = site_url(ADMIN_LOGIN . '/confirm/' . $confirm_id);
					$row['data']['reset_admin_password_link'] = anchor(site_url(ADMIN_LOGIN . '/confirm/' . $confirm_id));
				}
				else
				{
					//set confirmation link
					$confirm_id = $this->generate_confirm_id($table, $row['data']['member_id']);

					$row['data']['reset_member_password_link_text'] = site_url('login/confirm/' . $confirm_id);
					$row['data']['reset_member_password_link'] = anchor(site_url('login/confirm/' . $confirm_id));
				}
			}
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $email
	 * @return array|bool
	 */
	public function validate_social($data = array(), $email = '')
	{
		//check if the email address is given
		if (!empty($data->email))
		{
			return $data;
		}
		elseif (!empty($email))
		{
			if (valid_email($email))
			{
				$data->email = $email;

				return $data;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function validate()
	{
		if (config_enabled('sts_form_enable_login_captcha'))
		{
			$this->form_validation->set_rules(
				CAPTCHA_FIELD, 'lang:captcha',
				array(
					'required',
					array('check_captcha', array($this->dbv, 'check_captcha')),
				)
			);

			$this->form_validation->set_message('check_captcha', lang('invalid_security_captcha'));

			if (!$this->form_validation->run())
			{
				return FALSE;
			}
		}

		$this->form_validation->reset_validation();

		$this->form_validation->set_rules(
			$this->config->item('user_login_email_field'), 'lang:email_address',
			'trim|required|strtolower|valid_email');

		$this->form_validation->set_rules(
			$this->config->item('user_login_password_field'), 'lang:password',
			array(
				'trim', 'required',
				'min_length[' . $this->config->item('min_member_password_length') . ']',
				'max_length[' . $this->config->item('max_member_password_length') . ']',
				array('check_user_login', array($this->login, 'check_user_login')),
			)
		);

		$this->form_validation->set_message('check_user_login', lang('invalid_login_access'));

		if (!$this->form_validation->run())
		{
			return FALSE;
		}



		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $perms
	 * @return array
	 */
	protected function set_permissions($perms)
	{
		$vars = array();
		if (!empty($perms))
		{
			foreach ($perms as $k)
			{
				foreach ($k as $a => $b)
				{
					$vars[$a] = $b;
				}
			}

			ksort($vars);
		}

		return $vars;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $email
	 * @return bool
	 */
	protected function verify_social_user($email = '')
	{
		if (!$q = $this->db->where('primary_email', $email)->get(TBL_MEMBERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows > '0' ? FALSE : $q->row_array();
	}
}

/* End of file Login_model.php */
/* Location: ./application/models/Login_model.php */