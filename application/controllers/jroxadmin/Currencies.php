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
class Currencies extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'currency');

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
		$this->data['rows'] = $this->dbv->get_rows(array(), TBL_CURRENCIES);

		//run the page
		$this->load->page('localization/' . TPL_ADMIN_CURRENCIES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_CURRENCIES, TBL_CURRENCIES, $this->input->post(), FALSE);

			if (!empty($row['success']))
			{
				$row = $this->dbv->create(TBL_CURRENCIES, $row['data']);

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
		$this->data['row'] = set_default_form_values(array(TBL_CURRENCIES));

		$this->load->page('localization/' . TPL_ADMIN_CURRENCIES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_CURRENCIES, TBL_CURRENCIES, $this->input->post(), FALSE);

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_CURRENCIES, 'currency_id', $row['data']);

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

		$this->data['row'] = $this->dbv->get_record(TBL_CURRENCIES, 'currency_id', $this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('localization/' . TPL_ADMIN_CURRENCIES_UPDATE, $this->data);
	}

	function set_default()
	{
		$this->data['id'] = valid_id(uri(4), TRUE);

		$row = $this->set->update_db_settings(array('sts_site_default_currency' => $this->data['id']));

		if (!empty($row['success']))
		{
			$vars = $this->dbv->get_record(TBL_CURRENCIES, 'code', $this->data['id']);

			$this->db->where('currency_id', $vars['currency_id'])->update(TBL_CURRENCIES, array('status' => '1'));

			//update all currency values
			$this->currency->convert_currencies($vars);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id != config_item('sts_site_default_currency'))
		{
			$row = $this->dbv->delete(TBL_CURRENCIES, 'currency_id', $id);

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
			}
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}
}

/* End of file Currencies.php */
/* Location: ./application/controllers/admin/Currencies.php */