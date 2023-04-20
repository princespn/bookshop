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
class Affiliate_downline extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		//autoload public models
		$models = array(
			'invoices' => 'invoices',
			__CLASS__  => 'comm',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

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
		$this->data['id'] = (int)uri(4);

		$this->data['affiliate_data'] = $this->aff->get_affiliate_data($this->data['id'], 'member_id');

		//get rows
		$this->data['rows'] = $this->downline->generate_downline($this->data['id'], TRUE);


		//run the page
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_DOWNLINE_VIEW, $this->data, 'admin', FALSE, FALSE, TRUE);

	}
}

/* End of file Affiliate_downline.php */
/* Location: ./application/controllers/admin/Affiliate_downline.php */