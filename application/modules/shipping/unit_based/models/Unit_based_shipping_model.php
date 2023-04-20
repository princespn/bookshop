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
class Unit_based_shipping_model extends Shipping_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix($this->config->item('module_shipping_table')) . ';';

		if (!$q = $this->db->query($delete))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//install db table
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->db->dbprefix($this->config->item('module_shipping_table')) . ' (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `zone_id` int(10) NOT NULL DEFAULT \'0\',
                  `shipping_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
                  `min_amount` decimal(14,2) NOT NULL DEFAULT \'0.00\',
                  `max_amount` decimal(14,2) NOT NULL DEFAULT \'0.00\',
                  `amount` decimal(14,2) NOT NULL DEFAULT \'0.00\',
                  `sort_order` int(10) NOT NULL DEFAULT \'0\',
                  PRIMARY KEY (`id`),
                  KEY `zone_id` (`zone_id`,`min_amount`,`max_amount`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_unit_based_charge_shipping_tax',
			'settings_value'      => '0',
			'settings_module'     => 'shipping',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => 'enable',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_unit_based_tax_class',
			'settings_value'      => '1',
			'settings_module'     => 'shipping',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => 'tax_classes',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_unit_based_quantity_type',
			'settings_value'      => '1',
			'settings_module'     => 'shipping',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'zone_calc',
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
		$sql = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix($this->config->item('module_shipping_table')) . ';';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//remove settings from database
		$this->mod->remove_config($id, 'shipping');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function get_module_options()
	{
		//set the unique cache file
		$cache = __METHOD__;

		if (!$row = $this->init->cache($cache, 'module_db_query'))
		{
			$this->db->join(TBL_ZONES,
				$this->db->dbprefix(TBL_ZONES) . '.zone_id = ' .
				$this->db->dbprefix($this->config->item('module_shipping_table')) . '.zone_id', 'left');

			$this->db->order_by('sort_order', 'ASC');
			if (!$q = $this->db->get($this->config->item('module_shipping_table')))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row['zones'] = $q->result_array();
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'module_db_query');
		}

		return empty($row) ? FALSE : $row;
	}

	public function generate_rates($data = array(), $cart = array())
	{
		//get module zones first
		$row = $this->get_module_options();

		//check pricing amount
		if ($this->config->item('module_shipping_unit_based_quantity_type') == 'price')
		{
			$unit_totals = cart_subtotal($cart);
		}
		else //check total weight
		{
			$unit_totals = cart_total_weight($cart['items']);
		}

		$m = array();
		foreach ($row['zones'] as $k => $v)
		{
			if ($unit_totals >= $v['min_amount'] && $unit_totals <= $v['max_amount'])
			{
				array_push($m, $v);
			}
		}

		$zone = array();
		foreach ($m as $k => $v)
		{
			for ($i = 0; $i <= 2; $i++)
			{
				if ($zone_rate = $this->zone->get_regional_zones(array($v), $data, $i))
				{
					$zone_rate['amount'] = $v['amount']; //override the default
					$zone_rate['shipping_description'] = $v['shipping_description'];
					$zone_rate['proximity'] = $i;
					$zone[$i] = $zone_rate;
				}
			}
		}

		$zone_rate = check_proximity($zone);

		if (!empty($zone_rate))
		{
			$zone_rate['carrier'] = $this->config->item('module_alias');
			$zone_rate['service'] = $zone_rate['shipping_description'];

			$zone_rate['shipping_amount'] = $zone_rate['amount'];
			//add some taxes and handling...
			$zone_rate['shipping_taxes'] = calc_shipping_taxes($zone_rate['shipping_amount'], 'unit_based');
			$zone_rate['shipping_total'] = shipping_totals($zone_rate);
		}

		return isset($zone_rate['shipping_total']) ? array($zone_rate) : FALSE;
	}

	public function update_module_zones($data = array(), $table = '')
	{
		//get current zones first
		$c = $this->get_module_options();

		$a = array();
		//check if the list is already in the table
		if (!empty($data) && !empty($table))
		{
			foreach ($data as $v)
			{
				if (!empty($v['id']))
				{
					$row = $this->dbv->update($table, 'id', $v);

					array_push($a, $v['id']);
				}
				elseif (!empty($v['zone_id']))
				{
					$row = $this->dbv->create($table, $v);
				}
			}
		}

		//let's delete all the attributes not in the current one
		if (!empty($c['zones']))
		{
			foreach ($c['zones'] as $v)
			{
				if (!in_array($v['id'], $a))
				{
					$this->dbv->delete($table, 'id', $v['id']);
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function update_module($data = array(), $table = '')
	{
		//update module data
		$this->mod->update($data);

		//update mailing list mappings in the module table...
		if (!empty($data['zone']))
		{
			$this->update_module_zones($data['zone'],$table);
		}

		$row = array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => 'system_updated_successfully',
		);

		return empty($row) ? FALSE : sc($row);
	}

	public function validate_shipping_module($data = array(), $table = '')
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

						if ($v['function'] != 'none' && $v['function'] != '')
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
		if (!empty($data['zone']))
		{
			$zone_error = '';

			//get the list of fields required for this
			$required = $this->config->item('module_required_input_fields');

			//now get the list of fields directly from the table
			$fields = $this->db->field_data($table);

			foreach ($data['zone'] as $k => $v)
			{
				$this->form_validation->reset_validation();
				$this->form_validation->set_data($v);

				$custom_lang = ''; //for a custom error language

				//go through each field and
				foreach ($fields as $f)
				{
					//set the default rule
					$rule = 'trim|strip_tags|xss_clean';

					//if this field is a required field, let's set that
					if (in_array($f->name, $required))
					{
						$rule .= '|required';
					}

					switch ($f->name)
					{
						case 'amount':

							$rule .= '|numeric';

							break;

						case 'max_amount':

							$rule .= '|greater_than[' . $v['min_amount'] . ']';
							$custom_lang = array('greater_than' => lang('max_quantity_greater_than_min_quantity'));

							break;

						case 'id':

							$rule .= '|integer';

							break;

						case 'zone_id':

							$rule .= '|is_natural_no_zero';
							$custom_lang = array('is_natural_no_zero' => lang('shipping_zone_is_required'));

							break;
					}

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule, $custom_lang);
				}

				if ($this->form_validation->run())
				{
					$data['zone'][ $k ] = $this->dbv->validated($v);
				}
				else
				{
					$zone_error = validation_errors();
				}
			}

			$error .= $zone_error;
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

/* End of file Unit_based_shipping_model.php */
/* Location: ./application/modules/shipping/unit_based/models/Unit_based_shipping_model.php */