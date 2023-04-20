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
class Status extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('orders_model', 'order');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/update');
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//get statuses
		$this->data['order_statuses'] = $this->dbv->get_all(TBL_ORDERS_STATUS);
		$this->data['payment_statuses'] = $this->dbv->get_all(TBL_PAYMENT_STATUS);
		$this->data['cc_types'] = $this->dbv->get_all(TBL_CC_TYPES);

		if ($this->input->post())
		{
			if ($this->input->post('type') == 'cc_types')
			{
				$row = $this->order->update_card_types($this->input->post(), $this->data['cc_types']);
			}
			else
			{
				$row = $this->order->update_statuses($this->input->post(), $this->data[$this->input->post('type') . '_statuses']);
			}

			if (!empty($row[ 'success' ]))
			{
				//log it!
				$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update', $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		$this->data['meta_data'] = link_tag('themes/admin/default/third/minicolors/jquery.minicolors.css');
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/third/minicolors/jquery.minicolors.js') . '"></script>';

		$this->load->page('orders/' . TPL_ADMIN_ORDERS_STATUSES, $this->data);
	}
}

/* End of file Status.php */
/* Location: ./application/controllers/admin/Status.php */