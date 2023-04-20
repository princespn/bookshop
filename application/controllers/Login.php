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
class Login extends Public_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'affiliate_groups'    => 'aff_group',
			'discount_groups'     => 'disc_group',
			'email_mailing_lists' => 'lists',
			'products'            => 'prod',
			'login'               => 'login',
			'forms'               => 'form',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		//load the user agent library for referrers
		$this->load->library('user_agent');

		//check if we are using SSL for the members login
		$this->sec->check_ssl('members');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function page()
	{
		$this->data = $this->init->initialize('site');

		if ($this->input->post())
		{
			//check if the form submitted is correct
			if ($this->login->validate() == TRUE)
			{
				//run plugin
				$this->plugin->init_plugin(__METHOD__, $this->session->userdata());

				//set json response
				$data = array('type'     => 'success',
				              'msg_text' => lang('user_logged_in_successfully'),
				              'redirect' => $this->input->post('redirect') ? $this->input->post('redirect', TRUE) : site_url('login/process'));

				//remove ip block
				$this->sec->auto_block_ip('remove', $this->input->ip_address());

				//set logged_in session
				$this->session->set_flashdata('success', $data['msg_text']);

				$this->done(__METHOD__, $data, 'security');
			}
			else
			{
				//errors!
				$data = array('type' => 'error',
				              'msg'  => validation_errors());

				//add ip block
				$this->sec->auto_block_ip('block', $this->input->ip_address());

				//log errors in db
				$this->dbv->rec(array('method' => __METHOD__,
				                      'msg'    => lang('failed_user_login'),
				                      'vars'   => $this->input->post(),
				                      'level'  => 'security'));

			}

			//update cart data
			$this->cart->update_session_cart();

			//check if we're redirecting to ajax or another page
			if (is_ajax())
			{
				//send the response via ajax
				ajax_response($data);
			}
			else
			{
				$redirect = !$this->input->post('redirect', TRUE) ? $this->agent->referrer() : $this->input->post('redirect', TRUE);

				redirect_flashdata($redirect, $data['msg'], $data['type']);
			}
		}

		//first lets get the form fields
		$this->data['register'] = $this->form->get_form_fields(1, sess('default_lang_id'));

		if ($this->input->get('redirect'))
		{
			$this->data['redirect'] = $this->input->get('redirect', TRUE);
		}

		$this->show->display('form', 'login_page_default', $this->data);
	}

	// ------------------------------------------------------------------------

	public function process()
	{
		$this->data = $this->init->initialize('site');

		if (sess('success'))
		{
			$this->login->update_login_data('member', sess('member_id'));

			init_affiliate_store($_SESSION);

			$this->data['redirect'] = site_url(MEMBERS_ROUTE);

			$this->show->display('form', 'login_splash', $this->data);
		}
		else
		{
			redirect_page();
		}
	}

	// ------------------------------------------------------------------------

	public function confirm()
	{
		$this->data = $this->init->initialize('site');

		$this->sec->check_ssl('members');

		$this->data['breadcrumb'] = set_breadcrumb(array(lang('reset_password') => 'reset_password'));

		$this->data['code'] = uri(3);

		try //try the confirm code first or send an error
		{
			$this->login->check_reset_confirmation($this->data['code'], TBL_MEMBERS);
		} catch (Exception $e)
		{
			log_error('error', $e->getMessage());
		}

		if ($this->input->post())
		{
			$row = $this->login->validate_reset_confirm($this->input->post(NULL, TRUE), TBL_MEMBERS);

			if (!empty($row['success']))
			{
				//set the default response
				$response = array('type'     => 'success',
				                  'redirect' => site_url('login'),
				);

				$this->session->set_flashdata('success', $row['msg_text']);


				ajax_response($response);
			}
			else
			{
				$this->data['error'] = validation_errors();
			}
		}

		$this->show->display('form', 'reset_password', $this->data);
	}

	// ------------------------------------------------------------------------

	public function social($provider = '')
	{
		$this->data = $this->init->initialize('external');

		//set the page redirect first
		if ($this->input->get('redirect'))
		{
			$this->session->set_flashdata('redirect', $this->input->get('redirect'));
		}

		try
		{
			$this->load->library('HybridAuthLib', generate_social_config());

			if ($this->hybridauthlib->providerEnabled($provider))
			{
				log_message('debug', __METHOD__ . ' - service ' . $provider . ' enabled, trying to authenticate');
				$service = $this->hybridauthlib->authenticate($provider);

				if ($service->isUserConnected())
				{
					log_message('debug', __METHOD__ . ' - user authenticated.');

					$user = $service->getUserProfile();

					$this->session->set_flashdata('social_profile', $user);

					//check if the user has an email
					if (!$row = $this->login->validate_social($user, $this->input->post('email')))
					{
						$this->data['user'] = $user;

						//redirect to an email form
						$this->show->display('form', 'login_social', $this->data);
					}
					else
					{
						$row = $this->login->process_social(get_object_vars($row), $provider);

						if (!empty($row['success']))
						{
							//set json response
							$data = array('type'     => 'success',
							              'msg_text' => lang('user_logged_in_successfully'),
							              'redirect' => sess('redirect') ? sess('redirect') : site_url('login/process'));

							//remove ip block
							$this->sec->auto_block_ip('remove', $this->input->ip_address());

							//update cart data
							$this->cart->update_session_cart();

							$this->done(__METHOD__, $data, 'security');

							redirect_flashdata($data['redirect'], $data['msg_text'], $data['type']);
						}
						else
						{
							log_error('could_not_login_user - ' . $provider);
						}
					}
				}
				else // Cannot authenticate user
				{
					show_error('Cannot authenticate user');
				}
			}
			else // This service is not enabled.
			{
				log_error('error', 'This provider is not enabled (' . $provider . ')');
			}
		} catch (Exception $e)
		{
			$error = 'Unexpected error';
			switch ($e->getCode())
			{
				case 0 :
					$error = 'Unspecified error.';
					break;
				case 1 :
					$error = 'Hybridauth configuration error.';
					break;
				case 2 :
					$error = 'Provider not properly configured.';
					break;
				case 3 :
					$error = 'Unknown or disabled provider.';
					break;
				case 4 :
					$error = 'Missing provider application credentials.';
					break;
				case 5 :
					log_message('debug', 'controllers.HAuth.login: Authentification failed. The user has canceled the authentication or the provider refused the connection. ' . $e->getMessage());
					//redirect();
					if (isset($service))
					{
						log_message('debug', 'controllers.HAuth.login: logging out from service.');
						$service->logout();
					}
					show_error('User has cancelled the authentication or the provider refused the connection.');
					break;
				case 6 :
					$error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
					break;
				case 7 :
					$error = 'User not connected to the provider.';
					break;
			}

			if (ENVIRONMENT == 'development')
			{
				$error .= '<br />' . $e->getMessage();
			}

			if (isset($service))
			{
				$service->logout();
			}

			log_error('error', 'Error authenticating user.' . $error);
		}

	}

	// ------------------------------------------------------------------------

	public function endpoint()
	{
		$this->data = $this->init->initialize('external');

		log_message('debug', 'controllers.HAuth.endpoint called.');
		log_message('info', 'controllers.HAuth.endpoint: $_REQUEST: ' . print_r($_REQUEST, TRUE));

		if ($_SERVER['REQUEST_METHOD'] === 'GET')
		{
			log_message('debug', 'controllers.HAuth.endpoint: the request method is GET, copying REQUEST array into GET array.');
			$_GET = $_REQUEST;
		}

		log_message('debug', 'controllers.HAuth.endpoint: loading the original HybridAuth endpoint script.');
		require_once APPPATH . 'vendor/hybridauth/hybridauth/hybridauth/index.php';

	}

	// ------------------------------------------------------------------------

	public function reset_password()
	{
		$this->data = $this->init->initialize('site');

		if ($this->input->post('email'))
		{
			$row = $this->login->validate_pass_reset($this->input->post(NULL, TRUE), TBL_MEMBERS);

			if (!empty($row['success']))
			{
				//send out the email
				$this->mail->send_template(EMAIL_MEMBER_RESET_PASSWORD, $row['data'], FALSE, sess('default_lang_id'));
			}

			//set the default response
			$response = array('type'     => 'success',
			                  'redirect' => current_url(),
			);

			$this->session->set_flashdata('success', lang('reset_password_sent'));

			ajax_response($response);
		}

		$this->show->display('form', 'reset_password', $this->data);
	}

	// ------------------------------------------------------------------------

	public function admin()
	{
		$this->data = $this->init->initialize('site');

		$this->data['id'] = valid_id(uri(3));

		if ($row = $this->mem->get_details($this->data['id']))
		{
			if (generate_login_code($row) == uri(4))
			{
				$this->login->set_login_data($row, 'member');

				$this->plugin->init_plugin('Login::page', $this->session->userdata());
			}
		}

		$url = 'members';
		if (!empty(uri(5)))
		{
			$url = '/' . str_replace('-', '/', uri(5));
		}

		redirect_flashdata(site_url($url));
	}
}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */