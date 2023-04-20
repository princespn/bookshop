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
class Forms_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'form_id';
	/**
	 * @var string
	 */
	protected $field_id = 'field_id';

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function create()
	{
		$vars = array('form_type'        => 'custom',
		              'form_name'        => lang('new_form'),
		              'form_description' => lang('new_form_description'),
		              'form_method'      => 'GET',
		              'form_processor'   => 'email',
		              'function'         => config_option('sts_site_email'));

		//create default form
		if (!$this->db->insert(TBL_FORMS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		//add some default fields
		foreach (array('fname', 'lname', 'primary_email') as $v)
		{
			$this->create_field($row['id'], default_custom_form_fields($row['id'], $v));
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $form_id
	 * @return array
	 */
	public function create_field($form_id = '', $data = '')
	{
		$t = $this->db->where('custom', '1')->count_all_results(TBL_FORM_FIELDS) + 1;

		if (empty($data))
		{
			$data = array('form_id'          => $form_id,
			              'show_public'      => '0',
			              'show_account'     => '0',
			              'field_type'       => 'text',
			              'custom'           => '1',
			              'form_field'       => 'field_' . $t,
			              'field_required'   => '0',
			              'field_options'    => '',
			              'field_value'      => '',
			              'field_validation' => '',
			              'sub_form'         => '',
			              'sort_order'       => '999' + $t,
			);
		}

		$vars = $this->dbv->clean($data, TBL_FORM_FIELDS);
		if (!$this->db->insert(TBL_FORM_FIELDS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//add to names table
		$lang = get_languages();

		foreach ($lang as $k => $v)
		{
			$name = array($this->field_id     => $id,
			              'language_id'       => $k,
			              'field_name'        => empty($data['field_name']) ? lang('custom_field') . ' ' . $t : $data['field_name'],
			              'field_description' => empty($data['field_description']) ? lang('custom_field_description') : $data['field_description'],
			);

			$this->db->insert(TBL_FORM_FIELDS_NAME, $name);
		}

		if ($form_id < 3)
		{
			$data = array('form_id'       => $form_id,
			              $this->field_id => $id,
			              'sort_order'    => '1',
			);

			if (!$this->db->insert(TBL_MEMBERS_CUSTOM_FIELDS, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array(
			'id'       => $id,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $func
	 * @param array $data
	 * @return array
	 */
	public function generate_form_rule($func = '', $data = array())
	{
		//set the default rule
		$a = array('rule' => 'trim',
		           'msg'  => '',
		);

		switch ($func)
		{
			//rules for the member profile area
			case 'update_profile':

				//run regular validation rules
				if ($data['field_required'] == 1 && $data['show_account'] == 1)
				{
					if (empty($data['sub_form']))
					{
						$a['rule'] .= '|required';
					}
				}

				break;

			//where validating from the admin area
			case 'update_admin':
			case 'update_api':

				//only fname and email are required
				switch ($data['form_field'])
				{
					case 'fname':
					case 'primary_email':

						$a['rule'] .= '|required';

						break;
				}

				break;

			case 'account':
			case 'shipping':
			case 'billing':
			case 'payment':
				//run regular validation rules
				if ($data['field_required'] == 1 && $data['show_public'] == 1)
				{
					$a['rule'] .= '|required';
				}

				break;

			case 'form':

				//only fname and email are required
				switch ($data['form_field'])
				{
					case 'name':
					case 'primary_email':

						$a['rule'] .= '|required';

						break;
				}


				if ($data['field_required'] == 1)
				{
					$a['rule'] .= '|xss_clean|required';
				}

				break;
		}

		//run custom validation rules
		if (!empty($data['custom']))
		{
			if ($data['field_required'] == 1)
			{
				$a['rule'] .= '|xss_clean|required';
			}

			if (!empty($data['field_validation']))
			{
				$a['rule'] .= '|' . $data['field_validation'];
			}
		}
		else
		{
			$a['rule'] = reduce_multiples($a['rule']);

			//check for other required validation rules
			switch ($data['form_field'])
			{
				//check for a unique username
				case 'username':

					$r = explode('|', $a['rule']);

					$b = array(
						'strtolower',
						'alpha_numeric',
						'min_length[' . $this->config->item('sts_affiliate_min_username_length') . ']',
						'max_length[' . $this->config->item('max_member_username_length') . ']',
						array('check_username', array($this->mem, 'check_username')),
					);

					$a['rule'] = array_merge($r, $b);

					$a['msg'] = array(
						'check_username' => '%s ' . lang('already_exists'),
					);

					break;

				case 'password':

					$r = explode('|', $a['rule']);

					$b = array(
						'min_length[' . $this->config->item('min_member_password_length') . ']',
						'max_length[' . $this->config->item('max_member_password_length') . ']',
					);

					$a['rule'] = array_merge($r, $b);

					break;

				//email
				case 'primary_email':

					$r = explode('|', $a['rule']);

					$b = array(
						'required',
						'strtolower',
						'valid_email',
						array('check_email', array($this->mem, 'check_email')),
					);

					$a['rule'] = array_merge($r, $b);

					$a['msg'] = array(
						'check_email' => '%s ' . lang('is_already_in_use_or_not_allowed'),
					);

					break;

				//set other default rules
				default:

					//set rules for each field type
					switch ($data['field_type'])
					{
						case 'textarea':

							$a['rule'] .= '|html_escape';

							break;

						case 'date':

							$a['rule'] .= '|end_date_to_sql';

							break;

						case 'text':

							//add custom validation rules if needed.
							if (!empty($data['field_validation']))
							{
								$a['rule'] .= '|' . $data['field_validation'];
							}

							$a['rule'] .= '|strip_tags|xss_clean|max_length[255]';

							break;

						default:

							$a['rule'] .= '|strip_tags|xss_clean|max_length[255]';

							break;
					}

					break;
			}
		}

		return $a;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function get_rows()
	{
		if (!$q = $this->db->get(TBL_FORMS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $custom
	 * @return bool
	 */
	public function get_details($id = '', $custom = TRUE)
	{
		$sql = 'SELECT *,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_FORMS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '';

		$sql .= $custom == TRUE ? ' AND form_type = \'custom\'' : '';

		$sql .= ' ORDER BY `' . $this->id . '` DESC LIMIT 1)
				            AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_FORMS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '';

		$sql .= $custom == TRUE ? ' AND form_type = \'custom\'' : '';

		$sql .= ' ORDER BY `' . $this->id . '` ASC LIMIT 1)
				            AS next
				    FROM ' . $this->db->dbprefix(TBL_FORMS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_MAILING_LISTS) . ' m
                                ON p.list_id = m.list_id';



        $sql .= '  WHERE p.' . $this->id . '= ' . (int)$id . '';

		$sql .= $custom == TRUE ? ' AND form_type = \'custom\'' : '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_field_details($id = '')
	{
		if (!$q = $this->db->where($this->field_id, $id)
			->get(TBL_FORM_FIELDS)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			$row['names'] = $this->get_form_field_names($id);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param array $data
	 * @return bool|false|string
	 */
	public function get_member_custom_fields($id = '', $lang_id = '1', $data = array())
	{
		$sql = 'SELECT p.*, n.*, g.*, n.field_id as field_id, c.data AS field_value
				    FROM ' . $this->db->dbprefix(TBL_MEMBERS_CUSTOM_FIELDS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_FORM_FIELDS) . ' n
                        ON p.field_id = n.field_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_FORM_FIELDS_NAME) . ' g
                        ON p.field_id = g.field_id
                        AND g.language_id= ' . (int)$lang_id . '
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_TO_CUSTOM_FIELDS) . ' c
                        ON c.custom_field_id = p.custom_field_id
                        AND c.member_id = ' . (int)$id;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = format_user_fields($q->result_array(), $data);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param array $data
	 * @param bool $public
	 * @param string $form_type
	 * @return bool|false|string
	 */
	public function get_form_fields($id = '', $lang_id = '1', $data = array(), $public = FALSE, $form_type = '')
	{
		$row = $this->get_details($id, FALSE);

		$sql = 'SELECT *, p.field_id AS field_id
                    FROM ' . $this->db->dbprefix(TBL_FORM_FIELDS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_FORM_FIELDS_NAME) . ' n
                        ON p.field_id = n.field_id
                        AND n.language_id = \'' . $lang_id . '\'
                        WHERE p.' . $this->id . '= ' . (int)$id . '';

		if ($public == TRUE)
		{
			$sql .= ' AND show_public = \'1\'';
		}

		$sql .= ' ORDER BY p.sort_order ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row['values'] = format_user_fields($q->result_array(), $data, $form_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param array $data
	 * @param bool $public
	 * @param string $form_type
	 * @return bool|false|string
	 */
	public function init_form($id = '', $lang_id = '1', $data = array(), $public = FALSE, $form_type = '')
	{
		//format all the fields to be shown on the public page such as registration

		//get the form fields by id first
		$row = $this->get_form_fields($id, $lang_id, $data, $public, $form_type);

		//get any custom field values
		$mid = !empty($data['member_id']) ? $data['member_id'] : '';
		$custom_values = $this->get_member_custom_fields($mid, $lang_id);

		if (!empty($row['values']))
		{
			foreach ($row['values'] as $k => $v)
			{
				$a = '';
				if ($v['custom'] == 0) //check if this is a custom field
				{
					if (preg_match('/billing_*/', $v['form_field']))
					{
						//check for billing;
						$a = set_form_default('billing', $v['form_field'], $data);
					}
					elseif (preg_match('/shipping_*/', $v['form_field']))
					{
						//check for shipping
						$a = set_form_default('shipping', $v['form_field'], $data);
					}
					elseif (preg_match('/payment_*/', $v['form_field']))
					{
						//check for payment
						$a = set_form_default('payment', $v['form_field'], $data);
					}

					if (!empty($a))
					{
						$row['values'][$k]['field_value'] = $a;
					}
				}

				if ($v['custom'] == 1)
				{
					//check for custom fields
					if (!empty($custom_values))
					{
						foreach ($custom_values as $c)
						{
							if (!empty($c['value']))
							{
								$row['values'][$k]['field_value'] = $c['value'];
							}
						}
					}
				}
			}

			$row['values'] = format_user_fields($row['values'], $data);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update_admin_fields($data = array())
	{
		if (!empty($data['form_field']))
		{
			foreach ($data['form_field'] as $k => $v)
			{
				$vars = $this->dbv->clean($v, TBL_FORM_FIELDS);

				$vars['field_required'] = !empty($v['field_required']) ? '1' : '0';
				$vars['show_public'] = !empty($v['show_public']) ? '1' : '0';
				$vars['show_account'] = !empty($v['show_account']) ? '1' : '0';

				//check if field is required, it should be visible
				if (!empty($vars['field_required']) && empty($vars['show_public']))
				{
					$vars['show_public'] = '1';
				}

				$this->db->where($this->field_id, $k);

				if (!$this->db->update(TBL_FORM_FIELDS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array(
				'msg_text' => lang('system_updated_successfully'),
				'success'  => TRUE,
				'data'     => $data,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update_field($data = array())
	{
		if (!empty($data['names']))
		{
			foreach ($data['names'] as $k => $v)
			{
				//check if there is a field for this language, if not, add it.
				$this->db->where($this->field_id, $data[$this->field_id]);
				if (!$q = $this->db->where('language_id', $k)->get(TBL_FORM_FIELDS_NAME))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				$vars = $this->dbv->clean($v, TBL_FORM_FIELDS_NAME);

				if (empty($vars['field_name']))
				{
					$a = config_option('sts_site_default_language');
					$vars['field_name'] = $data['names'][$a]['field_name'];
				}

				if ($q->num_rows() > 0)
				{
					$this->db->where($this->field_id, $data[$this->field_id]);
					$this->db->where('language_id', $k);

					if (!$this->db->update(TBL_FORM_FIELDS_NAME, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
				else
				{
					$vars['language_id'] = $k;
					$vars['field_id'] = $data[$this->field_id];

					if (!$this->db->insert(TBL_FORM_FIELDS_NAME, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}
		}

		$vars = $this->dbv->clean($data, TBL_FORM_FIELDS);

		$this->db->where($this->field_id, $vars[$this->field_id]);

		if (!$this->db->update(TBL_FORM_FIELDS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_sort_order($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (!$q = $this->db->where($this->field_id, $v)
					->update(TBL_FORM_FIELDS, array('sort_order' => $k))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate_form($data = array())
	{
		$this->form_validation->set_data($data);

		$required = $this->config->item(TBL_FORMS, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_FORMS);

		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			switch ($f->name)
			{
				case 'form_processor':

					$rule .= '|in_list[' . implode(',', config_option('form_processor')) . ']';

					break;

				case 'function':

					if ($data['form_processor'] == 'email')
					{
						$rule .= '|valid_email';
					}

					break;
			}

			$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if (!$this->form_validation->run())
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
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

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param array $data
	 * @param array $form_fields
	 * @return array
	 */
	public function validate_fields($type = '', $data = array(), $form_fields = array())
	{
		$this->form_validation->reset_validation();

		$this->form_validation->set_data($data);

		//run the validation for subforms
		foreach ($form_fields as $k => $v)
		{
			switch ($type)
			{
				case 'billing': //setup rules for billing sub form only

					if ($v['sub_form'] != 'billing')
					{
						unset($form_fields[$k]);
					}

					break;

				case 'payment': //for payment information only

					if ($v['sub_form'] != 'payment')
					{
						unset($form_fields[$k]);
					}

					break;

				case 'account': //for account info

					//setup rules for account and payment sub forms
					if (!empty($v['sub_form']))
					{
						unset($form_fields[$k]);
					}

					break;

				case 'shipping':  //setup rules for shipping sub form only

					if ($v['sub_form'] != 'shipping')
					{
						unset($form_fields[$k]);
					}

					break;
			}
		}

		//let's go thru each field and setup the validation rule for it ...
		foreach ($form_fields as $k => $v)
		{
			//this will generate the validation rule!
			$a = $this->generate_form_rule($type, $v);

			//set the rule for the validation class...
			$this->form_validation->set_rules($v['form_field'], $v['field_description'], $a['rule']);

			//generate custom error messages if needed
			if (!empty($a['msg']))
			{
				foreach ($a['msg'] as $b => $c)
				{
					$this->form_validation->set_message($b, $c);
				}
			}
		}

		//run check for tos
		switch ($type)
		{
			case 'account':

				if (config_enabled('sts_form_enable_tos_checkbox'))
				{
					$this->form_validation->set_rules('tos', 'lang:terms_of_service', 'required');
				}

				if (check_the_box('subscribe_box'))
				{
					$this->form_validation->set_rules('subscribe', 'lang:newsletter_subscription', 'trim');
				}

				break;

			case 'register':

				if (config_enabled('sts_form_enable_tos_checkbox'))
				{
					$this->form_validation->set_rules('tos', 'lang:terms_of_service', 'required');
				}

				if (check_the_box('subscribe_box'))
				{
					$this->form_validation->set_rules('subscribe', 'lang:newsletter_subscription', 'trim');
				}

				if ($type == 'register')
				{
					//check referrals
					if (config_enabled('sts_affiliate_require_referral_code'))
					{
						$this->form_validation->set_rules('sponsor_id', 'lang:referral', 'required|is_natural_no_zero');
						$this->form_validation->set_message('is_natural_no_zero', lang('referral_field_required'));
					}
				}

				break;
		}

		//validate it!
		if ($this->form_validation->run())
		{
			//cool! no errors...
			$row = array('success' => TRUE);

			switch ($type)
			{
				case 'register':
				case 'form':
				case 'contact':

				//run check for recaptcha
				$c = $type == 'contact' ? 'contact_' : '';
				if (config_enabled('sts_form_enable_' . $c . 'captcha'))
				{
					$this->form_validation->reset_validation();

					$this->form_validation->set_data($data);

					$this->form_validation->set_rules(
						CAPTCHA_FIELD, 'lang:captcha',
						array(
							'required',
							array('check_captcha', array($this->dbv, 'check_captcha')),
						)
					);

					$this->form_validation->set_message('check_captcha', lang('invalid_security_captcha'));

					if (!$this->form_validation->run())
					{
						//sorry! got some errors here....
						$row = array('error'        => TRUE,
						             'msg'          => validation_errors(),
						             'error_fields' => generate_error_fields($data),
						);
					}
				}

				break;
			}
		}
		else
		{
			//sorry! got some errors here....
			$row = array('error'        => TRUE,
			             'msg'          => validation_errors(),
			             'error_fields' => generate_error_fields($data),
			);
		}

		//return the filtered data back
		$row['data'] = $this->dbv->validated($data);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate_field($data = array())
	{
		$error = '';

		foreach ($data['names'] as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);

			$required = $this->config->item(TBL_FORM_FIELDS_NAME, 'required_input_fields');

			//now get the list of fields directly from the table
			$fields = $this->db->field_data(TBL_FORM_FIELDS_NAME);

			foreach ($fields as $f)
			{
				//set the default rule
				$rule = 'trim|xss_clean|strip_tags';

				if ($k == config_option('sts_site_default_language'))
				{
					//if this field is a required field, let's set that
					if (is_array($required) && in_array($f->name, $required))
					{
						$rule .= '|required';
					}
				}

				$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

				$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

			}

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}
			else
			{
				$data['names'][$k] = $this->dbv->validated($v);
			}
		}

		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$required = $this->config->item(TBL_FORM_FIELDS, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_FORM_FIELDS);

		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean|strip_tags';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

			switch ($f->name)
			{
				case 'form_field':

					$rule .= '|url_title';

					break;
			}

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
		}
		else
		{
			$data = $this->dbv->validated($data);
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
			             'data'    => $data,
			);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate_admin_fields($data = array())
	{
		$error = '';

		foreach ($data['form_field'] as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);

			//now get the list of fields directly from the table
			$fields = $this->db->field_data(TBL_FORM_FIELDS);

			foreach ($fields as $f)
			{
				//set the default rule
				$rule = 'trim|xss_clean|strip_tags';

				$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

				$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

			}

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}
			else
			{
				$data['form_field'][$k] = $this->dbv->validated($v);
			}
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
			             'data'    => $data,
			);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	protected function get_form_field_names($id = '')
	{
		$sql = 'SELECT n.*, p.image, p.name, p.language_id
				    FROM ' . $this->db->dbprefix(TBL_LANGUAGES) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_FORM_FIELDS_NAME) . ' n
				    ON p.language_id = n.language_id
                    AND n.' . $this->field_id . '= ' . (int)$id . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);

	}
}

/* End of file Forms_model.php */
/* Location: ./application/models/Forms_model.php */