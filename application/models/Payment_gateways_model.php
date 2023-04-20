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
class Payment_gateways_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'module_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $public = FALSE)
	{
		$sort = $this->config->item(TBL_MODULES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		if ($public == TRUE)
		{
			$this->db->where('module_status', '1');
		}

		$this->db->where('module_type', 'payment_gateways');
		$this->db->order_by($options['sort_column'], $options['sort_order']);

		if (!$q = $this->db->get(TBL_MODULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $q->num_rows(),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return bool
	 */
	public function ipn_log($data = array(), $type = 'check')
	{
		$this->db->where('type', $data['type']);
		$this->db->where('reference_id', $data['reference_id']);
		if (!$q = $this->db->get(TBL_IPN_LOG))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//add it if its not yet there....
		if ($q->num_rows() > 0)
		{
			die('ipn done');
		}

		if ($type == 'create')
		{
			if (!$q = $this->db->insert(TBL_IPN_LOG, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function module_enabled($id = '')
	{
		$row = $this->mod->get_module_details($id, TRUE, 'shipping', 'module_folder');

		return !empty($row) ? TRUE : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update_module($data = array())
	{
		//update module data
		$this->mod->update($data);

		$row = array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $func
	 * @param array $data
	 * @param bool $return
	 * @return array|bool
	 */
	public function validate($func = 'create', $data = array(), $return = FALSE)
	{
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('status', 'lang:status', 'required');
		$this->form_validation->set_rules('region_name', 'lang:region_name', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('region_code', 'lang:region_code', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('sort_order', 'lang:sort_order', 'trim|integer');

		if ($func == 'update')
		{
			$this->form_validation->set_rules(
				'region_country_id', 'lang:country',
				array(
					'trim', 'required', 'integer',
					array('check_country_id', array($this->country, 'check_country_id')),
				),
				array(
					'required'         => '%s ' . lang('field_is_required'),
					'check_country_id' => '%s ' . lang('already_exists'),
				)
			);

			$this->form_validation->set_message('check_country_id', '%s ' . lang('field_is_required'));
		}

		if ($this->form_validation->run())
		{
			if ($return)
			{
				return $data;
			}

			$row = $func == 'create' ? $this->dbv->$func(TBL_MODULES, $data) : $this->dbv->$func(TBL_MODULES, $this->id, $data);

			return $row;
		}
		else
		{
			return FALSE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate_module($data = array())
	{
		$error = '';
		
		$this->form_validation->set_data($data);

		$row = $this->mod->get_module_details(valid_id($data['module_id']));

		//validate the module configuration settings...
		if (!empty($row['values']))
		{
			foreach ($row['values'] as $v)
			{
				$rule = 'trim|xss_clean';

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
			             'data'    => $this->dbv->validated($data, FALSE),
			);
		}

		return $row;
	}

}

/* End of file Payment_gateways_model.php */
/* Location: ./application/models/Payment_gateways_model.php */