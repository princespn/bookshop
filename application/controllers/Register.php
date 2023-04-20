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
class Register extends Public_Controller
{
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array('forms'                 => 'form',
		                'affiliate_commissions' => 'comm',
		                'email_mailing_lists'   => 'lists',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//first lets get the form fields
		$this->data['fields'] = $this->form->get_form_fields(1, sess('default_lang_id'), '', TRUE);

		if ($this->input->post())
		{
			$row = $this->form->validate_fields('register', $this->input->post(), $this->data['fields']['values']);

			if (!empty($row['success']))
			{
				//add the user
				$data = $this->mem->create($row['data'], 'affiliate');

				$data['sponsor_data'] = check_referral_data($data);

				//generate signup bonuses
				$data = $this->comm->generate_signup_bonuses($data);

				//generate rewards
				$this->rewards->add_reward_points($data['member_id'], 'reward_user_account_registration');

				//send emails
				$this->mail->send_registration_emails($data);

				//subscribe to newsletter
				if (check_the_box('subscribe', $data))
				{
					$this->lists->add_user(config_option('sts_members_default_mailing_list'), $data['primary_email'], $data);

					// check if we're using a third party module..
					if (config_option('sts_email_mailing_list_module') != 'internal')
					{
						$this->init_module('mailing_lists', config_option('sts_email_mailing_list_module'));

						//run the add_user/remove_user function from the module
						$module = config_item('module_alias');
						$func = config_item('module_add_user');
						$list_id = config_item('module_mailing_lists_' . config_item('sts_email_mailing_list_module') . ')_list_id');

						//run only if the method is available
						if (method_exists($this->$module, $func))
						{
							$row = $this->$module->$func($list_id, $data['primary_email'], $data);
						}
					}

					//reset module
					$this->remove_module('mailing_lists', config_option('sts_email_mailing_list_module'));
				}

				//set redirect url
				$url = site_url(strtolower(__CLASS__) . '/confirm');

				//run modules
				$this->done(__METHOD__, $data);

				//set the default response
				$response = array('type'     => 'success',
				                  'redirect' => $url,
				);
			}
			else
			{
				$response = array('type'         => 'error',
				                  'error_fields' => $row['error_fields'],
				                  'msg'          => $row['msg'],
				);
			}

			$this->session->set_flashdata('confirm', TRUE);

			ajax_response($response);
		}

		//set the tos
		$this->data['tos_link'] = uri(2) == 'affiliate' ? site_url('affiliate_program_tos') : site_url('tos');

		$this->show->display('form', CONTROLLER_CLASS, $this->data);
	}

	// ------------------------------------------------------------------------

	public function confirm()
	{
		if (uri(3))
		{
			//verify confirmation code
			if ($this->mem->confirm_registration(valid_id(uri(3, TRUE))))
			{
				$this->data['confirmed'] = TRUE;
			}
			else
			{
				redirect_page('login');
			}
		}
		elseif (!sess('confirm'))
		{
			redirect_page('login');
		}

		$this->show->display('form', __FUNCTION__, $this->data);
	}
}

/* End of file Register.php */
/* Location: ./application/controllers/Register.php */