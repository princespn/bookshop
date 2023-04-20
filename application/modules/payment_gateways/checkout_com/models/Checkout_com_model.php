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
class Checkout_com_model extends Modules_model
{
	protected $table = 'module_payment_gateway_checkout_com_members';

	protected $secret_key = '';
	protected $public_key = '';
	protected $endpoint = '';

	public function __construct()
	{
		parent::__construct();

		$this->secret_key = config_item('module_payment_gateways_checkout_com_api_secret_key');
		$this->public_key = config_item('module_payment_gateways_checkout_com_api_public_key');
		$this->endpoint = config_item('module_gateway_production_url');

		if (config_enabled('module_payment_gateways_checkout_com_enable_testing'))
		{
			$this->secret_key = config_item('module_payment_gateways_checkout_com_api_test_secret_key');
			$this->public_key = config_item('module_payment_gateways_checkout_com_api_test_public_key');
			$this->endpoint = config_item('module_gateway_test_url');
		}
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
                  `card_token` varchar(255) NOT NULL DEFAULT \'\',
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
			'settings_key'        => 'module_payment_gateways_checkout_com_title',
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
			'settings_key'        => 'module_payment_gateways_checkout_com_description',
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
			'settings_key'        => 'module_payment_gateways_checkout_com_api_secret_key',
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
			'settings_key'        => 'module_payment_gateways_checkout_com_api_public_key',
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
			'settings_key'        => 'module_payment_gateways_checkout_com_currency',
			'settings_value'      => 'USD',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_checkout_com_enable_testing',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_checkout_com_api_test_secret_key',
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
			'settings_key'        => 'module_payment_gateways_checkout_com_api_test_public_key',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_checkout_com_save_customer_token',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '9',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_checkout_com_checkout_logo',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '10',
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
			case 'cron':

				$vars = array(
					'url'   => $this->endpoint,
					'total' => round($data['total'], 2) * 100,
					'name'  => $data['customer_name'],
					'email' => $data['customer_primary_email'],

				);

				break;

			case 'refund':

				$vars = array(
					'url'       => $this->endpoint . '/' . $data['transaction_id'] . '/refunds',
					'amount'    => round($data['refund_amount'], 2) * 100,
					'id'        => $data['transaction_id'],
					'reference' => $data['invoice_id'],
				);

				break;

			case 'card_on_file':

				$vars = array(
					'url'                => $this->endpoint,
					'source'             => array('type' => 'id',
					                              'id'   => $data['payment_data']['card_token']),
					'currency'           => check_currency('module_payment_gateways_checkout_com_currency'),
					'reference'          => $data['member_id'] . '-' . $data['payment_data']['email'],
					'customer'           => array('email' => $data['payment_data']['email'],
					                              'name'  => $data['payment_data']['name']),
					'billing_descriptor' => array('name' => config_item('sts_site_name'),
					                              'city' => config_item('sts_site_shipping_city')),
					'amount'             => round($data['cart']['totals']['total_with_shipping'], 2) * 100,
				);

				break;

			default:

				$vars = array(
					'url'                => $this->endpoint,
					'source'             => array('type'  => 'token',
					                              'token' => $data['payment_data']['card_token']),
					'currency'           => check_currency('module_payment_gateways_checkout_com_currency'),
					'reference'          => $data['member_id'] . '-' . $data['payment_data']['email'],
					'customer'           => array('email' => $data['payment_data']['email'],
					                              'name'  => $data['payment_data']['name']),
					'billing_descriptor' => array('name' => $data['payment_data']['name'],
					                              'city' => $data['billing_city']),
					'amount'             => round($data['cart']['totals']['total_with_shipping'], 2) * 100,
				);

				break;
		}

		return $vars;
	}

	public function send_data($data = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $data['url']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  //Post Fields
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$headers = [
			'Authorization: ' . $this->secret_key,
			'Content-Type: application/json',
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$resp = curl_exec($ch);

		curl_close($ch);

		return $resp;
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		$data['payment_data'] = $this->input->post();

		if ($this->input->post('card_token'))
		{
			$payment_data = $this->format_card_data($data, $type);
		}
		else
		{
			//check if the user is logged in and has a token on file
			if (!empty($data['member_id']))
			{
				if ($row = $this->get_customer_token($data['member_id']))
				{
					//got token...
					$data['payment_data'] = array_merge($data['payment_data'], $row);

					$payment_data = $this->format_card_data($data, 'card_on_file');
				}
			}
		}

		if (!empty($payment_data))
		{
			try
			{
				$a = $this->send_data($payment_data);

				$resp = json_decode($a);
			} catch (Exception $e)
			{
				$body = $e->getJsonBody();

				return array('type'     => 'error',
				             'msg_text' => $e->getMessage(),
				);
			}

			if (isset($resp->status))
			{
				switch ($resp->status)
				{
					case 'Authorized':

						$vars = array(
							'type'           => 'success',
							'msg_text'       => 'payment_generated_successfully',
							'module'         => $module['module']['module_folder'],
							'description'    => $module['module']['module_description'],
							'status'         => '1',
							'transaction_id' => $resp->id,
							'amount'         => show_percent($resp->amount),
							'fee'            => '',
							'currency_code'  => check_currency('module_payment_gateways_checkout_com_currency'),
							'customer_token' => $resp->customer->id,
							'debug'          => serialize($resp),
							'card_data'      => array(
								'customer_token' => $resp->customer->id,
								'card_token'     => $resp->source->id,
								'cc_type'        => $resp->source->scheme,
								'cc_four'        => $resp->source->last4,
								'cc_month'       => $resp->source->expiry_month,
								'cc_year'        => $resp->source->expiry_year,
							),
						);

						if (sess('member_id'))
						{
							$this->add_customer_token(sess('member_id'), $vars['card_data']);
						}

						return $vars;

						break;

					case 'Declined':

						return array('type'     => 'error',
						             'msg_text' => lang('card_declined'),
						);

						break;
				}
			}
			elseif ($resp->error_type)
			{
				return array('type'     => 'error',
				             'msg_text' => $resp->error_codes[0],
				);
			}
		}

		return array('type'     => 'error',
		             'msg_text' => lang('invalid_gateway_access'),
		);
	}

	public function add_customer_token($id = '', $data = array())
	{
		if (config_enabled('module_payment_gateways_checkout_com_save_customer_token'))
		{
			if (!empty($data['card_token']) && !empty($data['customer_token']))
			{
				$row = $this->dbv->get_record('module_payment_gateway_checkout_com_members', 'member_id', $id);

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

		if ($q->num_rows() > 0)
		{
			$a = $q->row_array();
			$a['saved'] = 'saved';
		}

		return empty($a) ? FALSE : $a;
	}

	public function process_refund($data = array())
	{
		//set the totals
		$payment_data = $this->format_card_data($data, 'refund');

		try
		{
			$a = $this->send_data($payment_data);

			$resp = json_decode($a);

		} catch (Exception $e)
		{
			$body = $e->getJsonBody();

			return array('type'     => 'error',
			             'msg_text' => $e->getMessage(),
			);
		}

		if ($resp->reference)
		{
			$vars = array('success' => TRUE,
			              'data'    => $data);

			$vars['data']['date'] = get_time(now(), TRUE);
			$vars['data']['refund_amount'] = show_percent($payment_data['amount']);
			$vars['data']['fee'] = '0';
			$vars['data']['transaction_id'] = $resp->action_id;
			$vars['data']['notes'] = $payment_data['id'];
			$vars['data']['debug_info'] = base64_encode(serialize($resp));
		}
		else
		{
			$vars['error'] = TRUE;
			$vars['msg_text'] = lang('could_not_connect_to_gateway');
			$vars['data']['debug_info'] = base64_encode(serialize($resp));
		}

		return !empty($vars) ? $vars : FALSE;

	}
}

/* End of file Checkout_com_model.php */
/* Location: ./application/models/Checkout_com_model.php */