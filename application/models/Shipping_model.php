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
class Shipping_model extends CI_Model
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

		$this->db->where('module_type', 'shipping');
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
	 * @return bool|mixed
	 */
	public function get_shipping_modules()
	{
		$modules = $this->get_rows(array(), TRUE);

		return !empty($modules['values']) ? $modules['values'] : FALSE;

	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $reset
	 * @return array|bool
	 */
	public function init_shipping($reset = FALSE)
	{
		if ($reset == TRUE)
		{
			$this->db->where('cart_id', sess('cart_id'));
			$this->db->where('type', 'shipping');

			if (!$this->db->delete(TBL_CART_TOTALS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			return TRUE;
		}

		$vars = array(
			'cart_id'    => sess('cart_id'),
			'type'       => 'shipping',
			'text'       => sess('checkout_shipping_selected', 'shipping_description'),
			'amount'     => sess('checkout_shipping_selected', 'shipping_total'),
			'percent'    => 'flat',
			'sort_order' => 2,
		);

		if (!$this->db->insert(TBL_CART_TOTALS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('shipping_applied_successfully'),
		);
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
	 * @param string $id
	 * @param array $data
	 * @param string $order_id
	 * @return bool
	 */
	public function save_postage($id = '', $data = array(), $order_id = '')
	{
		$vars = array('label'     => base64_encode($data['label']),
		              'label_url' => is_var($data, 'url'));

		if (!$this->db->where('osid', $id)->update(TBL_ORDERS_SHIPPING, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
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
	 * @param string $table
	 * @return array
	 */
	public function validate_module($data = array(), $table = '')
	{
		$error = '';
		
		$this->form_validation->set_data($data);

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

/* End of file Shipping_model.php */
/* Location: ./application/models/Shipping_model.php */