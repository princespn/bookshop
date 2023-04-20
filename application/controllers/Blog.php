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
class Blog extends Public_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();

		$models = array('blog_posts'      => 'blog',
		                'blog_tags'       => 'tag',
		                'blog_comments'   => 'comments',
		                'blog_categories' => 'cat');

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		if (!config_enabled('sts_blog_enable')) redirect();

		$this->load->helper('content');

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//set the custom offset
		$opt = array('offset'           => (int)uri(2, 0),
		             'session_per_page' => $this->data['layout_design_blogs_per_page'],
		);

		//get the blog posts
		$rows = $this->blog->load_blog_posts(query_options($opt), sess('default_lang_id'));

		//set the posts array
		$this->data['posts'] = !empty($rows['values']) ? $rows['values'] : FALSE;

		//get categories
		$this->data['blog_categories'] = $this->cat->load_categories(sess('default_lang_id'));

		//check for pagination
		if (!empty($rows['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => site_url('blog'),
				'total_rows' => $rows['total'],
				'per_page'   => $opt['session_per_page'],
				'segment'    => 2,
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
			$this->data['next_scroll'] = check_infinite_scroll($this->data);
		}

		$this->show->display(CONTROLLER_CLASS, 'blog_' . config_option('layout_design_blog_page_layout'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function recent()
	{
		//get the blog posts
		$rows = $this->blog->load_blog_posts(query_options(array('offset'           => 0,
		                                                         'session_per_page' => 5,)),
																	sess('default_lang_id'));

		//set the posts array
		$this->data['posts'] = !empty($rows['values']) ? $rows['values'] : FALSE;

		$this->show->display(CONTROLLER_CLASS, 'blog_recent_sidebar', $this->data);
	}

	// ------------------------------------------------------------------------

	public function post()
	{
		//get the specific blog post entry

		//set the id
		$this->data['id'] = set_blog_id(uri(2));

		//check for preview
		$preview = $this->input->get('preview') ? FALSE : TRUE;

		$row = $this->blog->get_details($this->data['id'], set_lang_id(), $preview, 'url');

		if (empty($row['blog_id']))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//map blog details for page breaks
			$this->data['p'] = format_content_page($row);

			if (check_drip_feed($row) == FALSE)
			{
				if ($this->input->get('preview'))
				{
					show_error(lang('please_disable_drip_feed_and_group_restrictions_to_preview'));
				}
				else
				{
					$this->show->page('404', $this->data);
				}
			}
			else
			{
				//get related articles
				if (config_enabled('layout_design_blog_related_articles'))
				{
					$this->data['related_articles'] = $this->blog->get_related_posts($this->data['p']['blog_id'], $row['tag_ids'], sess('default_lang_id'));
				}

				//get categories
				$this->data['blog_categories'] = $this->cat->load_categories(sess('default_lang_id'));

				//format breadcrumbs
				$this->data['breadcrumb'] = set_breadcrumb(format_breadcrumb($row, 'blog'));

				//update stats
				$this->dbv->update_count(array('table' => TBL_BLOG_POSTS,
				                               'key'   => 'blog_id',
				                               'id'    => $row['blog_id'],
				                               'field' => 'views'));

				//load the template
				$this->show->display(CONTROLLER_CLASS, 'blog_post', $this->data);
			}
		}
	}

	// ------------------------------------------------------------------------

	public function comments()
	{
		$this->init->check_ajax_security();

		//set the blog id
		$this->data['id'] = (int)(uri(3));

		//check if we are using a custom comment system
		if ($this->data['sts_content_enable_comments'] == 1)
		{
			$this->data['comments'] = $this->comments->load_comments($this->data['id']);

			$this->show->display(CONTROLLER_CLASS, 'blog_comments_ajax', $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function add_comment()
	{
		$this->init->check_ajax_security();

		//set the id
		$this->data['id'] = valid_id(uri(3));

		if (!$vars = $this->blog->get_details($this->data['id'], sess('default_lang_id'), TRUE))
		{
			$response = array('type' => 'error',
			                  'msg'  => lang('invalid_id'),
			);
		}
		else
		{
			if ($this->input->post())
			{
				//validate the POST data first....
				$row = $this->comments->validate($this->input->post(), 'member');

				if (!empty($row['success']))
				{
					$row = $this->comments->create($row['data'], 'member');

					//generate rewards
					if (sess('member_id'))
					{
						$this->rewards->add_reward_points(sess('member_id'), 'reward_blog_comment');
					}

					$msg = lang('comment_added_successfully');

					if (config_enabled('sts_content_require_comment_moderation'))
					{
						$msg .= '<br /> ' . lang('please_wait_for_comment_approval');
					}

					//send the email
					$vars = array_merge($vars, $row['data']);

					$this->mail->send_admin_blog_alerts($vars);

					$this->session->set_flashdata('success', $msg);

					//log it!
					$this->dbv->rec(array('method' => __METHOD__,  'msg' => $row['msg_text']));

					//set the default response
					$response = array('type'     => 'success',
					                  'msg'      => $msg,
					);
				}
				else
				{
					$response = array('type' => 'error',
					                  'msg'  => $row['msg_text'],
					);
				}
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function download()
	{
		//set the id
		$this->data['id'] = valid_id(uri(4));

		$row = $this->blog->get_details($this->data['id'], sess('default_lang_id'), TRUE, 'url');

		if (empty($row['blog_id'])) {
			show_error(lang('invalid_file'));
		}

		download_file(uri(3), 'blog');
	}

	// ------------------------------------------------------------------------

	public function categories()
	{
		//get categories
		$this->data['blog_categories'] = $this->cat->load_categories(sess('default_lang_id'));

		$this->show->display(CONTROLLER_CLASS, 'blog_categories', $this->data);
	}

	// ------------------------------------------------------------------------

	public function tags()
	{
		$this->data['tags'] = $this->tag->load_tags();

		$this->show->display(CONTROLLER_CLASS, !is_ajax() ? 'blog_tags' : 'blog_tags_ajax', $this->data);
	}

	// ------------------------------------------------------------------------

	public function tag()
	{
		//set the category id
		$this->data['id'] = url_title(uri(3));

		//set the custom offset
		$opt = array('offset'           => (int)uri(4, 0),
		             'session_per_page' => $this->data['layout_design_blogs_per_page'],
		);

		//get the blog posts
		$rows = $this->blog->load_blogs_per_tag(query_options($opt), $this->data['id'], sess('default_lang_id'));

		//set the posts array
		$this->data['posts'] = !empty($rows['values']) ? $rows['values'] : FALSE;

		//get categories
		$this->data['blog_categories'] = $this->cat->load_categories(sess('default_lang_id'));

		//set category name
		$this->data['category_name'] = $this->data['id'];

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

		//set breadcrumbs
		$this->data['breadcrumb'] = set_breadcrumb(array(lang('blog')      => 'blog',
		                                                 lang('tags')      => 'blog/tags',
		                                                 $this->data['id'] => '',
		));

		//update tags
		$this->dbv->update_count(array('table' => TBL_BLOG_TAGS,
		                               'key'   => 'tag',
		                               'id'    => $this->data['id'],
		                               'field' => 'count'));

		$this->show->display(CONTROLLER_CLASS, 'blog_' . config_option('layout_design_blog_page_layout'), $this->data);
	}

	// ------------------------------------------------------------------------

	public function category()
	{
		//set the custom offset
		$opt = array('offset'           => (int)uri(5, 0),
		             'session_per_page' => $this->data['layout_design_blogs_per_page'],
		);

		//set the category id
		$this->data['id'] = get_id(uri(3));

		//get category details
		if (!$row = $this->cat->get_details($this->data['id'], sess('default_lang_id'), TRUE))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//set category details
			$this->data['c'] = $row;

			//set category name
			$this->data['category_name'] = $this->data['c']['category_name'];

			//get the blog posts
			$rows = $this->blog->load_blog_posts(query_options($opt), sess('default_lang_id'), $this->data['id']);

			//set the posts array
			$this->data['posts'] = !empty($rows['values']) ? $rows['values'] : FALSE;

			//get categories
			$this->data['blog_categories'] = $this->cat->load_categories(sess('default_lang_id'));

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

			//set breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(array(lang('blog')                 => 'blog',
			                                                 $this->data['category_name'] => '',
			));

			$this->show->display(CONTROLLER_CLASS, 'blog_' . config_option('layout_design_blog_page_layout'), $this->data);
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
			$rows = $this->blog->search(query_options($opt, TRUE, TRUE), sess('default_lang_id'), TRUE);

			//set the articles array
			$this->data[ 'posts' ] = empty($rows[ 'values' ]) ? FALSE : $rows[ 'values' ];

			//get categories
			$this->data['blog_categories'] = $this->cat->load_categories(sess('default_lang_id'));

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

			//set breadcrumbs
			$this->data['breadcrumb'] = set_breadcrumb(array(lang('blog')                 => 'blog',
			                                                 lang('search') => '',
			));

			$this->show->display(CONTROLLER_CLASS, 'blog_' . config_option('layout_design_blog_page_layout'), $this->data);

		}
		else
		{
			$this->show->page('404', $this->data);
		}
	}
}

/* End of file Blog.php */
/* Location: ./application/controllers/Blog.php */