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
class Regions extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'region');
		$this->load->model('countries_model', 'country');
		$this->load->helper('regions');

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

		$this->data['country_id'] = $this->input->get('country_id');

		$this->data['rows'] = $this->region->get_rows($this->data['page_options']);

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

		$this->data['title'] = get_region_title();
		//run the page
		$this->load->page('localization/' . TPL_ADMIN_REGIONS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data['row'] = list_fields(array(TBL_REGIONS));

		$this->data['row']['region_country_id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_REGIONS, TBL_REGIONS, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->create(TBL_REGIONS, $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/update/' . $row['id'] ),
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

		//get country data
		$this->data['country'] = $this->dbv->get_record(TBL_COUNTRIES, 'country_id', $this->data['row']['region_country_id']);

		$this->load->page('localization/' . TPL_ADMIN_REGIONS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->region->get_details($this->data['id']);

		//get country data
		$this->data['country'] = $this->dbv->get_record(TBL_COUNTRIES, 'country_id', $this->data['row']['region_country_id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_REGIONS, TBL_REGIONS, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_REGIONS, 'region_id', $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
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

		$this->load->page('localization/' . TPL_ADMIN_REGIONS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_REGIONS, 'region_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/?country_id=' . uri(5), $row['msg_text']);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('region'))
		{
			//update product data first
			$this->region->mass_update($this->input->post());
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	public function load_regions()
	{
		$term = $this->uri->segment(4, 'state');

		$all_regions = $this->uri->segment(5) == 'all_regions' ? TRUE : FALSE;

		if ($id = (int)$this->input->post_get('country_id'))
		{
			$rows = $this->region->load_country_regions($id, TRUE, $all_regions);

			echo form_dropdown($term, $rows, '', 'id="region_select" class="s2 select2 form-control"');
		}
	}
}

/* End of file Regions.php */
/* Location: ./application/controllers/admin/Regions.php */