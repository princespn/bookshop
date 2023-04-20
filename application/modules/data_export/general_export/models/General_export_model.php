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
class General_export_model extends Data_export_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_data_export_general_export_table',
			'settings_value'      => config_option('sts_data_import_folder'),
			'settings_module'     => 'data_export',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => 'module_export_tables',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_export_general_export_delimiter',
			'settings_value'      => 'comma',
			'settings_module'     => 'data_export',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => 'file_delimiters',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_export_general_export_start_date',
			'settings_value'      => get_time(now(), TRUE),
			'settings_module'     => 'data_export',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'start_date_to_sql',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_export_general_export_end_date',
			'settings_value'      => get_time(now(), TRUE),
			'settings_module'     => 'data_export',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => 'end_date_to_sql',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_data_export_general_export_first_row_column_names',
			'settings_value'      => '1',
			'settings_module'     => 'data_export',
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
		$this->mod->remove_config($id, 'data_export');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function validate_export_module($data = array())
	{
		$row = $this->mod->validate($data, FALSE);

		if (!empty($row['success']))
		{
			$row = array('success' => TRUE,
			             'data'    => $row['data']);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => $row['msg_text'],
			);
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

	public function do_export()
	{
		if (config_option('module_export_memory_limit'))
		{
			//we'll try and set a higher memory limit if possible...
			@ini_set("memory_limit", config_option('module_export_memory_limit'));
		}

		//set time limit
		if (config_option('module_export_time_limit'))
		{
			@set_time_limit(config_option('module_export_time_limit'));
		}

		$rows = $this->run_query(config_option('module_data_export_general_export_table'));

		if (!empty($rows))
		{
			$data = csv_from_result($rows, set_delimiter(config_option('module_data_export_general_export_delimiter')), "\r\n");

			$ext = config_option('module_data_export_general_export_delimiter') == 'tab' ? 'txt' : 'csv';

			$row = array('success'   => TRUE,
			             'file_name' => config_option('module_data_export_general_export_table') . '-' . config_option('module_export_file_name') . '-' . time() . '.' . $ext,
			             'data'      => $data,
			             'msg_text'  => lang('file_exported_successfully'));
		}

		return empty($row) ? FALSE : $row;
	}

	protected function run_query($table = '')
	{

		switch ($table)
		{
			case TBL_MEMBERS:

				$sql = 'SELECT  p.*, b.*,c.*, g.*, h.*, 
						a.sponsor_id,
						j.region_name,
						j.region_code, 
						k.country_name,
						k.country_iso_code_3,
 					    d.group_id AS affiliate_group,
 					    e.group_id AS blog_group,
 					    f.group_id AS discount_group,
 					    p.member_id AS member_id,
 					DATE_FORMAT(p.date,\'' . config_option('sql_date_format') . '\')
                        AS date,
                    DATE_FORMAT(p.birthdate,\'' . config_option('sql_date_format') . '\')
                        AS birthdate    
					FROM ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' a
                        ON p.member_id = a.member_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' b
                        ON p.member_id = b.member_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ALERTS) . ' c
                        ON p.member_id = c.member_id   
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_AFFILIATE_GROUPS) . ' d
                        ON p.member_id = d.member_id     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_BLOG_GROUPS) . ' e
                        ON p.member_id = e.member_id     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_DISCOUNT_GROUPS) . ' f
                        ON p.member_id = f.member_id               
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ALERTS) . ' g
                        ON p.member_id = g.member_id      
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ADDRESSES) . ' h
                        ON p.member_id = h.member_id   
                    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' j 
                        ON h.state = j.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' k 
                        ON h.country = k.country_id           
                    WHERE p.date > \'' . config_option('module_data_export_general_export_start_date') . '\'                                        AND  p.date < \'' . config_option('module_data_export_general_export_end_date') . '\'  
                        GROUP BY p.member_id
                        ORDER BY ' .
							$this->config->item($table,'module_export_sort_column') . ' ' .
							config_option('module_column_sort_order');

				break;

			case TBL_INVOICES:
				
				$sql = 'SELECT  p.*, 
							a.amount AS tax,
							b.amount AS points,
							c.amount AS sub_total,
							d.region_name AS customer_state_name,
							e.country_name AS customer_country_name,
							f.region_name AS shipping_state_name,
							g.country_name AS shipping_country_name,
 					DATE_FORMAT(p.date_purchased,\'' . config_option('sql_date_format') . '\')
                        AS date_purchased,
                    DATE_FORMAT(p.due_date,\'' . config_option('sql_date_format') . '\')
                        AS due_date    
					FROM ' . $this->db->dbprefix(TBL_INVOICES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICE_TOTALS) . ' a
                        ON p.invoice_id = a.invoice_id AND
                        a.type = \'tax\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICE_TOTALS) . ' b
                        ON p.invoice_id = b.invoice_id AND
                        b.type = \'points\'    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICE_TOTALS) . ' c
                        ON p.invoice_id = c.invoice_id AND
                        c.type = \'sub_total\'    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' d
                        ON p.customer_state = d.region_id     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' e
                        ON p.customer_country = e.country_id            
                     LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' f
                        ON p.shipping_state = f.region_id     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' g
                        ON p.shipping_country = g.country_id                
                    WHERE p.date_purchased > \'' . config_option('module_data_export_general_export_start_date') . '\'                                    AND  p.date_purchased < \'' . config_option('module_data_export_general_export_end_date') . '\'  
                        GROUP BY p.invoice_id
                        ORDER BY ' .
							$this->config->item($table,'module_export_sort_column') . ' ' .
							config_option('module_column_sort_order');

				break;

			case TBL_PRODUCTS:

				$sql = 'SELECT  p.*, a.*,
 					DATE_FORMAT(p.date_added,\'' . config_option('sql_date_format') . '\')
                        AS date_added
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' a
                        ON p.product_id = a.product_id AND
                        a.language_id = ' . sess('default_lang_id') . '
                    WHERE p.date_added > \'' . config_option('module_data_export_general_export_start_date') . '\'                                        AND  p.date_added < \'' . config_option('module_data_export_general_export_end_date') . '\'  
                        GROUP BY p.product_id
                        ORDER BY ' .
					$this->config->item($table,'module_export_sort_column') . ' ' .
					config_option('module_column_sort_order');

				break;

			case TBL_PRODUCTS_DOWNLOADS:

				$sql = 'SELECT  a.*, p.*
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS_NAME) . ' a
                        ON p.download_id = a.download_id AND
                        a.language_id = ' . sess('default_lang_id') . '
                        GROUP BY p.download_id
                        ORDER BY ' .
					$this->config->item($table,'module_export_sort_column') . ' ' .
					config_option('module_column_sort_order');

				break;

			case TBL_ORDERS:

				$sql = 'SELECT  p.*, 
							a.*,
							d.region_name AS order_state_name,
							e.country_name AS order_country_name,
							f.region_name AS shipping_state_name,
							g.country_name AS shipping_country_name,
 					DATE_FORMAT(p.date_ordered,\'' . config_option('sql_date_format') . '\')
                        AS date_ordered,
                    DATE_FORMAT(p.due_date,\'' . config_option('sql_date_format') . '\')
                        AS due_date,
                            p.order_id AS order_id
					FROM ' . $this->db->dbprefix(TBL_ORDERS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS_SHIPPING) . ' a
                        ON p.order_id = a.order_id 
                    LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' d
                        ON p.order_state = d.region_id     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' e
                        ON p.order_country = e.country_id            
                     LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' f
                        ON p.shipping_state = f.region_id     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' g
                        ON p.shipping_country = g.country_id                
                    WHERE p.date_ordered > \'' . config_option('module_data_export_general_export_start_date') . '\'                                    AND  p.date_ordered < \'' . config_option('module_data_export_general_export_end_date') . '\'  
                        GROUP BY p.order_id
                        ORDER BY ' .
					$this->config->item($table,'module_export_sort_column') . ' ' .
					config_option('module_column_sort_order');

				break;

			case TBL_AFFILIATE_COMMISSIONS:

				$sql = 'SELECT  p.*, 
 					DATE_FORMAT(p.date,\'' . config_option('sql_date_format') . '\')
                        AS date,
                    DATE_FORMAT(p.date_paid,\'' . config_option('sql_date_format') . '\')
                        AS date_paid
					FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p    
                    WHERE p.date > \'' . config_option('module_data_export_general_export_start_date') . '\'                                            AND  p.date < \'' . config_option('module_data_export_general_export_end_date') . '\'  
                        ORDER BY ' .
					$this->config->item($table,'module_export_sort_column') . ' ' .
					config_option('module_column_sort_order');

				break;

			case TBL_AFFILIATE_PAYMENTS:

				$sql = 'SELECT  p.*, 
 					DATE_FORMAT(p.payment_date,\'' . config_option('sql_date_format') . '\')
                        AS payment_date
					FROM ' . $this->db->dbprefix(TBL_AFFILIATE_PAYMENTS) . ' p    
                    WHERE p.payment_date > \'' . config_option('module_data_export_general_export_start_date') . '\'                                  AND  p.payment_date < \'' . config_option('module_data_export_general_export_end_date') . '\'  
                        ORDER BY ' .
					$this->config->item($table,'module_export_sort_column') . ' ' .
					config_option('module_column_sort_order');

				break;

			case TBL_INVOICE_PAYMENTS:

				$sql = 'SELECT  p.*, 
 					DATE_FORMAT(p.date,\'' . config_option('sql_date_format') . '\')
                        AS date
					FROM ' . $this->db->dbprefix(TBL_INVOICE_PAYMENTS) . ' p    
                    WHERE p.date > \'' . config_option('module_data_export_general_export_start_date') . '\'                                            AND  p.date < \'' . config_option('module_data_export_general_export_end_date') . '\'  
                        ORDER BY ' .
					$this->config->item($table,'module_export_sort_column') . ' ' .
					config_option('module_column_sort_order');

				break;
		}


		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row['list_fields'] = array();
			$row['result_array'] = array();

			$i = 0;
			foreach ($q->result_array() as $v)
			{
				if ($this->config->item($table,'module_export_exclude_fields'))
				{
					foreach ($this->config->item($table, 'module_export_exclude_fields') as $b)
					{
						unset($v[$b]);
					}
				}

				if ($i == 0)
				{
					foreach ($v as $k => $c)
					{
						array_push($row['list_fields'], $k);
					}
				}

				array_push($row['result_array'], $v);

				$i++;
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function get_archive()
	{
		$row = directory_map(config_option('sts_data_import_folder'), 1);
		asort($row);

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file General_export_model.php */
/* Location: ./application/modules/data_export/general_export/models/General_export_model.php */