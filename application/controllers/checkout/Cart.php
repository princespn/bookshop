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
class Cart extends Checkout_Controller
{
	protected $data;

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('cart');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		checkout_referral_id(TRUE);

		check_minimum_purchase($this->data['cart']['totals']['total'], TRUE);

		//check if the user is logged in and get his/her details
		$this->data['member'] = sess('user_logged_in') ? $this->mem->get_details((int)sess('member_id'), TRUE) : is_var($_SESSION, 'checkout_customer_data');

		//if there are, generate fields for shipping sub form
		$this->data['fields'] = init_sub_forms($this->form->init_form(2, sess('default_lang_id'), $this->data['member'], TRUE));

		//set breadcrumbs
		$this->data['breadcrumb'] = set_breadcrumb(array(lang('checkout') => 'checkout'));

		//set the payment step if needed
		if ($this->input->get('step') == 'payment' && sess('checkout_payment_option'))
		{
			$this->data['step'] = 'payment';
			$this->data['shipping_option'] = sess('checkout_shipping_selected', 'shipping_description');
			$this->data['cart']['totals'] = update_cart_totals($this->cart->get_cart());
		}

		//get available payment gateways
		$rows = $this->mod->get_modules('payment_gateways', TRUE);

		if (!empty($rows))
		{
			//set the checkout js and css if any...
			$checkout_js = array();
			$checkout_css = array();

			foreach ($rows as $v)
			{
				//initialize the require files for the module
				$this->init_module('payment_gateways', $v['module_folder']);

				//check if there are any header files that need to be included for the checkout page
				if (config_item('module_checkout_header_script'))
				{
					foreach (config_item('module_checkout_header_script') as $k => $c)
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

		//now check if there are items to be shipped
		$tpl = check_shipping($this->data['cart']['items']) ? 'cart_registration_shipping' : 'cart_registration';

		$this->show->display('checkout', $tpl, $this->data);
	}

	// ------------------------------------------------------------------------

	public function account_details()
	{
		//return if not posted
		if (!$this->input->post())
		{
			show_error(lang('invalid_link'));
		}

		//check if the user is logged in
		$row = $this->checkout->validate_account($this->input->post());

		//check if there are errors on validation
		if (!empty($row['error']))
		{
			$response = array('type'         => 'error',
			                  'error_fields' => $row['error_fields'],
			                  'msg'          => $row['error'],
			);
		}
		else
		{
			$response = array('type' => 'success');

			//check if we have a sponsor set
			$row['customer_data']['sponsor'] = checkout_referral_id();

			//save the customer data
			$this->session->set_userdata('checkout_customer_data', $row['customer_data']);
		}

		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function shipping_options()
	{
		//check if we need to generate shipping rates
		$this->session->unset_userdata('checkout_shipping_options');

		if (sess('cart_charge_shipping'))
		{
			//get shipping modules
			$rows = $this->ship->get_shipping_modules();

			//get the cart
			$cart = $this->cart->get_cart();

			if (!empty($rows))
			{
				//set the default options array
				$shipping_options = array();

				$i = 1;

				foreach ($rows as $v)
				{
					//initialize the require files for the module
					$this->init_module('shipping', $v['module_folder']);

					//set model and function alias for calling methods
					$module = $this->config->item('module_alias');
					$func = $this->config->item('module_generate_function'); //generate_rates()

					//run only if the method is available
					if (method_exists($this->$module, $func))
					{
						$rate = $this->$module->$func(sess('checkout_customer_data'), $cart);

						if (!empty($rate))
						{
							foreach ($rate as $r)
							{
								$r['sid'] = $i; //for shipping id..
								$shipping_options[$i] = $r;
								$i++;
							}
						}
					}
					//reset modules
					$this->remove_module('shipping', $v['module_folder']);
				}

				//set the shipping options
				$this->data['shipping_options'] = checkout_free_shipping($cart, $shipping_options);

				$this->session->set_userdata('checkout_shipping_options', $this->data['shipping_options']);
			}

			$this->show->display('checkout', 'cart_shipping_options', $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function select_shipping()
	{
		//default response
		$response = array('type' => 'success',
		                  'msg'  => '',
		);

		//reset shipping data first...
		$this->ship->init_shipping(TRUE);

		if (sess('cart_charge_shipping'))
		{
			if ($this->input->post('select_shipping'))
			{
				//save the selected shipping option
				$this->session->set_userdata('checkout_shipping_selected', sess('checkout_shipping_options', $this->input->post('select_shipping')));

				//add shipping amounts to cart_totals
				$this->ship->init_shipping();
			}
			else
			{
				//oh!  no shipping option was selected...
				$response = array('type' => 'error',
				                  'msg'  => lang('please_select_a_shipping_option'),
				);
			}
		}

		//no errors, lets go!
		if ($response['type'] == 'success')
		{
			//update cart
			$this->cart->update_session_cart();

			//send updated totals via ajax
			$response['cart_totals'] = update_cart_totals(sess('cart_details'));
			$response['cart_totals']['shipping_info'] = sess('checkout_shipping_selected', 'shipping_description');

			if (!empty($_SESSION['cart_details']['totals']['subscription']['text']))
			{
				$response['cart_totals']['subscription_text'] = $_SESSION['cart_details']['totals']['subscription']['text'];
			}
		}

		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function remove_certificate()
	{
		//remove certificate from cart
		$cart = $this->cart->get_cart();

		if ($row = $this->gift->remove_certificate($cart))
		{
			//unset session certificate
			$this->session->unset_userdata('checkout_gift_certificate_data');

			//show ajax errors
			$response = array('type'     => 'success',
			                  'msg'      => $row['msg_text'],
			                  'redirect' => !$this->agent->referrer() ? site_url('cart') : $this->agent->referrer(),
			);

			//send updated totals via ajax
			$this->cart->update_session_cart();

			$response['cart_totals'] = update_cart_totals(sess('cart_details'));
			$response['cart_totals']['cert_amount'] = '';
			$response['cart_totals']['cert_code'] = '';
		}

		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function apply_certificate()
	{
		//show ajax errors
		$response = array('type' => 'error',
		                  'msg'  => lang('invalid_certificate'),
		);

		if (config_enabled('sts_order_enable_gift_certificates'))
		{
			if ($this->input->get('certificate') && sess('cart_details'))
			{

				//check if there is already a cart
				$cart = $this->cart->get_cart();

				$row = $this->gift->validate_certificate($this->input->get('certificate', TRUE), $cart);

				//the certificate is valid...
				if (!empty($row['success']))
				{
					//apply certificate to cart
					if ($row = $this->gift->apply_certificate($row))
					{
						//set session certificate
						$this->session->set_userdata('checkout_gift_certificate_data', $row['certificate']);

						//show ajax errors
						$response = array('type'     => 'success',
						                  'msg'      => $row['msg_text'],
						                  'redirect' => !$this->agent->referrer() ? site_url('cart') : $this->agent->referrer(),
						);

						//send updated totals via ajax
						$this->cart->update_session_cart();

						$response['cart_totals'] = check_cert_value($row['certificate']);

					}
				}
				else
				{
					//show ajax errors
					$response['msg'] = $row['msg_text'];
				}
			}
		}

		//send the response via ajax
		ajax_response($response);
	}
}

/* End of file Cart.php */
/* Location: ./application/controllers/checkout/Cart.php */