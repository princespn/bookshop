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
class Security_model extends CI_Model
{

	// ------------------------------------------------------------------------

	/**
	 * Security_model constructor.
	 */
	public function __construct()
	{
		$this->load->helper('security');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $key
	 * @param string $type
	 * @param bool $encrypt
	 * @param bool $redirect
	 * @return bool
	 */
	public function check_system_key($key = '', $type = 'sts_system_domain_key', $encrypt = TRUE, $redirect = FALSE)
	{
		$k = $encrypt == TRUE ? md5($key) : $key;

		if ($k == config_item($type))
		{
			return TRUE;
		}

		if ($redirect == TRUE)
		{
			redirect();
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $ip
	 * @return bool|void
	 */
	public function auto_block_ip($type = 'remove', $ip = '')
	{
		if ($ip == '0.0.0.0')
		{
			return;
		}

		switch ($type)
		{
			case 'block':

				if ($this->config->item('sts_sec_enable_auto_ip_block') == 1 && !empty($ip))
				{
					$data = array('ip'        => $ip,
					              'date'      => get_time(),
					              'type'      => 'block',
					              'member_id' => '0',
					);

					$this->db->insert(TBL_SECURITY, $data);

					$day = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));

					$this->db->where('ip', $ip);
					$this->db->where('type', 'block');
					$this->db->where('date >=', $day);
					$total = $this->db->count_all_results(TBL_SECURITY);

					if ($total >= $this->config->item('sts_sec_auto_ip_block_interval'))
					{
						$this->add_auto_block($ip);
						$this->auto_block_ip('remove', $ip);
					}
				}

				break;

			case 'remove':

				if (!empty($ip))
				{
					$this->db->where('ip', $ip);
					$this->db->where('type', 'block');
					$this->db->delete(TBL_SECURITY);
				}

				break;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function check_admin_ip_restriction()
	{
		//check if IP is allowed to access the admin area
		if (config_item('sts_sec_enable_admin_restrict_ip'))
		{
			if ($this->config->item('sts_sec_admin_restrict_ip'))
			{
				//check if the IP is allowed in the ip restriction array
				$i = explode(',', $this->config->item('sts_sec_admin_restrict_ip'));

				foreach ($i as $ip)
				{
					if ($this->input->ip_address() == trim($ip))
					{
						return TRUE;
					}
				}

				log_error('security', lang('invalid_ip_address') . ': ' . $this->input->server('REMOTE_ADDR'));
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $func
	 * @param string $id
	 * @return bool
	 */
	public function check_flood_control($type = 'email', $func = 'check', $id = '')
	{
		if (config_enabled('sts_sec_site_enable_form_flood_control') && config_item('sts_sec_site_form_flood_control_interval_' . $type) > 0)
		{
			switch ($func)
			{
				case 'add':
					$data = array('ip'        => $this->input->ip_address(),
					              'date'      => get_time() + config_item('sts_sec_site_form_flood_control_interval_' . $type) * 60,
					              'type'      => 'flood_' .$type,
					              'member_id' => $id,
					);

					$this->db->insert(TBL_SECURITY, $data);

					//delete old ones
					$this->db->where('date <', get_time());
					$this->db->where('type', 'flood_' . $type);
					$this->db->where('member_id', $id);
					$this->db->delete(TBL_SECURITY);

					break;

				case 'check':

					$this->db->where('member_id', $id);
					$this->db->where('type', 'flood_' . $type);
					$this->db->where('date >=', get_time());

					$q = $this->db->get(TBL_SECURITY);

					if ($q->num_rows() > 0)
					{
						return FALSE;
					}

					break;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 *
	 */
	public function check_admin_folder()
	{
		if (uri(1) != ADMIN_LOGIN)
		{
			redirect_page();
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $uri1
	 * @param string $uri2
	 * @param bool $link
	 * @return bool
	 */
	public function check_admin_permissions($uri1 = '', $uri2 = '', $link = FALSE)
	{
		if ($this->session->admin['admin_group_id'] == 1)
		{
			return TRUE;
		}

		if (!$uri2)
		{
			return TRUE;
		}

		$uri = $uri2 == 'mass_update' ? 'update' : $uri2;

		$uri = $uri1 . '/' . $uri;

		switch ($uri2)
		{
			case 'view':
			case 'create':
			case 'delete':

				if (!isset($this->session->admin['permissions'][$uri]))
				{
					return FALSE;
				}

				break;

			case 'update':
			case 'mass_update':

				if (!isset($this->session->admin['permissions'][$uri]))
				{
					if ($this->input->post() OR $link == TRUE)
					{
						return FALSE;
					}

					return FALSE;
				}

				break;
		}

		//check license
		if ($uri1 == 'license' && $uri2 == 'update')
		{
			if (!isset($this->session->admin['permissions']['license/view']))
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 *
	 */
	public function check_installer()
	{
		if (file_exists(APPPATH . 'controllers/Install.php'))
		{
			show_error(APPPATH . 'controllers/Install.php' . ' ' . lang('file_must_be_deleted'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param bool $redirect
	 * @return bool
	 */
	public function check_login_session($type = '', $redirect = TRUE)
	{
		switch ($type)
		{
			case 'admin':

				if ($this->session->admin['admin_id'])
				{
					if (!$this->check_admin_permissions(uri(2), uri(3)))
					{
						redirect_page(admin_url('error_pages/permissions'));
					}

					return TRUE;
				}

				break;

			case 'member':

				if ($this->session->member_id && $this->session->user_logged_in)
				{
					return TRUE;
				}
				else
				{
					if ($redirect == FALSE)
					{
						return FALSE;
					}
				}

				break;
		}

		if (is_ajax())
		{
			echo '<script>window.location.href=\'' . site_url() . '\';</script>';
		}
		else
		{
			redirect_page();
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 */
	public function check_ssl($type = '')
	{
		if (config_enabled('ssl_' . $type . '_area'))
		{
			if (!is_https())
			{
				log_error('security', lang('ssl_required_page'));
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 *
	 */
	public function run_admin_checks()
	{
		//runs from the JX_Controller - Admin_Controller

		//check login security
		$this->check_login_session('admin');

		//check IP address block
		$this->check_admin_ip_restriction();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $redirect
	 */
	public function run_user_checks($redirect = TRUE)
	{
		//check login security
		$this->check_login_session('member', $redirect);
	}

	// ------------------------------------------------------------------------

	/**
	 * Verify ownership
	 *
	 * Check if the specified resource is owned and
	 * accessible to the specified member
	 *
	 * @param string $mid
	 * @return bool
	 */
	public function verify_ownership($mid = '', $sess_id = '', $log = TRUE)
	{
		//check if we are verifying a stated member ID or one that gets posted with data
		$id = !empty($mid) ? $mid : $this->input->post('member_id');
		$sess_id = !empty($sess_id) ? $sess_id : $this->session->member_id;

		if ($id == $sess_id)
		{
			return TRUE;
		}

		if ($log == TRUE && $this->input->post())
		{
			log_error('error', lang('invalid_id'));
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $ip
	 * @return bool
	 */
	protected function add_auto_block($ip = '')
	{
		$ips = $this->config->item('sts_sec_site_restrict_ips');

		$ips = $ips . "\n" . $ip;

		$data = array('sts_sec_site_restrict_ips' => $ips);

		if ($this->set->update_db_settings($data))
		{
			return TRUE;
		}

		return FALSE;
	}
}

/* End of file Security_model.php */
/* Location: ./application/models/Security_model.php */