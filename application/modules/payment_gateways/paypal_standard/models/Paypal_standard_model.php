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
class Paypal_standard_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_standard_title',
			'settings_value'      => 'Pay via Paypal',
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
			'settings_key'        => 'module_payment_gateways_paypal_standard_description',
			'settings_value'      => 'Make your payment via Paypal Standard Payments',
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
			'settings_key'        => 'module_payment_gateways_paypal_standard_enable_testing',
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
			'settings_key'        => 'module_payment_gateways_paypal_standard_paypal_email',
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
			'settings_key'        => 'module_payment_gateways_paypal_standard_currency_code',
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
			'settings_key'        => 'module_payment_gateways_paypal_standard_api_username',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_standard_api_password',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_standard_api_signature',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_standard_enable_debug_email',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '9',
			'settings_function'   => 'boolean',
		);


		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_standard_enable_ipn_verification',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '10',
			'settings_function'   => 'boolean',
		);

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_standard_checkout_logo',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '11',
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
				'cmd'         => '_xclick',
				'item_name'   => lang('invoice_number') . ' #' . $data['invoice_number'],
				'amount'      => format_amount($data['total'], FALSE, FALSE, TRUE, TRUE),
				'notify_url'  => ssl_url('checkout/invoice/notification/paypal_standard'),
				'item_number' => is_var($data, 'order_id', FALSE, $data['invoice_id']),
				'custom'      => $data['invoice_id'],
				'invoice'     => is_var($data, 'invoice_id'),
			);
		}
		else
		{
			$row = $this->cart->check_subscription($data['cart']['items']);

			if (!empty($row))
			{
				//generate subscribe button
				$p = unserialize($row['pricing_data']);

				$vars = array(
					'cmd' => '_xclick-subscriptions',
					'item_name'   => lang('order_number') . ' #' . $data['order']['order_number'],
					'notify_url'  => ssl_url('checkout/order/notification/paypal_standard'),
					'item_number' => $data['order']['order_id'],
					'custom'      => $data['order']['order_id'],
					'invoice'     => !empty($data['invoice']) ? is_var($data['invoice'], 'id') : '',

					'a3'  => !empty($p['enable_initial_amount']) ? $data['cart']['totals']['subscription']['amount'] : $data['cart']['totals']['total_with_shipping'],
					'p3'  => $p['interval_amount'],
					't3'  => $this->check_interval_type($p['interval_type']),
					'src' => '1',
				);

				$vars['a3'] = format_amount($vars['a3'], FALSE, FALSE, TRUE, TRUE);
				if (!empty($p['enable_initial_amount']))
				{
					$vars['a1'] = $data['cart']['totals']['total_with_shipping'];
					$vars['a1'] = round($vars['a1'], 2);
					$vars['p1'] = $p['initial_interval'];
					$vars['t1'] = $this->check_interval_type($p['initial_interval_type']);
				}

				if (!empty($p['recurrence']))
				{
					$vars['srt'] = $p['recurrence'];
				}
			}
			else
			{
				$vars = array(
					'cmd'         => '_xclick',
					'item_name'   => lang('order_number') . ' #' . $data['order']['order_number'],
					'amount'      => $data['cart']['totals']['total_with_shipping'],
					'notify_url'  => ssl_url('checkout/order/notification/paypal_standard'),
					'item_number' => $data['order']['order_id'],
					'custom'      => $data['order']['order_id'],
					'invoice'     => is_var($data['invoice'], 'id'),
				);
			}
		}

		$vars['rm'] = '1';
		$vars['no_shipping'] = '1';
		$vars['return'] = site_url('thank_you/page/paypal_standard');
		$vars['business'] = config_item('module_payment_gateways_paypal_standard_paypal_email');
		$vars['currency_code'] = check_currency('module_payment_gateways_paypal_standard_currency_code');
		$vars['image_url'] = config_item('module_payment_gateways_paypal_standard_checkout_logo');

		return $vars;
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		$url = config_enabled('module_payment_gateways_paypal_standard_enable_testing') ? config_item('module_gateway_test_url') : config_item('module_gateway_production_url');

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

	public function ipn_process($data = array(), $module = array())
	{
		$vars = array(
			'module'         => $module['module']['module_folder'],
			'description'    => $module['module']['module_description'],
			'status'         => '1',
			'transaction_id' => $data['txn_id'],
			'amount'         => $data['mc_gross'],
			'fee'            => $data['mc_fee'],
			'currency_code'  => $data['mc_currency'],
			'debug'          => serialize($data),
			'invoice_id'     => is_var($data, 'invoice'),
			'order_id'       => is_var($data, 'item_number'),
			'subscription_id' => is_var($data, 'subscr_id'),
			'subscription' => !empty($data['txn_type']) ? $data['txn_type'] == 'subscr_payment' ? '1' : '0' : '0',
			'card_data'      => array(),
		);

		switch ($data['payment_status'])
		{
			case 'Completed':

				$vars['type'] = 'payment';

				break;

			case 'Refunded':

				$vars['type'] = 'refund';

				break;
		}


		$row = array('success' => TRUE,
		             'data'    => $vars,
		             'post'    => $data,
		);

		$this->ipn_log($row, 'check');

		return $row;
	}

	public function ipn_verify()
	{
		$url = config_item('module_payment_gateways_paypal_standard_enable_testing') ? config_item('module_gateway_test_url') : config_item('module_gateway_production_url');

		// STEP 1: read POST data
		// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
		// Instead, read raw POST data from the input stream.
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$post_data = array();
		foreach ($raw_post_array as $keyval)
		{
			$keyval = explode('=', $keyval);
			if (count($keyval) == 2)
			{
				$post_data[$keyval[0]] = urldecode($keyval[1]);
			}
		}

		// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
		$req = 'cmd=_notify-validate';
		if (function_exists('get_magic_quotes_gpc'))
		{
			$get_magic_quotes_exists = TRUE;
		}
		foreach ($post_data as $key => $value)
		{
			if ($get_magic_quotes_exists == TRUE && get_magic_quotes_gpc() == 1)
			{
				$value = urlencode(stripslashes($value));
			}
			else
			{
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}


		if (config_enabled('module_payment_gateways_paypal_standard_enable_ipn_verification'))
		{
			// Step 2: POST IPN data back to PayPal to validate
			$ch = curl_init($url . '/cgi-bin/webscr');
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
			// In wamp-like environments that do not come bundled with root authority certificates,
			// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set
			// the directory path of the certificate as shown below:
			// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
			if (!($res = curl_exec($ch)))
			{
				// error_log("Got " . curl_error($ch) . " when processing IPN data");
				curl_close($ch);
				exit;
			}
			curl_close($ch);

			// inspect IPN validation result and act accordingly
			if (strcmp($res, "VERIFIED") == 0)
			{
				$row = array('success' => TRUE,
				             'data'    => $post_data,
				);
			}
			else
			{
				if (strcmp($res, "INVALID") == 0)
				{
					$row = array('error' => TRUE,
					);
				}
			}
		}
		else
		{
			$row = array('success' => TRUE,
			             'data'    => $post_data,
			);
		}

		//send a debug email if set...
		if (config_item('module_payment_gateways_paypal_standard_enable_debug_email'))
		{
			send_debug_email($post_data, 'Paypal IPN debug');
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function ipn_log($data = array(), $type = 'check')
	{
		$vars = array('type'         => 'paypal_standard',
		              'reference_id' => $data['post']['txn_id'],
		              'data'         => $data['data']['debug']);

		$this->pay->ipn_log($vars, $type);
	}

	public function process_refund($data = array())
	{
		if (!config_item('module_payment_gateways_paypal_standard_api_username'))
		{
			show_error(lang('invalid_api_credentials'));
		}

		$url = config_enabled('module_payment_gateways_paypal_standard_enable_testing') ? config_item('module_gateway_refund_testing_url') : config_item('module_gateway_refund_production_url');
		$vars = array('USER'          => config_item('module_payment_gateways_paypal_standard_api_username'),
		              'PWD'           => config_item('module_payment_gateways_paypal_standard_api_password'),
		              'SIGNATURE'     => config_item('module_payment_gateways_paypal_standard_api_signature'),
		              'METHOD'        => 'RefundTransaction',
		              'VERSION'       => '94',
		              'AMT'           => $data['refund_amount'],
		              'CURRENCYCODE'  => check_currency('module_payment_gateways_paypal_standard_currency_code'),
		              'TRANSACTIONID' => $data['transaction_id'],    #ID of the transaction for which the refund is made
		              'REFUNDTYPE'    => $data['amount'] > $data['refund_amount'] ? 'Partial' : 'Full');

		$resp = use_curl($url, $vars);

		if (!empty($resp))
		{
			$response = format_curl_response($resp);

			if (!empty($response['ACK']) && $response['ACK'] == 'Success')
			{
				$vars = array('success' => TRUE,
				              'data'    => $data);

				$vars['data']['date'] = get_time(now(), TRUE);
				$vars['data']['refund_amount'] = $response['GROSSREFUNDAMT'];
				$vars['data']['fee'] = $response['FEEREFUNDAMT'];
				$vars['data']['transaction_id'] = $response['REFUNDTRANSACTIONID'];
				$vars['data']['description'] = lang('refund') . ' - paypal_standard';
				$vars['data']['debug_info'] = base64_encode(serialize($response));
			}
			else
			{
				$vars['error'] = TRUE;
				$vars['msg_text'] = is_var($response, 'L_ERRORCODE0') . ' ' . is_var($response, 'L_LONGMESSAGE0');
				$vars['data']['debug_info'] = $response;
			}
		}
		else
		{
			$vars['error'] = TRUE;
			$vars['msg_text'] = lang('could_not_connect_to_gateway');
			$vars['data']['debug_info'] = '';
		}

		return !empty($vars) ? $vars : FALSE;
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

	private function check_interval_type($type = '')
	{
		switch ($type)
		{
			case 'day':

				return 'D';

				break;

			case 'week':

				return 'W';

				break;

			case 'month':

				return 'M';

				break;
		}
	}
}

/* End of file Paypal_standard_model.php */
/* Location: ./application/models/Paypal_standard_model.php */