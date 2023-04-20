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
class Email extends Public_Controller
{
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ .'_model', 'email');
		$this->load->model('email_mailing_lists_model', 'list');

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function subscriptions()
	{
		//set breadcrumbs
		$this->data['breadcrumb'] = set_breadcrumb(array(lang('email')                 => 'email',
		                                               lang('subscriptions') => '',
		));

		$this->sec->check_system_key(uri(3));

		$this->data['key'] = uri(3);
		$this->data['email'] = valid_id(uri(4), 'primary_email');

		if (uri(5))
		{
			//get list detail
			if ($this->list->remove_user(valid_id(uri(5)), $this->data['email']))
			{
				$msg  = 'email_unsubscribed_successfully';

				//set the session flash and redirect the page
				redirect_flashdata(site_url('email/subscriptions/' . uri(3) . '/' . $this->data['email']), $msg);
			}
		}

		$this->data['lists'] = $this->list->get_user_subscriptions($this->data['email']);

		$this->show->display('form', 'member_mailing_lists', $this->data);
	}
}

/* End of file Email.php */
/* Location: ./application/controllers/Email.php */