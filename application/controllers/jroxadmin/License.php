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
class License extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->config->set_item('menu', 'system');

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

	public function reset()
	{
		$row = $this->lc->reset(TRUE);

		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		if ($this->input->post())
		{
			if (!$this->input->post('sts_site_key'))
			{
				$row = $this->lc->reset();

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/update/'),
				);
			}
			else
			{
				if ($row = $this->lc->validate($this->input->post()))
				{
					if (!empty($row['success']))
					{
						$row = $this->set->update_db_settings($row['data']);

						//set the default response
						$response = array('type'     => 'success',
						                  'msg'      => $row['msg_text'],
						                  'redirect' => admin_url(strtolower(__CLASS__) . '/update/'),
						);
					}
					else
					{
						$response = array('type' => 'error',
						                  'msg'  => $row['msg_text'],
						);
					}
				}
			}

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			ajax_response($response);
		}

		$this->data['license_data'] = !config_item('sts_site_license_data') ? '' : unserialize(config_item('sts_site_license_data'));
		$this->data['matrix_data'] = !config_item('sts_mx_license_data') ? '' : unserialize(config_item('sts_mx_license_data'));
		$this->data['copyright_data'] = !config_item('sts_copyright_license_data') ? '' : unserialize(config_item('sts_copyright_license_data'));

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_LICENSE, $this->data);
	}

}

/* End of file License.php */
/* Location: ./application/controllers/admin/License.php */