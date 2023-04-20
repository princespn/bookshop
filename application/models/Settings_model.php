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
class Settings_model extends CI_Model
{

	// ------------------------------------------------------------------------

	/**
	 * Settings_model constructor.
	 */
	public function __construct()
	{
		$this->load->helper('settings');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $array
	 * @param bool $cache
	 * @return bool|false|string
	 */
	public function get_site_address($id = '', $array = TRUE, $cache = FALSE)
	{

		if (!$row = $this->init->cache('site_address', 'settings'))
		{
			$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_SITE_ADDRESSES) . ' p
            LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' c
                ON p.state = c.region_id
            LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' d
                ON p.country = d.country_id
            WHERE id = \'' . (int)$id . '\'';

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				if ($array == TRUE)
				{
					//add the array of regions / states
					$row['regions_array'] = $this->regions->load_country_regions($row['country_id'], TRUE);

					//set the default country for dropdowns
					$row['country_array'] = array($row['country'] => $row['country_name']);
				}
			}
			// Save into the cache
			$this->init->save_cache('settings', 'site_address', $row, 'settings');
		}


		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $format
	 * @return array|bool
	 */
	public function get_site_addresses($format = FALSE)
	{
		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_SITE_ADDRESSES) . ' p
            LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' c
                ON p.state = c.region_id
            LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' d
                ON p.country = d.country_id';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			if ($format == TRUE)
			{
				$options = array();
				foreach ($q->result_array() as $v)
				{
					$options[$v['id']] = $v['name'] . ' - ' . $v['address_1'] . ' ' . $v['city'] . ' ' . $v['region_name'] . ' ' . $v['country_iso_code_3'] . ' ' . $v['postal_code'];
				}
			}
			else
			{
				$options = $q->result_array();
			}
		}

		return empty($options) ? FALSE : $options;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return mixed
	 */
	public function get_settings($type = '')
	{

		if (!empty($type))
		{
			if ($type != 'all')
			{
				$this->db->where('settings_module', $type);
			}
		}

		$this->db->order_by('settings_sort_order', 'ASC');

		if (!$q = $this->db->get(TBL_SETTINGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = $q->result_array();

		foreach ($row as $k => $v)
		{
			$row[$k]['settings_value'] = html_entity_decode($v['settings_value']);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update_db_settings($data = array())
	{
		if (is_array($data))
		{
			foreach ($data as $k => $v)
			{
				$this->db->where('settings_key', trim($k));
				$this->db->update(TBL_SETTINGS, array('settings_value' => $v));
			}

			//update cache
			$this->init->reset_cache('jx_settings');
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function validate_settings($data = array(), $type = 'settings')
	{
		//get the settings first
		$fields = $this->get_settings($type, FALSE);

		$error_msg_text = '';


		//go through each fields and check if the field is being submitted...
		foreach ($fields as $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($data);

			if (!empty($data[$v['settings_key']]))
			{
				//this will generate the validation rule!
				$a = $this->generate_rule($v);

				//set the rule for the validation class...
				$name = $type == 'layout' ? str_replace('layout_design_', '', $v['settings_key']) : str_replace('sts_', '', $v['settings_key']);
				$this->form_validation->set_rules($v['settings_key'], 'lang:' . $name, $a['rule']);

				//generate custom error messages if needed
				if (!empty($a['msg']))
				{
					foreach ($a['msg'] as $b => $c)
					{
						$this->form_validation->set_message($b, lang($v['settings_key']) . ' - ' . $c);
					}
				}
			}

			//validate it!
			if (!$this->form_validation->run())
			{
				$error_msg_text .= validation_errors();
			}
		}

		if (!empty($error_msg_text))
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => $error_msg_text,
			);
		}
		else
		{
			$row = array('success'  => TRUE,
			             'msg_text' => lang('system_updated_successfully'));
		}

		//return the filtered data back
		$row['data'] = $this->dbv->validated($data, FALSE);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	protected function generate_rule($data = array())
	{
		//set the default rule
		$a = array('rule' => 'trim',
		           'msg'  => array(),
		);

		switch ($data['settings_type'])
		{
			case 'textarea':

				switch ($data['settings_key'])
				{
					case 'sts_site_refer_friend_code':
					case 'layout_design_modal_timer_text':

						$a['rule'] .= '';

						break;

					default:

						$a['rule'] .= '|html_escape';

						break;
				}

				break;

			case 'text':
			case 'number':

				//if there are custom functions, add it to the rule.
				if (!empty($data['settings_function']) && $data['settings_function'] != 'none')
				{
					$a['rule'] .= '|' . $data['settings_function'];
				}

				$a['rule'] .= '|strip_tags|xss_clean|max_length[255]';

				$a['msg'][$data['settings_function']] = lang('settings_' . $data['settings_function'] . '_error');

				break;
		}

		return $a;
	}
}

/* End of file Settings_model.php */
/* Location: ./application/models/Settings_model.php */