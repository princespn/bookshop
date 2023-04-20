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
class Shop extends Product_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'slide_shows' => 'slide',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function id()
	{
		$this->data['id'] = uri(2);

		//verify username
		if (sess('tracking_data', 'member_id'))
		{
			//initialize the require files for the module
			$this->init_module('affiliate_marketing', 'affiliate_stores');

			$module = $this->config->item('module_alias');

			$this->data['row'] = $this->$module->get_affiliate_store(sess('tracking_data', 'member_id'), sess('default_lang_id'));

			if (!$this->data['row'])
			{
				redirect();
			}

			//get slideshows if enabled
			if (config_enabled('layout_design_home_page_show_slideshows'))
			{
				$t = $this->slide->get_slideshows(sess('default_lang_id'), $this->data);
				$this->data['row'][ 'slide_shows' ] = $t[ 'slide_shows' ];
				$this->data['row'][ 'meta_data' ] = $t[ 'meta_data' ];
				$this->data['row'][ 'footer_data' ] = $t[ 'footer_data' ];
			}

			//load the template
			$this->show->display('product', 'affiliate_shop', $this->data);
		}
		else
		{
			redirect();
		}
	}

	// ------------------------------------------------------------------------

	public function affiliate_store()
	{
		if (sess('member_id'))
		{
			$row = $this->mod->get_module_details('affiliate_stores', TRUE, 'affiliate_marketing', 'module_folder');

			if (!empty($row))
			{

				//initialize the require files for the module
				$this->init_module('affiliate_marketing', 'affiliate_stores');

				$module = $this->config->item('module_alias');

				switch (uri(3))
				{
					case 'add':

						$this->$module->add_affiliate_store('add', array('product_id' => valid_id(uri(4)),
						                                              'member_id'  => sess('member_id')));
						break;

					case 'remove':

						$this->$module->add_affiliate_store('remove', array('product_id' => valid_id(uri(4)),
						                                                 'member_id'  => sess('member_id')));
						break;
				}
			}
			else
			{
				$this->session->unset_userdata('affiliate_store');
				$this->session->unset_userdata('affiliate_store_products');
			}
		}

		$url = !$this->agent->referrer() ? 'store' : $this->agent->referrer();

		redirect_flashdata($url, 'affiliate_store_updated_successfully');
	}
}
/* End of file Shop.php */
/* Location: ./application/controllers/Shop.php */