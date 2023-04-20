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
class Tracking extends Public_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('site');

		$this->load->model(__CLASS__ . '_model', 'track');
	}

	// ------------------------------------------------------------------------

	public function id()
	{
		$this->data['id'] = valid_id(uri(2));

		$page = site_url();

		//add to tracking table
		if ($row = $this->track->get_details($this->data['id']))
		{
			//record the referral
			$this->track->insert_referral($row);

			$page = $row['url'];

			//check for expired link
			if (empty($row['status']) || strtotime($row['end_date']) < get_time(now()))
			{
				redirect_page($row['expired_url']);
			}
			else
			{
				//if there is a member ID set tracking cookie too
				if (!empty($row['member_id']))
				{
					//set output headers
					set_headers('affiliate');

					//set cookie
					$row = $this->aff->set_tracking_data($row['username'], TRUE);

					if (!empty($row))
					{
						$this->session->set_userdata('tracking_data', $row);
					}
				}
			}
		}

		redirect_page($page);
	}
}

/* End of file Tracking.php */
/* Location: ./application/controllers/Tracking.php */