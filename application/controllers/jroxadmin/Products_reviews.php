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
class Products_reviews extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'rev');
		$this->load->helper('products');


		$this->config->set_item('menu', TBL_PRODUCTS);

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

		//check the cache
		if (!$rows = $this->init->cache(current_url(), 'admin'))
		{
			$rows = $this->rev->get_rows($this->data['page_options'], '', FALSE, sess('default_lang_id'));

			// Save into the cache
			$this->init->save_cache(__METHOD__, current_url(), $rows, 'admin');
		}

		$this->data['rows'] = $rows;

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
		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_REVIEWS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->rev->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->rev->create($row['data']);

				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('success', $row['msg_text']);

				//set the default response
				$url = $this->input->post('redir_button') ? admin_url(CONTROLLER_CLASS . '/create/') : admin_url(CONTROLLER_CLASS . '/update/' . $row['data']['id']);
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => $url,
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

		//fill in default values for input fields
		$this->data['row'] = list_fields(array(TBL_PRODUCTS_REVIEWS));

		$this->data['row']['date_formatted'] = display_date(get_time() - 86400, FALSE, 2, TRUE);
		$this->data['row']['username'] = '';
		$this->data['row']['product_name'] = '';

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_REVIEWS_VIEW_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->rev->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->rev->update($row['data']);

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

		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->rev->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_REVIEWS_VIEW_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->rev->delete($id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
		else
		{
			log_error('error', $row['msg_text']);
		}
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->rev->mass_update($this->input->post());

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}
}

/* End of file Products_reviews.php */
/* Location: ./application/controllers/admin/Products_reviews.php */