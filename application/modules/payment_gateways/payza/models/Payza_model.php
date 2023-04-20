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
class Payza_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_payment_gateways_payza_title',
			'settings_value'      => 'Pay via Payza',
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
			'settings_key'        => 'module_payment_gateways_payza_description',
			'settings_value'      => 'Make your payment via Payza',
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
			'settings_key'        => 'module_payment_gateways_payza_enable_testing',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payza_email',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => 'required|valid_email',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payza_currency_code',
			'settings_value'      => 'USD',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payza_enable_debug_email',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => 'boolean',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_payza_checkout_logo',
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
			'id'       => $id,
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
				'ap_itemname' => lang('invoice_number') . ' #' . $data['invoice_number'],
				'ap_itemcode' => $data['invoice_id'],
				'ap_amount'   => format_amount($data['total'], FALSE),
				'apc_1'       => $data['order_id'],
				'apc_2'       => $data['invoice_id'],
			);
		}
		else
		{
			$vars = array(
				'ap_itemname' => lang('order_number') . ' #' . $data['order']['order_number'],
				'ap_itemcode' => $data['order']['order_id'],
				'ap_amount'   => $data['cart']['totals']['total_with_shipping'],
				'apc_1'       => $data['order']['order_id'],
			);

			if (!empty($data['invoice']['id']))
			{
				$vars['apc_2'] = $data['invoice']['id'];
			}
		}

		$vars['ap_merchant'] = config_item('module_payment_gateways_payza_email');
		$vars['ap_returnurl'] = site_url('thank_you/page/payza');
		$vars['ap_cancelurl'] = site_url();
		$vars['ap_purchasetype'] = 'item-goods';
		$vars['ap_currency'] = check_currency('module_payment_gateways_payza_currency_code');
		$vars['ap_quantity'] = '1';
		$vars['ap_description'] = config_item('sts_site_name') . ' ' . lang('payment');

		if (config_item('module_payment_gateways_payza_enable_testing') == '1')
		{
			$vars['apc_test'] = '1';
		}

		return $vars;
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		$url = config_item('module_gateway_production_url');

		$vars = $this->format_card_data($data, $type);

		$html = form_open($url, 'id="auto-form"');

		foreach ($vars as $k => $v)
		{
			$html .= form_hidden($k, $v);
		}

		$html .= '<button type="submit" class="btn btn-secondary">' . lang('please_click_here_if_you_are_not_forwarded_automatically') . ' ' . i('fa fa-caret-right') . '</button>';

		$html .= form_close();

		return $html;
	}

	public function ipn_process($data = array(), $module = array(), $type = 'checkout')
	{
		$vars = array(
			'module'         => $module['module']['module_folder'],
			'description'    => $module['module']['module_description'],
			'status'         => '1',
			'transaction_id' => $data['ap_referencenumber'],
			'amount'         => $data['ap_totalamount'],
			'fee'            => $data['ap_feeamount'],
			'currency_code'  => check_currency('module_payment_gateways_payza_currency_code'),
			'debug'          => serialize($data),
			'invoice_id'     => is_var($data, 'apc_2'),
			'order_id'       => $type = 'checkout' ? is_var($data, 'ap_itemcode') : '',
			'card_data'      => array(),
		);

		switch ($data['ap_transactionstate'])
		{
			case 'Completed':

				$vars['type'] = 'payment';

				break;

			case 'Refunded':

				$vars['type'] = 'refund';

				break;
		}

		return array('success' => TRUE,
		             'data'    => $vars,
		             'post'    => $data,
		);
	}

	public function ipn_verify()
	{
		if (empty($_POST['token'])) exit;

		//The value is the url address of IPN V2 handler and the identifier of the token string
		define("IPN_V2_HANDLER", config_item('module_gateway_production_ipn_handler'));
		define("TOKEN_IDENTIFIER", "token=");

		// get the token from Payza
		$token = urlencode($_POST['token']);

		//preappend the identifier string "token="
		$token = TOKEN_IDENTIFIER . $token;

		/**
		 *
		 * Sends the URL encoded TOKEN string to the Payza's IPN handler
		 * using cURL and retrieves the response.
		 *
		 * variable $response holds the response string from the Payza's IPN V2.
		 */

		$response = '';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, IPN_V2_HANDLER);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);

		curl_close($ch);

		if (strlen($response) > 0)
		{
			if (urldecode($response) == "INVALID TOKEN")
			{
				//the token is not valid
				$row = array('error' => TRUE,
				);
			}
			else
			{
				//urldecode the received response from Payza's IPN V2
				$response = urldecode($response);

				//split the response string by the delimeter "&"
				$aps = explode("&", $response);

				//define an array to put the IPN information
				$info = array();

				foreach ($aps as $ap)
				{
					//put the IPN information into an associative array $info
					$ele = explode("=", $ap);
					$info[$ele[0]] = $ele[1];
				}

				$row = array('success' => TRUE,
				             'data'    => $info,
				);

				$this->ipn_log($row, 'check');
			}
		}
		else
		{
			//something is wrong, no response is received from Payza
			$row = array('error' => TRUE);
		}

		//send a debug email if set...
		if (config_item('module_payment_gateways_payza_enable_debug_email'))
		{
			send_debug_email($info, 'Payza IPN debug');
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function ipn_log($data = array(), $type = 'check')
	{
		$trans_id = $data['data']['transaction_id'];
		$trans_id .= config_enabled('module_payment_gateways_payza_enable_testing') ? '-' . time() : '';

		$vars = array('type'         => 'payza',
		              'reference_id' => $trans_id,
		              'data'         => $data['data']['debug']);

		$this->pay->ipn_log($vars, $type);
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

/* End of file Payza_model.php */
/* Location: ./application/models/Payza_model.php */