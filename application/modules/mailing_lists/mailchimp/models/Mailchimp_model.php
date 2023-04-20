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
class Mailchimp_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_mailing_lists_mailchimp_api_key',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_mailchimp_audience_id',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => '',
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
		$this->mod->remove_config($id, 'mailing_lists');

		//update sts_email_mailing_list_module
		if (config_option('sts_email_mailing_list_module') == $id)
		{
			$this->set->update_db_settings(array('sts_email_mailing_list_module' => 'internal'));
		}

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function add_user($list = '', $email = '', $data = array())
	{
		$data = array(
			'email'   => $email,
			'list_id' =>  config_option('module_mailing_lists_mailchimp_audience_id'),
			'api_key' => config_option('module_mailing_lists_mailchimp_api_key'),
			'json'    => json_encode(array(
				'email_address' => $email,
				'status'        => config_option('module_subscriber_status'),
				'merge_fields'  => array(
					'FNAME'    => is_var($data['fname']),
					'LNAME'    => is_var($data['lname']),
					'USERNAME' => is_var($data['username']),
				),
			)),
		);

		$row = $this->send($data);
		return empty($row) ? FALSE : $row;
	}

	public function remove_user($list = '', $email = '', $data = array())
	{
		if ($list = $this->get_list_id($list))
		{
			$data = array(
				'email'   => $email,
				'list_id' => config_option('module_mailing_lists_mailchimp_audience_id'),
				'api_key' => config_option('module_mailing_lists_mailchimp_api_key'),
				'json'    => json_encode(array(
					'email_address' => $data['email'],
				)),
			);

			$row = $this->send($data, 'DELETE');
		}

		return empty($row) ? FALSE : $row;
	}

	protected function send($data = array(), $type = 'PUT')
	{
		//get the right datacenter....
		$dataCenter = substr($data['api_key'], strpos($data['api_key'], '-') + 1);

		//set the API URL
		$url = 'https://' . $dataCenter . '.' . config_option('module_api_url') . '/lists/' . $data['list_id'] . '/members/' . md5(strtolower($data['email']));

		//initialize curl...
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $data['api_key']);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data['json']);

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $httpCode;
	}

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
/* End of file Mailchimp_model.php */
/* Location: ./modules/mailing_lists/mailchimp/models/Mailchimp_model.php */