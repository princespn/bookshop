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
class Product extends Product_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();

		$models = array('products_downloads' => 'dw',
		                'products_filters'   => 'filter',
		                'products_tags'      => 'tag');

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * store page for the site
	 *
	 * this method will show the default store
	 * and featured products routed through /store
	 */
	public function store()
	{
		//set the custom offset
		$opt = array('offset'           => (int)uri(2, 0),
		             'session_per_page' => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
		);

		//get the products
		$rows = $this->prod->load_home_products(query_options($opt), sess('default_lang_id'), 'featured_products');

		//set the products array
		$this->data['products'] = empty($rows['values']) ? FALSE : $rows['values'];

		//get product categories
		//$this->data['categories'] = $this->cat->select_categories(TRUE);
		$this->data['sub_categories'] = $this->cat->sub_categories('0', sess('default_lang_id'));

		//check for pagination
		if (!empty($rows['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => site_url() . uri(1),
				'total_rows' => $rows['total'],
				'per_page'   => $opt['session_per_page'],
				'segment'    => 2,
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
			$this->data['next_scroll'] = check_infinite_scroll($this->data);
		}

		//set the default listing template
		$tpl = 'product_' . $this->config->item('layout_design_product_page_layout');
		$this->show->display(CONTROLLER_CLASS, $tpl, $this->data);
	}

	// ------------------------------------------------------------------------

	public function details()
	{
		//set the id
		$this->data['id'] = get_id(uri(3));

		if (!$row = $this->prod->get_details($this->data['id'], sess('default_lang_id'), TRUE))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//map product details
			$this->data['p'] = format_products($row, sess('default_lang_id'));

			//format breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(format_breadcrumb($row));

			//update stats
			$this->dbv->update_count(array('table' => TBL_PRODUCTS,
			                               'key'   => 'product_id',
			                               'id'    => $this->data['id'],
			                               'field' => 'product_views'));

			$this->show->display(CONTROLLER_CLASS, set_template($row), $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function category()
	{
		//set the id
		$this->data['id'] = (int)uri(3);

		//get the category details first
		if (!$row = $this->cat->get_details($this->data['id'], sess('default_lang_id'), TRUE))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//set category details
			$this->data['c'] = $row;

			//set the custom offset
			$opt = array('offset'           => (int)uri(4, 0),
			             'session_per_page' => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
			);

			//get the products for this category
			$rows = $this->prod->load_category_products(query_options($opt), $this->data['id'], sess('default_lang_id'));

			//set the products array
			$this->data['products'] = empty($rows['values']) ? FALSE : $rows['values'];

			//get product categories
			$this->data['sub_categories'] = $this->cat->sub_categories($this->data['id'], sess('default_lang_id'));

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url() . uri(3, '', TRUE),
					'total_rows' => $rows['total'],
					'per_page'   => $opt['session_per_page'],
					'segment'    => 4,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
				$this->data['next_scroll'] = check_infinite_scroll($this->data);
			}

			//format breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(format_breadcrumb($row, 'product_category'));

			//set the default listing template
			$tpl = 'product_' . $this->config->item('layout_design_product_page_layout');
			$this->show->display(CONTROLLER_CLASS, $tpl, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function sub_categories($id = '')
	{
		//get product categories
		$sub = $this->cat->sub_categories(valid_id($id), sess('default_lang_id'));

		$response = array('type' => 'error');

		if (!empty($sub))
		{
			$response = array('type'           => 'success',
			                  'sub_categories' => $sub,
			);
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function brand($id = '')
	{
		//set the id
		$this->data['id'] = get_id($id);

		//get the category details first
		if (!$row = $this->brands->get_details($this->data['id'], sess('default_lang_id'), TRUE))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//set category details
			$this->data['b'] = $row;

			//set the custom offset
			$opt = array('offset'           => (int)uri(5, 0),
			             'session_per_page' => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
			);

			//get the products for this category
			$rows = $this->prod->load_brand_products(query_options($opt), $this->data['id'], sess('default_lang_id'), FALSE);

			//set the products array
			$this->data['products'] = empty($rows['values']) ? FALSE : $rows['values'];

			//get product categories
			$this->data['sub_categories'] = $this->cat->sub_categories('0', sess('default_lang_id'));

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url() . uri(4, '', TRUE),
					'total_rows' => $rows['total'],
					'per_page'   => $opt['session_per_page'],
					'segment'    => 5,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
				$this->data['next_scroll'] = check_infinite_scroll($this->data);
			}

			//format breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(array(lang('store')      => 'store',
			                                                 lang('brands')     => 'brands',
			                                                 $row['brand_name'] => ''));


			//set the default listing template
			$tpl = 'product_' . $this->config->item('layout_design_product_page_layout');
			$this->show->display(CONTROLLER_CLASS, $tpl, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function categories()
	{
		//set the id
		$this->data['id'] = (int)uri(3, 0);
		$this->data['c'] = '';

		if ($this->data['id'] > 0)
		{
			//get the category details first
			if (!$row = $this->cat->get_details($this->data['id'], sess('default_lang_id'), TRUE))
			{
				$this->show->page('404', $this->data);
			}
			else
			{
				$this->data['c'] = $row;
			}
		}

		//format breadcrumbs
		$this->data['breadcrumb'] = set_breadcrumb(format_breadcrumb($this->data['c'], 'product_category'));

		$this->data['categories'] = $this->cat->sub_categories($this->data['id'], sess('default_lang_id'), TRUE);

		$this->show->display(CONTROLLER_CLASS, 'product_categories', $this->data);
	}

	// ------------------------------------------------------------------------

	public function download()
	{
		$this->data['id'] = url_title(uri(3));

		if (!$row = $this->dw->check_download($this->data['id']))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			$expires = strtotime($row['expires']);

			if ($expires < now())
			{
				$this->data['error_message'] = lang('download_link_expired');
				$this->show->page('error', $this->data);
			}
			else
			{
				download_file($row['filename'], 'downloads');
			}
		}
	}

	// ------------------------------------------------------------------------

	public function similar()
	{
		//set the id
		$this->data['id'] = valid_id(uri(3));

		$rows = $this->prod->load_similar_products($this->data['id'], sess('default_lang_id'));

		//set the products array
		$this->data['products'] = !empty($rows['values']) ? $rows['values'] : FALSE;

		//similar products
		$this->show->display(CONTROLLER_CLASS, 'product_similar_ajax', $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_option()
	{
		if ($this->input->get('option_id'))
		{
			if ($row = $this->att->get_option_value($this->input->get('option_id')))
			{
				$a = !empty($row['unique_path']) ? $row['unique_path'] : $row['path'];
				echo $a;
			}
		}
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		//set the id
		$this->data['search_term'] = url_title($this->input->get('search_term'));

		if ($this->data['search_term'])
		{
			//set the custom offset
			$opt = array('offset'           => (int)uri(3, 0),
			             'session_per_page' => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
			);

			//get the products for this category
			$rows = $this->prod->search(query_options($opt, TRUE, TRUE), sess('default_lang_id'), TRUE);

			//set the products array
			$this->data['products'] = empty($rows['values']) ? FALSE : $rows['values'];

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url() . uri(2, '', TRUE),
					'total_rows' => $rows['total'],
					'per_page'   => $opt['session_per_page'],
					'segment'    => 3,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
				$this->data['next_scroll'] = check_infinite_scroll($this->data);
			}

			//set the default listing template
			$tpl = 'product_' . $this->config->item('layout_design_product_page_layout');

			$this->show->display(CONTROLLER_CLASS, $tpl, $this->data);
		}
		else
		{
			redirect('store');
		}
	}

	// ------------------------------------------------------------------------

	public function tags()
	{
		$this->data['tags'] = $this->tag->load_tags();
		$tpl = $this->input->get('q') == 'ajax' ? '_ajax' : '';
		$this->show->display(CONTROLLER_CLASS, 'product_tags' . $tpl, $this->data);
	}

	// ------------------------------------------------------------------------

	public function tag()
	{
		//set the id
		$this->data['id'] = uri(3);

		//get the category details first
		if (!$this->data['id'] || !$row = $this->dbv->get_record(TBL_PRODUCTS_TAGS, 'tag', $this->data['id']))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//set category details
			$this->data['c'] = $row;

			//set the custom offset
			$opt = array('offset'           => (int)uri(4, 0),
			             'session_per_page' => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
			);

			//get the products for this category
			$rows = $this->prod->load_tag_products(query_options($opt), $this->data['c']['tag_id'], sess('default_lang_id'));

			//set the products array
			$this->data['products'] = empty($rows['values']) ? FALSE : $rows['values'];

			//get product categories
			$this->data['sub_categories'] = $this->cat->sub_categories($this->data['id'], sess('default_lang_id'));

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url() . uri(3, '', TRUE),
					'total_rows' => $rows['total'],
					'per_page'   => $opt['session_per_page'],
					'segment'    => 4,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
				$this->data['next_scroll'] = check_infinite_scroll($this->data);
			}

			//set breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(array(lang('store')     => 'store',
			                                                 lang('tags')      => 'product/tags',
			                                                 $this->data['id'] => '',
			));

			//update tags
			$this->dbv->update_count(array('table' => TBL_PRODUCTS_TAGS,
			                               'key'   => 'tag',
			                               'id'    => $this->data['id'],
			                               'field' => 'count'));

			//set the default listing template
			$this->data['tag'] = $this->data['id'];
			$tpl = 'product_' . $this->config->item('layout_design_product_page_layout');
			$this->show->display(CONTROLLER_CLASS, $tpl, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function load_filters()
	{
		//shows the side bar product filters
		$this->filter->check_enabled();

		$this->data['opt'] = $this->filter->set_filters($this->input->get());

		$this->data['filters'] = $this->filter->get_rows(TRUE);

		$this->show->display('product', 'product_filters', $this->data);
	}

	// ------------------------------------------------------------------------

	public function set_filters()
	{
		$this->filter->check_enabled();

		$url = 'store';
		if ($this->input->post())
		{
			$vars = array();
			foreach ($this->input->post() as $k => $v)
			{
				if ($k != 'sort')
				{
					$v = implode('-', $v);
				}

				$vars[$k] = base64_encode($v);
			}

			$a = http_build_query($vars);
			$url = site_url('product/filters');
			$url .= '?' . $a;
		}

		redirect($url);
	}

	// ------------------------------------------------------------------------

	public function filters()
	{
		$this->filter->check_enabled();

		if ($this->input->get())
		{
			//set the id
			$opt = $this->filter->set_filters($this->input->get());

			//set the custom offset
			$opt['offset'] = (int)uri(3, 0);
			$opt['limit'] = !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page');

			//get the products for this category
			$rows = $this->filter->load_filters($opt, sess('default_lang_id'), TRUE);

			//set the products array
			$this->data['products'] = empty($rows['values']) ? FALSE : $rows['values'];
			$this->data['url'] = http_build_query($this->input->get());

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url() . uri(2, '', TRUE),
					'total_rows' => $rows['total'],
					'per_page'   => $opt['limit'],
					'segment'    => 3,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
				$this->data['next_scroll'] = check_infinite_scroll($this->data);
			}

			//set the default listing template
			$tpl = 'product_' . $this->config->item('layout_design_product_page_layout');

			$this->show->display(CONTROLLER_CLASS, $tpl, $this->data);
		}
		else
		{
			redirect('store');
		}
	}
}

/* End of file Product.php */
/* Location: ./application/controllers/Product.php */