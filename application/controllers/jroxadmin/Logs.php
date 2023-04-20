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
class Logs extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->config->set_item('menu', 'locale');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/update');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->dbv->get_rows($this->data['page_options'], TBL_TRANSACTIONS);

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_TRANSACTIONS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function reset()
	{
		$row = $this->dbv->reset_data(TBL_TRANSACTIONS);

		if (!empty($row['success']))
		{
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}
}

/* End of file Logs.php */
/* Location: ./application/controllers/admin/Logs.php */