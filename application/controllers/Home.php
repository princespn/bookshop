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
class Home extends Public_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'slide_shows'     => 'slide',
			'site_pages'      => 'page',
			'system_pages'    => 'system',
			'blog_categories' => 'cat',
			'currencies'      => 'currency',
			'widgets'         => 'w',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		//check age restriction
		check_age_restriction();

		//check if there is a cached copy of the page first
		//set the cache file to the current URL and serialized affiliate data if any
		$cache = current_url() . $this->config->item('username', 'affiliate_data');

		if (!$row = $this->init->cache($cache, 'site'))
		{
			//get home page layout style first..
			$row = $this->tpl->load_template(config_option('layout_design_home_page_content_layout'), $this->data);

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'site');
		}

		$this->data['row'] = $row;

		$folder = config_item('layout_design_home_page_content_layout') == 'builder' ? 'site_pages' : 'home';

		$this->show->display($folder, $this->data['row']['template'], $this->data);
	}

	// ------------------------------------------------------------------------

	public function offline()
	{
		if (config_enabled('sts_site_enable_offline_mode') || uri(2) == 'home')
		{
			$this->show->display('home', 'offline', $this->data);
		}
		else
		{
			redirect();
		}
	}

	// ------------------------------------------------------------------------

	public function switch_currency()
	{
		if ($row = $this->currency->switch_currency(url_title(uri(2))))
		{
			$this->session->set_userdata('custom_currency', $row['code']);
		}

		$page = !$this->agent->referrer() ? site_url() : $this->agent->referrer();

		redirect_flashdata($page);
	}

	// ------------------------------------------------------------------------

	public function javascript_required()
	{
		show_error(lang('please_enable_javascript'));
	}

	// ------------------------------------------------------------------------

	public function age_verification()
	{
		if (config_enabled('sts_site_enable_age_restriction'))
		{
			if ($this->input->post('agree'))
			{
				set_age_verification_cookie();

				redirect();
			}

			$this->show->display('home', 'age_verify', $this->data);
		}
		else
		{
			redirect();
		}
	}
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */