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
class Affiliate_commissions extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		//autoload public models
		$models = array(
			'invoices'            => 'invoices',
			'members'             => 'mem',
			'affiliate_groups'    => 'aff_group',
			'discount_groups'     => 'disc_group',
			'email_mailing_lists' => 'lists',
			__CLASS__             => 'comm',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->config->set_item('menu', 'affiliates');

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
	
	public function view()
	{
		$this->data['page_options'] = query_options($this->data);

		//get commissions
		$this->data['rows'] = $this->comm->get_rows($this->data['page_options']);

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
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_COMMISSIONS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		//if POST input is sent, let's validate it...
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->comm->validate(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				//check if we're generating using group amounts
				if ($row['data']['use_group_amounts'] == 1)
				{
					//get upline
					$upline = $this->downline->check_upline($row['data']);

					//check if we're crediting the upline
					if (!empty($row['data']['generate_upline']) && check_upline_config())
					{
						//go through each member in the downline
						$line = array_reverse($upline, TRUE);

						foreach ($line as $k => $v)
						{
							//generate single commission
							$this->comm->create_commission(format_commission_data($k,
								$row['data']['sale_amount'],
								$v,
								$row['data']));
						}
					}
					else
					{
						//generate single commission
						$this->comm->create_commission(format_commission_data(1,
							$row['data']['sale_amount'],
							$upline[1],
							$row['data']));
					}
				}
				else
				{
					$row = $this->comm->create($row['data']);
				}

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => lang('record_created_successfully'),
				                  'redirect' => admin_url(CONTROLLER_CLASS . '/view/'),
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
		$this->data['row'] = set_default_form_values(array(TBL_AFFILIATE_COMMISSIONS, TBL_INVOICES, TBL_MEMBERS));

		//get invoice data if any
		if (uri(4))
		{
			if ($invoice = $this->invoices->get_details((int)uri(4), TRUE))
			{
				$this->data['row'] = array_merge($this->data['row'], $invoice);

				//check for sponsor
				if (!empty($invoice['affiliate_id']))
				{
					if ($sponsor = $this->dbv->get_record(TBL_MEMBERS, 'member_id', $invoice['affiliate_id']))
					{
						$this->data['row']['member_id'] = $invoice['affiliate_id'];
						$this->data['row']['username'] = $sponsor['username'];
					}
				}
			}
		}

		//check for member id
		if ($this->input->get('member_id'))
		{
			if ($mem = $this->dbv->get_record(TBL_MEMBERS, 'member_id', $this->input->get('member_id')))
			{
				$this->data['row'] = array_merge($this->data['row'], $mem);
			}
		}

		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_COMMISSIONS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		//if POST input is sent, let's validate it...
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->comm->validate(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->comm->update($row['data']);

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

		//set the commission ID
		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->comm->get_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_COMMISSIONS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->comm->delete($id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
		else
		{
			log_error('error', $row['msg_text']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->comm->mass_update($this->input->post());

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		$page = !$this->agent->referrer() ? admin_url(uri(3)) : $this->agent->referrer();

		redirect_flashdata($page, 'mass_update_successful');
	}

	// ------------------------------------------------------------------------
	
	public function approve_commissions()
	{
		$row = $this->comm->approve_commissions();

		$this->done(__METHOD__, $row);

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/affiliate_payment_options/view', $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------
	
	public function member()
	{
		$this->init->check_ajax_security();

		$this->data['page_options'] = query_options($this->data);

		$this->data['id'] = valid_id(uri(4));

		$this->data['rows'] = $this->comm->get_commissions($this->data['id'], 'member_id', FALSE, ADMIN_MEMBERS_RECENT_DATA);

		//run the page
		$this->load->view('admin/members/' . TPL_AJAX_MEMBER_COMMISSIONS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function general_search()
	{
		$this->data['page_options'] = query_options($this->data);

		//get commissions
		$this->data['rows'] = $this->comm->search($this->data['page_options']);

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
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_COMMISSIONS_VIEW, $this->data);
	}
}

/* End of file Affiliate_commissions.php */
/* Location: ./application/controllers/admin/Affiliate_commissions.php */