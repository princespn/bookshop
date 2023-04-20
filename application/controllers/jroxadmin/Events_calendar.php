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
class Events_calendar extends Admin_Controller
{
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'events');

		$this->config->set_item('menu', 'promotions');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		$this->data['y'] = uri(4, date('Y', get_time()));
		$this->data['m'] = uri(5, date('m', get_time()));
		$this->data['d'] = uri(6, date('d', get_time()));

		//check the cache
		if (!$rows = $this->init->cache(current_url(), 'admin'))
		{
			$rows = $this->events->get_rows($this->data['m'], $this->data['y']);

			// Save into the cache
			$this->init->save_cache(__METHOD__, current_url(), $rows, 'admin');
		}

		init_calendar();

		$this->data['calendar'] = $this->calendar->generate($this->data['y'], $this->data['m'], $rows);

		//run the page
		$this->load->page('promotions/' . TPL_ADMIN_EVENTS_CALENDAR_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function events()
	{
		$this->data['y'] = uri(4, date('Y'));
		$this->data['m'] = uri(5, date('m'));
		$this->data['d'] = uri(6, date('d'));

		$this->data['current_day'] = current_date('l, ' . $this->data['format_date3'], $this->data['m'], $this->data['d'], $this->data['y']);

		$this->data['events'] = $this->events->get_daily_events($this->data['y'], $this->data['m'], $this->data['d']);

		$this->load->page('promotions/' . TPL_ADMIN_EVENTS_CALENDAR_EVENTS, $this->data, 'admin', FALSE, FALSE);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->events->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->events->create($row['data']);

				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('success', $row['msg_text']);

				//set the default response
				$response = array('type'     => 'success',
				                  'redirect' => (admin_url(strtolower(__CLASS__) . '/update/' . $row['id'])),
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
		$this->data['row'] = list_fields(array(TBL_EVENTS_CALENDAR));
		$this->data['row']['date'] = event_date();

		$a = array('start_hour', 'start_min', 'start_ampm','end_hour', 'end_min', 'end_ampm');

		foreach ($a as $v)
		{
			$this->data['row'][$v] = '';
		}

		$this->load->page('promotions/' . TPL_ADMIN_EVENTS_CALENDAR_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->events->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->events->update($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
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

		$this->data['row'] = $this->events->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('promotions/' . TPL_ADMIN_EVENTS_CALENDAR_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_EVENTS_CALENDAR, 'id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/' . uri(5) . '/' . uri(6) . '/' . uri(7), $row[ 'msg_text' ]);
	}
}

/* End of file Events_calendar.php */
/* Location: ./application/controllers/admin/Events_calendar.php */