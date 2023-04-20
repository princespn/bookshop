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
class Coinpayments_model extends Modules_model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{

		$config = array(
			'settings_key'        => 'module_payment_gateways_coinpayments_title',
			'settings_value'      => 'Pay via Bitcoin',
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
			'settings_key'        => 'module_payment_gateways_coinpayments_description',
			'settings_value'      => 'Make your payment via Bitcoin',
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
			'settings_key'        => 'module_payment_gateways_coinpayments_merchant_id',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_coinpayments_ipn_secret',
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
		'settings_key'        => 'module_payment_gateways_coinpayments_enable_debug_email',
		'settings_value'      => '0',
		'settings_module'     => 'payment_gateways',
		'settings_type'       => 'dropdown',
		'settings_group'      => $id,
		'settings_sort_order' => '5',
		'settings_function'   => 'boolean',
	);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_coinpayments_currency',
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
			'settings_key'        => 'module_payment_gateways_coinpayments_restrict_coins',
			'settings_value'      => '0',
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
			'settings_key'        => 'module_payment_gateways_coinpayments_allowed_coins',
			'settings_value'      => 'BTC,LTC,DOGE,ETH,BCH,DASH,ETC,BCN,POT,XVG,ZEC,ZEN,PPC,BLK,CURE,CRW,DCR,GLD,CLUB,BITB,BRK,CLOAK,DGB,EBST,EXP,FLC,GRS,KMD,KRS,LEC,LSK,MUE,NAV,NEO,NMC,NXT,PINK,PIVX,POA,PROC,QTUM,SMART,SNBL,SOXAX,STEEM,STRAT,SYS,TPAY,TRIG,UBQ,UNIT,VTC,WAVES,XCP,XEM,XMR,XSN,XZC',
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
			'settings_key'        => 'module_payment_gateways_coinpayments_checkout_logo',
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
			$n = explode(' ', $data['customer_name']);
			$fname = !empty($n[0]) ? $n[0] : $data['customer_name'];
			$lname = !empty($n[0]) ? str_replace($n[0], '', $data['customer_name']) : '';
			$vars = array(
				'invoice' => is_var($data, 'invoice_id'),
				'item_name' => lang('invoice_number') . ' #' . $data['invoice_id'],
				'amountf' => format_amount($data['total'], FALSE, FALSE, TRUE, TRUE),
				'email' => $data['customer_primary_email'],
				'first_name' => trim($fname),
				'last_name' =>  trim($lname),
				'cancel_url' => site_url('members/invoices/view'),
				'custom'      => $data['order_id'],
				'invoice'     => is_var($data, 'invoice_id'),
			);
		}
		else
		{
			$vars = array(
				'invoice' => is_var($data, 'invoice_id'),
				'item_name' => lang('order_number') . ' #' . $data['order']['order_number'],
				'amountf' => $data['cart']['totals']['total_with_shipping'],
				'email' => $data['customer']['primary_email'],
				'first_name' => $data['customer']['billing_fname'],
				'last_name' => $data['customer']['billing_lname'],
				'cancel_url' => site_url('cart'),
				'custom'      => $data['order']['order_id'],
				'invoice'     => is_var($data['invoice'], 'id'),
			);
		}

		$vars['cmd'] = '_pay_auto';
		$vars['merchant'] = config_item('module_payment_gateways_coinpayments_merchant_id');
		$vars['success_url'] = site_url('thank_you/page/coinpayments');
		$vars['ipn_url'] = ssl_url('checkout/order/notification/coinpayments');
		$vars['reset'] = '1';
		$vars['currency'] = check_currency('module_payment_gateways_coinpayments_currency');
		$vars['allow_currency'] = config_item('module_payment_gateways_coinpayments_restrict_coins');
		$vars['allow_currencies'] = config_item('module_payment_gateways_coinpayments_allowed_coins');

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

	public function ipn_verify()
	{
		if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
			$row = array('error' => TRUE);
		}

		if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
			$row = array('error' => TRUE);
		}

		if ($this->input->post('merchant') != trim(config_item('module_payment_gateways_coinpayments_merchant_id'))) {
			$row = array('error' => TRUE);
		}

		if ($this->input->post('ipn_type') != "button" && $this->input->post('ipn_type') != "simple") {
			$row = array('error' => TRUE);
		}

		$raw_post_data = file_get_contents('php://input');
		if ($raw_post_data === FALSE || empty($raw_post_data)) {
			$row = array('error' => TRUE);
		}

		$hmac = hash_hmac("sha512", $raw_post_data, trim(config_item('module_payment_gateways_coinpayments_ipn_secret')));
		if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
			$row = array('error' => TRUE);
		}

		if (!empty($row['error']))
		{
			$post_data = $row;
		}
		else
		{
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

			$row = array('success' => TRUE,
			             'data'    => $post_data,
			);
		}

		//send a debug email if set...
		if (config_item('module_payment_gateways_coinpayments_enable_debug_email'))
		{
			send_debug_email($post_data, 'Coinpayments IPN debug');
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function ipn_process($data = array(), $module = array())
	{
		$vars = array(
			'module'          => $module['module']['module_folder'],
			'description'     => $module['module']['module_description'],
			'status'          => '1',
			'transaction_id'  => $data['txn_id'],
			'amount'          => $data['amount1'],
			'fee'             => $data['fee'],
			'currency_code'   => $data['currency1'],
			'debug'           => serialize($data),
			'invoice_id'      => is_var($data, 'invoice'),
			'order_id'        => is_var($data, 'custom'),
			'subscription_id' => is_var($data, 'subscr_id'),
			'card_data'       => array(),
		);

		if ($this->input->post('status') >= 100 || $this->input->post('status') == 2) {
			// payment is complete or queued for nightly payout, success
		} else if ($this->input->post('status') < 0) {
			//payment error, this is usually final but payments will sometimes be reopened
			// if there was no exchange rate conversion or with seller consent
		}

		$row = array('success' => TRUE,
		             'data'    => $vars,
		             'post'    => $data,
		);

		$this->ipn_log($row, 'check');

		return $row;
	}

	public function ipn_log($data = array(), $type = 'check')
	{
		$vars = array('type'         => 'coinpayments',
		              'reference_id' => $data['post']['txn_id'],
		              'data'         => $data['data']['debug']);

		$this->pay->ipn_log($vars, $type);
	}
}

/* End of file coinpayments_model.php */
/* Location: ./application/models/coinpayments_model.php */