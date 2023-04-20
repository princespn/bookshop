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
class Kb extends Public_Controller
{
	public $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array( 'Kb_articles'   => 'kb',
		                 'Kb_categories' => 'cat' );

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		if (!config_enabled('sts_kb_enable')) redirect();

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//get the articles
		$this->data[ 'articles' ] = $this->kb->load_featured_articles(sess('default_lang_id'));

		//get categories
		$this->data[ 'kb_categories' ] = $this->cat->load_categories('0', sess('default_lang_id'));

		$this->show->display('support', 'kb_home_default', $this->data);
	}

	// ------------------------------------------------------------------------

	public function category()
	{
		//set the id
		$this->data[ 'id' ] = get_id(uri(3));

		//get the category details first
		$row = $this->cat->get_details($this->data[ 'id' ], sess('default_lang_id'), TRUE, 'category_url');

		if (!empty( $row[ 'category_url' ]))
		{
			//set category details
			$this->data[ 'c' ] = $row;

			//set the custom offset
			$opt = array( 'offset'           => (int)uri(4, 0),
			              'session_per_page' => !sess('rows_per_page') ? TBL_MEMBERS_DEFAULT_TOTAL_ROWS : sess('rows_per_page')
			);

			//get the articles
			$rows = $this->kb->load_kb_articles(query_options($opt), $this->data[ 'id' ], sess('default_lang_id'));

			//set the articles array
			$this->data[ 'articles' ] = empty($rows[ 'values' ]) ? FALSE : $rows[ 'values' ];

			//check for pagination
			if (!empty($rows[ 'total' ]))
			{
				$this->data[ 'page_options' ] = array(
					'uri'        => site_url('kb/category/' . uri(3)),
					'total_rows' => $rows[ 'total' ],
					'per_page'   => $opt['session_per_page'],
					'segment'    => 4,
				);

				$this->data[ 'paginate' ] = $this->paginate->generate($this->data[ 'page_options' ], CONTROLLER_CLASS);
				$this->data[ 'next_scroll' ] = check_infinite_scroll($this->data);
			}

			//get categories
			$this->data[ 'kb_categories' ] = $this->cat->load_categories($this->data[ 'id' ], sess('default_lang_id'));

			//format breadcrumbs
			$path[ 'cat_path' ] = $this->cat->cat_path($row, sess('default_lang_id'));
			$this->data[ 'breadcrumb' ] = set_breadcrumb(format_breadcrumb($path, 'kb'));

			$this->show->display('support', 'kb_category_list', $this->data);

		}
		else
		{
			$this->show->page('404', $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function article()
	{
		//set the id
		$this->data[ 'id' ] = url_title(uri(3));

		if (!$row = $this->kb->get_details($this->data[ 'id' ], sess('default_lang_id'), TRUE))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//map blog details
			$this->data[ 'p' ] = format_content_page($row, 'kb_');

			//get categories
			$this->data[ 'kb_categories' ] = $this->cat->load_categories('0', sess('default_lang_id'));

			//format breadcrumbs
			$row[ 'cat_path' ] = $this->cat->cat_path($row, sess('default_lang_id'));
			$this->data[ 'breadcrumb' ] = set_breadcrumb(format_breadcrumb($row, 'kb'));

			//update stats
			$this->dbv->update_count(array('table' => TBL_KB_ARTICLES,
			                               'key'   => 'kb_id',
			                               'id'    => $row['kb_id'],
			                               'field' => 'views'));

			//load the template
			$this->show->display('support', 'kb_article', $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function ticket_answers()
	{
		//set the id
		$this->data[ 'search_term' ] = url_title($this->input->get('search_term'));

		if ($this->data['search_term'])
		{
			//set the custom offset
			$opt = array( 'offset'           => 0,
			              'session_per_page' => 5,
			);

			//get the articles
			$rows = $this->kb->search(query_options($opt, TRUE, TRUE), sess('default_lang_id'), TRUE);

			//set the articles array
			if (!empty($rows[ 'values' ]))
			{
				$this->data['articles'] = $rows['values'];
				$this->show->display('support', 'kb_ticket_answers_ajax', $this->data);
			}
		}
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		//set the id
		$this->data[ 'search_term' ] = url_title($this->input->get('search_term'));

		if ($this->data['search_term'])
		{
			//set the custom offset
			$opt = array( 'offset'           => (int)uri(3, 0),
			              'session_per_page' => !sess('rows_per_page') ? $this->data[ 'layout_design_kb_per_page' ] : sess('rows_per_page')
			);

			//get the articles
			$rows = $this->kb->search(query_options($opt, TRUE, TRUE), sess('default_lang_id'), TRUE);

			//set the articles array
			$this->data[ 'articles' ] = empty($rows[ 'values' ]) ? FALSE : $rows[ 'values' ];

			//check for pagination
			if (!empty($rows[ 'total' ]))
			{
				$this->data[ 'page_options' ] = array(
					'uri'        => site_url() . uri(1),
					'total_rows' => $rows[ 'total' ],
					'per_page'   => $opt[ 'session_per_page' ],
					'segment'    => 3,
				);

				$this->data[ 'paginate' ] = $this->paginate->generate($this->data[ 'page_options' ], CONTROLLER_CLASS);
			}

			//get categories
			$this->data[ 'kb_categories' ] = $this->cat->load_categories(0, sess('default_lang_id'));

			$this->show->display('support', 'kb_category_list', $this->data);

		}
		else
		{
			$this->show->page('404', $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function download()
	{
		$this->data[ 'id' ] = url_title(uri(4));

		if (!$row = $this->kb->get_details($this->data[ 'id' ], sess('default_lang_id'), TRUE))
		{
			show_error('invalid_file');
		}

		download_file(uri(3), 'downloads');
	}
}

/* End of file Kb.php */
/* Location: ./application/controllers/Kb.php */