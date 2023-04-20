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
class Updates extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$m = array(__CLASS__ => 'updates',
		           'uploads' => 'up',
		           'backup'  => 'backup',
		);

		foreach ($m as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->dbforge();
		$this->load->config('backup');
		$this->load->config('updates');
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
		redirect_page($this->uri->uri_string() . '/check');
	}

	// ------------------------------------------------------------------------

	public function check_version()
	{
		$version = use_curl(config_item('check_version_url'));

		echo empty($version) ? lang('could_not_connect_to_server') : $version;
	}

	// ------------------------------------------------------------------------

	public function check()
	{
		$this->data['latest_version'] = use_curl(config_item('check_version_url'));

		if ($this->input->post())
		{
			//check if the form submitted is correct and update the path file
			if ($this->set->update_db_settings($this->input->post()))
			{
				//set the default response
				$response = array('type'     => 'success',
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/run/'),
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => lang('could_not_update_system'),
				);
			}

			ajax_response($response);
		}

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_UPDATES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function run()
	{
		if ($this->input->post())
		{
			//download latest update file from server
			$update_file = $this->updates->get_updates($this->input->post('path'));

			if (!empty($update_file) && file_exists($update_file))
			{
				if (is_file_type($update_file, 'zip'))
				{
					//backup the files first...
					if (config_enabled('backup_files_during_update'))
					{
						$this->backup->backup_files();
					}

					$row = $this->up->unzip($update_file, PUBPATH);

					if (!empty($row['success']))
					{
						//delete the old file
						@unlink($update_file);

						$this->done(__METHOD__, $row);

						$this->session->set_flashdata('data', $row['msg_text']);

						//set the default response
						$response = array('type'     => 'success',
						                  'msg'      => $row['msg_text'],
						                  'redirect' => admin_url(strtolower(__CLASS__) . '/view_results/'),
						);
					}
				}
			}

			//run database schema updates if any
			$row = $this->updates->run_db_updates($this->data);

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('data', lang('system_updated_successfully'));

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => lang('system_updated_successfully'),
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/view_results/'),
				);
			}

			if (!empty($row['success']))
			{
				//delete update files and sql updates
				$this->set->update_db_settings(array('sts_update_file_path' => ''));
			}

			if (empty($response))
			{
				$response = array('type' => 'error',
				                  'msg'  => is_var($row, 'msg_text', FALSE, lang('no_updates_applied')),
				);
			}

			ajax_response($response);
		}

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_UPDATES_RUN, $this->data);
	}

	// ------------------------------------------------------------------------

	public function view_results()
	{

		if ($this->session->flashdata('data'))
		{
			$this->data['results'] = $this->session->flashdata('data');

			//run the page
			$this->load->page('settings/' . TPL_ADMIN_UPDATES_RUN, $this->data);
		}
		else
		{
			redirect(admin_url() . 'updates');
		}
	}

	// ------------------------------------------------------------------------

	public function utilities()
	{
		$a = (int)uri(5);

		switch (uri(4))
		{
			case 'amount_point':

				$row = $this->updates->change_decimal_types($a);

				break;
		}

		if (!empty($row[ 'success' ]))
		{
			//log it!
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/settings' . '/view', $row[ 'msg_text' ]);

	}

	// ------------------------------------------------------------------------

	public function upload()
	{

		//check for file uploads
		$files = $this->up->validate_uploads('updates');

		if (!empty($files['success']))
		{
			//set json response
			$response = array('type'      => 'success',
			                  'file_name' => $files['file_data']['full_path'],
			                  'msg'       => lang('file_uploaded_successfully') . ' . ' . lang('please_proceed'),
			);

			//log it!
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('file_uploaded_successfully')));
		}
		else
		{
			//error!
			$response = array('type' => 'error',
			                  'msg'  => $files['msg'],
			);
		}


		//send the response via ajax
		ajax_response($response);
	}
}

/* End of file Updates.php */
/* Location: ./application/controllers/admin/Updates.php */