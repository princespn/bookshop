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
class Blog_tags extends Admin_Controller
{

	/**
	 * @var string
	 */
	protected $data = '';

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		//autoload public models
		$models = array(
			__CLASS__       => 'tag',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

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
		$this->data[ 'rows' ] = $this->tag->get_rows();

		//run the page
		$this->load->page('content/' . TPL_ADMIN_BLOG_TAGS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		//check for post data first...
		if ($this->input->post())
		{
			$row = $this->tag->create($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'msg'      => $row[ 'msg_text' ],
				                   'redirect' => admin_url(TBL_BLOG_TAGS . '/view'),
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
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		//get the ID
		$this->data[ 'id' ] = valid_id(uri(4));

		//check for post data first...
		if ($this->input->post())
		{
			$row = $this->tag->update($this->data[ 'id' ], $this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);
			}
		}
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->tag->delete($id);

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
	
	public function search()
	{
		$term = $this->input->get('tag', TRUE);

		$rows = $this->tag->ajax_search($term, TRUE);

		echo json_encode($rows);
	}
}

/* End of file Blog_tags.php */
/* Location: ./application/controllers/admin/Blog_tags.php */