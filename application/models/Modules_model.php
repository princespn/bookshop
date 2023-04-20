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
class Modules_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'module_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $folder
	 * @return bool|false|string
	 */
	public function add($type = '', $folder = '')
	{
		$vars = array('module_type'        => $type,
		              'module_name'        => $this->config->item('module_name'),
		              'module_description' => $this->config->item('module_description'),
		              'module_folder'      => $folder,
		);

		if (!$this->db->insert(TBL_MODULES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param bool $status
	 * @return bool|false|string
	 */
	public function get_modules($type = '', $status = FALSE)
	{
		$sort = $this->config->item(TBL_MODULES, 'db_sort_order');

		if (!empty($type))
		{
			$this->db->where('module_type', $type);
		}

		if ($status == TRUE)
		{
			$this->db->where('module_status', '1');
		}

		$this->db->order_by($sort['column'], $sort['order']);

		if (!$q = $this->db->get(TBL_MODULES))
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
	 * @param bool $public
	 * @param string $type
	 * @param string $col
	 * @return bool|false|string
	 */
	public function get_module_details($id = '', $public = FALSE, $type = '', $col = 'module_id')
	{
		$this->db->where($col, $id);

		if (!empty($type))
		{
			$this->db->where('module_type', $type);
		}

		if ($public == TRUE)
		{
			$this->db->where('module_status', '1');
		}

		if (!$q = $this->db->get(TBL_MODULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row['module'] = $q->row_array();

			$row['values'] = $this->get_module_settings($row['module']['module_id'], $row['module']['module_type']);

			if (!empty($row['values']))
			{
				foreach ($row['values'] as $k => $v)
				{
					$row['values'][$k]['module_alias'] = format_settings_label($v['key'], $row['module']['module_type'], $row['module']['module_folder']);
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $type
	 * @return mixed
	 */
	public function get_module_settings($id = '', $type = '')
	{
		$this->db->where('settings_group', $id);
		$this->db->where('settings_module', $type);
		$this->db->order_by('settings_sort_order', 'ASC');

		if (!$q = $this->db->get(TBL_SETTINGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = init_config($q->result_array());

			return $a[$id];
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @param bool $total
	 * @return array
	 */
	public function get_module_folders($form = FALSE, $total = FALSE)
	{
		$a = $this->list_module_folders();

		if ($form == TRUE)
		{
			$c = array();
			foreach ($a as $b)
			{
				$c[$b] = lang($b);
			}

			return $c;
		}

		if ($total == TRUE)
		{
			$t = array();
			foreach ($a as $v)
			{
				$map = directory_map('./application/modules/' . $v, 1);
				$t[$v]['total'] = count($map);
				$t[$v]['modules'] = array();

				foreach ($map as $b)
				{
					if ($b == 'index.html')
					{
						continue;
					}
					$b = substr($b, 0, -1);

					if ($b == 'affiliate_marketing')
					{
						if ($this->config->item('sts_affiliate_enable_affiliate_marketing') == 0)
						{
							continue;
						}
					}
					if ($b == 'affiliate_payments')
					{
						if ($this->config->item('sts_affiliate_enable_affiliate_marketing') == 0)
						{
							continue;
						}
					}

					array_push($t[$v]['modules'], $b);
				}
			}

			return $t;
		}

		return $a;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $type
	 * @return array|bool
	 */
	public function get_rows($options = '', $type = '')
	{
		$sort = $this->config->item(TBL_MODULES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_MODULES);

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_MODULES), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$a = $this->list_module_folders('./application/modules/' . $type);

		$b = array(); //installed
		foreach ($a as $v)
		{
			$info = $this->read_module_config(APPPATH . 'modules/' . $type . '/' . $v . '/config/module_config.php');

			$b[$v] = array('info' => $info);

			if ($q->num_rows() > 0)
			{
				foreach ($q->result_array() as $d)
				{
					if ($d['module_folder'] == $v)
					{
						$b[$v]['install'] = $d;
					}
				}
			}
		}

		$row = array(
			'values'  => $b,
			'total'   => $this->dbv->get_table_totals($options, TBL_MODULES),
			'success' => TRUE,
		);


		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $path
	 * @return array
	 */
	public function list_module_folders($path = './application/modules/')
	{
		$a = array();
		$map = directory_map($path, 1);

		foreach ($map as $v)
		{
			if ($v == 'index.html')
			{
				continue;
			}
			$v = substr($v, 0, -1);

			if ($v == 'affiliate_marketing')
			{
				if ($this->config->item('sts_affiliate_enable_affiliate_marketing') == 0)
				{
					continue;
				}
			}
			if ($v == 'affiliate_payments')
			{
				if ($this->config->item('sts_affiliate_enable_affiliate_marketing') == 0)
				{
					continue;
				}
			}

			array_push($a, $v);
		}

		sort($a);

		return $a;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return bool|false|string
	 */
	public function list_modules($type = '')
	{
		$mods = directory_map('application/modules/' . $type, 1);

		$i = array();
		if (!$q = $this->db->where('module_type', $type)
			->select('module_folder')->get(TBL_MODULES)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $v)
			{
				array_push($i, $v['module_folder']);
			}

			$row = array();

			foreach ($mods as $b)
			{
				if (file_exists(APPPATH . 'modules/' . $type . '/' . $b . 'config/module_config.php'))
				{
					$b = substr($b, 0, -1);
					if (!in_array($b, $i))
					{
						$row[$b] = ($b);
					}
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $path
	 * @return mixed
	 */
	public function read_module_config($path)
	{
		$file_data = file_get_contents($path);

		preg_match('|Module Name:(.*)$|mi', $file_data, $name);
		preg_match('|Description:(.*)$|mi', $file_data, $description);

		$data['module_name'] = !empty($name[1]) ? trim($name[1]) : '';
		$data['module_description'] = !empty($description[1]) ? trim($description[1]) : '';

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $type
	 * @return bool
	 */
	public function remove_config($id = '', $type = '')
	{
		$this->db->where('module_id', $id);

		if (!$q = $this->db->delete(TBL_MODULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$this->db->where('settings_group', $id);
		$this->db->where('settings_module', $type);

		if (!$q = $this->db->delete(TBL_SETTINGS))
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
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_MODULES);

		//update module data
		$this->dbv->update(TBL_MODULES, $this->id, $vars);

		foreach ($data as $k => $v)
		{
			$this->update_module_setting($k, $v);
		}

		$row = array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $key
	 * @param string $value
	 * @return bool
	 */
	public function update_module_setting($key = '', $value = '')
	{
		$this->db->where('settings_key', $key);

		if (!is_array($value))
		{
			if (!$q = $this->db->update(TBL_SETTINGS, array('settings_value' => $value)))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param bool $module
	 * @return bool|false|string
	 */
	public function validate($data = array(), $module = TRUE)
	{
		$this->form_validation->set_data($data);

		$row = $this->get_module_details(valid_id($data['module_id']));

		//validate the module status, namen and description...
		if ($module == TRUE)
		{
			//get the list of fields required for this
			$required = $this->config->item(TBL_MODULES, 'required_input_fields');

			//now get the list of fields directly from the table
			$fields = $this->db->field_data(TBL_MODULES);

			//go through each field and
			foreach ($fields as $f)
			{
				//set the default rule
				$rule = 'trim|xss_clean|strip_tags';

				//if this field is a required field, let's set that
				if (is_array($required) && in_array($f->name, $required))
				{
					$rule .= '|required';
				}

				$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

				$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
			}
		}

		//validate the module configuration settings...
		if (!empty($row['values']))
		{
			foreach ($row['values'] as $v)
			{
				$rule = 'trim';

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

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Modules_model.php */
/* Location: ./application/models/Modules_model.php */