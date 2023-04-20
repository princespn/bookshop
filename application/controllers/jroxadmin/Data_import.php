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
class Data_import extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'import');
		$this->load->model('uploads_model', 'up');

		$this->config->set_item('menu', 'import_export');

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
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->import->get_rows($this->data['page_options']);

		//run the page
		$this->load->page('import_export/' . TPL_ADMIN_DATA_IMPORT_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('data_import', $this->data['row']['module']['module_folder']);

		//use module_row array to store all module data for editing in the view
		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->$module->validate_import_module($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->$module->update_module($row['data']);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/import/' . $this->data['id']),
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

		$this->load->page($this->config->item('module_config_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function view_results()
	{

		$this->data['id'] = (int)uri(4);

		$this->data['sub_headline'] = 'view_import_results';
		$this->data['title'] = lang('import_results');

		$this->data['results'] = sess('data');

		$this->load->page('settings/' . TPL_ADMIN_RESULTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function import()
	{

		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('data_import', $this->data['row']['module']['module_folder']);

		//use module_row array to store all module data for editing in the view
		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			$row = $this->$module->do_import($this->input->post());

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('data', $row['data']);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/view_results/' . $this->data['id'])
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
		else
		{
			//generate and map table fields to the file fields
			$this->data['row'] = $this->$module->generate_fields();

			$this->load->page($this->config->item('module_map_fields_template'), $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function upload()
	{

		//check for file uploads
		$files = $this->up->validate_uploads('data_import');

		if (!empty($files['success']))
		{
			//set json response
			$response = array('type'      => 'success',
			                  'file_name' => $this->config->slash_item('sts_data_import_folder') . $files['file_data']['file_name'],
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

/* End of file Data_import.php */
/* Location: ./application/controllers/admin/Data_import.php */