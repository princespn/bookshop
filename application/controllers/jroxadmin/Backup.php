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
class Backup extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('backup_model', 'backup');

		$this->load->library('zip');
		$this->load->helper('download');
		$this->load->helper('settings');

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
		$this->data['db'] = $this->backup->get_backups('db');
		$this->data['files'] = $this->backup->get_backups('files');

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_BACKUPS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function backup_db()
	{
		$type = uri(4);

		//we'll try and set a higher memory limit if possible...
		@ini_set("memory_limit", BACKUP_MEMORY_LIMIT);

		$path = !$this->input->post('backup_path') ? BACKUP_FILES_PATH : $this->input->post('backup_path');
		$row = $type == 'file' ? $this->backup->backup_files($path) : $this->backup->backup_db();

		if (!empty($row['success']))
		{
			//log it!
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			//set the default response
			$response = array('type'     => 'success',
			                  'msg'      => $row['msg_text'],
			                  'redirect' => admin_url(strtolower(__CLASS__) . '/view'),
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

	// ------------------------------------------------------------------------
	
	public function restore_db()
	{
		$this->data['id'] = valid_id(uri(4), TRUE);

		if ($this->input->post('file'))
		{
			if ($row = $this->backup->restore_db($this->input->post('file')))
			{
				if (!empty($row['success']))
				{
					//log it!
					$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

					//set the default response
					$response = array('type'     => 'success',
					                  'msg'      => $row['msg_text'],
					                  'redirect' => admin_url(strtolower(__CLASS__) . '/view'),
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

		$this->load->page('settings/' . TBL_ADMIN_RESTORE_DATABASE, $this->data, 'admin');
	}

	// ------------------------------------------------------------------------
	
	public function download_archive()
	{
		//we'll try and set a higher memory limit if possible...
		@ini_set("memory_limit", BACKUP_MEMORY_LIMIT);

		$this->data['id'] = valid_id(uri(4), TRUE);

		download_file($this->data['id'], 'backup_archive');
	}
}

/* End of file Backup.php */
/* Location: ./application/controllers/admin/Backup.php */