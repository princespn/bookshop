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
class Thank_you extends Checkout_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$this->load->model('login_model', 'login');

		log_message('debug', __CLASS__ . ' Class Initialized');

		$p = sess('post_payment_data');

		$this->data = $this->init->initialize('site');

		//now login the user if needed
		if (!empty($p['member']['member_id']))
		{
			$this->mem->login_checkout_user($p['member']['member_id']);
		}
	}

	// ------------------------------------------------------------------------

	public function page()
	{
		if (sess('post_payment_data'))
		{
			$this->data['order_data'] = sess('post_payment_data');

			$this->session->unset_userdata('post_payment_data');

			$this->data['breadcrumb'] = set_breadcrumb(array(lang('store')     => '',
			                                                 lang('thank_you') => '',
			));

			$this->show->display('checkout', 'thank_you', $this->data);
		}
		else
		{
			redirect_page(MEMBERS_ROUTE);
		}
	}

	// ------------------------------------------------------------------------

	public function gift_certificates()
	{
		if (sess('post_payment_data'))
		{
			$this->data['order_data'] = sess('post_payment_data');
		}
		else
		{
			redirect('members');
		}

		//send and update the gift certificates....
		if ($this->input->post())
		{
			//queue and send the certificate template
			foreach ($this->input->post('cert') as $k => $v)
			{
				foreach ($this->data['order_data']['gift_certificates'] as $p)
				{
					if ($p['cert_id'] == $k)
					{
						//update cert id
						$data = format_gift_certificate_template($p, $v);
						$row = $this->dbv->update(TBL_ORDERS_GIFT_CERTIFICATES, 'cert_id', $data);

						if (!empty($row['success']))
						{
							$this->mail->send_template(EMAIL_MEMBER_GIFT_CERTIFICATE_DETAILS, $data, TRUE, sess('default_lang_id'), $v['to_email']);
						}
					}
				}
			}

			//set the default response
			$response = array('type'     => 'success',
			                  'msg'      => lang('email_sent_successfully'),
			                  'redirect' => site_url('thank_you'),
			);

			ajax_response($response);

		}

		$this->show->display('checkout', 'send_gift_certificates', $this->data);
	}
}

/* End of file Thank_you.php */
/* Location: ./application/controllers/Thank_you.php */