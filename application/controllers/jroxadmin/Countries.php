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
class Countries extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'country');
		$this->load->helper('country');

		$this->config->set_item('menu', 'locale');
		$this->config->set_item('sub_menu', 'zones');

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

		$this->data['rows'] = $this->dbv->get_rows($this->data['page_options'], TBL_COUNTRIES);

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
		$this->load->page('localization/' . TPL_ADMIN_COUNTRIES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_COUNTRIES, TBL_COUNTRIES, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->create(TBL_COUNTRIES, $row['data']);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				$page = !$this->input->post('redir_button') ? 'view' : 'create';

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/' . $page)
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
		$this->data['row'] = list_fields(array(TBL_COUNTRIES));
		$this->data['row']['sort_order'] = '1';

		$this->load->page('localization/' . TPL_ADMIN_COUNTRIES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_COUNTRIES, TBL_COUNTRIES, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_COUNTRIES, 'country_id', $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text']
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

		$this->data['row'] = $this->dbv->get_record(TBL_COUNTRIES, 'country_id', $this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}
		
		$this->load->page('localization/' . TPL_ADMIN_COUNTRIES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_COUNTRIES, 'country_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/', $row['msg_text']);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('country'))
		{
			//update product data first
			$this->country->mass_update($this->input->post());
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------

	public function search_countries()
	{
		$term = $this->input->get('country_name', TRUE);

		$all = uri(4) == 'all_regions' ? TRUE : FALSE;

		$rows = $this->country->ajax_search($term, $all);

		echo json_encode($rows);
	}
}

/* End of file Countries.php */
/* Location: ./application/controllers/admin/Countries.php */