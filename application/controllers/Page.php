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
class Page extends Public_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();

		$models = array('site_pages'      => 'page',
		                'system_pages'    => 'system',
		                'blog_categories' => 'cat',
		                'widgets'         => 'w');

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('content');

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//set the id
		$uri = strlen(uri(2)) < 1 ? uri(1) : uri(2);

		$this->data['id'] = url_title($uri);

		$preview = $this->input->get('preview') ? FALSE : TRUE;

		if (!$row = $this->page->get_details($this->data['id'], set_lang_id(), $preview, 'url'))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//map blog details
			$this->data['row'] = $row;

			//format breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(array($this->data['row']['title'] => ''));

			if ($this->data['row']['type'] == 'builder')
			{
				$t = $this->tpl->format_template_data($this->data['row']['page_content'] , $this->data);

				$this->data['row']['meta_data'] .= $t['meta_data'];
				$this->data['row']['footer_data'] = $t['footer_data'];

				$this->data['row']['template_data'] = $this->tpl->format_template_data($t['template_data'], $this->data, 'widget');
				$template = 'site_builder';
			}
			else
			{
				if (config_option('layout_design_site_page_sidebar') != 'none')
				{
					//get categories
					$this->data['blog_categories'] = $this->cat->load_categories(sess('default_lang_id'));
				}

				$template = 'site_page';
			}

			//update stats
			$this->dbv->update_count(array('table' => TBL_SITE_PAGES,
			                               'key'   => 'page_id',
			                               'id'    => $row['page_id'],
			                               'field' => 'views'));

			//load the template
			$this->show->display('site_pages', $template, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function system()
	{
		//set the id
		$id = url_title(uri(1));

		switch ($id)
		{
			case 'tos':

				$this->data['id'] = '1';
				$this->data['title'] = lang('terms_of_service');

				break;

			case 'privacy_policy':

				$this->data['id'] = '2';
				$this->data['title'] = lang($id);

				break;

			case 'affiliate_program_tos':

				$this->data['id'] = '3';
				$this->data['title'] = lang('affiliate_terms_of_service');

				break;
		}

		if (!$row = $this->system->get_details($this->data['id'], set_lang_id(), TRUE))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//map blog details
			$this->data['row'] = $row;

			//format breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(array($this->data['row']['title'] => ''));

			//load the template
			$this->show->display('site_pages', 'system_page', $this->data);
		}
	}
}

/* End of file Page.php */
/* Location: ./application/controllers/Page.php */