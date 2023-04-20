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
class Skrill_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_payment_gateways_skrill_title',
			'settings_value'      => 'Pay via Skrill',
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
			'settings_key'        => 'module_payment_gateways_skrill_description',
			'settings_value'      => 'Make your payment via Skrill',
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
			'settings_key'        => 'module_payment_gateways_skrill_enable_testing',
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
			'settings_key'        => 'module_payment_gateways_skrill_email',
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
			'settings_key'        => 'module_payment_gateways_skrill_secret_word',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_skrill_currency',
			'settings_value'      => 'USD',
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
			'settings_key'        => 'module_payment_gateways_skrill_enable_verification',
			'settings_value'      => '1',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
			'settings_function'   => 'boolean',
		);


		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_skrill_enable_debug_email',
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
			'settings_key'        => 'module_payment_gateways_skrill_checkout_logo',
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
				'transaction_id'  => $data['invoice_id'],
				'status_url'      => ssl_url('checkout/invoice/notification/skrill/'),
				'amount'          => $data['total'],
				'merchant_fields' => 'invoice_id',
				'invoice_id'      => $data['invoice_id'],
			);
		}
		else
		{
			$vars = array(
				'transaction_id'  => $data['order']['order_id'],
				'status_url'      => ssl_url('checkout/order/notification/skrill/'),
				'amount'          => $data['cart']['totals']['total_with_shipping'],
				'merchant_fields' => 'order_id',
				'order_id'        => $data['order']['order_id'],
			);
		}

		$vars['pay_to_email'] = config_item('module_payment_gateways_skrill_email');
		$vars['recipient_description'] = config_item('sts_site_name') . ' ' . lang('payment');
		$vars['currency'] = check_currency('module_payment_gateways_skrill_currency');
		$vars['logo_url'] = config_item('module_payment_gateways_skrill_checkout_logo');
		$vars['return_url'] = site_url('thank_you/page/skrill');
		$vars['return_url_text'] = lang('click_here_to_process_your_order');
		$vars['cancel_url'] = site_url();

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
		if ($data['status'] == '2')
		{
			$vars = array(
				'module'         => $module['module']['module_folder'],
				'description'    => $module['module']['module_description'],
				'status'         => '1',
				'transaction_id' => $data['transaction_id'],
				'amount'         => $data['mb_amount'],
				'currency_code'  => check_currency('module_payment_gateways_skrill_currency'),
				'debug'          => serialize($data),
				'card_data'      => array(),
			);

			if ($type == 'invoice')
			{
				$vars['invoice_id'] = is_var($data, 'invoice_id');
			}
			else
			{
				$vars['order_id'] = is_var($data, 'order_id');
			}

			switch ($data['status'])
			{
				case '2':

					$vars['type'] = 'payment';

					break;
			}

			$row = array('success' => TRUE,
			             'data'    => $vars,
			             'post'    => $data,
			);

			$this->ipn_log($row, 'check');
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	public function ipn_verify()
	{
		$row = array('success' => TRUE,
		             'data'    => $this->input->post(),
		);

		if ($this->config->item('module_payment_gateways_skrill_enable_verification') == 1)
		{
			$mb_secret = strtoupper(md5(config_item('module_payment_gateways_skrill_secret_word')));
			$row['data']['compare'] = $_POST['merchant_id'] . $_POST['transaction_id'] . $mb_secret . $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status'];

			if (strtoupper(md5($row['data']['compare'])) != $_POST['md5sig'])
			{
				die('invalid ipn');
			}
		}

		//send a debug email if set...
		if (config_item('module_payment_gateways_skrill_enable_debug_email'))
		{
			send_debug_email($row['data'], 'Skrill IPN debug');
		}

		return empty($row) ? FALSE : sc($row);
	}

	public function ipn_log($data = array())
	{
		$vars = array('type'         => 'skrill',
		              'reference_id' => $data['data']['transaction_id'],
		              'data'         => $data['data']['debug']);

		$this->pay->ipn_log($vars);
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

/* End of file Skrill_model.php */
/* Location: ./application/models/Skrill_model.php */