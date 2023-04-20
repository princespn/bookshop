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
class Worldpay_model extends Modules_model
{
	protected $table = 'module_payment_gateway_worldpay_members';

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
			'settings_key'        => 'module_payment_gateways_worldpay_title',
			'settings_value'      => 'Pay via Worldpay',
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
			'settings_key'        => 'module_payment_gateways_worldpay_description',
			'settings_value'      => 'Make your payment via Worldpay',
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
			'settings_key'        => 'module_payment_gateways_worldpay_client_key',
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
			'settings_key'        => 'module_payment_gateways_worldpay_service_key',
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
			'settings_key'        => 'module_payment_gateways_worldpay_currency',
			'settings_value'      => 'USD',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_worldpay_save_customer_token',
			'settings_value'      => '0',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_worldpay_checkout_logo',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '9',
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
					'token'             => $data['customer_token'],
					'amount'            => format_amount($data['total'], FALSE) * 100,
					'currencyCode'      => check_currency('module_payment_gateways_worldpay_currency'),
					'name'              => $data['customer_name'],
					'billingAddress'    => array(
						'address1'    => is_var($data, 'customer_address_1'),
						'address2'    => is_var($data, 'customer_address_2'),
						'address3'    => '',
						'city'        => is_var($data, 'customer_city'),
						'state'       => is_var($data, 'customer_state_code'),
						'countryCode' => is_var($data, 'customer_country_code_2'),
						'postalCode'  => is_var($data, 'customer_postal_code'),
					),
					'orderDescription'  => config_item('sts_site_name') . ' ' . lang('payment'),
					'customerOrderCode' => is_var($data, 'invoice_id'),
				);

				break;

			case 'cron':

				$vars = array(
					'token'             => $data['customer_token'],
					'amount'            => format_amount($data['total'], FALSE) * 100,
					'currencyCode'      => check_currency('module_payment_gateways_worldpay_currency'),
					'name'              => $data['customer_name'],
					'billingAddress'    => array(
						'address1'    => is_var($data, 'customer_address_1'),
						'address2'    => is_var($data, 'customer_address_2'),
						'address3'    => '',
						'city'        => is_var($data, 'customer_city'),
						'state'       => is_var($data, 'customer_state_code'),
						'countryCode' => is_var($data, 'customer_country_code_2'),
						'postalCode'  => is_var($data, 'customer_postal_code'),
					),
					'orderDescription'  => config_item('sts_site_name') . ' ' . lang('payment'),
					'customerOrderCode' => is_var($data, 'invoice_id', FALSE, $data['member_id']),
				);

				break;

			default:

				$vars = array(
					'token'             => $this->input->post('token'),
					'amount'            => format_amount($data['cart']['totals']['total_with_shipping'], FALSE) * 100,
					'currencyCode'      => check_currency('module_payment_gateways_worldpay_currency'),
					'name'              => $this->input->post('name'),
					'billingAddress'    => array(
						'address1'    => is_var($data, 'billing_address_1'),
						'address2'    => is_var($data, 'billing_address_2'),
						'address3'    => '',
						'city'        => is_var($data, 'billing_city'),
						'state'       => is_var($data, 'billing_state_code'),
						'countryCode' => is_var($data, 'billing_country_iso_code_2'),
						'postalCode'  => is_var($data, 'billing_postal_code'),
					),
					'orderDescription'  => config_item('sts_site_name') . ' ' . lang('payment'),
					'customerOrderCode' => is_var($data, 'member_id'),
				);

				break;

		}

		return $vars;
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		require_once(APPPATH . '/modules/payment_gateways/worldpay/libraries/init.php');

		//lets charge the card number given
		if ($this->input->post('token'))
		{
			$payment_data = $this->format_card_data($data, $type);
		}
		elseif (!empty($data['member_id']))
		{
			if ($row = $this->get_customer_token($data['member_id']))
			{
				$vars = array_merge($data, $row);
				$payment_data = $this->format_card_data($vars, $type);
			}
		}

		if (!empty($payment_data))
		{
			$worldpay = new Worldpay(config_item('module_payment_gateways_worldpay_service_key'));

			try
			{
				$response = $worldpay->createOrder($payment_data);

				if ($response['paymentStatus'] === 'SUCCESS')
				{
					$vars = array(
						'type'           => 'success',
						'module'         => $module['module']['module_folder'],
						'description'    => $module['module']['module_description'],
						'status'         => 1,
						'transaction_id' => $response['orderCode'],
						'amount'         => show_percent($response['amount']),
						'fee'            => '',
						'currency_code'  => check_currency('module_payment_gateways_worldpay_currency'),
						'customer_token' => $response['token'],
						'debug'          => serialize($response),
						'card_data'      => array(
							'customer_token' => $response['token'],
							'cc_type'        => $response['paymentResponse']['cardType'],
							'cc_four'        => substr($response['paymentResponse']['maskedCardNumber'], -4),
							'cc_month'       => $response['paymentResponse']['expiryMonth'],
							'cc_year'        => $response['paymentResponse']['expiryYear'],
						),
					);

					//check if we are saving the customer token
					if (sess('member_id') && config_enabled('module_payment_gateways_worldpay_save_customer_token'))
					{
						$this->add_customer_token(sess('member_id'), $vars['card_data']);
					}

					return $vars;
				}
				else
				{
					return array('type'     => 'error',
					             'msg_text' => $response['paymentStatus'],
					);
				}
			} catch (WorldpayException $e)
			{
				$error = 'Error code: ' . $e->getCustomCode() . '<br />
						    HTTP status code:' . $e->getHttpStatusCode() . '<br />
						    Error description: ' . $e->getDescription() . '<br />
						    Error message: ' . $e->getMessage();

				return array('type'     => 'error',
				             'msg_text' => $error,
				);

			} catch (Exception $e)
			{
				echo 'Error message: ' . $e->getMessage();

				return array('type'     => 'error',
				             'msg_text' => $e->getMessage(),
				);
			}
		}


		return array('type'     => 'error',
		             'msg_text' => lang('invalid_gateway_access'),
		);
	}

	public function add_customer_token($id = '', $data = array())
	{
		$row = $this->dbv->get_record('module_payment_gateway_worldpay_members', 'member_id', $id);

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

/* End of file Worldpay_model.php */
/* Location: ./application/models/Worldpay_model.php */