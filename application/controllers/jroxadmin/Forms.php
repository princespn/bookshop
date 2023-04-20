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
class Forms extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'form');

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
		$this->data['rows'] = $this->form->get_rows();

		//run the page
		$this->load->page('design/' . TPL_ADMIN_FORMS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$row = $this->form->create();

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

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->form->validate_form($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_FORMS, 'form_id', $row['data']);

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

		$this->data['row'] = $this->form->get_details($this->data['id'], FALSE);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('design/' . TPL_ADMIN_FORMS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id < 4)
		{
			show_error(lang('invalid_id'));
		}

		$row = $this->dbv->delete(TBL_FORMS, 'form_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function delete_field()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_FORM_FIELDS, 'field_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
			redirect_flashdata($url, $row['msg_text']);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function update_field()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->form->validate_field($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->form->update_field($row['data']);

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

		$this->data['row'] = $this->form->get_field_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('design/' . TPL_ADMIN_FORM_FIELD_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_order()
	{
		$this->form->update_sort_order($this->input->get('formid', TRUE));
	}

	// ------------------------------------------------------------------------

	public function create_field()
	{
		$this->data['id'] = valid_id(uri(4));

		$row = $this->form->create_field($this->data['id']);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		redirect_page(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update_fields/' . $this->data['id'] . '#custom');
	}

	// ------------------------------------------------------------------------

	public function update_fields()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->form->validate_admin_fields($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->form->update_admin_fields($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => admin_url(strtolower(__CLASS__) . '/update_fields/' . $this->data['id'])
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

		$this->data['row'] = $this->form->get_form_fields($this->data['id'], sess('default_lang_id'), '', FALSE, 'admin');

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/js/jquery-ui.js') . '"></script>';
		$this->load->page('design/' . TPL_ADMIN_FORM_FIELDS_MANAGE, $this->data);
	}
}

/* End of file Forms.php */
/* Location: ./application/controllers/admin/Forms.php */