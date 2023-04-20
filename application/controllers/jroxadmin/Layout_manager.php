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
class Layout_manager extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('templates_model', 'tpl');
		$this->load->model('widgets_model', 'w');

		$this->config->set_item('menu', 'design');
		$this->config->set_item('sub_menu', 'layout');

		$this->load->helper('html_editor');

		if (!class_exists('DOMDocument'))
		{
			log_error('error', lang('php_domdocument_required'));
		}

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/config');
	}

	// ------------------------------------------------------------------------

	public function config()
	{
		if ($this->input->post())
		{
			if ($row = $this->set->validate_settings($this->input->post(), 'layout'))
			{
				if (!empty($row['success']))
				{
					$row = $this->set->update_db_settings($row['data']);

					$this->done(__METHOD__, $row);

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
			}

			ajax_response($response);
		}

		//initialize the html editor...
		$this->data[ 'meta_data' ] = html_editor('head');
		
		$this->load->page('design/' . TPL_ADMIN_SITE_LAYOUT_VIEW, $this->data);
	}
}

/* End of file Layout_manager.php */
/* Location: ./application/controllers/admin/Layout_manager.php */