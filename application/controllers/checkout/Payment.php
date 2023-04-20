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
class Payment extends Checkout_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('checkout');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function options() //load the options available for payment
	{
		$this->init->check_ajax_security();

		//get the cart
		$this->data[ 'cart' ] = sess('cart_details');

		//check if the user is logged in and get his/her details
		$this->data[ 'member' ] = sess('user_logged_in') ? $this->mem->get_details((int)sess('member_id'), TRUE) : is_var($_SESSION, 'checkout_customer_data');

		//if there are, generate fields for shipping sub form
		$this->data[ 'fields' ] = init_sub_forms($this->form->init_form(2, sess('default_lang_id'), $this->data[ 'member' ], TRUE, 'billing'));

		//get available payment gateways
		$rows = $this->mod->get_modules('payment_gateways', TRUE);

		if (!empty($rows))
		{
			//set the default options array
			$this->data[ 'payment_options' ] = array();

			foreach ($rows as $v)
			{
				//initialize the require files for the module
				$this->init_module('payment_gateways', $v[ 'module_folder' ]);

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
				else
				{
					$this->data['payment_options'][] = $v;
				}

				//reset modules
				$this->remove_module('payment_gateways', $v[ 'module_folder' ]);
			}
		}

		$this->show->display('checkout', 'cart_payment_options', $this->data);
	}

	// ------------------------------------------------------------------------

	public function gateway_form() //show the actual payment form if there is one from the module
	{
		$this->init->check_ajax_security();

		//make sure there is a payment option set
		if (sess('checkout_payment_option'))
		{
			$cart = sess('cart_details');

			if (sess('checkout_payment_option') == 'free' && $cart[ 'totals' ][ 'total_with_shipping' ] == '0')
			{
				$this->show->display('checkout', 'cart_no_payment_required', $this->data);
			}
			else
			{
				//show the gateway form for processing the payment (enter credit card details..... )
				if ($row = $this->mod->get_module_details(sess('checkout_payment_option')))
				{
					//set the customer data if needed
					$this->data[ 'customer' ] = sess('checkout_customer_data');

					//generate payment option, captcha, tos and order notes
					$this->init_module('payment_gateways', $row[ 'module' ][ 'module_folder' ]);

					//set model and function alias for calling methods
					$module = $this->config->item('module_alias');

					if (method_exists($this->$module, 'generate_gateway_form'))
					{
						$this->data['form_data'] = $this->$module->generate_gateway_form($this->data['customer'], $cart['totals']['total_with_shipping'], 'checkout', $cart);
					}

					if (sess('user_logged_in'))
					{
						//get saved customer token if module allows
						if (method_exists($this->$module, 'get_customer_token'))
						{
							$this->data['customer_token'] = $this->$module->get_customer_token(sess('member_id'));
						}
					}

					//set the submit URL
					$this->data['submit_url'] = !config_item('module_submit_gateway_form_url') ? site_url('checkout/payment/pay') : config_item('module_submit_gateway_form_url');

					//set the path for the template to use...
					$path = APPPATH . 'modules/payment_gateways/' . $row[ 'module' ][ 'module_folder' ] . '/views';

					$this->show->display('checkout', $this->config->item('module_gateway_form'), $this->data, FALSE, $path);
				}
				else
				{
					is_ajax(TRUE);
				}
			}
		}
		else
		{
			is_ajax(TRUE);
		}
	}

	// ------------------------------------------------------------------------

	public function select_payment() //form for submitting billing and payment info
	{
		$this->init->check_ajax_security();

		if (!$this->input->post('select_payment'))
		{
			$response = array( 'type' => 'error',
			                   'msg'  => lang('please_select_payment_option')
			);
		}
		else
		{
			//validate the billing info
			$row = $this->checkout->validate_billing($this->input->post(NULL, TRUE));

			if (!empty($row[ 'error' ]))
			{
				$response = array( 'type'         => 'error',
				                   'error_fields' => $row[ 'error_fields' ],
				                   'msg'          => $row[ 'error' ]
				);
			}
			else
			{
				//save the customer data
				$this->session->set_userdata('checkout_customer_data', $row[ 'customer_data' ]);

				//set payment option
				$this->session->set_userdata('checkout_payment_option', $this->input->post('select_payment', TRUE));

				$response = array( 'type'      => 'success',
				                   'module_id' => $this->input->post('select_payment', TRUE)
				);

				//update cart
				$this->cart->update_session_cart();

				//send updated totals via ajax
				$response['cart_totals'] = update_cart_totals(sess('cart_details'));
			}
		}

		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function pay() //payment confirmation box - process the actual payment data entered (like credit card number..)
	{
		if (uri(4))
		{
			$row =  $this->mod->get_module_details(uri(4), TRUE,  '', $col = 'module_folder');
		}
		else
		{
			$row = $this->mod->get_module_details(sess('checkout_payment_option'));
		}

		if (!$row)
		{
			ajax_response(array( 'type' => 'error',
			                     'msg'  => lang('invalid_module')
			));
		}

		//generate payment option, captcha, tos and order notes
		$this->init_module('payment_gateways', $row[ 'module' ][ 'module_folder' ]);

		//set model and function alias for calling methods
		$module = $this->config->item('module_alias');

		//save the module in a session for use later
		$this->session->set_userdata('checkout_payment_module', $row[ 'module' ][ 'module_folder' ]);

		//save any order notes if any
		$this->session->set_userdata('checkout_order_notes', $this->input->post('order_notes', TRUE));

		//check if we are redirecting to a third party site for payment or generate the payment here
		if ($this->config->item('module_redirect_type') == 'onsite') //offsite or offline
		{
			//run the payment module now
			$func = $this->config->item('module_one_off_pay_function'); //generate_payment()

			//run only if the method is available
			if (method_exists($this->$module, $func))
			{
				$order = sess('checkout_customer_data');
				$order['cart'] = $this->cart->get_cart();

				//generate_payment
				$payment = $this->$module->$func($order, $row);

				//we got paid!
				if ($payment[ 'type' ] == 'success')
				{
					//save the transaction data and redirect
					$this->session->set_userdata('transaction_data', $payment);
				}
			}

			//redirect to the right page
			checkout_process_order($payment);

		}
		else
		{
			//redirect to order processing for external gateways and offline payments
			redirect_page(ssl_url('checkout/order/process'));
		}
	}

	// ------------------------------------------------------------------------

	public function free()
	{
		//check for free payment
		if (sess('checkout_payment_option') == 'free')
		{
			//save the module in a session for use later
			$this->session->set_userdata('checkout_payment_module', 'free');

			//save any order notes if any
			$this->session->set_userdata('checkout_order_notes', $this->input->post('order_notes', TRUE));

			redirect_page(ssl_url('checkout/order/process'));
		}

		show_error((lang('invalid_free_option')));
	}

	// ------------------------------------------------------------------------

	public function redirect()
	{
		$this->output->set_header('HTTP/1.0 200 OK');

		$this->data['module'] = valid_id(uri(4, TRUE));

		$type = uri(5) == 'invoice' ? 'invoice' : 'payment';

		//set the data to a session variable
		$this->data['fields'] = $this->input->post() ? $this->input->post() : $this->input->get();

		$this->data['submit_url'] = site_url('checkout/' . $type . '/pay/' . $this->data['module']);

		$this->show->display('checkout', 'redirect', $this->data);
	}
}

/* End of file Payment.php */
/* Location: ./application/controllers/checkout/Payment.php */
