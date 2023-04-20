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
class Members_credits extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$models = array(
			'members_credits' => 'credit',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

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
		//show users
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->credit->get_rows($this->data['page_options']);

		$this->load->page('members/' . TPL_ADMIN_MEMBER_CREDITS_VIEW, $this->data, 'admin');
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$this->data[ 'row' ] = list_fields(array( TBL_MEMBERS_CREDITS ));
		$this->data[ 'row' ]['username'] = '';
		$this->data['row']['date'] = display_date(get_time(), FALSE, 2, TRUE);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->credit->validate($this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{

				$row = $this->credit->create($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'redirect' => (admin_url(strtolower(__CLASS__) . '/update/' . $row['id'])));
			}
			else
			{
				$response = array('type'   => 'error',
				                  'msg'    => $row['msg'],
				                  'errors' => validation_errors(),
				);
			}

			ajax_response($response);
		}

		$this->load->page('members/' . TPL_ADMIN_MEMBER_CREDITS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->credit->get_details($this->data[ 'id' ]);

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->credit->validate($this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$row = $this->credit->update($this->data['id'], $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => $row['msg_text'],
				);
			}

			ajax_response($response);
		}

		$this->load->page('members/' . TPL_ADMIN_MEMBER_CREDITS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$row = $this->credit->delete((int)uri(4));

		if (is_array($row))
		{
			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . TBL_MEMBERS_CREDITS . '/view/', $row['msg_text']);
		}
	}
}

/* End of file Members_credits.php */
/* Location: ./application/controllers/admin/Members_credits.php */