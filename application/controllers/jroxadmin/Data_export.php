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
class Data_export extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'export');

		$this->load->helper('download');
		$this->load->helper('file');
		$this->load->helper('directory');

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

		$this->data['rows'] = $this->export->get_rows($this->data['page_options']);

		//run the page
		$this->load->page('import_export/' . TPL_ADMIN_DATA_EXPORT_VIEW, $this->data);
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
		$this->init_module('data_export', $this->data['row']['module']['module_folder']);

		//use module_row array to store all module data for editing in the view
		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->$module->validate_export_module($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->$module->update_module($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/generate/' . $this->data['id']),
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

		//get some archives if any...
		$this->data['archive'] = $this->$module->get_archive();

		$this->load->page($this->config->item('module_config_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{

		$this->data['id'] = valid_id(uri(4), TRUE);

		$file = config_option('sts_data_import_folder') . '/' . $this->data['id'];

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		if (file_exists($file) && @unlink($file))
		{
			redirect_flashdata($url, 'system_updated_successfully');
		}
		else
		{
			redirect_flashdata($url, 'could_not_delete_file', 'error');
		}
	}

	// ------------------------------------------------------------------------

	public function download()
	{

		$this->data['id'] = valid_id(uri(4), TRUE);

		$file = config_option('sts_data_import_folder') . '/' . $this->data['id'];

		if (file_exists($file))
		{
			force_download($file, NULL);
		}
		else
		{
			show_error(lang('file_not_found'));
		}
	}
	public function generate()
	{

		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('data_export', $this->data['row']['module']['module_folder']);

		//use module_row array to store all module data for editing in the view
		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			$row = $this->$module->do_export($this->input->post());

			if (!empty($row['success']))
			{
				//write the file
				if (!write_file($this->config->slash_item('sts_data_import_folder') . $row['file_name'], $row['data']))
				{
					show_error(lang('could_not_save_import_file') . '. ' . lang('check_file_path'));
				}

				force_download($row['file_name'], $row['data']);
			}
			else
			{
				$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

				redirect_flashdata($url, 'no_records_found', 'error');
			}

		}
		else
		{
			$this->load->page($this->config->item('module_do_export_template'), $this->data);
		}
	}
}

/* End of file Data_export.php */
/* Location: ./application/controllers/admin/Data_export.php */