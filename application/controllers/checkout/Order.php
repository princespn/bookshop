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
class Order extends Checkout_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('order');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function process()
	{
		//check for cart info
		if (sess('cart_details'))
		{
			if (sess('checkout_payment_module') != 'free')
			{
				//initialize payment module first
				if ($row = $this->mod->get_module_details(sess('checkout_payment_option')))
				{
					$this->init_module('payment_gateways', $row['module']['module_folder']);
					$module = $this->config->item('module_alias');
				}
			}

			//initialize the order data and add the order now...
			$this->data['order_data'] = initialize_order_data();

			$this->data['order_data']['order'] = $this->orders->create_order($this->data['order_data']);

			//check if the module saves customer tokens
			if (sess('transaction_data') && $this->data['order_data']['member']['member_id'])
			{
				if (method_exists($this->$module, 'add_customer_token'))
				{
					$this->$module->add_customer_token($this->data['order_data']['member']['member_id'], $_SESSION['transaction_data']['card_data']);
				}
			}

			//INVOICES
			$this->data['order_data']['invoice'] = $this->invoices->create_invoice($this->data['order_data']);

			//add payment and complete the order
			if (sess('transaction_data') || sess('checkout_payment_module') == 'free')
			{
				$this->data['order_data'] = $this->complete();

				//delete payment session for transaction so it's not used again
				$this->session->unset_userdata('transaction_data');
			}

			//update gift certificates if any
			if (!empty($this->data['order_data']['cart']['totals']['gift_certificates']))
			{
				$this->gift->update_certificate_redemption($this->data['order_data']['cart']['totals'], $this->data['order_data']['invoice']['id']);
			}

			//set the order data onto a new thank you page session
			$this->session->set_userdata('post_payment_data', $this->data['order_data']);

			//remove all items from the cart
			$this->cart->destroy();

			if (config_option('module_redirect_type') == 'offline') //bank transfer / check payments...
			{
				$this->data['order_data'] = $this->complete();

				$path = APPPATH . 'modules/payment_gateways/' . $row['module']['module_folder'] . '/views';

				$this->show->display('checkout', $row['module']['module_folder'] . '_process', $this->data, FALSE, $path);
			}
			elseif (config_option('module_redirect_type') == 'offsite')
			{
				//send the user to the offsite payment gateway
				$func = $this->config->item('module_one_off_pay_function');

				//run only if the method is available
				if (method_exists($this->$module, $func)) //generate_payment link
				{
					$this->data['redirect_link'] = $this->$module->$func($this->data['order_data'], $row);
				}

				$path = APPPATH . 'modules/payment_gateways/' . $row['module']['module_folder'] . '/views';

				$this->show->display('checkout', $row['module']['module_folder'] . '_process', $this->data, FALSE, $path);
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

	//send out the order mails here before redirecting to the thank you page.
	public function send()
	{
		if (config_enabled('send_checkout_emails_immediately'))
		{
			$this->mail->flush(0, 'checkout');
		}

		//check the redirect for specific product thank you pages...
		$url = site_url(checkout_offsite_url());

		$order_data = sess('post_payment_data');

		if (!empty($order_data['gift_certificates']))
		{
			$url = site_url('thank_you/gift_certificates');
		}

		$response = array('type'     => 'success',
		                  'redirect' => $url,
		);

		ajax_response($response);
	}

	//for ipn notifications
	public function notification()
	{
		$this->output->set_header('HTTP/1.0 200 OK');

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

								//get the order details
								$order = $this->orders->get_details($payment['data']['order_id'], TRUE, FALSE);

								$this->data['order_data'] = get_order_data($order);
								$this->data['order_data']['order'] = $order;

								if ($this->data['order_data'])
								{
									//set the payment transaction
									$this->data['order_data']['transaction'] = $payment['data'];

									if (!empty($payment['data']['invoice_id']))
									{
										$invoice = $this->invoices->get_details($payment['data']['invoice_id'], TRUE, FALSE);
									}

									//check for subscription payment
									if (!empty($payment['data']['subscription_id']) && empty($invoice))
									{
										//get invoice ID from subscription
										if ($sub = $this->dbv->get_record(TBL_MEMBERS_SUBSCRIPTIONS, 'subscription_id', $payment['data']['subscription_id']))
										{
											$invoice = $this->invoices->get_details($sub['invoice_id'], TRUE, FALSE);
										}
									}

									if (!empty($invoice) && $invoice['payment_status_id'] == '1')
									{
										$this->data['order_data']['invoice']['id'] = $invoice['invoice_id'];
										$this->data['order_data']['invoice']['data'] = $invoice;

										//complete the order
										$this->complete();
									}
									elseif (!empty($invoice))
									{
										//add credit if no invoice is to be paid
										$vars = $payment['data'];
										$vars['member_id'] = $invoice['member_id'];
										$vars['invoice_id'] = $invoice['invoice_id'];
										$this->credit->add_credit($vars );
									}
								}

								break;

							case 'refund':
								//todo
								break;
						}

						//log the ipn payment
						$this->$module->ipn_log($payment, 'create');

						$payment['msg_text'] = lang('ipn_data_received');
						$this->done(__METHOD__, $payment);
					}
				}
			}
		}

		$this->output->set_header('HTTP/1.0 200 OK');
	}

	protected function complete()
	{
		//set the order_data array
		$p = $this->data['order_data'];

		//if THERE IS A PAYMENT SESSION ADD IT
		//add the payment only if there is a transaction
		if (!empty($p['transaction']))
		{
			$p['payment'] = $this->invoices->create_payment($p, $p['invoice']['id']);

			//mark orders as paid
			if (!empty($p['payment']['paid']))
			{
				$this->orders->mark_paid($p['order']['order_id']);
			}

			//COMMISSIONS
			//generate commission for all downline members if any
			if (config_enabled('affiliate_marketing') && !empty($p['affiliate']))
			{
				$p['commissions'] = $this->comm->generate_commissions($p);
			}
		}

		if (!empty($p['transaction']) || sess('checkout_payment_module') == 'free')
		{
			//check if the user wants to subscribe to the list
			if (check_the_box('subscribe', $p['customer']))
			{
				//add to global mailing lists now
				$this->update_list('add_user', config_option('sts_cart_add_account_mailing_list'), $p['customer']['primary_email'], $p['customer'], sess('default_lang_id'));

				//remove from product specific mailing list
				$this->update_list('remove_user', config_option('sts_cart_remove_account_mailing_list'), $p['customer']['primary_email'], $p['customer'], sess('default_lang_id'));
			}

			//PRODUCT
			//set downloadable files
			$p['downloads'] = array();
			$p['gift_certificates'] = array();
			$p['subscriptions'] = array();

			//get admin users
			$admins = get_admins();

			foreach ($p['order']['items'] as $v)
			{
				$this->prod->update_product_inventories($v, $admins);

				switch ($v['product_type'])
				{
					case 'certificate':

						if ($certificates = $this->gift->add_certificate($p, $v))
						{
							foreach ($certificates as $g)
							{
								array_push($p['gift_certificates'], $g);
							}
						}

						break;

					case 'subscription':

						//add to subscription profiles
						$profile = $this->sub->create(format_cart_subscription($p, $v));

						array_push($p['subscriptions'], $profile);

						break;
				}

				if (check_the_box('subscribe', $p['customer']))
				{
					//add to product specific mailing list
					$this->update_list('add_user', $v['add_mailing_list'], $p['customer']['primary_email'], $p['customer'], sess('default_lang_id'));

					//remove from product specific mailing list
					$this->update_list('remove_user', $v['remove_mailing_list'], $p['customer']['primary_email'], $p['customer'], sess('default_lang_id'));
				}

				//set downloadable file access if any
				if ($downloads = $this->dw->generate_user_downloads($v['product_id'], $p['customer'], sess('default_lang_id')))
				{
					foreach ($downloads as $d)
					{
						array_push($p['downloads'], $d);
					}
				}
			}

			//add reward points to members
			if (!empty($p['member']['member_id']))
			{
				$p['reward_points'] = get_total_points($p['order']['items']);

				if ($p['reward_points'] > 0)
				{
					$this->rewards->add_reward_points($p['member']['member_id'], 'reward_product_points', $p['reward_points']);
				}
			}

			//mark the order processed
			$this->orders->mark_done($p['order']['order_id']);
		}

		//COUPONS
		//update coupon use
		$this->coupon->update_coupon_use($p['coupon']);

		//generate signup bonuses
		if (!empty($p['member']))
		{
			$p['bonuses'] = $this->comm->generate_signup_bonuses($p['member']);
		}

		//SEND OUT EMAILS
		//to customer if order alert emails are enabled
		$this->mail->send_customer_order_emails($p);

		//to all admins who have the alert enabled
		$this->mail->send_admin_order_emails($p);

		//update member to customer
		$this->checkout->update_to_customer($p['member']['member_id']);

		//run modules
		$p['msg_text'] = lang('order_id') . ' ' . is_var($p['order'], 'order_id');
		$this->done(__METHOD__, $p);

		return $p;
	}
}

/* End of file Order.php */
/* Location: ./application/controllers/checkout/Order.php */