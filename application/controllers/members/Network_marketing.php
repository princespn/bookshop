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
class Network_marketing extends Member_Controller
{
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize('site');

		if (!config_enabled('layout_enable_forced_matrix'))
		{
			show_error(lang('license_required'));
		}

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/email');
	}

	// ------------------------------------------------------------------------

	public function email()
	{
		if (config_enabled('sts_affiliate_allow_downline_email') && sess('allow_downline_email'))
		{
			if ($this->input->post())
			{
				if ($this->sec->verify_ownership())
				{
					//check if the form submitted is correct
					$row = $this->downline->validate_email($this->input->post());

					//all good...
					if (!empty($row['success']))
					{
						//get users downline
						$users = $this->downline->generate_downline(sess('member_id'), FALSE, 'array');

						if (!empty($users['results']))
						{
							$row = $this->mail->send_downline_email($row['data'], $users['results']);

							if (!empty($row['success']))
							{
								$this->sec->check_flood_control('downline_email', 'add', sess('member_id'));

								//set json response
								$response = array('type' => 'success',
								                  'msg'  => $row['msg_text'],
								                  'redirect' => current_url(),
								);

								$this->done(__METHOD__, $row);
							}
						}
						else
						{
							//set json response
							$response = array('type' => 'error',
							                  'msg'  => lang('no_users_in_downline'),
							);
						}
					}
					else
					{
						//errors!
						$response = array('type'         => 'error',
						                  'msg'          => $row['msg_text']);

						//log errors in db
						$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('error_updating_record'), 'level' => 'error'));

					}

					//send the response via ajax
					ajax_response($response);
				}
			}

			$this->show->display(MEMBERS_ROUTE, 'mass_email', $this->data);
		}
		else
		{
			show_error(lang('invalid_access'));
		}
	}
}

/* End of file Invoices.php */
/* Location: ./application/controllers/members/Invoices.php */