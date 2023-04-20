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

class Utilities extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$m = array(__CLASS__ => 'ut',
		           'updates' => 'updates',
		           'uploads' => 'up',
		           'backup'  => 'backup',
		);

		foreach ($m as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->config('backup');
		$this->load->helper('directory');
		$this->load->library('zip');
		$this->load->helper('download');

		$this->config->set_item('menu', 'system');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//run the page
		$this->load->page('settings/' . TPL_ADMIN_UTILITIES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function amount_point()
	{
		$a = (int)uri(4);

		$row = $this->updates->change_decimal_types($a);

		if (!empty($row['success']))
		{
			//log it!
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/utilities/view', $row[ 'msg_text' ]);

	}
}

/* End of file Utilities.php */
/* Location: ./application/controllers/admin/Utilities.php */