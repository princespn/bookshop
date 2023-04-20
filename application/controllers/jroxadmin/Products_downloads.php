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
class Products_downloads extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array(
			__CLASS__ => 'download',
			'uploads' => 'up',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('products');

		$this->config->set_item('menu', 'content');

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

		$this->data['rows'] = $this->download->get_rows($this->data['page_options'], sess('default_lang_id'));

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
		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_DOWNLOADS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$row = $this->download->create($this->language->get_languages());

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row['msg_text']);
		}
		else
		{
			show_error(lang('could_not_create_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//set the rule ID
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->download->validate($this->input->post());

			if (!empty($row['success']))
			{

				$row = $this->download->update($row['data']);

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

		$this->data['row'] = $this->download->get_details($this->data['id'], sess('default_lang_id'));

		//get languages
		$this->data['languages'] = get_languages(FALSE, FALSE);

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_DOWNLOADS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_PRODUCTS_DOWNLOADS, 'download_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function upload()
	{

		//check for file uploads
		$files = $this->up->validate_uploads('downloads');

		if (!empty($files['success']))
		{
			//set json response
			$response = array('type'      => 'success',
			                  'file_name' => $files['file_data']['file_name'],
			                  'msg'       => lang('file_uploaded_successfully'),
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

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('download_name', TRUE);

		$rows = $this->download->ajax_search($term, sess('default_lang_id'));

		echo json_encode($rows);
	}
}

/* End of file Products_downloads.php */
/* Location: ./application/controllers/admin/Products_downloads.php */