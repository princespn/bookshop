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
class Rewards extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'members'             => 'mem',
			'gift_certificates'   => 'gift',
			'affiliate_groups'    => 'aff_group',
			'discount_groups'     => 'disc_group',
			'blog_groups'         => 'blog_group',
			'email_mailing_lists' => 'lists',
			'regions'             => 'regions',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}


		$this->config->set_item('menu', 'promotions');

		$this->lc->check(__CLASS__);

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

		$this->data['rows'] = $this->dbv->get_rows($this->data['page_options'], TBL_REWARDS);

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
		$this->load->page('promotions/' . TPL_ADMIN_REWARDS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function history()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->rewards->get_history($this->data['page_options']);

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
		$this->load->page('promotions/' . TPL_ADMIN_REWARDS_HISTORY_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data['row'] = list_fields(array(TBL_REWARDS));
		$this->data['row']['points'] = '10';
		$this->data['row']['start_date_formatted'] = display_date(get_time() - 86400, FALSE, 2, TRUE);
		$this->data['row']['end_date_formatted'] = display_date(get_time() + 2592000, FALSE, 2, TRUE);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->rewards->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->rewards->create($row['data']);

				$this->dbv->db_sort_order(TBL_REWARDS, 'rule_id', 'sort_order');

				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('success', $row['msg_text']);

				//set the default response
				$response = array('type'     => 'success',
				                  'redirect' => (admin_url(strtolower(__CLASS__) . '/update/' . $row['id'])),
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

		$this->load->page('promotions/' . TPL_ADMIN_REWARDS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//set the rule ID
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->rewards->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->rewards->update($row['data']);

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

		$this->data['row'] = $this->rewards->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('promotions/' . TPL_ADMIN_REWARDS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_REWARDS, 'rule_id', $id);

		if (!empty($row['success']))
		{
			$this->dbv->db_sort_order(TBL_REWARDS, 'rule_id', 'sort_order');

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function update_user()
	{
		$member_id = valid_id(uri(4));
		$id = valid_id(uri(5));
		$points = (int)uri(6);

		$row = $this->dbv->delete(TBL_REWARDS_HISTORY, 'points_id', $id);

		if (!empty($row['success']))
		{
			$this->rewards->update_user_points(array('member_id' => $member_id, 'points' => $points), '-');

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/history/view', $row['msg_text']);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('rules'))
		{
			$this->rewards->mass_update($this->input->post('rules'));
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	public function redeem()
	{
		$member_id = valid_id(uri(4));

		$mem = $this->mem->get_details($member_id);

		$amount = redeem_points($mem['points']);

		if (!empty($amount))
		{
			$item = array('amount'     => format_amount($amount, FALSE),
			              'to_message' => lang('your_gift_certificate_for_loyalty_rewards'));

			$gift = $this->gift->create(format_new_cert($mem, $item, 'points'));

			$this->mail->send_template(EMAIL_MEMBER_GIFT_CERTIFICATE_DETAILS, $gift['data'], TRUE, sess('default_lang_id'), $gift['data']['to_email']);

			//update user points
			$this->rewards->update_user_points(array('member_id' => $member_id, 'points' => ($mem['points'] * -1)));
		}

		redirect_flashdata(ADMIN_ROUTE . '/gift_certificates/view', lang('system_updated_successfully'));
	}
}

/* End of file Rewards.php */
/* Location: ./application/controllers/admin/Rewards.php */