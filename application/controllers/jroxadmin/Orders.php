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
class Orders extends Admin_Controller
{
	/**
	 * @var string
	 */
	protected $data = '';

	public function __construct()
	{
		parent::__construct();

		$m = array('products'                   => 'prod',
		           'affiliate_groups'           => 'aff_group',
		           'discount_groups'            => 'disc_group',
		           'products_attributes'        => 'att',
		           'products_categories'        => 'cat',
		           'products_specifications'    => 'specs',
		           'products_downloads'         => 'dw',
		           'tax_classes'                => 'tax',
		           'orders'                     => 'orders',
		           'members'                    => 'mem',
		           'cart'                       => 'cart',
		           'uploads'                    => 'uploads',
		           'shipping'                   => 'ship',
		           'coupons'                    => 'coupon',
		           'email_mailing_lists'        => 'lists',
		           'forms'                      => 'form',
		           'modules'                    => 'mod',
		           'invoices'                   => 'invoices',
		           'affiliate_commissions'      => 'comm',
		           'forms'                      => 'form',
		           'checkout'                   => 'checkout',
		           'affiliate_commission_rules' => 'comm_rules',
		           'affiliate_groups'           => 'aff_group',
		           'subscriptions'              => 'sub',
		           'gift_certificates'          => 'gift',
		);

		foreach ($m as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('string');
		$this->load->helper('download');

		$this->init->init_measurements();

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->orders->get_rows($this->data['page_options'], sess('default_lang_id'));

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('orders/' . TPL_ADMIN_ORDERS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			$row = $this->orders->validate_client($this->input->post());

			if (!empty($row['success']))
			{
				//add the data to the session
				$this->session->set_userdata('order_client_data', $this->dbv->clean($row['data']));

				$response = array('type' => 'success',
				                  'data' => $row['data'],
				                  'msg'  => lang('client_verified_successfully'),
				);
			}
			else
			{
				$response = array('type'         => 'error',
				                  'error_fields' => $row['error_fields'],
				                  'msg'          => $row['msg'],
				);
			}

			ajax_response($response);
		}
		else
		{
			//reset cart contents
			reset_order_data();

			//fill in default values for input fields
			$this->data['row'] = set_default_order_data();

			//run the page
			$this->load->page('orders/' . TPL_ADMIN_ORDERS_CREATE, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = (int)uri(4);

		//lets refresh the session first so no other orders are set....
		reset_order_data();

		if ($p = $this->orders->get_details($this->data['id']))
		{
			$this->data['row'] = format_order_details($p);

			$this->load->page('orders/' . TPL_ADMIN_ORDERS_UPDATE, $this->data);
		}
		else
		{
			log_error('error', lang('no_record_found'));
		}
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_ORDERS, 'order_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
			redirect_flashdata($url, $row['msg_text']);
		}
	}

	// ------------------------------------------------------------------------

	public function add_product()
	{
		$this->init->check_ajax_security();

		if (!$this->input->post())
		{
			show_error(lang('invalid_ajax_request') . ' ' . __METHOD__);
		}

		//default response
		$response = array(
			'type' => 'error',
			'msg'  => lang('invalid_product'),
		);

		//validate product data and inventory
		$id = (int)$this->input->post('product_id');

		//get product data
		$p = $this->prod->get_details($id, sess('default_lang_id'));

		if (!empty($p) && $this->input->post('quantity') > 0)
		{
			//validate product data like inventory and quantity
			$row = $this->orders->validate_product($p, $this->input->post());

			if (!empty($row['success']))
			{
				if ($this->input->post('order_id'))
				{
					$this->orders->update_current_order_contents($row['data'], $this->input->post(), sess('default_lang_id'));
				}
				else
				{
					//add product to session order contents
					update_order_contents($row['data'], $this->input->post('quantity'));
				}

				$response = array('type' => 'success');
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => $row['msg'],
				);
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function create_order() //this creates the entire order now...
	{
		if ($this->input->post())
		{
			$this->data['cart'] = get_order_cart();

			//create admin order first
			$row = $this->orders->create_admin_order($this->data['cart'], $this->input->post('order_notes'));

			if (!empty($row['success']))
			{
				//create invoice if set...
				if ($this->input->post('generate_invoice') != '0')
				{
					$row['data']['invoice'] = $this->invoices->create_invoice($row['data'], 'order');

					if ($this->input->post('generate_invoice') == '2')
					{
						//send template email
						$invoice = format_checkout_email('invoice_admin', $row['data']['invoice']['data']);
						$this->mail->send_template(EMAIL_MEMBER_PAYMENT_INVOICE, $invoice, sess('default_lang_id'), TRUE, $invoice['customer_primary_email']);
					}
				}

				//send email if we wanna send the order details to the user
				if ($this->input->post('send_email'))
				{
					//send template email
					$row['data']['order']['invoice_id'] = $row['data']['invoice']['id'];
					$order = format_checkout_email('order_admin', $row['data']['order']);
					$this->mail->send_template(EMAIL_MEMBER_ORDER_DETAILS, $order, sess('default_lang_id'), TRUE, $order['order_primary_email']);
				}

				$this->done(__METHOD__, $row);

				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['data']['order']['order_id'], $row['msg_text']);
			}
			else
			{
				log_error('error', lang('could_not_create_record'));
			}
		}
	}

	// ------------------------------------------------------------------------

	public function email()
	{
		$this->data['id'] = (int)uri(4);

		$data['order'] = $this->orders->get_details($this->data['id']);

		$order = format_checkout_email('order', $data);

		if ($this->mail->send_template(EMAIL_MEMBER_ORDER_DETAILS, $order, FALSE, sess('default_lang_id'), $order['order_primary_email']))
		{
			$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
			redirect_flashdata($url, 'email_sent_successfully');
		}
		else
		{
			show_error(lang('could_not_send_email'));
		}
	}

	// ------------------------------------------------------------------------

	public function download()
	{
		if (file_exists($path = $this->config->slash_item('sts_products_upload_folder_path') . uri(4)))
		{
			download_file(uri(4), 'orders');
		}
		else
		{
			show_error(lang('invalid_file'));
		}
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('id'))
		{
			$row = $this->orders->mass_update($this->input->post('id'), $this->input->post('change-status'));
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		$msg = !empty($row['msg_text']) ? $row['msg_text'] : '';

		redirect_flashdata($url, $msg);
	}

	// ------------------------------------------------------------------------

	public function update_order_profile()
	{
		$this->init->check_ajax_security();

		if ($this->input->post())
		{
			$this->data['id'] = $this->input->post('order_id');

			//check if the form submitted is correct
			$row = $this->orders->validate_client($this->input->post(), 'update');

			if (!empty($row['success']))
			{
				//update billing info
				$row = $this->orders->update_order($this->data['id'], $row['data']);

				$this->done(__METHOD__, $row);

				$response = array('type'     => 'success',
				                  'data'     => $row['data'],
				                  'redirect' => admin_url('orders/view'),
				);
			}
			else
			{
				//show errors on form
				$response = array('type'         => 'error',
				                  'error_fields' => $row['error_fields'],
				                  'msg'          => validation_errors(),
				);
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function update_order_items()
	{
		$this->init->check_ajax_security();

		if ($this->input->post('item'))
		{
			foreach ($this->input->post('item') as $k => $v)
			{
				if ((int)$v > 0)
				{
					//update the quantity
					$this->orders->update_item_quantity($k, $v, $this->input->post('order_id'));
				}
				else
				{
					//remove the item from the table
					$this->orders->remove_item($k, $this->input->post('order_id'));
				}
			}
		}

		$response = array('type' => 'success',
		                  'msg'  => lang('items_updated_successfully'));

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function update_order_contents()
	{
		$this->init->check_ajax_security();

		$this->data['id'] = (int)uri(4);

		if ($p = $this->orders->get_details($this->data['id']))
		{
			if (!empty($p))
			{
				$this->data['row'] = format_order_details($p);
			}

			$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_UPDATE_PRODUCT_CONTENTS, $this->data);

		}
		else
		{
			log_error('error', lang('no_record_found'));
		}
	}

	// ------------------------------------------------------------------------

	public function update_shipping_info()
	{
		$this->init->check_ajax_security();

		if ($this->input->post())
		{
			$this->data['id'] = $this->input->post('order_id');

			//check if the form submitted is correct
			$row = $this->orders->validate_shipping($this->input->post());

			if (!empty($row['success']))
			{
				if (!$this->input->post('charge_shipping'))
				{
					$row['data']['shipping_name'] = '';
				}

				//update billing info
				$row = $this->orders->update_order($this->data['id'], $row['data'], 'shipping');

				$this->done(__METHOD__, $row);

				$response = array('type'            => 'success',
				                  'data'            => $row['data'],
				                  'charge_shipping' => $this->input->post('charge_shipping') ? TRUE : FALSE,
				                  'redirect'        => admin_url('orders/view'),
				                  'msg'        => lang('changes_saved_successfully')
				);
			}
			else
			{
				//show errors on form
				$response = array('type'         => 'error',
				                  'error_fields' => $row['error_fields'],
				                  'msg'          => validation_errors(),
				);
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function order_contents()
	{
		$this->init->check_ajax_security();

		$this->data['cart'] = get_order_cart();

		$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_PRODUCT_CONTENTS, $this->data);
	}

	// ------------------------------------------------------------------------

	public function check_order_contents()
	{
		$this->init->check_ajax_security();

		$this->data['id'] = (int)uri(4);

		//set the default response
		$response = array('success' => TRUE);

		if (!$row = $this->orders->get_order_items($this->data['id']))
		{
			$response = array('error' => TRUE,
			                  'msg'   => lang('no_products_added_to_order'),
			);
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function load_generate_payment()
	{
		$this->init->check_ajax_security();

		$this->data['cart'] = get_order_cart();

		$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_GENERATE_PAYMENT, $this->data);
	}

	// ------------------------------------------------------------------------

	public function check_cart_contents()
	{
		$this->init->check_ajax_security();

		//set the default response
		$response = array('success' => TRUE);

		if (!sess('order_contents_data'))
		{
			$response = array('error' => TRUE,
			                  'msg'   => lang('no_products_added_to_order'),
			);
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function update_cart()
	{
		$this->init->check_ajax_security();

		if (order_update_quantity($this->input->post(NULL, TRUE)))
		{
			$response = array('type' => 'success',
			);
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function set_discounts()
	{
		$this->init->check_ajax_security();

		if (sess('order_contents_data'))
		{
			$this->data['cart'] = sess('order_contents_data');

			$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_SET_DISCOUNTS, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function update_discount()
	{
		$this->init->check_ajax_security();

		//set default response
		$response = array('type' => 'error',
		                  'msg'  => '',
		);

		if ($this->input->post('discount_type'))
		{
			//check if there is already a cart
			$this->data['cart'] = get_order_cart();

			switch ($this->input->post('discount_type'))
			{
				case 'coupon':

					if (!empty($this->data['cart']['items']))
					{
						$row = $this->coupon->validate_admin_coupon($this->input->post('code', TRUE), $this->data['cart']);
					}

					//the coupon is valid...
					if (!empty($row['success']))
					{
						//set session coupon
						$this->session->set_userdata('order_coupon_data', $row['coupon_data']);

						$response = array('type' => 'success',
						                  'msg'  => lang('coupon_applied_successfully'),
						);
					}
					else
					{
						//show ajax errors
						$response['msg'] = $row['msg_text'];
					}

					break;

				case 'certificate': //@todo

					break;
			}


		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function set_shipping()
	{
		$this->init->check_ajax_security();

		if ($this->input->post())
		{
			//set default response
			$response = array('type' => 'success');

			//save the shipping address data
			$this->session->unset_userdata(array('order_shipping_data', 'order_shipping_address_data'));

			if ($this->input->post('charge_shipping'))
			{
				//validate address
				$row = $this->orders->validate_address('shipping', $this->input->post(NULL, TRUE));

				if (!empty($row['error']))
				{
					$response = array('type' => 'error',
					                  'msg'  => $row['msg'],
					);
				}
				else
				{
					$response['charge_shipping'] = TRUE;

					//save the shipping address data
					$this->session->set_userdata('order_shipping_address_data', $row['customer_data']);
				}
			}

			ajax_response($response);
		}
		else
		{
			//get shipping addresses if the user has a member id
			$this->data['member'] = sess('order_client_data', 'member_id') ? $this->mem->get_details((int)sess('order_client_data', 'member_id'), TRUE) : '';

			//if there are, generate fields for shipping sub form
			$this->data['fields'] = init_sub_forms($this->form->init_form(2, sess('default_lang_id'), $this->data['member'], TRUE));

			$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_SET_SHIPPING, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function update_notes()
	{
		$this->init->check_ajax_security();

		if ($this->input->post('order_notes'))
		{
			$response = array('type' => 'error');

			$row = $this->dbv->update(TBL_ORDERS, 'order_id', $this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$response = array('type' => 'success',
				                  'msg'  => lang('changes_saved_successfully'),
				                  'data' => $row['data'],
				);

				$this->done(__METHOD__, $row);

				ajax_response($response);
			}
		}
	}

	// ------------------------------------------------------------------------

	public function generate_postage()
	{
		//initialize the require files for the module
		$this->init_module('shipping', 'easypost');

		//set model and function alias for calling methods
		$module = $this->config->item('module_alias');

		//run only if the method is available
		if (method_exists($this->$module, 'generate_postage'))
		{
			$order = $this->orders->get_order_totals(uri(4));

			if (uri(5) == 'print')
			{
				$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_PRINT_POSTAGE, $order);
			}
			else
			{
				$shipping_data = $order;

				try
				{
					$row = $this->$module->generate_postage($shipping_data, $order);

					if (!empty($row['success']))
					{
						$this->done(__METHOD__, $row);

						redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . uri(4) . '#tracking', $row['msg_text']);
					}
				} catch (Exception $e)
				{
					log_error('error', $e->getMessage());
				}
			}
		}
		else
		{
			log_error('error', lang('no_record_found'));
		}
	}

	// ------------------------------------------------------------------------

	public function packing_list() //print an order packing list
	{
		$this->data['id'] = (int)uri(4);

		if ($p = $this->orders->get_details($this->data['id']))
		{
			$this->data['row'] = format_order_details($p);

			$this->load->page('orders/' . TPL_ADMIN_ORDERS_PRINT, $this->data, 'admin', FALSE, FALSE, TRUE);
		}
		else
		{
			log_error('error', lang('no_record_found'));
		}
	}

	// ------------------------------------------------------------------------

	public function process() //process the items on an order
	{
		$this->data['id'] = valid_id(uri(4));

		//get order details
		$p = $this->orders->get_details($this->data['id']);

		if (empty($p['parent_order'])) //process only for new orders
		{
			$p['downloads'] = array();
			$p['gift_certificates'] = array();
			$p['subscriptions'] = array();

			foreach ($p['items'] as $v)
			{
				//get product details
				$prod = $this->prod->get_details($v['product_id']);
				$v = array_merge($v, $prod);

				$this->prod->update_product_inventories($v);

				switch ($v['product_type'])
				{
					case 'certificate':

						$certificates = $this->gift->add_certificate($p, $v, 'admin');

						foreach ($certificates as $g)
						{
							array_push($p['gift_certificates'], $g);
						}

						break;

					case 'subscription':

						//add to subscription profiles
						$profile = $this->sub->create(format_order_subscription($p, $v, 'admin'));

						array_push($p['subscriptions'], $profile);

						break;
				}

				//add to product specific mailing list
				$this->update_list('add_user', $v['add_mailing_list'], $p['order_primary_email'], $p, sess('default_lang_id'));

				//remove from product specific mailing list
				$this->update_list('remove_user', $v['remove_mailing_list'], $p['order_primary_email'], $p, sess('default_lang_id'));

				//set downloadable file access if any
				if ($downloads = $this->dw->generate_user_downloads($v['product_id'], $p, sess('default_lang_id')))
				{
					foreach ($downloads as $d)
					{
						array_push($p['downloads'], $d);
					}
				}
			}

			//add reward points to members
			if (!empty($p['member_id']))
			{
				$p['reward_points'] = get_total_points($p['items']);

				if ($p['reward_points'] > 0)
				{
					$this->rewards->add_reward_points($p['member_id'], 'reward_product_points', $p['reward_points']);
				}
			}
		}

		//set order to processed
		$row = $this->orders->mark_done($this->data['id']);

		$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

		//set the session flash and redirect the page
		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'order_processed_successfully');
	}

	// ------------------------------------------------------------------------

	public function update_tracking()
	{
		$this->init->check_ajax_security();

		if ($this->input->post())
		{
			$response = array('type' => 'error');

			$row = $this->orders->update_tracking($this->input->post());

			if (!empty($row['success']))
			{
				$response = array('type' => 'success',
				                  'msg'  => lang('changes_saved_successfully'),
				                  'data' => array('shipping_carrier' => $row['data']['carrier'],
				                                  'shipping_service' => $row['data']['service'],
				                                  'tracking_id'      => $row['data']['tracking_id']),
				);

				$this->done(__METHOD__, $row);
			}

			ajax_response($response);
		}
	}

	// ------------------------------------------------------------------------

	public function update_order_shipping()
	{
		$this->init->check_ajax_security();

		if ($this->input->post('select_shipping'))
		{
			$response = array('type' => 'error');

			$row = $this->orders->update_order_shipping((int)uri(4), sess('order_shipping_options', $this->input->post('select_shipping')));

			if (!empty($row['success']))
			{
				$response = array('type' => 'success',
				                  'msg'  => lang('changes_saved_successfully'),
				                  'data' => $row['data'],
				);

				$this->done(__METHOD__, $row);
			}

			ajax_response($response);
		}
	}

	// ------------------------------------------------------------------------

	public function shipping_options()
	{
		$this->init->check_ajax_security();

		//get shipping modules
		$rows = $this->ship->get_shipping_modules();

		if ($this->input->post())
		{
			if ($this->input->post('select_shipping'))
			{
				$response = array('type' => 'success');

				//save the selected shipping option
				$this->session->set_userdata('order_shipping_data', sess('order_shipping_options', $this->input->post('select_shipping')));
			}
			else
			{
				//oh!  no shipping option was selected...
				$response = array('type' => 'error',
				                  'msg'  => lang('please_select_a_shipping_option'),
				);
			}

			ajax_response($response);
		}
		else
		{
			//check if this is an update to an existing order
			if (uri(4) == 'update' && uri(5))
			{
				$cart = $this->orders->get_order_totals(uri(5));
				$shipping_data = $cart;
				$this->data['form_url'] = admin_url('orders/update_order_shipping/' . uri(5));
			}
			else
			{
				$cart = get_order_cart();
				$shipping_data = sess('order_shipping_address_data');
				$this->data['form_url'] = admin_url('orders/shipping_options');
			}

			//go through each shipping module
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
					$func = $this->config->item('module_generate_function');

					//run only if the method is available
					if (method_exists($this->$module, $func))
					{
						$rate = $this->$module->$func($shipping_data, $cart);

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
				$this->data['shipping_options'] = order_free_shipping($cart, $shipping_options);
				$this->session->set_userdata('order_shipping_options', $this->data['shipping_options']);

				$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_SHIPPING_OPTIONS, $this->data);
			}
		}
	}

	// ------------------------------------------------------------------------

	public function billing_information()
	{
		$this->init->check_ajax_security();

		if ($this->input->post())
		{
			//set default response
			$response = array('type' => 'success');

			//validate address
			$row = $this->orders->validate_address('billing', $this->input->post(NULL, TRUE));

			if (!empty($row['error']))
			{
				$response = array('type' => 'error',
				                  'msg'  => $row['msg'],
				);
			}
			else
			{
				//save the billing address data
				$this->session->set_userdata('order_billing_address_data', $row['customer_data']);
			}


			ajax_response($response);
		}
		else
		{
			//get addresses if the user has a member id
			$this->data['member'] = sess('order_client_data', 'member_id') ? $this->mem->get_details((int)sess('order_client_data', 'member_id'), TRUE) : '';

			//if there are, generate fields for billing sub form
			$this->data['fields'] = init_sub_forms($this->form->init_form(2, sess('default_lang_id'), $this->data['member'], TRUE, 'billing'));

			$this->load->view('admin/orders/' . TPL_AJAX_ORDERS_BILLING_INFORMATION, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function upload()
	{
		//check for file uploads
		$files = $this->uploads->validate_uploads('cart');

		if (!empty($files['success']))
		{
			$row = $this->cart->add_upload($files);

			if (!empty($row['success']))
			{
				//set json response
				$response = array('type' => 'success',
				                  'key'  => $row['key'],
				                  'msg'  => $row['msg_text'],
				);

				$this->done(__METHOD__, $row);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => lang('could_not_save_file_to_db'),
				);
			}
		}
		else
		{
			//error!
			$response = array('type' => 'error',
			                  'msg'  => $files['msg'],
			);
		}


		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function general_search()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->orders->search($this->data['page_options'], sess('default_lang_id'));

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('orders/' . TPL_ADMIN_ORDERS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('order_id', TRUE);

		$rows = $this->orders->ajax_search(uri(5, 'order_number'), $term);

		echo json_encode($rows);
	}

	protected function update_list($type = '', $list = '', $email = '', $data = array())
	{
		//check if we are using a third party mailing list
		if (config_option('sts_email_mailing_list_module'))
		{
			//add the user to the internal list first...
			$this->lists->$type($list, $email, $data);

			// check if we're using a third party module..
			if (config_option('sts_email_mailing_list_module') != 'internal')
			{
				$this->init_module('mailing_lists', config_option('sts_email_mailing_list_module'));

				//run the add_user/remove_user function from the module
				$module = $this->config->item('module_alias');
				$func = $this->config->item('module_' . $type);

				//run only if the method is available
				if (method_exists($this->$module, $func))
				{
					$row = $this->$module->$func($list, $email, $data);

					//mailing list updated
					if (!empty($row['success']))
					{
						$this->done(__METHOD__, $row);
					}
				}
			}

			//reset module
			$this->remove_module('mailing_lists', config_option('sts_email_mailing_list_module'));
		}
	}
}

/* End of file Orders.php */
/* Location: ./application/controllers/admin/Orders.php */