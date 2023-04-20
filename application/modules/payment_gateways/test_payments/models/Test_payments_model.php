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

class Test_payments_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
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

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		//set the totals
		$payment_data = $this->format_card_data($data, $type);

		$vars = array(
			'type'           => 'success',
			'module'         => $module['module']['module_folder'],
			'description'    => $module['module']['module_description'],
			'status'         => 1,
			'transaction_id' => 'TEST-' . strtoupper(random_string('alnum', 12)),
			'amount'         => $payment_data['total'],
			'fee'            => '0.00',
			'currency_code'  => $this->config->item('code', 'currency'),
			'debug'          => '',
			'card_data'      => array(),
		);

		return $vars;
	}

	public function format_card_data($data = array(), $type = 'checkout')
	{
		switch ($type)
		{
			case 'invoice':
			case 'cron':

				$vars = array(
					'total' => $data['total'],
					'name'  => $data['customer_name'],

				);

				break;

			default:

				$vars = array(
					'total' => $data['cart']['totals']['total_with_shipping'],
					'name'  => $data['billing_fname'] . ' ' . $data['billing_lname'],
				);

				break;
		}

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
		return array(
			'success' => TRUE,
			'data' => $data);
	}
}

/* End of file Test_payments_model.php */
/* Location: ./application/models/Test_payments_model.php */