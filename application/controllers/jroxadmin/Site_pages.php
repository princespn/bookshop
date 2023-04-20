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
class Site_pages extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'page');

		$this->load->helper('html_editor');

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

		$this->data['rows'] = $this->page->get_rows($this->data['page_options'], sess('default_lang_id'));

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
		$this->load->page('content/' . TPL_ADMIN_SITE_PAGES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->page->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->page->create($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(TBL_SITE_PAGES . '/update/' . $row['id']),
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
		$this->data['row'] = list_fields(array(TBL_SITE_PAGES, TBL_SITE_PAGES_NAME));

		//set the default data
		$this->data['row']['lang'] = set_default_create_data($this->data['row'], get_languages(FALSE, FALSE));
		$this->data['row']['sort_order'] = '1';
		$this->data['row']['date_formatted'] = display_date('', FALSE, 2, TRUE);

		//initialize the html editor...
		$this->data['meta_data'] = html_editor('head');

		//run the page
		$this->load->page('content/' . TPL_ADMIN_SITE_PAGES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->page->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->page->update($row['data']);

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

		//get the ID
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->page->get_details($this->data['id'], sess('default_lang_id'));

		//initialize the html editor...
		$this->data['meta_data'] = html_editor('head');

		if ($this->data['row']['type'] == 'builder')
		{
			$this->lc->check('site_builder');
		}

		//run the page
		$this->load->page('content/' . TPL_ADMIN_SITE_PAGES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id > 1)
		{
			$row = $this->dbv->delete(TBL_SITE_PAGES, 'page_id', $id);

			if (!empty($row['success']))
			{

				if (config_option('sts_site_builder_default_home_page') == $id)
				{
					$row = $this->set->update_db_settings(array('sts_site_builder_default_home_page' => '1'));
				}

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

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->page->mass_update($this->input->post());

			$this->dbv->db_sort_order(TBL_SITE_PAGES, 'page_id', 'sort_order');

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function general_search()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->page->search($this->data['page_options'], sess('default_lang_id'));

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
		$this->load->page('content/' . TPL_ADMIN_SITE_PAGES_VIEW, $this->data);
	}
}

/* End of file Site_pages.php */
/* Location: ./application/controllers/admin/Site_pages.php */