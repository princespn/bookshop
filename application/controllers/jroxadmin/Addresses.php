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
class Addresses extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('settings');

		$this->load->model('cron_model', 'cron');

		$this->data = $this->init->initialize();

		$this->config->set_item('menu', 'system');
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
		$this->data['stores'] = $this->set->get_site_addresses();

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_SITE_ADDRESSES, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data['row'] = set_default_site_address_data();

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_SITE_ADDRESSES, TBL_SITE_ADDRESSES, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->create(TBL_SITE_ADDRESSES, $row['data']);

				//log it!
				$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => (admin_url(strtolower(__CLASS__) . '/update/' . $row['id'])),
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

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_SITE_ADDRESS_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			$row = $this->dbv->validate(TBL_SITE_ADDRESSES, TBL_SITE_ADDRESSES, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_SITE_ADDRESSES, 'id', $row['data']);

				//log it!
				$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
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

		$this->data['row'] = $this->set->get_site_address($this->data['id']);

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_SITE_ADDRESS_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_SITE_ADDRESSES, 'id', $id);

		if (config_item('sts_site_default_address') == $id)
		{
			$row = $this->set->update_db_settings(array('sts_site_default_address' => config_item('default_site_address')));
		}

		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/', $row['msg_text']);

	}
}

/* End of file Addresses.php */
/* Location: ./application/controllers/admin/Addresses.php */