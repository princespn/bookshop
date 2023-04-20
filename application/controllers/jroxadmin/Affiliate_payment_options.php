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
class Affiliate_payment_options extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('affiliate_payments_model', 'pay');
		$this->load->helper('download');

		$this->config->set_item('menu', 'affiliates');
		$this->config->set_item('sub_menu', TBL_AFFILIATE_PAYMENTS);

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

	// ------------------------------------------------------------------------

	public function view()
	{
		//get the reports
		$this->data['rows'] = $this->mod->get_modules('affiliate_payments', TRUE);

		//run the page
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_PAYMENT_OPTIONS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->pay->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->pay->update($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(CONTROLLER_CLASS . '/select_users/0/' . $row['data']['module_id']),
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

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_PAYMENT_OPTIONS_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function select_users()
	{

		$this->data['id'] = (int)uri(5);

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_payments', $this->data['row']['module']['module_folder']);

		$this->data['page_options'] = query_options($this->data);

		//get rows
		$module = $this->config->item('module_alias');
		$func = $this->config->item('module_admin_view_function');

		$this->data['rows'] = $this->$module->$func();

		//run the page
		$this->load->page($this->config->item('module_admin_view_template'), $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function generate_mass_payment()
	{

		$this->data['id'] = $this->input->post('module_id');

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_payments', $this->data['row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		if ($this->input->post('select'))
		{
			switch ($this->input->post('payment_type'))
			{
				case 'generate_file':

					$row = $this->$module->generate_payments($this->input->post());

					break;

				default:

					$note = config_item('module_affiliate_payments_' . $this->data['row']['module']['module_folder'] . '_payment_details');

					$row = $this->pay->mark_as_paid($this->input->post(), $note);

					$this->done(__METHOD__, $row);

					break;
			}
		}
		else
		{
			$row = array('type'     => 'error',
			             'msg_text' => lang('no_users_selected'));
		}

		$page = !$this->agent->referrer() ? admin_url('affiliate_payment_options') : $this->agent->referrer();

		redirect_flashdata($page, $row['msg_text'], $row['type']);
	}

	// ------------------------------------------------------------------------
	
	public function direct_pay()
	{

		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_payments', $this->data['row']['module']['module_folder']);

		$module = $this->config->item('module_alias');

		$row = array('type'     => 'error',
		             'msg_text' => lang('invalid_data'));

		if ($this->input->get())
		{
			$row = $this->$module->direct_pay($this->input->get());

			if (!empty($row['success']))
			{
				//mark the commissions paid
				$row = $this->pay->mark_commissions_paid($row);
				$row['type'] = 'success';

				$this->done(__METHOD__, $row);
			}
			else
			{
				$row = array('type'     => 'error',
				             'msg_text' => $row['msg_text']);
			}
		}

		$page = !$this->agent->referrer() ? admin_url('affiliate_payment_options') : $this->agent->referrer();

		redirect_flashdata($page, $row['msg_text'], $row['type']);

	}
}

/* End of file Affiliate_payment_options.php */
/* Location: ./application/controllers/admin/Affiliate_payment_options.php */