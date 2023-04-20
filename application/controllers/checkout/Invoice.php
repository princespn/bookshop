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
class Invoice extends Checkout_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('checkout');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		redirect_page('members/invoices/view');
	}

	// ------------------------------------------------------------------------

	public function payment()
	{
		$this->sec->check_login_session('member');

		$this->data['id'] = valid_id(uri(4));

		//set the invoice id
		$this->session->set_userdata('invoice_payment_id', $this->data['id']);

		$this->data['p'] = $this->invoices->get_details($this->data['id']);

		//get the invoice details
		if (!$this->data['p'])
		{
			log_error('error', lang('no_record_found'));
		}
		elseif ($this->data['p']['payment_status_id'] == '2')
		{
			log_error('error', lang('invoice_marked_as_paid'));
		}

		//check for ownership
		if (!$this->sec->verify_ownership($this->data['p']['member_id']))
		{
			log_error('error', lang('invalid_id'));
		}

		//get available payment gateways
		$rows = $this->mod->get_modules('payment_gateways', TRUE);

		if (!empty($rows))
		{
			//set the checkout js and css if any...
			$checkout_js = array();
			$checkout_css = array();

			//set the default options array
			$this->data['payment_options'] = array();

			foreach ($rows as $k => $v)
			{
				//initialize the require files for the module
				$this->init_module('payment_gateways', $v['module_folder']);

				//set model and function alias for calling methods
				$module = $this->config->item('module_alias');

				//run only if the method is available
				if (method_exists($this->$module, 'filter_payment_zones'))
				{
					if ($this->$module->filter_payment_zones(sess('checkout_customer_data')))
					{
						$this->data['payment_options'][] = $v;
					}
				}
				elseif ($this->config->item('module_redirect_type') == 'offline')
				{
					//continue;
				}
				else
				{
					$this->data['payment_options'][] = $v;
				}

				//check if there are any header files that need to be included for the checkout page
				if ($this->config->item('module_checkout_header_script'))
				{
					foreach ($this->config->item('module_checkout_header_script') as $k => $c)
					{
						if ($k == 'js')
						{
							foreach ($c as $j)
							{
								array_push($checkout_js, $j);
							}
						}
						elseif ($k == 'css')
						{
							foreach ($c as $j)
							{
								array_push($checkout_css, $j);
							}
						}
					}
				}

				$this->data['checkout_js'] = array_unique($checkout_js);
				$this->data['checkout_css'] = array_unique($checkout_css);

				//reset modules
				$this->remove_module('payment_gateways', $v['module_folder']);
			}
		}

		$this->show->display('checkout', 'pay_invoice', $this->data);

	}

	// ------------------------------------------------------------------------

	public function select_payment() //select the payment to use
	{
		$this->sec->check_login_session('member');

		if (!$this->input->post('select_payment'))
		{
			$response = array('type' => 'error',
			                  'msg'  => lang('please_select_payment_option'),
			);
		}
		else
		{
			$response = array('type'      => 'success',
			                  'module_id' => $this->input->post('select_payment', TRUE),
			);
		}

		//set payment option
		$this->session->set_userdata('checkout_payment_option', $this->input->post('select_payment', TRUE));

		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function gateway_form() //show the actual payment form if there is one from the module
	{
		$this->init->check_ajax_security();

		$this->sec->check_login_session('member');

		$id = valid_id(uri(4));

		//make sure there is a payment option set
		if ($id)
		{
			//show the gateway form for processing the payment (enter credit card details..... )
			if ($row = $this->mod->get_module_details($id))
			{
				//generate payment option, captcha, tos and order notes
				$this->init_module('payment_gateways', $row['module']['module_folder']);

				//set model and function alias for calling methods
				$module = $this->config->item('module_alias');

				//get invoice details
				$this->data['invoice'] = $this->invoices->get_details(sess('invoice_payment_id'));

				if (method_exists($this->$module, 'generate_gateway_form'))
				{
					$this->data['form_data'] = $this->$module->generate_gateway_form($this->data['invoice'], $this->data['invoice']['total'], 'invoice');
				}

				//get saved customer token if module allows
				if (method_exists($this->$module, 'get_customer_token'))
				{
					$this->data['customer_token'] = $this->$module->get_customer_token(sess('member_id'));
				}

				//set the submit URL
				$this->data['submit_url'] = !$this->config->item('module_submit_gateway_form_url') ? site_url('checkout/invoice/pay/' . $row['module']['module_folder']) : $this->config->item('module_submit_gateway_form_url');

				//set the path for the template to use...
				$path = APPPATH . 'modules/payment_gateways/' . $row['module']['module_folder'] . '/views';

				$this->show->display('checkout', $this->config->item('module_gateway_form'), $this->data, FALSE, $path);
			}
			else
			{
				is_ajax(TRUE);
			}
		}
		else
		{
			is_ajax(TRUE);
		}
	}

	// ------------------------------------------------------------------------

	public function pay() //run the payment module to process payment
	{
		$this->sec->check_login_session('member');

		$id = valid_id(uri(4, TRUE));

		if ($id)
		{
			$row = $this->mod->get_module_details($id, FALSE, 'payment_gateways', 'module_folder');
		}

		if (!$row)
		{
			ajax_response(array('type' => 'error',
			                    'msg'  => lang('invalid_module'),
			));
		}

		//generate payment option, captcha, tos and order notes
		$this->init_module('payment_gateways', $row['module']['module_folder']);

		//set model and function alias for calling methods
		$module = $this->config->item('module_alias');

		//save the module in a session for use later
		$this->session->set_userdata('checkout_payment_module', $row['module']['module_folder']);

		//save any order notes if any
		$this->session->set_userdata('checkout_order_notes', $this->input->post('order_notes', TRUE));

		//get invoice details
		$this->data['invoice'] = $this->invoices->get_details(sess('invoice_payment_id'));

		//check if we are redirecting to a third party site for payment or generate the payment here
		if ($this->config->item('module_redirect_type') == 'onsite') //offsite or offline
		{
			//run the payment module now
			$func = $this->config->item('module_one_off_pay_function'); //generate_payment()

			//run only if the method is available
			if (method_exists($this->$module, $func))
			{
				//generate_payment
				$payment = $this->$module->$func($this->data['invoice'], $row, 'invoice');//generate_payment()

				//we got paid!
				if ($payment['type'] == 'success')
				{
					//save the transaction data and redirect
					$this->session->set_userdata('transaction_data', $payment);
				}
			}

			//redirect to the right page
			checkout_process_order($payment, 'checkout/invoice/process/' . $id);
		}
		else
		{
			//redirect to order processing for external gateways and offline payments
			redirect_page(ssl_url('checkout/invoice/process/' . $id));
		}

	}

	// ------------------------------------------------------------------------

	public function process()
	{
		$this->sec->check_login_session('member');

		$id = valid_id(uri(4, TRUE));

		//check for cart info
		if ($id)
		{
			//initialize payment module first
			if (!$row = $this->mod->get_module_details($id, FALSE, 'payment_gateways', 'module_folder'))
			{
				log_error(lang('invalid_module'));
			}

			$this->init_module('payment_gateways', $row['module']['module_folder']);

			//get invoice details
			$invoice = $this->invoices->get_details(sess('invoice_payment_id'), FALSE, TRUE);

			//initialize the order data and add the order now...
			$this->data['order_data'] = array(
				'invoice'     => $invoice,
				'customer'    => $this->mem->get_details($invoice['member_id']),
				'affiliate'   => check_order_affiliate($invoice['affiliate_id']),
				'order_notes' => sess('checkout_order_notes'),
				'language'    => sess('default_lang_id'),
				'currency'    => $this->config->item('currency'),
				'user_agent'  => browser_info(),
			);

			if (!empty($invoice['order_id']))
			{
				$this->data['order_data']['order'] = $this->orders->get_details($invoice['order_id'], FALSE, TRUE);
			}

			//add payment and complete the order
			if (sess('transaction_data'))
			{
				//set the order data onto a new thank you page session
				$this->data['order_data']['transaction'] = sess('transaction_data');

				$this->data['order_data'] = $this->pay_invoice();

				//delete payment session for transaction so it's not used again
				$this->session->unset_userdata('transaction_data');
			}

			$this->session->set_userdata('post_payment_data', $this->data['order_data']);

			if (config_option('module_redirect_type') == 'offline') //bank transfer / check payments...
			{
				redirect_page('thank_you');
			}
			elseif (config_option('module_redirect_type') == 'offsite')
			{
				//send the user to the offsite payment gateway
				$module = $this->config->item('module_alias');
				$func = $this->config->item('module_one_off_pay_function');

				//run only if the method is available
				if (method_exists($this->$module, $func)) //generate_payment link
				{
					$this->data['redirect_link'] = $this->$module->$func($this->data['order_data']['invoice'], $row, 'invoice');
				}

				$path = APPPATH . 'modules/payment_gateways/' . $row['module']['module_folder'] . '/views';

				$this->show->display('checkout', $id . '_process', $this->data, FALSE, $path);
			}
			else
			{
				$this->show->display('checkout', 'process', $this->data);
			}
		}
		else
		{
			redirect(checkout_offsite_url());
		}
	}

	//for ipn notifications
	public function notification()
	{
		//set the module ID
		$id = uri(4);

		//load the correct payment module
		if ($row = $this->mod->get_module_details($id, TRUE, 'payment_gateways', 'module_folder'))
		{
			//generate payment option, captcha, tos and order notes
			$this->init_module('payment_gateways', $id);

			//run the payment module now
			$module = $this->config->item('module_alias');
			$func = $this->config->item('module_ipn_pay_function');

			//run only if the method is available
			if (method_exists($this->$module, $func)) //ipn_process
			{
				//ipn verification
				$ipn_data = $this->$module->ipn_verify();

				if (!empty($ipn_data['success']))
				{
					//create the payment data
					$payment = $this->$module->$func($ipn_data['data'], $row); //ipn_process

					//payment data is set
					if (!empty($payment['success']))
					{
						switch ($payment['data']['type'])
						{
							case 'payment':

								//get invoice details
								$invoice = $this->invoices->get_details($payment['data']['invoice_id'], FALSE, TRUE);

								//initialize the order data and add the order now...
								$this->data['order_data'] = array(
									'invoice'     => $invoice,
									'customer'    => $this->mem->get_details($invoice['member_id']),
									'affiliate'   => check_order_affiliate($invoice['affiliate_id']),
									'order_notes' => sess('checkout_order_notes'),
									'language'    => sess('default_lang_id'),
									'currency'    => $this->config->item('currency'),
									'user_agent'  => browser_info(),
								);

								if (!empty($invoice['order_id']))
								{
									$this->data['order_data']['order'] = $this->orders->get_details($invoice['order_id'], FALSE, TRUE);
								}

								if ($this->data['order_data'])
								{
									//set the payment transaction
									$this->data['order_data']['transaction'] = $payment['data'];

									if (!empty($payment['data']['invoice_id']))
									{
										if ($invoice = $this->invoices->get_details($payment['data']['invoice_id'], TRUE, FALSE))
										{
											$this->data['order_data']['invoice']['id'] = $payment['data']['invoice_id'];
											$this->data['order_data']['invoice']['data'] = $invoice;
										}
									}

									//create the invoice if no invoice is set for the order
									if (empty($invoice))
									{
										$this->data['order_data']['invoice'] = $this->invoices->create_invoice($this->data['order_data']);
									}

									//complete the order
									$this->pay_invoice();
								}

								break;

							case 'refund':
								//todo
								break;
						}

						//log the ipn payment
						$this->$module->ipn_log($payment);
					}
				}
			}
		}

		$this->output->set_header('HTTP/1.0 200 OK');
	}

	/**
	 * Pay Invoice
	 *
	 * Process the payment for the invoice
	 *
	 * @return mixed
	 */
	protected function pay_invoice()
	{
		//set the order_data array
		$p = $this->data['order_data'];

		//if THERE IS A PAYMENT SESSION ADD IT
		//add the payment only if there is a transaction
		if (!empty($p['transaction']))
		{
			$p['payment'] = $this->invoices->create_payment($p, $p['invoice']['invoice_id']);
			$p['msg_text'] = lang('invoice_paid_successfully');

			//mark orders as paid
			if (!empty($p['payment']['paid']) && !empty($p['invoice']['order_id']))
			{
				$this->orders->mark_paid($p['invoice']['order_id']);
			}

			//COMMISSIONS
			//generate commission for all downline members if any
			if (config_enabled('affiliate_marketing') && !empty($p['affiliate']))
			{
				$p['commissions'] = $this->comm->generate_commissions($p, 'invoice');
			}

			//get admin users
			$admins = get_admins();

			//INVENTORY
			foreach ($p['invoice']['items'] as $v)
			{
				$this->prod->update_product_inventories($v, $admins);

				//subscriptions, downloads and gift certficates are not automatically created when invoice is paid manually
			}
		}

		//to all admins who have the alert enabled
		$this->mail->send_admin_order_emails($p, 'invoice');

		//update member to custom
		if (!empty($p['invoice']['member_id']))
		{
			$this->checkout->update_to_customer($p['invoice']['member_id']);
		}

		//run modules
		$this->done(__METHOD__, $p);

		return $p;
	}
}

/* End of file Invoice.php */
/* Location: ./application/controllers/checkout/Invoice.php */
