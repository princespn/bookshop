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
class Support_predefined_replies extends Admin_Controller
{
	protected $data = array();

	protected $table = TBL_SUPPORT_PREDEFINED_REPLIES;

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'replies');

		$this->config->set_item('menu', TBL_SUPPORT_TICKETS);
		$this->config->set_item('sub_menu', TBL_SUPPORT_TICKETS);

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
		$this->data[ 'page_options' ] = query_options($this->data);

		$this->data[ 'rows' ] = $this->dbv->get_rows($this->data[ 'page_options' ], TBL_SUPPORT_PREDEFINED_REPLIES);

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
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_PREDEFINED_REPLIES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( $this->table ));

		//check for post data first...
		if ($this->input->post())
		{


			//validate the POST data first....
			$row = $this->replies->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->dbv->create(TBL_SUPPORT_PREDEFINED_REPLIES, $row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row[ 'id' ], $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_PREDEFINED_REPLIES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//get the ID
		$this->data[ 'id' ] = (int)uri(4);

		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->replies->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->dbv->update(TBL_SUPPORT_PREDEFINED_REPLIES, 'id', $row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row[ 'id' ], $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		$this->data[ 'row' ] = $this->dbv->get_record(TBL_SUPPORT_PREDEFINED_REPLIES, 'id', $this->data[ 'id' ]);

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_PREDEFINED_REPLIES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = (int)uri(4);

		$row = $this->dbv->delete(TBL_SUPPORT_PREDEFINED_REPLIES, 'id', $id);

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
}

/* End of file Support_predefined_replies.php */
/* Location: ./application/controllers/admin/Support_predefined_replies.php */