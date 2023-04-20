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
class Affiliate_downline extends Member_Controller
{
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	/**
	 * View downline members
	 *
	 * Get all referrals in the downline for the member
	 */
	public function view()
	{
		if (config_enabled('sts_affiliate_allow_downline_view') && sess('allow_downline_view'))
		{
			$cache = current_url() . $this->config->item('username', 'affiliate_data');

			if (!$row = $this->init->cache($cache, 'downline_db_query'))
			{
				//get rows
				$row = $this->downline->generate_downline(sess('member_id'));

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'downline_db_query');
			}

			$this->data['p'] = $row;


			$this->show->display(MEMBERS_ROUTE, CONTROLLER_CLASS, $this->data);
		}
		else
		{
			show_error(lang('this_requires_valid_network_marketing_license'));
		}
	}
}

/* End of file Invoices.php */
/* Location: ./application/controllers/members/Invoices.php */