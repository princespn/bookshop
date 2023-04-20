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
class Members_notes extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'note');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	public function recent()
	{
		//$this->init->check_ajax_security();

		$this->data[ 'id' ] = (int)$this->uri->segment(4);

		$this->data[ 'row' ] = $this->note->get_rows($this->data[ 'id' ]);

		$this->load->view('admin/members/' . TPL_AJAX_MEMBERS_NOTES, $this->data);
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		$this->data[ 'id' ] = (int)$this->uri->segment(4);

		$this->data[ 'row' ] = $this->note->get_rows($this->data[ 'id' ]);

		$this->load->page('members/' . TPL_AJAX_MEMBERS_NOTES, $this->data, 'admin');
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$this->data[ 'id' ] = (int)$this->uri->segment(4);

		if ($this->input->post())
		{
			$row = $this->note->create($this->data[ 'id' ], $this->input->post(NULL, TRUE));

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type' => 'success',
				                   'msg'  => $row[ 'msg_text' ] );
			}
			else
			{
				$response = array( 'type'   => 'error',
				                   'msg'    => $row[ 'msg' ],
				                   'errors' => validation_errors(),
				);
			}

			ajax_response($response);
		}
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data[ 'id' ] = valid_id(uri(4));

		if ($this->input->get())
		{
			$this->data[ 'row' ] = $this->note->update($this->data[ 'id' ], $this->input->get(NULL, TRUE));

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);
			}
		}
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$row = $this->note->delete((int)uri(5));

		if (is_array($row))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . TBL_MEMBERS . '/update/' . (int)uri(4), $row[ 'msg_text' ]);
		}
	}
}

/* End of file Members_notes.php */
/* Location: ./application/controllers/admin/Members_notes.php */