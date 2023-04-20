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
class Blog_comments extends Admin_Controller
{

	/**
	 * @var string
	 */
	protected $data = '';

	/**
	 * @var string
	 */
	protected $table = TBL_BLOG_COMMENTS;

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'comments');
		$this->load->helper('content');


		$this->config->set_item('menu', 'content');
		$this->config->set_item('sub_menu', 'blog');

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
		$this->data[ 'page_options' ] = query_options($this->data);

		if (!$rows = $this->init->cache(current_url(), 'admin'))
		{
			$rows = $this->comments->get_rows($this->data[ 'page_options' ], sess('default_lang_id'));

			// Save into the cache
			$this->init->save_cache(__METHOD__, current_url(), $rows, 'admin');
		}

		$this->data[ 'rows' ] = $rows;

		//check for pagination
		if (!empty($this->data[ 'rows' ][ 'total' ]))
		{
			$this->data[ 'page_options' ] = array(
				'uri'        => $this->data[ 'uri' ],
				'total_rows' => $this->data[ 'rows' ][ 'total' ],
				'per_page'   => $this->data[ 'session_per_page' ],
				'segment'    => $this->data[ 'db_segment' ],
			);

			$this->data[ 'paginate' ] = $this->paginate->generate($this->data[ 'page_options' ], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('content/' . TPL_ADMIN_BLOG_COMMENTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->comments->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->comments->create($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'msg'      => $row[ 'msg_text' ],
				                   'redirect' => admin_url(TBL_BLOG_COMMENTS . '/update/' . $row[ 'id' ]),
				);
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => $row[ 'msg_text' ],
				);
			}

			ajax_response($response);
		}

		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( TBL_BLOG_POSTS, TBL_BLOG_POSTS_NAME ));

		//run the page
		$this->load->page('content/' . TPL_ADMIN_BLOG_POSTS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->comments->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				if (!empty($row['data']['comment']))
				{
					$row = $this->comments->update($row['data']);
				}

				//add response if set
				if (!empty($row[ 'data' ][ 'admin_reply' ]))
				{
					//insert admin response
					$this->comments->create(format_comment_response($row[ 'data' ], 'admin'));
				}

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'msg'      => $row[ 'msg_text' ],
				                   'redirect' => admin_url(TBL_BLOG_COMMENTS . '/view')
				);
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => $row[ 'msg_text' ],
				);
			}

			ajax_response($response);
		}

		//get the ID
		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->comments->get_details($this->data[ 'id' ], sess('default_lang_id'));

		//run the page
		$this->load->page('content/' . TPL_ADMIN_BLOG_COMMENTS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->comments->delete($id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->comments->mass_update($this->input->post());

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}
}

/* End of file Blog_comments.php */
/* Location: ./application/controllers/admin/Blog_comments.php */