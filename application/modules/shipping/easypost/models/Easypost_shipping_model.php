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
class Easypost_shipping_model extends Shipping_model
{

	protected $shipment_object;

	public function __construct()
	{
		parent::__construct();

		\EasyPost\EasyPost::setApiKey($this->config->item('module_shipping_easypost_api_key'));
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
                  `sort_order` int(10) NOT NULL DEFAULT \'0\',
                  PRIMARY KEY (`id`),
                   KEY `zone_id` (`zone_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_api_key',
			'settings_value'      => '0',
			'settings_module'     => 'shipping',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_originating_address',
			'settings_value'      => '1',
			'settings_module'     => 'shipping',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => 'site_addresses',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_package_length',
			'settings_value'      => '0',
			'settings_module'     => 'shipping',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'numeric',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_package_width',
			'settings_value'      => '0',
			'settings_module'     => 'shipping',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => 'numeric',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_package_height',
			'settings_value'      => '0',
			'settings_module'     => 'shipping',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => 'numeric',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_charge_shipping_tax',
			'settings_value'      => '0',
			'settings_module'     => 'shipping',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => 'enable',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_tax_class',
			'settings_value'      => '1',
			'settings_module'     => 'shipping',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => 'tax_classes',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_shipping_easypost_enable_debug',
			'settings_value'      => '0',
			'settings_module'     => 'shipping',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
			'settings_function'   => 'boolean',
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
			$this->initialize($data, $cart);

			if ($this->shipment_object)
			{
				$rates = $this->get_rates();
			}
		}

		return empty($rates) ? FALSE : $rates;
	}

	public function generate_postage($data = array(), $cart = array())
	{
		//initialize the module first
		$this->initialize($data, $cart);

		if ($this->shipment_object)
		{
			foreach ($this->shipment_object->rates as $k => $rate)
			{
				//match the rate with the one selected
				if ($rate['carrier'] == $data['carrier'] && $rate['service'] == $data['service'])
				{
					$rate_id = defined('USE_EASYPOST_SAVED_RATE_ID') ? $data['rate_id'] : $rate['id'];

					$row = $this->shipment_object->buy(array('rate' => array('id' => $rate_id)));
				}
			}
		}

		if (!empty($row))
		{
			//save the label
			if ($row->postage_label->label_url)
			{
				$postage['url'] = $row->postage_label->label_url;
				$postage['label'] = file_get_contents($postage['url']);

				//save to the table
				$this->ship->save_postage($data['osid'], $postage, $data['order_id']);

				//update tracking code
				if (!$this->dbv->update(TBL_ORDERS_SHIPPING, 'order_id', array('order_id'     => $data['order_id'],
				                                                               'tracking_id'  => $row->tracking_code,
				                                                               'tracking_url' => $row->tracker->public_url)))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				$row = array(
					'success'  => TRUE,
					'msg_text' => 'postage_saved_successfully',
				);
			}
		}

		return !empty($row) ? $row : FALSE;
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

		//update zone mappings in the module table...
		if (!empty($data['zone']))
		{
			$this->update_module_zones($data['zone'], $table);
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

	protected function initialize($data = array(), $cart = array(), $rates = TRUE)
	{
		//get the originating address
		$from = $this->set->get_site_address($this->config->item('module_shipping_easypost_originating_address'));

		//set the weight
		$weight = cart_total_weight($cart['items'], 2); //2 is for oz

		if (!empty($weight))
		{
			//set the address we're sending to
			$name = !empty($data['shipping_name']) ? $data['shipping_name'] : $data['fname'] . ' ' . $data['lname'];

			$to_address_params = array("name"    => $name,
			                           "street1" => $data['shipping_address_1'],
			                           "street2" => $data['shipping_address_2'],
			                           "city"    => $data['shipping_city'],
			                           "state"   => $data['shipping_state_code'],
			                           "zip"     => $data['shipping_postal_code'],
			                           "country" => $data['shipping_country_iso_code_2'],
			);

			$to_address = \EasyPost\Address::create($to_address_params);

			//set the address we're sending from
			$from_address_params = array("name"    => $from['name'],
			                             "street1" => $from['address_1'],
			                             "street2" => $from['address_2'],
			                             "city"    => $from['city'],
			                             "state"   => $from['region_code'],
			                             "zip"     => $from['postal_code'],
			                             "phone"   => $from['phone'],
			                             "country" => $from['country_iso_code_2'],
			);

			$from_address = \EasyPost\Address::create($from_address_params);

			$parcel_params = array("length" => $this->config->item('module_shipping_easypost_package_length'),
			                       "width"  => $this->config->item('module_shipping_easypost_package_width'),
			                       "height" => $this->config->item('module_shipping_easypost_package_height'),
			                       //"predefined_package" => NULL,
			                       "weight" => $weight,
			);

			$parcel = \EasyPost\Parcel::create($parcel_params);

			// create shipment
			$shipment_params = array("from_address" => $from_address,
			                         "to_address"   => $to_address,
			                         "parcel"       => $parcel,
			);

			$this->shipment_object = \EasyPost\Shipment::create($shipment_params);

		}
	}

	protected function get_rates()
	{
		$ship_array = array();

		foreach ($this->shipment_object->rates as $rate)
		{
			$zone_rate['carrier'] = $rate['carrier'];
			$zone_rate['service'] = $rate['service'];
			$zone_rate['shipping_description'] = lang($rate['carrier']) . ' ' . lang($rate['service']);
			$zone_rate['shipping_amount'] = $rate['rate'];
			//add some taxes and handling...
			$zone_rate['shipping_taxes'] = calc_shipping_taxes($zone_rate['shipping_amount'], 'easypost');
			$zone_rate['shipping_total'] = shipping_totals($zone_rate);
			$zone_rate['shipment_id'] = $rate['shipment_id'];
			$zone_rate['rate_id'] = $rate['id'];

			if (config_enabled('module_shipping_easypost_enable_debug'))
			{
				$zone_rate['debug_info'] = $rate;
			}

			foreach ($rate as $k => $v)
			{
				$zone_rate['shipment_details'][$k] = $v;
			}

			array_push($ship_array, $zone_rate);
		}

		return $ship_array;
	}
}

/* End of file Flat_rate_model.php */
/* Location: ./application/modules/shipping/easypost/models/Flat_rate_model.php */