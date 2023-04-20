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
class Coupons extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * Coupons constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'coupon');

		$this->config->set_item('menu', 'Pin Code');

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

	/**
	 * View coupons
	 */
	public function view()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->dbv->get_rows($this->data['page_options'], TBL_COUPONS);

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
		$this->load->page('promotions/' . TPL_ADMIN_COUPONS_VIEW, $this->data);
	}

	/**
	 * Create a coupon
	 */
	public function create()
	{
		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( TBL_COUPONS ));
		$this->data['row']['coupon_code'] = $this->coupon->generate_serial();
		$this->data['row']['start_date'] = display_date(get_time() - 86400, FALSE, 2, TRUE);
		$this->data['row']['end_date'] = display_date(get_time() + 2592000 , FALSE, 2, TRUE);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->coupon->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->coupon->create($row['data']);

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

		$this->load->page('promotions/' . TPL_ADMIN_COUPONS_CREATE, $this->data);
	}

	/**
	 * Updata a coupon
	 */
	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->coupon->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->coupon->update($row['data']);

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

		$this->data['row'] = $this->coupon->get_details($this->data['id'], 'coupon_id', FALSE, sess('default_lang_id'));

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('promotions/' . TPL_ADMIN_COUPONS_UPDATE, $this->data);
	}

	/**
	 * Delete a coupon
	 */
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_COUPONS, 'coupon_id', $id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);

		}
		else
		{
			log_error('error', $row['msg_text']);
		}
	}

	/**
	 * Generate a coupon code via ajax
	 */
	public function generate_coupon()
	{
		$this->init->check_ajax_security();

		$response = array( 'type' => 'success',
		                   'coupon_code' => $this->coupon->generate_serial(),
		);

		ajax_response($response);
	}
}

/* End of file Coupons.php */
/* Location: ./application/controllers/admin/Coupons.php */