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
class Rss extends Session_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$models = array('products'   => 'prod',
		                'blog_posts' => 'blog');

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('rss');
		$this->load->helper('xml');

		$this->data = $this->init->initialize('system');

		log_message('debug', __CLASS__ . ' Class Initialized');

		header("Content-Type: application/rss+xml");
	}

	// ------------------------------------------------------------------------

	public function feed()
	{
		$this->data['type'] = valid_id(uri(2), TRUE);

		//set the offset
		$opt = array('offset' => 0,
		             'limit'  => RSS_LIMIT,
		);

		if ($this->data['type'] == 'products')
		{
			//get the products
			$rows = $this->prod->load_home_products($opt, sess('default_lang_id'), 'featured_products');
		}
		else
		{
			//get blog posts
			$rows = $this->blog->load_blog_posts(query_options($opt), sess('default_lang_id'));
		}

		$this->data['feed'] = format_rss($this->data['type'], $rows['values']);

		$this->show->display(__CLASS__, 'feed', $this->data);
	}
}

/* End of file Rss.php */
/* Location: ./application/controllers/Rss.php */