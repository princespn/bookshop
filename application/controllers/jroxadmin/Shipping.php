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
class Shipping extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'shipping');
		$this->load->model('regions_model', 'region');
		$this->load->model('modules_model', 'mod');
		$this->load->helper('settings');

		$this->config->set_item('menu', 'locale');

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

		$this->data['rows'] = $this->shipping->get_rows($this->data['page_options']);

		//run the page
		$this->load->page('localization/' . TPL_ADMIN_SHIPPING_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('shipping', $this->data['row']['module']['module_folder']);

		//use module_row array to store all module data for editing in the view
		$module = $this->config->item('module_alias');
		$this->data['module_row'] = $this->$module->get_module_options($this->data['id'], $this->data['row']);

		if (empty($this->data['module_row']['zones']))
		{
			$this->data['module_row']['zones'] = array();
		}

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->$module->validate_shipping_module($this->input->post(), config_option('module_shipping_table'));

			if (!empty($row['success']))
			{
				$row = $this->$module->update_module($row['data'], config_option('module_shipping_table'));

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/update/' . $this->data['id'])
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

		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/js/jquery-ui.js') . '"></script>';
		$this->load->page($this->config->item('module_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_order()
	{
		$this->dbv->update_sort_order('module_shipping_'.  uri(4), $this->input->get('sortid', TRUE), 'id');
	}
}

/* End of file Shipping.php */
/* Location: ./application/controllers/admin/Shipping.php */