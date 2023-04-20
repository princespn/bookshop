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
class Zones_model extends CI_Model
{

	/**
	 * @var string
	 */
	protected $table = TBL_ZONES;

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $id = 'zone_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return array|bool
	 */
	public function ajax_search($term = '')
	{
		$this->db->like('zone_name', $term);
		$this->db->select('zone_id, zone_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get($this->table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array();
		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return array|bool
	 */
	public function create()
	{
		$vars = array(
			'zone_name'        => lang('zone_name'),
			'zone_description' => '',
		);

		if (!$this->db->insert($this->table, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return array|bool
	 */
	public function check_tax_zone($data = array(), $type = 'checkout')
	{
		//check if we've now set the billing or shipping address and get the zone from there
		if ($type == 'admin')
		{
			$a = 'order_billing_address_data';
			if (sess('order_shipping_address_data'))
			{
				$a = 'order_shipping_address_data';
			}
		}
		else
		{
			$a = 'checkout_customer_data';
		}

		if (sess($a))
		{
			$customer = sess($a);

			if (!empty($customer['shipping_country']) || !empty($customer['billing_country']))
			{
				for ($i = 0; $i <= 2; $i++)
				{
					if ($row = $this->get_regional_zones([$data], $customer, $i, ''))
					{
						return $data;
					}
				}
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		//now delete the zone
		if (!$this->db->delete($this->table, array($this->id => $id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row['msg_text'] = lang('record_deleted_successfully');

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_details($id = '')
	{
		$cache = __METHOD__ . $id;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$sql = 'SELECT p.*,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix($this->table) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                            AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix($this->table) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                            AS next
                        FROM ' . $this->db->dbprefix($this->table) . ' p
                        WHERE p.' . $this->id . '= ' . (int)$id . '';

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				$row['regions'] = $this->get_regions($id);

				//get regions array
				if (!empty($row['regions']))
				{
					foreach ($row['regions'] as $k => $v)
					{
						$row['regions'][ $k ]['regions_array'] = $this->region->load_country_regions($v['country_id'], TRUE, TRUE);
					}
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return array|bool|false
	 */
	public function get_zones($form = FALSE)
	{
		if (!$q = $this->db->get($this->table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $form == TRUE ? format_array($q->result_array(), 'zone_id', 'zone_name') : $q->result_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return array|bool
	 */
	public function get_regions($id = '')
	{
		$this->db->join(TBL_COUNTRIES,
			$this->db->dbprefix(TBL_COUNTRIES) . '.country_id = ' .
			$this->db->dbprefix(TBL_REGIONS_TO_ZONES) . '.country_id', 'left');

		$this->db->join(TBL_REGIONS,
			$this->db->dbprefix(TBL_REGIONS) . '.region_id = ' .
			$this->db->dbprefix(TBL_REGIONS_TO_ZONES) . '.region_id', 'left');

		$this->db->where('zone_id', $id);
		if (!$q = $this->db->get(TBL_REGIONS_TO_ZONES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array();
			foreach ($q->result_array() as $v)
			{
				if (empty($v['country_id']))
				{
					$v['country_id'] = 0;
					$v['country_name'] = lang('all_countries');
				}

				if (empty($v['region_id']))
				{
					$v['region_id'] = 0;
					$v['region_name'] = lang('all_regions');
				}
				array_push($row, $v);
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $customer_data
	 * @param string $type
	 * @param string $table
	 * @param string $ship_to
	 * @return bool|mixed
	 */
	public function get_regional_zones($data = array(), $customer_data = array(), $type = '0', $table = '', $ship_to = 'shipping')
	{
		if (empty($customer_data['shipping_country']))
		{
			$ship_to = 'billing'; //calculate it off of the billing info instead of the shipping info
		}

		//get regions
		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_REGIONS_TO_ZONES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ZONES) . ' c
                        ON p.zone_id = c.zone_id';

		if (!empty($table))
		{
			$sql .= ' LEFT JOIN ' . $this->db->dbprefix($table) . ' z
                        ON p.zone_id = z.zone_id';
		}

		switch ($type)
		{
			case '0':

					$sql .= '  WHERE country_id = \'' . (int)$customer_data[$ship_to . '_country'] . '\'
                        AND region_id = \'' . (int)$customer_data[$ship_to . '_state'] . '\'';


				break;

			case '1':

					$sql .= '  WHERE country_id = \'' . (int)$customer_data[$ship_to . '_country'] . '\'
                        AND region_id = \'0\'';


				break;

			default:

				$sql .= '  WHERE country_id = \'0\'
                        AND region_id = \'0\'';

				break;
		}


		$sql .= ' ORDER BY priority ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			if ($row = match_region_rates($q->result_array(), $data))
			{
				return $row;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $customer_data
	 * @param string $table
	 * @return bool|mixed
	 */
	public function get_zone_rate($data = array(), $customer_data = array(), $table = '')
	{
		//let's see if there is a zone for both region and country
		for ($i = 0; $i <= 2; $i++)
		{
			if ($row = $this->get_regional_zones($data, $customer_data, $i, $table))
			{
				return $row;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Load zones
	 */
	public function load_zones()
	{
		if (!$row = $this->init->cache(__METHOD__, 'db_query'))
		{
			if (!$q = $this->db->order_by('priority', 'ASC')->get(TBL_REGIONS_TO_ZONES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();
				$this->config->set_item('zones', $row);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, __METHOD__, $row, 'db_query');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_ZONES);

		$this->db->where($this->id, $data[$this->id]);

		if (!$this->db->update(TBL_ZONES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//get current zones first
		$c = $this->get_regions($data['zone_id']);

		$a = array();
		//check if the list is already in the table
		if (!empty($data['zone']))
		{
			foreach ($data['zone'] as $v)
			{
				$v['zone_id'] = $data['zone_id'];
				if (!empty($v['region_zone_id']))
				{
					$row = $this->dbv->update(TBL_REGIONS_TO_ZONES, 'region_zone_id', $v);

					array_push($a, $v['region_zone_id']);
				}
				else
				{
					$row = $this->dbv->create(TBL_REGIONS_TO_ZONES, $v);
				}
			}
		}

		//let's delete all the regions not in the current one
		if (!empty($c))
		{
			foreach ($c as $v)
			{
				if (!in_array($v['region_zone_id'], $a))
				{
					$this->dbv->delete(TBL_REGIONS_TO_ZONES, 'region_zone_id', $v['region_zone_id']);
				}
			}
		}

		$row = array(
			'data'       => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		if (!empty($data['zone']))
		{
			foreach ($data['zone'] as $k => $v)
			{
				$this->form_validation->reset_validation();
				$this->form_validation->set_data($v);

				$required = $this->config->item(TBL_REGIONS_TO_ZONES, 'required_input_fields');

				//now get the list of fields directly from the table
				$fields = $this->db->field_data(TBL_REGIONS_TO_ZONES);

				foreach ($fields as $f)
				{
					//set the default rule
					$rule = 'trim|xss_clean';

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
				}

				if (!$this->form_validation->run())
				{

					$error .= validation_errors();
				}
				else
				{
					$data['zone'][ $k ] = $this->dbv->validated($v, FALSE);
				}
			}
		}

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$required = $this->config->item(TBL_ZONES, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_ZONES);

		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
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
}

/* End of file Zones_model.php */
/* Location: ./application/models/Zones_model.php */