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
class Email_queue extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

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
			$rows = $this->dbv->get_rows($this->data['page_options'], TBL_EMAIL_QUEUE);

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

			$this->data['header'] = $this->show->parse_tpl($this->data, config_item('layout_design_email_template_header'));
			$this->data['footer'] = $this->show->parse_tpl($this->data, config_item('layout_design_email_template_footer'));

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_QUEUE_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_EMAIL_QUEUE, 'id', $id);

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
				$row = $this->dbv->delete(TBL_EMAIL_QUEUE, 'id', $v);

				$this->done(__METHOD__, $row);
			}
		}
		
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------

	public function reset()
	{
		$row = $this->dbv->reset_data(TBL_EMAIL_QUEUE);

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function view_email()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->dbv->get_record(TBL_EMAIL_QUEUE, 'id', $this->data['id']);

		//run the page
		$this->load->view('admin/email/' . TPL_ADMIN_EMAIL_QUEUE_PREVIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function flush()
	{
		$this->data['queued_total'] = $this->db->count_all(TBL_EMAIL_QUEUE);

		if (!$this->data['queued_total'])
		{
			redirect_page(ADMIN_ROUTE . '/email_queue/view');
		}

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_FLUSH_QUEUE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function send_emails()
	{
		$response = array('type' => 'done');

		if ($this->input->post())
		{
			$row = $this->mail->flush($this->input->post('offset'));

			$sent_total = sess('sent_total') + $row['total_sent'];

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			if (!empty($sent['total_sent']))
			{
				$this->session->set_userdata('sent_total', $sent_total);

				$response = array('type'   => 'continue',
				                  'offset' => $sent['offset'],
				                  'total' => $sent_total,
				                  'width'  => $sent['total_sent'] / $this->input->post('queued_total') * 100,
				);
			}
			else
			{
				$response = array('type'   => 'done',
				                  'sent' => $sent_total);

				$this->session->unset_userdata('sent_total');
			}
		}

		ajax_response($response);
	}
}

/* End of file Email_queue.php */
/* Location: ./application/controllers/admin/Email_queue.php */