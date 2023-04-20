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
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
class Email_archive extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var string
	 */
	protected $table = TBL_EMAIL_ARCHIVE;

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model('email_model', 'email');


		$this->config->set_item('menu', 'email');

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
		$this->data['page_options'] = query_options($this->data);

		//check the cache
		if (!$rows = $this->init->cache(current_url(), 'admin'))
		{
			$rows = $this->dbv->get_rows($this->data['page_options'], $this->table);

			// Save into the cache
			$this->init->save_cache(__METHOD__, current_url(), $rows, 'admin');
		}

		$this->data['rows'] = $rows;

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

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_ARCHIVE_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_EMAIL_ARCHIVE, 'id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('id'))
		{
			foreach ($this->input->post('id') as $k => $v)
			{
				$row = $this->dbv->delete(TBL_EMAIL_ARCHIVE, 'id', $v);

				$this->done(__METHOD__, $row);
			}
		}

		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------

	public function member()
	{
		$this->init->check_ajax_security();

		$this->data['id'] = (int)$this->uri->segment(4);

		$this->data['row'] = $this->email->get_user_archive($this->data['id']);

		$this->load->view('admin/members/' . TPL_AJAX_MEMBERS_EMAIL_ARCHIVE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function reset()
	{
		$row = $this->dbv->reset_data(TBL_EMAIL_ARCHIVE);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function view_email()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->dbv->get_record(TBL_EMAIL_ARCHIVE, 'id', $this->data['id']);

		//run the page
		$this->load->view('admin/email/' . TPL_ADMIN_EMAIL_ARCHIVE_PREVIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function resend()
	{
		$this->data['id'] = valid_id(uri(4));

		$data = $this->dbv->get_record(TBL_EMAIL_ARCHIVE, 'id', $this->data['id']);

		if ($row = $this->mail->send($data, $data['recipient_email']))
		{
			$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
			redirect_flashdata($url, 'email_sent_successfully');
		}
		else
		{
			show_error(lang('could_not_send_email'));
		}
	}
}

/* End of file Email_archive.php */
/* Location: ./application/controllers/admin/Email_archive.php */