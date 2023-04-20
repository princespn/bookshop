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
class Payeezy_js_model extends Modules_model
{
	protected $table = 'module_payment_gateway_payeezy_js_members';

	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix($this->table) . ';';

		if (!$q = $this->db->query($delete))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//install db table
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->db->dbprefix($this->table) . ' (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `member_id` int(10) NOT NULL DEFAULT \'0\',
                  `customer_token` varchar(255) NOT NULL DEFAULT \'\',
                  `cc_type` varchar(50) NOT NULL DEFAULT \'\',
                  `cc_four` int(4) NOT NULL DEFAULT \'0\',
                  `cc_month` int(2) NOT NULL DEFAULT \'0\',
                  `cc_year` int(4) NOT NULL DEFAULT \'0\',
                  PRIMARY KEY (`id`),
                   KEY `member_id` (`member_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_title',
			'settings_value'      => 'Pay via Credit Card',
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
			'settings_key'        => 'module_payment_gateways_payeezy_js_description',
			'settings_value'      => 'Make your payment via credit card',
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
			'settings_key'        => 'module_payment_gateways_payeezy_js_api_key',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_api_secret',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_js_security_key',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_merchant_token',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_ta_token',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_currency',
			'settings_value'      => 'USD',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_environment',
			'settings_value'      => 'production',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '9',
			'settings_function'   => 'production_sandbox',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_auth_only',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '10',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_save_customer_token',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '11',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payeezy_js_checkout_logo',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '12',
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
		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix($this->table) . ';';

		if (!$q = $this->db->query($delete))
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

	public function format_card_data($data = array(), $type = 'checkout')
	{
		switch ($type)
		{
			case 'invoice':

				$vars = array(
					'merchant_ref'     => is_var($data, 'member_id'),
					'transaction_type' => 'purchase',
					'method'           => 'token',
					'amount'           => format_amount($data['total'], FALSE) * 100,
					'currency_code'    => check_currency('module_payment_gateways_payeezy_js_currency'),
					'token'            => array(
						'token_type' => 'FDToken',
						'token_data' => array(
							'type'            => $this->input->post('type'),
							'value'           => $this->input->post('token'),
							'cardholder_name' => $this->input->post('cardholder_name'),
							'exp_date'        => $this->input->post('exp_date'),

						),
					),
				);

				break;

			case 'cron':

				$vars = array(
					'merchant_ref'     => is_var($data, 'member_id'),
					'transaction_type' => 'purchase',
					'method'           => 'token',
					'amount'           => format_amount($data['total'], FALSE) * 100,
					'currency_code'    => check_currency('module_payment_gateways_payeezy_js_currency'),
					'token'            => array(
						'token_type' => 'FDToken',
						'token_data' => array(
							'type'            => $data['cc_type'],
							'value'           => $data['customer_token'],
							'cardholder_name' => $data['customer_name'],
							'exp_date'        => $data['cc_month'] . $data['cc_year'],

						),
					),
				);

				break;

			default:

				$vars = array(
					'merchant_ref'     => is_var($data, 'member_id'),
					'transaction_type' => 'purchase',
					'method'           => 'token',
					'amount'           => format_amount($data['cart']['totals']['total_with_shipping'], FALSE) * 100,
					'currency_code'    =>check_currency('module_payment_gateways_payeezy_js_currency'),
					'token'            => array(
						'token_type' => 'FDToken',
						'token_data' => array(
							'type'            => $this->input->post('type'),
							'value'           => $this->input->post('token'),
							'cardholder_name' => $this->input->post('cardholder_name'),
							'exp_date'        => $this->input->post('exp_date'),

						),
					),
				);

				break;
		}

		return $vars;
	}

	public function generate_gateway_form($data = array(), $amount = '0', $type = 'checkout')
	{
		if ($type == 'invoice')
		{
			$vars = array('email'           => is_var($data, 'customer_primary_email'),
			              'currency'        => check_currency('module_payment_gateways_payeezy_js_currency'),
			              'street'          => is_var($data, 'customer_address_1'),
			              'city'            => is_var($data, 'customer_city'),
			              'state_province'  => is_var($data, 'customer_state_code'),
			              'country'         => is_var($data, 'customer_country_iso_code_2'),
			              'zip_postal_code' => is_var($data, 'customer_postal_code'),
			);
		}
		else
		{
			$vars = array('email'           => is_var($data, 'primary_email'),
			              'currency'        => check_currency('module_payment_gateways_payeezy_js_currency'),
			              'street'          => is_var($data, 'billing_address_1'),
			              'city'            => is_var($data, 'billing_city'),
			              'state_province'  => is_var($data, 'billing_state_code'),
			              'country'         => is_var($data, 'billing_country_iso_code_2'),
			              'zip_postal_code' => is_var($data, 'billing_postal_code'),
			);
		}

		return $vars;
	}

	public function hmacAuthorizationToken($payload)
	{
		$nonce = strval(hexdec(bin2hex(openssl_random_pseudo_bytes(4, $cstrong))));
		$timestamp = strval(time() * 1000); //time stamp in milli seconds
		$data = config_item('module_payment_gateways_payeezy_js_api_key') . $nonce . $timestamp . config_item('module_payment_gateways_payeezy_js_merchant_token') . $payload;
		$hashAlgorithm = "sha256";
		$hmac = hash_hmac($hashAlgorithm, $data, config_item('module_payment_gateways_payeezy_js_api_secret'), FALSE);    // HMAC Hash in hex
		$authorization = base64_encode($hmac);

		return array(
			'authorization' => $authorization,
			'nonce'         => $nonce,
			'timestamp'     => $timestamp,
		);
	}

	public function postTransaction($payload)
	{
		$url = config_item('module_payment_gateways_payeezy_js_environment') == 'production' ? config_item('module_gateway_production_url') : config_item('module_gateway_test_url');

		$headers = $this->hmacAuthorizationToken($payload);
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $url);
		curl_setopt($request, CURLOPT_POST, TRUE);
		curl_setopt($request, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($request, CURLOPT_HEADER, FALSE);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($request, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'apikey:' . config_item('module_payment_gateways_payeezy_js_api_key'),
			'token:' . config_item('module_payment_gateways_payeezy_js_merchant_token'),
			'Authorization:' . $headers['authorization'],
			'nonce:' . $headers['nonce'],
			'timestamp:' . $headers['timestamp'],
		));
		$response = curl_exec($request);
		if (FALSE === $response)
		{
			echo curl_error($request);
		}
		//$httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
		curl_close($request);

		return $response;
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		//lets charge the card number given
		if ($this->input->post())
		{
			//set the payment data
			$payment_data = $this->format_card_data($data, $type);

		}
		else
		{
			//check if the user is logged in and has a token on file
			if (!empty($data['member_id']))
			{
				if ($row = $this->get_customer_token($data['member_id']))
				{
					$vars = array_merge($data, $row);
					$payment_data = $this->format_card_data($vars, $type);
				}
			}
		}

		if (!empty($payment_data))
		{
			$vars = json_encode($payment_data, JSON_FORCE_OBJECT);

			$row = json_decode($this->postTransaction($vars));

			//we created a customer successfully, not we can charge his card
			if (isset($row->transaction_status))
			{
				if ($row->transaction_status == 'approved')
				{
					$vars = array(
						'type'           => 'success',
						'module'         => $module['module']['module_folder'],
						'description'    => $module['module']['module_description'],
						'status'         => '1',
						'transaction_id' => $row->transaction_id,
						'amount'         => show_percent($row->amount),
						'fee'            => '',
						'currency_code'  => check_currency('module_payment_gateways_payeezy_js_currency'),
						'customer_token' => $row->token->token_data->value,
						'debug'          => serialize($row),
						'card_data'      => array(
							'customer_token' => $row->token->token_data->value,
							'cc_type'        => $row->token->token_data->type,
							'cc_four'        => '',
							'cc_month'       => substr($row->token->token_data->exp_date, 0, 2),
							'cc_year'        => substr($row->token->token_data->exp_date, 2, 4),
						),
					);

					//check if we are saving the customer token
					if (sess('member_id') && config_enabled('module_payment_gateways_payeezy_js_save_customer_token'))
					{
						$this->add_customer_token(sess('member_id'), $vars['card_data']);
					}

					return $vars;
				}
			}
		}

		return array('type'     => 'error',
		             'msg_text' => lang('invalid_gateway_access'),
		);
	}

	public function add_customer_token($id = '', $data = array())
	{
		$row = $this->dbv->get_record('module_payment_gateway_payeezy_js_members', 'member_id', $id);

		if (!empty($row))
		{
			if (!$this->db->where('member_id', $id)->update($this->table, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}
		else
		{
			$data['member_id'] = $id;

			if (!$this->db->insert($this->table, $data))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	public function delete_customer_token($id = '')
	{
		if (!$this->db->where('member_id', $id)->delete($this->table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	public function update_module($data = array())
	{
		//update module data
		$row = $this->mod->update($data);

		return $row;
	}

	public function validate_payment_module($data = array(), $table = '')
	{
		$row = $this->pay->validate_module($data, $table);

		return $row;
	}

	public function get_customer_token($id = '')
	{
		$this->db->where('member_id', (int)$id);
		if (!$q = $this->db->get($this->table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}
}

/* End of file Payeezy_js_model.php */
/* Location: ./application/models/Payeezy_js_model.php */