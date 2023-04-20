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
class Affiliate_marketing extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'aff');
		$this->load->model('affiliate_groups_model', 'aff_group');
		$this->load->helper('settings');
		$this->load->helper('html_editor');

		$this->config->set_item('menu', 'affiliates');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

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

		$this->data['rows'] = $this->aff->get_rows($this->data['page_options']);

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
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_MARKETING_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	// ------------------------------------------------------------------------

	public function create()
	{
		$this->data['module_id'] = valid_id(uri(4));

		$this->data['module_row'] = $this->aff->get_settings($this->data['module_id']);

		if (!$this->data['module_row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_marketing', $this->data['module_row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			//check if the form submitted is correct

			$row = $this->$module->validate_record($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->$module->create_record($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => (admin_url(strtolower(__CLASS__) . '/update/' . $row['id'] . '/' . $this->data['module_id'])),
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'error_fields' => empty($row['error_fields']) ? '' : $row['error_fields'],
				                  'msg'  => $row['msg_text'],
				);
			}

			ajax_response($response);
		}

		//fill in default values for input fields
		$this->data['row'] = set_default_form_values(array(config_item('module_table')));

		if (config_item('module_enable_wysiwyg'))
		{
			$this->data['meta_data'] = html_editor('head');
		}

		$this->load->page($this->config->item('module_admin_update_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));
		$this->data['module_id'] = valid_id(uri(5));

		$this->data['module_row'] = $this->aff->get_settings($this->data['module_id']);

		if (!$this->data['module_row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_marketing', $this->data['module_row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->$module->validate_record($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->$module->update_record($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'error_fields' => empty($row['error_fields']) ? '' : $row['error_fields'],
				                  'msg'  => $row['msg_text'],
				);
			}

			ajax_response($response);
		}

		//lets get the module's record from db
		$this->data['row'] = $this->$module->get_record_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_module_record_found'));
		}

		if ($this->config->item('module_enable_wysiwyg'))
		{
			$this->data['meta_data'] = html_editor('head');
		}

		$this->load->page($this->config->item('module_admin_create_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$this->data['id'] = valid_id(uri(4));
		$this->data['module_id'] = valid_id(uri(5));

		$this->data['module_row'] = $this->aff->get_settings($this->data['module_id']);

		if (!$this->data['module_row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_marketing', $this->data['module_row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		$row = $this->$module->delete_record($this->data['id']);
		
		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view_rows/0/?module_id=' . $this->data['module_id'], $row['msg_text']);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function settings()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['module_row'] = $this->aff->get_settings($this->data['id']);

		if (!$this->data['module_row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_marketing', $this->data['module_row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->$module->validate_affiliate_module($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->$module->update_affiliate_module($row['data']);

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

		$this->load->page($this->config->item('module_admin_settings_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function view_rows()
	{
		$this->data['id'] = $this->input->get('module_id');

		$this->data['module_row'] = $this->aff->get_settings($this->data['id']);

		if (!$this->data['module_row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_marketing', $this->data['module_row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->$module->get_rows($this->data['page_options']);

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

		$this->load->page($this->config->item('module_admin_view_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_order()
	{
		$this->data['id'] = (int)$this->uri->segment(4);

		$this->data['module_row'] = $this->aff->get_settings($this->data['id']);

		if (!$this->data['module_row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_marketing', $this->data['module_row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		$this->aff->update_sort_order($this->input->get('formid', TRUE), config_item('module_table'), 'id');
	}

	// ------------------------------------------------------------------------

	public function activate_account()
	{
		$id = valid_id(uri(4));

		$row = $this->aff->activate_affiliate_account($id, 'admin');

		if (!empty($row['success']))
		{
			redirect_flashdata(ADMIN_ROUTE . '/' . TBL_MEMBERS . '/update/' . $id, $row['msg_text']);

		}
	}

	// ------------------------------------------------------------------------

	public function get_user_totals()
	{
		//used on member details page to show total clicks, comms, referrals

		$row = $this->aff->get_user_totals((int)uri(4));

		if ($row['success'])
		{
			$comm = empty($row['data']['total_commissions']) ? 0 : $row['data']['total_commissions'];
			$row['data']['total_commissions'] = format_amount($comm);
			$response = array( 'type' => 'success',
			                   'data'  => $row[ 'data' ],
			);
		}

		ajax_response($response);
	}
}

/* End of file Affiliate_marketing.php */
/* Location: ./application/controllers/admin/Affiliate_marketing.php */