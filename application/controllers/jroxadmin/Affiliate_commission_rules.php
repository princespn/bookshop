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
class Affiliate_commission_rules extends Admin_Controller
{
	/**
	 * @var array
	 */
	public $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		//autoload public models
		$models = array(
			'affiliate_groups'           => 'aff_group',
			'affiliate_commissions'      => 'comm',
			'affiliate_commission_rules' => 'comm_rules',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->config->set_item('menu', 'affiliates');

		$this->lc->check(__CLASS__);

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	/**
	 * View rules in list format
	 */
	public function view()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->comm_rules->get_rows($this->data['page_options']);

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
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_COMMISSION_RULES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Create a new rule
	 */
	public function create()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->comm_rules->validate($this->input->post());

			if (!empty($row['success']))
			{

				$row = $this->comm_rules->create($row['data']);

				$this->dbv->db_sort_order(TBL_AFFILIATE_COMMISSION_RULES, 'id', 'sort_order');

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

		//fill in default values for input fields
		$this->data['row'] = set_default_form_values(array(TBL_AFFILIATE_COMMISSION_RULES));
		
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_COMMISSION_RULES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update a rule
	 */
	public function update()
	{
		//set the rule ID
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->comm_rules->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->comm_rules->update($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
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

		$this->data['row'] = $this->comm_rules->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_COMMISSION_RULES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete a rule
	 */
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_AFFILIATE_COMMISSION_RULES, 'id', $id);

		if (!empty($row['success']))
		{
			$this->dbv->db_sort_order(TBL_AFFILIATE_COMMISSION_RULES, 'id', 'sort_order');

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('rules'))
		{
			$row = $this->comm_rules->mass_update($this->input->post('rules'));

			$this->done(__METHOD__, $row);
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'system_updated_successfully');
	}
}

/* End of file Affiliate_commission_rules.php */
/* Location: ./application/controllers/admin/Affiliate_commission_rules.php */