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
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
class Account extends Member_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'affiliate_groups'    => 'aff_group',
			'discount_groups'     => 'disc_group',
			'email_mailing_lists' => 'lists',
			'login'               => 'login',
			'uploads'             => 'uploads',
			'regions'             => 'regions',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * Account for Member
	 *
	 * Shows the Account area for the member
	 */
	public function index()
	{
		$this->data['member'] = $this->mem->get_details((int)sess('member_id'), TRUE);

		if (!$this->data['member'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->data['fields'] = $this->form->init_form(1, sess('default_lang_id'), $this->data['member']);

		if ($this->data['fields'])
		{
			$this->data['custom_fields'] = $this->form->get_member_custom_fields(sess('member_id'), sess('default_lang_id'), $this->data['member']);

			$this->data['backgrounds'] = get_images('backgrounds');

			if ($this->sec->verify_ownership())
			{
				//check if the form submitted is correct
				$row = $this->mem->validate('update_profile', $this->input->post(), $this->data['fields']['values']);

				//all good...
				if (!empty($row['success']))
				{
					$row = $this->mem->update($row['data'], $this->data['custom_fields'], 'member');

					$this->login->set_login_data($this->mem->get_details(sess('member_id')));

					//set json response
					$response = array('type' => 'success',
					                  'msg'  => $row['msg_text'],
					                  'csrf' => set_csrf(),
					);

					$this->done(__METHOD__, $row);
				}
				else
				{
					//errors!
					$response = array('type'         => 'error',
					                  'error_fields' => generate_error_fields(),
					                  'msg'          => validation_errors());

					//log errors in db
					$this->dbv->rec(array('method' => __METHOD__,
					                      'msg'    => $row['msg_text'],
					                      'level'  => 'error'));

				}

				//send the response via ajax
				ajax_response($response);
			}
		}

		$this->show->display(MEMBERS_ROUTE, CONTROLLER_CLASS, $this->data);
	}

	/**
	 * Update address
	 *
	 * Update a members address details
	 */
	public function update_address()
	{
		//set the id
		$this->data['id'] = (int)uri(4);

		//check if there is any POST data and make sure it is for this session
		if ($this->sec->verify_ownership())
		{
			//check if the form submitted is correct
			$row = $this->mem->validate_address(__FUNCTION__, $this->input->post(NULL, TRUE));

			//all good...
			if (!empty($row['success']))
			{
				//set json response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text']);

				$this->done(__METHOD__, $row);
			}
			else
			{
				//errors!
				$response = array('type'         => 'error',
				                  'error_fields' => generate_error_fields(),
				                  'msg'          => validation_errors());

				//log errors in db
				$this->dbv->rec(array('method' => __METHOD__,
				                      'msg'    => lang('error_updating_record'),
				                      'level'  => 'error'));
			}

			//send the response via ajax
			ajax_response($response);
		}

		//get the addresses from db
		$this->data['address'] = $this->mem->get_member_address($this->data['id'], (int)sess('member_id'));

		$this->show->display(MEMBERS_ROUTE, 'manage_address', $this->data);
	}

	/**
	 *  Add address
	 *
	 * Add an address for the member
	 */
	public function add_address()
	{
		//check if there is any POST data and make sure it is for this session
		if ($this->sec->verify_ownership())
		{
			//check if the form submitted is correct
			$row = $this->mem->validate_address(__FUNCTION__, $this->input->post(NULL, TRUE));

			//all good...
			if (!empty($row['success']))
			{
				//set json response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => site_url());

				$this->done(__METHOD__, $row);
			}
			else
			{
				//errors!
				$response = array('type'         => 'error',
				                  'error_fields' => generate_error_fields(),
				                  'msg'          => validation_errors());

				//log errors in db
				$this->dbv->rec(array('method' => __METHOD__,
				                      'msg'    => lang('error_updating_record'),
				                      'level'  => 'error'));
			}

			//send the response via ajax
			ajax_response($response);
		}

		//set default country and region data
		$this->data['address'] = get_default_country();

		$this->show->display(MEMBERS_ROUTE, 'manage_address', $this->data);
	}

	/**
	 * Delete address
	 *
	 * Delete a member's address book entry
	 */
	public function delete_address()
	{
		$this->data['id'] = (int)uri(4);

		$address = $this->mem->get_member_address($this->data['id'], (int)sess('member_id'));

		//check if the user actually owns this address first
		if ($this->sec->verify_ownership($address['member_id'], (int)sess('member_id')))
		{
			if ($row = $this->mem->delete_address($this->data['id']))
			{
				$this->done(__METHOD__, $row);
			}
			else
			{
				//log errors in db
				$this->dbv->rec(array('method' => __METHOD__,
				                      'msg'    => lang('error_deleting_record'),
				                      'level'  => 'error'));

			}
		}

		redirect_flashdata(MEMBERS_ROUTE . '/' . strtolower(__CLASS__) . '#addresses', $row['msg_text']);
	}

	/**
	 * Reset password
	 *
	 * Allow the member to reset his / her password
	 * through the members profile area
	 */
	public function reset_password()
	{
		if ($this->sec->verify_ownership())
		{
			//update the password
			$row = $this->mem->verify_password($this->input->post(NULL, TRUE), TRUE, sess('member_id'));

			//Success!
			if (!empty($row['success']))
			{
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text']);
			}
			else
			{
				//errors!
				$response = array('type' => 'error',
				                  'msg'  => $row['msg_text']);
			}

			$this->done(__METHOD__, $row);

		}

		redirect_flashdata(MEMBERS_ROUTE . '/account#reset_password', $response['msg'], $response['type']);
	}

	// ------------------------------------------------------------------------

	public function mailing_lists()
	{
		$row = $this->mem->get_details((int)sess('member_id'), TRUE);

		$url = base_url('email/subscriptions/' . md5(config_Item('sts_system_domain_key')) . '/' . $row['primary_email']);

		redirect($url);
	}

	// ------------------------------------------------------------------------

	public function profile()
	{
		if ($this->sec->verify_ownership())
		{
			$row = $this->mem->validate_profile($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_MEMBERS_PROFILES, 'member_id', $row['data']);
			}

			redirect_flashdata(MEMBERS_ROUTE . '/account#profile', $row['msg_text']);
		}

		redirect_page(site_url('members/account#profile'));
	}

	// ------------------------------------------------------------------------

	public function upload()
	{

		if ($this->sec->verify_ownership(uri(4)))
		{
			//check for file uploads
			$row = $this->uploads->validate_uploads('account');

			if (!empty($row['success']))
			{
				$file = $this->config->slash_item('base_url') . 'images/uploads/members/' . $row['file_data']['file_name'];

				//update the profile photo
				$this->mem->update_member_profile(array('member_id'     => sess('member_id'),
				                                        'profile_photo' => $file));

				$this->session->set_userdata('profile_photo', $file);

				//set json response
				$response = array('type'      => 'success',
				                  'file_name' => $file,
				                  'msg'       => $row['msg'],
				);

				//log it!
				$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg']));
			}
			else
			{
				//error!
				$response = array('type' => 'error',
				                  'msg'  => $row['msg'],
				);
			}
		}
		else
		{
			$response = array('type' => 'error',
			                  'msg'  => lang('invalid_photo'),
			);
		}
		//send the response via ajax
		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function billing()
	{
		//used for updating stripe related billing only
		$this->init_module('payment_gateways', 'stripe');

		//set model and function alias for calling methods
		$module = $this->config->item('module_alias');

		$row = $this->$module->get_customer_token(sess('member_id'));

		if (!empty($row))
		{
			$this->data['card'] = $row;

			$this->data['update_form'] = $this->$module->init_form('update_cc');
		}

		if ($this->input->post())
		{

			//generate payment option, captcha, tos and order notes
			$this->init_module('payment_gateways', 'stripe');

			//set model and function alias for calling methods
			$module = $this->config->item('module_alias');

			$row = $this->$module->update_card($this->data['card'], sess('member_id'), $this->input->post());

			//log it!
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			redirect_flashdata(site_url('members/account'), $row['msg_text']);
		}

		$this->show->display(MEMBERS_ROUTE, 'manage_billing', $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete_billing()
	{

		$this->data['id'] = valid_id(uri(4));

		//generate payment option, captcha, tos and order notes
		$this->init_module('payment_gateways', 'stripe');

		//set model and function alias for calling methods
		$module = $this->config->item('module_alias');

		if ($this->$module->delete_customer_token(sess('member_id')))
		{
			//set the session flash and redirect the page
			$type = 'success';
			$msg = lang('record_deleted_successfully');

			//log it!
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));
		}
		else
		{
			$type = 'error';
			$msg = lang('could_not_delete_record');
		}

		redirect_flashdata(MEMBERS_ROUTE . '/account', $msg, $type);
	}
}

/* End of file Account.php */
/* Location: ./application/controllers/members/Account.php */