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
class Wish_list extends Public_Controller
{
	public $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('wish_lists_model', 'wish');

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		$this->data['id'] = valid_id(uri(2), TRUE);

		if ($this->data['member'] = $this->mem->get_basic_member($this->data['id']))
		{
			//set the custom offset
			$opt = array('offset'           => (int)uri(5, 0),
			             'session_per_page' => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
			);

			//get the products for this category
			$rows = $this->wish->load_wish_list(query_options($opt), $this->data['member']['member_id'], sess('default_lang_id'));

			//set the products array
			$this->data['products'] = empty($rows['values']) ? FALSE : $rows['values'];

			$this->show->display('product', 'wish_list', $this->data);
		}
		else
		{
			redirect(site_url('login'));
		}
	}

	// ------------------------------------------------------------------------

	public function add()
	{
		if (sess('user_logged_in'))
		{
			$row = $this->wish->add_member_wish(sess('member_id'), valid_id(uri(3)));

			$msg = !empty($row['success']) ? 'list_updated_successfully' : '';

			$url = !$this->agent->referrer() ? site_url('store') : $this->agent->referrer();

			redirect_flashdata($url, $msg);
		}
		else
		{
			redirect_flashdata('login', 'login_required', 'error');
		}
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$row = $this->wish->delete_member_wish(sess('member_id'), valid_id(uri(3)));

		$msg = !empty($row['success']) ? 'list_updated_successfully' : '';

		$url = !$this->agent->referrer() ? site_url('store') : $this->agent->referrer();

		redirect_flashdata($url, $msg);
	}
}

/* End of file Wish_list.php */
/* Location: ./application/controllers/Wish_list.php */