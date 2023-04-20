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
class Blog_groups extends Admin_Controller
{
	protected $data = '';

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'group');

		$this->config->set_item('menu', 'content');
		$this->config->set_item('sub_menu', 'blog');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------
	
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------
	
	public function view()
	{
		$this->data[ 'page_options' ] = query_options($this->data);

		$this->data[ 'rows' ] = $this->group->get_rows($this->data[ 'page_options' ]);

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
		$this->load->page('content/' . TPL_ADMIN_BLOG_GROUPS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		//create the group
		$row = $this->group->create(array( 'group_name'        => lang('new_group'),
		                                   'group_description' => lang('new_group'),
		));

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/', $row[ 'msg_text' ]);
		}

		show_error(lang('could_not_create_record'));
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));



		$row = $this->group->delete($id);

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
			//check if we are sending an email
			if ($this->input->post('email'))
			{
				//save the session
				$this->session->set_flashdata('send_groups', $this->input->post('group_id'));

				$response = array( 'type' => 'success',
				                   'redirect' => admin_url('email_send/group/blog' )
				);
			}
			else
			{
				$row = $this->group->update_groups($this->input->post('groups'));

				if (!empty($row[ 'success' ]))
				{
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
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------
	
	public function search()
	{
		$term = $this->input->get('blog_group_name', TRUE);

		$rows = $this->group->ajax_search($term, uri(5));

		echo json_encode($rows);
	}
}

/* End of file Discount_groups.php */
/* Location: ./application/controllers/admin/Discount_groups.php */