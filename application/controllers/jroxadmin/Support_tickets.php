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
class Support_tickets extends Admin_Controller
{
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array(
			__CLASS__            => 'support',
			'support_categories' => 'cat',
			'uploads'            => 'uploads',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('content');
		$this->load->helper('html_editor');

		$this->config->set_item('menu', TBL_SUPPORT_TICKETS);
		$this->config->set_item('sub_menu', TBL_SUPPORT_TICKETS);

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
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->support->get_rows($this->data['page_options'], sess('default_lang_id'));

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//set the title
		$this->data['title'] = get_title();

		$this->data['sub_title'] = get_title(TRUE);

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_TICKETS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//get the member ID
		$this->data['id'] = (int)uri(4);

		//get member details
		if (!$row = $this->dbv->get_record(TBL_MEMBERS, 'member_id', $this->data['id']))
		{
			log_error('error', lang('invalid_member_id'));
		}

		$this->data['row'] = $row;

		//check for post data first...
		if ($this->input->post())
		{
			//check if there are file attachments first and validate
			$files = $this->uploads->validate_uploads('support');

			//validate the data
			$row = $this->support->validate_ticket($this->input->post(), $files, $this->data['row']);

			if (!empty($row['success']))
			{
				if ($this->input->post('send_email'))
				{

					$this->mail->send_support_alerts(__FUNCTION__, $row['data'], 'members');
				}

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row['msg_text']);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		$this->data['replies'] = $this->support->get_predefined_replies(TRUE);

		//set defaults..
		$this->data['row']['ticket_subject'] = '';
		$this->data['row']['reply_content'] = '';

		//get email template data
		if ($this->input->get('reply'))
		{
			if ($reply = $this->support->get_predefined_reply($this->input->get('reply')))
			{
				$this->data['row']['ticket_subject'] = $reply['ticket_subject'];
				$this->data['row']['reply_content'] = $reply['reply_content'];
			}
		}

		//set the HTML editor
		$this->data['meta_data'] = html_editor('head');

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_TICKET_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//get the member ID
		$this->data['id'] = (int)uri(4);

		//get the ticket parent first
		$this->data['row'] = $this->support->get_details($this->data['id'], sess('default_lang_id'));

		//get member details
		if (!$merge_fields = $this->dbv->get_record(TBL_MEMBERS, 'member_id', $this->data['row']['member_id']))
		{
			log_error('error', lang('invalid_member_id'));
		}

		$this->data['merge_fields'] = array_merge($this->data['row'], $merge_fields);

		$this->data['row']['reply_content'] = '';

		//check for post data first...
		if ($this->input->post())
		{
			//check if there are file attachments first and validate
			$files = $this->uploads->validate_uploads('support');

			//validate the data
			$row = $this->support->validate_ticket_reply($this->input->post(), $files, $this->data['merge_fields']);

			if (!empty($row['success']))
			{
				$this->mail->send_support_alerts(CONTROLLER_FUNCTION, $row['data'], 'members');

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row['msg_text']);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		//get predefined replies...
		$this->data['predefined_replies'] = $this->support->get_predefined_replies(TRUE);

		//get email template data
		if ($this->input->get('reply'))
		{
			if ($reply = $this->support->get_predefined_reply($this->input->get('reply')))
			{
				$this->data['row']['reply_content'] = $reply['reply_content'];
			}
		}

		//set the HTML editor
		$this->data['meta_data'] = html_editor('head');

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_TICKET_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = (int)uri(4);


		$row = $this->support->delete_ticket($id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function update_notes()
	{
		$this->data['id'] = (int)uri(4);

		if ($this->input->post())
		{
			$row = $this->support->update_note($this->data['id'], $this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text']);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => validation_errors(),
				);
			}

			ajax_response($response);
		}
	}

	// ------------------------------------------------------------------------

	public function get_predefined_reply()
	{
		$row = $this->support->get_predefined_reply((int)uri(4));

		if ($row)
		{
			$response = array('type' => 'success',
			                  'data' => $row['reply_content'],
			);

			ajax_response($response);
		}
	}

	// ------------------------------------------------------------------------

	public function download()
	{
		$row = $this->support->ticket_reply_details((int)uri(5));

		if (empty($row))
		{
			show_error('invalid_file');
		}

		download_file(uri(4), 'support');
	}

	// ------------------------------------------------------------------------

	public function member()
	{
		$this->init->check_ajax_security();

		$this->data['page_options'] = query_options($this->data);

		$this->data['id'] = valid_id(uri(4));

		//get support tickets
		$this->data['rows'] = $this->support->get_user_tickets($this->data['id'], 0, sess('default_lang_id'), ADMIN_MEMBERS_RECENT_DATA);

		//run the page
		$this->load->view('admin/support/' . TPL_AJAX_MEMBER_TICKETS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_reply()
	{
		$this->data['id'] = (int)$this->uri->segment(4);

		if ($this->input->post())
		{
			$row = $this->support->update_reply($this->data['id'], $this->input->post());

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'data' => $row['data'],
				                  'msg'  => $row['msg_text']);
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
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->support->mass_update($this->input->post());

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
		redirect_flashdata($url, $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function general_search()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->support->search($this->data['page_options'], sess('default_lang_id'));

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//set the title
		$this->data['title'] = get_title();

		$this->data['sub_title'] = get_title(TRUE);

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_TICKETS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete_reply()
	{
		$id = (int)uri(4);


		$row = $this->dbv->delete(TBL_SUPPORT_TICKETS_REPLIES, 'reply_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . (int)uri(5), $row['msg_text']);
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}
}

/* End of file Support_tickets.php */
/* Location: ./application/controllers/admin/Support_tickets.php */