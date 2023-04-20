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
class Support extends Member_Controller
{
	protected $data;

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'support_categories' => 'cat',
			'support_tickets'    => 'support',
			'uploads'            => 'uploads',
			'admin_users'        => 'admins'
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('content');

		$this->data = $this->init->initialize('site');

		$this->support->check_enabled();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	/**
	 * Member tickets
	 *
	 * View support tickets for this user
	 */
	public function view()
	{

		//check if we're looking at closed / open tickets
		$this->data[ 'closed' ] = (int)$this->input->get('closed');

		//get support tickets
		$this->data[ 'tickets' ] = $this->support->get_user_tickets(sess('member_id'), $this->data[ 'closed' ], sess('default_lang_id'));

		$this->show->display(MEMBERS_ROUTE, CONTROLLER_CLASS, $this->data);
	}

	/**
	 * Ticket details
	 *
	 * View ticket details and replies
	 */
	public function ticket()
	{

		//set the ticket id
		$this->data[ 'id' ] = (int)$this->uri->segment(4);

		//get the ticket parent first
		$this->data[ 'p' ] = $this->support->get_details($this->data[ 'id' ], sess('default_lang_id'));

		//check to verify ticket ownership
		if ($this->input->post())
		{
			if ($this->sec->verify_ownership($this->data[ 'id' ], (int)$this->input->post('ticket_id')))
			{
				//check if there are file attachments first and validate
				$files = $this->uploads->validate_uploads('support');

				//add ticket data
				$row = $this->support->validate_ticket_reply($this->input->post(), $files);

				if ($row[ 'success' ])
				{
					//send out any alerts
					$ticket = array_merge($this->data['p'], $row['data']);

					$this->mail->send_support_alerts('update', $ticket, 'admins');

					//run any plugins
					$this->done(__METHOD__, $row);

					redirect_flashdata('members/support/view/', $row[ 'msg_text' ]);
				}
			}
		}
		//get support categories
		$this->data[ 'categories' ] = $this->cat->get_categories(sess('default_lang_id'));

		if ($this->data[ 'p' ])
		{
			//check for ownership
			if (!$this->sec->verify_ownership($this->data[ 'p' ][ 'member_id' ]))
			{
				log_error('error', lang('invalid_id'));
			}
		}

		//now get the replies
		$this->show->display(MEMBERS_ROUTE, 'support_ticket_details', $this->data);
	}

	/**
	 * Download attachment
	 *
	 * Download attachment for this ticket reply
	 */
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

	public function update_status()
	{

		$this->load->library('user_agent');

		$row = $this->support->update_status((int)uri(4));

		$page = !$this->agent->referrer() ? site_url('members/support') : $this->agent->referrer();

		//run any plugins
		$this->done(__METHOD__, $row);

		redirect_flashdata($page, $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	public function create()
	{

		//get support categories
		$this->data[ 'categories' ] = $this->cat->get_categories(sess('default_lang_id'));

		if ($this->input->post())
		{
			//check if there are file attachments first and validate
			$files = $this->uploads->validate_uploads('support');

			//validate the data
			$row = $this->support->validate_ticket($this->input->post(NULL, TRUE), $files);

			if (!empty($row[ 'success' ]))
			{
				//add the ticket

				//send out any alerts
				$ticket = array_merge($_SESSION, $row['data']);
				$this->mail->send_support_alerts('create', $ticket, 'admins');

				//run any plugins
				$this->done(__METHOD__, $row);

				redirect_flashdata('members/support/view', $row[ 'msg_text' ]);
			}
			else
			{
				show_error(validation_errors());
			}
		}

		$this->show->display(MEMBERS_ROUTE, 'support_create_ticket', $this->data);

	}
}

/* End of file Support.php */
/* Location: ./application/controllers/members/Support.php */