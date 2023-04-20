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
class Blog_categories extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'cat');


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

		$this->data[ 'rows' ] = $this->cat->get_rows($this->data[ 'page_options' ], sess('default_lang_id'));

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
		$this->load->page('content/' . TPL_ADMIN_BLOG_CATEGORIES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		$row = $this->cat->create($this->language->get_languages());

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id' ], $row[ 'msg_text' ]);
		}
		else
		{
			show_error(lang('could_not_create_record'));
		}
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->cat->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->cat->update($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type' => 'success',
				                   'msg'  => $row[ 'msg_text' ]
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

		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->cat->get_details($this->data[ 'id' ], sess('default_lang_id'));

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		//get languages
		$this->data[ 'languages' ] = get_languages(FALSE, FALSE);

		$this->load->page('content/' . TPL_ADMIN_BLOG_CATEGORIES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->cat->delete($id);

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
			$row = $this->cat->mass_update($this->input->post(), sess('default_lang_id'));

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------
	
	public function search()
	{
		$term = $this->input->get('category_name', TRUE);

		$rows = $this->cat->ajax_search($term, sess('default_lang_id'),  uri(5));

		echo json_encode($rows);
	}
}

/* End of file Blog_categories.php */
/* Location: ./application/controllers/admin/Blog_categories.php */