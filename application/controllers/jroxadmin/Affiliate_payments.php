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
class Affiliate_payments extends Admin_Controller
{
	protected $data = array();

	/**
	 * @var string
	 */
	protected $table = TBL_AFFILIATE_PAYMENTS;

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'pay');

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
	
	public function view()
	{
		$this->data[ 'page_options' ] = query_options($this->data);

		$this->data[ 'rows' ] = $this->pay->get_rows($this->data[ 'page_options' ]);

		//check for pagination
		if (!empty($this->data[ 'rows' ][ 'total' ]))
		{
			$this->data[ 'page_options' ] = array(
				'uri'        => $this->data[ 'uri' ],
				'total_rows' => $this->data[ 'rows' ][ 'total' ],
				'per_page'   => $this->data[ 'session_per_page' ],
				'segment'    => $this->data[ 'db_segment' ],
			);

			$this->data[ 'paginate' ] = $this->paginate->generate($this->data[ 'page_options' ], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_PAYMENTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		//if POST input is sent, let's validate it...
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->pay->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->pay->update($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'redirect' => (ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row[ 'id' ])
				);
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => $row[ 'msg_text' ],
				);
			}

			ajax_response($response);
		}

		//set the ID
		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->pay->get_details($this->data[ 'id' ]);

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_PAYMENTS_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_AFFILIATE_PAYMENTS, 'aff_pay_id', $id);

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
}

/* End of file Affiliate_payments.php */
/* Location: ./application/controllers/admin/Affiliate_payments.php */