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
class Email_send extends Admin_Controller
{
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'members'             => 'mem',
			'affiliate_groups'    => 'aff_group',
			'discount_groups'     => 'disc_group',
			'email_mailing_lists' => 'lists',
			'email_templates'     => 'template',
			'regions'             => 'regions',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('html_editor');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		redirect_page();
	}

	// ------------------------------------------------------------------------

	public function queue_mass_email()
	{
		$response = array('type' => 'done');

		if ($this->input->post())
		{
			//get the list totals
			$offset = (int)$this->input->post('offset');

			$list_total = $this->lists->get_list_totals((int)$this->input->post('list_id'));

			$queued = $this->mail->queue_mass_email($this->input->post(), $offset);

			$queued_total = sess('queued_total') + $queued['total_sent'];

			if (!empty($queued['total_sent']))
			{
				$this->session->set_userdata('queued_total', $queued_total);

				$response = array('type'   => 'continue',
				                  'offset' => $queued['offset'],
				                  'total' => $queued_total,
				                  'width'  => $queued['total_sent'] / $list_total * 100,
				);
			}
			else
			{
				$response = array('type'   => 'done',
				                  'queued' => $queued_total);

				$this->session->unset_userdata('queued_total');
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function process()
	{
		if ($this->session->flashdata('email_data'))
		{
			$this->data['row'] = $this->session->flashdata('email_data');

			//run the page
			$this->load->page('email/' . TPL_ADMIN_QUEUE_MASS_EMAIL, $this->data);
		}
		else
		{
			redirect_page(ADMIN_ROUTE . '/email_mailing_lists/view');
		}
	}

	// ------------------------------------------------------------------------

	public function mailing_list()
	{
		//get the id for the member
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->dbv->get_record(TBL_EMAIL_MAILING_LISTS, 'list_id', $this->data['id'], TRUE);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//set the defaults....
		$this->data['row']['subject'] = '';
		$this->data['row']['html_body'] = '';
		$this->data['row']['text_body'] = '';

		$this->data['templates'] = $this->template->get_templates('custom', sess('default_lang_id'), TRUE);

		if ($this->input->post())
		{
			//validate the email data first....
			$row = $this->mail->validate_mass_email($this->input->post(), $this->data['row']);

			if (!empty($row['success']))
			{
				//set the session flash and redirect the page
				$this->session->set_flashdata('email_data', $row['data']);
				redirect_page(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/process');
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		//get email template data
		if ($this->input->get('template'))
		{
			if ($template = $this->template->get_details($this->input->get('template')))
			{
				$this->data['row']['subject'] = $template['subject'];
				$this->data['row']['html_body'] = $template['html_body'];
				$this->data['row']['text_body'] = $template['text_body'];
			}
		}

		$this->data['meta_data'] = html_editor('head');

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_MASS_EMAIL, $this->data);
	}

	// ------------------------------------------------------------------------

	public function user()
	{
		//get the id for the member
		$this->data['id'] = valid_id(uri(4));

		//get member details
		if (!$row = $this->mem->get_details($this->data['id'], sess('default_lang_id')))
		{
			log_error('error', 'invalid_user');
		}

		//set the defaults....
		$this->data['row'] = $row;
		$this->data['row']['subject'] = '';
		$this->data['row']['html_body'] = '';
		$this->data['row']['text_body'] = '';

		$this->data['templates'] = $this->template->get_templates('custom', sess('default_lang_id'), TRUE);

		if ($this->input->post())
		{
			//validate the email data first....
			$row = $this->mail->validate($this->input->post(), $this->data['row']);

			if (!empty($row['success']))
			{
				$row = $this->mail->send($row['data']);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/members/update/' . $this->data['id'], $row['msg_text']);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		//get email template data
		if ($this->input->get('template'))
		{
			if ($template = $this->template->get_details($this->input->get('template')))
			{
				$this->data['row']['subject'] = $template['subject'];
				$this->data['row']['html_body'] = $template['html_body'];
				$this->data['row']['text_body'] = $template['text_body'];
			}
		}

		$this->data['meta_data'] = html_editor('head');

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_SEND_USER, $this->data);
	}
}

/* End of file Email_send.php */
/* Location: ./application/controllers/admin/Email_send.php */