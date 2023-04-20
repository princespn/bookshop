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
class Update_status extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	function table()
	{
		$vars = $this->uri->uri_to_assoc();

		if (count($vars) < 4)
		{
			show_error(lang('invalid_data'));
		}

		//check if the form submitted is correct
		$row = $this->dbv->update_status($vars);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			$page = !$this->agent->referrer() ? admin_url(uri(3)) : $this->agent->referrer();

			redirect_flashdata($page, $row[ 'msg_text' ]);
		}
		else
		{
			//show errors on form
			log_error('error', lang('could_not_update_system'));
		}
	}

	// ------------------------------------------------------------------------

	public function settings()
	{
		$setting = url_title(uri(4));

		$option = config_item($setting) == '0' ? '1' : '0';

		$this->set->update_db_settings(array($setting => $option));

		$url = !$this->agent->referrer() ? admin_url(uri(2) . '/'  . uri(3)) : $this->agent->referrer();

		redirect_page($url);
	}
}

/* End of file Update_status.php */
/* Location: ./application/controllers/admin/Update_status.php */