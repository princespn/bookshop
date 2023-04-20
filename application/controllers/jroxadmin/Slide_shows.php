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
class Slide_shows extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'slide');

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

		$this->data['rows'] = $this->slide->get_rows($this->data['page_options'], sess('default_lang_id'));

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

		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/js/jquery-ui.js') . '"></script>';

		//run the page
		$this->load->page('content/' . TPL_ADMIN_SLIDE_SHOWS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{

		$row = $this->slide->create($this->language->get_languages(), uri(4));

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

	public function update_order()
	{
		$this->slide->update_sort_order($this->input->get('formid', TRUE));
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//set the rule ID
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->slide->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->slide->update($row['data']);

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

		$this->data['row'] = $this->slide->get_details($this->data['id'], sess('default_lang_id'));

		//set header data
		$this->data['meta_data'] = html_editor('head');

		//get languages
		$this->data['languages'] = get_languages(FALSE, FALSE);

		$this->data['meta_data'] .= link_tag('themes/admin/default/third/minicolors/jquery.minicolors.css');
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/third/minicolors/jquery.minicolors.js') . '"></script>';

		$this->load->page('content/' . TPL_ADMIN_SLIDE_SHOWS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_SLIDE_SHOWS, 'slide_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->slide->mass_update($this->input->post());

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}
}

/* End of file Slide_shows.php */
/* Location: ./application/controllers/admin/Slide_shows.php */