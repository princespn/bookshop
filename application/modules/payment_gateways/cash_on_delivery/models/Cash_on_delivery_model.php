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

class Cash_on_delivery_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix($this->config->item('module_payment_gateway_table')) . ';';

		if (!$q = $this->db->query($delete))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//install db table
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->db->dbprefix($this->config->item('module_payment_gateway_table')) . ' (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `zone_id` int(10) NOT NULL DEFAULT \'0\',
                  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
                  PRIMARY KEY (`id`),
                   KEY `zone_id` (`zone_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_cash_on_delivery_title',
			'settings_value'      => 'Payment Voucher',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_cash_on_delivery_description',
			'settings_value'      => 'Make payment via Payment Voucher',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'textarea',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_cash_on_delivery_instructions',
			'settings_value'      => 'Your order will be delivered to you and paid via Payment Voucher.',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'textarea',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_cash_on_delivery_checkout_logo',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => 'image_manager',
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
		$sql = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix($this->config->item('module_payment_gateway_table')) . ';';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//remove settings from database
		$this->mod->remove_config($id, 'payment_gateways');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function update_module_zones($data = array())
	{
		//get current zones first
		$c = $this->get_module_options();

		$a = array();
		//check if the list is already in the table
		if (!empty($data) && config_option('module_payment_gateway_table'))
		{
			foreach ($data as $v)
			{
				if (!empty($v['id']))
				{
					$row = $this->dbv->update(config_option('module_payment_gateway_table'), 'id', $v);

					array_push($a, $v['id']);
				}
				elseif (!empty($v['zone_id']))
				{
					$row = $this->dbv->create(config_option('module_payment_gateway_table'), $v);
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
					$this->dbv->delete(config_option('module_payment_gateway_table'), 'id', $v['id']);
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function update_module($data = array())
	{
		//update module data
		$row = $this->mod->update($data);

		//update zone mappings in the module table...
		if (!empty($data['zone']))
		{
			$this->update_module_zones($data['zone']);
		}

		return $row;
	}

	public function validate_payment_module($data = array())
	{
		$row = $this->pay->validate_module($data);

		return $row;
	}
	public function filter_payment_zones($data = array())
	{
		if (!empty($data['shipping_country']))
		{
			//get module zones first
			$row = $this->get_module_options();

			$zone_rate = $this->zone->get_zone_rate($row['zones'], $data, $this->config->item('module_payment_gateway_table'));

			if (!empty($zone_rate))
			{
				return TRUE;
			}

			return FALSE;
		}

		return TRUE;
	}

	public function get_module_options()
	{
		//set the unique cache file
		$cache = __METHOD__;

		if (!$row = $this->init->cache($cache, 'module_db_query'))
		{
			$this->db->join(TBL_ZONES,
				$this->db->dbprefix(TBL_ZONES) . '.zone_id = ' .
				$this->db->dbprefix($this->config->item('module_payment_gateway_table')) . '.zone_id', 'left');

			if (!$q = $this->db->get($this->config->item('module_payment_gateway_table')))
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
}

/* End of file Cash_on_delivery_model.php */
/* Location: ./application/models/Cash_on_delivery_model.php */