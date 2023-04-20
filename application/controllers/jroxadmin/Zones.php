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
class Zones extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array(
			__CLASS__   => 'zones',
			'countries' => 'country',
			'regions'   => 'region',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

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
		$this->data['rows'] = $this->dbv->get_rows(array(), TBL_ZONES);

		//run the page
		$this->load->page('localization/' . TPL_ADMIN_ZONES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$row = $this->zones->create();

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
		$this->data['id'] = (int)$this->uri->segment(4);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->zones->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->zones->update($row['data']);

				$this->done(__METHOD__, $row);

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

		$this->data['row'] = $this->zones->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//get countries
		$this->data['countries'] = $this->country->load_countries_array(FALSE, TRUE, TRUE);

		$this->load->page('localization/' . TPL_ADMIN_ZONES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id > 1)
		{
			$row = $this->zones->delete($id);

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);
			}
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function search_zones()
	{
		$rows = $this->zones->ajax_search($this->input->get('zone_name', TRUE), TRUE);

		echo json_encode($rows);
	}

	// ------------------------------------------------------------------------

	public function search_regions()
	{
		$rows = $this->region->ajax_search($this->input->get('region_name', TRUE), TRUE);

		echo json_encode($rows);
	}
}

/* End of file Zones.php */
/* Location: ./application/controllers/admin/Zones.php */