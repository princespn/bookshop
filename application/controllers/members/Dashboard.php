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
class Dashboard extends Member_Controller
{
	protected $data;

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model('dashboard_model', 'dash');

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{

		//get dashboard icons if any
		$this->data['icons'] = $this->dash->load_icons();

		$this->data['breadcrumb'] = set_breadcrumb(array(lang('members')   => 'members',
		                                                 lang('dashboard') => '',
		));

		$this->data['invoices'] = $this->dash->get_invoices(sess('member_id'));

		if (config_enabled('sts_support_enable'))
		{
			$this->data['tickets'] = $this->dash->get_tickets(sess('member_id'));
		}

		//member dashboard template
		$tpl = empty($this->data['layout_members_dashboard_template']) ? 'dashboard_default'
			: $this->data['layout_members_dashboard_template'];

		$this->show->display(MEMBERS_ROUTE, $tpl, $this->data);
	}

	// ------------------------------------------------------------------------

	public function activate_affiliate()
	{
		$msg = '';

		if (config_enabled('sts_affiliate_admin_approval_required'))
		{
			if ($admins = get_admins()) //get active admins to send alerts to.
			{
				foreach ($admins as $a)
				{
					if (!empty($a['alert_affiliate_signup']))
					{
						$msg = $this->mail->send_template(EMAIL_ADMIN_AFFILIATE_MARKETING_ACTIVATION, format_affiliate_activation_email($_SESSION, $a), TRUE, sess('default_lang_id'), $a['primary_email']);
					}
				}

				$msg = lang('admin_approval_required');
			}
		}
		else
		{
			$row = $this->aff->activate_affiliate_account(sess('member_id'));

			$msg = $row['msg_text'];
		}

		redirect_flashdata(MEMBERS_ROUTE, $msg);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/members/Dashboard.php */