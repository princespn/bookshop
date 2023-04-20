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
class Affiliate_marketing_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $module_type = 'affiliate_marketing';

	// ------------------------------------------------------------------------

	/**
	 * Affiliate_marketing_model constructor.
	 */
	public function __construct()
	{
		$this->load->helper('affiliate_marketing');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $type
	 * @return array
	 */
	public function activate_affiliate_account($id = '', $type = 'user')
	{
		$is_affiliate = $this->lc->check_aff('1');

		if (!empty($is_affiliate))
		{
			if (!$this->db->where('member_id', $id)->update(TBL_MEMBERS, array('is_affiliate' => '1')))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($type == 'user')
			{
				$this->session->set_userdata('is_affiliate', '1');
			}

			return array('success'  => TRUE,
			             'msg_text' => lang('system_updated_successfully'));
		}
		else
		{
			redirect_page(admin_url('error_pages/license'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @return mixed
	 */
	public function count_affiliates()
	{
		$this->db->where('is_affiliate', '1');

		return $q = $this->db->count_all_results(TBL_MEMBERS);
	}

	// ------------------------------------------------------------------------

	//redirect the regular affiliate link to a store
	/**
	 * @param string $url
	 * @param $user
	 * @return string
	 */
	public function check_affiliate_stores($url = '', $user)
	{
		if (config_enabled('module_affiliate_marketing_affiliate_stores_redirect_affiliate_link'))
		{
			if (config_option('sts_affiliate_link_type') == 'regular')
			{
				$row = $this->mod->get_module_details('affiliate_stores', TRUE, 'affiliate_marketing', 'module_folder');

				if ($row['module']['module_status'] == 1)
				{
					$url = site_url('shop/' . $user);
				}
			}
		}

		return $url;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function check_aff_store()
	{
		if ($this->config->item('sts_affiliate_enable_profiles' == 1))
		{
			if ($this->session->tracking_data)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function check_coupon_affiliate()
	{
		if (sess('checkout_coupon_code'))
		{
			if (sess('checkout_coupon_code', 'member_id'))
			{
				if ($row = $this->get_affiliate_data(sess('checkout_coupon_code', 'member_id'), 'member_id'))
				{
					//set tracking cookie for future use
					$row['cookie_data'] = set_tracking_cookie($row);
				}
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param bool $insert
	 * @return string
	 */
	public function generate_tracking_code($data = array(), $insert = FALSE)
	{
		$chk = FALSE;

		while ($chk == FALSE)
		{
			$data['tracking_code'] = generate_random_string('15');
			$q = $this->db->where('tracking_code', $data['tracking_code'])->get(TBL_AFFILIATE_TRAFFIC);

			if ($q->num_rows() == 0)
			{
				if ($insert == TRUE)
				{
					$this->insert_traffic_data($data);
				}

				return $data['tracking_code'];
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool|string
	 */
	public function get_active_affiliates($str = '')
	{
		if (!empty($str))
		{
			$this->db->select($str);
		}

		if (!$q = $this->db->where('is_affiliate', '1')->get(TBL_MEMBERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $col
	 * @return bool
	 */
	public function get_sponsor_id($id = '', $col = 'sponsor_id')
	{
		if (!$q = $this->db->where('member_id', $id)->get(TBL_MEMBERS_SPONSORS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			return $row[$col];
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @param bool $get_cache
	 * @return bool|string
	 */
	public function get_user_totals($id = '', $public = FALSE, $get_cache = TRUE)
	{
		$sql = 'SELECT COUNT(*) AS total_affiliate_clicks,
                (SELECT SUM(commission_amount)
                  FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . '
                  WHERE member_id = \'' . valid_id($id) . '\')
                AS total_commissions,
                (SELECT COUNT(*)
                  FROM ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . '
                  WHERE sponsor_id = \'' . valid_id($id) . '\')
                AS total_referrals
                FROM ' . $this->db->dbprefix(TBL_AFFILIATE_TRAFFIC) . '
                WHERE member_id = \'' . valid_id($id) . '\'';

		//check if we have a cache file and return that instead
		$cache = __METHOD__ . md5($sql);
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';

		if ($row = $this->init->cache($cache, $cache_type))
		{
			if ($get_cache == TRUE)
			{
				$data = $row;
			}
		}
		else
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$data = $q->row_array();
		}

		$row = array(
			'success' => TRUE,
			'data'    => $data,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $col
	 * @return bool
	 */
	public function get_affiliate_data($id = '', $col = 'username')
	{
		//run the db query
		$sql = 'SELECT p.*, b.*, m.*,
                    g.aff_group_name,
                    a.group_id AS affiliate_group,
                    n.username AS sponsor_username,
                    n.fname AS sponsor_fname,
                    n.lname AS sponsor_lname,
                    n.primary_email AS sponsor_primary_email,
                    p.member_id AS member_id,
                    s.sponsor_id,
                    s.original_sponsor_id,
                    p.member_id AS member_id
				    FROM ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_AFFILIATE_GROUPS) . ' a
                        ON p.member_id= a.member_id
                     LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' s
                        ON p.member_id = s.member_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . ' g
                        ON a.group_id = g.group_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ALERTS) . ' b
                         ON p.member_id = b.member_id 
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' n
                        ON s.sponsor_id = n.member_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' m
                        ON p.member_id = m.member_id
                    WHERE p.' . $col . '= \'' . xss_clean($id) . '\'
                        AND p.is_affiliate = \'1\'
                        AND p.status = \'1\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_MODULES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$this->db->where('module_type', $this->module_type);

		$this->db->order_by($options['sort_column'], $options['sort_order']);
		$query = $this->db->get(TBL_MODULES);


		if ($query->num_rows() > 0)
		{
			$values = array();
			foreach ($query->result_array() as $k => $v)
			{
				$values[$k] = $v;
				$values[$k]['settings'] = $this->get_module_settings($v['module_id']);
			}

			$row = array(
				'values'  => $values,
				'total'   => $this->get_table_totals(),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|string
	 */
	public function get_module_settings($id = '')
	{
		$this->db->where('settings_group', $id);
		$this->db->where('settings_module', $this->module_type);
		$this->db->order_by('settings_sort_order', 'ASC');

		if (!$q = $this->db->get('settings'))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = init_config($q->result_array());
			$row = $a[$id];
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @return mixed
	 */
	public function get_table_totals()
	{
		$this->db->where('module_type', $this->module_type);

		return $this->db->count_all_results(TBL_MODULES);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|string
	 */
	public function get_settings($id = '')
	{
		$this->db->where('module_id', $id);
		$this->db->where('module_type', $this->module_type);

		if (!$q = $this->db->get(TBL_MODULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$module = $q->row_array();

			$row = array('values' => $this->get_module_settings($id),
			             'module' => $module,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $ip
	 * @return bool|string
	 */
	public function get_tracking_by_ip($ip = '')
	{
		if (!empty($ip))
		{
			$this->db->where('ip_address', $ip);

			if (defined('TRACK_USER_AGENT_IP_TRACKING'))
			{
				$this->db->where('user_agent', $this->agent->referrer());
			}

			$this->db->order_by(TBL_AFFILIATE_TRAFFIC.'.date', 'DESC');
			$this->db->limit(1);

			$this->db->join(TBL_MEMBERS, TBL_MEMBERS . '.member_id=' . TBL_AFFILIATE_TRAFFIC . '.member_id', 'left');

			if (!$q = $this->db->get(TBL_AFFILIATE_TRAFFIC))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();
				$user = $row['username'];
			}

			return empty($user) ? FALSE : sc($user);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get traffic
	 *
	 * Get traffic fields for affiliate referrals
	 *
	 * @param string $options
	 * @param string $member_id
	 *
	 * @return bool|string
	 */
	public function get_traffic($options = '', $member_id = '')
	{
		$sort = $this->config->item(TBL_AFFILIATE_TRAFFIC, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		//set the cache file
		$cache = __METHOD__ . $options['md5'] . $member_id;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT p.*, c.username
                  FROM ' . $this->db->dbprefix(TBL_AFFILIATE_TRAFFIC) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c
				    ON p.member_id = c.member_id';

			if (!empty($member_id))
			{
				$sql .= ' WHERE p.member_id = \'' . (int)$member_id . '\'';
			}

			if (!empty($options['query']))
			{
				$this->dbv->validate_columns(array(TBL_AFFILIATE_TRAFFIC), $options['query']);

				$sql .= $options['and_string'];
			}

			$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}


			if ($q->num_rows() > 0)
			{
				$row = array(
					'rows'    => $q->result_array(),
					'total'   => $this->get_traffic_totals($options, $member_id),
					'success' => TRUE,
				);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get traffic totals
	 *
	 * Get the total rows from the affiliate traffic table
	 *
	 * @param        $options
	 * @param string $member_id
	 *
	 * @return bool
	 */
	public function get_traffic_totals($options, $member_id = '')
	{
		$sql = 'SELECT COUNT(p.traffic_id)
                  AS total
                  FROM ' . $this->db->dbprefix(TBL_AFFILIATE_TRAFFIC) . ' p';

		if (!empty($member_id))
		{
			$sql .= ' WHERE p.member_id = \'' . (int)$member_id . '\'';
		}

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(TBL_AFFILIATE_TRAFFIC, $options['query']);

			$sql .= $options['and_string'];
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$q = $q->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function insert_traffic_data($data = array())
	{
		$data = format_traffic_data($data);

		if (!$this->db->insert(TBL_AFFILIATE_TRAFFIC, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @return bool
	 */
	public function reset_module_group_id($id)
	{
		//reset the affiliate group IDs on each module if the admin deletes an affiliate group
		$tables = $this->db->list_tables();

		foreach ($tables as $table)
		{
			if (preg_match('/module_affiliate_marketing_*/', $table))
			{
				$fields = $this->db->list_fields($table);

				foreach ($fields as $field)
				{
					if ($field == 'affiliate_group')
					{
						$this->dbv->reset_id($table,
							'affiliate_group',
							$id,
							config_option('sts_affiliate_default_registration_group')
						);
					}
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 *
	 */
	public function reset_tracking_data()
	{
		//reset and delete all tracking data for the user on site
		$a = array('tracking_data', 'lifetime_tracking_data');

		$this->session->unset_userdata($a);

		//delete tracking cookie
		delete_cookie(config_option('tracking_cookie_name'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $username
	 * @param bool $overwrite
	 * @param string $tool_type
	 * @param string $tool_id
	 * @return bool
	 */
	public function set_tracking_data($username = '', $overwrite = FALSE, $tool_type = '', $tool_id = '')
	{
		//check blocks first
		$this->check_affiliate_blocks();

		//check if the tracking cookie is set
		if ($cookie = get_affiliate_cookie())
		{
			if (!empty($cookie['username']))
			{
				if (!config_enabled('sts_affiliate_overwrite_existing_cookie') && $overwrite == TRUE)
				{
					$username = $cookie['username'];
				}
			}
		}

		if (empty($username))
		{
			//check the affiliate link type
			if (config_option('sts_affiliate_link_type') == 'subdomain')
			{
				//check the subdomain
				$url = $this->input->server('HTTP_HOST');
				preg_match("/^([^\.]+)./", $url, $args);

				//make sure its not part of our restricted subdomains
				if (validate_subdomain($args[1]))
				{
					$username = $args[1];
				}
			}
		}

		//check direct product code
		if (CONTROLLER_METHOD == 'Product::details' || CONTROLLER_METHOD == 'Blog::post')
		{
			if (uri(4))
			{
				$username = url_title(uri(4));
			}
		}

		//check for affiliate store
		if (CONTROLLER_METHOD == 'Shop::id')
		{
			if (uri(2))
			{
				$username = url_title(uri(2));
			}
		}

		//check $_GET variable for page specific referrals
		if ($this->input->get(config_option('sts_affiliate_get_variable')))
		{
			$username = $this->input->get(config_option('sts_affiliate_get_variable'), TRUE);
			unset($_GET[config_option('sts_affiliate_get_variable')]);
		}

		//LAST RESORT! try checking the referral table for IP if no cookie is set
		if (empty($username) && config_enabled('sts_affiliate_enable_ip_address_tracking'))
		{
			$username = $this->get_tracking_by_ip($this->input->ip_address());
		}

		//we got a username - let's see if he's okay....
		if (!empty($username))
		{
			//looks like he's good to go..
			if ($row = $this->get_affiliate_data($username, 'username'))
			{
				$row['tool_type'] = $tool_type;
				$row['tool_id'] = $tool_id;

				//set tracking cookie for future use
				$row['cookie_data'] = set_tracking_cookie($row);
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function set_lifetime_sponsor($id = '')
	{
		//check if the user is logged in and we are set to use lifetime sponsors
		if (sess('user_logged_in') && config_enabled('sts_affiliate_lifetime_sponsor'))
		{
			$row = $this->get_affiliate_data($id, 'member_id');
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @param string $id
	 */
	public function update_sort_order($data = array(), $table = '', $id = 'id')
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (!$q = $this->db->where($id, (int)$v)
					->update($table, array('sort_order' => $k))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @return array
	 */
	public function validate_module($data = array(), $table = '')
	{
		$error = '';

		$this->form_validation->set_data($data);

		$row = $this->mod->get_module_details(valid_id($data['module_id']));

		//validate the module configuration settings...
		if (!empty($row['values']))
		{
			foreach ($row['values'] as $v)
			{
				$rule = 'trim|xss_clean|required';

				$lang = format_settings_label($v['key'], $row['module']['module_type'], $row['module']['module_folder']);

				switch ($v['type'])
				{
					case 'text':

						if (!empty($v['function']))
						{
							$rule .= '|' . trim($v['function']);
						}

						break;

					case 'dropdown':

						$options = array();
						foreach (options($v['function']) as $a => $b)
						{
							array_push($options, $a);
						}

						$rule .= '|in_list[' . implode(',', $options) . ']';

						break;
				}

				$this->form_validation->set_rules($v['key'], 'lang:' . $lang, $rule);
			}
		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
		}

		if (!empty($error))
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => $error,
			);
		}
		else
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function check_restrictions($data = array())
	{
		if (count ($data) > 0)
		{
			foreach ($data as $k =>$v)
			{
				if (!empty($v['affiliate_group']))
				{
					if (sess('affiliate_group') != $v['affiliate_group'])
					{
						unset($data[$k]);
					}
				}
			}
		}

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_referrals($options = '')
	{
		$sort = $this->config->item(TBL_AFFILIATE_TRAFFIC, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, m.username 
				FROM ' . $this->db->dbprefix(TBL_AFFILIATE_TRAFFIC) . ' p 
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                    ON p.member_id = m.member_id';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_AFFILIATE_TRAFFIC), $options['query']);

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
				'total'          => $this->dbv->get_table_totals($options, TBL_AFFILIATE_TRAFFIC),
				'debug_db_query' => $this->db->last_query(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 *
	 */
	protected function check_affiliate_blocks()
	{
		if (config_enabled('sts_affiliate_enable_traffic_blocks'))
		{
			//block IPs first
			$deny = explode("\n", trim($this->config->item('sts_system_block_affiliate_ip_addresses')));

			foreach ($deny as $ip)
			{
				$ip = trim($ip);
				if (!empty($ip) && strlen($ip) > 7)
				{
					if (preg_match("/$ip/", $_SERVER['REMOTE_ADDR']))
					{
						show_error(lang('affiliate_ip_address_blocked'));
					}
				}
			}

			//now check websites
			$deny = explode("\n", trim($this->config->item('sts_system_block_affiliate_websites')));

			foreach ($deny as $site)
			{
				$site = trim($site);
				$ref = $this->agent->referrer();

				if (!empty($ref) && !empty($site) && strlen($site) > 5)
				{
					if ($site == $ref)
					{
						show_error(lang('affiliate_website_blocked'));
					}
				}
			}
		}
	}
}

/* End of file Affiliate_marketing_model.php */
/* Location: ./application/models/Affiliate_marketing_model.php */