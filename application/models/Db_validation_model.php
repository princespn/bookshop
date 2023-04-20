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
class Db_validation_model extends CI_Model
{
	/**
	 * Calculate the views as set higher numbers in tag cloud using css
	 *
	 * @param array $data
	 * @return array
	 */
	public function calc_cloud($data = array())
	{
		$f = reset($data);

		$row = array();
		for ($i = 1; $i <= 4; $i++)
		{
			$row[$i] = ceil($f['count'] / $i);
		}

		foreach ($data as $k => $v)
		{
			if ($v['count'] >= $row[1])
			{
				$data[$k]['css'] = 'success';
			}
			elseif ($v['count'] < $row[1] && $v['count'] >= $row[2])
			{
				$data[$k]['css'] = 'warning';
			}
			elseif ($v['count'] < $row[2] && $v['count'] >= $row[3])
			{
				$data[$k]['css'] = 'danger';
			}
			else
			{
				$data[$k]['css'] = 'default';
			}
		}

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check captcha
	 *
	 * Run validation on the captcha if it is enabled
	 *
	 * @param string $str
	 * @return bool
	 */
	public function check_captcha($str = '')
	{
		$data = array('secret'   => config_item('sts_form_captcha_secret'),
		              'response' => $str,
		              'remoteip' => $this->input->ip_address(),
		);

		$row = use_curl(CAPTCHA_SERVER, $data);

		$vars = json_decode($row);

		if (isset($vars->success) && $vars->success == TRUE)
		{
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check start date
	 *
	 * Make sure that start date happens before
	 * the end date
	 *
	 * @param string $str
	 * @return bool
	 */
	public function check_start_date($str = '')
	{
		if ($this->input->post('end_date'))
		{
			$str = empty($str) ? $this->input->post($this->id) : $str;

			$a = strtotime($str);

			$b = strtotime($this->input->post('end_date'));

			if ($b < $a)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check unique
	 *
	 * Check to make sure that the value is unique
	 *
	 * @param string $id
	 * @param string $table
	 * @param string $col
	 * @return bool
	 */
	public function check_unique($id = '', $table = '', $col = '')
	{
		if (!$q = $this->db->where($col, $id)->get($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? TRUE : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean
	 *
	 * Clean the default form values
	 *
	 * @param array $data
	 * @param string $table
	 * @param bool $strip_tags
	 * @param string $optional_id
	 * @return array
	 */
	public function clean($data = array(), $table = '', $strip_tags = FALSE, $optional_id = '')
	{
		$filter = $this->config->item('dbi_filter');

		foreach ($data as $key => $value)
		{
			//check if password needs to be updated or not
			if (!empty($key) && $key == 'password') //for users
			{
				if (!empty($value))
				{
					if (empty($data['encrypted']))
					{
						$value = password_hash($value, PASSWORD_DEFAULT);
					}

					$data[$key] = xss_clean($value);
				}
				else
				{
					array_push($filter, $key);
				}
			}
			elseif ($key == 'apassword') //for admins
			{
				if (!empty($value))
				{
					$data[$key] = password_hash($value, PASSWORD_DEFAULT);
				}
				else
				{
					unset($data[$key]);
					array_push($filter, $key);
				}
			}
			elseif ($key == $this->config->item('csrf_token_name')) //remove csrf token
			{
				unset($data[$key]);
			}
		}

		foreach ($data as $key => $value)
		{
			//remove keys that are not in the database
			if (in_array($key, $this->config->item('dbi_filter')))
			{
				unset($data[$key]);
			}
			else
			{
				in_array($key, $this->config->item('dbi_arrays')) ? '' : $data[$key] = $this->input_clean($value, $strip_tags);
			}
		}

		//remove optional ID if set
		if (!empty($optional_id))
		{
			unset($data[$optional_id]);
		}

		if (!empty($table))
		{
			$data = $this->filter_columns($data, $table);
		}

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * clean each input field
	 *
	 * <p>This function will to go through a db field, check if there's a posted value for it, then sanitize it.
	 * if there's no posted value, set it to a default one </p>
	 *
	 * @param array $data usually this is the $_POST array
	 * @param string $meta meta data from each database field
	 * @param string $field field name we are cleaning
	 * @param string $value default value if there is no $_POST value
	 * @param string $func function for sanitizing text areas
	 *
	 * @return mixed|string
	 */
	public function clean_field($data = array(), $meta = '', $field = '', $value = '', $func = 'xss_clean')
	{
		switch ($meta->type)
		{
			case 'varchar':
				$value = !empty($data[$field]) ? xss_clean(strip_tags($data[$meta->name])) : $value;
				break;

			case 'text':
			case 'longtext':
				$value = !empty($data[$field]) ? $func($data[$meta->name]) : $value;
				break;

			case 'int':
			case 'enum':
				$value = !empty($data[$field]) ? (int)($data[$meta->name]) : empty($value) ? $meta->default : $value;
				break;

			case 'decimal':
			case 'float':
				$value = !empty($data[$field]) ? (float)($data[$meta->name]) : empty($value) ? $meta->default : $value;
				break;
		}

		return $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Create
	 *
	 * Create a new row in the specified table using the supplied form values
	 *
	 * @param string $table
	 * @param array $data
	 * @return array
	 */
	public function create($table = '', $data = array())
	{
		$data = $this->clean($data, $table);

		if (!$this->db->insert($table, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'data'     => $data,
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete
	 *
	 * Deletes the value from the specified table
	 *
	 * @param string $table
	 * @param string $key
	 * @param string $id
	 * @return bool|string
	 */
	public function delete($table = '', $key = '', $id = '')
	{
		if (!$q = $this->db->where($key, $id)->get($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row['data'] = $q->row_array();

			$this->db->where($key, valid_id($id));

			if (!$this->db->delete($table))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row['success'] = TRUE;
			$row['msg_text'] = lang('record_deleted_successfully');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Db sort order
	 * this changes the sort order in the table designated so that all required sorting is in sequential order
	 *
	 * @param string $table table to sort
	 * @param string $id unique id
	 * @param string $order_by sort order field to use
	 * @param string $where if there is a where clause, add it
	 *
	 * @return bool
	 */
	public function db_sort_order($table = '', $id = '', $order_by = '', $where = '')
	{
		if ($this->config->item('disable_db_autosorting') == TRUE)
		{
			return;
		}

		$this->db->order_by($order_by, 'ASC');

		if (!empty($where))
		{
			$this->db->where($where);
		}

		$q = $this->db->get($table);

		$total = $q->num_rows();
		$i = 1;
		if ($total > 1)
		{
			foreach ($q->result_array() as $row)
			{
				$update = array($order_by => $i);
				$this->db->where($id, $row[$id]);

				if ($this->db->update($table, $update))
				{
					//log success
					log_message('info', 'sort order changed for ' . $table);
				}

				$i++;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Filter columns
	 *
	 * Filter out columns that are not in the table
	 *
	 * @param array $data
	 * @param string $table
	 * @return array
	 */
	public function filter_columns($data = array(), $table = '')
	{
		//get the fields from the table
		$fields = $this->db->list_fields($table);

		foreach ($data as $key => $value)
		{
			if (!in_array($key, $fields))
			{
				unset($data[$key]);
			}
		}

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get all
	 *
	 * Get all the records from a specified table
	 *
	 * @param string $table
	 * @param string $sort_column
	 * @param string $sort_by
	 * @return bool|string
	 */
	public function get_all($table = '', $sort_column = '', $sort_by = 'ASC')
	{
		if (!empty($sort_column))
		{
			$this->db->order_by($sort_column, $sort_by);
		}

		if (!$q = $this->db->get($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get field
	 *
	 * Get a specific field from the table
	 *
	 * @param string $table
	 * @param string $key
	 * @param string $id
	 * @param string $col
	 * @return bool|string
	 */
	public function get_field($table = '', $key = '', $id = '', $col = '')
	{
		$this->db->where($key, valid_id($id));

		if (!empty($col))
		{
			$this->db->select($col);
		}

		if (!$q = $this->db->get($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @return mixed
	 */
	public function get_fields($table = '')
	{
		$fields = $this->db->list_fields($table);

		switch ($table)
		{
			case TBL_MEMBERS_PERMISSIONS:

				$exclude = array('id', 'member_id');

				break;
		}

		if (!empty($exclude))
		{
			foreach ($exclude as $k => $a)
			{
				unset($fields[$k]);
			}
		}

		return $fields;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $table
	 * @return bool|string
	 */
	public function get_rows($options = '', $table = '')
	{
		$sort = $this->config->item($table, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		if (!empty($options['query']))
		{
			$this->validate_columns(array($table), $options['query']);

			foreach ($options['query'] as $k => $v)
			{
				if ($k == 'order' OR $k == 'column')
				{
					continue;
				}
				$this->db->where($k, $v);
			}
		}

		if (isset($options['sort_column']) && isset($options['sort_order']))
		{
			$this->db->order_by($options['sort_column'], $options['sort_order']);
		}
		if (isset($options['limit']) && isset($options['offset']))
		{
			$q = $this->db->get($table, $options['limit'], $options['offset']);
		}
		else
		{
			$q = $this->db->get($table);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->get_table_totals($options, $table),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $table
	 *
	 * @return mixed
	 */
	public function get_table_totals($options = '', $table = '')
	{
		$sql = 'SELECT COUNT(*) AS total
                    FROM ' . $this->db->dbprefix($table) . ' p';

		if (!empty($options['query']))
		{
			$sql .= $options['where_string'];
		}

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
		{
			$q = $query->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $sql
	 * @param string $var
	 * @param bool $count
	 * @return mixed
	 */
	public function get_query_total($sql = '', $var = 'total', $count = FALSE)
	{
		if (!$a = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = $a->row_array();

		return $count == TRUE ? $a->num_rows() : $row[$var];
	}

	// ------------------------------------------------------------------------

	/**
	 * * query a database table for specific row
	 *
	 * @param string $table
	 * @param string $key
	 * @param string $id
	 *
	 * @return bool|string
	 */
	public function get_record($table = '', $key = '', $id = '', $public = FALSE, $str = FALSE)
	{
		$sql = 'SELECT *';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $key . '
				        FROM ' . $this->db->dbprefix($table) . ' p
				        WHERE p.' . $key . ' < ' . (int)valid_id($id) . '
				        ORDER BY `' . $key . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $key . '
				        FROM ' . $this->db->dbprefix($table) . ' p
				        WHERE p.' . $key . ' > ' . (int)valid_id($id) . '
				        ORDER BY `' . $key . '` ASC LIMIT 1)
				        AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix($table) . ' p
                    WHERE p.' . $key . '= \'' . valid_id($id, $str) . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param string $key
	 * @param string $id
	 * @return bool|string
	 */
	public function get_names($table = '', $key = '', $id = '')
	{
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id = ' .
			$this->db->dbprefix($table) . '.language_id', 'left');

		if (!$q = $this->db->where($key, $id)->get($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $html
	 *
	 * @return mixed
	 */
	public function input_clean($html = '', $strip_tags = FALSE)
	{
		if (!is_array($html))
		{
			if (config_enabled('allow_programming_codes_in_text'))
			{
				return $html;
			}

			if ($strip_tags == TRUE)
			{
				$html = strip_tags($html);

				$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
			}

			return $html;
		}

		return $html;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function rec($data = array())
	{
		//record it in the database log
		$user = !$this->session->admin['username'] ? $this->session->username : $this->session->admin['username'];

		if (!$this->db->insert(TBL_TRANSACTIONS, array('ip'      => $this->input->ip_address(),
		                                               'user'    => empty($user) ? 'system' : $user,
		                                               'message' => is_var($data, 'msg', FALSE, 'system_updated_successfully'),
		                                               'level'   => is_var($data, 'level', FALSE, 'info'),
		                                               'method'  => is_var($data, 'method', FALSE, 'system'),
		                                               'vars'    => is_var($data, 'vars') ? is_array($data['vars']) ? serialize($data['vars']) : $data['vars'] : '')))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//log for debug
		log_message('debug',  is_var($data, 'msg', FALSE, 'system_updated_successfully'));

		//send email alert if set
		if (!empty($data['email']))
		{
			$data['event'] =  is_var($data, 'msg', FALSE, 'system_updated_successfully');
			$this->mail->send_email_events($data);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @return string
	 */
	public function reset_data($table = '')
	{
		if (!$this->db->empty_table($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => $table . ' ' . lang('table_truncated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param string $key
	 * @param string $id
	 * @param string $default
	 */
	public function reset_id($table = '', $key = '', $id = '', $default = '0')
	{
		if (!$this->db->where($key, $id)->update($table, array($key => $default)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $vars
	 * @return string
	 */
	public function update_status($vars = array())
	{
		//get the table status first
		$q = $this->db->select($vars['type'])->where($vars['key'], $vars['id'])->get($vars['table']);

		$row = $q->row_array();

		$new_status = $row[$vars['type']] == '1' ? '0' : '1';
		if (!$this->db->where($vars['key'], $vars['id'])
			->update($vars['table'], array($vars['type'] => $new_status))
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $vars['id'],
			'msg_text' => $vars['table'] . ' ' . lang('id') . ' ' . $vars['id'] . ' '  .lang('updated_successfully'),
			'vars' => $vars,
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param array $data
	 * @param string $key
	 * @param string $sort
	 */
	public function update_sort_order($table = '', $data = array(), $key = '', $sort = 'sort_order')
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (!$q = $this->db->where($key, $v)->update($table, array($sort => $k)))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param string $key
	 * @param array $data
	 * @return array
	 */
	public function update($table = '', $key = '', $data = array())
	{
		$data = $this->clean($data, $table);

		$this->db->where($key, $data[$key]);
		if (!$this->db->update($table, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data[$key],
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * update counter stats for each table
	 *
	 * @param array $vars
	 * @return bool
	 */
	public function update_count($vars = array())
	{
		$sql = 'UPDATE `' . $this->db->dbprefix($vars['table']) . '`
                SET `' . $vars['field'] . '` = ' . $vars['field'] . ' + 1
                WHERE `' . $vars['key'] . '` = \'' . $vars['id'] . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Run form validation on input
	 *
	 * Run form validation on POST input fields based on
	 * config file and database fields
	 *
	 * @param string $table
	 * @param string $valid
	 * @param array $data
	 *
	 * @return bool|string
	 */
	public function validate($table = '', $valid = '', $data = array(), $escape = TRUE)
	{
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		if (!empty($valid))
		{
			$required = is_array($valid) ? $valid : $this->config->item($valid, 'required_input_fields');
		}

		//now get the list of fields directly from the table
		$fields = $this->db->field_data($table);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = $escape == TRUE ? 'trim|xss_clean' : 'trim';

			//if this field is a required field, let's set that
			if (!empty($required))
			{
				if (is_array($required) && in_array($f->name, $required))
				{
					$rule .= '|required';
				}
			}

			$rule .= generate_db_rule($f->type, $f->max_length, TRUE, $f->name);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->validated($data, $escape),
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
	 * Validate columns
	 *
	 * Validate the columns that are in each table,
	 * make sure they are set for the table
	 *
	 * @param array $tables
	 * @param array $data
	 */
	public function validate_columns($tables = array(), $data = array())
	{
		$rows = array();
		if (is_array($tables))
		{
			foreach ($tables as $t)
			{
				$fields = $this->db->list_fields($t);

				foreach ($fields as $f)
				{
					array_push($rows, $f);
				}
			}

			foreach ($data as $k => $v)
			{
				$key = explode('.', $k);

				$n = !empty($key[1]) ? $key[1] : $key[0];

				if (in_array($n, $this->config->item('query_type_filter')))
				{
					continue;
				}

				if ($n == config_option('sts_affiliate_get_variable'))
				{
					continue;
				}

				if (!in_array($n, $rows))
				{
					log_error('error', 'invalid query fields');
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param bool $html_escape
	 * @return array
	 */
	public function validated($data = array(), $html_escape = TRUE)
	{
		foreach ($data as $k => $v)
		{
			//clean values if its a string
			if (!is_array($v))
			{
				$data[$k] = set_value($k, '', $html_escape);
			}
		}

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate a specific field for TRUE / FALSE
	 *
	 * this validates the field to see if there is already
	 * a corresponding value that is the same in the db
	 *
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 * @param string $id_key
	 * @param string $id
	 *
	 * @return bool
	 */
	public function validate_field($table = '', $field = '', $value = '', $id_key = '', $id = '')
	{
		//check for unique field
		$this->db->where($field, $value);

		//if updating only
		if (!empty($id))
		{
			//do not include the current user ID
			$this->db->where($id_key . ' !=', $id);
		}

		$q = $this->db->get($table);

		return $q->num_rows() > 0 ? TRUE : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @param string $table
	 * @param string $column
	 * @return string
	 */
	public function generate_permalink($str = '', $table = '', $column = '')
	{
		$str = url_title(filter_stop_words(strtolower($str)));
		$url = empty($str) ? random_string('alpha', 8) : $str;
		$url = strtolower($str);
		while ($this->dbv->check_unique($url, $table, $column))
		{
			$url = $str;
			$url .= '-' . rand(1, 10000);
		}

		return $url;
	}
}

/* End of file Db_validation_model.php */
/* Location: ./application/models/Db_validation_model.php */