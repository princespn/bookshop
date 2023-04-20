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
class Products_filters extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('products_filters_model', 'filter');
		$this->load->helper('products');

		$this->config->set_item('menu', TBL_PRODUCTS);

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
		$this->data['rows'] = $this->filter->get_rows();

		//run the page
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/js/jquery-ui.js') . '"></script>';
		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_FILTERS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_PRODUCTS_FILTERS, TBL_PRODUCTS_FILTERS, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->filter->update($row['data']);

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

		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->filter->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_FILTERS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_order()
	{
		$this->dbv->update_sort_order(TBL_PRODUCTS_FILTERS, $this->input->get('filterid', TRUE), 'filter_id');
	}
}

/* End of file Products_filters.php */
/* Location: ./application/controllers/admin/Products_filters.php */
