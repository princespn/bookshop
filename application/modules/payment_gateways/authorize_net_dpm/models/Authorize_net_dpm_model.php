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

use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

class Authorize_net_dpm_model extends Modules_model
{
	protected $table = 'module_payment_gateway_authorize_net_members';

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
			'settings_key'        => 'module_payment_gateways_authorize_net_title',
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
			'settings_key'        => 'module_payment_gateways_authorize_net_description',
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
			'settings_key'        => 'module_payment_gateways_authorize_net_environment',
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
			'settings_key'        => 'module_payment_gateways_authorize_net_api_login_id',
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
			'settings_key'        => 'module_payment_gateways_authorize_net_transaction_key',
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
			'settings_key'        => 'module_payment_gateways_authorize_net_currency',
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
			'settings_key'        => 'module_payment_gateways_authorize_net_auth_only',
			'settings_value'      => '1',
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
			'settings_key'        => 'module_payment_gateways_authorize_net_enable_cvv',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_authorize_net_checkout_logo',
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
		if ($type == 'invoice')
		{
			$customer = explode(' ', $data['customer_name']);
			$lname = end($customer);
			$fname = str_replace($lname, '', $data['customer_name']);

			$vars = array(
				'billingFirstName' => trim($fname),
				'billingLastName'  => trim($lname),

				'billingAddress1' => is_var($data,'customer_address_1'),
				'billingAddress2' => is_var($data,'customer_address_2'),
				'billingState'    => is_var($data,'customer_state_code'),
				'billingCity'     => is_var($data,'customer_city'),
				'billingPostcode' => is_var($data,'customer_postal_code'),
				'billingCountry'  => is_var($data,'customer_country_code_2'),
				'email'           => is_var($data,'customer_primary_email'),
				'number'          => null,
				'expiryMonth'     => null,
				'expiryYear'      => null,
				'cvv'             => null,
			);
		}
		else
		{
			$vars = array(
				'billingFirstName' => is_var($data,'billing_fname'),
				'billingLastName'  => is_var($data,'billing_lname'),

				'billingAddress1' => is_var($data,'billing_address_1'),
				'billingAddress2' => is_var($data,'billing_address_2'),
				'billingState'    => is_var($data,'billing_state_code'),
				'billingCity'     => is_var($data,'billing_city'),
				'billingPostcode' => is_var($data,'billing_postal_code'),
				'billingCountry'  => is_var($data,'billing_country_iso_code_2'),
				'email'           => is_var($data,'primary_email'),
				'number'          => null,
				'expiryMonth'     => null,
				'expiryYear'      => null,
				'cvv'             => null,
			);
		}

		return $vars;
	}

	public function generate_gateway_form($data = array(), $amount = '0', $type = 'checkout')
	{
		$gateway = Omnipay::create('AuthorizeNet_DPM');

		$gateway->setApiLoginId(config_item('module_payment_gateways_authorize_net_api_login_id'));
		$gateway->setTransactionKey(config_item('module_payment_gateways_authorize_net_transaction_key'));

		$card = new CreditCard($this->format_card_data($data, $type));

		$purchase = config_item('module_payment_gateways_authorize_net_auth_only') == '1' ? 'authorize' : 'purchase';

		$vars = array('amount'         => $amount,
		              'currency'       => check_currency('module_payment_gateways_authorize_net_currency'),
		              'transactionId'  => !empty($data['invoice_number']) ? $data['invoice_number'] : !empty($data['member_id']) ? $data['member_id'] : $data['primary_email'],
		              'card'           => $card,
		              'description'    => config_item('sts_site_name') . ' ' . lang('payment'),
		              'customerId'     => !empty($data['member_id']) ? $data['member_id'] : $data['primary_email'],
		              'shippingAmount' => 0,
		              'taxAmount'      => 0,
		              'returnUrl' => site_url('checkout/payment/redirect/authorize_net'),
		              'cancelUrl'      => site_url(),
		);

		$vars['returnUrl'] .= $type == 'invoice' ? '/invoice' : '';

		$request = $gateway->$purchase($vars);

		try
		{
			$response = $request->send();

			if ($response->isRedirect())
			{
				$row = $response->getData();
			}

		} catch (Exception $e)
		{
			show_error($e->getMessage());
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{

		//check for response
		switch ($this->input->post('x_response_code'))
		{
			case '2':
			case '3':
				//error processing the payment... let's go back ....
				$this->session->set_flashdata('error', $this->input->post('x_response_reason_text'));

				redirect_page(site_url('checkout/cart/?step=payment'));

				break;
		}

		//we're all good...
		$vars = array(
			'type'           => 'success',
			'module'         => $module['module']['module_folder'],
			'description'    => $module['module']['module_description'],
			'status'         => $this->input->post('x_response_code') == '4' ? '0' : '1',
			'transaction_id' => $this->input->post('x_trans_id', TRUE),
			'amount'         => $this->input->post('x_amount', TRUE),
			'currency_code'  => check_currency('module_payment_gateways_authorize_net_currency'),
			'fee'            => '0',
			'customer_token' => '',
			'debug'          => serialize($this->input->post()),
			'card_data'      => array(),
		);

		return $vars;
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

/* End of file Authorize_net_model.php */
/* Location: ./application/models/Authorize_net_model.php */