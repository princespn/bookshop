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
class Cart extends Cart_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//check if there is already a cart
		$this->data[ 'cart' ] = $this->cart->get_cart();

		$this->data['id'] = $this->cart->get_random_id($this->data['cart']);

		if (config_enabled('sts_cart_recommend_products'))
		{
			$rows = $this->prod->load_similar_products($this->data['id'], sess('default_lang_id'), uri(4, DEFAULT_TOTAL_SIMILAR_PRODUCTS));

			//set the products array
			$this->data['recommended_products'] = !empty($rows['values']) ? $rows['values'] : FALSE;
		}

		//view the cart contents
		$this->show->display('cart', 'cart_' . $this->data['layout_design_cart_layout'], $this->data);
	}

	// ------------------------------------------------------------------------

	public function add()
	{
		$this->data[ 'id' ] = !$this->input->post('product_id') ? (int)uri(3) : (int)$this->input->post('product_id');

		//set default error
		$response = array( 'type' => 'error',
		                   'msg'  => lang('invalid_data')
		);

		if ($p = $this->prod->get_details($this->data[ 'id' ], sess('default_lang_id'), TRUE, TRUE, FALSE))
		{
			//check what type of product this is...
			if ($p['product_type'] == 'third_party')
			{
				if (is_ajax())
				{
					$response = array( 'type' => 'success',
					                   'msg'  => lang('affiliate_redirect'),
					                   'redirect' => $p['affiliate_redirect'],
					);
				}
				else
				{
					redirect_page($p['affiliate_redirect']);
				}
			}
			else
			{
				$row = $this->cart->validate($p, $this->input->post(NULL, TRUE));

				if (!empty($row['success']))
				{
					//set json response
					$response = array('type' => 'success');

					//default url to redirect to after adding product to cart
					$url = site_url('cart');

					//check the product type and redirect properly
					switch ($p['product_type'])
					{
						default:

							//add the product to the cart
							if ($this->cart->add_item($p, $row['post']))
							{
								//set the default message
								$response['msg'] = lang('item_added_to_cart_successfully');

								//set the flash data first
								$this->session->set_flashdata('success', $response['msg']);

								//check for recommendations
								if (!empty($p['cross_sell']))
								{
									$response['redirect'] = site_url('cart/recommend/' . $this->data['id']);
									$url = $response['redirect'];
								}
								else
								{
									//for general products, let's see if were going to the cart or staying on page
									if (config_enabled('sts_cart_redirect_to_cart_on_add_item'))
									{
										$response['redirect'] = $url;
									}
									else
									{
										$response['redirect'] = $this->agent->referrer();
										$url = $this->agent->referrer();
									}
								}
							}

							break;
					}
				}
				else
				{
					//show ajax errors
					$response['msg'] = $row['msg_text'];
				}
			}
		}
		else
		{
			$response[ 'msg' ] = lang('product_not_found');
		}

		//check if we're sending responses back via ajax or header redirect
		if ($this->input->post())
			{
			//send the response via ajax
			ajax_response($response);
		}
		else
		{
			//redirect to the proper area
			$url = $response[ 'type' ] == 'error' ? page_url('product', $p) : $url;

			redirect_flashdata($url, $response[ 'msg' ], 'error');
		}
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$row = $this->cart->validate_cart($this->input->post('qty', TRUE));

		if ($row[ 'result' ] == 'success')
		{
			$row = $this->cart->update_cart($this->input->post('qty', TRUE), $row[ 'cart_items' ]);

			//update coupon requirements
			$this->coupon->check_coupon_minimums();
		}

		redirect_flashdata(site_url('cart'), $row[ 'msg_text' ], $row[ 'result' ]);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$row = $this->cart->delete_item((int)uri(3));

		if (!empty($row[ 'success' ]))
		{
			$this->cart->update_session_cart();

			redirect_flashdata(site_url('cart'), $row[ 'msg_text' ]);
		}

		show_error('could_not_delete_item');
	}

	// ------------------------------------------------------------------------

	public function upload()
	{
		//check for file uploads
		$files = $this->uploads->validate_uploads('cart');

		if (!empty($files[ 'success' ]))
		{
			$row = $this->cart->add_upload($files);

			if (!empty($row[ 'success' ]))
			{
				//set json response
				$response = array( 'type' => 'success',
				                   'key'  => $row[ 'key' ],
				                   'file' => $row['file_name'],
				                   'msg'  => $row[ 'msg_text' ],
				);

				//log it!
				$this->dbv->rec(array('method' => __METHOD__,
				                      'msg' => $row['msg_text'],
				                      'vars' => $row
					));
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => lang('could_not_save_file_to_db')
				);
			}
		}
		else
		{
			//error!
			$response = array( 'type' => 'error',
			                   'msg'  => $files[ 'msg' ],
			);
		}

		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function recommend()
	{
		$this->data[ 'id' ] = valid_id(uri(3));

		$this->data['products'] = $this->prod->get_product_cross_sells($this->data[ 'id' ], sess('default_lang_id'));

		//no recommendations, let's redirect them to the cart.
		if (!$this->data['products'])
		{
			redirect_page('cart');
		}

		$this->data[ 'breadcrumb' ] = set_breadcrumb(array( lang('related_products') => '' ));

		//get product details
		$this->data['product'] = $this->prod->get_details($this->data[ 'id' ], sess('default_lang_id'), TRUE, TRUE, FALSE);

		$this->show->display(CONTROLLER_CLASS, 'cart_recommendations_' . $this->config->item('layout_design_cross_sell_layout'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function remove_coupon()
	{
		$row = $this->coupon->remove_coupon(uri(3), sess('cart_id'));

		if ($row[ 'success' ] == TRUE)
		{
			$this->cart->update_session_cart();
		}

		redirect_flashdata(site_url('cart'), $row[ 'msg_text' ], $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	public function apply_coupon()
	{
		//show ajax errors
		$response = array( 'type' => 'error',
		                   'msg'  => lang('invalid_coupon_code'),
		);

		if ($this->input->get('coupon'))
		{
			//check if there is already a cart
			$this->data[ 'cart' ] = $this->cart->get_cart();

			if (!empty($this->data[ 'cart' ][ 'items' ]))
			{
				$row = $this->coupon->validate_coupon($this->input->get('coupon', TRUE), $this->data[ 'cart' ]);
			}

			//the coupon is valid...
			if (!empty($row[ 'success' ]))
			{
				//apply coupon to cart
				if ($row = $this->coupon->apply_coupon($row))
				{
					//set session coupon
					$this->session->set_userdata('checkout_coupon_code', $row[ 'coupon' ]);

					//update cart
					$this->cart->update_session_cart();

					//set the flash data first
					$this->session->set_flashdata('success', $row[ 'msg_text' ]);

					//show ajax redirect
					$response = array( 'type'     => 'success',
					                   'msg'      => $row[ 'msg_text' ],
					                   'redirect' => !$this->agent->referrer() ? site_url('cart') : $this->agent->referrer(),
					);
				}
			}
			else
			{
				//show ajax errors
				$response[ 'msg' ] = $row[ 'msg_text' ];
			}
		}

		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function destroy()
	{
		$this->cart->destroy();

		redirect_flashdata(site_url('cart'), 'cart_updated_successfully', 'sucesss');
	}

	// ------------------------------------------------------------------------

	public function referral()
	{
		//reset shipping
		$this->session->unset_userdata('cart_charge_shipping');

		$this->cart->update_session_cart();

		//if the tracking data is already set let's redirect
		if (config_item('affiliate_data')) redirect(site_url('checkout'));

		if ($this->input->post('username'))
		{
			//validate username
			$user = url_title($this->input->post('username'));

			//set cookie
			$row = $this->aff->set_tracking_data($user);

			if (!empty($row))
			{
				$this->session->set_userdata('tracking_data', $row);

				//show ajax errors
				$response = array( 'type'     => 'success',
				                   'redirect' => site_url('checkout'),
				);
			}
			else
			{
				//show ajax errors
				$response[ 'msg' ] = lang('invalid_username');
			}

			//send the response via ajax
			ajax_response($response);
		}

		$this->show->display(CONTROLLER_CLASS, 'cart_referral', $this->data);

	}
}

/* End of file Cart.php */
/* Location: ./application/controllers/Cart.php */