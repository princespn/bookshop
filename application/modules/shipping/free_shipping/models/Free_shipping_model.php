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
class Free_shipping_model extends Shipping_model
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
                  `amount` decimal(14,2) NOT NULL DEFAULT \'0.00\',
                  `sort_order` int(10) NOT NULL DEFAULT \'0\',
                  PRIMARY KEY (`id`),
                   KEY `zone_id` (`zone_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_free_shipping_charge_shipping_tax',
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
			'settings_key'        => 'module_shipping_free_shipping_tax_class',
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
			'settings_key'        => 'module_shipping_free_shipping_quantity_type',
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

		$zone_rate = $this->zone->get_zone_rate($row['zones'], $data, $this->config->item('module_shipping_table'));

		if (!empty($zone_rate))
		{
			//generate shipping amount
			if (cart_subtotal($cart) >= $zone_rate['amount'])
			{
				$zone_rate['carrier'] = $this->config->item('module_alias');
				$zone_rate['service'] = $zone_rate['shipping_description'];
				$zone_rate['shipping_amount'] = 0;
				$zone_rate['shipping_taxes'] = array('shipping' => 0, 'handling' => 0);
				$zone_rate['shipping_total'] = shipping_totals($zone_rate);

				return array($zone_rate);
			}
		}

		return FALSE;
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
		$row = $this->shipping->validate_module($data, $table);

		return $row;
	}
}

/* End of file Free_shipping_model.php */
/* Location: ./application/modules/shipping/free_shipping/models/Free_shipping_model.php */