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
class Checkout_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * Checkout_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('checkout');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function update_to_customer($id = '')
	{
		$this->db->where('member_id', $id)->update(TBL_MEMBERS, array('is_customer' => '1'));

		$this->session->set_userdata('is_customer', '1');

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate_billing($data = array())
	{
		//get all the fields for validation
		$fields = $this->form->init_form(2, sess('default_lang_id'), '', TRUE);

		//use the same billing as shipping
		if (empty($data[ 'use_different_billing' ]) && sess('cart_charge_shipping'))
		{
			$customer_data = checkout_shipping_as_billing($fields[ 'values' ]);
		}
		else
		{
			//validate account details for checkout
			$row = array( 'error'         => '',
			              'error_fields'  => array(),
			              'customer_data' => array() );

			//check if the user has set a default address
			if (sess('user_logged_in') && $this->input->post('billing_address_id'))
			{
				//validate the id and make sure it is for the logged in user
				$mem = $this->mem->get_member_address((int)$this->input->post('billing_address_id'), sess('member_id'));

				$customer_data = order_member_data('billing', $mem);
			}
			else
			{
				//let's validate the other billing fields
				$p = $this->form->validate_fields('billing', $data, $fields[ 'values' ]);

				if (!empty($p[ 'error' ]))
				{
					$row[ 'error' ] .= $p[ 'msg' ];
					$row[ 'error_fields' ] = array_merge($row[ 'error_fields' ], $p[ 'error_fields' ]);
				}

				$customer_data = $p[ 'data' ];
			}
		}

		//check if we are adding a new address
		if (!$this->input->post('billing_address_id'))
		{
			$customer_data[ 'add_billing_address' ] = TRUE;
		}

		//set the default billing name if it's not set
		if (empty($customer_data[ 'billing_fname' ]))
		{
			$customer_data[ 'billing_fname' ] = is_var($customer_data,'fname');
			$customer_data[ 'billing_lname' ] = is_var($customer_data,'lname');
		}

		//merge the current customer data with billing data
		$row[ 'customer_data' ] = array_merge(sess('checkout_customer_data'), $customer_data);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return array
	 */
	public function validate_account($data = array(), $type = 'account')
	{
		//validate account details for checkout
		$row = array( 'error'         => '',
		              'error_fields'  => array(),
		              'customer_data' => array() );

		//get all the fields for validation
		$fields = $this->form->init_form(2, sess('default_lang_id'), '', TRUE);

		//check if the user is logged in and get his/her details
		if (sess('user_logged_in'))
		{
			if (!$mem_data = $this->mem->get_details((int)sess('member_id')))
			{
				show_error('invalid_user', 500, lang('checkout_error'));
			}

			//lets format the fields to use
			$row[ 'customer_data' ] = order_member_data($type, $mem_data);
		}
		else
		{
			//check for all required fields first.
			$p = $this->form->validate_fields($type, $data, $fields[ 'values' ]);

			//set the language id
			$p['data']['language'] = sess('default_lang_id');

			if (!empty($p[ 'error' ]))
			{
				$row[ 'error' ] .= $p[ 'msg' ];
				$row[ 'error_fields' ] = array_merge($row[ 'error_fields' ], $p[ 'error_fields' ]);
			}

			$row[ 'customer_data' ] = array_merge($row[ 'customer_data' ], $p[ 'data' ]);
		}

		//check if we need to check for shipping info
		if (sess('cart_charge_shipping'))
		{
			$customer_data = array();

			//check if the user has set a default address
			if (sess('user_logged_in') && $this->input->post('shipping_address_id'))
			{
				//validate the id and make sure it is for the logged in user
				$customer_data = $this->mem->get_member_address((int)$this->input->post('shipping_address_id'), sess('member_id'));

			}

			//if no default address has been set, run validation on shipping address
			if (empty($customer_data))
			{
				//check for all required fields first.
				$p = $this->form->validate_fields('shipping', $data, $fields[ 'values' ]);

				//check for errors
				if (!empty($p[ 'error' ]))
				{
					$row[ 'error' ] .= $p[ 'msg' ];
					$row[ 'error_fields' ] = array_merge($row[ 'error_fields' ], $p[ 'error_fields' ]);
				}

				$row[ 'customer_data' ] = array_merge($row[ 'customer_data' ], $p[ 'data' ]);
			}

			//check if we are adding a new address
			if (!$this->input->post('shipping_address_id'))
			{
				$row[ 'customer_data' ][ 'add_shipping_address' ] = TRUE;
			}

			$row[ 'customer_data' ] = order_member_data('shipping', $customer_data, $row[ 'customer_data' ]);
		}

		if (!sess('user_logged_in'))
		{
			//check payment subform info
			$p = $this->form->validate_fields('payment', $data, $fields['values']);
		}

		if (!empty($p[ 'error' ]))
		{
			$row[ 'error' ] .= $p[ 'msg' ];
			$row[ 'error_fields' ] = array_merge($row[ 'error_fields' ], $p[ 'error_fields' ]);
		}

		//merge the payment data back to the customer data array..
		if (!empty($p['data']))
		{
			$row['customer_data'] = array_merge($row['customer_data'], $p['data']);
		}
		
		return $row;
	}
}

/* End of file Checkout_model.php */
/* Location: ./application/models/Checkout_model.php */