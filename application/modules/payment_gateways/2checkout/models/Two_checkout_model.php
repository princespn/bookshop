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
class Two_checkout_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_payment_gateways_2checkout_title',
			'settings_value'      => 'Pay via 2checkout',
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
			'settings_key'        => 'module_payment_gateways_2checkout_description',
			'settings_value'      => 'Make your payment via 2checkout',
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
			'settings_key'        => 'module_payment_gateways_2checkout_environment',
			'settings_value'      => 'production',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'production_sandbox',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_2checkout_seller_id',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_2checkout_publishable_key',
			'settings_value'      => '',
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
			'settings_key'        => 'module_payment_gateways_2checkout_private_key',
			'settings_value'      => '',
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
			'settings_key'        => 'module_payment_gateways_2checkout_currency',
			'settings_value'      => 'USD',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_2checkout_checkout_logo',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
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
		//remove settings from database
		$this->mod->remove_config($id, 'payment_gateways');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function format_card_data($data = array(), $type = 'checkout')
	{
		if ($type == 'invoice')
		{
			$vars = array(
				'merchantOrderId' => !empty($data['invoice_number']) ? $data['invoice_number'] : $data['member_id'],
				'token'           => $this->input->post('token'),
				'currency'        => check_currency('module_payment_gateways_2checkout_currency'),
				'total'           => format_amount($data['total'], FALSE),
				'billingAddr'     => array(
					'name'        => $data['customer_name'],
					'addrLine1'   => is_var($data, 'customer_address_1'),
					'city'        => is_var($data, 'customer_city'),
					'state'       => is_var($data, 'customer_state_code'),
					'zipCode'     => is_var($data, 'customer_postal_code'),
					'country'     => is_var($data, 'customer_country_code_3'),
					'email'       => is_var($data, 'customer_primary_email'),
					'phoneNumber' => is_var($data, 'customer_telephone'),
				),
			);
		}
		else
		{
			$vars = array(
				'merchantOrderId' => is_var($data, 'member_id'),
				'token'           => $this->input->post('token'),
				'currency'        => check_currency('module_payment_gateways_2checkout_currency'),
				'total'           => $data['cart']['totals']['total_with_shipping'],
				'billingAddr'     => array(
					'name'        => $data['billing_fname'] . ' ' . is_var($data, 'billing_lname'),
					'addrLine1'   => is_var($data, 'billing_address_1'),
					'city'        => is_var($data, 'billing_city'),
					'state'       => is_var($data, 'billing_state_code'),
					'zipCode'     => is_var($data, 'billing_postal_code'),
					'country'     => is_var($data, 'billing_country_iso_code_3'),
					'email'       => is_var($data, 'primary_email'),
					'phoneNumber' => is_var($data, 'home_phone'),
				),
			);
		}

		return $vars;
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		require_once(APPPATH . '/modules/payment_gateways/2checkout/libraries/Twocheckout.php');

		Twocheckout::privateKey(config_item('module_payment_gateways_2checkout_private_key'));
		Twocheckout::sellerId(config_item('module_payment_gateways_2checkout_seller_id'));

		$sandbox = config_item('module_payment_gateways_2checkout_environment') == 'sandbox' ? TRUE : FALSE;
		Twocheckout::sandbox($sandbox);

		//lets charge the card number given
		if ($this->input->post('token'))
		{
			try
			{
				$vars = $this->format_card_data($data, $type);

				$charge = Twocheckout_Charge::auth($vars);

				if ($charge['response']['responseCode'] == 'APPROVED')
				{
					$vars = array(
						'type'           => 'success',
						'module'         => $module['module']['module_folder'],
						'description'    => $module['module']['module_description'],
						'status'         => '1',
						'transaction_id' => $charge['response']['transactionId'],
						'amount'         => $charge['response']['total'],
						'fee'            => '0',
						'currency_code'  => $vars['currency'],
						'customer_token' => '',
						'debug'          => serialize($charge),
						'card_data'      => array(),
					);

					return $vars;
				}
			} catch (Twocheckout_Error $e)
			{
				return array('type'     => 'error',
				             'msg_text' => $e->getMessage(),
				);
			}

		}

		return array('type'     => 'error',
		             'msg_text' => lang('invalid_gateway_access'),
		);
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
}

/* End of file Two_checkout_model.php */
/* Location: ./application/models/Two_checkout_model.php */