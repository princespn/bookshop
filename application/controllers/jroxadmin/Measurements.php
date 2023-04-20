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
class Measurements extends Admin_Controller
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

	public function update()
	{
		//get statuses
		$this->data['measurements'] = $this->dbv->get_all(TBL_MEASUREMENTS, 'sort_order');
		$this->data['weight'] = $this->dbv->get_all(TBL_WEIGHT, 'sort_order');

		if ($this->input->post())
		{
			if ($this->input->post('type') == 'weight')
			{
				$row = $this->weight->update($this->input->post(), $this->data['weight']);
			}
			elseif ($this->input->post('type') == 'measurement')
			{
				$row = $this->measure->update($this->input->post(), $this->data['measurements']);
			}

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg' => $row['msg_text']
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => $row['msg_text'],
				);
			}

			ajax_response($response);
		}

		$this->load->page('localization/' . TPL_ADMIN_MEASUREMENTS_MANAGE, $this->data);
	}
}

/* End of file Measurements.php */
/* Location: ./application/controllers/admin/Measurements.php */