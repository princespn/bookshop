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
class Languages extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('uploads_model', 'up');

		$this->config->set_item('menu', 'locale');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */

	// ------------------------------------------------------------------------
	
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------
	
	public function view()
	{
		$this->data['rows'] = $this->dbv->get_rows(array(), TBL_LANGUAGES);

		//run the page
		$this->load->page('localization/' . TPL_ADMIN_LANGUAGES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->dbv->validate(TBL_LANGUAGES, TBL_LANGUAGES, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->language->create($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(TBL_LANGUAGES . '/update_entries/' . $row['id']),
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

		$this->data['row'] = set_default_form_values(array(TBL_LANGUAGES));

		$this->data['languages'] = format_lang_folders($this->language->get_rows(), FALSE);

		$this->data['flags'] = get_flags();

		$this->data['meta_data'] .= '<script src="' . base_url('js/select2/select2.min.js') . '"></script>';

		//run the page
		$this->load->page('localization/' . TPL_ADMIN_LANGUAGES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function map_custom_entries()
	{
		$this->data['id'] = valid_id(uri(4));

		$row = $this->language->map_custom_entries($this->data['id']);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update_custom_entries/' . $this->data['id'] . '/?file=custom', $row['msg_text']);
	}

	// ------------------------------------------------------------------------
	
	public function update_entries()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['file'] = $this->input->get('file');

		//get language details
		$this->data['row'] = $this->dbv->get_record(TBL_LANGUAGES, 'language_id', $this->data['id']);

		$this->data['lang_files'] = $this->language->get_files($this->data['row']['name']);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->language->update_entries($this->input->post(NULL, TRUE), $this->data['row']['name'], $this->input->post('file'));

			if (!empty($row['success']))
			{
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

			ajax_response($response);
		}

		//check if we're doing a search
		if ($this->input->get('term'))
		{
			$this->data['lang_entries'] = $this->language->search($this->data['row']['name'], $this->input->get('term', TRUE));
		}
		else
		{
			if ($this->input->get('file'))
			{
				$this->data['lang_entries'] = $this->language->get_language_entries($this->data['row']['name'], $this->input->get('file', TRUE) . '_lang.php');
			}
		}

		$this->data['lang_custom_entries'] = $this->language->get_custom_entries($this->data['id'], TRUE);

		$this->load->page('localization/' . TPL_ADMIN_LANGUAGES_ENTRIES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id != config_item('sts_site_default_language'))
		{
			$row = $this->dbv->delete(TBL_LANGUAGES, 'language_id', $id);

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
			}
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------
	
	public function reset()
	{
		$id = valid_id(uri(4));

		$row = $this->language->reset_db();

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}
		else
		{
			show_error(lang($row['msg_text']));
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update_entries/' . $id . '?file=admin', $row['msg_text']);
	}

	// ------------------------------------------------------------------------
	
	public function unzip()
	{

		$msg = '';

		if ($this->input->post())
		{
			$row = $this->up->unzip($this->input->post('zip_file'), APPPATH . 'language/');

			if (!empty($row['success']))
			{
				$msg = lang('file_unzipped_successfully');

				$this->done(__METHOD__, $row);
			}
			else
			{
				show_error(lang($row['msg_text']));
			}
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/create/', $msg);
	}

	// ------------------------------------------------------------------------
	
	public function upload()
	{

		//check for file uploads
		$files = $this->up->validate_uploads('languages');

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

	/**
	 * Mass update records via checkboxes
	 */

	// ------------------------------------------------------------------------
	
	public function mass_update()
	{
		if ($this->input->post('languages'))
		{
			//update product data first
			$this->language->mass_update($this->input->post('languages', TRUE));
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------
	
	public function create_custom_entry()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post('key'))
		{
			$row = $this->language->create_key(url_title($this->input->post('key')), $this->data['id']);
		}

		//set the session flash and redirect the pageupdate_custom_entries/1/?file=custom
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update_custom_entries/' . $this->data['id'] . '?file=custom', $row['msg_text']);
	}

	// ------------------------------------------------------------------------
	
	public function update_custom_entries()
	{
		$this->data['id'] = valid_id(uri(4));

		//get language details
		if (!$row = $this->dbv->get_record(TBL_LANGUAGES, 'language_id', $this->data['id']))
		{
			log_error('error', lang('no_record_found'));
		}
		else
		{
			$this->data['row'] = $row;
		}

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->language->update_entries($this->input->post(NULL, TRUE), $this->data['row']['name']);

			if (!empty($row['success']))
			{
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

			ajax_response($response);
		}

		$this->data['file'] = $this->input->get('file');

		$this->data['lang_entries'] = $this->language->get_custom_entries($this->data['id'], TRUE, 'custom');

		$this->data['lang_custom_entries'] = $this->language->get_custom_entries($this->data['id'], TRUE);

		$this->data['lang_files'] = $this->language->get_files($this->data['row']['name']);

		//run the page
		$this->load->page('localization/' . TPL_ADMIN_LANGUAGES_ENTRIES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete_entry()
	{
		$id = valid_id(uri(4), TRUE);

		$row = $this->dbv->delete(TBL_LANGUAGE_ENTRIES, 'key', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update_custom_entries/' . valid_id(uri(5)) . '?file=custom', $row['msg_text']);
		}
		else
		{
			show_error(lang('could_not_delete_record'));
		}
	}
}

/* End of file Languages.php */
/* Location: ./application/controllers/admin/Languages.php */