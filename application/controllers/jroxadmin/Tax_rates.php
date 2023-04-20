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
class Tax_rates extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('tax_classes_model', 'tax');
		$this->load->model('zones_model', 'zones');

		$this->config->set_item('menu', 'locale');
		$this->config->set_item('sub_menu', 'zones');

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
		$this->data['rows'] = $this->tax->get_tax_rates();

		//run the page
		$this->load->page('localization/' . TPL_ADMIN_TAX_RATES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$row = $this->tax->create_rate();

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
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_TAX_RATES, TBL_TAX_RATES, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_TAX_RATES, 'tax_rate_id', $row['data']);

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

		$this->data['row'] = $this->tax->get_tax_rates_details($this->data['id']);

		$this->data['zones'] = $this->zones->get_zones(TRUE);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('localization/' . TPL_ADMIN_TAX_RATES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = (int)$this->uri->segment(4);

		$row = $this->tax->delete_rate($id);

		if (is_array($row))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/', $row['msg_text']);
		}

		//run the page
		$this->load->page(__METHOD__, $this->data);
	}

	// ------------------------------------------------------------------------

	public function assign()
	{
		$this->data['id'] = (int)$this->uri->segment(4);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->tax->update_class_tax_rates($this->data['id'], $this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . TBL_TAX_CLASSES . '/update/' . $this->data['id'] . '#rules', $row['msg_text']);
			}
			else
			{
				//show errors on form
				log_error('error', __CLASS__ . ': ' . lang('invalid_data'));
			}
		}

		$this->data['tax_rates'] = $this->tax->get_rate_rules($this->data['id']);

		$this->data['meta_data'] = link_tag('themes/admin/' . $this->data['sts_admin_layout_theme'] . '/css/iframe.css');
		$this->data['meta_data'] .= '<script src="' . base_url('js/select2/select2.min.js') . '"></script>';

		$this->load->page('localization/' . TPL_ADMIN_TAX_RATES_ASSIGN, $this->data, 'admin', FALSE, FALSE);
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('tax_rate_name', TRUE);

		$rows = $this->tax->ajax_search($term, 'tax_rates');

		echo json_encode($rows);
	}
}

/* End of file Tax_rates.php */
/* Location: ./application/controllers/admin/Tax_rates.php */