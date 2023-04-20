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
class Email_mailing_lists extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'list');
		$this->load->model('email_follow_ups_model', 'f');

		$this->config->set_item('menu', 'email');

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

		$this->data['rows'] = $this->list->get_rows($this->data['page_options']);

		$this->data['modules'] = $this->mod->get_modules('mailing_lists');

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_MAILING_LISTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function view_subscribers()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->list->get_subscribers($this->data['page_options']);

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

		$id = $this->input->get('p-list_id');

		$this->data['sequence'] = $this->f->get_follow_ups($id);

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_MEMBERS_MAILING_LISTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data['row'] = set_default_form_values(array(TBL_EMAIL_MAILING_LISTS));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_EMAIL_MAILING_LISTS, TBL_EMAIL_MAILING_LISTS, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->create(TBL_EMAIL_MAILING_LISTS, $row['data']);

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

		$this->load->page('email/' . TPL_ADMIN_EMAIL_MAILING_LISTS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_module()
	{
		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (! $this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('mailing_lists', $this->data['row']['module']['module_folder']);

		//use module_row array to store all module data for editing in the view
		$module = $this->config->item('module_alias');

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->list->validate_module($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->list->update_module($row['data'], config_option('module_mailing_list_table'));

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

		$this->data['module_row'] = $this->$module->get_module_options( $this->data['id'], $this->data['row']);

		$this->load->page($this->config->item('module_template'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//set the rule ID
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_EMAIL_MAILING_LISTS, TBL_EMAIL_MAILING_LISTS, $this->input->post());

			if (!empty($row['success']))
			{

				$row = $this->dbv->update(TBL_EMAIL_MAILING_LISTS, 'list_id', $row['data']);

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

		$this->data['row'] = $this->dbv->get_record(TBL_EMAIL_MAILING_LISTS, 'list_id', $this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('email/' . TPL_ADMIN_EMAIL_MAILING_LISTS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id > 3)
		{
			$row = $this->list->delete($id);

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);
			}

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function delete_subscriber()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_MEMBERS_EMAIL_MAILING_LIST, 'eml_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		//set the session flash and redirect the page
		redirect_flashdata($url, $row['msg_text']);

	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('sort'))
		{
			$this->list->mass_subscriber_update($this->input->post('sort'));
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('list_name', TRUE);

		$rows = $this->list->ajax_search($term);

		echo json_encode($rows);
	}
}

/* End of file Email_mailing_lists.php */
/* Location: ./application/controllers/admin/Email_mailing_lists.php */