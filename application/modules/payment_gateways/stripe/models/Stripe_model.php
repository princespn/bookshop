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
class Stripe_model extends Modules_model
{
	protected $table = 'module_payment_gateway_stripe_members';

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
			'settings_key'        => 'module_payment_gateways_stripe_title',
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
			'settings_key'        => 'module_payment_gateways_stripe_description',
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
			'settings_key'        => 'module_payment_gateways_stripe_api_live_key',
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
			'settings_key'        => 'module_payment_gateways_stripe_api_publishable_key',
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
			'settings_key'        => 'module_payment_gateways_stripe_currency',
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
			'settings_key'        => 'module_payment_gateways_stripe_enable_testing',
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
			'settings_key'        => 'module_payment_gateways_stripe_api_test_key',
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
			'settings_key'        => 'module_payment_gateways_stripe_api_test_publishable_key',
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
			'settings_key'        => 'module_payment_gateways_stripe_save_customer_token',
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
			'settings_key'        => 'module_payment_gateways_stripe_checkout_logo',
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
					'total' => round($data['total'], 2) * 100,
					'name'  => $data['customer_name'],
					'email' => $data['customer_primary_email'],

				);

				break;

			case 'refund':

				$vars = array(
					'total' => round($data['refund_amount'], 2) * 100,
				);

				break;

			default:

				$vars = array(
					'total' => round($data['cart']['totals']['total_with_shipping'], 2) * 100,
					'name'  => $data['billing_fname'] . ' ' . $data['billing_lname'],
					'email' => $data['primary_email'],
				);

				break;
		}

		return $vars;
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		//check if we are testing or not...
		$key = config_enabled('module_payment_gateways_stripe_enable_testing') ? 'test' : 'live';

		\Stripe\Stripe::setApiKey($this->config->item('module_payment_gateways_stripe_api_' . $key . '_key'));

		//set the totals
		$payment_data = $this->format_card_data($data, $type);

		//lets charge the card number given
		if ($this->input->post('stripeToken'))
		{
			try
			{
				$customer = \Stripe\Customer::create(array(
					"description" => $payment_data['name'],
					"email" => $payment_data['email'],
					"source"      => $this->input->post('stripeToken'),
				));
			} catch (Exception $e)
			{
				//log_error('error', $e->getMessage());

				return array('type'       => 'error',
				             'msg_text'   => $e->getMessage(),
				             'debug_info' => '',
				);
			}

			//cool! we created a new customer in stripe
			$vars = array('type'           => 'success',
			              'customer_token' => $customer->id,
			              'card_token'     => $customer->default_card,
			);
		}
		else
		{
			//check if the user is logged in and has a token on file
			if (!empty($data['member_id']))
			{
				if ($row = $this->get_customer_token($data['member_id']))
				{
					//got token...
					$vars = array('type'           => 'success',
					              'customer_token' => $row['customer_token'],
					              'card_token' => $row['card_token'],
					);
				}
			}
		}

		//we created a customer successfully, not we can charge his card
		if (!empty($vars['type']) && $vars['type'] == 'success')
		{
			try
			{
				$charge = \Stripe\Charge::create(array(
					"amount"      => $payment_data['total'],
					"currency"    => check_currency('module_payment_gateways_stripe_currency'),
					'customer'    => $vars['customer_token'],
					"source"      => is_var($vars, 'card_token'), // obtained with Stripe.js
					"description" => $payment_data['name'],
				));
			} catch (Exception $e)
			{
				//log_error('error', $e->getMessage());

				return array('type'     => 'error',
				             'msg_text' => $e->getMessage(),
				);
			}

			//charging the card was successful!
			if ($charge)
			{
				$vars = array(
					'type'           => 'success',
					'msg_text'       => 'payment_generated_successfully',
					'module'         => $module['module']['module_folder'],
					'description'    => $module['module']['module_description'],
					'status'         => '1',
					'transaction_id' => $charge->id,
					'amount'         => show_percent($charge->amount),
					'fee'            => show_percent($charge->fee),
					'currency_code'  => check_currency('module_payment_gateways_stripe_currency'),
					'customer_token' => $vars['customer_token'],
					'debug'          => serialize($charge),
					'card_data'      => array(
						'customer_token' => is_var($vars, 'customer_token'),
						'card_token'     => is_var($vars,'card_token'),
						'cc_type'        => $charge->card['brand'],
						'cc_four'        => $charge->card['last4'],
						'cc_month'       => $charge->card['exp_month'],
						'cc_year'        => $charge->card['exp_year'],
					),
				);

				if (sess('member_id'))
				{
					$this->add_customer_token(sess('member_id'), $vars['card_data']);
				}

				return $vars;
			}
			else //error
			{
				return array('type'     => 'error',
				             'msg_text' => $charge->getMessage(),
				);
			}
		}

		return array('type'     => 'error',
		             'msg_text' => lang('invalid_gateway_access'),
		);
	}

	public function add_customer_token($id = '', $data = array())
	{
		if (config_enabled('module_payment_gateways_stripe_save_customer_token'))
		{
			if (!empty($data['card_token']) && !empty($data['customer_token']))
			{
				$row = $this->dbv->get_record('module_payment_gateway_stripe_members', 'member_id', $id);

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

	public function init_form($type = '')
	{
		switch ($type)
		{
			case 'update_cc':

				$key = config_enabled('module_payment_gateways_stripe_enable_testing') ? 'test_' : '';

				$form = '<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
				                data-key="' . $this->config->item('module_payment_gateways_stripe_api_' . $key . 'publishable_key') . '"
				                data-name="' . config_option('sts_site_name') . '"
				                data-panel-label="' . lang('update_card_details') . '"
				                data-label="' . lang('update_card_details') . '"
				                data-allow-remember-me=false
				                data-email="' . sess('primary_email') . '"
				                data-locale="auto">
				        </script>';

				break;
		}

		return $form;
	}

	public function process_refund($data = array())
	{
		//set the totals
		$payment_data = $this->format_card_data($data, 'refund');

		//check if we are testing or not...
		$key = config_enabled('module_payment_gateways_stripe_enable_testing') ? 'test' : 'live';

		\Stripe\Stripe::setApiKey($this->config->item('module_payment_gateways_stripe_api_' . $key . '_key'));

		try
		{
			$refund = \Stripe\Refund::create(array(
				"charge" => $data['transaction_id'],
				'amount' =>  $payment_data['total']
			));

		} catch (Exception $e)
		{
			$body = $e->getJsonBody();

			return array('type'     => 'error',
			             'msg_text' => $e->getMessage(),
			);
		}

		if ($refund->status == 'succeeded')
		{

			$vars = array('success' => TRUE,
			              'data'    => $data);

			$vars['data']['date'] = get_time(now(), TRUE);
			$vars['data']['refund_amount'] = $refund->amount / 100;
			$vars['data']['fee'] = '0';
			$vars['data']['transaction_id'] = $refund->id;
			$vars['data']['description'] = lang('refund');
			$vars['data']['debug_info'] = base64_encode(serialize($refund));
		}
		else
		{
			$vars['error'] = TRUE;
			$vars['msg_text'] = lang('could_not_connect_to_gateway');
			$vars['data']['debug_info'] = '';
		}

		return !empty($vars) ? $vars : FALSE;

	}

	public function update_card($card = '', $member_id = '', $data = array())
	{
		//check if we are testing or not...
		$key = config_enabled('module_payment_gateways_stripe_enable_testing') ? 'test' : 'live';

		\Stripe\Stripe::setApiKey($this->config->item('module_payment_gateways_stripe_api_' . $key . '_key'));

		if (!empty($data['stripeToken']))
		{
			$vars = array('token'             => $data['stripeToken'],
			              'cardReference'     => $card['card_token'],
			              'customerReference' => $card['customer_token'],
			              'stripeEmail'       => $data['stripeEmail'],
			);

			try
			{
				$customer = \Stripe\Customer::retrieve($card['customer_token']); // stored in your application
				$customer->source = $data['stripeToken']; // obtained with Checkout
				$customer->save();

			} catch (\Stripe\Error\Card $e)
			{

				// Use the variable $error to save any errors
				// To be displayed to the customer later in the page
				$body = $e->getJsonBody();

				return array('type'     => 'error',
				             'msg_text' => $body['error'],
				);
			}

			if ($customer)
			{
				//update customer data
				$vars = array(
					'card_token' => $customer->active_card->id,
					'cc_type'    => $customer->active_card->brand,
					'cc_four'    => $customer->active_card->last4,
					'cc_month'   => $customer->active_card->exp_month,
					'cc_year'    => $customer->active_card->exp_year,
				);

				if (!$this->db->where('member_id', $member_id)->update($this->table, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				$row = array('type'           => 'success',
				             'customer_token' => $card['customer_token'],
				             'debug_info'     => serialize($customer),
				             'msg_text'       => lang('system_updated_successfully'),
				);
			}
			else
			{
				$row = array('type'     => 'error',
				             'msg_text' => lang('gateway_processing_error'),
				);
			}
		}

		return !empty($row) ? $row : FALSE;
	}
}

/* End of file Stripe_model.php */
/* Location: ./application/models/Stripe_model.php */