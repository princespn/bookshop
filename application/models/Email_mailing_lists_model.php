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
class Email_mailing_lists_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'list_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $email
	 * @param array $data
	 * @param string $lang_id
	 * @return bool
	 */
	public function add_user($id = '', $email = '', $data = array(), $lang_id = '1')
	{
		if (!empty($id) && !empty($email))
		{
			//check if the user is already subscribed
			if (!$row = $this->is_subscribed($id, $email))
			{
				$vars = format_mailing_list_member_data($id, $email, $data, $lang_id);

				if (!$this->db->insert(TBL_MEMBERS_EMAIL_MAILING_LIST, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return bool|false|string
	 */
	public function ajax_search($term = '')
	{
		$this->db->like('list_name', $term);
		$this->db->select('list_id, list_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_EMAIL_MAILING_LISTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}
		else
		{
			$row[0] = array('list_id'   => '0',
			             'list_name' => lang('none'));
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function delete($id = '')
	{
		if ($id != config_option('sts_members_default_mailing_list'))
		{
			if (!$this->db->where($this->id, $id)->delete(TBL_EMAIL_MAILING_LISTS))
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
	 * @param string $id
	 * @return mixed
	 */
	public function get_list_totals($id = '')
	{
		$this->db->where($this->id, $id);

		return $this->db->count_all_results(TBL_MEMBERS_EMAIL_MAILING_LIST);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_module_rows($options = '', $public = FALSE)
	{
		$sort = $this->config->item(TBL_MODULES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		if ($public == TRUE)
		{
			$this->db->where('module_status', '1');
		}

		$this->db->where('module_type', 'mailing_lists');
		$this->db->order_by($options['sort_column'], $options['sort_order']);

		if (!$q = $this->db->get(TBL_MODULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $q->num_rows(),
				'success' => TRUE,
			);
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
		$sort = $this->config->item(TBL_EMAIL_MAILING_LISTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *, 
				(SELECT COUNT(list_id) 
					FROM ' . $this->db->dbprefix(TBL_EMAIL_FOLLOW_UPS) . '
			        WHERE p.list_id = ' . $this->db->dbprefix(TBL_EMAIL_FOLLOW_UPS) . '.list_id) 
			    AS follow_ups ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(member_id) 
						FROM ' . $this->db->dbprefix(TBL_MEMBERS_EMAIL_MAILING_LIST) . '
			                WHERE p.list_id = ' . $this->db->dbprefix(TBL_MEMBERS_EMAIL_MAILING_LIST) . '.list_id) 
			            AS total ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_EMAIL_MAILING_LISTS) . ' p';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_EMAIL_MAILING_LISTS), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'];

		$q = $this->db->query($sql);


		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_EMAIL_MAILING_LISTS),
				'success' => TRUE,
			);

			return sc($row);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_subscribers($options = '', $id = '')
	{
		$sort = $this->config->item(TBL_MEMBERS_EMAIL_MAILING_LIST, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, 
						c.fname, 
						c.lname, 
						c.username,
						e.follow_up_name
			    FROM ' . $this->db->dbprefix(TBL_MEMBERS_EMAIL_MAILING_LIST) . ' p
			     LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c
				    ON (p.member_id = c.member_id) 
				 LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_FOLLOW_UPS) . ' e
				    ON (p.list_id = e.list_id) 
				    AND (p.sequence_id = e.sequence) ';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_MEMBERS_EMAIL_MAILING_LIST), $options['query']);

			$sql .= $options['where_string'];
		}
		elseif (!empty($id))
		{
			$sql .= ' WHERE p.list_id = \'' . valid_id($id) . '\'';
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
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_MEMBERS_EMAIL_MAILING_LIST),
				'success' => TRUE,
			);

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
		$this->db->join(TBL_EMAIL_MAILING_LISTS,
			$this->db->dbprefix(TBL_EMAIL_MAILING_LISTS) . '.list_id = ' .
			$this->db->dbprefix(TBL_MEMBERS_EMAIL_MAILING_LIST) . '.list_id', 'left');

		if (!$q = $this->db->where('member_id', $id)->get(TBL_MEMBERS_EMAIL_MAILING_LIST))
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
	 * @param string $email
	 * @return bool|false|string
	 */
	public function get_user_subscriptions($email = '')
	{
		$this->db->where('email_address', valid_id($email, 'primary_email'));

		$this->db->join(TBL_EMAIL_MAILING_LISTS,
			$this->db->dbprefix(TBL_EMAIL_MAILING_LISTS) . '.list_id = ' .
			$this->db->dbprefix(TBL_MEMBERS_EMAIL_MAILING_LIST) . '.list_id', 'left');

		if (!$q = $this->db->get(TBL_MEMBERS_EMAIL_MAILING_LIST))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $email
	 * @return bool
	 */
	public function is_subscribed($id = '', $email = '')
	{
		$this->db->where('list_id', $id);
		$this->db->where('email_address', $email);

		if (!$q = $this->db->get(TBL_MEMBERS_EMAIL_MAILING_LIST))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return ($q->num_rows() > 0) ? TRUE : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_subscriber_update($data = array())
	{
		foreach ($data as $k => $v)
		{
			$vars = array('sequence_id' => $v,
			              'eml_id'      => $k);

			$this->dbv->update(TBL_MEMBERS_EMAIL_MAILING_LIST, 'eml_id', $vars);
		}

		$row = array(
			'data'     => $vars,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param array $data
	 * @param string $list_id
	 */
	public function mass_update($type = 'add', $data = array(), $list_id = '')
	{
		if (!empty($data))
		{
			foreach ($data as $v)
			{
				//get the member details first
				if ($row = $this->mem->get_details($v))
				{
					if ($type == 'add')
					{
						//check if the user is subscribed,
						$this->add_user($list_id, $row['primary_email'], $row);
					}
					else
					{
						//delete him from the list
						$this->db->where('list_id', $list_id);
						$this->db->where('member_id', $v);

						if (!$this->db->delete(TBL_MEMBERS_EMAIL_MAILING_LIST))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}
					}
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $email
	 * @return bool
	 */
	public function remove_user($id = '', $email = '')
	{
		if (!empty($id) && !empty($email))
		{
			$this->db->where('list_id', $id);
			$this->db->where('email_address', $email);

			if (!$this->db->delete(TBL_MEMBERS_EMAIL_MAILING_LIST))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $email
	 * @param array $data
	 * @param string $member_id
	 * @return bool
	 */
	public function update_user($id = '', $email = '', $data = array(), $member_id = '')
	{
		if (!empty($id) && !empty($email))
		{
			$this->db->where('list_id', $id);
			$this->db->where('email_address', $email);

			if (!empty($member_id))
			{
				$this->db->where('member_id', $member_id);
			}

			if (!$this->db->update(TBL_MEMBERS_EMAIL_MAILING_LIST, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @return bool|false|string
	 */
	public function update_module_lists($data = array(), $table = '')
	{
		//check if the list is already in the table
		if (!empty($data) && !empty($table))
		{
			foreach ($data as $v)
			{
				if (!empty($v['id']))
				{
					$row = $this->dbv->update($table, 'id', $v);
				}
				elseif (!empty($v['external_id']))
				{
					$row = $this->dbv->create($table, $v);
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @return bool|false|string
	 */
	public function update_module($data = array(), $table = '')
	{
		foreach ($data as $k => $v)
		{
			if (!is_array($v))
			{
				$this->mod->update_module_setting($k, $v);
			}
		}

		//update mailing list mappings in the module table...
		if (!empty($data['list']))
		{
			$this->update_module_lists($data['list'], $table);
		}

		$row = array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate_module($data = array())
	{
		$this->form_validation->set_data($data);

		$error = '';

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

		//validate the lists
		if (!empty($data['list']))
		{
			foreach ($data['list'] as $k => $v)
			{
				$this->form_validation->reset_validation();
				$this->form_validation->set_data($v);
				$this->form_validation->set_rules('external_id', 'lang:list_id', 'trim|strip_tags|xss_clean');
				$this->form_validation->set_rules('list_id', 'lang:list_id', 'trim|integer');
				$this->form_validation->set_rules('id', 'lang:id', 'trim|integer');

				if ($this->form_validation->run())
				{
					$data['list'][$k] = $this->dbv->validated($v);
				}
				else
				{
					$error .= validation_errors();
				}
			}
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
}

/* End of file Email_mailing_lists_model.php */
/* Location: ./application/models/Email_mailing_lists_model.php */