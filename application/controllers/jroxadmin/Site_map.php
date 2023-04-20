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
class Site_map extends Admin_Controller
{
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'sitemap');

		$this->config->set_item('menu', 'promotions');

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
		if (uri(4) == 'notify')
		{
			$this->data['response'] = array();

			foreach ($this->data['site_maps'] as $k => $v)
			{
				if ($v == 'site_map_index')
				{
					$this->data['response'][$k]['url'] = site_url('site_map/' . $v . '/sitemap.xml');
				}
				else
				{
					$this->data['response'][$k]['url'] = site_url('site_map/id/' . $v . '.xml');
				}

				$this->data['response'][$k]['response'] = use_curl('www.google.com/webmasters/tools/ping?sitemap=' . urlencode($this->data['response'][$k]['url']));
			}
		}

		//run the page
		$this->load->page('promotions/' . TPL_ADMIN_SITE_MAP_VIEW, $this->data);
	}
}

/* End of file Site_map.php */
/* Location: ./application/controllers/admin/Site_map.php */