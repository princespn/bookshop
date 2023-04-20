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
class Site_menus extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'menus');

		$this->config->set_item('menu', 'design');
		
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
		$this->data['rows'] = $this->menus->get_rows();

		//run the page
		$this->load->page('design/' . TPL_ADMIN_SITE_MENUS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$row = $this->menus->create();

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
		else
		{
			show_error(lang('could_not_create_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->menus->delete($id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	public function update_order()
	{
		$this->menus->update_menu_sort_order($this->input->get('menu', TRUE));
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->menus->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->menus->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->menus->update_links($this->data['id'], $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => admin_url(CONTROLLER_CLASS . '/update/' . $this->data['id']),
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

		$this->data['links'] = generate_internal_links();
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/js/jquery-ui.js') . '"></script>';
		$this->load->page('design/' . TPL_ADMIN_SITE_MENUS_UPDATE, $this->data);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('menu'))
		{
			$this->menus->mass_update($this->input->post('menu'));
		}

		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------

	public function add_link()
	{
		$parent_id = (int)uri(5, 0);
		$menu_id = (int)uri(4);

		$lang = get_languages(FALSE, TRUE);

		$row = $this->menus->add_link($menu_id, $parent_id, $lang);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $menu_id . '/' . $row['id'], $row['msg_text']);
		}
	}

	// ------------------------------------------------------------------------

	public function delete_link()
	{
		$this->data['id'] = valid_id(uri(4));

		//check if the form submitted is correct
		$row = $this->menus->delete_link($this->data['id']);

		if (!empty($row['success']))
		{
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
}

/* End of file Site_menus.php */
/* Location: ./application/controllers/admin/Site_menus.php */