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
class Import_product_downloads_model extends Data_import_model
{
	protected $lang = array();

	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_data_import_product_downloads_use_server_path',
			'settings_value'      => '0',
			'settings_module'     => 'data_import',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_import_product_downloads_server_file_path',
			'settings_value'      => config_option('sts_data_import_folder'),
			'settings_module'     => 'data_import',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_import_product_downloads_delimiter',
			'settings_value'      => 'comma',
			'settings_module'     => 'data_import',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'file_delimiters',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_import_product_downloads_generate_new_ids',
			'settings_value'      => '1',
			'settings_module'     => 'data_import',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_import_product_downloads_first_row_column_names',
			'settings_value'      => '1',
			'settings_module'     => 'data_import',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_installed_successfully'),
		);
	}

	public function uninstall($id = '')
	{
		//remove settings from database
		$this->mod->remove_config($id, 'data_import');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function validate_import_module($data = array())
	{
		//validate the file path
		if (!file_exists($data['module_data_import_product_downloads_server_file_path']))
		{
			$row = array('error'    => TRUE,
			             'msg_text' => lang('invalid_import_file'),
			);
		}
		else
		{
			$row = array('success' => TRUE,
			             'data'    => $data);
		}

		return $row;
	}

	public function update_module($data = array())
	{
		//update module config settings
		foreach ($data as $k => $v)
		{
			$this->mod->update_module_setting($k, $v);
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('system_updated_successfully'));
	}

	public function generate_fields()
	{
		if ($this->config->item('module_import_memory_limit'))
		{
			//we'll try and set a higher memory limit if possible...
			@ini_set("memory_limit", $this->config->item('module_import_memory_limit'));
		}

		//generate the table field names for mapping
		$row['fields'] = $this->generate_field_names();

		//let's read the file now...
		$csv = array_map('read_csv', file(config_option('module_data_import_product_downloads_server_file_path')));

		$row['values'] = empty($csv) ? '' : $csv[0];

		return empty($row) ? FALSE : $row;
	}

	public function generate_field_names()
	{
		$input_fields = array();

		foreach ($this->config->item('module_import_tables') as $v)
		{
			$fields = $this->db->list_fields($v);

			foreach ($fields as $f)
			{
				if (config_option('module_data_import_product_downloads_generate_new_ids') == 1)
				{
					if ($f == 'product_id')
					{
						continue;
					}
				}

				$g = $v . '.' . $f;
				if (!in_array($g, $input_fields))
				{
					//remove any primary keys
					if (!in_array($g, $this->config->item('module_exclude_keys')))
					{
						array_push($input_fields, $g);
					}
				}
			}
		}

		$a = array('none' => lang('none'));
		foreach ($input_fields as $v)
		{
			$a[ $v ] = $v;
		}

		return $a;
	}

	public function do_import($data = array())
	{
		$error = '';
		$total = 0;

		if ($this->config->item('module_import_memory_limit'))
		{
			//we'll try and set a higher memory limit if possible...
			@ini_set("memory_limit", $this->config->item('module_import_memory_limit'));
		}

		//set time limit
		if ($this->config->item('module_import_time_limit'))
		{
			@set_time_limit($this->config->item('module_import_time_limit'));
		}

		//let's read the file now...
		$lines = array_map('str_getcsv', file(config_option('module_data_import_product_downloads_server_file_path')));

		if (!empty($lines))
		{
			$this->lang = get_languages(FALSE, FALSE);

			//first get the fields for all the tables
			foreach (config_option('module_import_tables') as $v)
			{
				$fields[$v] = $this->db->field_data($v);
			}

			foreach ($lines as $a => $b)
			{
				//remove the column names first...
				if ($a == 0)
				{
					if (config_option('module_data_import_product_downloads_first_row_column_names') == 1)
					{
						continue;
					}
				}

				$m = array();
				foreach ($data['fields'] as $k => $v)
				{
					if ($v != 'none')
					{
						list($table, $field) = explode('.', $v);

						$m[ $table ][ $field ] = $b[ $k ];
					}
				}

				//now validate it for each table
				$row = $this->validate_fields($m, $fields);

				if (!empty($row['success']))
				{
					$m = $row['data'];

					$row = $this->import_fields($m);

					if (!empty($row['success']))
					{
						$total++;
					}
				}
				else
				{
					$error .= $row['msg_text'];
				}
			}
		}

		return array('success'  => TRUE,
		             'total'    => $total,
		             'data'     => array('total' => $total,
		                                 'error' => $error),
		             'msg_text' => $total . ' ' . lang('imported_successfully'));
	}

	public function import_fields($data = array())
	{
		//add the products table first and get the product_id

		$prod = $this->dbv->create(TBL_PRODUCTS_DOWNLOADS, $data[ TBL_PRODUCTS_DOWNLOADS ]);

		foreach ($data as $k => $v)
		{
			$v['download_id'] = $prod['id'];

			switch ($k)
			{
				case TBL_PRODUCTS_DOWNLOADS_NAME:

					//add for each language id
					foreach ($this->lang as $a)
					{
						$v['language_id'] = $a['language_id'];

						$row = $this->dbv->create($k, $v);
					}

					break;
			}
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('imported_successfully'),
		);
	}

	public function validate_fields($data = array(), $fields = array())
	{
		$error = '';

		foreach ($data as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);

			switch ($k)
			{
				case TBL_PRODUCTS_DOWNLOADS:

					//go through each field and
					foreach ($fields[$k] as $f)
					{
						//set the default rule
						$rule = 'trim|xss_clean';
						$custom_lang = '';

						switch ($f->name)
						{
							case 'download_id':

								if (config_option('module_data_import_product_downloads_generate_new_ids') == '0')
								{
									$rule .= '|check_download_id';

									$custom_lang = array(
										'check_download_id' => '%s ' . $v['product_id'] . ' ' . lang('already_exists'),
									);
								}

								break;

							case 'date_expires':

								$rule .= '|check_import_end_time';

								break;

							case 'date_added':

								$rule .= '|check_import_start_time';

								break;

							default:

								$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

								break;
						}

						$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule, $custom_lang);
					}

					if ($this->form_validation->run())
					{
						$data[ $k ] = $this->dbv->validated($v);
					}
					else
					{
						$error .= validation_errors();
					}

				break;

				case TBL_PRODUCTS_DOWNLOADS_NAME:

					//go through each field and
					foreach ($fields[$k] as $f)
					{
						//set the default rule
						$rule = 'trim|xss_clean';
						$custom_lang = '';

						switch ($f->name)
						{
							case 'download_name':

								$rule .= '|required';

								break;

							default:

								$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

								break;
						}

						$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule, $custom_lang);
					}

					if ($this->form_validation->run())
					{
						$data[ $k ] = $this->dbv->validated($v);
					}
					else
					{
						$error .= validation_errors();
					}

					break;

				

				default:

					//go through each field and
					foreach ($fields[$k] as $f)
					{
						//set the default rule
						$rule = 'trim|xss_clean';
						$custom_lang = '';

						switch ($f->name)
						{
							default:

								$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

								break;
						}

						$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule, $custom_lang);
					}

					if ($this->form_validation->run())
					{
						$data[ $k ] = $this->dbv->validated($v);
					}
					else
					{
						$error .= validation_errors();
					}

					break;
			}
		}

		if (!empty($error))
		{
			$row = array('error'    => TRUE,
			             'msg_text' => '<div class="error-box alert alert-danger">' . $error . '</div>',
			);
		}
		else
		{
			$row = array('success' => TRUE,
			             'data'    => $data,
			);
		}

		return $row;
	}


}

/* End of file Import_product_downloads_model.php */
/* Location: ./application/modules/data_import/products/models/Import_product_downloads_model.php */